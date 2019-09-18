<?php
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class SellerRuleController extends Controller {
  /**
   * 分类列表页
   * @access public  
   */ 
   public function categoryList(){ 
	   	$data = M('Seller_rule')->select();
	   	$list = tree_1($data);
	   	$this->assign('list' , $list);
	   	$this->display();
   }

   /*添加帮助菜单分类*/
   public function categoryAdd(){
	   	if(IS_POST){
	   		$data   = $_POST;		
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
	   		$result = D('SellerRule')->categoryAdd($data);
	   		$this->ajaxReturn($result);
	   	}else{
	   		$categorys  = M('Seller_rule')->select();
	   		$this->assign('categorys' , tree_1($categorys));
		   	$sort       = M('Seller_rule')->max('sort');
		   	$this->assign('sort' , $sort +1 );
	   		$this->display();
	   	}
   }
   
   /*修改帮助菜单分类*/
   public function categoryUpdate(){
	   if(IS_POST){
	   		$data   = $_POST;	   		   			   		
	   	    //上传图片
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize =  3145728;// 设置附件上传大小
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
			// 上传文件
			$info = $upload->upload();
			if($info) {// 上传错误提示错误信息
				if($info['thumb']){
					$data['thumb'] = $upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];//获取图片路径
				}
			}	
			$result = D('SellerRule')->categoryUpdate($data);	
			$this->ajaxReturn($result);
	   	}else{
	   		$id       = I('id');
	   		$category = M('Seller_rule')->where(array('id'=>$id))->find(); 
	   		$this->assign('category' , $category);   		
	   		$categorys = M('Seller_rule')->select();
	   		$this->assign('categorys' , tree_1($categorys));	   		
	   		$this->display();
	   	}
   }
   
   /*ajax删除商品分类*/
   public function categoryDelete(){
   	  if(IS_POST){
   	  	  $id     = I('id');
   	  	  $result = D('SellerRule')->categoryDelete($id);
   	  	  $this->ajaxReturn($result);
   	  }
   }   
}