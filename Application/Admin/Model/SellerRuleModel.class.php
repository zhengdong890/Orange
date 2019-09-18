<?php
/**
 * 商家规则业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class SellerRuleModel extends Model{
  protected $tableName='Seller_rule'; //关闭检测字段
  /**
   * 添加商家规则
   * @access public
   * @param  array $data  商家规则数据  一维数组    
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
            'content'=> $data_['content'],
            'time'   => time()
        );    
        /*验证数据*/
        $model  = D('Seller_rule');
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
        $id = M('Seller_rule')->add($data);
        if($id === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }
        return $result;
  }

  /**
   * 修改商家规则
   * @access public
   * @param  array $data   商家规则数据 一维数组    
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
        /*验证数据*/
        $model  = D('Seller_rule');
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
        $r = M('Seller_rule')->where(array('id'=>$data_['id']))->save($data);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }
        return $result;
  }

  /**
   * 删除商家规则
   * @access public  
   * @param  int   $id   商家规则id
   * @return array $categorys 执行结果
   */ 
  public function categoryDelete($id){
      $data = M('Seller_rule')->select();
   	  $a    = getTree($data,$id);
   	  if(!empty($a)){//判断该分类是不是最底层分类
   	  	  $result ='该分类不是最底层分类,无法删除';
   	  }else{
  	  	 	$a=M('Seller_rule')->where(array('id'=>$id))->delete();//删除该商品分类
  	  	 	if($a){
  	  	 		$result = '删除成功';
  	  	 	}else{
  	  	 		$result = '删除失败';
  	  	 	}   	  	  	 	 	  	  	 
   	 }
   	 return $result;
  }    
}