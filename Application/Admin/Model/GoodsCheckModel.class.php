<?php
/**
 * 共享商品审核业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class GoodsCheckModel extends Model{
    protected $tableName='Goods_check';
    /**
     * 商品审核
     * @access public
     * @return array $result 执行结果
     */    
  public function goodsCheck($data){
      $result = array(
          'status' => 1,
          'msg'    => '审核成功'
      );
      $data['check_status'] = $data['check_status'] == 1 ? $data['check_status'] : 0;
      $data['id'] = intval($data['id']);
      if(!$data['id']){
          return array(
              'status' => 1,
              'msg'    => '商品id不正确'
          );
      }
      //审核失败
      if(!$data['check_status'] && !$data['content']){
          return array(
              'status' => 0,
              'msg'    => '请输入审核不通过原因'
          );
      }
      $check_data = array(
          'content'  => $data['content'],
          'goods_id' => $data['id'],
          'status'   => $data['check_status'],
          'time'     => date('Y-m-d H:i:s')
      );    
      $r = M('Goods')->where(array('id'=>$data['id']))->save(array('check_status'=>$data['check_status'],'is_check'=>1));
      if($r === false){
          return array(
              'status' => 0,
              'msg'    => '审核失败'
          );          
      }
      //审核失败
      if(!$data['check_status']){
          $id = M('Goods_check')->where(array('goods_id'=>$data['id']))->getField('id');
          if($id){
              $r = M('Goods_check')->where(array('id'=>$id))->save($check_data);
          }else{
              $r = M('Goods_check')->add($check_data);
          }         
      }      
      return $result;
  }
} 