<?php
namespace WeChat\Controller;
use Think\Controller;
use my_weixin\WechatAuth;
use my_weixin\jssdk;
header("content-type:text/html;charset=utf-8");
class KanjiaController extends Controller{		
    /*直接通过公众号进入*/
    public function index(){   	
       if(IS_POST){//参加活动
	       	$a=M('Kanjia')->where(array('openid'=>$_SESSION['openid']))->find();
	       	$error='';
	       	/*判断用户是否关注*/
	       	$b = M("wechatfans")->where(array("openid"=>$_SESSION['openid']))->find();
	       	$error=empty($_SESSION['openid'])?'无法获取您的信息':(!empty($a)?'您已经参加活动啦!请勿重复参加!':(empty($b)?'请您关注后再试!':$error));
	       	if(!$error){
	       		$data['openid']=$_SESSION['openid'];
	       		$id=M('Kanjia')->add($data);
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
    		$a=M('Kanjia')->where(array('openid'=>$_SESSION['openid']))->find();//查看该粉丝是否参加活动
    		if($a){
    			$this->is_canjia='y';//y表示显示已经参加
    			/*获取粉丝信息*/
    			$fansmesg=$wechat->getuserinformation(array(array('openid'=>$a['openid'])));
    			$this->fansmesg=$fansmesg['0'];
    			$this->data=$a;//获取粉丝助力信息
    		}else{
    			$this->is_canjia='n';//n表示未参加
    		}
    		$this->is_me='y';
    		/*jssdk设置转发信息*/
    		$jssdk=new jssdk();
    		$jssdk=$jssdk->getSignPackage();
    		$url="http://www.hailuohao.net/myself/index.php/WeChat/Kanjia/kanjia";
    		$jssdk['url']=$url.'?popenid='.$_SESSION['openid'];//设置转发的url链接地址
    		$jssdk['imgurl']="http://www.hailuohao.net/myself/public/images/xyjt/header.png";//转发的图像
    		$jssdk['desc']="hi!我是".$this->fansmesg['nickname'].",请帮我砍价,谢谢!";//转发的描述内容
    		$this->jssdk=$jssdk;		
    		/*判断粉丝是否关注*/
    		$b = M("wechatfans")->where(array("openid"=>$_SESSION['openid']))->find();
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
   public function kanjia(){
   	    $popenid=$_GET['popenid'];
   	    //如果是GET过来的设置授权页面链接并自动跳转到该页面
    	if(!empty($popenid)){
    		unset($_SESSION["popenid"]);
    		$_SESSION["popenid"]=$popenid;//保存传过来的popenid
    		$url_='http://www.hailuohao.net/myself/index.php/WeChat/Kanjia/kanjia';
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
    		$a=M('Kanjia')->where(array('openid'=>$_SESSION['popenid']))->find();//查看该粉丝是否参加活动
    		if($a){
    			$fansmesg=$wechat->getuserinformation(array(array('openid'=>$_SESSION["popenid"])));
    			$this->fansmesg=$fansmesg['0'];
    			$this->is_canjia='y';//y表示显示已经参加
    			$this->data=$a;//获取助力信息
    		}else{
    			$this->is_canjia='n';//n表示未参加
    		}   
    		/*jssdk设置转发信息*/
    		$jssdk=new jssdk();
    		$jssdk=$jssdk->getSignPackage();
    		$url="http://www.hailuohao.net/myself/index.php/WeChat/Kanjia/kanjia";
    		$jssdk['url']=$url.'?popenid='.$_SESSION['popenid'];//设置转发的url链接地址
    		$jssdk['imgurl']="http://www.hailuohao.net/myself/public/images/header.png";//转发的图像
    		$jssdk['desc']="hi!我是".$this->fansmesg['nickname'].",请帮我砍价,谢谢!";//转发的描述内容
    		$this->jssdk=$jssdk;
    		/*判断是否为自己的页面*/
    		if($_SESSION["popenid"]==$_SESSION["openid"]){
    			$this->is_me='y';
    		}
    		/*判断粉丝是否关注*/
    		$b = M("wechatfans")->where(array("openid"=>$_SESSION['openid']))->find();
    		if($b){
    			$this->is_guanzhu='y';
    		}
    		/*判断被助力的用户是否关注*/
    		$b = M("wechatfans")->where(array("openid"=>$_SESSION['popenid']))->find();
    		if($b){
    			$this->is_pguanzhu='y';
    		}
    		$this->display('index');
    	}  	
   }

   /*ajax助力砍价*/
   public function kanjia_1(){
    	if(IS_POST){//参加活动
    		$result='';
    		/*判断助力的用户是否关注*/
    		$a = M("wechatfans")->where(array("openid"=>$_SESSION['openid']))->find();
    		/*判断被助力的用户是否关注*/
    		$b = M("wechatfans")->where(array("openid"=>$_SESSION['popenid']))->find();
    		/*判断是否重复助力砍价*/
    		$c=M('Kanjia_data')->where(array('openid'=>$_SESSION['openid'],'toopenid'=>$_SESSION["popenid"]))->find();
    		$data["tishi"]='';    		
    		$data["error"]=empty($b)?'被砍价的该粉丝已经取消关注,无法砍价':(empty($c)?'':($_SESSION['openid']==$_SESSION['popenid']?'您已经砍过价了,请勿重复操作！':'您已经为TA砍过价了,请勿重复操作 ！'));  
    		/*判断价格是否为0*/
    		$d=M('Kanjia')->where(array('openid'=>$_SESSION["popenid"]))->find();
    		$data["error"]=($d['price']==0)?'价格已经为0,无法在继续砍价':$data["error"];
    		if(!$data["error"]){ 
    		 	/*设置砍价的随机价格*/
    		 	$arr=array('1'=>'10','2'=>'11','3'=>'12','4'=>'13','5'=>'14','6'=>'15','7'=>'16','8'=>'17','9'=>'18','10'=>'19');
    		 	$arr_1=array(10,10,10,10,10,10,10,10,10,10,10);
    		 	$result=get_rand($arr_1)+1;
    		 	$result=$arr["$result"];//随机结果
    		 	$id=M('Kanjia_data')->add(array('openid'=>$_SESSION['openid'],'toopenid'=>$_SESSION["popenid"]));
    			if($id){   				
    				if(($d['price']-$result)<=0){
    					$result=$d['price'];
    				}
    				M('Kanjia')->where(array('openid'=>$_SESSION["popenid"]))->setInc('number',1);//砍价数+1
    				M('Kanjia')->where(array('openid'=>$_SESSION["popenid"]))->setDec('price',$result);//更新价格
    				if($_SESSION['openid']==$_SESSION['popenid']){
    					$data["tishi"]="您成功帮自己砍掉".$result."元";
    				}else{
    					$data["tishi"]="您成功帮".$b['nickname']."砍掉".$result."元";
    				}    				
    			}else{
    				$data["tishi"]='砍价失败';
    			}
    		}
    		$this->ajaxReturn($data);
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