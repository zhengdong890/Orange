<?php
/*
 * 注册会员
 * */
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class MemberController extends Controller {
    /* 
     * 身份认证申请列表
     * */
    public function memberList(){
        $this->display();
    }

    /*
     * 获取身份认证申请列表
     * */    
    public function getMembers(){
        if(IS_AJAX){
            $where    = array();
            $username = I('username');
            if($username){
                $where['username'] = array('like',"%$username%");
            }
            $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
            $listRows = intval(I('listRows'))?intval(I('listRows')):10;
            $list     = M('Member')->order('id desc')->limit($firstRow,$listRows)->where($where)->select();           
            $this->ajaxReturn(array('data'=>$list,'total'=>M('Member')->where($where)->count()));
        }
    }
    
    /*
     * 锁定用户
     * */ 
    public function changeLock(){
      if(IS_POST){
          $data   = I();
          $result = D('Member')->changeLock($data['id'] , $data['status']);
          $this->ajaxReturn($result);
      }
    }
}