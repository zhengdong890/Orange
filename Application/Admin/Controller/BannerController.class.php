<?php
/*
 * banner模块
 * */
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class BannerController extends Controller {  
   /*出租banner*/
   public function bannerList(){
   		$banners = M('Banner')->where(array('type'=>1))->order('sort')->select();
   		$this->assign('banners' , $banners);
   		$this->display();
   }

   /*商城banner*/
   public function mallBannerList(){
   		$banners = M('Banner')->where(array('type'=>2))->order('sort')->select();
   		$this->assign('banners' , $banners);
   		$this->display();
   }

   /*增加banner*/
   public function bannerAdd(){
	   	if(IS_POST){
	   	    $data = $_POST;
	   		//上传图片
	   		$upload = new \Think\Upload();// 实例化上传类
	   		$upload->maxSize  = 3145728 ;// 设置附件上传大小
	   		$upload->exts     = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	   		$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
	   		// 上传文件
	   		$info = $upload->upload();
	   		if($info) {
	   			$data['banner_thumb'] = $upload->rootPath.$info['banner_thumb']['savepath'].$info['banner_thumb']['savename'];	
	   		}else{
		       $this->ajaxReturn(array(
		           'status' => 0,
		           'msg'    => $upload->getError()		           
		       ));die;
		    }
	   		$result = D('Banner')->bannerAdd($data);
	   		$this->ajaxReturn($result);
	   	}else{
	   		$type = I('type') == 1 ? 1 : 2;
	   		$sort = M('Banner')->where(array('type'=>$type))->max('sort');
	   		$this->assign('sort' , $sort || $sort == 0 ? $sort + 1 : 0);
	   		if($type == 1){
	   			$this->display();
	   		}else{
	   			$this->display('mallBannerAdd');
	   		}	   		
	   	}
   }
   
   /*banner编辑页*/
   public function bannerUpdate(){
	   	if(IS_POST){
	   		$data   = $_POST;
	   		//上传图片
	   		$upload = new \Think\Upload();// 实例化上传类
	   		$upload->maxSize = 3145728 ;// 设置附件上传大小
	   		$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	   		$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
	   		// 上传文件
	   		$info = $upload->upload();
	   		if($info) {
	   			$data['banner_thumb'] = $upload->rootPath.$info['banner_thumb']['savepath'].$info['banner_thumb']['savename'];	
	   		}else
	   		if($upload->getError() != '没有文件被上传！'){
		       $this->ajaxReturn(array(
		           'status' => 0,
		           'msg'    => $upload->getError()		           
		       ));die;
		    }
	   		$result = D('Banner')->bannerUpdate($data);
	   		$this->ajaxReturn($result);
	   	}else{
	   		$id = I('id');
	   		$this->banner = M('Banner')->where(array('id'=>$id))->find();
	   		$this->display();
	   	}
   }

   /*ajax删除banner*/
   public function bannerDelete(){
	   	if(IS_POST){
	   		$id = I('id');
            $result = D('Banner')->bannerDelete($id);
	   		$this->ajaxReturn($result);
	   	}
   }   
}
