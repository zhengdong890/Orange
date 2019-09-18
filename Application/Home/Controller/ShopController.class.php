<?php
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class ShopController extends Controller {
    public function _initialize(){
        $redis = new \Com\Redis();
        $seller_id = I('seller_id');
        $this->seller_id = $seller_id;
        /*获取店铺数据*/
        $shop_data = $redis->get('shop_data'.$seller_id , 'array');//获取redis的缓存
        if(!$shop_data){
            $shop_data = M('Shop_data')->where(array('member_id'=>$seller_id))->find();
            $redis->set('shop_data'.$seller_id , serialize($shop_data));//设置redis的缓存
        }
        /*获取导航*/
        $nav = $redis->get('shop_nav'.$seller_id , 'array');//获取redis的缓存
        if(!$nav){
            $nav  = M('Shopping')
                  ->where(array('member_id'=>$seller_id,'status'=>1))
                  ->order('rsort asc')
                  ->select();
            $redis->set('shop_nav'.$seller_id , serialize($nav));//设置redis的缓存
        }
        /*获取导航css*/
        $nav_css = $redis->get('nav_css'.$seller_id , 'array');//获取redis的缓存
        if(!$nav_css){
            $nav_css = M('Shop_nav_css')->where(array('member_id'=>$seller_id))->getField('background_color');      
           $redis->set('nav_css'.$seller_id , serialize($nav_css));//设置redis的缓存
        }    
        $help = D('HelpCategory')->redisCatName($redis);
        $this->assign('shop_data' , $shop_data);
        $this->assign('shop_nav' , $nav);
        $this->assign('nav_css' , $nav_css);          
        $this->assign('help' , get_child($help));
    }

	public function index(){
        $seller_id = I('get.seller_id'); 
		/*访问量加1*/
        $isset =  M('visitor_count')->where(array('member_id'=>$seller_id))->find();
		if( $isset ){
			M('visitor_count')->where(array('member_id'=>$seller_id))->setInc('visitor_num');
		}else{	
            $adddata['member_id']=$seller_id;
            $adddata['visitor_num']=1;
			$adddata['ip_num']=0;
			
			M('visitor_count')->add($adddata);
		}
		/*无访客就添加，有则加1*/
		$visitor_ip = getIP();
		$issetip =  M('visitor_ip')->where(array('member_id'=>$seller_id,'visitor_id'=>$visitor_ip))->find();
		if( $issetip ){	
            
		}else{
		   $dataip['member_id'] = $seller_id;
		   $dataip['visitor_ip']=$visitor_ip;			   
		   M('visitor_ip')->add($dataip);		     		  
		   M('visitor_count')->where(array('member_id'=>$seller_id))->setInc('ip_num');				  
                    
		}										
		
       
		/*获取轮播*/
		$banner = M('Shop_banner')->where(array('member_id'=>$seller_id))->getField('thumb');
        $shop_nav = M('shopping')->where(array('member_id'=>$seller_id,'status'=>1))->order('rsort asc')->select();		
	    $shop_data = M('Shop_data')->where(array('member_id'=>$seller_id))->find();
        /*获取畅销商品*/
        $hot_goods = M('Mall_goods')
		           ->where(array('member_id'=>$seller_id,'is_new'=>2))
		           ->field('id,goods_name,goods_price,goods_thumb,sale_num')
		           ->order('sort')
		           ->limit(0,8)
		           ->select();
		foreach( $hot_goods as $k=>$v ){
			$hot_goods[$k]['goods_price'] = $v['goods_price']/10000;			
		}		   
        /*获取最新商品*/
        $new_goods = M('Mall_goods')
		           ->where(array('member_id'=>$seller_id,'is_new'=>1))
		           ->field('id,goods_name,goods_price,goods_thumb,sale_num')
		           ->order('id desc,sort')
		           ->limit(0,8)
		           ->select();	 
		   
		foreach( $new_goods as $k1=>$v1 ){
			$new_goods[$k1]['goods_price'] = $v1['goods_price']/10000;			
		}			
		$shopcat = M('shop_category')->where(array('member_id'=>$seller_id,'status'=>1))->order('sort asc')->select();		
		$shopcat = getLayer($shopcat);
	    $shop_css = M('shop_nav_css')->where(array('member_id'=>$seller_id))->getField('background_color');				
		$shop_status = M('mall_application')->where(array('seller_id'=>$seller_id))->getField('check_status');
		$this->assign('shop_status',$shop_status);
		$uinfo= $_SESSION['member_data'];		
		$this->assign('uinfo',$uinfo);
		$this->assign('shop_css',$shop_css);
		$this->assign('shop_data' , $shop_data);
		$this->assign('shopcat',$shopcat);	  		   				   
        $this->assign('seller_id' , $seller_id);
		$this->assign('shop_nav' , $shop_nav);
        $this->assign('banner' , $banner);
        $this->assign('hot_goods' , $hot_goods);
        $this->assign('new_goods' , $new_goods);
        $this->display();
    }
 /*根据导航显示商品*/   
    public function goodsList(){
        $seller_id = I('seller_id');
        $cat_id  = intval(I('cat_id'));//当前分类id
        $paramer = I();//获取查询参数
        $where['a.member_id'] = $seller_id;
        $where['a.status'] = 1;
        $where['a.shop_cat'] = $cat_id;
		;
          
        /*url参数格式组装*/
        foreach($paramer as $k => $v){
            $paramer[$k] = "$k=$v";
        }
       
        $crumb = M('shop_category')->where(array('id'=>$cat_id))->getField('name');
        /*分页*/
        //$count = M('Mall_goods as a')->where($where)->count();//统计商品数量
		$count = M('Mall_goods')->where(array('member_id'=>$seller_id,'shop_cat'=>$cat_id,'status'=>1))->count();
        $Page  = new \Think\Page($count,8);// 实例化分页类 传入总记录数和每页显示的记录数(5)
        $show  = $Page->getPage();
        $goods = M('Mall_goods as a')
               ->where($where)
               ->field('id,goods_name,goods_price,goods_thumb,sale_num,shop_cat,status')
               ->limit($Page->firstRow.','.$Page->listRows)
               ->order('sort')
               ->select();
			   
		foreach( $goods as $k2=>$v2 ){
			$goods[$k2]['goods_price'] = $v2['goods_price']/10000;			
		}	 
		
  		unset($paramer['p']);
		$shopcat = M('shop_category')->where(array('member_id'=>$seller_id,'status'=>1))->order('sort asc')->select();		
		$shopcat = getLayer($shopcat);
		$shop_css = M('shop_nav_css')->where(array('member_id'=>$seller_id))->getField('background_color');		
		$shop_data = M('Shop_data')->where(array('member_id'=>$seller_id))->find();
		
		$this->assign('shop_css',$shop_css);
	   
		$this->assign('shopcat',$shopcat);	
		$shop_nav = M('shopping')->where(array('member_id'=>$seller_id,'status'=>1))->order('rsort asc')->select();
  		$html  = $this->pageHtml($show ,'goodsList', implode('&' , $paramer));
		$uinfo= $_SESSION['member_data'];
        $shop_status = M('mall_application')->where(array('seller_id'=>$seller_id))->getField('check_status');
		$this->assign('shop_status',$shop_status);		
		$this->assign('uinfo',$uinfo);
  		$this->assign('crumb',$crumb);
        $this->assign('shop_nav' , $shop_nav);		
		$this->assign('shop_data' , $shop_data);
  		$this->assign('paramers',json_encode(I()));
  		$this->assign('html',$html);
  		$this->assign('cat_id',$cat_id);  	
  		$this->assign('count',$count);
  		$this->assign('goods' , $goods);
  		$this->assign('seller_id' , $seller_id);
  		$this->display();
    } 
  
    protected function pageHtml($data,$address,$parameter){		
        $url  = U($address).'?'.$parameter;
        $html = array("<div class='turn-page'>");
        if($data['prev']){
            array_push($html,"<a href='".$url."&p={$data['perv']}'><div class='left-btn'></div></a>");
        }else{
            array_push($html,"<div class='left-btn'></div>");
        }
        foreach($data['page'] as $k => $v){
            if($v == '.'){
                array_push($html,"<div class='omit'>...</div>");
            }else
                if($v != $data['nowPage']){
                array_push($html,"<a href='".$url."&p={$v}'><div class='page-btn'>{$v}</div></a>");
            }else{
                array_push($html,"<div class='page-btn active'>{$v}</div>");
            }
        }
        if($data['next']){
            array_push($html,"<a href='".$url."&p={$data['next']}'><div class='right-btn'></div></a>");
        }else{
            array_push($html,"<div class='right-btn'></div>");
        }
    
        array_push($html,"</div>");
        array_push($html,"<p class='to-page'>跳至");
        array_push($html,"<input class='go_number' type='text' value='1'>页<input type='button' value='跳转' class='go'>");
        array_push($html,"</p>");
        $html = implode('',$html);
        return $html;
    }
/*店铺收藏*/
     public function shopcllect(){
		 
		 if(IS_AJAX){
			 $data['member_id'] = $_SESSION['member_data']['id'];
			 if(!$data['member_id']){
				$this->ajaxReturn(array('msg'=>'nologin')); 
			 }
			 $data['seller_id'] = I('post.title');
			 $data['status'] = 1;
			 $data['time'] = time();
			 $is_set = M('shop_collect')->where(array('seller_id'=>$data['seller_id'],'member_id'=>$data['member_id']))->find();
			 if( $is_set ){
				 $this->ajaxReturn(array('msg'=>'onemore'));				 
			 }else{
				 $addData = M('shop_collect')->add($data);
				 if( $addData ){
					 $this->ajaxReturn(array('msg'=>'ok'));				 				 
				 }		 
				 
			 }
			 	 
		 }		 		 
	 }	
/*获取联系卖家信息*/ 	 
     public function getseller(){		
	     if( $_GET['title'] ){
			 $seller_id = $_GET['title'];
			 $memInfo = M('member_data')->where(array('member_id'=>$seller_id))->find();
			 			 
			 if( $memInfo ){
				$this->assign('memInfo',$memInfo);			 
			 }			  			 
		 }
	    $this->display();			
	 }
/* 所有商品列表*/     
	 public function allgoods(){		
	       $seller_id = I('get.seller_id');
		   $paramer = I();
		   //$paramer = 'seller_id='.$seller_id; 
          foreach($paramer as $k => $v){
          $paramer[$k] = "$k=$v";
         }   
           $count = M('mall_goods')->where(array('member_id'=>$seller_id))->count();		   
		   
		   $Page  = new \Think\Page($count,12);// 实例化分页类 传入总记录数和每页显示的记录数(5)
           $show  = $Page->getPage();
		   $allgoods = M('mall_goods')->where(array('member_id'=>$seller_id,'status'=>1))->limit($Page->firstRow.','.$Page->listRows)->select();		   		   
		   foreach( $allgoods as $k3=>$v3 ){ 
			  $allgoods[$k3]['goods_price'] = $v3['goods_price']/10000;			
		   }
		   unset($paramer['p']);
		   $shop_nav = M('shopping')->where(array('member_id'=>$seller_id,'status'=>1))->order('rsort asc')->select();		
		   $shop_data = M('Shop_data')->where(array('member_id'=>$seller_id))->find();
		   $paramer = implode('&',$paramer);
		   $html  = $this->pageHtml($show,'allgoods',$paramer);
		   $uinfo= $_SESSION['member_data'];		
		   $this->assign('uinfo',$uinfo);
		   $this->assign('allgoods',$allgoods);	
           $this->assign('page',$html);			   
		   $banner = M('Shop_banner')->where(array('member_id'=>$seller_id))->getField('thumb');
		   $shopcat = M('shop_category')->where(array('member_id'=>$seller_id,'status'=>1))->order('sort asc')->select();		
		   $shopcat = getLayer($shopcat);	
           $shop_css = M('shop_nav_css')->where(array('member_id'=>$seller_id))->getField('background_color');		
		   $shop_status = M('mall_application')->where(array('seller_id'=>$seller_id))->getField('check_status');
		   $this->assign('shop_status',$shop_status);
		   $this->assign('paramers',$paramer);
		   $this->assign('shop_nav' , $shop_nav);
		   $this->assign('shop_data' , $shop_data);
		   $this->assign('shop_css',$shop_css);		   
		   $this->assign('shopcat' , $shopcat);
		   $this->assign('seller_id',$seller_id);
		   $this->assign('banner' , $banner);
	   $this->display();			
	 }
	 
	 

/*根据分类显示商品*/	 
	 public function showgoods(){
         $seller_id = I('get.seller_id');
         $cat_id = I('get.cat_id');		 
	     $goods_list =  M('mall_goods')->where(array('member_id'=>$seller_id,'shop_cat'=>$cat_id))->select();
		 foreach( $goods_list as $k4=>$v4 ){
			$goods_list[$k4]['goods_price'] = $v4['goods_price']/10000;			
		 }	
		 $shop_nav = M('shopping')->where(array('member_id'=>$seller_id,'status'=>1))->order('rsort asc')->select();		
         $shop_data = M('Shop_data')->where(array('member_id'=>$seller_id))->find();		 
		 $shopcat = M('shop_category')->where(array('member_id'=>$seller_id,'status'=>1))->order('sort asc')->select();		
		 $shopcat = getLayer($shopcat);
		 $banner = M('Shop_banner')->where(array('member_id'=>$seller_id))->getField('thumb');
		 $shop_css = M('shop_nav_css')->where(array('member_id'=>$seller_id))->getField('background_color');		
		 $uinfo= $_SESSION['member_data'];	
         $shop_status = M('mall_application')->where(array('seller_id'=>$seller_id))->getField('check_status');
		 $this->assign('shop_status',$shop_status);		 
		 $this->assign('uinfo',$uinfo);
		 $this->assign('shop_data',$shop_data);
		 $this->assign('shop_css',$shop_css);
		 $this->assign('banner' , $banner);
		 $this->assign('shop_nav' , $shop_nav);
		 $this->assign('shopcat' , $shopcat); 
		 $this->assign('seller_id' , $seller_id);
		 if( $goods_list ){			
			$this->assign('goods_list',$goods_list); 	            			
		 }
	   $this->display();			
	 }
	 
/*商品搜索*/	 
	  public function search(){		  
		  if( IS_AJAX){
			 $seller_id = I('post.seller_id');
			 $search = trim(I('post.search'));	
			 $condition["goods_name"] = array('like','%'.$search.'%');	
			 $condition["member_id"] = $seller_id;		 
			 $id =  M('mall_goods')->where($condition)->getField('id');	
			 if( $id ){
			    $this->ajaxReturn(array('msg'=>'ok','goods_id'=>$id));			 
		     }else{	 	  
				$this->ajaxReturn(array('msg'=>'no'));			  
			 }			  			  
		  }	  		
	 }
     
	 public function service(){		
        $seller_id = I('get.seller_id');
        $shop_css = M('shop_nav_css')->where(array('member_id'=>$seller_id))->getField('background_color');				
        $shop_data = M('Shop_data')->where(array('member_id'=>$seller_id))->find();
		$shop_status = M('mall_application')->where(array('seller_id'=>$seller_id))->getField('check_status');
		$shop_nav = M('shopping')->where(array('member_id'=>$seller_id,'status'=>1))->order('rsort asc')->select();		
		$this->assign('shop_nav' , $shop_nav);
		$this->assign('seller_id' , $seller_id);
		$this->assign('shop_status',$shop_status);
	    $this->assign('shop_css',$shop_css);
		$this->assign('shop_data',$shop_data);	 
		$this->display();	  			  
		   		
	 }
	 
	 public function company(){	
	    $seller_id = I('get.seller_id');
        $shop_css = M('shop_nav_css')->where(array('member_id'=>$seller_id))->getField('background_color');				
        $shop_data = M('Shop_data')->where(array('member_id'=>$seller_id))->find();
		$shop_status = M('mall_application')->where(array('seller_id'=>$seller_id))->getField('check_status');
		$shop_nav = M('shopping')->where(array('member_id'=>$seller_id,'status'=>1))->order('rsort asc')->select();		
		$content = M('shop_detail')->where(array('member_id'=>$seller_id))->getField('content');
		$content = html_entity_decode($content);
		$shopcat = M('shop_category')->where(array('member_id'=>$seller_id,'status'=>1))->order('sort asc')->select();		
		$shopcat = getLayer($shopcat);
		$uinfo= $_SESSION['member_data'];		
		$this->assign('uinfo',$uinfo);
		$this->assign('shopcat' , $shopcat);
		$this->assign('content' , $content);
		$this->assign('shop_nav' , $shop_nav);
		$this->assign('seller_id' , $seller_id);
		$this->assign('shop_status',$shop_status);
	    $this->assign('shop_css',$shop_css);
		$this->assign('shop_data',$shop_data);
		$this->display();	  			  
		   		
	 }
	    //店铺公告
	 public function shop_notice(){
	 		  //查询分页
	 	$seller_id = $_SESSION['member_data']['id'];

        $news=M('Mall_goods_check');
       
        $count=$news->where(array('seller_id'=>$seller_id))->count();//统计数量
        //dump($count);
        $num1=10;
        $page       = new \Think\Page($count,$num1);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $page->setConfig('header','个会员');
                $page->setConfig('prev','上一页');
                $page->setConfig('next','下一页');
                $page->setConfig('first','首页');
                $page->setConfig('last','末页');
                $show = $page->show();
        //$show       = $Page->show();// 分页显示输出
                //共几页
                $aa=ceil($count/$num1);

        //新闻查询
               // $num=2;
                if ($num=I('num')) {
                   $notice=$news->where(array('seller_id'=>$seller_id))->order('time desc')->limit(12)->page($num)->select();
                }else{
                    $notice=$news->where(array('seller_id'=>$seller_id))->order('time desc')->limit($page->firstRow.','.$page->listRows)->select();
                }
        

        $this->assign('num',$aa);
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('count',$count);
        $this->assign('notice',$notice);
   
        $this->display();
	 }
    
}