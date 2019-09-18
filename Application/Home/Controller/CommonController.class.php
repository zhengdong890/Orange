<?php
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class CommonController extends Controller {  
    public function _initialize(){
        if($_SESSION['member_data'] && empty($_SESSION['cart_number'])){
        	$member_id = $_SESSION['member_data']['id'];
        	$number_1  = M('Cart')->where(array('member_id'=>$member_id))->count();
        	$number_2  = M('Mall_cart')->where(array('member_id'=>$member_id))->count();
        	$number    = $number_1 + $number_2;
        	$_SESSION['cart_number'] = $number;
        }      
    }
}