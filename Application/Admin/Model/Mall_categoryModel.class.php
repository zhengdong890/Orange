<?php
/**
 * 商城商品分类业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class Mall_categoryModel extends Model{
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
   * 添加商城商品分类
   * @access public
   * @param  array $data   商城商品分类数据 一维数组    
   * @return array $result 执行结果
   */ 
  public function categoryAdd($data_){ 
  	    $result = array(
            'status' => 1,
            'msg'    => '数据添加成功'
        ); 
        $data = array(
            'pid'               => $data_['pid'],
            'cat_name'          => $data_['cat_name'],
            'cat_remark'        => $data_['cat_remark'],
            'level'             => intval($data_['level']),
            'status'            => intval($data_['status']),
            'sort'              => intval($data_['sort']),
            'router'            => $data_['router'],
            'cat_thumb'         => isset($data_['cat_thumb'])? $data_['cat_thumb'] : '',
            'cat_thumb_1'       => isset($data_['cat_thumb_1'])? $data_['cat_thumb_1'] : '',
            'index_thumb'       => isset($data_['index_thumb'])? $data_['index_thumb'] : ''
        );    
        /*验证数据*/
        $model  = D('Mall_category');
        $rules  = array(
        	  array('pid','/^([1-9]\d*)|0+$/','请选择上级分类'),
            array('cat_name','require','请输入商品分类名称')
        );
        if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
        } 
        $id = M('Mall_category')->add($data);
        if($id === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }else{
        	$this->categoryBrand($id , $data_['brand_id']);//商品分类品牌处理
        }
        return $result;
  }

  /**
   * 修改商城商品分类
   * @access public
   * @param  array $data   商城商品分类数据 一维数组    
   * @return array $result 执行结果
   */ 
  public function categoryUpdate($data_){ 
  	    $result = array(
            'status' => 1,
            'msg'    => '数据修改成功'
        );         
        $data = array(
        	'id'          => $data['id'],  
            'pid'         => $data_['pid'],
            'type_id'     => $data_['type_id'],
            'cat_name'    => $data_['cat_name'],
            'cat_remark'  => $data_['cat_remark'],
            'status'      => intval($data_['status']),
            'level'       => intval($data_['level']),
            'router'      => $data_['router'],
            'sort'        => intval($data_['sort'])
        );    
        if($data_['filter_extendattr']){
        	$data['filter_extendattr'] = $data_['filter_extendattr'];
        }
        if($data_['cat_thumb']){
        	$data['cat_thumb'] = $data_['cat_thumb'];
        	$old_thumb = M('Mall_category')->where(array('id'=>$data_['id']))->getField('cat_thumb');
        }
        if($data_['cat_thumb_1']){
        	$data['cat_thumb_1'] = $data_['cat_thumb_1'];
        	$old_thumb_1 = M('Mall_category')->where(array('id'=>$data_['id']))->getField('cat_thumb_1');
        }
        if($data_['index_thumb']){
        	$data['index_thumb'] = $data_['index_thumb'];
        	$old_index_thumb = M('Mall_category')->where(array('id'=>$data_['id']))->getField('index_thumb');
        }
        /*验证数据*/
        $model  = D('Mall_category');
        $rules  = array(
        	array('id','/^[1-9]\d*+$/','请选择商品分类id'),
        	array('pid','/^([1-9]\d*)|0+$/','请选择上级分类'),
            array('cat_name','require','请输入商品分类名称')
        );
        if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
        } 
        $this->categoryBrand($data_['id'] , $data_['brand_id'] , 2);//商品分类品牌处理
        unset($data['id']);
        $r = M('Mall_category')->where(array('id'=>$data_['id']))->save($data);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }else{
            if($data_['cat_thumb']){
        	    unlink($old_thumb);
            }
            if($data_['cat_thumb_1']){
        	    unlink($old_thumb_1);
            }
            if($data_['index_thumb']){
        	    unlink($old_index_thumb);
            }
        }
        return $result;
  }

  /**
   * 删除商品分类
   * @access public  
   * @param  int   $id   商商品分类id
   * @return array $categorys 执行结果
   */ 
  public function categoryDelete($id){
      $data = M('Mall_category')->select();
   	  $a    = getTree($data,$id);
   	  if(!empty($a)){//判断该分类是不是最底层分类
          return array(
              'status' => 0,
              'msg'    => '该商品分类不是最底层分类,无法删除'
          );
   	  }else{
  	  	 $a = M('Mall_goods')->where(array('cat_id'=>$id))->find();
  	  	 if(!empty($a)){//判断该分类下有木有商品
            return array(
                'status' => 0,
                'msg'    => '该商品分类下有产品,无法删除'
            );
  	  	 }else{
  	  	 	$oldthumb = M('Mall_category')->where(array('id'=>$id))->getField('cat_thumb');//获取旧图
  	  	 	$a=M('Mall_category')->where(array('id'=>$id))->delete();//删除该商品分类
  	  	 	if($a){
  	  	 		//删除分类下的所属品牌
  	  	 		D('MallCategoryBrand')->categoryBrandDelete(array('cat_id'=>$id));
  	  	 		unlink($oldthumb);//删除旧图
  	  	 		return array(
	                'status' => 1,
	                'msg'    => '删除成功'
                );
  	  	 	}else{
	            return array(
	                'status' => 0,
	                'msg'    => '删除失败'
	            );
  	  	 	}   	  	  	 	
  	  	 }   	  	  	 
   	 }
   	 return $result;
  }

  /**
   * 商品分类品牌处理
   * @access public 
   * @param  int   $id        商品分类id 
   * @param  array $data      商品分类品牌数据
   * @param  int   $type      1添加 2修改  
   * @return array $categorys 执行结果
   */ 
  public function categoryBrand($id , $data ,$type = 1){
      if(!is_array($data) || count($data) <= 0){
      	  return false;
      }
      if($type == 2){
      	  $r = M('Mall_category_brand')->where(array('cat_id'=>$id))->delete();
      }     
      $fields = array('`brand_id`' , '`cat_id`');
      $values = array();
      foreach($data as $k => $v){
      	  $arr['brand_id'] = $v;
          $arr['cat_id']   = $id;
          $values[]        = "('" . implode("','",$arr) . "')";
      }
      $sql = "INSERT INTO `tp_mall_category_brand` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
      $r   = M()->execute($sql);   
      if($r === false){
      	 return false;
      }
      return true;
  }

  /**
   * 获取商城商品分类
   * @access public  
   * @return array $categorys 执行结果
   */ 
  public function getCategory($order = 'sort'){
      $categorys = M('Mall_category')
                 ->order($order)
                 ->field('id,pid,cat_name,sort,status')
                 ->select();
      return $categorys;
  } 

   /**
    * 获取下级分类
    * @access public  
    * @return array $categorys 执行结果
    */ 
    public function getNextCategory($cat_id = 0 , $order = 'sort'){
        $categorys = M('Mall_category')
	               ->where(array('pid'=>$cat_id))
	               ->order($order)
	               ->field('id,pid,cat_name,sort,status')
	               ->select();
	    return $categorys;
    } 

    /**
     * 商品分类单位编辑
     * @access public  
     * @return array $result 执行结果     
     */
    public function unitUpdate($data){
        $data = array(
            'id'   => $data['id'],
            'unit' => $data['unit']
        );
        $r = M('Mall_category')->save($data);
        if($r === false){
            return array(
                'status' => 0,
                'msg'    => '保存失败'
            );
        }
        return array(
            'status' => 0,
            'msg'    => '保存成功'
        );   
    }  

    /**
     * 商品分类单位编辑
     * @access public  
     * @return array $result 执行结果     
     */
    public function unitUnitUpdate($data){
        $data = array(
            'id'        => $data['id'],
            'unit_unit' => $data['unit_unit']
        );
        $r = M('Mall_category')->save($data);
        if($r === false){
            return array(
                'status' => 0,
                'msg'    => '保存失败'
            );
        }
        return array(
            'status' => 1,
            'msg'    => '保存成功'
        );   
    }         
}