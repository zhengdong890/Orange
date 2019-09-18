<?php
/**
 * 快递选择设置业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class CourierModel extends Model{
    /**
     * 添加快递公司
     * @access public
     * @param  array $data_  添加的快递公司数据
     * @return array $result 执行结果
     */    
  public function courierAdd($data_){
      $result = array(
          'status' => 1,
          'msg'    => '添加成功'
      );
      $data = array(
          'name'   => $data_['name'],
          'remark' => $data_['remark'],
          'status' => $data_['status'],
          'thumb'  => $data_['thumb']
      );
      /*验证数据*/
      $model = D("Courier");
      $rules = array(
          array('name','require','必须输入快递公司名字',self::EXISTS_VALIDATE),
          array('thumb','require','必须输入图片',self::EXISTS_VALIDATE)
      );
      if($model->validate($rules)->create($data) === false){
          $result = array(
              'status' => 0,
              'msg'    => $model->getError()
          );
          return $result;
      }
      $r = M('Courier')->add($data);
      if($r === false){
          $result = array(
              'status' => 0,
              'msg'    => '添加失败'
          );
      }
      return $result;
  }
  
  /**
   * 修改快递公司
   * @access public
   * @param  array $data_  修改快递公司数据
   * @return array $result 执行结果
   */
  public function courierUpdate($data_){
      $result = array(
          'status' => 1,
          'msg'    => '添加成功'
      );
      $data = array(
          'id'     => intval($data_['id']),
          'name'   => $data_['name'],
          'remark' => $data_['remark'],
          'status' => $data_['status']
      );
      if($data_['thumb']){
          $data['thumb'] = $data_['thumb'];
      }
      /*验证数据*/
      $model = D("Courier");
      $rules = array(
          array('id','/^[1-9]\d*$/','快递公司id不正确'),
          array('name','require','必须输入快递公司名字',self::EXISTS_VALIDATE),
          array('thumb','require','必须输入图片',self::EXISTS_VALIDATE)
      );
      if($model->validate($rules)->create($data) === false){
          $result = array(
              'status' => 0,
              'msg'    => $model->getError()
          );
          return $result;
      }
      unset($data['id']);
      $r = M('Courier')->where(array('id'=>$data_['id']))->save($data);
      if($r === false){
          $result = array(
              'status' => 0,
              'msg'    => '添加失败'
          );
      }
      return $result;
  }  
  
  /**
   * 删除快递公司
   * @access public
   * @param  int   $id     删除快递公司id
   * @return array $result 执行结果
   */
  public function courierDelete($id){
      $result = array(
          'status' => 1,
          'msg'    => '添加成功'
      );
      $id = intval($id);
      if(!$id){
          return array(
              'status' => 0,
              'msg'    => 'id不正确'
          );
      }
      $r = M('Courier')->where(array('id'=>$id))->delete();
      if($r === false){
          $result = array(
              'status' => 0,
              'msg'    => '删除失败'
          );
      }
      $thumb = M('Courier')->where(array('id'=>$id))->getField('thumb');
      unset($thumb);
      return $result;
  }  
}