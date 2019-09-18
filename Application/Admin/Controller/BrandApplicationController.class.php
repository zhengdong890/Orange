<?php
/*
 * 品牌申请模块
 * */
namespace Admin\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class BrandApplicationController extends CommonController{   
    public function brandList(){
        if(IS_POST){
        	$firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
	        $listRows = intval(I('listRows'))?intval(I('listRows')):10;
	        $list     = M('Brand_application')
			          ->order('id desc')
			          ->limit($firstRow,$listRows)
			          ->select();
	        $this->ajaxReturn(array(
	        	'data'  => $list,
	        	'total' => M('Brand_application')->count()
	        ));
        }else{
        	$this->display();
        }
    } 

	/*
	 * 品牌申请审核
	 * */
    public function checkApplication(){
        if(IS_POST){ 
            $id      = intval(I('id'));  
            $status  = intval(I('status'));  
            $content = I('content');       	
            $r = D('BrandApplication')->checkApplication($id , $status , $content);
            if($r['code'] == 1){
                //更新缓存
                Hook::add('mallCategoryBrandAdd','Home\\Addons\\BrandAddon');
		        Hook::listen('mallCategoryBrandAdd');
            }else
            if($r['code'] == 2){
                //更新缓存
                Hook::add('brandAdd','Home\\Addons\\BrandAddon');
		        Hook::listen('brandAdd');
                Hook::add('mallCategoryBrandAdd','Home\\Addons\\BrandAddon');
		        Hook::listen('mallCategoryBrandAdd');
            }            
            $this->ajaxReturn($r);
        }else{
            $id   = I('id');
            $data = M('Brand_application')->where(array('id'=>$id))->find();
            if(empty($data)){
                exit('暂无该申请');
            }
            $shop_data = M('Shop_data')->where(array('member_id'=>$data['member_id']))->find();
            $cat_name  = M('Mall_category')->where(array('id'=>array('in' , $data['cat_id'])))->Field('cat_name')->select();
            $cat_name  = implode('-',array_column($cat_name, 'cat_name'));
            $this->assign('data' , $data);
            $this->assign('cat_name' , $cat_name);
            $this->assign('shop_data' , $shop_data);
        	$this->display();
        }
    }
}