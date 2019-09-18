<?php
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
/*
底部链接列表
*/
class ProblemController extends Controller{	
	public function index(){
	    $id   = intval(I('id')) ? intval(I('id')) : 2;
	    $list = M('Help_category')->where(array('status'=>1))->order('sort')->select();
	    foreach($list as $v){
	       if($v['id'] == 2){
	           $pid = $v['pid'];
	           break;
	       }
	    }
	    $list = get_child($list);
	    $this->assign('list' , $list);
	    $this->assign('pid' , $pid);
	    $this->assign('id' , $id);
	    $this->display();
	}
	
}	