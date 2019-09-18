<?php
/**
 * 优惠券处理 分平台优惠券和商家优惠券
 * @author 幸福无期
 */
namespace Home\Model;
use Think\Model;
class MemberShopCouponModel extends Model{
	protected $tableName ='member_shop_coupon';

	/**
     * 领取店铺优惠券
     * @param  int  $member_id    会员id
     * @param  int  $coupon_id_id 优惠券id
     * @return array 返回操作结果和过滤后的数据
     */ 
	public function couponAdd($member_id , $coupon_id){
		$member_id  = intval($member_id);
		$coupon_id  = intval($coupon_id);
		//查看优惠券是否可以领取
        $r = D('Home/ShopCoupons')->couponIsHave($coupon_id , $member_id);
        if($r['status'] == 0){
            return $r;
        }
        $coupon = $r['data'];
        $data = array(
            'member_id'     => $member_id,
            'coupon_id'     => $coupon_id,
            'seller_id'     => $coupon['seller_id'],
            'coupon_name'   => $coupon['name'],
            'max'           => $coupon['max'],
            'benefit_price' => $coupon['benefit_price'],
            'start_time'    => $coupon['start_time'],
            'end_time'      => $coupon['end_time'],
            'status'    	=> 1
        );
		$id = M('Member_shop_coupon')->add($data);
		if($id === false){
			return array(
				'status' => 0,
				'msg'	 => '领取失败'
		    );
		}
		//优惠券数量减少1
		M('Shop_coupons')->where(array('id' => $coupon_id))->setDec('sy_number');
		return array(
			'status' => 1,
			'msg'    => '领取成功'
		);			
	}

	/**
     * 获取会员 拥有的店铺优惠券 按会员id 和卖家id 查询 并统计相同优惠券张数 合并相同优惠券
     * @param  int       $member_id 会员id
     * @param  int|array $seller_id 卖家id
     * @return bool 返回操作结果
     */ 
	public function getCoupons($member_id , $seller_id = array() , $is_timeout = 1){
        if(empty($member_id) || empty($seller_id)){
            return array();
        }
        $condition = array(
            'member_id' => $member_id,
            'status'    => 1,
            'seller_id' => is_array($seller_id) ? array('in' , $seller_id) : $seller_id
        );
        $time = date('Y-m-d H:i:s');
        $is_timeout == 1 && ($condition['start_time'] = array('elt' , $time));
        $is_timeout == 1 && ($condition['end_time']   = array('egt' , $time));
        $field  = "id,coupon_id,seller_id,coupon_name,max,benefit_price";
        $data   = M('Member_shop_coupon')->where($condition)->field($field)->select();  
        if($data === false){
        	return array();
        }
        $coupon = array();
        foreach($data as $v){
        	if(!isset($coupon[$v['seller_id']])){
                $coupon[$v['seller_id']] = array();
        	}
        	if(!isset($coupon[$v['seller_id']][$v['coupon_id']])){
        		$v['coupon_number'] = 1;
        		$coupon[$v['seller_id']][$v['coupon_id']] = $v;
        	}else{
        		$coupon[$v['seller_id']][$v['coupon_id']]['coupon_number']++;
        	}
        }
        return $coupon;
	}

	/**
     * 计算店铺优惠券减免的金额
     * @param  int    member_id      买家id
     * @param  array  $member_coupon 优惠券数据 array('卖家id'=>array('seller_id'=>'卖家id','id'=>'会员优惠券id'));
     * @param  array  order          订单数据
     * @return array  result 
     */ 
	public function getShopCouponCase($member_id , $member_coupon , $order){
		$coupon_case = array();
		foreach($member_coupon as $k => $v){
			if(!isset($order[$k])){
                return false;
			}
			$coupon_case[$k] = 0;
			foreach($v as $v1){
				$v['member_id'] = $member_id;
	            $coupon = M('Member_shop_coupon')->where($v1)->find();	            
	            if(empty($coupon)){
	                return false;
	            }
	            /*判断是否满足使用优惠券条件*/
	            if($order[$k]['total_price'] < $coupon['max']){
		            return false;
			    }
			    $coupon_case[$k] = $coupon_case[$k] + $coupon['benefit_price'];//统计减免金额
			} 
		}
		return $coupon_case;         
	}
} 