<?php
/**
 * 平台优惠券处理 
 * @author 幸福无期
 */
namespace Home\Model;
use Think\Model;
class MemberCouponModel extends Model{
	/**
     * 获取会员 拥有的平台优惠券 
     * @param  int  $member_id  会员id
     * @param  int  $is_timeout 是否过滤过期的优惠券
     * @return bool 返回操作结果
     */ 
	public function getCoupons($member_id , $is_timeout = 1){
        if(empty($member_id)){
            return array();
        }
        $condition = array(
            'member_id' => $member_id,
            'status'    => 1
        );
        $time = date('Y-m-d H:i:s');
        $is_timeout == 1 && ($condition['start_time'] = array('elt' , $time));
        $is_timeout == 1 && ($condition['end_time']   = array('egt' , $time));
        $field  = "id,coupon_id,coupon_name,max,benefit_price";
        $data   = M('Member_coupon')->where($condition)->field($field)->select();  
        if($data === false){
        	return array();
        }
        $coupon = array();
        foreach($data as $v){
        	if(!isset($coupon[$v['id']])){
                $coupon[$v['id']] = array();
        	}
        	if(!isset($coupon[$v['id']][$v['coupon_id']])){
        		$v['coupon_number'] = 1;
        		$coupon[$v['id']][$v['coupon_id']] = $v;
        	}else{
        		$coupon[$v['id']][$v['coupon_id']]['coupon_number']++;
        	}
        }
        return $coupon;
	}

	/**
     * 计算平台优惠券减免的金额
     * @param  int    member_id         买家id
     * @param  array  $member_coupon_id 会员有的平台优惠券id;
     * @param  float  $price            订单总额
     * @return array  result 
     */ 
	public function getCouponCase($member_id , $member_coupon_id , $price){
		$data = M('Member_coupon')->where(array('id'=>$member_coupon_id,'member_id'=>$member_id))->find();
	    if(empty($data)){
            return false;
        }
		if($price < $data['max']){
            return false;
		}
		return $data['benefit_price'];    
	}
} 