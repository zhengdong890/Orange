<?php
namespace Home\Addons;
use Think\Controller;
/*
 * 统计扩展插件
 * */
class totalAddon extends Controller{
    /* 
     * 统计购物车商品数量
     * */
    public function totalCart($member_id){
        $cart_total = $_SESSION['cart_total']; 
        if(empty($$cart_total)){
            if($member_id){
                $count1 = M('Cart')->where(array('member_id'=>$member_id))->count();
                $count2 = M('Mall_cart')->where(array('member_id'=>$member_id))->count();
                $cart_total = $count1 + $count2;
            }else{
            	$count1 = is_array(unserialize($_COOKIE['cart'])) ? count(unserialize($_COOKIE['cart'])) : 0;      
            	$count2 = is_array(unserialize($_COOKIE['mall_cart'])) ? count(unserialize($_COOKIE['mall_cart'])) : 0;      	
                $cart_total = $count1 + $count2;   
            }
            $_SESSION['cart_total'] = $cart_total;                
        }
    }
    
    /*
     * 统计订单数量并存入redis缓存
     * */
    public function totalOrder($member_id){
        if(!$member_id){
            return;    
        }
        /*共享订单*/
        //订单状态集 付款状态,物流状态,评论状态,订单状态
        $share_status = array(
            '0,0,0,0' => 0, //未付款 未发货 未评论 未确认
            '0,0,0,1' => 0, //未付款 未发货 未评论 已确认
            '1,0,0,1' => 0, //已付款 未发货 未评论 已确认
            '1,1,0,1' => 0, //已付款 已发货 未评论 已确认
            '1,2,0,1' => 0, //已付款 已收货 未评论 已确认
            '1,2,1,2' => 0, //已付款 已收货 已评论 已完成
        );
        $order = M('Order')
               ->where(array('member_id'=>$member_id))
               ->field('status,pay_status,send_status,is_comment')
               ->select();
        foreach($order as $k => $v){
            $key = "{$v['pay_status']},{$v['send_status']},{$v['is_comment']},{$v['status']}";
            if(!$share_status[$key]){
                 $share_status[$key] = 1;
            }else{
                 $share_status[$key]++;
            } 
        }
        
        /*商城订单*/
        //订单状态集 付款状态,物流状态,评论状态,订单状态
        $mall_status = array(
            '0,0,0,0' => 0, //未付款 未发货 未评论 未确认   
            '0,0,0,1' => 0, //未付款 未发货 未评论 已确认
            '1,0,0,1' => 0, //已付款 未发货 未评论 已确认
            '1,1,0,1' => 0, //已付款 已发货 未评论 已确认
            '1,2,0,1' => 0, //已付款 已收货 未评论 已确认
            '1,2,1,2' => 0, //已付款 已收货 已评论 已完成
        );
        $order_total = array('share'=>array(),'mall'=>array());
        $mall_order = M('Mall_order')
                    ->where(array('member_id'=>$member_id))
                    ->field('status,pay_status,send_status,comment_status')
                    ->select();
        foreach($mall_order as $k => $v){
            $key = "{$v['pay_status']},{$v['send_status']},{$v['comment_status']},{$v['status']}";
            if(!$mall_status[$key]){
                $mall_status[$key] = 1;
            }else{
                $mall_status[$key]++;
            }                    
        }
        $_SESSION['order_total'] = array(
            'share'        => $share_status,
            'share_number' => array_sum($share_status),
            'mall'         => $mall_status,
            'mall_number'  => array_sum($mall_status),
        );
    }
}