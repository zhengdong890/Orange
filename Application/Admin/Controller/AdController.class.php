<?php
/*
 * 广告位
 * */
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class AdController extends Controller {
    /*
     * 广告
     * */
    public function adList(){
        $list = M('Ad')->select();
        $type = array('1'=>'商城首页顶部');
        $this->assign('list' , $list);
        $this->display();
    }

    /*
     * 添加广告
     * */
    public function goodsAdAdd(){
    	if(IS_POST){
	   		$data = I();
		    //上传图片
	        $upload = new \Think\Upload();// 实例化上传类
	        $upload->maxSize = 3145728 ;// 设置附件上传大小
	        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	        $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
	        // 上传文件
	        $info = $upload->upload();
	        if($info) {
	        	/*商品表图片处理*/
	            if($info['ad_img']){//获取缩略图片路径         
	                $data['ad_thumb'] = $upload->rootPath.$info['ad_img']['ad_img'].'thumb_'.$info['ad_img']['savename'];
	                $data['ad_img']   = $upload->rootPath.$info['ad_img']['savepath'].$info['ad_img']['savename'];
	                //生成缩略图
	                $image = new \Think\Image();
	                $image->open($data['ad_img']);
	                $image->thumb(220, 110)->save($data['ad_thumb']);
	            }     	            
	        }else{
		       $this->ajaxReturn(array(
		           'status' => 0,
		           'msg'    => $upload->getError()		           
		       ));die;
		    }
	        $result = D('Ad')->goodsAdAdd($data);
	        $this->ajaxReturn($result);
    	}else{
    		$type = array('1'=>'商城首页顶部');
    		$this->assign('type' , $type);
    		$this->display();
    	}   	
    }

    /*
     * 添加广告
     * */
    public function goodsAdUpdate(){
    	if(IS_POST){
	   		$data = I();
		    //上传图片
	        $upload = new \Think\Upload();// 实例化上传类
	        $upload->maxSize = 3145728 ;// 设置附件上传大小
	        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	        $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
	        // 上传文件
	        $info = $upload->upload();
	        if($info) {
	        	/*商品表图片处理*/
	            if($info['ad_img']){//获取缩略图片路径         
	                $data['ad_thumb'] = $upload->rootPath.$info['ad_img']['ad_img'].'thumb_'.$info['ad_img']['savename'];
	                $data['ad_img']   = $upload->rootPath.$info['ad_img']['savepath'].$info['ad_img']['savename'];
	                //生成缩略图
	                $image = new \Think\Image();
	                $image->open($data['ad_img']);
	                $image->thumb(220, 110)->save($data['ad_thumb']);
	            }     	            
	        }else
			if($upload->getError() != '没有文件被上传！'){
		       $this->ajaxReturn(array(
		           'status' => 0,
		           'msg'    => $upload->getError()		           
		       ));die;
		    }
	        $result = D('Ad')->goodsAdUpdate($data);
	        $this->ajaxReturn($result);
    	}else{
    		$id   = I('id');
    		if(!$id){
                exit('id错误');
    		}
    		$data = M('Ad')->where(array('id'=>$id))->find();
    		$type = array('1'=>'商城首页顶部');
    		$this->assign('data' , $data);
    		$this->assign('type' , $type);
    		$this->display();
    	}   	
    }

    public function goodsAdDelete(){
    	if(IS_POST){
    		$id = I('id');
    		if(!$id){
    			return array(
                    'status' => '0',
                    'msg'    => 'id错误'
    			);
    		}
    		$r = M('Ad')->where(array('id'=>$id))->delete();
    		if(!$r){
    			return array(
                    'status' => '0',
                    'msg'    => 'id错误'
    			);    			
    		}else{
    			return array(
                    'status' => '1',
                    'msg'    => '删除成功'
    			);
    		}
    	}
    }
}