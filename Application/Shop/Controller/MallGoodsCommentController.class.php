<?php
namespace Shop\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class MallGoodsCommentController extends Controller{	
    public function getCommentList(){
    	if(IS_AJAX){
    		$firstRow = intval(I('firstRow'));
    		$listRows = intval(I('listRows'));
    		$goods_id = intval(I('goods_id'));
	        if($goods_id == 0){
	            $this->ajaxReturn(array(
	                'status' => 0,
	                'msg'    => '请输入商品id'
	            ));
	        }
    		$result   = D('Home/MallGoodsComment')->getCommentListById($goods_id);

    		if(I('is_get_total') == 1){
	    		$total    = D('Home/MallGoodsComment')->getGoodsCommentTotal($goods_id);
	    		$result['total'] = $total;
    		}
    		$this->ajaxReturn($result);
    	}
    }
}