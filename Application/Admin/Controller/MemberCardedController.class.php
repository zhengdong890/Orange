<?php
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class MemberCardedController extends Controller {
    /* 
     * 身份认证申请列表
     * */
    public function qualificationList(){
        $this->display();
    }

    /*
     * 获取身份认证申请列表
     * */    
    public function getQualifications(){
        if(IS_AJAX){
            $where['is_check'] = intval(I('is_check')) === '0' || I('is_check') ? intval(I('is_check')) : 0;
            if(I('status') === '0' || I('status')){
                $where['status'] = intval(I('status'));
            }
            $username = I('username');
            if($username){
                $where['username'] = array('like',"%$username%");
            }
            $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
            $listRows = intval(I('listRows'))?intval(I('listRows')):10;
            $list_ = M('Member_carded')->order('id desc')->limit($firstRow,$listRows)->where($where)->select();  
            $member_ids = array();
            foreach($list_ as $v){
                $member_ids[] = $v['member_id'];
                $list[$v['member_id']] = $v;
            }
            $member_ids = implode(',' , $member_ids);
            $data = M('Member')->where(array('id'=>array('in' , $member_ids)))->Field('id,username')->select();
            foreach($data as $v){
                $list[$v['id']]['username'] = $v['username'];
            }
            $this->ajaxReturn(array('data'=>$list,'total'=>M('Member_carded')->where($where)->count()));
        }
    }
    
    /*
     * 身份认证申请审核
     * */    
    public function qualificationCheck(){
        if(IS_AJAX){
            $data   = I();
            $result = D('MemberCarded')->qualificationCheck($data);
            $this->ajaxReturn($result);
        }
    }
}