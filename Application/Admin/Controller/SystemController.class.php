<?php
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class SystemController extends CommonController{
   /*seo*/
   public function seo(){
   	   if(IS_POST){
   	   	  $data=I();
   	   	  /*验证数据*/
   	   	  $seo = D("Seo");
   	   	  $rules= array(
   	   	  		array('title','require','必须输入标题'),
   	   	  		array('keyword','require','必须输入关键字'),
   	   	  		array('keyword','require','必须输入描述'),
   	   	  );
   	   	  if(!$seo->validate($rules)->create($data)){
   	   	  	$this->error($seo->getError(),'seo');exit;
   	   	  }
   	   	  //上传图片
   	   	  $upload = new \Think\Upload();// 实例化上传类
   	   	  $upload->maxSize = 3145728 ;// 设置附件上传大小
   	   	  $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
   	   	  $upload->rootPath = './Uploads/';
   	   	  $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
   	   	  // 上传文件
   	   	  $info = $upload->upload();
   	   	  if(!$info) {// 上传错误提示错误信息
   	   	  	//$this->error($upload->getError());
   	   	  }else{// 上传成功
   	   	  	  $data['thumb']=$upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];
   	   	  	  $oldthumb=M('Seo')->where(array('id'=>$data['id']))->getField('thumb');
   	   	  }
   	   	  $a=M('Seo')->save($data);
   	      if($a){
   	      	  unlink($oldthumb);
              $this->success('修改成功',U("System/seo"),1);
          }else{
              $this->error('修改失败',U("System/seo"),1);
          }
   	   }else{
   	   	  $this->seo=M('Seo')->find();
   	   	  $this->display();
   	   }
   }	
   
   /*友情链接列表*/
   public function friendlink(){
   	   $this->friendlinks=M('Friendlink')->select();
   	   $this->display();
   }
   
   /*添加友情链接*/
   public function friendlink_add(){
   	  if(IS_POST){
   	  	$data=I();
   	  	/*验证数据*/
   	  	$a = D("Friendlink");
   	  	$rules= array(
   	  			array('name','require','必须输入网站名称'),
   	  			array('url','require','必须输入链接地址'),
   	  	);
   	  	if(!$a->validate($rules)->create($data)){
   	  		$this->error($a->getError(),'friendlink_add');exit;
   	  	}
   	  	//上传图片
   	  	$upload = new \Think\Upload();// 实例化上传类
   	  	$upload->maxSize = 3145728 ;// 设置附件上传大小
   	  	$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
   	  	$upload->rootPath = './Uploads/';
   	  	$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
   	  	// 上传文件
   	  	$info = $upload->upload();
   	  	if(!$info) {// 上传错误提示错误信息
   	  		//$this->error($upload->getError());
   	  	}else{// 上传成功
   	  		$data['thumb']=$upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];
   	  	}
   	  	$id=M('Friendlink')->add($data);
   	  	if($id){
   	  		$this->success('添加成功',U("System/friendlink"),1);
   	  	}else{
   	  		$this->error('添加失败',U("System/friendlink"),1);
   	  	}
   	  }else{
   	   	 $this->display();
   	  }
   }
   
   /*修改友情链接*/
   public function friendlink_update(){
   	if(IS_POST){
   		$data=I();
   		/*验证数据*/
   		$a = D("Friendlink");
   		$rules= array(
   				array('name','require','必须输入网站名称'),
   				array('url','require','必须输入链接地址'),
   		);
   		if(!$a->validate($rules)->create($data)){
   			$this->error($a->getError(),'friendlink_add');exit;
   		}
   		//上传图片
   		$upload = new \Think\Upload();// 实例化上传类
   		$upload->maxSize = 3145728 ;// 设置附件上传大小
   		$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
   		$upload->rootPath = './Uploads/';
   		$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
   		// 上传文件
   		$info = $upload->upload();
   		if(!$info) {// 上传错误提示错误信息
   			//$this->error($upload->getError());
   		}else{// 上传成功
   			$data['thumb']=$upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];
   			$oldthumb=M('Friendlink')->where(array('id'=>$data['id']))->getField('thumb');
   		}
   		$id=M('Friendlink')->save($data);
   		if($id){
   			unlink($oldthumb);
   			$this->success('保存成功',U("System/friendlink"),1);
   		}else{
   			$this->error('保存失败',U("System/friendlink"),1);
   		}
   	}else{
   		$id=I('id');
   		$friendlink=M('Friendlink')->where(array('id'=>$id))->find();
   		$friendlink['checked_y']=($friendlink['is_show']=='y')?'checked=checked':'';
   		$friendlink['checked_n']=($friendlink['is_show']!='y')?'checked=checked':'';
   		$this->friendlink=$friendlink;
   		$this->display();
   	}
   }
   
   /*删除友情链接*/
   public function friendlink_delete(){
   	if(IS_POST){
   		$id=$_POST['id'];
   		$oldthumb=M('Friendlink')->where(array('id'=>$id))->getField('thumb');
   		$a=M('Friendlink')->where(array('id'=>$id))->delete($id);
   		if(!empty($a)){
   			unlink($oldthumb);//删除图片
   			$result='删除成功';
   		}else{
   			$result='删除失败';
   		}
   		echo $result;
   	}
   }
   
   /*ajax修改友情链接显示状态*/
   public function friendlink_is_show_change(){
   	if(IS_POST){
   		$id=I('id');
   		$is_show=M('Friendlink')->where(array('id'=>$id))->getField('is_show');
   		$is_show=($is_show=='y')?'n':'y';
   		$a=M('Friendlink')->where(array('id'=>$id))->save(array('is_show'=>$is_show));
   		if($a){
   			$result['tishi']="修改成功";
   		}else{
   			$result['tishi']="修改失败";
   		}
   		$result['is_show']=$is_show;
   		$this->ajaxReturn($result);
   	}
   }
   
   /*店铺列表*/
   public function shop(){
	   $this->shops=M('Shop')->select();
	   $this->display();
   }
   
   /*添加店铺*/
   public function shop_add(){
      if(IS_POST){
            $data=$_POST;
   	      	/*验证数据*/
	   		$shop = D("Shop");   		
	   		$rules= array(
			      array('name','require','必须输入店铺名'),  
			      array('name','','您输入的店铺已存在',1,'unique',3), 
			      array('telnum','number','请输入正确的电话号码'),
			      array('telnum','8,11','请输入正确的电话号码',1,'length',3),
			      array('kefuqq','number','qq必须位数字'),
	   		);
	   		if(!$shop->validate($rules)->create($data)){
	   			exit($shop->getError());
	   		}
            //上传店铺图片
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728 ;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->rootPath = './Uploads/';
            $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
            // 上传文件
            $info = $upload->upload();
            if(!$info) {// 上传错误提示错误信息
                 //$this->error($upload->getError());
            }else{// 上传成功
               $data['shop_thumb']=$upload->rootPath.$info['shop_thumb']['savepath'].$info['shop_thumb']['savename'];
            }
            $a=M('Shop')->add($data);
            if($a){
            	 $this->success('添加成功',U("Index/index"),1);
            }else{
            	$this->error('添加失败',U("Index/index"),1);
            }
        }else{
        	$this->display();
        }
   }
   
   /*编辑店铺信息*/
   public function shop_update(){
	   	if(IS_POST){
	   		$data=$_POST;
	   		/*验证数据*/
	   		$shop = D("Shop");
	   		$rules= array(
	   				array('name','require','必须输入店铺名'),
	   				array('telnum','number','请输入正确的电话号码'),
	   				array('telnum','8,11','请输入正确的电话号码',1,'length'),
	   				array('kefuqq','number','qq必须位数字'),
	   		);
	   		if(!$shop->validate($rules)->create($data)){
	   			exit($shop->getError());
	   		}
	   		//上传图片
	   		$upload = new \Think\Upload();// 实例化上传类
	   		$upload->maxSize = 3145728 ;// 设置附件上传大小
	   		$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	   		$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
	   		// 上传文件
	   		$info = $upload->upload();
	   		if(!$info) {//无图片上传
	   			//$this->error($upload->getError());
	   		}else{// 上传成功
	   			$data['shop_thumb']=$upload->rootPath.$info['shop_thumb']['savepath'].$info['shop_thumb']['savename'];
	   			$oldshop_thumb=M('Shop')->where(array('id'=>$data['id']))->getField('shop_thumb');
	   		}
	   		$a=M('Shop')->save($data);//更新数据
	   		if($a){
	   			unlink($oldshop_thumb);
	   			$this->success('修改成功','index');
	   		}
	   	}else{
	   		$shopid=$_GET['id'];
	   		$this->shop=M('Shop')->where(array('id'=>$shopid))->find();
	   		$this->display();
	   	}
   }
   
   /*banner列表页*/
   public function banner(){
   		$this->banners=M('Banner')->order('sort')->select();
   		$this->display();
   }

   /*增加banner*/
   public function banner_add(){
	   	if(IS_POST){
	   	    $data=$_POST;
	   		//上传图片
	   		$upload = new \Think\Upload();// 实例化上传类
	   		$upload->maxSize = 3145728 ;// 设置附件上传大小
	   		$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	   		$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
	   		// 上传文件
	   		$info = $upload->upload();
	   		if(!$info) {//无图片上传
	   			//$this->error($upload->getError());
	   		}else{// 上传成功
	   			$data['banner_thumb']=$upload->rootPath.$info['banner_thumb']['savepath'].$info['banner_thumb']['savename'];	   			
	   		}
	   		$a=M('Banner')->add($data);//插入数据
	   		if($a){
	   			$this->success('修改成功','banner');
	   		}
	   	}else{
	   		$this->display();
	   	}
   }
   
   /*ajax删除banner*/
   public function banner_delete(){
	   	if(IS_POST){
	   		$id=$_POST['id'];
	   		$oldbanner_thumb=M('Banner')->where(array('id'=>$id))->getField('banner_thumb');
	   		$a=M('Banner')->where(array('id'=>$id))->delete($id);
	   		if(!empty($a)){
	   			unlink($oldbanner_thumb);//删除图片
	   			$result='删除成功';
	   		}else{
	   			$result='删除失败';
	   		}
	   		echo $result;
	   	}
   }
   
   /*banner编辑页*/
   public function banner_update(){
	   	if(IS_POST){
	   		$data=$_POST;
	   		//上传图片
	   		$upload = new \Think\Upload();// 实例化上传类
	   		$upload->maxSize = 3145728 ;// 设置附件上传大小
	   		$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	   		$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
	   		// 上传文件
	   		$info = $upload->upload();
	   		if(!$info) {//无图片上传
	   			//$this->error($upload->getError());
	   		}else{// 上传成功
	   			$data['banner_thumb']=$upload->rootPath.$info['banner_thumb']['savepath'].$info['banner_thumb']['savename'];
	   			$oldbanner_thumb=M('Banner')->where(array('id'=>$data['id']))->getField('banner_thumb');
	   		}
	   		$a=M('Banner')->save($data);//更新数据
	   		if($a){
	   			unlink($oldbanner_thumb);
	   			$this->success('修改成功','banner');
	   		}
	   	}else{
	   		$id=I('id');
	   		$this->banner=M('Banner')->where(array('id'=>$id))->find();
	   		$this->display();
	   	}
   }
}
