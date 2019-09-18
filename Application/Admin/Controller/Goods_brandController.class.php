<?php
namespace Admin\Controller;
use Com\Auth;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class Goods_brandController extends CommonController{
    public function brandList(){
    	$list = M('Goods_brand')->select();
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
				$data['brand_thumb'] = $upload->rootPath.$info['brand_thumb']['savepath'].$info['brand_thumb']['savename'];//获取图片路径								
		   }
           $result = D('Goods_brand')->brandAdd($data);
           if($result['status']){
              $redis  = new \Com\Redis();
              Hook::add('brandAdd','Home\\Addons\\BrandAddon');
		      Hook::listen('brandAdd');
              $redis->redis->delete('brands');           	
           }
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
				$data['brand_thumb'] = $upload->rootPath.$info['brand_thumb']['savepath'].$info['brand_thumb']['savename'];//获取图片路径								
		    }
            $result = D('Goods_brand')->brandUpdate($data);
            if($result['status']){
                $redis  = new \Com\Redis(); 
                Hook::add('brandAdd','Home\\Addons\\BrandAddon');
		        Hook::listen('brandAdd');
                Hook::add('mallCategoryBrandAdd','Home\\Addons\\BrandAddon');
		        Hook::listen('mallCategoryBrandAdd');
               $redis->redis->delete('brands');           	
            }
            $this->ajaxReturn($result);
    	}else{
    		$id = intval(I('id'));
    		if(!$id){
                exit('id不正确');
    		}
		    $data = M('Goods_brand')->where(array('id'=>$id))->find();
		    $this->assign('data' , $data);
            $this->display();   		
    	}  
    } 

    public function brandDelete(){
    	if(IS_POST){
	    	$id     = I('id');
	    	$result = array('status'=>0);
	    	Hook::add('brandAdd','Home\\Addons\\BrandAddon');
	        Hook::listen('brandAdd');
            Hook::add('mallCategoryBrandAdd','Home\\Addons\\BrandAddon');
	        Hook::listen('mallCategoryBrandAdd');
 	    	//$result = D('Goods_brand')->brandDelete($id);
	    	$this->ajaxReturn($result);    		
    	}
    } 

    /*ajax更改商品品牌状态*/
    public function changeStatus(){
        if(IS_POST){
            $id=I('id');
            $status=M('Goods_brand')->where(array('id'=>$id))->getField('status');
            $status=($status=='1')?'0':'1';
            $a=M('Goods_brand')->where(array('id'=>$id))->save(array('status'=>$status));
            if($a){
                $result['tishi']="修改成功";
            }else{
                $result['tishi']="修改失败";
            }
            $result['status']=$status;
            $redis  = new \Com\Redis(); 
            Hook::add('brandAdd','Home\\Addons\\BrandAddon');
	        Hook::listen('brandAdd');
            Hook::add('mallCategoryBrandAdd','Home\\Addons\\BrandAddon');
	        Hook::listen('mallCategoryBrandAdd');
            $redis->redis->delete('brands'); 
            $this->ajaxReturn($result);
        }
    }          
}