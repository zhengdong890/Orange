<?php
namespace Admin\Controller;
use Com\Wechat;
use Com\My_Page;
use Think\Controller;
use my_weixin\WechatAuth;
use my_weixin\Gettoken;
header("content-type:text/html;charset=utf-8");
class WechatfansController extends Controller{	
	public function _initialize(){
		session_start;
		if(empty($_SESSION['admin'])){
			$this->redirect('Index/login');
		}else{
			$this->admin=$_SESSION['admin'];
		}
	}
	
	//粉丝列表
	public function index(){
		$wechat=new WechatAuth();
		$list=M('Wechatfans')->select();
		$a=new \Com\My_Page();
		$list=$a->pages($list,$pagesize=7,$url='');
		$this->list=$list['list'];
		$this->page=$list['page'];	
		$this->group=M('Wechatgroup')->select();
		$this->display();
	}
	
	/*ajax移动粉丝到分组*/
	public function move_group(){
		if(IS_POST){
			$ids=I('ids');
			$groupid=I('groupid');
			if($ids){
				$arr=array();
				$ids=explode(',',$ids);//分割成数组
				M('Wechatfans')->Field('groupid')->select();//获取
				$arr["$groupid"]=0;
				foreach($ids as $v){
					$id=M('Wechatfans')->where(array('id'=>$v))->getField('groupid');//获取当前的分组id						
					if($groupid!=$id){
						$arr["$groupid"]++;
						if($id){
							if(!empty($arr["$id"])){
								$arr["$id"]--;
							}else{
								$arr["$id"]=-1;
							}
						}						
				    }												
					M('Wechatfans')->where(array('id'=>$v))->save(array('groupid'=>$groupid));
				}
				echo "操作成功";
			}
		}
	}
	
	//同步微信分组
	public function updatewechatgroup(){
		$wechat=new WechatAuth();
		$allgroup=$wechat->getgroup();
		foreach($allgroup as $k=>$v){
			$where=array('groupid'=>$v['id']);
			$data=array('groupid'=>$v['id'],'groupname'=>$v['name']);
			$a=M('Wechatgroup')->where($where)->find();
			if(empty($a)){
				M('Wechatgroup')->add($data);
			}else{
				$data['id']=$a['id'];
				M('Wechatgroup')->save($data);
			}
		}
		$this->redirect('group');
	}
	
	//粉丝分组列表
	public function group(){
		$list=M('Wechatgroup')->select();
		foreach($list as $k=>$v){
			$list["$k"]['num']=M('Wechatfans')->where(array('groupid'=>$v['id']))->count();
		}
		$this->list=$list;
		$this->display();
	}
	
	//添加分组
	public function addgroup(){		
		if(IS_POST){
			$data=$_POST;
			$a=M('Wechatgroup')->add($data);
			if($a){
				/* $wechat=new WechatAuth();
				$res=$wechat->creategroup($data['groupname']); */
				$this->success('操作成功',group);
			}
		}else{			
			$this->display();
		}
	}
	
	//更新分组信息
	public function updategroup(){
		$id=$_GET['id'];
		if(IS_POST){
			$data=$_POST;
			$a=M('Wechatgroup')->save($data);
			if($a){
				/* $wechat=new WechatAuth();
				$res=$wechat->updategroup($data['id'],$data['groupname']);
				dump($res);die; */
				$this->success('操作成功',group);
			}
		}else{
			if($id){
				$where=array('id'=>$id);
				$this->list=M('Wechatgroup')->where($where)->find();
			}
			$this->display();
		}		
	}
	
	//刷新粉丝列表
	public function freshen_1(){		
		$wechat=new WechatAuth();
		$fans=$wechat->getuser();//获取关注者列表
		$fansids=$fans['data']['openid'];//获取关注者openid
		$allusers=M('Wechatfans')->select();//获取数据库中的所有用户
		//获取需要增加的关注用户和已经取消关注的用户
		foreach($allusers as $v){
			$flag=1;
			foreach($fansids as $k1=>$v1){
				if($v['openid']==$v1){
					$flag=2;
					unset($fansids["$k1"]);
					break 1;
				}
			}
			if($flag==1){
				$where=array('openid'=>$v['openid']);
				M('Wechatfans')->where($where)->delete();//删除以取消关注的用户
			}
		}				
		//把新增关注者openid存入数据库
		foreach($fansids as $v){	
			 $data=array('openid'=>$v);		
			 M('Wechatfans')->add($data);
		}
		echo count($fansids);
	}
	
	//刷新粉丝信息
	public function freshen_2(){
		if(IS_POST){
			$wechat=new WechatAuth();
			$arr=$_POST['arr'];	
			$where['id'] = array('in',$arr);			
			$openids=M('Wechatfans')->where($where)->Field('openid')->select();
			$fansmesg=$wechat->getuserinformation($openids);//获取所有关注者信息*/
			$i=0;
			foreach($fansmesg as $v){//循环存入数据库
				$a=$wechat->usergroupname($v['openid']);//获取用户所在分组
				$where=array('openid'=>$v['openid']);
				$data=$v;
				$data['groupid']=$a['groupid'];
				$a=M('Wechatfans')->where($where)->save($data);
				if(!empty($a)){
					$i++;
				}
			}
			echo $i;
		}	
	}
}
