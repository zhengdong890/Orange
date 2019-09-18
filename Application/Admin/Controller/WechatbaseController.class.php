<?php
namespace Admin\Controller;
use Think\Controller;
use my_weixin\WechatAuth;
use my_weixin\jssdk;
header("content-type:text/html;charset=utf-8");
class WechatbaseController extends CommonController{		
	public function index(){
		$this->display();
	}
	
	//关注时回复与帮助
	public function guanzhu(){
		if(IS_POST){
			$data=$_POST;
			$a=M('Guanzhu')->save($data);
			if($a){
				$this->success('操作成功','guanzhu');
			}
		}else{
			$this->list=M('Guanzhu')->find();
			$this->display();
		}		
	}
	
	/****自定义菜单****/
	public function menu(){			
		$list=M('Menu')->select();
		$pmenu=M('Menu')->where('pid='.'0')->order('sort')->select();
		$n=1;
		foreach($pmenu as $k=>$v){//一级菜单
			$pmenu["$k"]=$v;
			$pmenu["$k"]['type']="顶级菜单-";
			if($v['type']==1){
				$pmenu["$k"]['type'].="关键词回复";
			}else
				if($v['type']==2){
				$pmenu["$k"]['type'].="url外链";
			}else{
				$pmenu["$k"]['type'].="扩展菜单";
			}
			$b["$n"]=$pmenu["$k"];
			$a=M('Menu')->where('pid='.$v['id'])->order('sort')->select();
			foreach($a as $k1=>$v1){//二级菜单
				$n++;
				$a["$k1"]['type']="二级菜单-";
				if($v1['type']==1){
					$a["$k1"]['type'].="关键词回复";
				}else
				if($v1['type']==2){
					$a["$k1"]['type'].="url外链";
				}else{
					$a["$k1"]['type'].="扩展菜单";
				}
				$a["$k1"]['url']=PRCsubstr($a["$k1"]['url'],$length=50);
				$b["$n"]=$a["$k1"];
			}
			$n++;
		}
        $this->pmenu=M('Menu')->where('pid='.'0')->select();
		$this->list=$b;
		if($_GET['id']){
			$id=$_GET['id'];
			$where=array('id'=>$id);
			$a=M('Menu')->where($where)->find();
			$a['show']="style='display:block'";
			$this->type_1=$a['type']==1?"selected='selected'":'';
			$this->type_2=$a['type']==2?"selected='selected'":'';
			$this->type_3=$a['type']==3?"selected='selected'":'';
			$this->show_1=$a['type']==1?"display:block":"display:none";
			$this->show_2=$a['type']==2?"display:block":"display:none";
			$this->show_3=$a['type']==3?"display:block":"display:none";
			$this->check_1=$a['isshow']=='y'?"checked='checked'":'';
			$this->check_2=$a['isshow']=='n'?"checked='checked'":'';
			$this->menudata=$a;
		}
		$this->display();
	}
	
	/****增加自定义菜单****/
	public function addmenu(){
		if(IS_POST){
			$data=$_POST;
			$a=M('menu')->add($data);
			if($a){
				$this->success('操作成功','menu');
			}else{
				$this->success('操作失败','menu');
			}
		}
	}
	
	/****修改自定义菜单****/
	public function updatemenu(){
		if(IS_POST){
			$data=$_POST;
			$a=M('menu')->save($data);
			if($a){
				$this->success('操作成功','menu');
			}else{
				$this->success('操作失败','menu');
			}
		}
	}
	
	//删除自定义菜单
	public function deletemenu(){
		if(IS_POST){
			$id=$_POST['id'];
			$result='操作成功';
			$a=M('Menu')->where('id='.$id)->find();
			if($a['pid']!=0){
				$b=M('Menu')->delete($id);
				if(empty($b)){
					$result='操作失败';
				}
			}else{
				$b=M('Menu')->where('pid='.$id)->select();
				foreach($b as $v){
					$b=M('Menu')->delete($v['id']);
				}
				$b=M('Menu')->delete($id);
				if(empty($b)){
					$result='操作失败';
				}
			}
			echo $result;
		}
	}
	
	//发送自定义菜单
	public function sendmenu(){
		$wechat=new WechatAuth();
        $where=array('pid'=>0);
		$a=M('Menu')->where($where)->select();
		foreach ($a as $k=>$v) {
			$where=array('pid'=>$v['id']);
			$b=M('Menu')->where($where)->order('id')->select();			
			$a["$k"]['twomenu']=$b;			
		}
		$result=$wechat->automenu($a);	
		dump($result);die;
	    if($result['errmsg']=='ok'){
	    	$this->success('操作成功','menu');
		}else{	
			$this->error($result['errmsg'],'menu');
		}
	}
	
	//取消自定义菜单
	public function deletewxmenu(){
		$wechat=new WechatAuth();
		$result=$wechat->deletemenu();
		if($result['errmsg']=='ok'){
			$this->success('操作成功','menu');
		}else{
			$this->error($result['errmsg'],'menu');
		}
	}
	
	//文本回复
	public function text(){	
		$where=array('msg_type'=>'text');
		$this->list=M('autoreplay')->where($where)->select();
		$this->display();
	}
	
	//增加文本回复
	public function addtext(){
		if(IS_POST){
			$data=$_POST;
			$data['time']=time();
			$data['msg_type']='text';
			$a=M('autoreplay')->add($data);
			if($a){
				$this->success('操作成功','text');
			}
		}else{
			$this->display();
		}		
	}
	
	//修改文本
	public function updatetext(){
		if(IS_POST){
			$data=$_POST;
			$data['time']=time();
			$a=M('autoreplay')->save($data);
			if($a){
				$this->success('操作成功','text');
			}
		}else{
			$id=$_GET['id'];
			$this->list=M('autoreplay')->where('id='.$id)->find();
			$this->display();
		}
	}
	
	//删除文本回复
	public function deletetext(){
		if(IS_POST){
		    $id=$_POST['id'];			
			$a=M('autoreplay')->delete($id);
			if(!empty($a)){
				$result='操作成功';
			}else{
				$result='操作失败';
			}
			echo $result;
		}		
	}
	
	//图文消息
	public function image_text(){
		$where=array('msg_type'=>'news');
		$this->list=M('autoreplay')->where($where)->select();
		$this->display();
	}
	
	//增加图文消息
	public function addimage_text(){
		if(IS_POST){
			$data=$_POST;
			$data['msg_type']='news';
		    //上传图片
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 3145728 ;// 设置附件上传大小
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
			// 上传文件
			$info = $upload->upload();
			if(!$info) {// 上传错误提示错误信息
				$data['thumb']=$data['thumb_1'];				
				$data['ispiclocal']='n';			
			}else{// 上传成功
				$data['thumb']=$upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];
				$data['ispiclocal']='y';
			}	
			$data['time']=time();
			$a=M('autoreplay')->add($data);
			if($a){
				$this->success('操作成功','image_text');
			}
		}else{			
			$this->display();
		}
	}
	
	//删除图文回复
	public function deleteimg_text(){
		if(IS_POST){
			$id=$_POST['id'];
			$oldthumb=M('autoreplay')->where('id='.$id)->getField('thumb');
			$a=M('autoreplay')->delete($id);
			if(!empty($a)){
				unlink($oldthumb);
				$result='操作成功';
			}else{
				$result='操作失败';
			}
			echo $result;
		}
	}
	
	//修改图文消息
	public function updateimage_text(){
		if(IS_POST){
			$data=$_POST;
			$oldthumb=$data['oldthumb'];
			unset($data['oldthumb']);
			 //上传图片
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 3145728 ;// 设置附件上传大小
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
			// 上传文件
			$info = $upload->upload();
			if(!$info) {//无图片上传				
				if(!empty($data['thumb_1'])){
					$data['thumb']=$data['thumb_1'];
					$data['ispiclocal']='n';
					unlink($oldthumb);
				}
			}else{// 上传成功
				$data['thumb']=$upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];
				$data['ispiclocal']='y';
				unlink($oldthumb);
			}	
			unset($data['thumb_1']);
			$data['time']=time();
			$a=M('autoreplay')->save($data);
			if($a){
				$this->success('操作成功','image_text');
			}
		}else{
			$id=$_GET['id'];
			$this->list=M('autoreplay')->where('id='.$id)->find();
			$this->display();
		}
	}
	
	//多图文消息
	public function image_texts(){
		$where=array('msg_type'=>'news');
		$this->list=M('autoreplay')->where($where)->select();
		$where=array('msg_type'=>'morenews');
		$this->info=M('autoreplay')->where($where)->select();
		$this->display();
	}
	
	//选中图文消息
	public function selectimage_texts(){
		$allid=$_POST['arr'];
		$allid=json_decode($allid);
		$list='';
		$k=1;
        foreach($allid as $v){
        	$where=array('id'=>$v,'msg_type'=>'news');
        	$a=M('autoreplay')->where($where)->find();
        	$thumb=$a['thumb'];
        	if($a['ispiclocal']=='y'){
        		$thumb=substr($a['thumb'],1);
        		$thumb="/myself".$thumb;
        	}
        	
        	if($k==1){
        		$list.="<li class='li'>
                             <a>
                                <img style='margin-top:0;margin-right:0' src='".$thumb."'></img>
                                <p style='margin-left:0'>".$a['content']."</p>
                              </a>
                      </li>";
        	}else{
        		$list.="<li>
                               <p>".$a['content']."</p>
                               <img src='".$thumb."'></img>
                        </li>";
        	}
        	$k++;
        }
        echo $list;
	}
	
	//增加多图文消息
	public function addimage_texts(){
		if(IS_POST){
			$allid=$_POST['arr'];
			$allid=json_decode($allid);
			$sid=implode('-',$allid);
			$data['sid']=$sid;
			$data['keyword']=$_POST['keyword'];
			$data['msg_type']='morenews';			
			$a=M('autoreplay')->add($data);
			if($a){
				echo '操作成功';
			}else{
				echo '操作失败';
			}
		}
	}
	
	//修改多图文消息
	public function updateimage_texts(){
		if(IS_POST){			
			$data=$_POST;
			$a=M('autoreplay')->save($data);
			if($a){
				$this->success('操作成功','image_texts');
			}else{
				$this->success('操作失败','image_texts');
			}
		}else{
			$id=$_GET['id'];
			$list=M('autoreplay')->where('id='.$id)->find();
		    if(!empty($list['sid'])){
		    	$allid=explode('-',$list['sid']);
				foreach($allid as $k=>$v){
					$a=M('autoreplay')->where('id='.$v)->find();
					$a['url']=PRCsubstr($a['url'],$length=50);
					$imgtext["$k"]=$a;
					
				}
				$this->imgtext=$imgtext;
		    }	
		    $where=array('msg_type'=>'news');
		    $this->info=M('autoreplay')->where($where)->select();
			$this->list=$list;
			$this->display();
		}
	}
	
	//删除多图文消息
	public function deleteimage_texts(){
		if(IS_POST){
			$id=$_POST['id'];
			$a=M('autoreplay')->delete($id);
			if($a){
				echo '操作成功';
			}else{
				echo '操作失败';
			}
		}
	}
	
	//删除多图文消息下的图文消息
	public function deleteimage_textsson(){
		if(IS_POST){
			$id=$_POST['id'];
			$sid=$_POST['sid'];
			$oldsid=M('autoreplay')->where('id='.$id)->getField('sid');
			$oldsid=explode('-',$oldsid);
			foreach($oldsid as $k=>$v){				
			    if($v==$sid){
						unset($oldsid["$k"]);
						break 1;
				}
			}
			$data['id']=$id;
			$data['sid']=implode('-',$oldsid);
			$a=M('autoreplay')->save($data);
			if($a){
				echo '操作成功';
			}else{
				echo '操作失败';
			}		   
		}
	}
	
	//增加多图文消息下的图文消息
	public function addimage_textsson(){
		if(IS_POST){
			$id=$_POST['id'];
			$sid=$_POST['sid'];
			$oldsid=M('autoreplay')->where('id='.$id)->getField('sid');
			if(!empty($oldsid)){
				$oldsid.='-';
			}
			$oldsid.=$sid;
			$newsid=explode('-',$oldsid);			
			$data['id']=$id;
			$data['sid']=implode('-',$newsid);
			$a=M('autoreplay')->save($data);
		    if($a){
				echo '操作成功';
			}else{
				echo '操作失败';
			}
		}
	}
	
	//语音回复
	public function voice(){
		$where=array('msg_type'=>'voice');
		$this->list=M('Autoreplay')->where($where)->select();
		$this->display();
	}
	
	//增加语音回复
	public function addvoice(){
		if(IS_POST){
			$data=$_POST;
			$data['msg_type']='voice';
		    //上传图片
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize =0;// 设置附件上传大小
			$upload->exts = array('jpg', 'gif','png','jpeg','mp3');// 设置附件上传类型
			$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
			// 上传文件
			$info = $upload->upload();
			if(!$info) {// 上传错误提示错误信息
				$data['thumb']=$data['thumb_1'];				
				$data['ispiclocal']='n';			
			}else{// 上传成功
				$data['thumb']=$upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];
				$data['ispiclocal']='y';
			}	
			$data['time']=time();
			$a=M('autoreplay')->add($data);
			if($a){
				$this->success('操作成功','voice');
			}
		}else{
			$this->display();
		}		
	}
	
	//修改语音回复
	public function updatevoice(){
		if(IS_POST){
		    $data=$_POST;
			$oldthumb=$data['oldthumb'];
			unset($data['oldthumb']);
			 //上传音乐
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize =0;// 设置附件上传大小
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg','mp3');// 设置附件上传类型
			$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
			// 上传文件
			$info = $upload->upload();
			if(!$info) {//无音乐上传				
				if(!empty($data['thumb_1'])){
					$data['thumb']=$data['thumb_1'];
					$data['ispiclocal']='n';
					unlink($oldthumb);
				}
			}else{// 上传成功
				$data['thumb']=$upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];
				$data['ispiclocal']='y';
				unlink($oldthumb);
			}	
			unset($data['thumb_1']);
			$data['time']=time();
			$a=M('autoreplay')->save($data);
			if($a){
				$this->success('操作成功','voice');
			}
		}else{
			$id=$_GET['id'];
			$where=array('id'=>$id);
			$this->list=M('Autoreplay')->where($where)->find();
			$this->display();
		}
	}
	
	//删除语音
	public function deletevoice(){
		if(IS_POST){
			$id=$_POST['id'];
			$oldthumb=M('autoreplay')->where('id='.$id)->getField('thumb');
			$a=M('autoreplay')->delete($id);
			if(!empty($a)){
				unlink($oldthumb);
				$result='操作成功';
			}else{
				$result='操作失败';
			}
			echo $result;
		}
	}
	
	//群发回复
	public function sendallusers(){
		$this->display();
	}
	
	//增加群发回复
	public function msgs_add(){
/* 		$wechat=new WechatAuth();
		$msg=M('Autoreplay')->where(array('id'=>12))->find();
		$content=msg_send($msg);
		$result=$wechat->send_news('oyFYQs9tjfdofgJVe8GfzqPO9Sm4',$content);
		dump($result); */
		$this->display();
	}
	
	/*ajax发送群发消息*/
	public function msgs_send(){
		if(IS_POST){
			$groupid=I('groupid');
			$msg_id=I('msg_id');
			if($groupid==0){
				session_start();
				if(empty($_SESSION['msgs'])){
					$msgs['openid']=M('Wechatfans')->Field('openid')->select();//获取需要发送人的openid
					$msg=M('Autoreplay')->where(array('id'=>$msg_id))->find();//获取需要发送的消息
					$content=msg_send($msg);//转成微信格式
					$msgs['content']=$content;
					$_SESSION['msgs']=$msgs;
				}
				$msgs=$_SESSION['msgs'];
				$wechat=new WechatAuth();
				foreach($msgs['openid'] as $k=>$v){
					$res=$wechat->send_news($v,$msgs['content']);
					if($res['errmsg']=='ok'){
						$result['success']='y';
					}else{
						$result['success']='n';
					}
					unset($msgs['openid']["$k"]);
					break;
				}
			}
		}
	}
	
	//ajax根据消息类型显示消息
	public function show_msgs_html(){
		if(IS_POST){
			$type=I('type');
			$list=M('Autoreplay')->where(array('msg_type'=>$type))->select();
			$html="<tr>
						<td class='td'>标题</td>
						<td>关键词</td>
						<td>操作</td>
				   </tr>";
			foreach($list as $k=>$v){
				$html.="<tr>
				           <td class='td'>{$v['title']}</td>
					       <td>{$v['keyword']}</td>
						   <td><a href='javascript:void(0)' onclick='set_msgs({$v['id']},this)'>选中</a></td>
						</tr>";
			}
			echo $html;
		}
	}
	
	//模板消息
	public function templetmsg(){
		/*$data=array('touser'=>'oyFYQs9tjfdofgJVe8GfzqPO9Sm4',
				    'template_id'=>'L28yNS7b5jsFfjL47wD7vtRZTc5QYJAeYCakJ11std8',
				    'url'=>'http://weixin.qq.com/download',
		            'topcolor'=>'#FF0000',
					'data'=>array('first'=>array('value'=>'恭喜你购买成功！','color'=>'#173177'),
					              'product'=>array('value'=>'39元','color'=>'#173177'),
							      'remark'=>array('value'=>'欢迎再次购买！','color'=>'#173177'),
								  )		
		            );
		$wechat=new WechatAuth();
        dump($wechat->templetmsg($data));*/
		$this->display();
	}
	
//回答不上来的配置
	public function notkeyword(){
		if(IS_POST){
			$data=$_POST;
            $data['id']=M('Nokeyword')->getField('id');           
			if(M('Nokeyword')->save($data)){
				$this->success('操作成功','notkeyword');
			}else{
				$this->error('操作失败','notkeyword');
			}
		}else{
			$this->list=M('Nokeyword')->find();
			$this->display();
		}		
	}

}