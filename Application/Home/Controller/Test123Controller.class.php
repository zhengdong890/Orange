<?php
namespace Home\Controller;
use Think\Controller;
class Test123Controller extends Controller{
	public function index(){
		 $ma = M('Shop_data')
		 ->where(array('tp_shop_data.status'=>'1'))
		 ->where(array('tp_mall_goods.status'=>'1'))
		 ->order('tp_mall_goods.create_time desc')
		 ->join('tp_mall_goods ON tp_mall_goods.member_id = tp_shop_data.member_id')
		 ->limit(500)
		 ->select();
		
		 // foreach ($ma as $v) {

		 // 	$maa="http://"."{$v['domain']}".".orangesha.com/shangpin-"."{$v['id']}".".html";
		 	
		 // 	$this->assign('url',$maa);
		 // 	echo "<br>";
		 // 	 //echo $maa;
		 // 	 $arr=array($maa);
		 	 
		 // 	 //dump($arr);
		 // }
        $this->assign('goods',$ma);
		$this->display();
		

	}
	public function img(){
		$this->display();
	}
	public function url_img(){
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
				echo "上传成功";
				echo "<br>";
				$data['img'] = $upload->rootPath.$info['img']['savepath'].$info['img']['savename'];//获取图片路径
				echo "图标地址：";	
				echo $data['img'];							
			}else{
				echo "上传失败";
			}	
	}
	
}
}