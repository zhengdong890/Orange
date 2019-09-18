<?php
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class CartController extends Controller {	
    public function _initialize(){
        if(!$_SESSION['member_data']['id']){
             //$this->ajaxReturn(array('status'=>0,'code'=>'10','msg'=>'未登录'));
        }
    }
    
    public function cartList(){
        $this->display();
    }

    /*
     * ajax 商品加入购物车
     * */
    public function cartAdd(){
        if(IS_AJAX){
            $data = I();
            $seller_id = M('Goods')->where(array('goods_id'=>$data['goods_id']))->getField('member_id');
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
                $result = D("Cart")->cartAdd($data);
                unset($_SESSION['cart_total']);
                $this->ajaxReturn($result);   
            }else{
                $cart = is_array(unserialize($_COOKIE['cart']))? unserialize($_COOKIE['cart']) : array();
                $cart[] = $data;
                setcookie('cart' , serialize($cart) , time() + 3600 * 24 , "/" , '.orangesha.com');
                $this->ajaxReturn(array('status'=>1,'msg'=>'ok'));
            }          
             

        }
    }
    
    /*
     * ajax 修改购物车商品数量
     * */
    public function cartChangeNumber(){
        if(IS_AJAX){
            $data   = I();
            $result = D('Cart')->cartChangeNumber($data['id'] , $data['number']);
            $this->ajaxReturn($result);
        }
    }

    /*
     * ajax 修改购物车商品租期
     * */
    public function cartChangeTime(){
        if(IS_AJAX){
            $data   = I();
            $result = D('Cart')->cartChangeTime($data['id'] , $data['rent_time']);
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
            $r = M('Cart')->where(array('id'=>$id,'member_id'=>$member_id))->delete();
            if($r === false){
                $this->ajaxReturn(array('status'=>0,'msg'=>'error'));
            }else{
                unset($_SESSION['cart_total']);
                $this->ajaxReturn(array('status'=>1,'msg'=>'ok'));
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
            $result    = D('Cart')->cartAllDelete($member_id , $cart_ids);
            $this->ajaxReturn($result);   
        }
    }
    
    public function getSeller(){
        if(IS_AJAX){
            $data = I();
            $seller_id = M('Goods')->where(array('goods_id'=>$data['goods_id']))->getField('member_id');
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

    public function goodsUpdate(){
    	if(IS_AJAX){
    		$goods_id = I('id');
    		if(I('number')){
    			$data['number'] = I('number');
    		}
    		if(I('rent_time')){
                $data['rent_time'] = I('rent_time');
    		}
    		$result   = D('Cart')->goodsAdd($goods_id , $data);
    		$this->axjaxReturn($result);
    	}
    }

    public function getCartGoods(){
    	if(IS_AJAX){
            $member_id = $_SESSION['member_data']['id'];
            if(!$member_id){
                $cart = $_COOKIE['cart'];
                if(!$cart){
                    $this->ajaxReturn(array('data'=>array())); 
                } 
                /*获取购物车商品*/
                $field    = 'id,goods_thumb,goods_price,goods_name,member_id,goods_number,max_rent,min_rent';
                $cart     = unserialize($cart);
                $goods_id = implode(',' , array_column($cart , 'goods_id'));
                $carts    = M('Goods')
                          ->where(array('id'=>array('in' , $goods_id)))
                          ->field($field)
                          ->select(); 
                $carts    = array_all_column($carts , 'id');
                foreach($cart as $k => $v){
                    $cart[$k]['goods_thumb']  = $carts[$v['goods_id']]['goods_thumb'];
                    $cart[$k]['goods_price']  = $carts[$v['goods_id']]['goods_price'];
                    $cart[$k]['goods_name']   = $carts[$v['goods_id']]['goods_name'];
                    $cart[$k]['member_id']    = $carts[$v['goods_id']]['member_id'];
                    $cart[$k]['goods_number'] = $carts[$v['goods_id']]['goods_number'];
                    $cart[$k]['max_rent']     = $carts[$v['goods_id']]['max_rent'];
                    $cart[$k]['min_rent']     = $carts[$v['goods_id']]['min_rent'];
                }
                $this->ajaxReturn(array('data'=>$cart)); 
            }else{
                /*获取购物车商品*/
                $field = 'a.rent_number,a.rent_time,a.id as cart_id,b.id,b.goods_thumb,b.goods_price,b.goods_name,b.member_id,b.max_rent,b.min_rent,b.goods_number';
                $carts = M('Cart as a')
                       ->join('tp_goods as b on a.goods_id=b.id')
                       ->where(array('a.member_id'=>$member_id))
                       ->field($field)
                       ->select();
                foreach($carts as $k => $v){
                    $goods_ids[] = $v['id'];           
                }
                $goods_ids  = implode(',' , $goods_ids);
                /*获取商品租金优惠区间*/
                $goods_rent = M('Goods_rent')->where(array('goods_id'=>array('in',$goods_ids)))->select();
                $this->ajaxReturn(array('data'=>$carts,'goods_rent'=>$goods_rent));                
            }  		
    	}

    }
}