<?php
/**
 * 商户模块业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class MallApplicationModel extends Model{
  protected $tableName='Mall_application'; //关闭检测字段
  
   /**
    * 商城申请审核
    * @access public
    * @param  array $data      申请数据 一维数组
    * @param  int   $seller_id 卖家id
    * @return array $result 执行结果
    */  
    public function mallApplicationCheck($data_ , $seller_id){
        if(!$seller_id){
            return array(
              'status' => 0,
              'msg'    => '卖家id错误'
            );
        }
        //审核数据过滤
        $data = array(
            'id'         => intval($data_['id']), // 申请id
            'status'     => intval($data_['status']) == 0 ? intval($data_['status']) : 1, //审核结果
            'is_sign'     => intval($data_['is_sign']) == 0 ? intval($data_['is_sign']) : 1, //审核结果
            'content'    => $data_['content'], //审核意见
            'check_status' => 1, //标记为已经审核
            'check_time' => time() //审核时间
        );
        $r  = M('Mall_application')->save($data);
        if($r === false){
            return array(
              'status' => 0,
              'msg'    => '审核失败'
            );          
        }
        if($data['status'] == 1){//审核通过
            M('Member')->where(array('id'=>$seller_id))->save(array('is_mall'=>1));
            //申请商城送积分
	   	    $r = D('Home/MemberScore')->score($member_id , 'SHOP');
        }else{
            M('Member')->where(array('id'=>$seller_id))->save(array('is_mall'=>0));
        }
        return array(
            'status' => 1,
            'msg'    => '审核成功'
        );
    }
  
   /**
    * 商城申请 检测数据
    * @access public
    * @param  array $data  
    * @return array $result 执行结果
    */  
    public function checkMallApplication($data){
        $result = array(
          'status' => 1,
          'msg'    => '审核成功'
        );
        /*检测数据合法性*/
        $model  = D('Businesses_application');
        $rules  = array(
          array('id','/^([1-9]\d*)|0+$/','请选择需要审核的企业'),
          array('status','/^0|1$/','请选择正确的审核结果')
        );
        if($model->validate($rules)->create($data) === false){
            $result = array(
              'status' => 0,
              'msg'    => $model->getError()
            );
        }
        return $result;
    } 
}