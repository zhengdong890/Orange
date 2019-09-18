<?php
//友情链接
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class FriendlinkController extends CommonController{
	public function index(){
		//echo "这是友情链接...";
	$file_path = "/data/wwwroot/default/Public/cnt.txt";
   	if(file_exists($file_path)){
		$fp = fopen($file_path,"r");
		$str = fread($fp,filesize($file_path));//指定读取大小，这里把整个文件内容读取出来
		$str = str_replace("\r\n","<br />",$str);

		}
		$this->assign('str',$str);
		$friendlink = M('Friendlink')->select();
		//dump($friendlink);
		$this->assign('friendlink',$friendlink);
		$this->display();
	}

	public function friendadd(){
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
	   		$result = D('Friendlink')->friendAdd($data);
	   		
	   		$this->ajaxReturn($result);
	   	}
		$this->display();
	}
	public function update(){
		$id = I('id');
		$friend = M('Friendlink')->where(array('id'=>$id))->find();
		$this->assign('fr',$friend);

		$this->display();
	}
	public function friendupdate(){
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
			$result = D('Friendlink')->friendupdate($data);	
			$this->ajaxReturn($result);
	   	}
	}

	public function del(){
    if(is_AJAX){
        $id = I('id');
        $del = M('Friendlink')->where(array('id'=>$id))->delete();
        if($del){
        	echo "删除成功";
        }else{
        	echo "删除失败";
        }

    }
  }

}