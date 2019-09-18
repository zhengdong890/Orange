<?php
/**
 * 团购模块业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class GroupBuyModel extends Model{
  protected $tableName='group_goods'; //关闭检测字段

  /**
   * 团购商品编辑
   * @access public
   * @param  array $data   团购信息 一维数组    
   * @return array $result 执行结果
   */ 
  public function groupBuyUpdate($data_){     
        $result = array(
            'status' => 1,
            'msg'    => '数据修改成功'
        );  
        $data = array(           
            'id' => intval($data_['id'])                        
        );
        if(!$data['id']){
            return array(
                'status' => 0,
                'msg'    => '商品团购id错误'                
            );
        }
        if($data_['img']){
            $data['img'] = $data_['img'];   
        }
        if($data_['img_1']){
            $data['img_1'] = $data_['img_1'];
        }
        if($data_['img_2']){
            $data['img_2'] = $data_['img_2'];
        }  
        if(count($data) <= 0){
            return array(
                'status' => 1,
                'msg'    => '数据修改成功'
            );            
        }
        $r = M('Group_goods')->save($data);
        if($r === false){
            return array(
                'status' => 0,
                'msg'    => '数据修改失败'
            ); 
        }else{
            return array(
                'status' => 1,
                'msg'    => '编辑成功'
            );
        }
  }

  /**
   * 团购申请审核
   * @access public
   * @param  array $data   审核信息 一维数组    
   * @return array $result 执行结果
   */ 
  public function groupBuyCheck($data){       
        $save_data = array(
            'id'            => intval($data['id']),    
            'check_status'  => intval($data['check_status']) == 1 ? 1 : 0,
            'is_check'      => 1,
            'check_content' => $data['check_content'],
            'check_time'    => time()
        );
        if(!$save_data['id']){
            return array(
                'status' => 0,
                'msg'    => 'id错误'
            );
        }
        $r = M('Group_goods')->save($save_data);
        if($r === false){
            return array(
                'status' => 0,
                'msg'    => '审核失败'
            ); 
        }else{
            if($save_data['check_status']){
                $goods_id = M('Group_goods')->where(array('id'=>$save_data['id']))->getField('goods_id');
                M('Mall_goods')->where(array('id'=>$goods_id))->save(array('group_id'=>$save_data['id']));   
            }
            return array(
                'status' => 1,
                'msg'    => '审核成功'
            );
        }
  }  
}