<?php
namespace my_weixin;
header("content-type:text/html;charset=utf-8");
class jssdk{
  private $appId;
  private $appSecret;
  public $User_ID;
 
  public function __construct($appId, $appSecret, $User_ID = 1) {
    $this->appId = APPID;//微信开发者申请的appID;
    $this->appSecret = APPSECRET;//微信开发者申请的appSecret
    $this->User_ID = $User_ID;
  }
   
  public function Ext_FileSystem(){
    if(!file_exists("jSon_file/jsapi_ticket_".$this->User_ID.".json")){
        touch("jSon_file/jsapi_ticket_".$this->User_ID.".json");
        chmod("jSon_file/jsapi_ticket_".$this->User_ID.".json",0755);
    }
     
    if(!file_exists("jSon_file/access_token_".$this->User_ID.".json")){
        touch("jSon_file/access_token_".$this->User_ID.".json");
        chmod("jSon_file/access_token_".$this->User_ID.".json",0755);
    }
  }
   
  public function getSignPackage() {
    $this->Ext_FileSystem();
    $jsapiTicket = $this->getJsApiTicket();
    $url= "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $timestamp = time();
    $nonceStr = $this->createNonceStr();
    // 这里参数的顺序要按照 key 值 ASCII 码升序排序
    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
    $signature = sha1($string);
    $signPackage = array(
      "appId"     => $this->appId,
      "nonceStr"  => $nonceStr,
      "timestamp" => $timestamp,
      "url"       => $url,
      "signature" => $signature,
      "rawString" => $string
    );
    return $signPackage; 
  }
 
  private function createNonceStr($length = 16) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $str = "";
    for ($i = 0; $i < $length; $i++) {
      $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
    }
    return $str;
  }
  
  /**
   * 获取aJsApiTicket，用于后续接口访问
   */
  private function getJsApiTicket(){
	    // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
	    $time=time();
	    $arr=json_decode(file_get_contents("./jsapi_ticket.txt"),true);
	    if(empty($arr)||($time-$arr['time']>7000)){
	    	$accessToken = $this->getAccessToken();
	    	$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
	    	$res=$this-> http_request($url);
	    	$result=json_decode($res,true);
	    	$data = array("time"=>$time,"jsapi_ticket"=>$result['ticket']);
	    	file_put_contents("./jsapi_ticket.txt",json_encode($data));//把access_token和time存入text文本
	    	$arr = json_decode(file_get_contents("./jsapi_ticket.txt"),true);
	    }
	    return $arr['jsapi_ticket'];
  }
 
    /**
     * 获取access_token，用于后续接口访问
     * @return array access_token信息，包含 token 和有效期
     */
	private function getAccessToken(){
		$time=time();
		$arr=json_decode(file_get_contents("./access_token.txt"),true);
		if(empty($arr)||($time-$arr['time']>7000)){
			$url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
			$res=$this->http_request($url);
			$result=json_decode($res,true);
			$data = array("time"=>$time,"access_token"=>$result['access_token']);
			file_put_contents("./access_token.txt",json_encode($data));//把access_token和time存入text文本
			$arr = json_decode(file_get_contents("./access_token.txt"),true);
		}
		return $arr['access_token'];
	}
 
  /**
   * 发送HTTP请求方法，目前只支持CURL发送请求
   * @param  string $url    请求URL
   * @param  array  $data   POST的数据，GET请求时该参数无效
   * @return array          响应数据
   */
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
