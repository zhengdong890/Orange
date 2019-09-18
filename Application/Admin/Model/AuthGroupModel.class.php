<?php
/**
 * 权限规则分组管理逻辑
* @author 幸福无期
* @email  597089187@qq.com
*/

namespace Admin\Model;
use Think\Model;
class AuthGroupModel extends Model{
    protected $tableName='Auth_group';

    /**
     * 添加分组
     * @access public
     * @param  array $data_  分组数据
     * @return array $result 执行结果
    */
    public function ruleGroupAdd($data_){
        $data = array(
            'title'  => $data_['title'] ? $data_['title'] : '',
            'status' => intval($data_['status']) == 0 ? 0 : 1
        );
        if(!$data['title']){
            return array(
                'status' => 0,
                'msg'    => '请输入分组名称'
            );    
        }
        $id=M('Auth_group')->add($data);
        if($id !== false){
            return(array('status'=>'1','msg'=>'ok','id'=>$id));
        }else{
            return(array('status'=>'0','msg'=>'操作失败'));
        }
    }
    
    /**
     * 修改分组
     * @access public
     * @param  array $data_  分组数据
     * @return array $result 执行结果
     */
    public function ruleGroupUpdate($data_){
        $id   = intval($data_['id']);
        if(!$id){
            return array(
                'status' => 0,
                'msg'    => '请输入分组id'
            );
        }
        $data = array(
            'title'  => $data_['title'] ? $data_['title'] : '',
            'status' => intval($data_['status']) == 0 ? 0 : 1
        );
        if(!$data['title']){
            return array(
                'status' => 0,
                'msg'    => '请输入分组名称'
            );
        }
        $r = M('Auth_group')->where(array('id'=>$id))->save($data);
        if($r !== false){
            return(array('status'=>'1','msg'=>'ok'));
        }else{
            return(array('status'=>'0','msg'=>'操作失败'));
        }
    }
}

?>