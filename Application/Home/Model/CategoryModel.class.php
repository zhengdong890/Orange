<?php
namespace Home\Model;
use Think\Model;
/**
 * 共享商品分类业务逻辑
 * @author 幸福无期
 */
class CategoryModel extends Model{   
 /**
  * 主页商品获取
  * @return array 返回结果
  */
  public function getCategory(){
      $categorys = M("Category")->where(array('status'=>'1'))->select();//获取商品分类
      return $categorys;
  }
}