<?php
/**
 * 商家购物券业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Home\Model;
use Think\Model;
class ShopCouponsModel extends Model{
    protected $tableName = 'shop_coupons';

    /**
     * 添加优惠券
     * @access public
     * @param  array $data_  优惠数据
     * @return array $result 执行结果
     */
    public function couponAdd($data_){
        /*优惠券数据*/
        $data = array(
            'seller_id'     => '',//卖家id
            'max'           => '',
            'benefit_price' => '',
            'start_time'    => '',
            'end_time'      => '', 
            'number'        => '',//发放总量
            'sy_number'     => '',//剩余数量
            'max_number'    => '',//每人限领张数
            'name'          => ''
        );
        $data_['sy_number'] = $data_['number'];
        $data = array_intersect_key($data_ , $data); //获取键的交集 
        $n    = M('Shop_coupons')->where(array('seller_id' => $data['seller_id']))->count();
        if($n >= 3){
            return array(
                'status' => 0,
                'msg'    => '不能添加超过3个优惠卷'
            );
        }
        $r = M('Shop_coupons')->add($data);
        if($r === false){
            return array(
                'status' => 0,
                'msg'    => '添加失败'
            );
        }
        return array(
            'status' => 1,
            'msg'    => '添加成功'
        );             
    }

    /**
     * 修改优惠券
     * @access public
     * @param  array $data_  优惠数据
     * @return array $result 执行结果
     */
    public function couponUpdate($data_ , $seller_id){
        /*优惠券数据*/
        $data = array(
            'max'           => '',
            'benefit_price' => '',
            'start_time'    => '',
            'end_time'      => '', 
            'number'        => '',
            'sy_number'     => '',
            'max_number'    => '',
            'name'          => ''
        );
        $id   = $data_['id'];
        $data = array_intersect_key($data_ , $data); //获取键的交集 
        $r    = M('Shop_coupons')->where(array('id' => $id , 'seller_id' => $seller_id))->save($data);
        if($r === false){
            return array(
                'status' => 0,
                'msg'    => '修改失败'
            );
        }
        return array(
            'status' => 1,
            'msg'    =>'修改成功'
        );
    }

    /**
     * 检测优惠券数据
     * @param  array $data_   优惠券数据
     * @param  int   $type    1增加 2修改
     * @return array 返回操作结果和过滤后的数据
     */   
    public function checkCoupon($data = array() , $type = 1){
   	    if(empty($data)){
            return array('status' => 0 , 'msg' => '数据不能为空');  
   	    } 
        /*验证数据*/
        $validate_model = $type == 1 ? self::MUST_VALIDATE : self::EXISTS_VALIDATE;
	    $model = D("Shop_coupons");
	    $rules = array(	          
	        array('max','require','必须输入满多少的值',self::EXISTS_VALIDATE),
	        array('benefit_price','require','必须输入减多少的值',$validate_model), 
	        array('start_time','isDate','请输入正确的生效时间！',$validate_model,'function',true),
	        array('end_time','require','必须输入优惠券过期时间',$validate_model),
	        array('end_time','isDate','请输入正确的过期时间！',$validate_model,'function',true),
	        array('number','/^[1-9]\d*$/','请输入优惠券发放数量',$validate_model),
	        array('max_number','/^[1-9]\d*$/','请输入领取优惠券最大数量',$validate_model)
	    );
	    $type == 1 && (array_unshift($rules , 'seller_id','/^[1-9]\d*$/','卖家id错误',$validate_model));
	    $type == 2 && (array_unshift($rules , 'id','/^[1-9]\d*$/','优惠券id错误',self::MUST_VALIDATE));
	    if($model->validate($rules)->create($data) === false){
	        return array(
	            'status' => 0,
	            'msg'    => $model->getError()
	        );
	    }
	    /*验证 优惠券生效时间 生效时间 必须小于过期时间*/
	    if($type == 1 || (isset($data['start_time']) && isset($data['end_time']))){
            if(strtotime($data['start_time']) >= strtotime($data['end_time'])){
            	return array('status' => 0 , 'msg' => '生效时间必须小于过期时间');
            }
	    }else
	    if(isset($data['start_time']) || isset($data['end_time'])){
            $coupon = M('Shop_coupons')->where(array('id'=>$data['id']))->find();
            if(isset($data['start_time']) && strtotime($data['start_time']) >= strtotime($coupon['end_time'])){
                return array('status' => 0 , 'msg' => '生效时间必须小于过期时间');
            }
            if(isset($data['end_time']) && strtotime($coupon['start_time']) >= strtotime($data['end_time'])){
                return array('status' => 0 , 'msg' => '生效时间必须小于过期时间');
            }
	    }
        return array('status' => 1);    
    }  
  
   /**
    * 删除优惠券
    * @access public
    * @param  array $id     优惠id
    * @param  array $id     卖家id
    * @return array $result 执行结果
    */
    public function couponDelete($id , $seller_id){
	    $r = M('Shop_coupons')->where(array('id'=>$id,'seller_id'=>$seller_id))->delete();
	    if($r === false){
	        return array(
	            'status' => 0,
	            'msg'    => '删除失败'
	        );
	    }else{
	        return array(
	            'status' => 1,
	            'msg'    => '删除成功'
	        );
	    }
    }

   /**
    * 根据商家id获取 优惠券
    * @access public
    * @param  array $seller_id 卖家id
    * @return array $result    执行结果
    */    
    public function  getCouponsBySellerId($seller_id){
        if(empty($seller_id)){
            return array();          
        }
        if(is_array($seller_id)){
            $seller_id = implode(',' , $seller_id);
        }
        $data = M('Shop_coupons')
            ->where(array('seller_id'=>array('in' ,  $seller_id)))
            ->field('name,seller_id,start_time,end_time,max,benefit_price')
            ->select();
        return $data;
    }

   /**
    * 根据优惠券id 获取优惠券数据
    * @access public
    * @param  array $coupon_id 优惠券id
    * @return array $data      优惠券数据
    */    
    public function  getCouponsById($coupon_id){
        if(empty($coupon_id)){
            return array();          
        }
        $coupon_id = is_array($coupon_id) ? implode(',' , $coupon_id) : "$coupon_id";
        $data = M('Shop_coupons')
            ->where(array('id'=>array('in' ,  $coupon_id)))
            ->select();
        return $data === false ? array() : (is_array($coupon_id) ? $data : $data[0]);
    } 

   /**
    * 判断 优惠券是否 满足领取条件
    * @access public
    * @param  int   $coupon_id 优惠券id
    * @param  int   $member_id 会员id
    * @return array $result    操作结果
    */ 
    public function couponIsHave($coupon_id , $member_id){
        $member_id  = intval($member_id);
		$coupon_id  = intval($coupon_id);
		if(empty($member_id)){
			return array(
				'status' => 0,
				'msg'	 => '亲，请先登入再领取'
		    );            
		}
		if(empty($coupon_id)){
			return array(
				'status' => 0,
				'msg'	 => '优惠券id错误'
		    );            
		}   
		//获取优惠券信息
		$coupon = $this->getCouponsById($coupon_id);
		if($coupon['status'] == 0){
			return array(
				'status' => 0,
				'msg'	 => '该优惠券已经失效'
		    );         
		}
		if($coupon['sy_number'] <= 0){
			return array(
				'status' => 0,
				'msg'	 => '优惠券已抢完'
		    );         
		}	
		if($coupon['seller_id'] == $member_id){
			return array(
				'status' => 0,
				'msg'	 => '不能领取自己的优惠券'
		    );         
		}	
		//获取会员已经领取的数量
		$count = M('Member_shop_coupon')->where(array('coupon_id'=>$coupon_id,'member_id'=>$member_id))->count();	
		if($coupon['max_number'] <= $count){
			return array(
				'status' => 0,
				'msg'	 => '已经超过最大领取上限'
		    );         
		} 
		return array(
			'status' => 1,
			'data'   => $coupon
		);   
    }
}