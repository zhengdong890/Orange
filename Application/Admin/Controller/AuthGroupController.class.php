<?php
/*
 * 权限规则分组 模块
 * */
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html; charset=utf-8");
class AuthGroupController extends CommonController{
    /*规则分组*/
    public function ruleGroup(){
    	if(IS_AJAX){
	        $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
	        $listRows = intval(I('listRows'))?intval(I('listRows')):10;
	        $list     = M('Auth_group')->limit($firstRow,$listRows)->select();
	        $this->ajaxReturn(array('data'=>$list,'total'=>M('Auth_group')->count()));
	    }else{
	        $this->display();
	    }
    }
    
    /*获取所有分组分组*/
    public function getGroup(){
        if(IS_AJAX){
            $list = M('Auth_group')->select();
            $this->ajaxReturn($list);
        }   
    }
    
    /*添加规则分组*/
    public function ruleGroupAdd(){
        if(IS_POST){
            $data   = I();
            $result = D('AuthGroup')->ruleGroupAdd($data);
            $this->ajaxReturn($result);
        }
    }
    
    /*编辑规则*/
    public function ruleGroupUpdate(){
        if(IS_POST){
            $data   = I();
            $result = D('AuthGroup')->ruleGroupUpdate($data);
            $this->ajaxReturn($result);
        }
    }
    
    /*分组权限分配*/
    public function ruleGroupAccess(){
        if(IS_AJAX){
            $id    = intval(I('id'));
            $rules = I('rules');
            $rules = array_unique($rules);
            $rules = implode(',' , $rules);
            $r     = M('Auth_group')->where(array('id'=>$id))->save(array('rules'=>$rules));
            if($r === false){
                $this->ajaxReturn(array('status'=>0,'msg'=>'修改失败'));
            }else{
                $this->ajaxReturn(array('status'=>1,'msg'=>'ok'));
            }
        }
    }
}

?>
