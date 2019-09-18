<?php
/*
 * 设备商城
 * */
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class MallCategorysController extends Controller {	
    public function _initialize(){    	
        $redis = new \Com\Redis();
        $member_id = $_SESSION['member_data']['id'];
        /*获取导航缓存*/
        Hook::add('getNav','Home\\Addons\\NavAddon');
        Hook::listen('getNav');
        $navs = $redis->get('navs' , 'array');//获取redis的缓存
        $this->assign('navs' , $navs );
        /*获取购物车统计缓存*/
        Hook::add('totalCart','Home\\Addons\\TotalAddon');
        Hook::listen('totalCart' , $member_id);
        $this->assign('cart_total' , $_SESSION['cart_total']);
        /*底部帮助*/
        Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
        Hook::listen('getFooterHelp');
        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
        /*商城商品分类缓存   更新*/
        Hook::add('getCategory','Home\\Addons\\MallCategoryAddon');
        Hook::listen('getCategory');
        $mall_categorys = $redis->get('mall_category_tree' , 'array');  
        $this->assign('mall_categorys' , $mall_categorys);
        $this->assign('help' , $help);
    }
    
    public function goodsList(){
        $where   = $this->getSearch();//获取搜索条件
        $search_attr = I('attr');
        if($search_attr){
	        $search_attr    = explode('-' , $search_attr);
	        $attr_goods_ids = D('Search')->getGoodsByAttr($search_attr); 
	        if(count($attr_goods_ids) > 0){
                $where['id'] = array('in' , implode(',' , $attr_goods_ids));
	        }       	
        }  
   	    $cat_id  = intval(I('cat_id'));//当前分类id

   	    if($cat_id == 0){
            exit('请选择分类');
   	    }
        $redis   = new \Com\Redis();
        /*获取商城商品分类缓存*/
        $categorys = $redis->get('mall_category' , 'array');
        $mall_category_tree = array();
            foreach($categorys as $v){
                if($v['level'] != '4'){
                    $mall_category_tree[] = $v;
                }
            }  
        $mall_category_tree = get_child($mall_category_tree);
        foreach($mall_category_tree as $k=>$v){

        }
        $temp_cat  = array_all_column($categorys , 'id');
        //面包屑 处理
        $crumb    = D('MallCategory')->getCrumb($cat_id , $temp_cat);
        //当前分类的下级分类
        $next_cat = D('MallCategory')->getNextCategory($cat_id , $categorys);
        //dump($next_cat);
        foreach($next_cat as $k=>$v){
         $last_son = D('MallCategory')->getLastLevelCategory($v['id'] , $temp_cat);//获取当前分类下面的最底级
            $cat_ids  = array_column($last_son , 'id');
            $where['a.cat_id'] = array('in' , implode($cat_ids , ','));  
                // $next_cat[$k]['num'] = M('Mall_goods as a')
                //                     ->join('tp_shop_data as s ON s.member_id=a.member_id')
                //                     ->join('left join tp_sku as k on k.goods_id=a.id')
                //                     ->where(array('s.status'=>1,'a.status'=>1))
                //                     ->where($where)
                //                     ->count();//统计商品数量
        }
        //dump($next_cat);
        if(count($next_cat) <= 0){
            $cat_ids         = array($cat_id);
            $get_attr_where  = array('cat_id' => $cat_id);
            $last_son        = array($temp_cat[$cat_id]);
        }else{
            //所有的子级分类
            $last_son = D('MallCategory')->getLastLevelCategory($cat_id , $temp_cat);
            $cat_ids  = array_column($last_son , 'id');
            $where['cat_id'] = array('in' , implode($cat_ids , ','));
        }    
        if($temp_cat[$cat_id]['level'] >= 3){
            $attr_arr = array_column($last_son , 'filter_attr');
            $attr_ids = array();
            foreach($attr_arr as $v){
                $attr_ids = array_merge($attr_ids , explode(',' , $v));
            }
            $attr_ids = array_unique($attr_ids);
            $get_attr_where  = array(
               'attr_id'=> array('in' , implode($attr_ids , ','))
            );                         	
            //其他属性处理
            $attr = M('Attrbute')
	              ->where($get_attr_where)
                  ->where(array('status'=>1))
                  ->limit(6)
	              ->field('attr_id,attr_name,attr_value')
	              ->select();
            //dump($attr);
    	    	if(count($attr) > 0){
    		    	  //获取属性值
    		    	  $attr_id   = implode(',' , array_column($attr , 'attr_id'));
    		    	  $attr_temp =  M('Attrbute_value')
    			    	    ->where(array('attr_id'=>array('in' , $attr_id)))
    			    	    ->field('attr_value_id,attr_id,attr_value')
    			    	    ->select();
    			      if(count($attr_temp) > 0){
        			    	//组合  
        			    	foreach($attr_temp as $v){
        			            $attr_value[$v['attr_id']][] = $v;
        			    	}
        			    	foreach($attr as $k => $v){
        			            $attr[$k]['attr_value'] = $attr_value[$v['attr_id']];
        			    	}			            
    		    	  } 	   		
    	    	}      
        } 
	    /*获取品牌*/
        $category_brands = $redis->get('mall_category_brands', 'array');//获取redis的缓存 
        //关联

        $brands  = array();    
        $cat_arr = array_flip($cat_ids);
        /*查询商品 分页*/
        //$category_brands = array_slice($category_brands, 1,10);
        
        $where['a.cat_id'] = array('in' , implode($cat_ids , ','));         
        foreach($category_brands as $k => $v){
            if(isset($cat_arr[$v['cat_id']])){
            	$v['id']                = $v['brand_id'];
                // $v['number']            = M('Mall_goods as a')
                //                         ->join('tp_shop_data as s ON s.member_id=a.member_id')
                //                         ->join('left join tp_sku as k on k.goods_id=a.id')
                //                         ->where(array('s.status'=>1,'a.status'=>1))
                //                         ->where(array('a.brand_id'=>$v['brand_id']))
                //                         ->where($where)
                //                         ->count();//统计商品数量
            	$brands[$v['brand_id']] = $v;  
            }
        }
        //dump($brands);
        
        $count = M('Mall_goods as a')
                ->join('tp_shop_data as s ON s.member_id=a.member_id')
                ->join('left join tp_sku as k on k.goods_id=a.id')
                ->where(array('s.status'=>1,'a.status'=>1))
                ->where($where)
                ->count();//统计商品数量
        $Page  = new \Think\Page($count,50);// 实例化分页类 传入总记录数和每页显示的记录数(5)
        $show  = $Page->getPage();
        //按照价格降序排列
        if($price = I('low')){
            //dump($price);
            $order='a.goods_price desc';
            $goods =$this->Mall_goods_($where,$order,$Page);
            //按照销量降序排列
        }elseif($sales = I('sales')){
            $order='a.sale_num desc';
            $goods =$this->Mall_goods_($where,$order,$Page);
            //按照最新商品进行排序
        }elseif($news = I('news')){
            $order='a.update_time desc';
            $goods =$this->Mall_goods_($where,$order,$Page);
            //按照最佳人气进行排序
        }elseif($mem = I('mem')){
            $order='a.comment_number desc';
            $goods =$this->Mall_goods_($where,$order,$Page);
            //只显示在售商品
        }elseif($on_sale = I('on_sale')){
            $order='a.id desc,sort';
            $goods =$this->Mall_goods_($where,$order,$Page);
        }else{
            $order='a.id desc,sort';
            $goods =$this->Mall_goods_($where,$order,$Page);
        }
                  
        /*获取所有店铺的信息  更新*/   
        $all_shop_data = $redis->get('all_shop_data' , 'array');//获取redis的缓存
        foreach($goods as $k=>$v){
            if($all_shop_data[$v['member_id']]['status']){
                $goods[$k]['shop_name'] = $all_shop_data[$v['member_id']]['shop_name'];
                $goods[$k]['domain']    = $all_shop_data[$v['member_id']]['domain']?$all_shop_data[$v['member_id']]['domain']:$v['member_id'];
            }else{
                unset($goods[$k]);
            }
        }
        
        /*近期热卖商品*/
        $hot_goods = M('Mall_goods')->limit(0,5)->order('sort')->select();
        foreach($hot_goods as $k=>$v){
            $hot_goods[$k]['domain'] = $all_shop_data[$v['member_id']]['domain']?$all_shop_data[$v['member_id']]['domain']:$v['member_id'];
        }
        /*url参数格式组装*/
        $paramer  = I();//获取查询参数
        
        foreach($paramer as $k => $v){
            $paramer[$k] = "$k=$v";
        }
        
        unset($paramer['p']);unset($paramer['m']);unset($paramer['cat_id']);unset($paramer['pid']);
        $html  = pageHtml('http://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['PATH_INFO'] , $show , implode('&' , $paramer));
        /*seo*/
        Hook::add('getSeo','Home\\Addons\\MallCategorySeoAddon');
        Hook::listen('getSeo' , $cat_id);
        $seo = $redis->get('mall_category_seo' . $cat_id , 'array');//获取redis的缓存    
        //搜索价格区间段
 	    $price    = C('MALL_GOODS_PRICE_SEARCH');
        //商品打折
        $time = date('Y-m-d H:i:s');

        $map['end_time'] = array('gt',$time);//过滤大于活动结束时间

        $rele = M('Release_activity')
                    ->where($map)
                    ->field('title,scope,seller_id,min_max,favourable,goods_id')
                    ->select();	
       

        foreach($goods as $k=>$v){
            foreach($rele as $k1=>$v1){
                $temp = explode(',', $v1['goods_id']);
                if($v1['scope']==1){
                    if($v1['seller_id']==$v['member_id']){
                        $goods[$k]['title']=$v1['title'];
                        $goods[$k]['min_max']=$v1['min_max'];
                        $goods[$k]['favourable']=$v1['favourable'];
                        $goods[$k]['scope']=$v1['scope'];
                    }
                    $goods[$k]['title']=$v1['title'];

                }else{
                   if(in_array($v['id'], $temp)){ 
                   
                    $goods[$k]['title']=$v1['title'];
                    $goods[$k]['min_max']=$v1['min_max'];
                    $goods[$k]['favourable']=$v1['favourable'];
                    $goods[$k]['scope']=$v1['scope'];
                   } 
                }
                
               
            }
        }
        $end_crumb = end($crumb);
        $this->assign('end_crumb',$end_crumb);
 	    $this->assign('level' , $temp_cat[$cat_id]['level']);
        $this->assign('paramers',json_encode(I()));
        $this->assign('crumb',$crumb);
        $this->assign('next_cat',count($next_cat) > 0 ? $next_cat : '');
        $this->assign('html',$html);
        $this->assign('cat_id',$cat_id);
        //dump($cat_id);
        $this->assign('price',$price);
        $this->assign('seo',$seo);
	    $this->assign('brands',$brands);
	    $this->assign('count',$count);	
	    $this->assign('attr',$attr);
        $this->assign('categorys',$categorys);
	    $this->assign('goods' , $goods);
        $this->assign('hot_goods',$hot_goods);
	    $this->display();    
    }
    public function Mall_goods_($where,$order,$Page){
        $goods = M('Mall_goods as a')
                ->join('tp_shop_data as s ON s.member_id=a.member_id')
                ->where(array('s.status'=>1,'a.status'=>1))
                ->join('left join tp_sku as k on k.goods_id=a.id')
               ->where($where)
               ->field('a.id,a.caiji_id,k.term,k.sale_num,a.goods_name,a.goods_price,a.goods_thumb,a.sale_num,a.member_id,a.comment_number,s.status')
               ->limit($Page->firstRow.','.$Page->listRows)
               ->order($order)
               ->select();
      return $goods;
    }

    /**
     * 获取查询条件
     */	
	private function getSearch(){
		$paramer = I();//获取查询参数
        $where['a.status'] = 1;    	
 	    /*生成查询条件*/
 	    if($paramer['price']){//价格区间
 	    	$price_arr = explode('-' , $paramer['price']);
 	    	if($price_arr[1] == 'max'){
              $where['a.goods_price'] = array('egt' , $price_arr[0]);
 	    	}else
 	    	if($price_arr[1] == 'min'){
              $where['a.goods_price'] = array('elt' , $price_arr[0]);
 	    	}else{
              $where['a.goods_price'] = array(between,array($price_arr[0],$price_arr[1]));
 	    	}   	
 	    }  
 	    if($paramer['brand_id']){//商品品牌
 	    	$brand_id = explode(',',$paramer['brand_id']);
 	    	$brand_id = array_filter($brand_id);
 	    	$where['a.brand_id'] = array('in' , $brand_id);
 	    }
	    if($paramer['province']){//商品区域
	        $where['a.province'] = $paramer['province'];
	    }
	    if($paramer['city']){//商品区域
	        $where['a.city'] = $paramer['city'];
	    }         		    
 	    return $where;
	}  

  /**
    *商品分类
    */		 	
    public function categoryList(){     
        $redis = new \Com\Redis(); 
        /*商城商品分类缓存   更新*/
        Hook::add('getCategory','Home\\Addons\\MallCategoryAddon');
        Hook::listen('getCategory');
        $categorys = $redis->get('mall_category' , 'array'); 
        foreach($mall_categorys as $k => $v){
            if($v['level'] != '4'){
                $temp_categorys[$v['id']] = $v;   
            }
        }
        $categorys = D('MallCategory')->getTwoTree($temp_categorys);  
        /*获取Seo缓存*/
        Hook::add('mallCategorysSeo','Home\\Addons\\SeoAddon');
        Hook::listen('mallCategorysSeo',$redis);
        $seo = $redis->get('seo_mall_category' , 'array');
        $this->assign('seo',$seo);
        $this->assign('categorys' , $categorys);
	    $this->display(); 			 
    }
}