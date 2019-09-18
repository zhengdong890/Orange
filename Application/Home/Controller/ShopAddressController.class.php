<?php
/*
 * 商家地址库设置
 * */
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class ShopAddressController extends Controller{	 
    /*获取发布地址列表*/
    public function getShopAddress(){
        if(IS_AJAX){
            if(empty($_SESSION['member_data'])){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请先登录'));
            }
            $list = M('Shop_address')->select();
            $this->ajaxReturn(array('status'=>1,'msg'=>'ok','data'=>$list));
        }
    }
    
    /*添加发布地址*/
    public function shopAddressAdd(){
        if(IS_AJAX){
            if(empty($_SESSION['member_data'])){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请先登录'));
            }
            $data = I();
            $data['seller_id'] = $_SESSION['member_data']['id'];
            $result = D('ShopAddress')->shopAddressAdd($data);
            $this->ajaxReturn($result);
        }
    }
    
    /*编辑发布地址*/
    public function shopAddressUpdate(){
        if(IS_AJAX){
            if(empty($_SESSION['member_data'])){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请先登录'));
            }
            $data   = I();
            $result = D('ShopAddress')->shopAddressUpdate($_SESSION['member_data']['id'] , $data);
            $this->ajaxReturn($result);
        }
    }  
    
    /*删除发布地址*/
    public function shopAddressDelete(){
        if(IS_AJAX){
            if(empty($_SESSION['member_data'])){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请先登录'));
            }
            $id = I('id');
            $result = D('ShopAddress')->shopAddressDelete($_SESSION['member_data']['id'] , $id);
            $this->ajaxReturn($result);
        }
    }
    
    /*修改地址是发货还是退货地址*/
    public function changeType(){
        if(IS_AJAX){
            if(empty($_SESSION['member_data'])){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请先登录'));
            }
            $id     = I('id');
            $type   = I('type');
            $result = D('ShopAddress')->changeType($_SESSION['member_data']['id'] , $id , $type);
            $this->ajaxReturn($result);
        } 
    }
}