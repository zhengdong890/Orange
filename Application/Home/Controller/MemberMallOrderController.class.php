<?php
/*
 * 买家商城商品订单处理
* */
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class MemberMallOrderController extends Controller {
    public function _initialize(){
        if(empty($_SESSION['member_data'])){
            $this->redirect('Member/login');
        }
    }
    
    /*
     * 买家申请退款
     * */
    public function refunds(){
        if(IS_POST){
            $data = I();
        }
    }
}