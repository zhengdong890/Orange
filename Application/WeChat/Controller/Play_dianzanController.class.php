<?php
namespace Home\Controller;
use Think\Controller;
use my_weixin\WechatAuth;
use my_weixin\jssdk;
header("content-type:text/html;charset=utf-8");
class Play_dianzanController extends Controller{		
    /*直接通过公众号进入*/
    public function dianzan(){
    	if(IS_POST){//参加活动
	    		$a=M('Dianzan')->where(array('openid'=>$_SESSION['openid']))->find();
    			$error='';
    			/*判断用户是否关注*/
    	 		$b = M("wechatfans")->where(array("openid"=>$_SESSION['openid']))->find();
    			$error=empty($_SESSION['openid'])?'无法获取您的信息':(!empty($a)?'您已经参加活动了!请勿重复参加':(empty($b)?'请您关注后再试':$error)); 
    			if(!$error){
    				$data['openid']=$_SESSION['openid'];
    				$id=M('Dianzan')->add($data);
    				if($id){
    					$error="参加成功";
    					session_start();
    					$_SESSION['openid']=$data['openid'];//设置用户openid
    					$_SESSION['popenid']=$data['openid'];//设置用户openid
    				}else{
    					$error="参加失败";
    				}
    			}   
    			echo $error;
    	}else{
    		$wechat=new WechatAuth();//实例化对象
    		$data=$wechat->getuserdata();//获取用户信息
    		session_start();
    		if($data['openid']){
    			unset($_SESSION["openid"]);
    			unset($_SESSION["popenid"]);
    			$_SESSION['openid']=$data['openid'];//设置用户openid
    			$_SESSION['popenid']=$data['openid'];//设置用户popenid
    		} 
    		$a=M('Dianzan')->where(array('openid'=>$_SESSION['openid']))->find();//查看该粉丝是否参加活动
    		if($a){
    			$this->is_showbtn='y';//y表示显示已经参加
    			/*获取粉丝信息*/
    			$fansmesg=$wechat->getuserinformation(array(array('openid'=>$a['openid'])));
    			$this->fansmesg=$fansmesg['0'];
    			$this->number=$a['number'];
    			$a['sy_number']=$a['number']-$a['number_1'];
    			$this->data=$a;//获取粉丝助力信息
    		}else{
    			$this->is_showbtn='n';//n表示未参加
    		}
    		$this->is_me='y';
    		/*jssdk设置转发信息*/
    		$jssdk=new jssdk();
    		$jssdk=$jssdk->getSignPackage();
    		$url="http://www.hailuohao.net/my_weixin/index.php/Home/Play_dianzan/dianzan_";
    		$jssdk['url']=$url.'?popenid='.$_SESSION['openid'];//设置转发的url链接地址
    		$jssdk['imgurl']="http://www.hailuohao.net/my_weixin/public/images/header.png";//转发的图像
    		$jssdk['desc']="hi!我是".$this->fansmesg['nickname'].",请帮我助力,谢谢!";//转发的描述内容
    		$this->jssdk=$jssdk;		
    		/*判断粉丝是否关注*/
    		$b = M("wechatfans")->where(array("openid"=>$_SESSION['popenid']))->find();
    		if($b){
    			$this->is_guanzhu='y';
    		} 
    		/*判断被助力的用户是否关注*/
    		$b = M("wechatfans")->where(array("openid"=>$_SESSION['popenid']))->find();
    		if($b){
    			$this->is_pguanzhu='y';
    		}   	
    		$this->display();
    	}
    }
    
    /*通过转发进入*/
   public function dianzan_(){
   	    $popenid=$_GET['popenid'];
   	    //如果是GET过来的设置授权页面链接并自动跳转到该页面
    	if(!empty($popenid)){
    		unset($_SESSION["popenid"]);
    		$_SESSION["popenid"]=$popenid;//保存传过来的popenid
    		$url_='http://www.hailuohao.net/my_weixin/index.php/Home/Play_dianzan/dianzan_';
    		$url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=".$url_.'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect';
    		header("location:$url");//跳转到授权页面用于获取用户openid
    	}else{
    		//如果是header跳转过来的则直接获取用户信息
    		$wechat=new WechatAuth();//
    		$data=$wechat->getuserdata();//获取用户信息
    	    if($data['openid']){
    	     	$_SESSION["openid"]=$data["openid"];//保存当前粉丝的openid 
    		}  
    		/*获取转发人的信息*/   		
    		$a=M('Dianzan')->where(array('openid'=>$_SESSION['popenid']))->find();//查看该粉丝是否参加活动
    		if($a){
    			$fansmesg=$wechat->getuserinformation(array(array('openid'=>$_SESSION["popenid"])));
    			$this->fansmesg=$fansmesg['0'];
    			$this->is_showbtn='y';//y表示显示已经参加
    			$a['sy_number']=$a['number']-$a['number_1'];
    			$this->number=$a['number'];/*获取投票数*/
    			$this->data=$a;//获取粉丝助力信息
    		}else{
    			$this->is_showbtn='n';//n表示未参加
    		}   
    		/*jssdk设置转发信息*/
    		$jssdk=new jssdk();
    		$jssdk=$jssdk->getSignPackage();
    		$url="http://www.hailuohao.net/my_weixin/index.php/Home/Play_dianzan/dianzan_";
    		$jssdk['url']=$url.'?popenid='.$_SESSION['popenid'];//设置转发的url链接地址
    		$jssdk['imgurl']="http://www.hailuohao.net/my_weixin/public/images/header.png";//转发的图像
    		$jssdk['desc']="hi!我是".$this->fansmesg['nickname'].",请帮我助力,谢谢!";//转发的描述内容
    		$this->jssdk=$jssdk;
    		/*判断是否为自己的页面*/
    		if($_SESSION["popenid"]==$_SESSION["openid"]){
    			$this->is_me='y';
    		}
    		/*判断被助力的用户是否关注*/
    		$b = M("wechatfans")->where(array("openid"=>$_SESSION['popenid']))->find();
    		if($b){
    			$this->is_pguanzhu='y';
    		}
    		$this->display('dianzan');
    	}  	
   }

   /*ajax助力*/
   public function dianzan_1(){
    	if(IS_POST){//参加活动
    		$result='';
    		/*判断助力的用户是否关注*/
    		$a = M("wechatfans")->where(array("openid"=>$_SESSION['openid']))->find();
    		/*判断被助力的用户是否关注*/
    		$b = M("wechatfans")->where(array("openid"=>$_SESSION['popenid']))->find();
    		/*判断是否重复点赞*/
    		$c=M('Dianzandata')->where(array('openid'=>$_SESSION['openid'],'toopenid'=>$_SESSION["popenid"]))->find();
    		$result=empty($a)?'您暂未关注,无法参加助力':(empty($b)?'被助力的该粉丝已经取消关注,无法助力':(empty($c)?'':($_SESSION['openid']==$_SESSION['popenid']?'您已经助力了,请勿重复助力！':'您已经为TA助力过了,请勿重复助力 ！')));  
    		 if(!$result){ 
    			$id=M('Dianzandata')->add(array('openid'=>$_SESSION['openid'],'toopenid'=>$_SESSION["popenid"]));
    			if($id){
    				M('Dianzan')->where(array('openid'=>$_SESSION["popenid"]))->setInc('number',1);//助力数+1
    				$result='助力成功';
    			}
    		}
    		echo $result;
    	}
    }
    
    /*ajax兑换优惠卷*/
    public function youhui_add(){
        if(IS_POST){
    	    $data=M('Dianzan')->where(array('openid'=>$_SESSION['openid']))->find();
    	    $sy_number=$data['number']-$data['number_1'];
    	    $result=$sy_number<10?'点数不够，无法兑换':'';
    	    if(empty($result)){
    	    	$youhui_number=floor($sy_number/10);
    	    	$number_1=$youhui_number*10;
    	    	M('Dianzan')->id=$data['id'];
    	    	M('Dianzan')->youhui_number = array('exp',"youhui_number+$youhui_number");//更新优惠卷数量
    	    	M('Dianzan')->number_1 = array('exp',"number_1+$number_1");//更新已用点数
    	    	$a=M('Dianzan')->save();
                if($a){
                	$result='兑换成功';
                }
    	    } 
    	    echo $result; 	    
    	}
    }
    
    /*领取优惠券*/
    public function get_youhui(){
    	if($_GET['flag']==1){
    		$url_='http://www.hailuohao.net/my_weixin/index.php/Home/Play_dianzan/get_youhui';
    		$url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=".$url_.'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect';
    		header("location:$url");//跳转到授权页面用于获取用户openid
    	}   	
    	$wechat=new WechatAuth();//实例化对象
    	$data=$wechat->getuserdata();//获取用户信息
    	/*判断用户是否关注*/
    	$a = M("wechatfans")->where(array("openid"=>$data['openid']))->find();
    	/*判断用户是否参加*/
    	$b = M('Dianzan')->where(array('openid'=>$data['openid']))->find();
    	$result=empty($data['openid'])?'无法获取到您的微信信息':(empty($a)?'您未关注我们无法领取':(empty($b)?'您还未参加活动':''));
    	if(empty($result)){
    		$get_num=$b['youhui_number'];
    		if($get_num>0){
    			$a=M('Dianzan')->where(array("openid"=>$data['openid']))->save(array('youhui_number'=>0));//优惠卷数量清0
    			if($a){
    				$result="恭喜您，成功领取饮品券{$get_num}";
    				/*获取用户个人信息*/
    				$fansmesg=$wechat->getuserinformation(array(array('openid'=>$data['openid'])));
    				$fansmesg=$fansmesg['0'];
    				$content=$fansmesg['nickname']."成功领取饮品券".$get_num."张";
    				$wechat->send_text("oj0eNwmzIRwRVBblXS_EZFbq1TGU",$content);
    			}
    		}else{
    			$result="饮品券数量为0，无法领取";
    		}   		
    	}     	
    	echo "<script language='javascript'>alert('".$result."')</script>";
    }
}