<?php
/**
 * 商品模块业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class MemberCardedModel extends Model{
   protected $tableName='Member_carded';
    
  /**
   * 身份认证申请审核
   * @access public
   * @return array $result 执行结果
   */ 
  public function qualificationCheck($data = array()){
        $id = intval($data['id']);
        if(!$id){
           return array(
               'status' => 0,
               'msg'    => '必须传入id'
           );
        }
        $check_data = array(
            'status'     => intval($data['status']) == 1 ? 1 : 0, 
            'content'    => $data['content'],
            'check_time' => date('Y-m-d H:i:s'),
            'is_check'   => 1
        );
        $r = M('Member_carded')->where(array('id'=>$id))->save($check_data);
        if($r === false){
            return array(
                'status' => 0,
                'msg'    => '审核失败'
            );
        }else{
            $member_id = M('Member_carded')->where(array('id'=>$id))->getField('member_id');
            if(!$data['status']){//审核不通过
                M('Member')->where(array('id'=>$member_id))->save(array('is_identity'=>'-1'));
            }else{
                M('Member')->where(array('id'=>$member_id))->save(array('is_identity'=>'1'));
            }
            return array(
                'status' => 1,
                'msg'    => '审核成功'
            );
        }
  }
}