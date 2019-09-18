<?php
/**
 * 商品类型模块业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class Goods_typeModel extends Model{
  /**
   * 获取商品类型
   * @access public  
   * @return array $result 执行结果
   */ 
  public function getGoodsType(){
      $goods_type = M('Goods_type')->select();    
      return $goods_type;     
  }

  /**
   * 获取商品类型属性
   * @access public 
   * @return array $result 执行结果 
   */ 
  public function getFilterAttr($type_id){
	  if($type_id){
	  	  //获取商品类型下的属性
		  $attr = M('Attrbute')
		        ->where(array('type_id'=>$type_id))
		        ->select();
		  return array(
	          'status' => 1,
	          'msg'    => 'ok',
	          'data'   => $attr
	      );   
	  }
	  return array(
          'status' => 0,
          'msg'    => '请选择商品类型id'
	  );
  }  
}