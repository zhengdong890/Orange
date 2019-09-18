<?php
//友情链接
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class KuaiDiController extends CommonController{
	public function index(){
		//echo "快递公司...";
		$k = M('Kuaidi')->select();
		//dump($friendlink);
		$this->assign('friendlink',$k);
		$this->display();
	}

	public function add(){
		if(IS_POST){
	   		$data   = $_POST;		
	   		$result = D('KuaiDi')->Kuaidiadd($data);
	   		$this->ajaxReturn($result);
	   	}
		$this->display();
	}
	public function update(){
		$id = I('id');
		$k = M('Kuaidi')->where(array('id'=>$id))->find();
		$this->assign('fr',$k);

		$this->display();
	}
	public function Kuaidiupdate(){
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
			$result = D('KuaiDi')->Kuaidiupdate($data);	
			$this->ajaxReturn($result);
	   	}
	}

	public function del(){
    if(is_AJAX){
        $id = I('id');
        $del = M('Kuaidi')->where(array('id'=>$id))->delete();
        if($del){
        	echo "删除成功";
        }else{
        	echo "删除失败";
        }

    }
  }

}