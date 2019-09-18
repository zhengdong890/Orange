<?php
/**
 * 商品品牌业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class Goods_brandModel extends Model{
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
        	'brand_name' => $data_['brand_name'],
          'brand_en_name' => $data_['brand_en_name']
        ); 
        /*验证数据*/
        $model = D('Goods_brand');
        $rules = array(
            array('brand_name','require','品牌名称不能为空',self::EXISTS_VALIDATE)
        );
        if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
        }
        if($data_['brand_thumb']){
        	$data['brand_thumb'] = $data_['brand_thumb'];
        }
        $r  = M('Goods_brand')->add($data);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }
        $result['id'] = $r;
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
        	'brand_name' => $data_['brand_name'] ? $data_['brand_name'] : '',
          'brand_en_name' => $data_['brand_en_name']
        ); 
        /*验证数据*/
        $model = D('Goods_brand');
        $rules = array(
            array('id','/^[1-9]\d*$/','请选择id'),
            array('brand_name','require','品牌名称不能为空',self::EXISTS_VALIDATE)
        );
        if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
        }
        if($data_['brand_thumb']){
        	$data['brand_thumb'] = $data_['brand_thumb'];
        	$old_thumb     = M('Goods_brand')->where(array('id'=>$data['id']))->getField('brand_thumb');
        }
        $id = $data['id'];unset($data['id']);
        $r  = M('Goods_brand')->where(array('id'=>$id ))->save($data);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据修改失败'
            );
        }else{
            if($data['brand_thumb']){
        	    unlink($old_thumb);
            }
        }
        return $result;
  }

  /**
   * 品牌删除
   * @access public
   * @param  int    $id    品牌id
   * @return array $result 执行结果
   */ 
  public function brandDelete($id){
        $result = array(
            'status' => 1,
            'msg'    => '数据删除成功'
        );  
        $id = intval($id);
        if($id){
        	$r = M('Goods_brand')->where(array('id'=>$id))->delete();
        	if($r === false){
        	    $result = array(
		            'status' => 0,
		            'msg'    => '数据删除失败'
		        ); 
        	}
        }else{
        	$result = array(
	            'status' => 0,
	            'msg'    => '品牌id错误'
            );  
        }
        return $result;
  }    
}