<?php
/*
 * 商城商品
 * */
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class Mall_goodsController extends Controller {	
    public function _initialize(){

    }  

    /*
     * 商品详情页
     * */    
    public function goods(){
        $goods_id   = I('goods_id'); //商品id
        $goods      = M('Mall_goods')->where(array('id'=>$goods_id))->find(); //商品信息
        $goods_data = M('Mall_goods_data')->where(array('goods_id'=>$goods_id))->find();

        $goods_extendattr = D("Mall_goods")->getGoodsExtendAttr($goods_id); //获取商品扩展属性
        /*商品相册*/
        $goods_gallery = M("Mall_goods_gallery")->where(array('goods_id'=>$goods_id))->select();
        if($goods_gallery){
            array_unshift($goods_gallery,array('gallery_img'=>$goods['goods_thumb']));
        }else{
            $goods_gallery[0] = array('gallery_img'=>$goods['goods_thumb']);
        }       
        $goods_hot  = M("Mall_goods")->limit(0,4)->select();  //最热 
        $area_str ="{$goods['province']},{$goods['city']},{$goods['area']}";
        $area = array();
        /*获取商品区域*/
        if($goods['province']){	        
	        $area = M('Area')
		          ->where(array('area_no'=>array('in',$area_str)))
		          ->Field('area_level,area_name')->select();        	
		}
		/*品牌*/
		$goods['brand_name'] = M('Goods_brand')->where(array('id'=>$goods['brand_id']))->getField('brand_name');
		
		$redis = new \Com\Redis();
		$seller_id = $goods['member_id'];
		/*获取店铺数据*/
		$shop_data = $redis->get('shop_data'.$seller_id , 'array');//获取redis的缓存
		if(!$shop_data){
		    $shop_data = M('Shop_data')->where(array('member_id'=>$seller_id))->find();
		    $redis->set('shop_data'.$seller_id , serialize($shop_data));//设置redis的缓存
		}
		/*获取导航*/
		//$nav = $redis->get('shop_nav'.$seller_id , 'array');//获取redis的缓存
		if(!$nav){
		    $nav = M('Shopping')
    		     ->where(array('member_id'=>$seller_id,'status'=>1))
    		     ->order('rsort')
    		     ->select();
		    $redis->set('shopping'.$seller_id , serialize($nav));//设置redis的缓存
		}
		/*获取导航css*/
		$nav_css = $redis->get('nav_css'.$seller_id , 'array');//获取redis的缓存
		if(!$nav_css){
		    $nav_css = M('Shop_nav_css')->where(array('member_id'=>$seller_id))->getField('background_color');
		    $redis->set('nav_css'.$seller_id , serialize($nav_css));//设置redis的缓存
		}
        $shop_da = M('Shop_data')->where(array('member_id'=>$seller_id))->find();		 
		$shopcat = M('shop_category')->where(array('member_id'=>$seller_id,'status'=>1))->order('sort asc')->select();		
		$shopcat = getLayer($shopcat);
		$help = D('HelpCategory')->redisCatName($redis);
		$shop_css = M('shop_nav_css')->where(array('member_id'=>$seller_id))->getField('background_color');		
		$this->assign('shop_css',$shop_css);
	    $this->assign('shop_da' , $shop_da);
		$this->assign('shop_data' , $shop_data);
		$this->assign('shopcat',$shopcat);	
		$this->assign('shop_nav' , $nav);
		$this->assign('nav_css' , $nav_css);
		$this->assign('help' , get_child($help));	    
		$this->assign('area',$area);      
        $this->assign('goods',$goods);
        $this->assign('goods_data',$goods_data);
        $this->assign('goods_hot',$goods_hot);
        $this->assign('seller_id' , $seller_id);
        $this->assign('goods_gallery',$goods_gallery);//html_entity_decode
        $this->assign('goods_extendattr',$goods_extendattr);
        $this->display();
    }
	
	

}