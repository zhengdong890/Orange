<?php
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
/*
 * 帮助
 * */
class HelpController extends Controller{	
	public function index(){
	    $id   = intval(I('id')) ? intval(I('id')) : 2;
	    $list = M('Help_category')
			  ->where(array('status'=>1,'id'=>array('not in' ,'30')))
			  ->order('sort')
			  ->select();

	    foreach($list as $k => $v){
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