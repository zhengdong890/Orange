<?php
namespace Home\Model;
use Think\Model;
/**
 * 退款 换货 退货退款
 * type 1 退款 2换货 3退货退款
 * status 申请单状态 0申请中 -1不同意 -2已撤销 1同意 2已完成
 * order_data表 service_status 售后状态
 * 售后状态 
 * -11 商家不同意退款     11 申请退款     12 商家同意申请退款  13 已退款  
 * -21 商家不同意换货     21 申请换货     22 商家同意申请换货  23 商家已重新发货  
 * -31 商家不同意退款退货 31 申请退货退款 32 商家同意退款退货  33已退款 
 * @author 幸福无期
 */
class RefundGoodsModel extends Model{ 
    protected $tableName = 'refund_goods'; 

   /**
    * 申请退款
    * @return array 返回结果
    */
    public function refundCase($data_){
        $data = array(
            'order_id'       => $data_['order_id'],
            'order_data_id'  => $data_['order_data_id'],
            'member_id'      => $data_['member_id'],
            'seller_id'      => $data_['seller_id'],
            'goods_id'       => $data_['goods_id'],
            'case'           => $data_['case'],//退款价格
            'beacuse'        => intval($data_['beacuse']), //申请原因
            'type'           => 1, //申请类型
            'refund_content' => $data_['content'],//说明
            'thumb'          => $data_['thumb'],
            'create_time'    => time(),
            'refund_sn'      => setnum(10),
            'order_sn'       => $data_['order_sn'],
            'order_price'    => $data_['order_price'],
            'buy_name'       => $data_['buy_name']
        );   
        $id = M('Refund_goods')->add($data);
        if($id === false){
            return array(
                'status' => 0,
                'msg'    => '申请失败'
            );
        }
        //设置该商品订单 售后状态为申请退款
        M('Mall_order_data')->where(array('id'=>$data['order_data_id']))->save(array('service_status' => '11'));
        return array(
            'status' => 1,
            'msg'    => '申请成功',
            'time'   => $data['create_time']
        );                  
    } 

   /**
    * 换货
    * @return array 返回结果
    */
    public function refundGoods_($data_){
        $data = array(
            'order_id'       => $data_['order_id'],
            'order_data_id'  => $data_['order_data_id'],
            'member_id'      => $data_['member_id'],
            'seller_id'      => $data_['seller_id'],
            'goods_id'       => $data_['goods_id'],
            'beacuse'        => intval($data_['beacuse']), //申请原因
            'type'           => 2, //申请类型
            'refund_content' => $data_['content'],//说明
            'thumb'          => $data_['thumb'],
            'create_time'    => time(),
            'refund_sn'      => setnum(10),
            'order_sn'       => $data_['order_sn'],
            'order_price'    => $data_['order_price'],
            'buy_name'       => $data_['buy_name']
        ); 
        $id = M('Refund_goods')->add($data);
        if($id === false){
            return array(
                'status' => 0,
                'msg'    => '申请失败'
            );
        }
        //设置该商品订单 售后状态为申请换货
        M('Mall_order_data')->where(array('id'=>$data['order_data_id']))->save(array('service_status' => '31'));
        return array(
            'status' => 1,
            'msg'    => '申请成功',
            'time'   => $data['create_time']

        );    
    }

   /**
    * 退款退货
    * @return array 返回结果
    */
    public function refundCaseGoods($data_){
        $data = array(
            'order_id'       => $data_['order_id'],
            'order_data_id'  => $data_['order_data_id'],
            'member_id'      => $data_['member_id'],
            'seller_id'      => $data_['seller_id'],
            'goods_id'       => $data_['goods_id'],
            'case'           => $data_['case'],//退款价格
            'beacuse'        => intval($data_['beacuse']), //申请原因
            'type'           => 3, //申请类型
            'refund_content' => $data_['content'],//说明
            'thumb'          => $data_['thumb'],
            'create_time'    => time(),
            'refund_sn'      => setnum(10),
            'order_sn'       => $data_['order_sn'],
            'order_price'    => $data_['order_price'],
            'buy_name'       => $data_['buy_name']
        );  
        $id = M('Refund_goods')->add($data);
        if($id === false){           
            M('Mall_order_data')
            ->where(array('id'=>$data['order_id']))
            ->save(array('service_status' => '21'));
            return array(
                'status' => 0,
                'msg'    => '申请失败'
            );
        }
        //设置该商品订单 售后状态为申请退款退货
        M('Mall_order_data')->where(array('id'=>$data['order_data_id']))->save(array('service_status' => '31'));
        return array(
            'status' => 1,
            'msg'    => '申请成功',
            'time'   => $data['create_time']
        );        
    }

   /**
    * 检测 提交的 退换货款 数据 合法性
    * @return array 返回结果
    */
    public function checkRefund($data){
        $data['type'] = intval($data['type']);
        /*验证数据*/
        $model = D("Refund_goods");
        $rules = array(
            array('type',array(1,2,3),'类型设置错误',self::MUST_VALIDATE,'in'),
            array('because','/^[1-9]\d*$/','请选择申请原因',self::MUST_VALIDATE),
        );
        $temp  = array();
        if($data['type'] == 1 || $data['type'] == 3){
            $temp = array(
               // array('case','require','请输入正确的价格',self::MUST_VALIDATE),
                //array('case','/^[0-9]+(.[0-9]{1,2})?$/','请输入正确的价格'),
            ); 
        }
        $rules = array_merge($rules , $temp);
        if($model->validate($rules)->create($data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $model->getError()
            );
            return $result;
        }
             
        return array(
          'status' => 1
        );
    }


/******************************订单售后操作 所需条件**************************************/
   /**
    * 根据商品订单获取 商品订单的售后类型
    * @param  array order_data 商品订单数据
    * @return array 返回结果
    */
    public function getRefundTypeByOrder($order_data){
        $type = array();
        /*退款*/
        if($this->isFulfilRefund($order_data)){
            $type[] = 1;
        }               
        /*换货*/
        if($this->isFulfilRefundGoods($order_data)){
            $type[] = 2;
        }                
        /*退货退款*/
        if($this->isFulfilRefundGoodsCase($order_data)){
            $type[] = 3;
        }   
        return $type;     
    }

   /**
    * 检测 提交的 售后是否满足条件
    * @param  int type 售后类型 
    * @param  array order_data商品订单数据     
    * @return array 返回结果
    */
    public function isFulfilCondition($type , $order_data){
        /*退款*/
        if(($type == 1) && !$this->isFulfilRefund($order_data)){
            return array('status'=>0,'msg'=>'该订单状态不允许退款');
        }               
        /*换货*/
        if($type == 2 && !$this->isFulfilRefundGoods($order_data)){
            return array('status'=>0,'msg'=>'该订单状态不允许退货退款');
        }                
        /*退货退款*/
        if($type == 3 && !$this->isFulfilRefundGoodsCase($order_data)){
            return array('status'=>0,'msg'=>'该订单状态不允许换货');
        }
        return array('status'=>1);
    }

   /**
    * 订单是否能够退款 未付款 或者 未审核 发货状态下不允许退款
    * @param  array data 商品订单数据     
    * @return array 返回结果
    */
    private function isFulfilRefund($data){
        if($data['pay_status'] != 1 || $data['status'] != 1 || $data['send_status'] != 0){
            return false;
        } 
        return true;
    }

   /**
    * 订单是否能够换货 未付款 或者 未发货 状态下不允许换货
    * @param  array order_data 商品订单数据     
    * @return array 返回结果
    */
    private function isFulfilRefundGoods($data){
        if($data['pay_status'] == 0 || $data['send_status'] == 0){
            return false;
        }                
        return true;
    }

   /**
    * 订单是否能够退款退货 未付款 或者 未发货 状态下不允许退款退货
    * @param  array order_data 商品订单数据     
    * @return array 返回结果
    */
    private function isFulfilRefundGoodsCase($data){
        if($data['pay_status'] == 0 || $data['send_status'] == 0){
            return false;
        } 
        return true;
    }

/***************************************商家审核*********************************************/
   //status -1卖家不同意 -2已撤销 0申请中 1卖家同意 2已完成 

   /**
    * 更改商品订单售后状态
    * @param  int id             商品订单id
    * @param  int service_status 售后状态 
    */     
    public function changeOrderServiceStatus($id , $service_status){
        M('Mall_order_data')->where(array('id'=>$id))->save(array('service_status' => $service_status)); 
    }

   /**
    * 退款申请审核
    * @param  int status 售后申请单状态
    * @param  int refund 售后申请单数据
    * @return array 返回结果
    */    
    public function checkRefundCase($status , $refund){
        //设置该商品 售后申请单的状态
        $r = M('Refund_goods')->where(array('id'=>$refund['id']))->save(array('status'=>$status));
        if($r === false){
            return array(
                'status' => 0,
                'msg'    => '审核失败'
            );
        }  
        /*设置该商品订单 售后状态*/
        $service_status =($status < 0 ? '-' : '') . "1" . abs($status);
        $this->changeOrderServiceStatus($refund['order_data_id'] , $service_status);       
        /*生成退款单号*/
        if($status == 1){
           $r = D('RefundCase')->refundCaseAdd($refund);
        }       
        return array(
            'status' => 1,
            'msg'    => '审核成功'
        );     
    }

   /**
    * 换货申请审核
    * @param  int id     售后申请单id
    * @param  int status 售后申请单状态
    * @return array 返回结果
    */    
    public function checkRefundGoods($status , $refund){ 
        //设置该商品 售后申请单的状态
        $r = M('Refund_goods')->where(array('id'=>$refund['id']))->save(array('status'=>$status));
        if($r === false){
            return array(
                'status' => 0,
                'msg'    => '审核失败'
            );
        }
        //设置该商品订单 售后状态
        $service_status =($status < 0 ? '-' : '') . "2" . abs($status);
        $this->changeOrderServiceStatus($refund['order_data_id'] , $service_status);  
        return array(
            'status' => 1,
            'msg'    => '审核成功'
        );          
    }

   /**
    * 退款退货申请审核
    * @param  int id     售后申请单id
    * @param  int status 售后申请单状态
    * @return array 返回结果
    */    
    public function checkRefundCaseGoods($status , $refund){
        //设置该商品 售后申请单的状态
        $r = M('Refund_goods')->where(array('id'=>$refund['id']))->save(array('status'=>$status));
        if($r === false){
            return array(
                'status' => 0,
                'msg'    => '审核失败'
            );
        }   
        //设置该商品订单 售后状态
        $service_status =($status < 0 ? '-' : '') . "3" . abs($status);
        $this->changeOrderServiceStatus($refund['order_data_id'] , $service_status);  
        return array(
            'status' => 1,
            'msg'    => '审核成功'
        );       
    }

   /**
    * 超过2天自动 同意申请售后
    * @param  int status 售后申请单详情
    * @return array 返回结果
    */     
    public function autoCheck($refund_goods){
        $time = time();       
        if(($time - $refund_goods['create_time']) < 2 * 24 * 3600 ){
            return array(
                'status' => 0,
                'msg'    => '暂未过期'
            );        
        }  
        /*退款申请审核*/   
        if($refund_goods['type'] == 1){
            $result = $this->checkRefundCase($status , $refund);
        }
        /*换货申请审核*/
        if($refund_goods['type'] == 2){
            $result = $this->checkRefundGoods($status , $refund);
        }
        /*退款退货申请审核*/   
        if($refund_goods['type'] == 3){
            $result = $this->checkRefundCaseGoods($status , $refund);
            D('RefundCase')->refundCaseAdd($refund_goods);
        }    
    }

/***************************************售后撤销*********************************************/
//status -1卖家不同意 -2已撤销 0申请中 1卖家同意 2已完成    

   /**
    * 售后撤销
    * @param  array $refund 售后单数据
    * @return array 返回结果
    */       
    public function revoke($refund){
    	//售后状态设为 -2 已撤销
        $r = M('Refund_goods')->where(array('id'=>$refund['id']))->save(array('status'=>'-2','create_time'=>time()));
        if($r === false){
	        return array(
	            'status' => 0,
	            'msg'    => '撤销失败'
	        );         	
        }
        M('Mall_order_data')->where(array('id'=>$refund['order_data_id']))->save(array('service_status'=>'0'));
        if($refund['type'] == 1){
        	//退款单标记为 作废
            $r = D('RefundCase')->changeStatus(array('refund_id'=>$refund['id']) , '-1');
        }
        return array(
            'status' => 1,
            'msg'    => '撤销成功'
	    );  
    }
}