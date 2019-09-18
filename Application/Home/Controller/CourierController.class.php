<?php
/*
 * 快递选择设置
 * */
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class CourierController extends Controller{	 
    /*获取快递公司*/
    public function getCouriersCompany(){
        if(IS_AJAX){
            if(empty($_SESSION['member_data'])){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请先登录'));
            }
            $list = M('Courier')->select();
            $this->ajaxReturn(array('status'=>1,'msg'=>'ok','data'=>$list));
        }
    }
    
    /*选择快递公司*/
    public function selectCouriersCompany(){
        if(IS_AJAX){
            if(empty($_SESSION['member_data'])){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请先登录'));
            }
            $data   = I();
            $result = D('Courier')->selectCouriersCompany($_SESSION['member_data']['id'] , $data);
            $this->ajaxReturn($result);
        }
    }
    
    /*取消选择的快递公司*/
    public function awayCouriersCompany(){
        if(IS_AJAX){
            if(empty($_SESSION['member_data'])){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请先登录'));
            }
            $data   = I();
            $result = D('Courier')->awayCouriersCompany($_SESSION['member_data']['id'] , $data['id']);
            $this->ajaxReturn($result);
        }
    }
}