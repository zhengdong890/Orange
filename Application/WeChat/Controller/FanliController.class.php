<?php
namespace WeChat\Controller;
use Think\Controller;
use my_weixin\WechatAuth;
use my_weixin\jssdk;
header("content-type:text/html;charset=utf-8");
class FanliController extends Controller{	 
    /*返利活动通过公众号直接进入*/
    public function fanli(){  	      	       
        $jssdk=new jssdk();
        $jssdk=$jssdk->getSignPackage();
        $url="http://www.hailuohao.net/my_weixin/index.php/Home/Play_fanli/fanli_jianjie";
        $jssdk['url']=$url.'?popenid='.$openid;//设置转发的url链接地址
        $jssdk['imgurl']="http://www.hailuohao.net/my_weixin/public/images/tb.png";       
        $this->jssdk=$jssdk;
        $this->display();	
    }
    
    /*返利活动通过转发间接进入*/
    public function fanli_jianjie(){
    	session_start();
        unset($_SESSION["openid"]);//清除popenid的缓存        
    	$popenid=$_GET['popenid'];
    	//如果是GET过来的设置授权页面链接并自动跳转到该页面
    	if(!empty($popenid)){
    		$_SESSION["popenid"]=$popenid;//保存传过来的popenid  			  		
    		$url_='http://www.hailuohao.net/my_weixin/index.php/Home/Play_fanli/fanli_jianjie';
    		$url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=".$url_.'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect';
    		header("location:$url");//跳转到授权页面用于获取用户openid
    	}else{
    		//如果是header跳转过来的则直接获取用户信息
    		$wechat=new WechatAuth();//
    		$data=$wechat->getuserdata();//获取用户信息
    		$openid=$data["openid"];//获取用户openid
    		$a=M("wechatfans")->where(array("openid"=>$openid))->find();
    		if(!empty($a)){//如果关注了则设置session openid
    			$_SESSION["openid"]=$openid;
    			$this->isguanzhu=1;
    		}
    		$jssdk=new jssdk();
	        $jssdk=$jssdk->getSignPackage();
	        $url="http://www.hailuohao.net/my_weixin/index.php/Home/Play_fanli/fanli_jianjie";	
	        $jssdk['url']=$url.'?popenid='.$_SESSION["popenid"];//设置转发的url链接地址	       
	        $jssdk['imgurl']="http://www.hailuohao.net/my_weixin/public/images/tb.png";
	        /*获取转发的粉丝信息*/
	        $wechat=new WechatAuth();
	        $fansmesg=$wechat->getuserinformation(array(array('openid'=>$_SESSION["popenid"])));
	        $this->fansmesg=$fansmesg['0']; 
    	    $a=M("wechatfans")->where(array("openid"=>$_SESSION["popenid"]))->find();
    		if(!empty($a)){
    			$this->ispguanzhu=1;//该优惠券页面的人是否关注(1为关注)
    		}    	       
	        $this->jssdk=$jssdk;
	        $url_='http://www.hailuohao.net/my_weixin/index.php/Home/Play_fanli/fanli';
	        $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=".$url_.'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect';
	        $this->url=$url;
	        /*判断是否是自己的页面*/
	        if($_SESSION["openid"]==$_SESSION["popenid"]){
	        	$this->iszj=1;
	        }
	        /*作假*/
	        $zj=M('Totalfanli')->find();
	        $zj['ylq']=5*$zj['number'];
	        $this->zj= $zj;
    		$this->display('fanli');
    	}    	   	
    }
    
    //领取优惠卷
    public function getyouhui(){
    	if(IS_POST){
    		if(empty($_SESSION["openid"])){
    			$result='请关注后再执行此操作';
    		}else{
    			$a=M('Fansbeifen')->where(array('openid'=>$_SESSION["openid"]))->find();
    			if(empty($a)){
    				M('Fansbeifen')->add(array('openid'=>$_SESSION["openid"],'islq'=>1));
    				$result='领取欢乐共享券成功,请将此网页分享给朋友';
    				//获取粉丝信息
    				$wechat=new WechatAuth();
    				$fansmesg=$wechat->getuserinformation(array(array('openid'=>$_SESSION["popenid"])));
    				$nickname=$fansmesg['0']['nickname'];
    				$headimgurl=$fansmesg['0']['headimgurl'];
    				$data['html']="<li class='headerimg'><p><img src='$headimgurl'/></p></li>
    				               <li>{$nickname}的欢乐共享券</li>";
    				
    			}else{
    				if($a['islq']==1){
    					$result='您已经领取了，请勿重复领取';
    				}else{
    					M('Fansbeifen')->save(array('id'=>$a["id"],'islq'=>1));
    					$result='领取欢乐共享券成功,请将此网页分享给朋友';
    					//获取粉丝信息
    					$wechat=new WechatAuth();
    					$fansmesg=$wechat->getuserinformation(array(array('openid'=>$_SESSION["popenid"])));   					
    					$nickname=$fansmesg['0']['nickname'];
    					$headimgurl=$fansmesg['0']['headimgurl'];    					
    					$data['html']="<li class='headerimg'><p><img src='$headimgurl'/></p></li>
		                                 <li>$nickname的欢乐共享券</li>"; 
    				}
    			}   			
    		}
    		$data['result']=$result;
    		$this->ajaxReturn($data);
    	}
    }
    
    //判断是否领取优惠券
    public function panduanislq(){
    	if(IS_POST){
    		$a=M('Fansbeifen')->where(array('openid'=>$_SESSION["openid"]))->find();
    		if(empty($a)){
    			$result='您还未领券,分享的页面无效';
    		}
    	}
    }
    
    //验证激活码
    public function yanzheng(){
    	if(IS_POST){
    		session_start(); 
    		$data['code']=$_POST['code'];
    		$data['openid']=$_SESSION["openid"];
    		$data['popenid']=$_SESSION["popenid"];
    		$a=M("wechatfans")->where(array("openid"=>$data['popenid']))->find();
    		if(empty($a)){//如果关注了则设置session openid
    			$data['popenid']='';
    		}
    		$result='';    		    		
    		$result=$data['openid']==$data['popenid']?'不能再自己的页面执行此操作':$result;//判断该用户是否在自己的页面
    		$result=empty($data['openid'])?'您还未关注我们,请关注后再来执行此操作':$result;//判断该用户是否关注
    		$result=empty($data['popenid'])?'转发给您的微信号未关注我们,请提醒对方关注后再来执行此操作':$result;//判断推荐用户是否关注
    		/*判断转发的人是否领取欢乐共享券*/
    		$a=M('Fansbeifen')->where(array('openid'=>$_SESSION["popenid"]))->find();
    		$result=$a['islq']!=1?'转发给您的微信号未领取欢乐共享券,请提醒对方领取后再来执行此操作':$result;//判断转发的人是否领取欢乐共享券
    		if(!$result){
    			$a=M('Fanli')->where(array('code'=>$data['code']))->find();
    			$result=empty($a)?'该激活码不存在':(time()-$a['sctime']>180?'该激活码以过期':(!empty($a['openid'])?'该激活码以使用':$result));
    		}
    		if(!$result){
    			$data['id']=$a['id'];
    			$data['time']=time();
    			$a=M('Fanli')->save($data);
    			if($a){
    				$wechat=new WechatAuth();
    				/*发送通知给上家*/
    				$nickname=M('Wechatfans')->where(array('openid'=>$data['openid']))->getField('nickname');
    				$result="恭喜您推荐的用户:$nickname 在本店消费,您将获得5元返利(请到中方门店领取,地址:芙蓉区东牌楼新世界商贸城A栋附一楼,五一平和堂后侧。最终解释权归本公司所有),电话:84431535";
    				$wechat->send_text($data['popenid'],$result);//微信通知用户
    				/*发送通知给输入激活码的人*/
    				$nickname=M('Wechatfans')->where(array('openid'=>$data['popenid']))->getField('nickname');
    				$result="恭喜您成功获得5元优惠券:在本店消费,转发给您页面的用户($nickname)将获得5元返利(编码:".$a['id'].")";
    				$wechat->send_text($data['openid'],$result);//微信通知用户
    				$result='操作成功';
    				$a=M('Totalfanli')->find();
    				$a['number']++;
    				M('Totalfanli')->save($a);
    			}else{
    				$result='操作出现错误';
    			}
    		}
    		echo $result;
    		//$this->ajaxReturn($result);
    	}
    }
}
