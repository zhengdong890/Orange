<?php
/**
 * 导航业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class NavModel extends Model{
  /**
   * 添加导航
   * @access public
   * @param  array $data   导航数据
   * @return array $result 执行结果
   */ 
  public function navAdd($data_){ 
  	    $result = array(
            'status' => 1,
            'msg'    => '数据添加成功'
        ); 
        $data = array(
            'name'   => $data_['name'],
            'url'    => $data_['url'],
            'status' => intval($data_['status']),
            'sort'   => intval($data_['sort'])
        );    
        /*验证数据*/
        $model  = D('Nav');
        $rules  = array(
            array('name','require','请输入导航名称',self::EXISTS_VALIDATE),
            array('url','require','请输入导航地址',self::EXISTS_VALIDATE)
        );
        if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
        } 
        $id = M('Nav')->add($data);
        if($id === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }
        return $result;
  }

  /**
   * 修改导航
   * @access public
   * @param  array $data   导航数据
   * @return array $result 执行结果
   */ 
  public function navUpdate($data_){ 
  	    $result = array(
            'status' => 1,
            'msg'    => '数据修改成功'
        ); 
        $data = array(
            'name'   => $data_['name'],
            'url'    => $data_['url'],
            'status' => intval($data_['status']),
            'sort'   => intval($data_['sort'])
        );    
        /*验证数据*/
        $model  = D('Nav');
        $rules  = array(
            array('id','/^[1-9]\d*+$/','请选择导航id'),
            array('name','require','请输入导航名称',self::EXISTS_VALIDATE),
            array('url','require','请输入导航地址',self::EXISTS_VALIDATE)
        );
        if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
        } 
        unset($data['id']);
        $r = M('Nav')->where(array('id'=>$data_['id']))->save($data);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }
        return $result;
  }

  /**
   * 删除导航
   * @access public  
   * @param  int $id   导航id
   * @return array  执行结果
   */ 
  public function navDelete($id){
      $result = array(
          'status' => 0,
          'msg'    => '删除成功'
      );
      $id = intval($id);
      if(!$id){
          return array(
              'status' => 0,
              'msg'    => 'id错误'
          );
      }
  	  $r = M('Nav')->where(array('id'=>$id))->delete();//删除该商品分类
  	  if($r === false){
  	      $result = array(
  	          'status' => 0,
  	          'msg'    => '删除失败'
  	      );
  	  }
   	  return $result;
  } 

    public function navSeoUpdate($data_){
        $result = array(
            'status' => 1,
            'msg'    => '修改成功'
        );
        $data = array(
            'id'          => intval($data_['id']),
            'title'       => $data_['title'],
            'keyword'     => $data_['keyword'],
            'description' => $data_['description']
        );
        $model  = D('Nav');
        $rules  = array(
            array('id','/^([1-9]\d*)|0+$/','id不能为空'),
            array('title','require','必须输入描述',self::EXISTS_VALIDATE),
            array('keyword','require','必须输入关键字',self::EXISTS_VALIDATE),
            array('description','require','必须输入描述',self::EXISTS_VALIDATE)
        );
        if($model->validate($rules)->create($data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $model->getError()
            );
            return $result;
        }     
        $r = M('Nav')->save($data);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '修改失败'
            );
        }
        return $result;
    }   
}