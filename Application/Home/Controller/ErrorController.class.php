<?php
/*
 * 错误显示
 * */
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class ErrorController extends Controller{	 
    /*404*/
    public function error_404(){
        $this->display();    
    }
}