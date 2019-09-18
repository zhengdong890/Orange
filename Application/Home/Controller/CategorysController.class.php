<?php
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class CategorysController extends Controller {
    public function _initialize(){
        $redis = new \Com\Redis();
        $member_id = $_SESSION['member_data']['id'];
        /*获取导航缓存*/
        Hook::add('getNav','Home\\Addons\\NavAddon');
        Hook::listen('getNav');
        $navs = $redis->get('navs' , 'array');//获取redis的缓存
        /*获取购物车统计缓存*/
        Hook::add('totalCart','Home\\Addons\\TotalAddon');
        Hook::listen('totalCart',$member_id);
        $this->assign('cart_total' , $_SESSION['cart_total']);
        /*底部帮助*/
        Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
        Hook::listen('getFooterHelp');
        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
        $this->assign('help' , get_child($help));
        $this->assign('navs' , $navs );
    }
    
    public function goodsList(){
   	    $cat_id  = intval(I('cat_id'))?intval(I('cat_id')):72;//当前分类id
        $pid     = $cat_id;
   	    $paramer = I();//获取查询参数  
        $where   = $this->getSearch();    	
        $redis = new \Com\Redis();
        /*分享商品分类   更新*/ 
        Hook::add('getCategory','Home\\Addons\\CategoryAddon');
        Hook::listen('getCategory');      
        $categorys = $redis->get('index_cattegory' , 'array');//获取redis的缓存
        /*当前选择的商品分类面包屑*/
        $crumb_cat = array_all_column($categorys , 'id');
        $crumb     = array(
            array($crumb_cat[$pid]['router'] , $crumb_cat[$pid]['cat_name']),
            
        );

        if($pi=I('pi')){//获取接收过来的pi判断类型
            $crumb1     = array(
            array($crumb_cat[$pi]['router'] , $crumb_cat[$pi]['cat_name']),
            
        );
        $this->assign('pi',$pi);
        }
              
        /*获取下级分类的所有id*/
        $categorys = array_all_column(get_child($categorys) , 'id');
        $cats      = $categorys[$pid]['child'];//当前的二级分类
       if(empty($cats)){
        $pid=I('pi');//当二级分类为空时获取默认id为72的二级分类
        $cats      = $categorys[$pid]['child'];//当前的二级分类
        $where['cat_id'] = array(
            'in' , 
            empty($cat_ids) ? "$cat_id" : implode(',' , $cat_ids)
        );
        $cat_ids   = array_column($cats , 'id');
       }else{
       $cat_ids   = array_column($cats , 'id');
       
        //查询条件组装    
        $where['cat_id'] = array(
            'in' , 
            empty($cat_ids) ? "$cat_id" : implode(',' , $cat_ids)
        );
       
   } 
        /*分页获取商品*/
        $count = M('Goods as a')->where($where)->count();//统计商品数量 
        $Page  = new \Think\Page($count,48);// 实例化分页类 传入总记录数和每页显示的记录数(5)
        $show  = $Page->getPage();
        $goods = M('Goods as a')
	           ->where($where)
	           ->field('id,goods_name,goods_price,goods_thumb,sale_num,member_id,safest,comment_number,rent_dw')
	           ->order('id desc,sort')
               ->limit($Page->firstRow.','.$Page->listRows)
	           ->select();
      
        /*获取卖家信息*/       
        $seller_id   = array_column($goods, 'member_id') ;
        $seller_id   = implode(',' , array_unique($seller_id));
        $seller_data = M('Member')
                     ->where(array('id'=>array('in' , $seller_id?$seller_id:'')))
                     ->field('id,is_renzheng')
                     ->select();
        $seller_data = array_all_column($seller_data , 'id');
        /*商品和卖家信息关联*/
	    foreach($goods as $k => $v){
	        $goods[$k]['is_renzheng'] = $seller_data[$v['member_id']]['is_renzheng'];
	    }  

        /*商品品牌数据缓存更新处理*/
        //商品分类 品牌更新
        Hook::add('updateCategoryBrand','Home\\Addons\\BrandAddon');
        Hook::listen('updateCategoryBrand');
        $category_brands = $redis->get('category_brands', 'array');//获取redis的缓存 
        //商品 品牌更新
        Hook::add('updateBrand','Home\\Addons\\BrandAddon');
        Hook::listen('updateBrand');
        $brands_ = $redis->get('brands', 'array');//获取redis的缓存
        //关联
        $brands = array();  
        foreach($category_brands as $v){
            if(in_array($v['cat_id'] , $cat_ids)){
                $brands[$v['brand_id']] = $brands_[$v['brand_id']];    
            }
        }   

        /*共享商品分类seo  redis缓存数据*/
        $cat_id = $cat_id ? $cat_id : $pid;
        Hook::add('categorySeo','Home\\Addons\\SeoAddon');
        Hook::listen('categorySeo' , $arr = array($redis , $cat_id));
        $seo = $redis->get('seo_category' . $cat_id , 'array');//获取redis的缓存  
        /*近期热租商品*/
        $rents = M('Goods')->limit(0,5)->order('sort')->select(); 
        //dump($rents);
        /*url参数格式组装*/
        foreach($paramer as $k => $v){
            $paramer[$k] = "$k=$v";
        }
        unset($paramer['p'] , $paramer['m'] , $paramer['pid'] , $paramer['cat_id']);
        $html  = pageHtml('http://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['PATH_INFO'] , $show , implode('&' , $paramer));//分页处理
        $this->assign('pid',$pid);
        $this->assign('paramers',json_encode(I()));
        $this->assign('crumb',$crumb);
        $this->assign('crumb1',$crumb1);

        $this->assign('html',$html);
        $this->assign('cat_id',$cat_id);
        $this->assign('price',C('GOODS_PRICE_SEARCH'));
	    $this->assign('brands',$brands);
	    $this->assign('count',$count);	
	    $this->assign('cats',$cats);
	    $this->assign('categorys', $categorys);
	    $this->assign('goods' , $goods);
        $this->assign('rents',$rents);  
        $this->assign('seo',$seo);
	    $this->display();     
    }
    
    /**
     *商品分类
     */		 	
    public function categoryList(){
        $redis = new \Com\Redis();
        /*分享商品分类   更新*/
        Hook::add('getCategory','Home\\Addons\\CategoryAddon');
        Hook::listen('getCategory');
        $categorys = $redis->get('index_cattegory' , 'array');//获取redis的缓存
    	$this->assign('categorys', get_child($categorys));
		$this->display(); 			 
	}

    /**
     * 获取查询条件
     */	
	private function getSearch(){
		$paramer = I();//获取查询参数
		$where   = array(
            'a.status'       => 1,
            'a.check_status' => 1,
            'a.is_check'     => 1,
		);   
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
    	    $where['a.brand_id'] = array('in' , $paramer['brand_id']);
        }
        if($paramer['province']){//商品区域
            $where['a.province'] = $paramer['province'];
        }
        if($paramer['city']){//商品区域
            $where['a.city'] = $paramer['city'];
        }           	
 	    return $where;
	}
}