<?php
namespace Home\Model;
use Think\Model;
/**
 * 商城商品购物车模块业务逻辑
 * @author 幸福无期
 */
class ShopCategoryModel extends Model{
    protected $tableName='Shop_category'; //切换检测表
    /**
     * 店铺导航修改
     * @param  array $data_ 店铺导航数据
     * @param  array $seller_id 店铺卖家id
     * @return array 返回操作结果
     */
    public function categorysUpdate($data_ = array() , $seller_id){
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
                'msg'    => '卖家id错误'
            );    
        }
        $sql_arr = array(
            'name'   => " SET name = CASE id",
            'status' => " status = CASE id",
            'sort'   => " sort = CASE id"
        );
        $ids = array();
        foreach($data_ as $v){
            $r = $this->checkCategory($v);
            if(!$r['status']){
                return $r;  
            }
            $ids[] = $v['id']; 
            $v['status'] = intval($v['status']) == 1 ? 1 : 0;
            $v['sort']   = intval($v['sort']);
            $sql_arr['name']   .= " WHEN {$v['id']} THEN '{$v['name']}'";
            $sql_arr['status'] .= " WHEN {$v['id']} THEN '{$v['status']}'";
            $sql_arr['sort']   .= " WHEN {$v['id']} THEN '{$v['sort']}'";
        }
        $ids = implode(',' , $ids);
        $sql_arr['name'] .= ' END,';
        $sql_arr['status'] .= ' END,';
        $sql_arr['sort'] .= ' END';
        $sql = "UPDATE tp_shop_category".$sql_arr['name'].$sql_arr['status'].$sql_arr['sort']." where id IN ($ids)";
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
    public function categorysAdd($data_ = array() , $seller_id){
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
        $fields = array('`name`','`member_id`','`time`','`status`','`pid`','`sort`');
        foreach($data_ as $k => $v){
            if(!$v['name']){
                return array(
                    'status' => 0,
                    'msg'    => '必须输入分类名称'
                );                
            }
            if(!intval($v['pid'])){
                return array(
                    'status' => 0,
                    'msg'    => '必须上级分类'
                );
            }
            $data = array(
                'name'      => $v['name'],
                'member_id' => $seller_id,
                'time'      => time(),
                'status'    => 1,
                'pid'       => intval($v['pid']),
                'sort'      => intval($v['sort'])
            );
            $values[]      = "('" . implode("','",$data) . "')";
        }
        $sql = "INSERT INTO `tp_shop_category` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
        $r   = M()->execute($sql);
        if($r === false){
            return array(
                'status' => 1,
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
     * 店铺分类删除
     * @param  int   $id 店铺分类id
     * @param  array $seller_id 店铺卖家id
     * @return array 返回操作结果
     */
    public function categorysDelete($id , $seller_id){
        $r = M('Shop_category')->where(array('pid'=>$id,'member_id'=>$seller_id))->getField('id');
        if($r){
            return array(
                'status' => 0,
                'msg'    => '请先删除子类'
            );
        }
        $r = M('Shop_category')->where(array('id'=>$id,'member_id'=>$seller_id))->delete();
        if($r === false){
            return array(
                'status' => 0,
                'msg'    => '删除失败'
            );
        }else{
            M('Shopping')->where(array('cat_id'=>$id,'member_id'=>$seller_id))->delete();
            return array(
                'status' => 0,
                'msg'    => '删除成功'
            );            
        }
    }
    
    /**
     * 检测店铺导航数据合法性
     * @param  array $data 店铺导航数据
     * @return array 返回操作结果
     */    
    protected function checkCategory($data){
        $model = D("Shop_category");
        $rules = array(
            array('id','/^[1-9]\d*$/','id错误'),
            array('name','require','必须输入分类名',self::EXISTS_VALIDATE)
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
}