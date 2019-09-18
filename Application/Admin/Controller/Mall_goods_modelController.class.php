<?php
namespace Admin\Controller;
use Com\Auth;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class Mall_goods_modelController extends CommonController{
	/*加入推荐列表*/
	public function modelList(){
		$model_list = M('Mall_goods_model')->Field('id,name,status,sort')->select();
        $this->assign('model_list' , $model_list);
		$this->display();
	}
	
	/*获取商品加入推荐*/
	public function getGoodsModel(){
	    if(IS_AJAX){
	        $goods_model = M('Mall_goods_model')->select();
	        $this->ajaxReturn($goods_model);
	    }
	}	
	
	/*添加加入推荐*/
	public function modelAdd(){
		if(IS_POST){
			$data   = I();
			$result = D('Mall_goods_model')->modelAdd($data);
			$this->ajaxReturn($result);
		}else{
			$this->display();
		}
	}
	 
	/*修改加入推荐*/
	public function modelUpdate(){
		if(IS_POST){
			$data   = I();
			$result = D('Mall_goods_model')->modelUpdate($data);
			$this->ajaxReturn($result);
		}else{
			$id    = I('id');
			$model = M('Mall_goods_model')->where(array('id'=>$id))->find();
			$this->assign('model' , $model);
			$this->display();
		}
	}
}