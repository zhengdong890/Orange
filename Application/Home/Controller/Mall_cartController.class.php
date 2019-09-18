<?php
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class Mall_cartController extends Controller {	     
    /*
     * ajax 商品加入购物车
     * */
    public function cartAdd(){
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
                $result = D("MallCart")->cartAdd($data);
                $this->ajaxReturn($result);
            }else{
                $this->ajaxReturn(array('status'=>1,'msg'=>'加入成功'));
            }
        }
    }

    /*
     * ajax 修改购物车商品数量
     * */
    public function cartChangeNumber(){
        if(IS_AJAX){
            $data   = I();
            $result = D('MallCart')->cartChangeNumber($data['id'] , $data['number']);
            $this->ajaxReturn($result);
        }
    }
    
    /*
     * ajax 删除购物车
     * */
    public function cartDelete(){
        if(IS_AJAX){
            $id = intval(I('id'));
            $member_id = $_SESSION['member_data']['id'];
            if($member_id){
                if($id == 0){
                     $this->ajaxReturn(array('status'=>'0' , 'msg'=>'id错误')); 
                }
                $r = M('Mall_cart')->where(array('id'=>$id,'member_id'=>$member_id))->delete();
                if($r === false){
                    $this->ajaxReturn(array('status'=>0,'msg'=>'error'));
                }else{
                    unset($_SESSION['cart_total']);
                    $this->ajaxReturn(array('status'=>1,'msg'=>'ok'));
                }                
            }else{
                $cart = $_COOKIE['mall_cart'];
                $cart = unserialize($cart);
                if(empty($cart)){
                    $this->ajaxReturn(array('status'=>'0' , 'msg'=>'购物车暂无商品')); 
                } 
                if(!isset($cart[$id])){
                    $this->ajaxReturn(array('status'=>'0' , 'msg'=>'购物车无该商品')); 
                }
                unset($cart[$id]);
                setcookie('mall_cart' , serialize($cart) , time() + 3600 * 24 , "/" , '.orangesha.com');
                $this->ajaxReturn(array('status'=>'1' , 'msg'=>'ok')); 
            }  
        }
    }  

    /*
     * ajax批量  删除购物车
     * */
    public function cartAllDelete(){
        if(IS_AJAX){
            $cart_ids  = I();
            $member_id = $_SESSION['member_data']['id'];
            if($member_id){
                $result = D('MallCart')->cartAllDelete($member_id , $cart_ids);
                $this->ajaxReturn($result);
            }else{
                $cart = $_COOKIE['mall_cart'];
                $cart = unserialize($cart);
                if(empty($cart)){
                    $this->ajaxReturn(array('status'=>'0' , 'msg'=>'购物车暂无商品')); 
                } 
                foreach($cart_ids as $v){
                    unset($cart[$v]);
                }     
                setcookie('mall_cart' , serialize($cart) , time() + 3600 * 24 , "/" , '.orangesha.com');
                $this->ajaxReturn(array('status'=>'1' , 'msg'=>'ok'));          
            }
        }
    }
    
    /*
     * ajax 直接购买商品是否可以购买验证
     * */    
    public function getSeller(){
        if(IS_AJAX){
            $data = I();
            $seller_id = M('Mall_goods')->where(array('goods_id'=>$data['goods_id']))->getField('member_id');
            $renzheng  = M('Member')->where(array('id'=>$seller_id))->getField('is_renzheng');
            if(!$renzheng){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'msg'    => '该商家未认证'
                ));
            }
            if($_SESSION['member_data']['id']){
                $this->ajaxReturn(array(
                    'status' => 1,
                    'msg'    => 'ok'
                ));
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'未登录'));
            }
        }
    }
    
    public function cartList(){
    	$this->display();
    }

    public function getCartGoods(){
    	if(IS_AJAX){
            $member_id = $_SESSION['member_data']['id'];
            if(!$member_id){
                $cart_data = D('MallCart')->getCartByMemberId();
                $this->ajaxReturn(array('status'=>1,'data'=>$cart_data));  
            }else{
                //获取购物车 数据
                $cart_data = D('MallCart')->getCartByMemberId($member_id);
                //获取卖家优惠券
                $seller_id = array_column($cart_data , 'member_id');
                $coupons   = D('ShopCoupons')->getCouponsBySellerId($seller_id);
                if($cart_data){
                    $this->ajaxReturn(array(
                    'status'  => 1,
                    'data'    => $cart_data,
                    'coupons' => $coupons
                )); 
                }
		       
	        } 		
    	}
    }  
  	     
}