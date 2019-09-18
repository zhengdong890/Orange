<?php
/**
 * 商城商品加入推荐业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class Mall_goods_modelModel extends Model{
  /**
   * 添加商城商品加入推荐
   * @access public
   * @param  array $data   商城商品加入推荐数据 一维数组    
   * @return array $result 执行结果
   */ 
  public function modelAdd($data_){ 
  	    $result = array(
            'status' => 1,
            'msg'    => '数据添加成功'
        ); 
        $data = array(
            'name'   => $data_['name'],
            'status' => intval($data_['status']),
            'sort'   => intval($data_['sort'])
        );    
        /*验证数据*/
        $model  = D('Mall_goods_model');
        $rules  = array(
            array('name','require','请输入模块名称')
        );
        if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
        } 
        $id = M('Mall_goods_model')->add($data);
        if($id === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }
        return $result;
  }

  /**
   * 修改商城商品加入推荐
   * @access public
   * @param  array $data   商城商品加入推荐数据 一维数组    
   * @return array $result 执行结果
   */ 
  public function modelUpdate($data_){ 
        $result = array(
            'status' => 1,
            'msg'    => '数据修改成功'
        ); 
        $data = array(
            'id'     => $data_['id'],
            'name'   => $data_['name'],
            'status' => intval($data_['status']),
            'sort'   => intval($data_['sort'])
        );    
        /*验证数据*/
        $model  = D('Mall_goods_model');
        $rules  = array(
            array('name','require','请输入模块名称')
        );
        if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
        } 
        $id = $data['id'];unset($data['id']);
        $r  = M('Mall_goods_model')->where(array('id'=>$id))->save($data);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据修改失败'
            );
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
   	  	  $result ='该商品分类不是最底层分类,无法删除';
   	  }else{
  	  	 $a = M('Mall_goods')->where(array('cat_id'=>$id))->find();
  	  	 if($a){//判断该分类下有木有商品
  	  	 	$result = '该商品分类下有产品,无法删除';
  	  	 }else{
  	  	 	$oldthumb = M('Mall_category')->where(array('id'=>$id))->getField('cat_thumb');//获取旧图
  	  	 	$a=M('Mall_category')->where(array('id'=>$id))->delete();//删除该商品分类
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
}