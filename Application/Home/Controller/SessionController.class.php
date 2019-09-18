<?php
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class SessionController extends Controller {
    public function index(){	  
	
	$_SESSION['brandarr']= session('brandarr');	
	$_SESSION['Mall_cardata']= session('Mall_cardata');
    $_SESSION['order']= session('order');
	$_SESSION['buyorder']= session('buyorder');
	$_SESSION['shareorder']= session('shareorder');
	$_SESSION['sbuyorder']= session('sbuyorder');
	$_SESSION['info']= session('info');
    //session('sbuyorder',null);
    //session('info',null);
	  $_SESSION['uid']= session('uid');

			
		//dump(session('info'));exit;
		//dump(session('buyorder'));
		//dump(session('order'));exit;
		//dump(session('shareorder'));
dump(session('aaa'));exit;
//dump(session('buyorder'));exit;		
	
	
//	$r =self::getIP();
//	echo $r;exit;
	
	$this->display();
  
    }

	
	function getIP()
{
    static $realip;
    if (isset($_SERVER)){
        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
            $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            $realip = $_SERVER["HTTP_CLIENT_IP"];
        } else {
            $realip = $_SERVER["REMOTE_ADDR"];
        }
    } else {
        if (getenv("HTTP_X_FORWARDED_FOR")){
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("HTTP_CLIENT_IP")) {
            $realip = getenv("HTTP_CLIENT_IP");
        } else {
            $realip = getenv("REMOTE_ADDR");
        } 
    }
    return $realip;
}
	
	
	
}