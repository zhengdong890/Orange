<?php
namespace Home\Model;
use Think\Model;
/**
 * 共享商品订单模块业务逻辑
 * @author 幸福无期
 */
class OrderModel extends Model{   
   /**
    * 增加订单(购物车) 
    * @param  array $order      订单数据 
    * @param  array $order_data 订单详情数据   
    * @return array 返回结果
    */
   public function orderAdd($order , $order_data){
      /*订单入库*/
      $fields = array(
        '`goods_id`',
        '`rent_time`',
        '`rent_number`',
        '`rent_dw`',
        '`member_id`',
        '`goods_price`',
        '`goods_name`',
        '`total_price`',
        '`order_id`',
        '`goods_thumb`',
        '`seller_id`',
        '`create_time`'
      );
      /*按商家插入*/
      $total_price = 0;
      $child_id = array();
      foreach($order as $k => $v){
          $order_id = M('Order')->add($v);
          $child_id[$v['seller_id']] = $order_id;
          $values   = array();
          $total_price += $v['total_price'];
          if($order_id){
              foreach($order_data[$k] as $k1 => $v1){
                  $arr = array(
                      'goods_id'    => $v1['goods_id'],
                      'rent_time'   => $v1['rent_time'],
                      'rent_number' => $v1['rent_number'],
                      'rent_dw'     => $v1['rent_dw'],
                      'member_id'   => $v['member_id'],
                      'goods_price' => $v1['goods_price'],
                      'goods_name'  => $v1['goods_name'],
                      'total_price' => $v1['total_price'],
                      'order_id'    => $order_id,
                      'goods_thumb' => $v1['goods_thumb'],
                      'seller_id'   => $v1['seller_id'],
                      'create_time' => $v1['create_time']
                  );
                  $values[]      = "('" . implode("','",$arr) . "')";
              }
              $sql = "INSERT INTO `tp_order_data` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
              $r   = M()->execute($sql);             
          }             
      }
      $order_sn = $order[$k]['order_sn'];
      $order_type = 1;
      /*如果不止一家卖家*/
      $pay_id = '';
      if(count($order) >1 ){
          $data = array(
             'order_ids'   => implode(',' , $child_id),
             'total_price' => $total_price
          );
          $order_sn   = $data['order_sn'];
          $pay_id     = M('Order_pay')->add($data);
          $order_type = 2;
      }
      return array(
         'status' => 1,
         'msg'    => '订单提交成功',
         'data'   => array(
              'total_price' => $total_price,
              'order_type'  => $order_type,
              'pay_id'      => $pay_id,
              'order_id'    => $child_id
          )
      );
   }
   
   /*更改订单付款状态*/
   public function changePayState($pay_type , $id){
      if($pay_type == 1){
          M('Order')->where(array('id'=>$id))->save(array('pay_status'=>1));
          M('Order_data')->where(array('order_id'=>$id))->save(array('pay_status'=>1));
      }else
      if($pay_type == 2){
          $order_ids = M('Order_pay')->where(array('id'=>$id))->getField('order_ids');
          M('Order')->where(array('id'=>array('in' , $order_ids)))->save(array('pay_status'=>1));
         
      }
   }

   /**
    * 取消订单 
    * @return array 返回结果
    */
    public function orderDelete($member_id , $id){
    	$result = array('status'=>1,'msg'=>'取消订单成功');
        $data   = M('Order')->where(array('member_id'=>$member_id,'id'=>$id))->find();
        if(!$data){
        	return array('status'=>0,'msg'=>'订单不存在');
        }
        if($data['pay_status'] != 0){
        	return array('status'=>0,'msg'=>'该订单状态不允许取消');
        }
        $r = M('Order')->where(array('id'=>$id))->delete();
        if($r === false){          
        	$result = array('status'=>0,'msg'=>'取消订单失败');
        }else{
        	M('Order_data')->where(array('order_id'=>$id))->delete();
        }
        return $result;
   }

   /**
    * 确认收货  
    * @return array 返回结果
    */
    public function recipient($member_id , $id){
    	  $result = array('status'=>1,'msg'=>'收货成功');
        $data   = M('Order')->where(array('member_id'=>$member_id,'id'=>$id))->find();
        if(!$data){
        	return array('status'=>0,'msg'=>'订单不存在');
        }
        if($data['send_status'] != 1){
        	return array('status'=>0,'msg'=>'该订单状态不允许收货');
        }
        $r = M('Order')->where(array('id'=>$id))->save(array('send_status'=>2));
        if($r === false){
        	$result = array('status'=>0,'msg'=>'收货失败');
        }
        $goods_ids = M('Order_data')->where(array('order_id'=>$id))->Field('goods_id')->select();
        $goods_ids = array_column($goods_ids, 'goods_id');
        $goods_ids = implode(',' , $goods_ids);
        M('Goods')->where(array('goods_id'=>array('in' , $goods_ids)))->setInc('sale_num',1);
        M('Order_data')->where(array('order_id'=>$id))->save(array('send_status'=>2));
        return $result;
   }

   /**
    * 卖家发货
    * @param  int   $id   订单id
    * @param  int   $seller_id   卖家id
    * @return array 返回结果
    */   
   public function sendGoods($id , $seller_id){
       $seller_id = intval($seller_id);
       if(!$seller_id){
           return array(
               'status' => 0,
               'msg'    => '必须输入卖家id'
           );
       }
       if(!is_array($id)){
           $id   = intval($id);
           if(!$id){
               return array(
                   'status' => 0,
                   'msg'    => '请传入非空数据'
               );               
           }
           $data = M('Order')->where(array('id'=>$id,'seller_id'=>$seller_id))->find();
           if($data['pay_status'] != 1 || $data['send_status'] != 0 || $data['status'] != 1){
               return array(
                   'status' => 0,
                   'msg'    => '该订单状态不允许发货'
               );
           }
           $r = M('Order')->where(array('id'=>$id,'seller_id'=>$seller_id))->save(array('send_status'=>1));
           if($r === false){
               return array('status'=>0,'msg'=>'发货失败');
           }else{
               M('Order_data')->where(array('order_id'=>$id,'seller_id'=>$seller_id))->save(array('send_status'=>1));
               return array('status'=>1,'msg'=>'发货成功');
           }           
       }else{
           if(count($id) <= 0){
               return array('status' => 0,'msg' => '请传入非空数据');               
           }
           $id   = implode(',' , $id);
           $data = M('Order')->where(array('id'=>array('in' , $id),'seller_id'=>$seller_id))->select();
           if(count($data) <= 0){
               return array(
                   'status' => 0,
                   'msg'    => '该订单不存在'
               );
           }
           $sql_arr = array(
               'send_status' => " SET send_status = CASE id"
           );
           $sql_arr_ = array(
               'send_status' => " SET send_status = CASE order_id"
           );
           $ids  = array();
           foreach($data as $v){
               if($v['pay_status'] != 1 || $v['send_status'] != 0 || $v['status'] != 1){
                   return array(
                       'status' => 0,
                       'msg'    => '该订单状态不允许发货'
                   );
               }
               $ids[] = $v['id'];
               $sql_arr['send_status'] .= " WHEN {$v['id']} THEN 1";
               $sql_arr_['send_status'] .= " WHEN {$v['id']} THEN 1";
           }
           $ids = implode(',' , $ids);
           $sql_arr['send_status'] .= ' END';
           $sql = "UPDATE tp_order".$sql_arr['send_status']." where id IN ($ids) and seller_id=$seller_id";
           $r   = M()->execute($sql);
           if($r === false){
               return array(
                   'status' => 0,
                   'msg'   => '发货失败'
               );
           }
           $sql_arr_['send_status'] .= ' END';
           $sql = "UPDATE tp_order_data". $sql_arr_['send_status']." where order_id IN ($ids) and seller_id=$seller_id";
           $r   = M()->execute($sql);
           return array('status'=>1,'msg'=>'发货成功');
       }       
   }
}