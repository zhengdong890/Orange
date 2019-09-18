<?php
/*
 * 卖家商品详情
 * */  
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class Seller_OrderdetailController extends Controller{
	public function orderDetail(){
		//echo "商品详情...";
		$order_id = I('id');
		//dump($order_id);
		$member_id = $_SESSION['member_data']['id'];
		//dump($member_id);
		$mall_order = M('Mall_order')->where(array('id'=>$order_id))->find();
		$mall_order_data = M('Mall_order_data')->where(array('order_id'=>$order_id))->find();
		//dump($mall_order);
		//dump($mall_order_data);
		$this->assign('goods',$mall_order);
		$this->assign('goods_data',$mall_order_data);
		$this->display();
	}
}