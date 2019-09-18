<?php
/*
 * 卖家中心 优惠券处理
 * */  
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class ShopCouponController extends Controller {	
	public function _initialize(){
        if(empty($_SESSION['member_data'])){
            if(IS_AJAX || IS_POST){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'msg'   => '请登录'
                ));
            }else{
                header("Location:http://www.orangesha.com/login.html");
            }
        }
        if(IS_GET){
            $id = $_SESSION['member_data']['id'];  
            $redis = new \Com\Redis();       
            unset($_SESSION['order_total']);
            /*订单数量统计处理*/
            Hook::add('totalOrder','Home\\Addons\\TotalAddon');
            Hook::listen('totalOrder',$id);
            /*购物车统计处理*/
            Hook::add('totalCart','Home\\Addons\\TotalAddon');
            Hook::listen('totalCart',$id);    
            /*底部帮助*/
            Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
            Hook::listen('getFooterHelp');
            $help = $redis->get('footer_help' , 'array');//获取redis的缓存
            $this->assign('help' , $help);
            $this->assign('order_total' , $_SESSION['order_total']);
            $this->assign('cart_total' , $_SESSION['cart_total']);
        }
    }
    
    /*
     * 优惠券列表
     * */    
    public function coupon(){
        $member_data = $_SESSION['member_data']['id'];
        $list      = M('Shop_coupons')->where(array('seller_id'=>$member_data))->select();
        //dump($list);
        $this->assign('list',$list);
        $this->display();
    }

    public function couponsList(){
       
        if(IS_AJAX){
            $seller_id = $_SESSION['member_data']['id'];
            $list      = M('Shop_coupons')->where(array('seller_id'=>$seller_id))->select();
            $count     = M('Shop_coupons')->where(array('seller_id'=>$seller_id))->count();
            $this->ajaxReturn(array('status'=>1,'msg'=>'ok','data'=>$list,'totalRows'=>$count));
        }else{
           
            $this->display();
        }       
    }
    
    /*
     * 添加优惠券
     * */
    public function couponAdd(){
       if(IS_POST){
            $data   = I();
            $data['seller_id'] = $_SESSION['member_data']['id'];
            $r = D('ShopCoupons')->checkCoupon($data);
            if($r['status'] == 0){
                $this->ajaxReturn($r);
            }
            $result = D('ShopCoupons')->couponAdd($data);
            $this->ajaxReturn($result);
       }
    }
 
    /*
     * 添加优惠券
     * */
    public function couponUpdate(){
        if(IS_AJAX){
            $data      = I();
            $seller_id = $_SESSION['member_data']['id'];
            $r = D('ShopCoupons')->checkCoupon($data);
            if($r['status'] == 0){
                $this->ajaxReturn($r);
            }
            $result = D('ShopCoupons')->couponUpdate($data , $seller_id);
            $this->ajaxReturn($result);
        }
    }

    /*
     * 删除优惠券
     * */
    public function couponDelete(){
        if(IS_AJAX){
            $id     = intval(I('id'));
            if(!$id){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'msg'    => '请输入优惠券id'
                ));die;
            }
            $result = D('ShopCoupons')->couponDelete($id , $_SESSION['member_data']['id']);
            $this->ajaxReturn($result);
        }
    }

    public function edit(){
        if(IS_AJAX){
            $id = I('id');
            $seller_id = $_SESSION['member_data']['id'];
            $shop_coupons = M('Shop_coupons')->where(array('seller_id'=>$seller_id,'id'=>$id))->find();
            $this->ajaxReturn($shop_coupons);
        }
    }

}