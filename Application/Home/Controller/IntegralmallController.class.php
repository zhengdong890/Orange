<?php
//积分商城
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class IntegralmallController extends Controller {
	public function index(){
		//积分商品价格查询
		$num = I('num')?I('num'):1;
		$num1 = I('num1')?I('num1'):1000000000;
		if($num1){
			$data['goods_price'] = array(array('gt',$num),array('lt',$num1), 'and') ;
			$goods = M('Integration_goods')->where(array('status'=>1))->where($data)->select();
		}


		$Inte_goods = M('Integration_goods');
		//推荐积分商品
		$reco_goods = M('Integration_goods')->where(array('status'=>1))->order('create_time desc')->limit(2)->select();
		//全部商品
		
		$data = $_SESSION['member_data'];//查询用户是否登入信息
		if($data){
			$status =1;
			$this->assign('status',$status);
		}
		$this->assign('re_goods',$reco_goods);
		$this->assign('data',$data);
		$this->assign('goods',$goods);
		$this->display();
	}
	public function exchange_goods(){
		$id = I('id');//接收传过来的ID
		$goods = M('Integration_goods')->where(array('id'=>$id))->find();
		$data = $_SESSION['member_data'];
		$member_id = $data['id'];
		$mem = M('member')->where(array('member_id'=>$member))->find();
		$this->assign('mem',$mem);
		$this->assign('goods',$goods);
		$this->display();
	}

	public function exchange_record(){
		
		$this->display();
	}

	public function common_problem(){

		$this->display();
	}



}