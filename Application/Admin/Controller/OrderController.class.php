<?php
/*
 * 共享商品订单
 * */
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class OrderController extends CommonController{
    
/**************************************审核通过的共享订单**************************************************/
   
   /*
    * 订单列表
    * */    
   public function orderList(){
       $this->display();
   }
   
   /*
    * 获取订单的详细信息
    * */
   public function getOrderData(){
       if(IS_AJAX){
           $order_id = I('order_id');
           $data     = M('Order_data')->where(array('order_id'=>$order_id))->select();
           $this->ajaxReturn(array('data'=>$data));
       }
   } 
   
   /*获取共享订单*/
   public function getOrder(){
       if(IS_AJAX){
           $where['status'] = array('neq' , 0);
           if(I('order_sn')){
               $where['order_sn'] = I('order_sn');
           }
           if(I('status') === 0 || I('status')){
               $where['status'] = intval(I('status'));
           }
           if(I('send_status') === 0 || I('send_status')){
               $where['send_status'] = intval(I('send_status'));
           }
           $start_time = '1980-02-02';
           $end_time   = '2222-02-02';
           if(I('start_time')){
               $start_time = I('start_time');
           }
           if(I('end_time')){
               $end_time = I('end_time');
           }
           $where['time']   = array(between,array($start_time , $end_time));
           $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
           $listRows = intval(I('listRows'))?intval(I('listRows')):10;
           $list     = M('Order')->order('id desc')->limit($firstRow,$listRows)->where($where)->select();
           $this->ajaxReturn(array('data'=>$list,'total'=>M('Order')->where($where)->count()));
       }
   }
   
   
/**************************************共享订单审核**************************************************/   
   
   /*
    * 订单审核列表
    * */
   public function checkList(){
       $this->display();
   }
   
   /*获取共享审核订单*/
   public function getCheckList(){
       if(IS_AJAX){
           $where['status'] = 0;
           $start_time = '1980-02-02';
           $end_time   = '2222-02-02';
           if(I('order_sn')){
               $where['order_sn'] = I('order_sn');
           }
           if(I('start_time')){
               $start_time = I('start_time');
           }
           if(I('end_time')){
               $end_time = I('end_time');
           }
           $where['time']   = array(between,array($start_time , $end_time));
           $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
           $listRows = intval(I('listRows'))?intval(I('listRows')):10;
           $list     = M('Order')->order('id desc')->limit($firstRow,$listRows)->where($where)->select();
           foreach($list as $v){
               $ids[] = $v['seller_id'];
           }
           $ids = implode(',' , array_unique($ids));          
           /*获取卖家信息*/
           $seller_data_  = M('Member_data')
                          ->where(array('member_id'=>array('in' , $ids)))->field("member_id,telnum,nickname,name")
                          ->select();
           foreach($seller_data_ as $k => $v){
               $seller_data[$v['member_id']] = $v;
           }
           foreach($list as $k => $v){
               $list[$k]['seller_data'] = $seller_data[$v['seller_id']];
           }
           $this->ajaxReturn(array('data'=>$list,'total'=>M('Order')->where($where)->count()));
       }
   }
   
   /*
    * 审核订单
    * */
   public function orderCheck(){
       if(IS_POST){
           $data   = I();
           $result = D('Order')->orderCheck($data);
           $this->ajaxReturn($result);
       }
   }
   
   
   public function index(){
   	 $list=M('Order')->select();  	  	
   	 $this->list=$list;
   	 $this->display();
   }
   
   public function order(){
	    $id=$_GET['id'];
	    $list=M('Order')->where('id='.$id)->find();
	    $a=M('Member')->where('id='.$list['memberid'])->Field('name,telnum,email,username')->find();
	    $b=M('Orderdata')->where('orderid='.$list['id'])->select();
	    $list['name']=$a['name'];
	    $list['telnum']=$a['telnum'];
	    $list['email']=$a['email'];
	    $list['username']=$a['username'];
	    $list['pctname']=$b;	    
	    $this->list=$list;
	   	$this->display();
   }
   
   public function deleteorder(){
   	 if(IS_POST){
   	 	$id=$_POST['id'];
   	 	M('Orderdata')->where('orderid='.$id)->delete();   	 	
   	 	M('Order')->where('id='.$id)->delete();  
   	 }
   }
   
   public function recharge(){
   	  $list=M('Recharge')->select();
   	  $this->list=$list;
   	  $this->display();
   }
   
   public function deleterecharge(){
   	if(IS_POST){
   		$id=$_POST['id'];
   		M('Recharge')->where('id='.$id)->delete();
   	}
   }
}