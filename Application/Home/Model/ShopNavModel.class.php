<?php
namespace Home\Model;
use Think\Model;
/**
 * 店铺导航模块业务逻辑
 * @author 幸福无期
 */
class ShopNavModel extends Model{
    protected $tableName='Shopping'; //切换检测表
    /**
     * 店铺导航修改
     * @param  array $data_ 店铺导航数据
     * @param  array $seller_id 店铺卖家id
     * @return array 返回操作结果
     */
    public function navsUpdate($data_ = array() , $seller_id){
        if(count($data_) <=0 ){
            return array(
                'status' => 1,
                'msg'    => '保存成功'
            );
        }
        $result = array(
            'status' => 1,
            'msg'   => '保存成功'
        );
        $seller_id = intval($seller_id);
        if(!$seller_id){
            return array(
                'status' => 0,
                'msg'    => '店铺id错误'
            );    
        }
        $sql_arr = array(
            'status' => " SET status = CASE id",
            'rsort'  => " rsort = CASE id"
        );
        $ids = array();
        foreach($data_ as $v){
            $r = $this->checkNav($v);
            if(!$r['status']){
                return $r;  
            }
            $ids[] = $v['id']; 
            $v['status']  = intval($v['status']) == 1 ? 1 : 0;
            $v['rsort']   = intval($v['rsort']);
            $sql_arr['status'] .= " WHEN {$v['id']} THEN '{$v['status']}'";
            $sql_arr['rsort']   .= " WHEN {$v['id']} THEN '{$v['rsort']}'";
        }
        $ids = implode(',' , $ids);
        $sql_arr['status'] .= ' END,';
        $sql_arr['rsort'] .= ' END';
        $sql = "UPDATE tp_shopping".$sql_arr['status'].$sql_arr['rsort']." where id IN ($ids)";
        $r   = M()->execute($sql);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'   => '保存失败'
            );
        }
        return $result;
        
    }

    /**
     * 店铺导航名字修改
     * @param  array $data_ 店铺导航数据
     * @param  array $seller_id 店铺卖家id
     * @return array 返回操作结果
     */
    public function navsNameUpdate($data_ = array() , $seller_id){
        if(count($data_) <=0 ){
            return array(
                'status' => 1,
                'msg'    => '保存成功'
            );
        }
        $result = array(
            'status' => 1,
            'msg'   => '保存成功'
        );
        $seller_id = intval($seller_id);
        if(!$seller_id){
            return array(
                'status' => 0,
                'msg'    => '店铺id错误'
            );
        }
        $sql_arr = array(
            'nav_name' => " SET nav_name = CASE cat_id"
        );
        $ids = array();
        foreach($data_ as $v){
            $ids[] = $v['cat_id'];
            $sql_arr['nav_name'] .= " WHEN {$v['cat_id']} THEN '{$v['nav_name']}'";
        }
        $ids = implode(',' , $ids);
        $sql_arr['nav_name'] .= ' END';
        $sql = "UPDATE tp_shopping".$sql_arr['nav_name'].$sql_arr['rsort']." where member_id=$seller_id and cat_id IN ($ids)";
        $r   = M()->execute($sql);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'   => '保存失败'
            );
        }
        return $result;   
    }
    
    /**
     * 店铺导航添加
     * @param  array $data_ 店铺导航数据
     * @param  array $seller_id 店铺卖家id
     * @return array 返回操作结果
     */
    public function navsAdd($data_ = array() , $seller_id){
        if(count($data_) <=0 ){
            return array(
                'status' => 1,
                'msg'    => '添加成功'
            );
        }
        $seller_id = intval($seller_id);
        if(!$seller_id){
            return array(
                'status' => 0,
                'msg'    => '卖家id错误'
            );
        }
        $values = array();
        $fields = array('`nav_name`','`cat_id`','`member_id`','`time`','`status`','`rsort`');
        foreach($data_ as $k => $v){
            if(!$v['nav_name']){
                return array(
                    'status' => 0,
                    'msg'    => '必须输入导航名称'
                );                
            }
            if(!intval($v['cat_id'])){
                return array(
                    'status' => 0,
                    'msg'    => '必须输入分类id'
                );
            }
            $data = array(
                'nav_name'  => $v['nav_name'],
                'cat_id'    => intval($v['cat_id']),
                'member_id' => $seller_id,
                'time'      => time(),
                'status'    => intval($v['status']),
                'rsort'     => intval($v['rsort'])
            );
            $values[]      = "('" . implode("','",$data) . "')";
        }
        $sql = "INSERT INTO `tp_shopping` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
        $r   = M()->execute($sql);
        if($r === false){
            return array(
                'status' => 0,
                'msg'    => '添加失败'
            );
        }else{
            return  array(
                'status' => 1,
                'msg'   => '添加成功'
            );
        }
    
    }
    
    /**
     * 检测店铺导航数据合法性
     * @param  array $data 店铺导航数据
     * @return array 返回操作结果
     */    
    protected function checkNav($data){
        $model = D("Shopping");
        $rules = array(
            array('id','/^[1-9]\d*$/','导航id错误'),
        );
        if($model->validate($rules)->create($model) === false){
            $result = array(
                'status' => 0,
                'msg'    => $model->getError()
            );
            return $result;
        }  
        return array('status'=>1);
    }
    
    /**
     * 店铺导航删除
     * @param  int   $cat_id 店铺分类id
     * @param  array $seller_id 店铺卖家id
     * @return array 返回操作结果
     */
    public function navDelete($cat_id , $seller_id){
        $cat_id = intval($cat_id);
        $r = M('Shopping')->where(array('member_id'=>$seller_id,'cat_id'=>$cat_id))->delete();
        if($r === false){
            return array(
                'status' => 0,
                'msg'    => '删除失败'
            );
        }else{
            return  array(
                'status' => 1,
                'msg'   => '删除成功'
            );
        }
    }
}