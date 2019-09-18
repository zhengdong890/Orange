<?php
namespace Shop\Model;
use Think\Model;
/**
 * 店铺模块业务逻辑
 * @author 幸福无期
 */
class ShopModel extends Model{  
    protected $tableName='Shop_data'; //切换检测表
  /**
    * 修改店铺信息
    * @param  array data需要修改的数据 
    * @return array 返回结果
    */
    public function shopSetting($data = array()){
        $result    = array('status'=>1,'msg'=>'保存成功');
        $shop_data = $data;
   	    /*数据验证*/
   	    $model = D("Shop_data");
        $rules = array(
              array('member_id','/^[1-9]\d*$/','商家id错误'),
              array('shop_name','require','必须输入店铺名',self::EXISTS_VALIDATE),
              array('area','/^[1-9]\d*$/','请选择区域')
        );
        if($model->validate($rules)->create($shop_data) === false){
            $result = array(
               'status' => 0,
               'msg'    => $model->getError()
            );
            return $result;
        }
        $member_id = $shop_data['member_id'];
        unset($shop_data['member_id']);
        $r = M('Shop_data')->where(array('member_id'=>$member_id))->save($shop_data); 
        if($r === false){
            $result    = array('status'=>0,'msg'=>'更新失败');
        }
        return $result;
   }   

  /**
    * 修改店铺页面信息
    * @param  array data需要修改的数据 
    * @return array 返回结果
    */
    public function shopDataUpdate($data = array()){
        $result    = array('status'=>1,'msg'=>'保存成功');
        $shop_data = $data;
        /*数据验证*/
        $model = D("Shop_data");
        $rules = array(
              array('member_id','/^[1-9]\d*$/','商家id错误'),
              array('shop_name','require','必须输入店铺名',self::EXISTS_VALIDATE)
        );
        if($model->validate($rules)->create($shop_data) === false){
            $result = array(
               'status' => 0,
               'msg'    => $model->getError()
            );
            return $result;
        }
        $member_id = $shop_data['member_id'];
        unset($shop_data['member_id']);
        $r = M('Shop_data')->where(array('member_id'=>$member_id))->save($shop_data); 
        if($r === false){
            $result    = array('status'=>0,'msg'=>'更新失败');
        }
        return $result;
   }

  /**
    * 增加店铺导航信息
    * @param  array data需要增加的数据 
    * @return array 返回结果
    */
    public function shopNavAdd($member_id , $data = array()){
        if(count($data) <= 0){
            return array(
               'status' => 0,
               'msg'    => '数据为空'
            );            
        }
        if(!preg_match('/^[1-9]\d*$/', $member_id)){
            return array(
               'status' => 0,
               'msg'    => '商家id错误'
            );
        }
        $result    = array('status'=>1,'msg'=>'添加成功');
        $nav_data  = $data;
        /*数据验证*/
        $model = D("Shop_nav");
        $rules = array(
            array('name','require','必须输入导航名称',self::EXISTS_VALIDATE),
            array('link','require','必须输入链接地址',self::EXISTS_VALIDATE)
        );
        foreach($nav_data as $k => $v){
            if($model->validate($rules)->create($nav_data) === false){
                $result = array(
                   'status' => 0,
                   'msg'    => $goods->getError()
                );
                return $result;
            }        
        }        
        $values = array();
        $fields = array('`name`','`link`','`member_id`');
        foreach($nav_data as $k => $v){
           $v['member_id'] = $member_id;
           $values[]       = "('" . implode("','",$v) . "')";
        }
        $sql = "INSERT INTO `tp_shop_nav` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
        $r   = M()->execute($sql); 
        if($r === false){
            $result = array(
               'status' => 0,
                'msg'   => '添加失败' 
            );
        } 
        return $result;
   }

  /**
    * 修改店铺导航信息
    * @param  array data需要修改的数据 
    * @return array 返回结果
    */
    public function shopNavUpdate($member_id , $data = array()){
        if(count($data) <= 0){
            return array(
               'status' => 0,
               'msg'    => '数据为空'
            );            
        }
        if(!preg_match('/^[1-9]\d*$/', $member_id)){
            return array(
               'status' => 0,
               'msg'    => '商家id错误'
            );
        }
        $result     = array('status'=>1,'msg'=>'修改成功');
        $save_data  = $data;
        /*数据验证*/
        $model = D("Shop_nav");
        $rules = array(
            array('cat_id','/^[1-9]\d*$/','请选择id')
        );
        $sql_arr = array(
            'name'   => " SET name = CASE cat_id",
            'sort'   => " sort = CASE cat_id",
            'status' => " status = CASE cat_id"
        );  
        $ids = array();      
        foreach($save_data as $k => $v){
            if($model->validate($rules)->create($save_data) === false){
                $result = array(
                   'status' => 0,
                   'msg'    => $model->getError()
                );
                return $result;
            }
            $ids[] = $v['cat_id'];
            $sql_arr['name'] .= " WHEN {$v['cat_id']} THEN '{$v['name']}'";
            $sql_arr['sort'] .= " WHEN {$v['cat_id']} THEN '{$v['sort']}'"; 
            $v['status'] = $v['status']?$v['status']:0;
            $sql_arr['status'] .= " WHEN {$v['cat_id']} THEN '{$v['status']}'";         
        }  
        $ids = implode(',' , $ids); 
        $sql_arr['name'] .= ' END,';
        $sql_arr['sort'] .= ' END,';
        $sql_arr['status'] .= ' END';
        $sql = "UPDATE tp_shop_nav".$sql_arr['name'].$sql_arr['sort'].$sql_arr['status']." WHERE member_id ={$member_id} and cat_id IN ($ids)";
        $r   = M()->execute($sql); 
        if($r === false){
            $result = array(
               'status' => 0,
                'msg'   => '添加失败' 
            );
        } 
        return $result;
    }  

  /**
    * 修改店铺导航样式
    * @param  int   $member_id 商家id    
    * @param  array data需要修改的数据 
    * @return array 返回结果
    */
    public function NavCssUpdate($member_id , $data = array()){
        if(!preg_match('/^[1-9]\d*$/', $member_id)){
            return array(
               'status' => 0,
               'msg'    => '商家id错误'
            );
        }
        $result    = array('status'=>1,'msg'=>'保存成功');
        $r = M('Shop_nav_css')->where(array('member_id'=>$member_id))->save($data); 
        if($r === false){
            $result = array('status'=>0,'msg'=>'更新失败');
        }
        return $result;
   }         
}