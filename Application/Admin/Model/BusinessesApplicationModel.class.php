<?php
/**
 * 企业认证业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class BusinessesApplicationModel extends Model{
    protected $tableName = 'Businesses_application'; //切换检测字段
  
	/**
	* 企业认证审核
	* @access public
	* @param  array $data   认证审核数据 一维数组    
	* @return array $result 执行结果
	*/ 
	public function qualification($data){ 
    	//数据过滤
        $data_filter = array(
            'id'           => '',//企业认证信息id
            'status'       => '',//审核状态
            'because'      => '',//原因
            'content'      => '',//审核内容
        );
        $data  = array_intersect_key($data , $data_filter); //获取键的交集    	    
	    $id    = $data['id'];unset($data['id']);       
	    $data['check_status'] = 1;
	    $data['check_time']   = time();
	    $r     = M('Businesses_application')->where(array('id'=>$id))->save($data);
	    /*认证通过*/
	    if($data['status'] == 1){
	        $member_id = M('Businesses_application')
	                   ->where(array('id'=>$id))
	                   ->getField('member_id');
	        M('Member')->where(array('id'=>$member_id))->save(array('is_renzheng'=>1));
	        //企业认证送积分
	   		$r = D('Home/MemberScore')->score($member_id , 'BUSINESS_APPLICATION');
	    }else{
	        $member_id = M('Businesses_application')
	                   ->where(array('id'=>$id))
	                   ->getField('member_id');
	        M('Member')->where(array('id'=>$member_id))->save(array('is_renzheng'=>0));
	    }
	    if($r === false){
	        return array(
	            'status' => 0,
	            'msg'    => '审核失败'
	        );
	    }
	    return array(
	        'status' => 1,
	        'msg'    => '审核成功'
	    );
	}

  /**
	* 企业认证数据检测
	* @access public
	* @param  array $data   认证审核数据 一维数组    
	* @return array $result 执行结果
	*/     
    public function checkQualificationData($data){
	    $model  = D('Businesses_application');
	    $rules  = array(
	    	array('id','/^([1-9]\d*)|0+$/','请选择需要审核的企业'),
	        array('status','/^0|1$/','请选择正确的审核结果')
	    );
	    if($model->validate($rules)->create($data) === false){
	        return array(
	            'status' => 0,
	            'msg'    => $model->getError()
	        );
	    } 
	    return array(
	        'status' => 1
	    );
    }

   /**
    * 检测未通过原因数据是否合法
    * @access protected
    * @param  array $baseuse  通过原因数据 一维数组    
    * @return array $result   执行结果
    */ 
    protected function checkBecause($baseuse){
        $arr = array(1,2,3,4);
        $r   = array_diff($baseuse , $arr);
        if(count($r) > 0){
            return false;
        }
        return true;    
    }
 }