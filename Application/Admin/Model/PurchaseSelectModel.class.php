<?php
/**
 * 中标融资租赁业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class PurchaseSelectModel extends Model{
  protected $tableName='purchase_select'; //关闭检测字段

  /**
   * 添加中标融资租赁
   * @access public
   * @param  array $data_   融资租赁信息 一维数组    
   * @return array $result 执行结果
   */ 
  public function selectAdd($data_){              
        $data = array(
        	'title'          => $data_['title'], //命名
            'desc'           => $data_['desc'], //描述
            'area'           => $data_['area'],//地址                                 
            'create_time'    => date('Y-m-d H:i:s'),
            'update_time'    => date('Y-m-d H:i:s'),
            'status'         => intval($data_['status']) == 1 ? 1 : 0
        );  
        /*验证数据*/
        $model  = D('Purchase_select');
        $rules  = array(
            array('title','require','必须输入命名标题',self::MUST_VALIDATE),
            array('desc','require','必须输入描述',self::MUST_VALIDATE),
            array('area','require','必须输入地址',self::MUST_VALIDATE)
        );
        if($model->validate($rules)->create($data) === false){
           return array(
             'status' => 0,
             'msg'    => $model->getError()
           );
        }
        $id = M('Purchase_select')->add($data);
        if($id === false){
           $result = array(
              'status' => 0,
              'msg'    => '添加失败'
           ); 
        }
        return array(
            'status' => 1,
            'msg'    => '添加成功'
        );
  }

  /**
   * 修改中标融资租赁
   * @access public
   * @param  array $data_   中标融资租赁信息 一维数组
   * @return array $result 执行结果
   */
  public function selectUpdate($data_){
      $data = array(
          'id'             => $data_['id'],
          'title'          => $data_['title'], //命名
          'desc'           => $data_['desc'], //描述
          'area'           => $data_['area'],//地址
          'update_time'    => date('Y-m-d H:i:s'),
          'status'         => intval($data_['status']) == 1 ? 1 : 0
      );
      /*验证数据*/
      $model  = D('Purchase_select');
      $rules  = array(
          array('id','/^[1-9]\d*$/','请选择id',self::MUST_VALIDATE),
          array('title','require','必须输入命名标题',self::MUST_VALIDATE),
          array('desc','require','必须输入描述',self::MUST_VALIDATE),
          array('area','require','必须输入地址',self::MUST_VALIDATE)
      );
      if($model->validate($rules)->create($data) === false){
          return array(
              'status' => 0,
              'msg'    => $model->getError()
          );
      }
      $id = M('Purchase_select')->save($data);
      if($id === false){
          $result = array(
              'status' => 0,
              'msg'    => '数据添加失败'
          );
      }
      return array(
          'status' => 1,
          'msg'    => '添加成功'
      );
  }
  
  /**
   * 删除中标融资租赁
   * @access public
   * @param  int   $id     融资租赁id 
   * @return array $result 执行结果
   */ 
  public function selectDelete($id){ 
       $result = array(
          'status' => 0,
          'msg'    => '删除成功'
       );     
       if(!preg_match('/^[1-9]\d*$/', $id)){
           return array(
             'status' => 0,
             'msg'    => '请选择正确的id'
           );
       }
       $r = M('Purchase_select')->where(array('id'=>$id))->delete();
       if($r === false){
          $result = array(
              'status' => 0,
              'msg'    => '删除失败'
          ); 
       }
       return $result;
  } 
}