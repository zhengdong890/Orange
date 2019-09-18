<?php
/**
 * 商城商品分类品牌业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class MallCategoryBrandModel extends Model{
    protected $tableName = 'mall_category_brand'; 
    public function brandAdd($data_){
        $result = array(
            'status' => 1,
            'msg'    => '添加成功'
        );
        $data = array(
            'cat_id'      => $data_['cat_id'],
            'brand_id'    => $data_['brand_id']
        );
        $model  = D('Mall_category_brand');
        $rules  = array(
            array('cat_id','/^[1-9]\d*$/','请选择所属分类',self::MUST_VALIDATE),
            array('brand_id','/^[1-9]\d*$/','品牌id不能为空',self::MUST_VALIDATE)
        );
        if($model->validate($rules)->create($data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $model->getError()
            );
            return $result;
        }  
        $r = M('Mall_category_brand')->where($data)->find();
        if(!empty($r)){
	        return array(
	            'status' => 1,
	            'msg'    => '添加成功'
	        );
        }
        $r = M('Mall_category_brand')->add($data);
        if($r === false){
            return array(
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
   * 商品分类品牌 删除
   * @access public   
   * @return array $categorys 执行结果
   */ 
    public function categoryBrandDelete($condition = array()){
        if(empty($condition)){
      	    return false;
        }
        $r = M('Mall_category_brand')->where($condition)->delete();    
        if($r === false){
            return array(
                'status' => 0,
                'msg'    => '删除失败'
            );
        }
        return array(
            'status' => 1,
            'msg'    => '删除成功'
        );
    }    
}