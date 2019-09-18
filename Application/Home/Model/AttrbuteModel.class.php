<?php
 /**
 * 商品属性模块业务逻辑
 * @author 幸福无期
 * @email 597089187@qq.com
 */
namespace Home\Model;
use Think\Model;
class AttrbuteModel extends Model{   
   /**
    * 获取商品分类下的的属性和属性值
    * @access  public
    * @param   int $cat_id 商品分类id  
    * @return         
    */   
    public function getCategoryBaseAttr($filter_attr){
    	//先获取属性   	
	    $attr = M('Attrbute')
    	      ->where(array('attr_id' => array('in',$filter_attr)))
    	      ->field('attr_id,attr_name')
    	      ->select();
    	if(count($attr) <= 0){
            return array();
    	}      
    	//获取属性值
    	$attr_id   = implode(',' , array_column($attr , 'attr_id'));
    	$attr_temp =  M('Attrbute_value')
	    	  ->where(array('attr_id'=>array('in' , $attr_id)))
	    	  ->field('attr_value_id,attr_id,attr_value')
	    	  ->select();	

    	//组合  
    	foreach($attr_temp as $v){
            $attr_value[$v['attr_id']][] = $v;
    	}
    	foreach($attr as $k => $v){
            $attr[$k]['attr_value'] = isset($attr_value[$v['attr_id']]) ? $attr_value[$v['attr_id']] : array();
    	}
    	return $attr;
    }

   /**
    * 获取属性 根据属性id
    * @access  public
    * @param   int|array $attr_id 属性id  
    * @return         
    */   
    public function getAttrDataById($attr_id , $field = ''){
    	if(!is_array($attr_id)){
    		$attr_id = array($attr_id);
    	}
    	$attr_id = implode(',' , $attr_id);
    	$model   = M('Attrbute')->where(array('id'=>array('in' , $attr_id)));
    	!empty($field) && ($model = $model->field($field));
    	$data    = $model->select();
        return $data;
    }
}