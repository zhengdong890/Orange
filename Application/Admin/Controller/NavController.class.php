<?php
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class NavController extends Controller {
  /**
   * 导航列表页
   * @access public  
   */ 
   public function navList(){ 
       $list = M('Nav')->select();
       $this->assign('list' , $list);
       $this->display();
   }
   
   /*添加导航*/
   public function navAdd(){
	   	if(IS_POST){
	   		$data   = $_POST;			
	   		$result = D('Nav')->navAdd($data);
	   		if($result['status']){
	   		    $redis = new \Com\Redis();
	   		    $redis->redis->delete('navs');
	   		}
	   		$this->ajaxReturn($result);
	   	}else{
	   		$this->display();
	   	}
   }
   
   /*修改导航*/
   public function navUpdate(){
	   if(IS_POST){
	   		$data   = $_POST;	   		   			   		
			$result = D('Nav')->navUpdate($data);
			if($result['status']){
			    $redis = new \Com\Redis();
			    $redis->redis->delete('navs');
			}
			$this->ajaxReturn($result);
	   	}else{
	   		$id  = I('id');
	   		$nav = M('Nav')->where(array('id'=>$id))->find(); 
	   		$this->assign('nav' , $nav);
	   		$this->display();
	   	}
   }
   
   /*ajax删除导航*/
   public function navDelete(){
   	  if(IS_POST){
   	  	  $id     = I('id');
   	  	  $result = D('Nav')->navDelete($id);
   	  	  if($result['status']){
   	  	      $redis = new \Com\Redis();
   	  	      $redis->redis->delete('navs');
   	  	  }
   	  	  $this->ajaxReturn($result);
   	  }
   }

   /*ajax更改显示状态*/
   public function statusChange(){
       if(IS_POST){
           $result=array(
               'status'=>'1',
               'msg' => 'ok'
           );
           $id = I('id');
           $status = I('status');
           $r = M('Nav')->where(array('id'=>$id))->save(array('status'=>$status));
           if($r === false){
               $result=array(
                   'status'=>'0',
                   'msg' => '失败'
               );
           }
           $redis = new \Com\Redis();
           $redis->redis->delete('navs');
           $this->ajaxReturn($result);
       }
   }   
}