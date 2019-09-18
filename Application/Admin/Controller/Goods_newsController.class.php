<?php
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class Goods_newsController extends Controller {
	public function news(){
		//echo "新闻";
		$thumb = M('Seothumb')->select();
		//dump($thumb);
		$this->assign('thumb',$thumb);
		$this->display();
	}

		public function addnews(){
		if(IS_POST){
	   		$data   = $_POST;		
	   	    /*上传图片*/
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 3145728 ;// 设置附件上传大小
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath = date('Y-m-d').'/'; // 设置附件上传（子）目录
			// 上传文件
			$info = $upload->upload();
			if($info) {
				// $data['thumb'] = $upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];//获取图片路径	

			foreach($info as $file){      
			  $model = M('Seothumb'); 
			 // echo $file['savepath'].$file['savename'];   
			  $dataList[] = array('thumb'=>$upload->rootPath.$file['savepath'].$file['savename'],'time'=> NOW_TIME);
			}
			$photo=$model->addAll($dataList);
			if($photo){
				$this->ajaxReturn(array('status'=>'1','msg'=>'添加数据成功'));
			}
			
			}	
	   		
	   	}
		$this->display();
	}

	public function del(){
		if(IS_AJAX){
			$id = I('id');
			$img = M('Seothumb')->where(array('id'=>$id))->find();
			$del = M('Seothumb')->where("id=$id")->delete();
			if($del){
				unlink($img['thumb']);
				echo "删除成功";
			}else{
				echo "删除失败";
			}
		}
	}
	public function update(){
		$id = I('id');
		$img = M('Seothumb')->where(array('id'=>$id))->find();
		$this->assign('fr',$img);
		$this->display();

	}
	public function edit(){
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
			$result = D('Seothumb')->update($data);	
			$this->ajaxReturn($result);
	   	}
	}
}