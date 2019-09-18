<?php
/**
 * 商城商品分类业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class CategoryModel extends Model{
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
            'type_id'           => $data_['type_id'],
            'cat_name'          => $data_['cat_name'],
            'cat_remark'        => $data_['cat_remark'],
            'filter_extendattr' => $data_['filter_extendattr'],
            'status'            => intval($data_['status']),
            'sort'              => intval($data_['sort']),
            'router'            => $data_['router'],
            'cat_thumb'         => isset($data_['cat_thumb'])? $data_['cat_thumb'] : '',
            'index_thumb'       => isset($data_['index_thumb'])? $data_['index_thumb'] : ''
        );    
        /*验证数据*/
        $model  = D('Category');
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
        $id = M('Category')->add($data);
        if($id === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }else{
        	$this->categoryBrand($data_['id'] , $data_['brand_id']);//商品分类品牌处理
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
            'router'      => $data_['router'],
            'sort'        => intval($data_['sort'])
        );    
        if($data_['filter_extendattr']){
        	$data['filter_extendattr'] = $data_['filter_extendattr'];
        }
        if($data_['cat_thumb']){
        	$data['cat_thumb'] = $data_['cat_thumb'];
        	$old_thumb = M('Category')->where(array('id'=>$data_['id']))->getField('cat_thumb');
        }
        if($data_['index_thumb']){
            $data['index_thumb'] = $data_['index_thumb'];
            $old_thumb = M('Category')->where(array('id'=>$data_['id']))->getField('index_thumb');
        }
        /*验证数据*/
        $model  = D('Category');
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
        $r = M('Category')->where(array('id'=>$data_['id']))->save($data);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }else{
            if($data_['cat_thumb']){
        	    unlink($old_thumb);
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
      $data = M('Category')->select();
   	  $a    = getTree($data,$id);
   	  if(!empty($a)){//判断该分类是不是最底层分类
   	  	  $result ='该商品分类不是最底层分类,无法删除';
   	  }else{
  	  	 $a = M('Goods')->where(array('cat_id'=>$id))->find();
  	  	 if($a){//判断该分类下有木有商品
  	  	 	$result = '该商品分类下有产品,无法删除';
  	  	 }else{
  	  	 	$oldthumb = M('Category')->where(array('id'=>$id))->getField('cat_thumb');//获取旧图
  	  	 	$a=M('Category')->where(array('id'=>$id))->delete();//删除该商品分类
  	  	 	if($a){
  	  	 		unlink($oldthumb);//删除旧图
  	  	 		$result = '删除成功';
  	  	 	}else{
  	  	 		$result = '删除失败';
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
      	  $r = M('Category_brand')->where(array('cat_id'=>$id))->delete();
      }     
      $fields = array('`brand_id`' , '`cat_id`');
      $values = array();
      foreach($data as $k => $v){
      	  $arr['brand_id'] = $v;
          $arr['cat_id']   = $id;
          $values[]        = "('" . implode("','",$arr) . "')";
      }
      $sql = "INSERT INTO `tp_category_brand` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
      $r   = M()->execute($sql);   
      if($r === false){
      	 return false;
      }
      return true;
  }

  /**
   * 获取商品分类
   * @access public  
   * @return array $categorys 执行结果
   */ 
  public function getCategory($order = 'sort'){
      $categorys = M('Category')
                 ->order($order)
                 ->select();
      return $categorys;
  }    
}