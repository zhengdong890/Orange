<?php
namespace Home\Model;
use Think\Model;
/**
 * 退款单号 业务
 * 退款单状态 status 0待操作 1已完成 -1已作废
 * @author 幸福无期
 */
class RefundCaseModel extends Model{ 
    protected $tableName = 'refund_case'; 

   /**
    * 添加退款单号
    * @param  array data 退款单号数据
    * @return array 返回结果
    */
    public function refundCaseAdd($refund){
        $trade_no    = M('Order')->where(array('id'=>$refund['order_id']))->getField('trade_no');
        $refund_case = array(
           'refund_id'     => $refund['id'],
           'trade_no'      => $trade_no,
           'member_id'     => $refund['member_id'],
           'seller_id'     => $refund['seller_id'],
           'goods_id'      => $refund['goods_id'],
           'order_data_id' => $refund['order_data_id'],
           'case'          => $refund['case'],
           'create_time'   => time()
        );
        $r = M('Refund_case')->add($refund_case);
        if($r === false){
            return array(
                'status' => 0
            );
        }
        return array(
            'status' => 0,
            'msg'    => 'ok'
        );                
    } 
    
   /**
    * 更改退款单状态
    * @param  array data 退款单号数据
    * @return array 返回结果
    */    
    public function changeStatus($condition_ , $status){
        $condition = array(
            'id'        => '',
            'refund_id' => ''
        );
        $condition = array_intersect_key($condition_ , $condition);
        $r = M('Refund_case')->where($condition)->save(array('status'=>$status));
        return $r;
    }
}