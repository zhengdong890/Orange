<?php
namespace Home\Model;
use Think\Model;
/**
 * 商城商品分类业务逻辑
 * @author 幸福无期
 */
class MallCategoryModel extends Model{  
   protected $tableName = 'Mall_category'; 

   /**
    * 获取某级分类面包屑路径
    * @access public
    * @param  int   $cat_id    当前分类id
    * @param  array $category  所有分类      
    * @return array $crumb 当前分类面包屑路径
    */ 
    public function getCrumb($cat_id , $category){
   	    $level = $category[$cat_id]['level'];
   	    $crumb = array($category[$cat_id]);
        for($i = 1 ; $i < $level ; $i++){
            $cat_id  = $category[$cat_id]['pid'];
            $crumb[] = $category[$cat_id];   
        }
        $crumb = array_reverse($crumb);
        return $crumb;
    }

   /**
    * 获取某级分类的下一级分类
    * @access public
    * @param  int   $cat_id    当前分类id
    * @param  array $category  所有分类      
    * @return array $next_cat  当前分类面包屑路径
    */ 
    public function getNextCategory($cat_id , $category){
        $next_cat = array();
        foreach($category as $v){
        	if($v['pid'] == $cat_id){
                $next_cat[] = $v; 
        	}
        }
        return $next_cat;
    }

   /**
    * 获取某级分类的最顶级分类
    * @access public
    * @param  int   $cat_id    当前分类id
    * @param  array $category  所有分类      
    * @return array $next_cat  当前分类面包屑路径
    */ 
    public function getTopCategory($cat_id , $category){ 
        $pid = $category[$cat_id]['pid'];	
    	if($pid != 0){      	    	   		
    		$cat_id = $this->getTopCategory($pid , $category); 
    		return $cat_id;   			           
    	}else{    		
    		return $cat_id;
    	}    	   
    }

   /**
    * 获取前两级分类树
    * @access public
    * @param  array $category  所有分类      
    * @return array $tree      两级分类树
    */ 
    public function getTwoTree($category){
    	$tree = array();
        foreach($category as $v){
            if($v['level'] == 1){
            	if(!$tree[$v['id']]){
                    $tree[$v['id']]	= $v;
            	}               
            }else
            if($v['level'] == 2){
                if(!$tree[$v['pid']]){
                    $tree[$v['pid']] = $category[$v['pid']];
            	} 
            	$tree[$v['pid']]['child'][] = $v;
            }
        }
        return $tree;
    }

   /**
    * 获取某级分类下面的最底级的所有分类
    * @access public
    * @param  int   $cat_id    当前分类id
    * @param  array $category  所有分类      
    * @return array $cat_ids 执行结果
    */ 
    public function getLastLevelCategory($cat_id , $category = array()){   	
    	$cat      = array($cat_id => $cat_id);
    	$next_cat = array();
    	for($i = 0 ; $i<=10 ; $i++){
    		$temp_cat = array();
	        foreach($category as $k => $v){
	            if(isset($cat[$v['pid']])){
	            	$next_cat[$v['id']] = $v;
	                $temp_cat[$v['id']] = $v;	                
	                $cat[$v['id']] = $v['id'];
	                unset($next_cat[$v['pid']]);
	                unset($category[$v['pid']]);
	            }   
	        }	         
	        if(empty($temp_cat)){
                break;
	        }   
    	}
    	return $next_cat;       
    }

   /**
    * 获取某级分类下面的所有子级
    * @access public
    * @param  int   $cat_id    当前分类id
    * @param  array $category  所有分类      
    * @return array $cat_ids 执行结果
    */ 
    public function getSonCategory($cat_id , $category = array()){
    	$level = $category[$cat_id]['level'];
        foreach($category as $k => $v){
        	if(!$category[$k]){
                continue;
            } 
            if(($v['level'] <= $level) ||($v['level'] == $level + 1 && $v['pid'] != $cat_id)){
            	unset($category[$k]);
            }else
            if($v['level'] == $level + 2){
                if(!isset($category[$v['pid']])){
                    unset($category[$k]);
                }else{
                    if($category[$v['pid']]['pid'] != $cat_id){
                    	unset($category[$k]);unset($category[$v['pid']]);
                    }
                }
            }else
            if($v['level'] == $level + 3){
                if(!isset($category[$v['pid']]) || !isset($category[$category[$v['pid']]['pid']])){
                    unset($category[$k]);
                }else{
                    if($category[$category[$v['pid']]['pid']]['pid'] != $cat_id){
                    	unset($category[$k]);
                    	unset($category[$v['pid']]);
                    	unset($category[$category[$v['pid']]['pid']]['id']);
                    }
                }
            }         
        }
        $cat_ids = array();
        //机器人 处理
        $else = array_flip(array('2','34','43','50'));
        foreach($category as $k => $v){
            if($v['level'] == 4 || isset($else[$v['id']]) || isset($else[$v['pid']])){
            	$cat_ids[] = $v;
            }
        }  
        return $cat_ids;
    }    
}