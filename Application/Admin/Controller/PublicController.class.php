<?php
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class PublicController extends Controller{
	public function _initialize(){
		session_start;
		if(empty($_SESSION['admin'])){
			$this->redirect('Index/login');
		}else{
			$this->admin=$_SESSION['admin'];
		}
	}
	
	public function index(){
		$this->display();
	}
	
	public function selectshop(){
		if(IS_POST){
			$id=$_POST['id'];
			session_start;
			unset($_SESSION['shopid']);
			$_SESSION['shopid']=$id;
		}
	}
	
	public function left(){
	    $menu  = C('LEFT_MENU');
	    $rules = D('Auth')->getAuth($_SESSION['admin']['id']);
	    if($_SESSION['admin']['username'] != 'admin'){
	        $rules = array_column($rules , 'name');
	        foreach($menu as $k => $v){
                foreach($v['child'] as $k1 => $v1){
                    if(!in_array($v1['c'].'/'.$v1['a'] , $rules)){
                        unset($menu[$k]['child'][$k1]);
                    }
                }
	        }	        
	    }
	    foreach($menu as $k => $v){
	    	if(!$v['child']){
                unset($menu[$k]);continue;
	    	}
            foreach($v['child'] as $k1 => $v1){
                if(!$v1['url']){
                    $menu[$k]['child'][$k1]['url'] = "http://houtai.orangesha.com/index.php?c={$v1['c']}&a={$v1['a']}";
                }
            }
	    }
	    $this->assign('menu' , $menu);
	    $this->display();
	}
}