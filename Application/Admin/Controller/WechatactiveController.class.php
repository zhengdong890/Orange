<?php
namespace Admin\Controller;
use Com\Wechat;
use Com\My_Page;
use Think\Controller;
use my_weixin\WechatAuth;
use my_weixin\Gettoken;
header("content-type:text/html;charset=utf-8");
class WechatactiveController extends CommonController{
	//大转盘奖品及编码页面
	public function setbigwheel(){
		$this->display();
	}
		
	//粉丝列表
	public function bigwheel(){
		if(IS_POST){
			$data=$_POST;
			if($data['isuse']==2){
				$where['isuse']=2;
			}else{
				$where['isuse']=0;
			}
			if(!empty($data['num'])){
				$where['num']=$data['num'];
			}
			if(!empty($data['time'])){
				$where['time']=strtotime($data['time']);
			}
		}	
		$list=M('Bigwheel')->where($where)->select();
		foreach($list as $k=>$v){
			$where=array('openid'=>$v['openid']);
			$a=M('Wechatfans')->where($where)->find();
			$list["$k"]['msg']=$a;
		}		
		$a=new \Com\My_Page();
		$list=$a->pages($list,$pagesize=5,$url='');
		$this->list=$list['list'];
		$this->page=$list['page'];						
		$this->display();
	}
	
	public function set(){
		if(IS_POST){
			$data['id']=$_POST['id'];
			$data['isuse']=2;
			$a=M('Bigwheel')->save($data);
			if($a){
				echo '操作成功';
			}else{
				echo '操作失败';
			}
		}
	}
}