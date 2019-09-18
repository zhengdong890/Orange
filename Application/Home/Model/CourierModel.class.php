<?php
/**
 * 快递选择设置业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Home\Model;
use Think\Model;
class CourierModel extends Model{
    /**
     * 选择快递公司
     * @access public
     * @param  array $data_  选择的快递公司
     * @return array $result 执行结果
     */    
  public function selectCouriersCompany($seller_id , $data_){
      $seller_id = intval($seller_id);
      if(!$seller_id){
          return array('status'=>0,'卖家id不正确');
      }     
      $data = array();
      foreach($data_ as $v){
          $data[] = intval($v);
      }
      $courier_ids = M('Courier')->where(array('seller_id'=>$seller_id))->getField('courier_ids');
      $courier_arr = explode(',' , $courier_ids);
      $data = array_merge($data , $courier_arr);
      $courier_ids = implode(',' , $data);
      $r = M('Courier')->where(array('seller_id'=>$seller_id))->save(array('courier_ids'=>$courier_ids));
      if($r === false){
          return array(
              'status' => 0,
              'msg'    => '操作失败'
          );
      }
      return array(
          'status' => 1,
          'msg'    => '操作成功'
      );
  }
  
  /**
   * 取消选择的快递公司
   * @access public
   * @param  array $data_  取消选择的快递公司
   * @param  int   $seller_id  卖家id 
   * @param  int   $courier_id 选择类型 1选择一个 2批量选择
   * @return array $result 执行结果
   */
  public function awayCouriersCompany($seller_id , $courier_id){
      $seller_id = intval($seller_id);
      if(!$seller_id){
          return array('status'=>0,'卖家id不正确');   
      }
      $courier_id = intval($courier_id);
      if(!$courier_id){
          return array('status'=>0,'快递公司id不正确');
      }
      $courier_ids = M('Courier')->where(array('seller_id'=>$seller_id))->getField('courier_ids');
      $courier_arr = explode(',' , $courier_ids);
      $k = array_search($courier_id, $courier_arr);
      unset($courier_arr[$k]);
      $courier_ids = implode(',' , $courier_arr);
      $r = M('Courier')->where(array('seller_id'=>$seller_id))->save(array('courier_ids'=>$courier_ids));
      if($r === false){
          return array(
              'status' => 0,
              'msg'    => '操作失败'
          );
      }
      return array(
          'status' => 1,
          'msg'    => '操作成功'
      );
  }  
}