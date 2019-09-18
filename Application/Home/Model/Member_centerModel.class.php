<?php
namespace Home\Model;
use Think\Model;
/**
 * 商品模块业务逻辑
 * @author 幸福无期
 */
class Member_centerModel extends Model{   
	protected $tableName='Member'; //关闭检测字段
   /**
    * 获取会员信息
    */
   public function getMemberData($member_id){
        $member_data = M('Member as a')
    	   ->join("tp_member_data as b on a.id=b.member_id")
    	   ->field("a.username,a.id,b.sex,b.email,b.qq")
    	   ->where(array('member_id'=>$member_id))
    	   ->find();
    	return  $member_data; 
   }
}