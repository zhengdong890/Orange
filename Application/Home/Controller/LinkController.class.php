<?php
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class LinkController extends Controller {	
   public function zhuce(){
	   $map['id']  = 1;
		$zhuce = M('zhuce')->where($map)->find();
		//var_dump($zhuce);exit; 
		$this->assign('zhuce',$zhuce['content']);	    
		$this->display();
	}
 public function lists(){ 
 /*
		$map['name']  = '码垛机器人';
		
		$goodlist = M('goods')->where($map)->select();
		
		$this->assign('goodlist',$goodlist);
		
		$this->display();
		*/
	}


}