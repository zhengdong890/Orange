<?php
/*
 * 快递选择设置
 * */
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class CourierController extends CommonController{	 
    public function courierList(){
        $list = M('Courier')->select();
   	    $this->assign('list' , $list);
        $this->display();
    }
   
   public function courierAdd(){
       if(IS_AJAX){
           $data = I();
           //上传图片
           $upload = new \Think\Upload();// 实例化上传类
           $upload->maxSize = 3145728 ;// 设置附件上传大小
           $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
           $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
           // 上传文件
           $info = $upload->upload();
           if($info) {
               if($info['thumb']){//获取缩略图片路径
                   $data['thumb'] = $upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];
               }
           }
           $r = D('Courier')->courierAdd($data);  
           $this->ajaxReturn($r);
       }else{
           $this->display();
       }
   }
   
   public function courierUpdate(){
       if(IS_AJAX){
           $data = I();
           //上传图片
           $upload = new \Think\Upload();// 实例化上传类
           $upload->maxSize = 3145728 ;// 设置附件上传大小
           $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
           $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
           // 上传文件
           $info = $upload->upload();
           if($info) {
               if($info['thumb']){//获取缩略图片路径
                   $data['thumb'] = $upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];
                   $image = new \Think\Image();
                   $image->open($data['thumb']);
                   $file_mini = $upload->rootPath.$info['thumb']['savepath'].'thumb_'.$info['thumb']['savename'];
                   $image->thumb(150, 150)->save($file_mini);
               }
           }
           $r    = D('Courier')->courierUpdate($data);
           $this->ajaxReturn($r);
       }else{
           $id   = I('id');
           $data = M('Courier')->where(array('id'=>$id))->find();
           $this->assign('data' , $data);
           $this->display();
       }
   }
   
   public function courierDelete(){
       if(IS_AJAX){
           $id   = I('id');
           $r    = D('Courier')->courierDelete($id);
           $this->ajaxReturn($r);
       }
   }   
}