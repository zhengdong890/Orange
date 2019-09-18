<?php
/*订单状态值
status : 订单状态 0未审核 1已审核 2已完成 -1已取消
pay_model : 支付模式 1支付宝 2微信 3银联 4线下公对公转账
send_status : 发货状态 0未发货 1已经发货
pay_status  : 付款状态 0未付款 1已经付款
comment_status  : 是否评价 0未评价 1已经评价
service_status : 
 订单当前 售后状态
 * -11 商家不同意退款     11 申请退款     12 商家同意申请退款 13 已退款  
 * -21 商家不同意换货     21 申请换货     22 商家同意申请换货 23 商家已重新发货  
 * -31 商家不同意退款退货 31 申请退货退款 32 商家同意退款退货 33 已退款    
*/
namespace Home\Model;
use Think\Model;
/**
 * 商城商品订单模块业务逻辑
 * @author 幸福无期
 */
class Mall_orderModel extends Model{   
   /**
    * 增加订单(购物车) 
    * @param  array $order      订单数据 
    * @param  array $order_data 订单详情数据   
    * @return array 返回结果
    */
    public function orderAdd($order , $order_data , $member_id){
        /*按商家插入*/
        $model = M();
   	    $model->startTrans();//事务开始
        $pay_price = 0;//线上需要支付的金额
        $child_id  = array();
        $flag      = true;
        foreach($order as $k => $v){
            $order_id = $model->table(C('DB_PREFIX').'mall_order')->add($v);
            $child_id[$v['seller_id']] = $order_id;
            $values   = array();
            $total_price += $v['total_price'];
            if($order_id){
                $r = $this->orderDataAdd($order_data[$k] , $order_id);//插入订单详情
                if($r['status'] == 0){
              	    $model->rollback();//事务回滚
          	        return $r;
          	    } 
            }else{
          	    $model->rollback();//事务回滚
          	    return array('status' => 0,'msg' => '订单提交失败');
            }            
        }
        //提交事务
        $model->commit();	
        $order_type = 1;//1表示只有一家商家 2为多家商家
        /*如果不止一家卖家*/
        $pay_id = '';
        if(count($order) >1 ){
            $data = array(
                'order_ids'   => implode(',' , $child_id),
                'total_price' => $total_price,
                'member_id'   => $member_id
            );
            $pay_id     = M('Mall_order_pay')->add($data);
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

   /**
    * 增加订单详情 
    * @param  array $order_data 订单详情数据   
    * @return array 返回结果
    */   
    private function orderDataAdd($order_data , $order_id){
    	/*订单详情*/
        $fields = array(
	        '`goods_id`',
	        '`number`',
	        '`member_id`',
	        '`goods_price`',
	        '`goods_name`',
	        '`total_price`',
	        '`order_id`',
	        '`goods_thumb`',	       
	        '`seller_id`',
	        '`create_time`',
	        '`sku_id`',
	        '`sku_code`',
	        '`attr`',
	        '`name`',
	        '`templet_id`',
	        '`status`'
        );
	    foreach($order_data as $k => $v){
	        $arr = array(
	            'goods_id'    => $v['goods_id'],//商品id
	            'number'      => $v['number'],//购买数量
	            'member_id'   => $v['member_id'],//购买者id
	            'goods_price' => $v['goods_price'],//商品价格
	            'goods_name'  => $v['goods_name'],//商品名字
	            'total_price' => $v['total_price'],//订单详情总价
	            'order_id'    => $order_id,//订单id
	            'goods_thumb' => $v['goods_thumb'],//商品图片
	            'seller_id'   => $v['seller_id'],//卖家id
	            'create_time' => $v['create_time'],//下单时间
	            'sku_id'      => $v['sku_id'],//sku_id
	            'sku_code'    => $v['sku_code'],//sku 订货码
	            'attr'        => addslashes($v['attr']),//sku属性集
	            'name'        => $v['name'],
	            'templet_id'  => $v['templete_id'],
	            'status'      => 1
	        );
	        $values[]  = "('" . implode("','",$arr) . "')";
            /*库存检测*/
            /*$r = D('MallGoodsSku')->checkNumber($v['sku_id'] , 1);            
            if($r === false || $r == 0){
                return $r;
            }*/
	    }
	    $sql = "INSERT INTO `" . C('DB_PREFIX') . "mall_order_data` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
        $r   = M()->execute($sql);
        if($r === false || $r == 0){
        	return array('status' => 0 , 'msg' => '订单提交失败');
        }
	    return array('status' => 1 , 'msg' => '订单提交成功');
    }

   /**
    * 更改订单付款状态
    * @param  int $pay_type  订单支付是一家商家还是多家
    * @param  int $id        支付id   
    * @param  int $trade_no  支付宝流水号   
    * @param  int $pay_model 支付类型        
    * @return array 返回结果
    */     
    public function changePayState($pay_type , $id , $trade_no , $pay_model){
   	    $time = time();
        if($pay_type == 1){
            $r = M('Mall_order')
                ->where(array('id'=>$id))
                ->save(array(
                	'pay_status' => 1 ,
                	'trade_no'   => $trade_no , 
                	'pay_time'   => $time,
                	'pay_model'  => $pay_model
                ));
            if(!$r){
                return;
            }                
            $r = M('Mall_order_data')
                ->where(array('order_id'=>$id))
                ->save(array('pay_status'=>1));
            /*销售额增加*/
            $sku_ids = M('Mall_order_data')->where(array('order_id'=>$id))->field('sku_id,number')->select();
            foreach($sku_ids as $v){
                D('MallGoodsSku')->changeNumber($v['sku_id'] , $v['number']);
            } 
        }else
        if($pay_type == 2){
            $order_ids = M('Mall_order_pay')
                ->where(array('id'=>$id))
                ->getField('order_ids');

            $r = M('Mall_order')
                ->where(array('id'=>array('in' , $order_ids)))
                ->save(array(
                	'pay_status'  => 1 ,
                	'trade_no'    => $trade_no ,
                	'pay_time'    => $time,
                	'pay_model'   => $pay_model
                )); 
            if(!$r){
                return;
            }    
            /*销售额增加*/
            $sku_ids = M('Mall_order_data')
                ->where(array('order_id'=>array('in' , $order_ids)))
                ->field('sku_id,number')
                ->select();
            foreach($sku_ids as $v){
                D('MallGoodsSku')->changeNumber($v['sku_id'] , $v['number']);
            }          
        }
    }
   
   /**
    * 取消订单 
    * @return array 返回结果
    */
    public function orderDelete($member_id , $id){
    	$result = array('status'=>1,'msg'=>'取消订单成功');
        $data   = M('Mall_order')->where(array('member_id'=>$member_id,'id'=>$id))->find();
        if(!$data){
        	return array('status'=>0,'msg'=>'订单不存在');
        }
        if($data['pay_status'] != 0){
        	return array('status'=>0,'msg'=>'该订单状态不允许取消');
        }
        $r = M('Mall_order')->where(array('id'=>$id))->save(array('status'=>-1));
        if($r === false){          
        	$result = array('status'=>0,'msg'=>'取消订单失败');
        }else{
        	M('Mall_order_data')->where(array('order_id'=>$id))->save(array('status'=>-1));
        }
        return $result;
   }

   /**
    * 确认收货  
    * @return array 返回结果
    */
    public function recipient($member_id , $id){
        $result = array('status'=>1,'msg'=>'收货成功');
        $data   = M('Mall_order')->where(array('member_id'=>$member_id,'id'=>$id))->find();
        if(!$data){
          return array('status'=>0,'msg'=>'订单不存在');
        }
        if($data['send_status'] != 1){
          return array('status'=>0,'msg'=>'该订单状态不允许收货');
        }
        $r = M('Mall_order')->where(array('id'=>$id))->save(array('send_status'=>2,'status'=>2));
        if($r === false){
          $result = array('status'=>0,'msg'=>'收货失败');
        }
        M('Mall_order_data')->where(array('order_id'=>$id))->save(array('send_status'=>2,'status'=>2));
        return $result;
   }  
   
   /**
    * 卖家发货
    * @param  array $data       发货数据
    * @param  int   $seller_id  卖家id
    * @return array 返回结果
    */
    public function sendGoods($send_data , $seller_id){
        $seller_id = intval($seller_id);
        if(!$seller_id){
            return array(
                'status' => 0,
                'msg'    => '必须输入卖家id'
            );
        }      
        if(count($send_data) <= 0){
            return array(
           	    'status' => 0,
           	    'msg' => '请传入非空数据'
            );
        }
        if(!$this->checkAirWayCode($send_data)){
            return array(
            	'status' => 0,
            	'msg'    => '请输入正确的运单号'
            );
        }
        $id   = array_keys($send_data);
        $ids  = implode(',' , $id);
        $data = M('Mall_order')
		      ->where(array('id'=>array('in' , $ids),'seller_id'=>$seller_id))
		      ->select();
        if(count($data) <= 0 || count($data) != count($send_data)){
            return array(
               'status' => 0,
               'msg'    => '订单不存在'
            );
        }
        $sql_arr = array(
           'send_status'   => " SET send_status = CASE id",
           'send_time'     => " send_time = CASE id",
           'air_way_code'  => " air_way_code = CASE id",
           'company_code'  =>" company_code = CASE id"
        );
        $sql_arr_ = array(
           'send_status' => " SET send_status = CASE order_id"
        );
        $ids   = array();
        $time  = time();
        $model = M();
   	    $model->startTrans();//事务开始
        foreach($data as $v){
            if($v['pay_status'] != 1 || $v['send_status'] != 0 || $v['status'] != 1){
                return array(
                   'status' => 0,
                   'msg'    => '该订单状态不允许发货'
                );
            }
            $ids[] = $v['id'];
            $sql_arr['send_status'] .= " WHEN {$v['id']} THEN 1";
            $sql_arr['send_time'] .= " WHEN {$v['id']} THEN {$time}";
            $sql_arr['air_way_code'] .= " WHEN {$v['id']} THEN {$send_data[$v['id']]['air_way_code']}";
            $sql_arr['company_code'] .= " WHEN {$v['id']} THEN '{$send_data[$v['id']]['company_code']}'";

            $sql_arr_['send_status'] .= " WHEN {$v['id']} THEN 1";
        }
        $ids = implode(',' , $ids);
        $sql_arr['send_status'] .= ' END,';
        $sql_arr['air_way_code'] .= ' END,';
        $sql_arr['company_code'] .= ' END';
        $sql_arr['send_time'] .= ' END,';

        $sql = "UPDATE tp_mall_order".$sql_arr['send_status'].$sql_arr['send_time'].$sql_arr['air_way_code'].$sql_arr['company_code']." where id IN ($ids) and seller_id=$seller_id";
        $r   = M()->execute($sql);
        if($r === false){
        	$model->rollback();//事务回滚
            return array(
               'status' => 0,
               'msg'    => '发货失败'
            );
        }
        $sql_arr_['send_status'] .= ' END';
        $sql = "UPDATE tp_mall_order_data". $sql_arr_['send_status']." where order_id IN ($ids) and seller_id=$seller_id";
        $r   = M()->execute($sql);  
        if($r === false){
        	$model->rollback();//事务回滚
            return array(
               'status' => 0,
               'msg'    => '发货失败'
            );
        }  
        $model->commit();//提交事务	   
        return array('status'=>1,'msg'=>'发货成功');
   } 

   /**
    * 检测运单号
    */
    private function checkAirWayCode($data){
       foreach($data as $v){
           if(!$v['air_way_code']){
               return false;
           }
       }
       return true;
    }

   /**
    * 获取近三个月的订单
    * @param  int   $seller_id  卖家id
    * @param  array $limit      分页数据
    * @param  array $field      所需字段
    * @return array 返回结果
    */
    public function getThreeMonthOrder($seller_id , $limit , $field){
        //获取需要的字段
        $field     = array(
            'a.id','a.address_id','a.order_sn','a.address','a.create_time','a.send_status','a.create_time',
            'a.pay_price','a.shipping_price','a.total_price','a.shop_coupon_price','a.member_id','a.name',
            'b.goods_name','b.goods_thumb','b.goods_price','b.order_id','b.total_price',
            'b.number','b.status','b.pay_status','b.comment_status','b.send_status'
        );
        $field     = implode(',' , $field);
        //近三个月的订单 条件
        $time = time() - 3 * 3600 * 24 * 30;            
        $condition = array(
            'a.seller_id'   => $seller_id,
            'a.status'      => 1,
            'a.create_time' => array('egt' , $time)       
        );
        /*获取数据*/
        $temp = M('Mall_order as a')
              ->join('tp_mall_order_data as b on a.id=b.order_id')
              ->where($condition)
              ->field($field)
              ->order('a.id desc')
              ->limit($limit[0] , $limit[1])
              ->select();
        /*按订单 - 订单详情进行组装*/
        $order = array();
        foreach($temp as $v){
            if(!isset($order[$v['order_id']])){
                $order[$v['order_id']] = array(
                    'order_id'          => $v['order_id'],
                    'create_time'       =>date('Y-m-d H:i:s', $v['create_time']),
                    'order_sn'          => $v['order_sn'],
                    'address_id'        => $v['address_id'],
                    'address'           => $v['address'],
                    'total_price'       => $v['total_price'],
                    'shipping_price'    => $v['shipping_price'],
                    'shop_coupon_price' => $v['shop_coupon_price'],
                    'pay_price'         => $v['pay_price'],
                    'send_status'       => $v['send_status']
                );
            }
            $order[$v['order_id']]['data'][] = $v;     
        }
        $result = array(
            'data'      => $order,
            'totalRows' => M('Mall_order as a')->where($condition)->count()
        );
        return $result;
    }

   /**
    * 获取近三个月之前的订单
    * @param  int   $seller_id  卖家id
    * @param  array $limit      分页数据
    * @param  array $field      所需字段
    * @return array 返回结果
    */
    public function getThreeMonthBeforeOrder($seller_id , $limit , $field){
        //获取需要的字段
        $field     = array(
            'a.id','a.address_id','a.order_sn','a.address','a.create_time','a.send_status',
            'a.pay_price','a.shipping_price','a.total_price','a.shop_coupon_price','a.member_id','a.name',
            'b.goods_name','b.goods_thumb','b.goods_price','b.order_id','b.total_price',
            'b.number','b.status','b.pay_status','b.comment_status','b.send_status'
        );
        $field     = implode(',' , $field);
        //近三个月的订单 条件
        $time = time() - 3 * 3600 * 24 * 30;            
        $condition = array(
            'a.seller_id'   => $seller_id,
            'a.status'      => 1,
            'a.create_time' => array('lt' , $time)       
        );
        /*获取数据*/
        $temp = M('Mall_order as a')
              ->join('tp_mall_order_data as b on a.id=b.order_id')
              ->where($condition)
              ->field($field)
              ->order('a.id desc')
              ->limit($limit[0] , $limit[1])
              ->select();
        /*按订单 - 订单详情进行组装*/
        $order = array();
        foreach($temp as $v){
            if(!isset($order[$v['order_id']])){
                $order[$v['order_id']] = array(
                    'order_id'          => $v['order_id'],
                    'order_sn'          => $v['order_sn'],
                    'address_id'        => $v['address_id'],
                    'address'           => $v['address'],
                    'total_price'       => $v['total_price'],
                    'shipping_price'    => $v['shipping_price'],
                    'shop_coupon_price' => $v['shop_coupon_price'],
                    'pay_price'         => $v['pay_price'],
                    'send_status'       => $v['send_status']
                );
            }
            $order[$v['order_id']]['data'][] = $v;     
        }
        $result = array(
            'data'      => $order,
            'totalRows' => M('Mall_order as a')->where($condition)->count()
        );
        return $result;
    }

   /**
    * 获取等待买家付款的订单
    * @param  int   $seller_id  卖家id
    * @param  array $limit      分页数据
    * @param  array $field      所需字段
    * @return array 返回结果
    */
    public function getWaitPayOrder($seller_id , $limit , $field){
        //获取需要的字段
        $field     = array(
            'a.id','a.address_id','a.order_sn','a.address','a.create_time','a.send_status',
            'a.pay_price','a.shipping_price','a.total_price','a.shop_coupon_price','a.member_id','a.name',
            'b.goods_name','b.goods_thumb','b.goods_price','b.order_id','b.total_price',
            'b.number','b.status','b.pay_status','b.comment_status','b.send_status'
        );
    	$field     = implode(',' , $field);
    	//等待买家付款的订单 条件
    	$condition = array(
    		'a.seller_id'   => $seller_id,
    		'a.status'      => 1,
    		'a.pay_status'  => 0,   		
    	);
    	/*获取数据*/
        $temp = M('Mall_order as a')
              ->join('tp_mall_order_data as b on a.id=b.order_id')
	          ->where($condition)
	          ->field($field)
	          ->order('a.id desc')
	          ->limit($limit[0] , $limit[1])
	          ->select();
	    /*按订单 - 订单详情进行组装*/
	    $order = array();
	    foreach($temp as $v){
	    	if(!isset($order[$v['order_id']])){
                $order[$v['order_id']] = array(
                    'order_id'          => $v['order_id'],
                    'order_sn'          => $v['order_sn'],
                    'create_time'          => date('Y-m-d H:i:s', $v['create_time']),
                    'address_id'        => $v['address_id'],
                    'address'           => $v['address'],
                    'total_price'       => $v['total_price'],
                    'shipping_price'    => $v['shipping_price'],
                    'shop_coupon_price' => $v['shop_coupon_price'],
                    'pay_price'         => $v['pay_price'],
                    'send_status'       => $v['send_status']
                );
	    	}
            $order[$v['order_id']]['data'][] = $v;     
	    }
        $result = array(
            'data'      => $order,
            'totalRows' => M('Mall_order as a')->where($condition)->count()
        );
	    return $result;
    }

   /**
    * 获取等待发货的订单
    * @param  int   $seller_id  卖家id
    * @param  array $limit      分页数据
    * @param  array $field      所需字段
    * @return array 返回结果
    */
    public function getWaitSendOrder($seller_id , $limit , $field){
        //获取需要的字段
        $field     = array(
            'a.id','a.address_id','a.order_sn','a.address','a.create_time','a.send_status',
            'a.pay_price','a.shipping_price','a.total_price','a.shop_coupon_price','a.member_id','a.name',
            'b.goods_name','b.goods_thumb','b.goods_price','b.order_id','b.total_price',
            'b.number','b.status','b.pay_status','b.comment_status','b.send_status'
        );
    	$field     = implode(',' , $field);
    	//等待发货的订单 条件
    	$condition = array(
    		'a.seller_id'   => $seller_id,
    		'a.status'      => 1,
    		'a.pay_status'  => 1,
    		'a.send_status' => 0
    	);
    	/*获取数据*/
        $temp = M('Mall_order as a')
              ->join('tp_mall_order_data as b on a.id=b.order_id')
	          ->where($condition)
	          ->field($field)
	          ->order('a.id desc')
	          ->limit($limit[0] , $limit[1])
	          ->select();
	    /*按订单 - 订单详情进行组装*/
	    $order = array();
	    foreach($temp as $v){
	    	if(!isset($order[$v['order_id']])){
                $order[$v['order_id']] = array(
                    'order_id'          => $v['order_id'],
                    'order_sn'          => $v['order_sn'],
                    'create_time'          => date('Y-m-d H:i:s', $v['create_time']),
                    'address_id'        => $v['address_id'],
                    'address'           => $v['address'],
                    'total_price'       => $v['total_price'],
                    'shipping_price'    => $v['shipping_price'],
                    'shop_coupon_price' => $v['shop_coupon_price'],
                    'pay_price'         => $v['pay_price'],
                    'send_status'       => $v['send_status']
                );
	    	}
            $order[$v['order_id']]['data'][] = $v;     
	    }
        $result = array(
            'data'      => $order,
            'totalRows' => M('Mall_order as a')->where($condition)->count()
        );
	    return $result;
    }

   /**
    * 获取已经发货的订单
    * @param  int   $seller_id  卖家id
    * @param  array $limit      分页数据
    * @param  array $field      所需字段
    * @return array 返回结果
    */
    public function getSendOrder($seller_id , $limit , $field){
        //获取需要的字段
        $field     = array(
            'a.id','a.address_id','a.order_sn','a.address','a.create_time','a.send_status',
            'a.pay_price','a.shipping_price','a.total_price','a.shop_coupon_price','a.member_id','a.name',
            'b.goods_name','b.goods_thumb','b.goods_price','b.order_id','b.total_price',
            'b.number','b.status','b.pay_status','b.comment_status','b.send_status'
        );
    	$field     = implode(',' , $field);
    	//已经发货的订单 条件
    	$condition = array(
    		'a.seller_id'   => $seller_id,
    		'a.pay_status'  => 1,
    		'a.status'      => 1,
    		'a.send_status' => 1
    	);
    	/*获取数据*/
        $temp = M('Mall_order as a')
              ->join('tp_mall_order_data as b on a.id=b.order_id')
	          ->where($condition)
	          ->field($field)
	          ->order('a.id desc')
	          ->limit($limit[0] , $limit[1])
	          ->select();
	    /*按订单 - 订单详情进行组装*/
	    $order = array();
	    foreach($temp as $v){
	    	if(!isset($order[$v['order_id']])){
                $order[$v['order_id']] = array(
                    'order_id'          => $v['order_id'],
                    'order_sn'          => $v['order_sn'],
                    'create_time'          => date('Y-m-d H:i:s', $v['create_time']),
                    'address_id'        => $v['address_id'],
                    'address'           => $v['address'],
                    'total_price'       => $v['total_price'],
                    'shipping_price'    => $v['shipping_price'],
                    'shop_coupon_price' => $v['shop_coupon_price'],
                    'pay_price'         => $v['pay_price'],
                    'send_status'       => $v['send_status']
                );
	    	}
            $order[$v['order_id']]['data'][] = $v;     
	    }
        $result = array(
            'data'      => $order,
            'totalRows' => M('Mall_order as a')->where($condition)->count()
        );
	    return $result;
    }

   /**
    * 获取待评价的订单
    * @param  int   $seller_id  卖家id
    * @param  array $limit      分页数据
    * @param  array $field      所需字段
    * @return array 返回结果
    */
    public function getWaitCommentOrder($seller_id , $limit , $field){
        //获取需要的字段
        $field     = array(
            'a.id','a.address_id','a.order_sn','a.address','a.create_time','a.send_status',
            'a.pay_price','a.shipping_price','a.total_price','a.shop_coupon_price','a.member_id','a.name',
            'b.goods_name','b.goods_thumb','b.goods_price','b.order_id','b.total_price',
            'b.number','b.status','b.pay_status','b.comment_status','b.send_status'
        );
    	$field     = implode(',' , $field);
    	//待评价的订单 条件
    	$condition = array(
    		'b.seller_id'      => $seller_id,
    		'b.status'         => 1,
    		'b.pay_status'     => 1,
    		'b.send_status'    => 2,
    		'b.comment_status' => 0,
    	);
    	/*获取数据*/
        $temp = M('Mall_order as a')
              ->join('tp_mall_order_data as b on a.id=b.order_id')
	          ->where($condition)
	          ->field($field)
	          ->order('a.id desc')
	          ->limit($limit[0] , $limit[1])
	          ->select();
	    /*按订单 - 订单详情进行组装*/
	    $order = array();
	    foreach($temp as $v){
	    	if(!isset($order[$v['order_id']])){
                $order[$v['order_id']] = array(
                    'order_id'          => $v['order_id'],
                    'order_sn'          => $v['order_sn'],
                    'create_time'          => date('Y-m-d H:i:s', $v['create_time']),
                    'address_id'        => $v['address_id'],
                    'address'           => $v['address'],
                    'total_price'       => $v['total_price'],
                    'shipping_price'    => $v['shipping_price'],
                    'shop_coupon_price' => $v['shop_coupon_price'],
                    'pay_price'         => $v['pay_price'],
                    'send_status'       => $v['send_status']
                );
	    	}
            $order[$v['order_id']]['data'][] = $v;     
	    }
        $result = array(
            'data'      => $order,
            'totalRows' => M('Mall_order_data as b')->where($condition)->count()
        );
	    return $result;
    }

   /**
    * 获取成功的订单
    * @param  int   $seller_id  卖家id
    * @param  array $limit      分页数据
    * @param  array $field      所需字段
    * @return array 返回结果
    */
    public function getSuccessOrder($seller_id , $limit , $field){
        //获取需要的字段
        $field     = array(
            'a.id','a.address_id','a.order_sn','a.address','a.create_time','a.send_status',
            'a.pay_price','a.shipping_price','a.total_price','a.shop_coupon_price','a.member_id','a.name',
            'b.goods_name','b.goods_thumb','b.goods_price','b.order_id','b.total_price',
            'b.number','b.status','b.pay_status','b.comment_status','b.send_status'
        );
    	$field     = implode(',' , $field);
    	//成功的订单 条件
    	$condition = array(
    		'a.seller_id'      => $seller_id,
    		'a.status'         => 1,
    		'a.pay_status'     => 1,
    		'a.send_status'    => 2,
    		'b.comment_status' => 1,
    	);
    	/*获取数据*/
        $temp = M('Mall_order as a')
              ->join('tp_mall_order_data as b on a.id=b.order_id')
	          ->where($condition)
	          ->field($field)
	          ->order('a.id desc')
	          ->limit($limit[0] , $limit[1])
	          ->select();
	    /*按订单 - 订单详情进行组装*/
	    $order = array();
	    foreach($temp as $v){
	    	if(!isset($order[$v['order_id']])){
                $order[$v['order_id']] = array(
                    'order_id'          => $v['order_id'],
                    'order_sn'          => $v['order_sn'],
                    'create_time'          => date('Y-m-d H:i:s', $v['create_time']),
                    'address_id'        => $v['address_id'],
                    'address'           => $v['address'],
                    'total_price'       => $v['total_price'],
                    'shipping_price'    => $v['shipping_price'],
                    'shop_coupon_price' => $v['shop_coupon_price'],
                    'pay_price'         => $v['pay_price'],
                    'send_status'       => $v['send_status']
                );
	    	}
            $order[$v['order_id']]['data'][] = $v;     
	    }
	    unset($condition['b.comment_status']);
	    $condition['a.comment_status'] = 1;
        $result = array(
            'data'      => $order,
            'totalRows' => M('Mall_order as a')->where($condition)->count()
        );
	    return $result;
    }

   /**
    * 获取关闭的订单
    * @param  int   $seller_id  卖家id
    * @param  array $limit      分页数据
    * @param  array $field      所需字段
    * @return array 返回结果
    */
    public function getCloseOrder($seller_id , $limit , $field){
        //获取需要的字段
        $field     = array(
            'a.id','a.address_id','a.order_sn','a.address','a.create_time','a.send_status',
            'a.pay_price','a.shipping_price','a.total_price','a.shop_coupon_price','a.member_id','a.name',
            'b.goods_name','b.goods_thumb','b.goods_price','b.order_id','b.total_price',
            'b.number','b.status','b.pay_status','b.comment_status','b.send_status'
        );
    	$field     = implode(',' , $field);
    	//关闭的订单 条件
    	$condition = array(
    		'a.seller_id'   => $seller_id,
    		'a.status'      => '-1',
    	);
    	/*获取数据*/
        $temp = M('Mall_order as a')
              ->join('tp_mall_order_data as b on a.id=b.order_id')
	          ->where($condition)
	          ->field($field)
	          ->order('a.id desc')
	          ->limit($limit[0] , $limit[1])
	          ->select();
	    /*按订单 - 订单详情进行组装*/
	    $order = array();
	    foreach($temp as $v){
	    	if(!isset($order[$v['order_id']])){
                $order[$v['order_id']] = array(
                    'order_id'          => $v['order_id'],
                    'order_sn'          => $v['order_sn'],
                    'create_time'          => date('Y-m-d H:i:s', $v['create_time']),
                    'address_id'        => $v['address_id'],
                    'address'           => $v['address'],
                    'total_price'       => $v['total_price'],
                    'shipping_price'    => $v['shipping_price'],
                    'shop_coupon_price' => $v['shop_coupon_price'],
                    'pay_price'         => $v['pay_price'],
                    'send_status'       => $v['send_status']
                );
	    	}
            $order[$v['order_id']]['data'][] = $v;     
	    }
        $result = array(
            'data'      => $order,
            'totalRows' => M('Mall_order as a')->where($condition)->count()
        );
	    return $result;
    }

   /**
    * 检测订单是否满足支付条件
    * @param  int   $member_id 买家id
    * @param  array $order_id  订单id
    * @return array 返回结果
    */
    public function orderIsCanPay($member_id , $order_id){
        $order = M('Mall_order')
            ->where(array('id'=>$order_id,'member_id'=>$member_id))
            ->find();  
        if(empty($order)){
        	return array('status' => 0 , 'msg' => '订单不存在');
        }      
        if($order['pay_status'] != 0){
        	return array('status' => 0 , 'msg' => '订单已经支付');
        }  
        if($order['pay_model'] == 4){
        	return array('status' => 0 , 'msg' => '订单为公对公转账');
        }
        $order['data'] = M('Mall_order_data')
            ->where(array('order_id' => $order_id))
            ->select();
        return array('status' => 1 , 'data' => $order);            
    }
   
   /**
    * 设置订单支付方式
    * @param  int   $member_id 买家id
    * @param  array $order_id  订单id
    * @return array 返回结果
    */
    public function setOrdePayModel($member_id , $order_id , $pay_model){
        $order = M('Mall_order')
            ->where(array('id'=>$order_id,'member_id'=>$member_id))
            ->find();  
        if(empty($order)){
        	return array('status' => 0 , 'msg' => '订单不存在');
        }      
        if($order['pay_status'] != 0){
        	return array('status' => 0 , 'msg' => '订单已经支付');
        }  
        $r = M('Mall_order')
            ->where(array('id' => $order_id))
            ->save(array('pay_model' => $pay_model));
        if($r === false){
            return array('status' => 0 , 'msg' => '操作失败');
        }
        return array('status' => 1 , 'msg' => '操作成功');            
    }
/*******************************************以前商品预留功能***************************************/

   /**
    * 增加订单(购物车) 
    * @param  array $order      订单数据 
    * @param  array $order_data 订单详情数据   
    * @return array 返回结果
    */
   public function orderOldAdd($order , $order_data){
      /*订单入库*/
      $fields = array(
        '`goods_id`',
        '`number`',
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
      $model = M();
   	  $model->startTrans();
      $pay_price = 0;//线上需要支付的金额
      $child_id  = array();
      $flag      = true;
      foreach($order as $k => $v){
          $order_id = $model->table(C('DB_PREFIX').'mall_order')->add($v);
          $child_id[$v['seller_id']] = $order_id;
          $values   = array();
          $total_price += $v['total_price'];
          if($order_id){
              foreach($order_data[$k] as $k1 => $v1){
                  $arr = array(
                      'goods_id'    => $v1['goods_id'],
                      'number'      => $v1['number'],
                      'member_id'   => $v1['member_id'],
                      'goods_price' => $v1['goods_price'],
                      'goods_name'  => $v1['goods_name'],
                      'total_price' => $v1['total_price'],
                      'order_id'    => $order_id,
                      'goods_thumb' => $v1['goods_thumb'],
                      'seller_id'   => $v1['seller_id'],
                      'create_time' => $v1['create_time']
                  );
                  $values[]      = "('" . implode("','",$arr) . "')";
                  $model_goods   = M('Mall_goods');
                  $model_goods->goods_number = array('exp','goods_number-1');
                  $model_goods->sale_num = array('exp','sale_num+1');
                  $r = $model_goods->where(array('id'=>$v1['goods_id'],'goods_number'=>array('gt',0)))->save();
                  if(!$r){
              	      $model->rollback();
          	          return array('status' => 0,'msg' => '库存不足');
                  } 
              }
              $sql = "INSERT INTO `tp_mall_order_data` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
              $r   = M()->execute($sql);
              if(!r){
              	 $model->rollback();
          	     return array('status' => 0,'msg' => '订单提交失败');
              }             
          }else{
          	  $model->rollback();
          	  return array('status' => 0,'msg' => '订单提交失败');
          }            
      }
      $model->commit();	
      $order_type = 1;//1表示只有一家商家 2为多家商家
      /*如果不止一家卖家*/
      $pay_id = '';
      if(count($order) >1 ){
          $data = array(
              'order_ids'   => implode(',' , $child_id),
              'total_price' => $total_price
          );
          $pay_id     = M('Mall_order_pay')->add($data);
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
}