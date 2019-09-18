<?php
namespace Shop\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class CartController extends Controller {	     
    /*
     * ajax 商品加入购物车
     * */
    public function cartAdd(){
        if(IS_AJAX){
        	$data      = stripslashes(I('sku_data'));
            $data      = htmlspecialchars_decode($data);
            $data      = json_decode($data , true);
            $goods_id  = I('goods_id');
            if($goods_id == 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'商品id错误'));
            }
            $member_id = $_SESSION['member_data']['id'];
            $r = D('Home/MallCart')->checkCartData($data , $goods_id , $member_id);
            if(!$r['status']){
                $this->ajaxReturn($r);
            }  
            array_walk($data, function(&$v , $k , $goods_id){
                $v['goods_id'] = $goods_id;
            },$goods_id);         
            if($member_id){
                $result = D("Home/MallCart")->cartAdd($data , $member_id);
                $this->ajaxReturn($result);
            }else{
            	$cart = is_array(unserialize($_COOKIE['mall_cart']))? unserialize($_COOKIE['mall_cart']) : array();
            	$cart = array_merge($cart , $data);
                setcookie('mall_cart' , serialize($cart) , time() + 3600 * 24 , "/" , '.orangesha.com');
                $result = array('status'=>1,'msg'=>'ok');                
            }
            if($result['status'] == 1){
            	unset($_SESSION['cart_total']);
            }
            $this->ajaxReturn($result);
        }
    } 

/************************************************以前商品预留功能******************************************/
    
    /*
     * ajax 商品加入购物车
     * */
    public function cartOldAdd(){
        if(IS_AJAX){
            $data = I();
            $seller_id = M('Mall_goods')->where(array('goods_id'=>$data['goods_id']))->getField('member_id');
            $renzheng  = M('Member')->where(array('id'=>$seller_id))->getField('is_renzheng');
            if(!$renzheng){
                $this->ajaxReturn(array(
                   'status' => 0,
                   'code'   => 11,
                   'msg'    => '该商家未认证'
                ));
            }
            if($_SESSION['member_data']['id']){
                $data['member_id'] = $_SESSION['member_data']['id'];
                $result = D("Home/MallCart")->cartOldAdd($data);
                $this->ajaxReturn($result);
            }else{
                $cart = is_array(unserialize($_COOKIE['mall_cart']))? unserialize($_COOKIE['mall_cart']) : array();
                $cart[] = $data;
                setcookie('mall_cart' , serialize($cart) , time() + 3600 * 24 , "/" , '.orangesha.com');
                $this->ajaxReturn(array('status'=>1,'msg'=>'ok'));
            }
        }
    }         
}