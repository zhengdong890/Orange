<?php
namespace Admin\Model;
use Think\Model;
/**
 * 共享商品订单模块业务逻辑
 * @author 幸福无期
 */
class OrderModel extends Model{   
   /**
    * 审核订单 
    * @param  array $data 审核结果 
    * @return array 返回结果
    */
   public function orderCheck($data){
       $id = intval($data['id']);
       if(!$id){
           return array(
               'status' => 0,
               'msg'    => '请选择需要审核的订单id'
           );
       }
       $check_data = array( 
           'status'   => intval($data['status']),
           'content'  => $data['content']          
       );
       if(!in_array($check_data['status'] , array(-1,0,1,2))){
           return array(
               'status' => 0,
               'msg'    => '请选择正确的审核状态'
           );
       }
       $r = M('Order')->where(array('id'=>$id))->save($check_data);
       if($r){
           M('Order_data')->where(array('order_id'=>$id))->save(array('status' => intval($data['status'])));
           return array(
               'status' => 1,
               'msg'    => '审核成功'
           );
       }else{
           return array(
               'status' => 0,
               'msg'    => '审核失败'
           );           
       }
   }   
}