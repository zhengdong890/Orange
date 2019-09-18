<?php
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class MechineController extends Controller {
    public function _initialize(){
        $redis = new \Com\Redis();
        $member_id = $_SESSION['member_data']['id'];
        /*获取导航缓存*/
        Hook::add('getNav','Home\\Addons\\NavAddon');
        Hook::listen('getNav');
        $navs = $redis->get('navs' , 'array');//获取redis的缓存
        /*获取购物车统计缓存*/
        Hook::add('totalCart','Home\\Addons\\totalAddon');
        Hook::listen('totalCart',$member_id);
        $this->assign('cart_total' , $_SESSION['cart_total']);
        /*底部帮助*/
        Hook::add('getFooterHelp','Home\\Addons\\helpAddon');
        Hook::listen('getFooterHelp');
        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
        $this->assign('navs' , $navs );
        $this->assign('help' , get_child($help));
    }
    
    public function index(){  	       
        $pid     = 72; //上级id
   	    $cat_id  = intval(I('cat_id'));//当前分类id
   	    $paramer = I();//获取查询参数   	
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
        if($paramer['cat_id']){//商品分类
    	    $where['a.cat_id'] = $paramer['cat_id'];
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
 	    /*价格区间段*/
 	    $price = array(
            array('500-min','0-500'),
            array('500-5000','500-5000'),
            array('5000-50000','5000-5万'),
            array('50000-100000','5万-10万'),
            array('100000-max','10万以上'),
 	    );
 	    $cats = M('Mall_category')
	   	      ->where(array('pid'=>array('in','2,34')))
	   	      ->field('id,cat_name,router')
	   	      ->order('sort')
	   	      ->select(); 	  
		$cat_ids  = array();
		$now_cat  = M('Mall_category')->where(array('id'=>$pid))->Field('router,cat_name')->find();
        $crumb[]  = array($now_cat['router'] , '机器人');
        foreach ($cats as $k => $v) {
      	    $cat_ids[] = $v['id'];
    	    if($cat_id == $v['id']){
                $crumb[] = array($v['router'] , $v['cat_name']);
    		}
        }
        $where['cat_id'] = array('in' , $cat_id ? "$cat_id" : implode(',' , $cat_ids));
        /*分页*/
        $count = M('Mall_goods as a')->where($where)->count();//统计商品数量 
        $Page  = new \Think\Page($count,16);// 实例化分页类 传入总记录数和每页显示的记录数(5)
        $show  = $Page->getPage();
        $goods = M('Mall_goods as a')
	           ->where($where)
	           ->field('id,goods_name,goods_price,goods_thumb,sale_num,safest')
               ->limit($Page->firstRow.','.$Page->listRows)
	           ->order('id desc,sort')
	           ->select(); 
        foreach($goods as $k=>$v){
            $seller_id[] = $v['member_id'];
        }
        $seller_id   = implode(',' , array_unique($seller_id));
        $seller_data_ = M('Member')->where(array('id'=>array('in' , $seller_id)))->field('id,is_renzheng')->select();
        foreach($seller_data_ as $v){
            $seller_data[$v['id']] = $v;
        }
        foreach($goods as $k=>$v){
            $goods[$k]['is_renzheng'] = $seller_data[$v['member_id']]['is_renzheng'];
        }      
	    /*获取品牌*/
		$cat_ids   = implode(',' , $cat_ids);
	    $brand_ids = array();
        $brand_arr = M('Mall_category_brand')
				   ->where(array('cat_id'=>array('in' , $cat_ids)))
				   ->field('brand_id')
				   ->select();
  		foreach($brand_arr as $k => $v){
            $brand_ids[$v['brand_id']] = $v['brand_id'];
  		}
	    $brand_ids = implode(',' , $brand_ids);
		$brands    = M('Goods_brand')
				   ->where(array('id'=>array('in' , $brand_ids),'status'=>1))
				   ->field('id,brand_name,brand_thumb')
				   ->select();  
        /*近期热卖商品*/
        $rents = M('Mall_goods')->limit(0,5)->order('sort')->select();
        /*url参数格式组装*/
        foreach($paramer as $k => $v){
            $paramer[$k] = "$k=$v";
        }
        unset($paramer['p']);unset($paramer['m']);
        $html  = pageHtml('http://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['PATH_INFO'] , $show , implode('&' , $paramer));
        $redis = new \Com\Redis();
        /*商城商品  redis缓存数据*/
        $cat_id = $cat_id ? $cat_id : $pid;
        Hook::add('categorySeo','Home\\Addons\\seoAddon');
        Hook::listen('categorySeo' , $arr = array($redis , $cat_id));
        $seo = $redis->get('seo_category' . $cat_id , 'array');//获取redis的缓存
        $this->assign('crumb',$crumb);
        $this->assign('html',$html);
        $this->assign('cat_id',$cat_id);
        $this->assign('price',$price);
	    $this->assign('brands',$brands);
	    $this->assign('count',$count);	
	    $this->assign('cats',$cats);		   	       
	    $this->assign('goods' , $goods);
        $this->assign('rents',$rents); 
        $this->assign('seo',$seo);
	    $this->display();   
	} 
}