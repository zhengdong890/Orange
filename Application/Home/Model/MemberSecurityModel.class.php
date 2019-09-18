<?php
namespace Home\Model;
use Think\Model;
/**
 * 密保问题模块业务逻辑
 * @author 幸福无期
 */
class MemberSecurityModel extends Model{  
  protected $tableName = 'Member_security'; //关闭检测字段
  /**
   * 添加密保问题
   * @access public
   * @param  array $data       密保问题数据
   * @param  int   $member_id  会员id
   * @return array $result 执行结果
   */ 
  public function securityAdd($data_ = array() , $member_id){
      if(!intval($member_id)){
          return array(
              'status' => 0,
              'msg'    => '请输入会员id'
          );
      }
      /*检测密保问题答案是否合法*/
      $security_data = array_keys(C('SECURITY'));
      if(count($data_) < 3){
          return array(
              'status' => 0,
              'msg'    => '请填写完整密保'
          );
      }
      foreach($data_ as $k => $v){
          if(!in_array($v['question'] , $security_data)){
              return array(
                  'status' => 0,
                  'msg'    => '请选择正确的密保问题'
              ); 
          }
          if(!$v['answer']){
              return array(
                  'status' => 0,
                  'msg'    => '请选择正确的密保答案'
              );
          }
      }
      /*批量添加*/
      $values = array();
      $fields = array('`member_id`','`question`','`answer`','`create_time`');
      foreach($data_ as $k => $v){
          $data = array(
              'member_id' => $member_id,
              'question'  => $v['question'],
              'answer'    => $v['answer'],
              'create_time' => date('Y-m-d H:i:s')
          );
          $values[] = "('" . implode("','",$data) . "')";
      }
      $sql = "INSERT INTO `tp_member_security` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
      $r   = M()->execute($sql);
      if($r === false){
          return array(
              'status' => 0,
              'msg'    => '设置失败'
          );
      }else{
          M('Member')->where(array('id'=>$member_id))->save(array('is_security'=>1));
          return array(
              'status' => 1,
              'msg'    => '设置成功'
          );          
      }
  }
}