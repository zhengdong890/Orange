<?php
/**
 * 商家信息业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Home\Model;
use Think\Model;
class ShopDataModel extends Model{
   protected $tableName = 'shop_data';
   /**
    * 根据会员账号id获取店铺信息
    * @access public
    * @param  int|array $member_id 会员id
    * @return array     $data 执行结果
    */
    public function getShopDataByMemberId($member_id , $field = '*'){
    	if(empty($member_id)){
            return array();
    	}
    	if(is_array($member_id)){
    		$member_id = implode(',' , $member_id);
    	}
        $data = M('Shop_data')
            ->where(array('member_id'=>array('in' , $member_id)))
            ->field($field)
            ->select();
        return $data;
    }
}