<?php
namespace WeChat\Controller;
use Think\Controller;
use my_weixin\WechatAuth;
use my_weixin\jssdk;
header("content-type:text/html;charset=utf-8");
class CardController extends Controller{	 
	public function index(){
		$jssdk=new jssdk();
		dump($jssdk->createCardMeg());
	}
}