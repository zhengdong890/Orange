<?php
namespace Admin\Controller;
use Com\Auth;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class Goods_modelController extends CommonController{
	/*加入推荐列表*/
	public function modelList(){
		$model_list = M('Goods_model')->Field('id,name,status,sort')->select();
        $this->assign('model_list' , $model_list);
		$this->display();
	}
	 
	/*添加加入推荐*/
	public function modelAdd(){
		if(IS_POST){
			$data   = I();
			$result = D('Goods_model')->modelAdd($data);
			$this->ajaxReturn($result);
		}else{
			$this->display();
		}
	}
	 
	/*修改加入推荐*/
	public function modelUpdate(){
		if(IS_POST){
			$data   = I();
			$result = D('Goods_model')->modelUpdate($data);
			$this->ajaxReturn($result);
		}else{
			$id    = I('id');
			$model = M('Goods_model')->where(array('id'=>$id))->find();
			$this->assign('model' , $model);
			$this->display();
		}
	}

	/*删除加入推荐*/
	public function modelDelete(){
		if(IS_POST){
			$id     = I('id');
			$result = D('Goods_model')->modelDelete($id);
			$this->ajaxReturn($result);
		}
	}
}