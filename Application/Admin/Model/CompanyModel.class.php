<?php
/**
 * 公司类型及品牌业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class CompanyModel extends Model{
  protected $tableName='Company_type'; //关闭检测字段

  /**
   * 品牌增加
   * @access public
   * @param  array $data   品牌增加数据
   * @return array $result 执行结果
   */ 
  public function brandAdd($data_){
        $result = array(
            'status' => 1,
            'msg'    => '数据添加成功'
        );  
        $data = array(
        	'name' => $data_['name']
        ); 
        /*验证数据*/
        $model = D('Company_brand');
        $rules = array(
            array('name','require','品牌名称不能为空')
        );
        if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
        }
        if($data_['thumb']){
        	$data['thumb'] = $data_['thumb'];
        }
        $r  = M('Company_brand')->add($data);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }
        return $result;
  }

  /**
   * 品牌修改
   * @access public
   * @param  array $data   品牌修改数据
   * @return array $result 执行结果
   */ 
  public function brandUpdate($data_){
        $result = array(
            'status' => 1,
            'msg'    => '数据修改成功'
        );  
        $data = array(
        	'id'   => intval($data_['id']),
        	'name' => $data_['name']
        ); 
        /*验证数据*/
        $model = D('Company_brand');
        $rules = array(
            array('id','/^[1-9]\d*$/','请选择id'),
            array('name','require','品牌名称不能为空')
        );
        if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
        }
        if($data_['thumb']){
        	$data['thumb'] = $data_['thumb'];
        	$old_thumb     = M('Company_brand')->where(array('id'=>$data['id']))->getField('thumb');
        }
        $id = $data['id'];unset($data['id']);
        $r  = M('Company_brand')->where(array('id'=>$id ))->save($data);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据修改失败'
            );
        }else{
            if($data['thumb']){
        	    unlink($old_thumb);
            }
        }
        return $result;
  } 
}