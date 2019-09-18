<?php
/**
 * 注册会员模块业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class MemberModel extends Model{
    /**
     * 更改用户锁定状态
     * @access public
     * @param  int   $id     用户id
     * @return array $result 执行结果
     */    
  public function changeLock($id , $status){
      $id    = intval($id);
      $status= intval($status) == 0 ? 0 : 1;
      if(!$id){
          return array('status'=>0 , 'msg'=>'id错误');    
      }
      $r = M('Member')->where(array('id'=>$id))->save(array('lock'=>$status));
      if($r === false){
          return array('status'=>0 , 'msg'=>'操作失败');    
      }
      return array('status'=>1 , 'msg'=>'操作成功');
  } 
}