<?php
namespace Home\Controller;
use Think\Controller;

header("content-type:text/html;charset=utf-8");
class TestController extends Controller {	
 
    public function index(){	
      $token = $this->getAccessToken();
	 
	$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=$token";
	  $data = '{
     "button":[
     {	
          "type":"view",
          "name":"e橙商城",
          "url":"http://orangesha.com/wxmall/index.php/Home/Test"
      },
	  {	
          "type":"click",
          "name":"最新消息",
          "key":"todaynews"
      },
      {
           "name":"顾客服务",
           "sub_button":[
           {	
               "type":"click",
               "name":"公司介绍",
               "key":"introduce"
            },
            {
               "type":"view",
               "name":"业务合作",
               "url":"http://orangesha.com/wxmall/index.php/Home/Test"
            }]
       }]
 }';
	  
	 $result =  $this->http_request($url,$data);
	 dump($result);
       
	
	}
	
		public function getAccessToken(){

	
		
			$url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx9d633204f65174d0&secret=cd91ecae9428fdcd42dbbb4a49980b6d";
	
			$res=$this->http_request($url);
			
			$result=json_decode($res,true);
			
			return $result['access_token'];
	
	}
	
	protected function http_request($url,$data=null){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
		if(!empty($data)){
			curl_setopt($ch, CURLOPT_POST,1);
			curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$output = curl_exec($ch);
		curl_close($ch);
		return($output);
	}
	
}