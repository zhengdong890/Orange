<?php
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class CompanyController extends CommonController{	 

   public function typeList(){
   	   $list = M('Company_type')->select();
   	   $this->assign('list' , $list);
       $this->display();
   }

   public function typeAdd(){
   	   if(IS_POST){
   	   	   $data = array(
               'name' => I('name')
   	   	   );
           $id   = M('Company_type')->add($data);
           if($id !== false){
           	    $result = array(
           	    	'status' => 1,
           	    	'msg'    => '添加成功'
           	    );
           }else{
           	    $result = array(
           	    	'status' => 0,
           	    	'msg'    => '添加失败'
           	    );
           }
           $this->ajaxReturn($result);
   	   }else{
   	   	   $this->display();
   	   }     
   }

   public function typeUpdate(){
   	   if(IS_POST){
   	   	   $id   = I('id');
   	   	   $data = array(
               'name' => I('name')
   	   	   );
           $r = M('Company_type')->where(array('id'=>I('id')))->save($data);
           if($r !== false){
           	    $result = array(
           	    	'status' => 1,
           	    	'msg'    => '添加成功'
           	    );
           }else{
           	    $result = array(
           	    	'status' => 0,
           	    	'msg'    => '添加失败'
           	    );
           }
           $this->ajaxReturn($result);
   	   }else{
   	   	   $id = I('id');
   	   	   if($id){
   	   	   	  $type = M('Company_type')->where(array('id'=>$id))->find();
   	   	   	  $this->assign('type' , $type);
   	   	   } 	   	   
   	   	   $this->display();
   	   }     
   }

   public function typeDelete(){
   	   if(IS_POST){
   	   	   $id = I('id');
   	   	   $r  = M('Company_type')->where(array('id'=>$id))->delete();
   	   	   if($r !== false){
           	    $result = array(
           	    	'status' => 1,
           	    	'msg'    => '删除成功'
           	    );
           }else{
           	    $result = array(
           	    	'status' => 0,
           	    	'msg'    => '删除失败'
           	    );
           }
           $this->ajaxReturn($result);
   	   }
   } 

   public function brandList(){
   	   $list = M('Company_brand')->select();
   	   $this->assign('list' , $list);
       $this->display();
   }

   public function brandAdd(){
   	   if(IS_POST){
	   	   $data   = I();		
   	       /*上传图片*/
		   $upload = new \Think\Upload();// 实例化上传类
		   $upload->maxSize = 3145728 ;// 设置附件上传大小
		   $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		   $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
		   // 上传文件
		   $info = $upload->upload();
		   if($info) {
				$data['thumb'] = $upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];//获取图片路径								
		   }	
           $result = D('Company')->brandAdd($data);
           $this->ajaxReturn($result);
   	   }else{
   	   	   $this->display();
   	   }     
   } 

   public function brandUpdate(){
   	   if(IS_POST){
	   	   $data   = I();		
   	       /*上传图片*/
		   $upload = new \Think\Upload();// 实例化上传类
		   $upload->maxSize = 3145728 ;// 设置附件上传大小
		   $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		   $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
		   // 上传文件
		   $info = $upload->upload();
		   if($info) {
				$data['thumb'] = $upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];//获取图片路径								
		   }	
           $result = D('Company')->brandUpdate($data);
           $this->ajaxReturn($result);
   	   }else{
   	   	   $id = I('id');
   	   	   if($id){
   	   	   	  $brand = M('Company_brand')->where(array('id'=>$id))->find();
   	   	   	  $this->assign('brand' , $brand);
   	   	   } 	   	   
   	   	   $this->display();
   	   }     
   }

   public function brandDelete(){
   	   if(IS_POST){
   	   	   $id = I('id');
   	   	   $r  = M('Company_brand')->where(array('id'=>$id))->delete();
   	   	   if($r !== false){
           	    $result = array(
           	    	'status' => 1,
           	    	'msg'    => '删除成功'
           	    );
           }else{
           	    $result = array(
           	    	'status' => 0,
           	    	'msg'    => '删除失败'
           	    );
           }
           $this->ajaxReturn($result);
   	   }
   }               
}