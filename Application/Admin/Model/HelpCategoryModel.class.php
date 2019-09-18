<?php
/**
 * 帮助分类业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class HelpCategoryModel extends Model{
  protected $tableName='Help_category'; //关闭检测字段
  /**
   * 添加帮助分类
   * @access public
   * @param  array $data  帮助分类数据  一维数组    
   * @return array $result 执行结果
   */ 
  public function categoryAdd($data_){ 
  	    $result = array(
            'status' => 1,
            'msg'    => '数据添加成功'
        ); 
        $data = array(
            'pid'    => $data_['pid'],
            'name'   => $data_['name'],
            'status' => intval($data_['status']),
            'sort'   => intval($data_['sort']),
            'thumb'  => isset($data_['thumb'])? $data_['thumb'] : '',
            'content'=> $data_['content'],
            'time'   => time()
        );    
        /*验证数据*/
        $model  = D('Help_category');
        $rules  = array(
        	array('pid','/^([1-9]\d*)|0+$/','请选择上级分类'),
            array('name','require','请输入分类名称',self::EXISTS_VALIDATE)
        );
        if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
        } 
        $id = M('Help_category')->add($data);
        if($id === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }
        return $result;
  }

  /**
   * 修改帮助分类
   * @access public
   * @param  array $data   帮助分类数据 一维数组    
   * @return array $result 执行结果
   */ 
  public function categoryUpdate($data_){ 
  	    $result = array(
            'status' => 1,
            'msg'    => '数据修改成功'
        ); 
        $data = array(
        	'id'      => $data['id'],  
            'pid'     => $data_['pid'],
            'name'    => $data_['name']?$data_['name']:'',
            'status'  => intval($data_['status']),
            'sort'    => intval($data_['sort']),
            'content'=> $data_['content'],
            'time'    => time()
        );    
        if($data_['thumb']){
        	$data['thumb'] = $data_['thumb'];
        	$old_thumb = M('Help_category')->where(array('id'=>$data_['id']))->getField('thumb');
        }
        /*验证数据*/
        $model  = D('Help_category');
        $rules  = array(
        	array('id','/^[1-9]\d*+$/','请选择分类id'),
        	array('pid','/^([1-9]\d*)|0+$/','请选择上级分类'),
            array('name','require','请输入分类名称',self::EXISTS_VALIDATE)
        );
        if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
        } 
        unset($data['id']);
        $r = M('Help_category')->where(array('id'=>$data_['id']))->save($data);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }else{
            if($data_['thumb']){
        	    unlink($old_thumb);
            }
        }
        return $result;
  }

  /**
   * 删除帮助分类
   * @access public  
   * @param  int   $id   帮助分类id
   * @return array $categorys 执行结果
   */ 
  public function categoryDelete($id){
      $data = M('Help_category')->select();
   	  $a    = getTree($data,$id);
   	  if(!empty($a)){//判断该分类是不是最底层分类
   	  	  $result ='该分类不是最底层分类,无法删除';
   	  }else{
  	  	 	$a=M('Help_category')->where(array('id'=>$id))->delete();//删除该商品分类
  	  	 	if($a){
  	  	 		$result = '删除成功';
  	  	 	}else{
  	  	 		$result = '删除失败';
  	  	 	}   	  	  	 	 	  	  	 
   	 }
   	 return $result;
  }    
}