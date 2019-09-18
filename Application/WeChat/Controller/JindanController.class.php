<?php
namespace WeChat\Controller;
use Think\Controller;
use my_weixin\WechatAuth;
use my_weixin\jssdk;
header("content-type:text/html;charset=utf-8");
class JindanController extends Controller{
    public function index($id = ''){
    	$wechat=new WechatAuth();
    	$data=$wechat->getuserdata();
    	if(!empty($data['openid'])){
    		unset($_SESSION['openid']);
    		session_start();
    		$_SESSION['openid']=$data['openid'];
    	}
        $this->display();
    }
    
    /*砸金蛋活动中奖结果*/
    public function jindan(){ 
    	if(IS_POST){
    		$ziduan="jindan_".$_POST['n'];
    		$wechat=new WechatAuth();
   	        session_start();
    		$is_guanzhu=M('Wechatfans')->where(array('openid'=>$_SESSION['openid']))->find();//判断粉丝是否关注
    		$a=M('Jindan')->where(array('openid'=>$_SESSION['openid']))->find();//获取粉丝活动信息
    		$data['error']=empty($_SESSION['openid'])?'无法获取到您的微信账号信息':(empty($is_guanzhu)?'您只有关注后才能参与活动':($a["$ziduan"]!=1?'该蛋已经砸过啦!':''));
    		if(empty($data['error'])){
    			$arr=array('1'=>'爆米花一份','2'=>'饮料2瓶','3'=>'10元抵用券',
    					'4'=>'20元抵用券','5'=>'观影6折优惠券','6'=>'谢谢参与');
    			/*三个蛋设置最多且最少中一次*/
    			$b=M("Jindan_result")->where(array('openid'=>$_SESSION['openid']))->find();//该粉丝是否已经中奖
    			//设置概率数组
    			if(empty($b)){//如果没有中过奖
    				$sum=3;
    				if($a['jindan_1']!=1){
    					$sum--;
    				}
    				if($a['jindan_2']!=1){
    					$sum--;
    				}
    				if($a['jindan_3']!=1){
    					$sum--;
    				}
    				if($sum==1){//最后一次必中
    					$arr_1=array(20,10,8,7,5,0);
    				}else{
    					$arr_1=array(20,10,8,7,5,50);
    				}
    			}else{//中过将不会在中
    				$arr_1=array(0,0,0,0,0,50);
    			}   			    			
    	 		$result=get_rand($arr_1)+1;//抽奖结果
    			$data["result"]=$arr["$result"];//返回给js的抽奖结果
    			M("Jindan")->where(array('openid'=>$_SESSION['openid']))->save(array($ziduan=>'0'));
    			if($result!=6){//获奖了
    				$content="恭喜您赢取:".$data["result"];
    				$wechat->send_text($_SESSION['openid'],$content);
    				M("Jindan_result")->add(array('openid'=>$_SESSION['openid'],'result'=>$data["result"]));
    			}
    		} 
    		$this->ajaxReturn($data);
    	}else{
    		$wechat=new WechatAuth();
    		$data=$wechat->getuserdata();
    		if(!empty($data['openid'])){
    			unset($_SESSION['openid']);
    			session_start();
    			$_SESSION['openid']=$data['openid'];
    		}else{
    			/*  $url_='http://www.hailuohao.net/myself/index.php/WeChat/Jindan/jindan';
    			$url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=".$url_.'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect';
    			header("location:$url");//跳转到授权页面用于获取用户openid */
    		} 
    		$this->canyu();//参加活动
    		/*jssdk设置转发信息*/
	        $jssdk=new jssdk();
	    	$jssdk=$jssdk->getSignPackage();
	    	$url="http://www.hailuohao.net/myself/index.php/WeChat/Jindan/jindan";
	    	$jssdk['url']=$url;
	    	$jssdk['imgurl']="http://www.hailuohao.net/myself/public/images/header2.png";//转发的图像
	    	$jssdk['desc']="老地方电影院免费观影活动开始啦!";//转发的描述内容
	    	$this->jssdk=$jssdk;     
	    	$this->data=M('Jindan')->where(array('openid'=>$_SESSION['openid']))->find();   	
	    	$this->display();
    	}   	
    }
    
    /*参与活动*/
    public function canyu(){
    	if(!empty($_SESSION['openid'])){
    		$a=M('Jindan')->where(array('openid'=>$_SESSION['openid']))->find();//判断粉丝是否参与了活动
    		if(empty($a)){
    		    	M('Jindan')->add(array('openid'=>$_SESSION['openid']));
    	    }
    	}
    }
    
    /*ajax分享到朋友圈*/
    public function ajax_fenxiang(){
    	if(IS_POST){
    		$is_fenxiang=M('Jindan')->where(array('openid'=>$_SESSION['openid']))->getField('is_fenxiang');//判断粉丝是否参与了活动
    		if($is_fenxiang!='y'){//如果没有分享
    			$a=M('Jindan')->where(array('openid'=>$_SESSION['openid']))->save(array('is_fenxiang'=>'y'));
    			if($a){
    				echo "分享成功";
    			}
    		}
    	}
    }
}