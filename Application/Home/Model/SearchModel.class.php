<?php
 /**
 * 搜索查询
 * @author 幸福无期
 * @email 597089187@qq.com
 */
namespace Home\Model;
use Think\Model;
class SearchModel extends Model{   
	protected $tableName='sku'; //切换检测表
   /**
    * 通过选择的sku属性 从sku_value表里面 获取满足条件的商品
    * @access  public
    * @param   array $search_attr sku属性集 
    *  array(3) {
	* 	  [0] => string(16) "3217_14309_14308"
	* 	  [1] => string(10) "3218_14311"
	* 	  [2] => string(10) "3219_14312"
	*  }
    * @return         
    */   
    public function getGoodsByAttr($search_attr = array()){
    	$table_name  = array('a','b','c','d','e'); 
    	if(!$search_attr){
            return array();
    	} 
        $attr_where = '';
        $join       = '';
        foreach($search_attr as $k => $v){
            $value         = explode('_' , $v);
            $attr_id       = array_shift($value);
            $attr_value_id = implode(',' , $value);
            $attr_where = $attr_where ."{$table_name[$k]}.attr_id=$attr_id and {$table_name[$k]}.attr_value_id in ($attr_value_id) and ";
            if($join == ''){
                $join = 'tp_sku_value as a';
            }else{
            	$join = $join . " left join tp_sku_value as {$table_name[$k]} on a.sku_id={$table_name[$k]}.sku_id";
            }                
        }
        $attr_where = substr($attr_where , 0 , -5);       
        $sql  = " SELECT a.* FROM $join
                  WHERE 
                      $attr_where
                  ";
        $data = M()->query($sql); 
        if(count($data) < 0){
            return array();
        }
        $goods_id = array_column($data , 'goods_id');
        return $goods_id;   	
    }
}