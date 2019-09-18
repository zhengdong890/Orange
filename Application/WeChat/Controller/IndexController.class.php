<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace WeChat\Controller;
use Think\Controller;
use Com\Wechat;
use my_weixin\WechatAuth;
use my_weixin\Gettoken;
class IndexController extends Controller{
    /**
     * 微信消息接口入口
     * 所有发送到微信的消息都会推送到该操作
     * 所以，微信公众平台后台填写的api地址则为该操作的访问地址
     */
    public function index($id = ''){
        $token =C('TOKEN'); //微信后台填写的TOKEN
        /* 加载微信SDK */
        $wechat = new Wechat($token);
        /* 获取请求信息 */
        $data = $wechat->request();
        if($data && is_array($data)){
            /**
             * 你可以在这里分析数据，决定要返回给用户什么样的信息
             * 接受到的信息类型有9种，分别使用下面九个常量标识
             * Wechat::MSG_TYPE_TEXT       //文本消息
             * Wechat::MSG_TYPE_IMAGE      //图片消息
             * Wechat::MSG_TYPE_VOICE      //音频消息
             * Wechat::MSG_TYPE_VIDEO      //视频消息
             * Wechat::MSG_TYPE_MUSIC      //音乐消息
             * Wechat::MSG_TYPE_NEWS       //图文消息（推送过来的应该不存在这种类型，但是可以给用户回复该类型消息）
             * Wechat::MSG_TYPE_LOCATION   //位置消息
             * Wechat::MSG_TYPE_LINK       //连接消息
             * Wechat::MSG_TYPE_EVENT      //事件消息
             *
             * 事件消息又分为下面五种
             * Wechat::MSG_EVENT_SUBSCRIBE          //订阅
             * Wechat::MSG_EVENT_SCAN               //二维码扫描
             * Wechat::MSG_EVENT_LOCATION           //报告位置
             * Wechat::MSG_EVENT_CLICK              //菜单点击
             * Wechat::MSG_EVENT_MASSSENDJOBFINISH  //群发消息成功
             */
            if($data['MsgType']=='text'){//文本类型
            	$a=M('Autoreplay')->where(array('keyword'=>$data['Content'],'type'=>2))->find();//关键字完全匹配
            	if(empty($a)){//如果完全匹配不上则不完全匹配           		
            		$sql="select * from ".C('DB_PREFIX')."autoreplay where type=1 and '".$data['Content']."' like concat('%',keyword,'%')";
            		$a=M()->query($sql);  
            		$a=$a['0'];
            	}
            	//如果都匹配失败(回答不上来的配置)
            	if(empty($a)){           		
            		$a=M('Nokeyword')->find();//回答不上来的配置
            		if(empty($a['keyword'])){//如果关键字没设置则回复内容为配置内容
            			$a['msg_type']='text';
            		}else{//匹配关键字
            			$a=M('Autoreplay')->where(array('keyword'=>$a['keyword']))->find();//关键字匹配
            		}
            	}
	            $content=$this->msg($a);//回复内容
	            if(empty($content)){
	            	echo "";exit;
	            }
	            //回复类型
	            if($a['msg_type']=='text'){
	            	$type=Wechat::MSG_TYPE_TEXT;
	            }else
	            if($a['msg_type']=='news'||$a['msg_type']=='morenews'){
	            	$type=Wechat::MSG_TYPE_NEWS;
	            }else
	            if($a['msg_type']=='voice'){
	                $type=Wechat::MSG_TYPE_MUSIC;
	            }            	           
            }else 
            if($data['MsgType']=='event'&&$data['Event']=='CLICK'){//菜单类型
                $keyword=$data['EventKey'];//关键字
                $where=array('keyword'=>$keyword);
                $a=M('Autoreplay')->where($where)->find();
                $content=$this->msg($a);  
                if($a['msg_type']=='text'){
               	    $type=Wechat::MSG_TYPE_TEXT;
                }else{
               	    $type=Wechat::MSG_TYPE_NEWS;
                }           
            }else 
            if($data['MsgType']=='event'&&$data['Event']=='unsubscribe'){//取消关注
               $openid=$data['FromUserName'];
               $where=array('openid'=>$openid);
               M('Wechatfans')->where($where)->delete();
            }else 
            if($data['MsgType']=='event'&&$data['Event']=='subscribe'){//关注
            		$openid=$data['FromUserName'];//获取用户openid
            		$arr['0']['openid']=$openid;          		
            		$wechat=new WechatAuth();
            		$data=$wechat->getuserinformation($arr);//获取用户信息
            		$data=$data['0'];
            		M('Wechatfans')->add($data);//信息存入数据库
            		$content=M('Guanzhu')->find();            		           		
            		if(empty($content['keyword'])){
            			$content=$content['content'];
            			$wechat->send_text($openid,$content);
            			$type=Wechat::MSG_TYPE_TEXT;
            		}else{
            			$keyword=$content['keyword'];//关键字
            			$where=array('keyword'=>$keyword);
            			$a=M('Autoreplay')->where($where)->find();
            			if($a['msg_type']=='text'){
            				$type=Wechat::MSG_TYPE_TEXT;
            			}else{
            				$type=Wechat::MSG_TYPE_NEWS;
            			}
            			$content=$this->msg_1($a);
            			$wechat->send_news($openid,$content);
            		}            		
            }	         
            /* 响应当前请求(自动回复) */
            $wechat->response($content,$type);       	

            /**
             * 响应当前请求还有以下方法可以只使用
             * 具体参数格式说明请参考文档
             * 
             * $wechat->replyText($text); //回复文本消息
             * $wechat->replyImage($media_id); //回复图片消息
             * $wechat->replyVoice($media_id); //回复音频消息
             * $wechat->replyVideo($media_id, $title, $discription); //回复视频消息
             * $wechat->replyMusic($title, $discription, $musicurl, $hqmusicurl, $thumb_media_id); //回复音乐消息
             * $wechat->replyNews($news, $news1, $news2, $news3); //回复多条图文消息
             * $wechat->replyNewsOnce($title, $discription, $url, $picurl); //回复单条图文消息
             * 
             */
        }
    }
    
    //被动推送消息组装
    public function msg($a){
    	if($a['msg_type']=='text'){
    		$content =$a['content']; //回复内容，回复不同类型消息，内容的格式有所不同   	
    	}else
    	if($a['msg_type']=='news'){//单图文组装
    	    if($a['ispiclocal']=='y'){
    	    	$a['thumb']=substr($a['thumb'],1);
    	    	$a['thumb']="http://".$_SERVER['SERVER_NAME']."/".WENJIANNAME.$a['thumb'];
    	    }	
    	    if($a['isoauth']=='y'){
    	    	$a['url']="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=".$a['url']."&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
    	    }else{
    	    	$a['url']=$a['url'];
    	    }    	
	    	$news=array($a['title'],$a['content'],$a['url'],$a['thumb']);
	    	$content=array($news);
    	}else
    	if($a['msg_type']=='morenews'){//多图文组装
    		$allid=explode('-',$a['sid']);
    		$news=array();
    		$content=array();
    		foreach($allid as $k=>$v){
    			$where=array('msg_type'=>'news','id'=>$v);
    			$b=M('Autoreplay')->where($where)->find();   			
    			if($b['ispiclocal']=='y'){
    				$b['thumb']=substr($b['thumb'],1);
    			    $b['thumb']="http://".$_SERVER['SERVER_NAME']."/".WENJIANNAME.$b['thumb'];
    			}    			
    		    if($b['isoauth']=='y'){
    	    	    $b['url']="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=".$b['url']."&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
    	        }else{
    	    	    $b['url']=$a['url'];
    	        }
    			$news=array($b['title'],$b['content'],$b['url'],$b['thumb']);
    			$content["$k"]=$news;
    		}    		
    	}else 
    	if($a['msg_type']=='voice'){//音乐消息
	    	if($a['ispiclocal']=='y'){
	    		$a['thumb']=substr($a['thumb'],1);
	    		$a['thumb']="http://".$_SERVER['SERVER_NAME']."/".WENJIANNAME.$a['thumb'];
	    	}
	    	$data=array($a['title'],$a['content'],$a['thumb'],$a['thumb']);
            $content=$data;
    	}   	
    	return $content;
    }
    
    //主动推送消息组装
    public function msg_1($a){
    	if($a['msg_type']=='text'){//文本消息
    		$content =$a['content']; 
    	}else
    	if($a['msg_type']=='news'){//单图文组装		   	
	    	if($a['ispiclocal']=='y'){
	    		$a['thumb']=substr($a['thumb'],1);
	    		$a['thumb']="http://".$_SERVER['SERVER_NAME']."/".WENJIANNAME.$a['thumb'];
	    	}
		    $data['0']['title']=$a['title'];
		    $data['0']['description']=$a['content'];
		    if($a['isoauth']=='y'){
		    	$data['0']['url']="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=".$a['url']."&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
		    }else{
		    	$data['0']['url']=$a['url'];
		    }		    
		    $data['0']['picurl']=$a['thumb'];		   
    	    $content=$data;		
    	}else{//多图文组装
    		$allid=explode('-',$a['sid']);
    		foreach($allid as $k=>$v){
    			$where=array('msg_type'=>'news','id'=>$v);
    			$b=M('Autoreplay')->where($where)->find();
    			if($b['ispiclocal']=='y'){
    				$b['thumb']=substr($b['thumb'],1);
    				$b['thumb']="http://".$_SERVER['SERVER_NAME']."/".WENJIANNAME.$b['thumb'];
    			}
    			$data["$k"]['title']=$b['title'];//标题
    			$data["$k"]['description']=$b['content'];//描述
    			if($b['isoauth']=='y'){
    				$data["$k"]['url']="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".APPID."&redirect_uri=".$b['url']."&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect";
    			}else{
    				$data["$k"]['url']=$b['url'];
    			}
    			$data["$k"]['picurl']=$b['thumb'];//图片链接地址
    		}   		   		
    		$content=$data;
    	}  	
    	return $content;	
    }
}