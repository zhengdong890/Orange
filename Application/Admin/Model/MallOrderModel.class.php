<?php
namespace Admin\Model;
use Think\Model;
/**
 * 商城商品订单模块业务逻辑
 * @author 幸福无期
 */
class MallOrderModel extends Model{   
    protected $tableName='Mall_order';
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
           'status' => intval($data['status']) == 1 ? 1 : 0,
           'content'  => $data['content']          
       );
       if(!in_array($check_data['status'] , array(-1,0,1,2))){
           return array(
               'status' => 0,
               'msg'    => '请选择正确的审核状态'
           );
       }
       $r = M('Mall_order')->where(array('id'=>$id))->save($check_data);
       if($r){
           M('Mall_order_data')->where(array('order_id'=>$id))->save(array('status' => intval($data['status'])));
           return array(
               'status' => 1,
               'msg'    => '审核成功'
           );
       }else{
           return array(
               'status' => 1,
               'msg'    => '审核失败'
           );           
       }
    } 

   /**
    * 获取订单列表 分页 
    * @param  array $data 审核结果 
    * @return array 返回结果
    */
    public function orderDataPageList($condition = array() , $limit = array(0 , 10) , $field = '*'){
        $list = M('Mall_order')
            ->order('id desc')
            ->limit($limit[0] , $limit[1])
            ->where($condition)
            ->select();
        return array(
        	'data'  => $list,
	        'total' => M('Mall_order')->where($condition)->count(),
	    );
    } 

   /**
    * 根据订单id获取订单详情 
    * @param  array $data 审核结果 
    * @return array 返回结果
    */
    public function getOrderDataById($id){
        $order = M('Mall_order')
	        ->where(array('id'=>$id))
	        ->find();
	    if(empty($order)){
            return array();
	    }	  
	    $order_msg  = C('ORDER_MSG');      
        $order_data = M('Mall_order_data')
	        ->where(array('order_id'=>$id))
	        ->select();
	    foreach($order_data as &$v){
            $v['attr'] = unserialize($v['attr']);
            /*售后状态*/
            if($v['service_status'] != 0){
                $service_type = floor(abs($v['service_status'])/10);
                $v['service_type']       = $order_msg['service_type'][$service_type]; 
                $v['service_status_msg'] = $order_msg['service_status'][$v['service_status']]; 
            }
	    }
	    $order['status_msg']      =  $order_msg['status'][$order['status']];
	    $order['pay_msg']         =  $order_msg['pay_status'][$order['pay_status']];
	    $order['send_status_msg'] =  $order_msg['send_status'][$order['send_status']];
	    $order['pay_model_msg']   =  $order_msg['pay_model'][$order['pay_model']];
        return array(
            'order'      => $order,
            'order_data' => $order_data
        );   
    } 

    /*
	 * 设置订单为已付款 
	 * */	    
    public function setOrderPayStatus($id , $pay_status){
    	$time = time();
    	$r = M('Mall_order')
    	    ->where(array('id'=>$id))
    	    ->save(array('status'=>1,'pay_status'=>$pay_status,'pay_time'=>$time));
    	if($r === false){
	        return array(
	            'status' => 1,
	            'msg'    => '操作失败'
	        );    		
    	}    
    	M('Mall_order_data')
    	->where(array('order_id'=>$id))
    	->save(array('status'=>1,'pay_status'=>$pay_status));
        return array(
            'status' => 1,
            'msg'    => '操作成功'
        );
    }            
}