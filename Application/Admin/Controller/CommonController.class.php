<?php
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class CommonController extends Controller{
    function _initialize(){
        if(!isset($_SESSION['admin']['id'])){
            echo "<script language='javascript' type='text/javascript'>";
            echo "top.location.href='".U('Admin/login')."'";
            echo "</script>";
        }
        $r = D('Auth')->check(CONTROLLER_NAME.'/'.ACTION_NAME,$_SESSION['admin']['id']);
        if(!$r && $_SESSION['admin']['username'] != 'admin' && CONTROLLER_NAME.'/'.ACTION_NAME != 'Index/index'){
            if(IS_AJAX){
                $this->ajaxReturn(array('status'=>0,'msg'=>'无权限'));
            }else{
                echo "<script language='javascript' type='text/javascript'>";
                echo "top.location.href='".U('Index/index')."'";
                echo "</script>";
            }            
        } 
    }
}

?>