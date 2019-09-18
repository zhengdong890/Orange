<?php
/**
 * 共享商品加入推荐业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class Goods_modelModel extends Model{
  /**
   * 添加共享商品加入推荐
   * @access public
   * @param  array $data   共享商品加入推荐数据 一维数组    
   * @return array $result 执行结果
   */ 
  public function modelAdd($data_){ 
  	    $result = array(
            'status' => 1,
            'msg'    => '数据添加成功'
        ); 
        $data = array(
            'name'   => $data_['name'],
            'status' => intval($data_['status']),
            'sort'   => intval($data_['sort'])
        );    
        /*验证数据*/
        $model  = D('Goods_model');
        $rules  = array(
            array('name','require','请输入模块名称')
        );
        if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
        } 
        $id = M('Goods_model')->add($data);
        if($id === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }
        return $result;
  }

  /**
   * 修改共享商品加入推荐
   * @access public
   * @param  array $data   共享商品加入推荐数据 一维数组    
   * @return array $result 执行结果
   */ 
  public function modelUpdate($data_){ 
        $result = array(
            'status' => 1,
            'msg'    => '数据修改成功'
        ); 
        $data = array(
            'id'     => $data_['id'],
            'name'   => $data_['name'],
            'status' => intval($data_['status']),
            'sort'   => intval($data_['sort'])
        );    
        /*验证数据*/
        $model  = D('Goods_model');
        $rules  = array(
            array('name','require','请输入模块名称')
        );
        if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
        } 
        $id = $data['id'];unset($data['id']);
        $r  = M('Goods_model')->where(array('id'=>$id))->save($data);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据修改失败'
            );
        }
        return $result;
    } 

   /**
    * 删除共享商品加入推荐
    * @access public
    * @param  int   $id   共享商品加入推荐id 
    * @return array $result 执行结果
    */ 
    public function modelDelete($id){  
        $id = intval($id);
        if(!$id){
        	return array(
                'status' => 0,
                'msg'    => '请选择正确的id'
            ); 
        }
        $result = array(
            'status' => 1,
            'msg'    => '删除成功'
        );        
        $r = M('Goods_model')->where(array('id'=>$id))->delete();
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '删除失败'
            ); 
        }
        return $result;
    }    
}