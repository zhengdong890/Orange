<?php
/**
 * 平台优惠券业务
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class CouponsModel extends Model{
   /**
    * 获取优惠券列表 分页 
    * @param  array $data 审核结果 
    * @return array 返回结果
    */
    public function couponDataPageList($condition = array() , $limit = array(0 , 10) , $field = '*'){
        $list = M('Coupons')
            ->order('id desc')
            ->limit($limit[0] , $limit[1])
            ->where($condition)
            ->select();
        return array(
            'data'  => $list,
            'total' => M('Coupons')->where($condition)->count(),
        );
    }

    /**
     * 添加优惠券
     * @access public
     * @param  array $data_  优惠数据
     * @return array $result 执行结果
     */
    public function couponAdd($coupon_data){
        /*优惠券数据*/
        $data = array(
            'max'           => '',//满多少
            'benefit_price' => '',//面值 减多少
            'start_time'    => '',//开始时间
            'end_time'      => '',//结束时间
            'number'        => '',//发放总量
            'sy_number'     => '',//剩余数量
            'max_number'    => '',//每人限领张数
            'name'          => '' //优惠券名字
        );
        $coupon_data['sy_number'] = $coupon_data['number'];
        $data = array_intersect_key($coupon_data , $data); //获取键的交集 
        $r = M('Coupons')->add($data);
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
    public function couponUpdate($coupon_data){
        /*优惠券数据*/
        $data = array(
            'max'           => '',//满多少
            'benefit_price' => '',//减多少
            'start_time'    => '',//开始时间
            'end_time'      => '',//结束时间
            'number'        => '',//发放总量
            'sy_number'     => '',//剩余数量
            'max_number'    => '',//每人限领张数
            'name'          => '' //优惠券名字
        );
        $id   = $coupon_data['id'];
        $data = array_intersect_key($coupon_data , $data); //获取键的交集 
        $r    = M('Coupons')->where(array('id' => $id))->save($data);
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
	    $model = D("Coupons");
	    $rules = array(	          
	        array('max','require','必须输入满多少的值',self::EXISTS_VALIDATE),
	        array('benefit_price','require','必须输入减多少的值',$validate_model), 
	        array('start_time','isDate','请输入正确的生效时间！',$validate_model,'function',true),
	        array('end_time','require','必须输入优惠券过期时间',$validate_model),
	        array('end_time','isDate','请输入正确的过期时间！',$validate_model,'function',true),
	        array('number','/^[1-9]\d*$/','请输入优惠券发放数量',$validate_model),
	        array('max_number','/^[1-9]\d*$/','请输入领取优惠券最大数量',$validate_model)
	    );
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
            $coupon = M('Coupons')->where(array('id'=>$data['id']))->find();
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
    public function couponDelete($id){
	    $r = M('Coupons')->where(array('id' => $id))->delete();
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
        $data = M('Coupons')
            ->where(array('id'=>array('in' ,  $coupon_id)))
            ->select();
        return $data === false ? array() : (is_array($coupon_id) ? $data : $data[0]);
    } 
}