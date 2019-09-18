<?php
namespace my_weixin;
header("content-type:text/html;charset=utf-8");
class WechatAuth {
    private $appid;
    private $appsecret;
    /**
     * @param string $token  获取到的access_token
     */
    public function __construct(){
        $this->appid = C('APPID');//微信开发者申请的appID
        $this->appsecret = C('APPSECRET');//微信开发者申请的appSecret
    	$this->getAccessToken();
    }
	
	 /**
     * 获取access_token，用于后续接口访问
     * @return array access_token信息，包含 token 和有效期
     */
	public function getAccessToken(){
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
		$this->access_token=$arr['access_token'];
	}
	
	/**
     * 获取关注者列表
     */
	public function getuser(){
		$url="https://api.weixin.qq.com/cgi-bin/user/get?access_token=".$this->access_token;
		$res=$this->http_request($url);
		$res=json_decode($res);
		$res=get_object_vars($res);
		$res['data']=get_object_vars($res['data']);
		return $res;
	}
	
	 /**
     * 创建用户组
     * @param  string $name 组名称
     * @return array 返回请求结果
     */
	public function creategroup($name){
		$url="https://api.weixin.qq.com/cgi-bin/groups/create?access_token=".$this->access_token;
		$data=array('group'=>array('name'=>$name));
		//保护中文，微信api不支持中文转义的json结构
		array_walk_recursive($data, function(&$value){
			$value = urlencode($value);
		});
		$data = urldecode(json_encode($data));
		$res=$this->http_request($url,$data);
		$res=json_decode($res);
		$res=$this->objtoarr($res);
		return $res;
	}
	 
	 /**
     * 查询所有分组
     * @return array 分组列表
     */
	public function getgroup(){
		$url="https://api.weixin.qq.com/cgi-bin/groups/get?access_token=".$this->access_token;
		$res=$this->http_request($url);
		$res=json_decode($res)->groups;
		foreach($res as $k=>$v){
			$res["$k"]=get_object_vars($v);
		}
		return $res;
	}
	
	/**
     * 查询用户所在的分组
     * @param  string $openid 用户的OpenID
     * @return $res        分组ID
     */
	public function usergroupname($openid){
		$url="https://api.weixin.qq.com/cgi-bin/groups/getid?access_token=".$this->access_token;
		$data=array('openid'=>$openid);
		$data=urldecode(json_encode($data));
		$res=$this->http_request($url,$data);
		$res=json_decode($res);	
		$res=$this->objtoarr($res);		
		return $res;
	}
		
	 /**
     * 修改分组
     * @param  number $id   分组ID
     * @param  string $name 分组名称
     * @return array        修改成功或失败信息
     */
	public function updategroup($id,$name){
		$url="https://api.weixin.qq.com/cgi-bin/groups/update?access_token=".$this->access_token;
		$data=array('group'=>array('id'=>$id,'name'=>$name));
		//保护中文，微信api不支持中文转义的json结构
		array_walk_recursive($data, function(&$value){
			$value = urlencode($value);
		});
		$data = urldecode(json_encode($data));
		dump($data);
		$res=$this->http_request($url,$data);
		$res=json_decode($res);
		$res=$this->objtoarr($res);
		return $res;
	}
	
	/**
     * 移动用户分组
     * @param  string $openid     用户的OpenID
     * @param  number $to_groupid 要移动到的分组ID
     * @return array              移动成功或失败信息
     */
	public function movegroup($openid,$to_groupid){
		$url="https://api.weixin.qq.com/cgi-bin/groups/members/update?access_token=".$this->access_token;
		$data=array('openid'=>$openid,'to_groupid'=>$to_groupid);
		$data=urldecode(json_encode($data));
		$res=$this->http_request($url,$data);
		return json_decode($res);
	}
	 
	/**
     * 获取指定用户的详细信息
     * @param  $arr 用户的openid二维数组
     * @param  return $data返回的用户信息
     */
	public function getuserinformation($arr){
		$data=array();
		foreach($arr as $k=>$v){
			$url="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->access_token."&openid=".$v['openid'];
			$res=$this->http_request($url);
			$res=json_decode($res);
			$res=get_object_vars($res);
			if($res['sex']==1){
				$res['sex']="男";
			}
			if($res['sex']==2){
				$res['sex']="女";
			}
			if(!$res['sex']){
				$res['sex']="未填写";
			}
			unset($res['remark']);
			unset($res['language']);
			unset($res['subscribe']);
			$data[]=$res;
		}
		return $data;
	}
	
	 /**
     * 获取授权用户信息
     * @return array         用户信息数据，具体参见微信文档
     */
	public function getuserdata()
	{   
		if(isset($_GET['code'])){
			$code=$_GET['code'];
			$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$this->appid."&secret=".$this->appsecret."&code=$code&grant_type=authorization_code";			
			$data= $this->http_request($url);
			$data = json_decode($data,true);
			return $data;
		}
	}
	
     /**
     * 创建自定义菜单
     */
	public function automenu($arr){
		$url_1="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appid."&redirect_uri=";
		$jsonmenu = '{"button":[
		';
		foreach($arr as $v){
			$jsonmenu.='{
			"name":'.'"'.$v['name'].'",'.'"sub_button":[
			';
			foreach ($v['twomenu'] as $v1) {
				if($v1['type']==2){
					$jsonmenu.='{"type":"view",'.
					'"name":'.'"'.$v1['name'].'",';
					if($v1['isoauth']=='y'){
						$jsonmenu.='"url":'.'"'.$url_1.$v1['url']."&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect".'"},';
					}else{
						$jsonmenu.='"url":'.'"'.$v1['url'].'"},';
					}					
				}else{
					$jsonmenu.='{"type":"click",'.
					'"name":'.'"'.$v1['name'].'",'.
					'"key":'.'"'.$v1['keyword'].'"},';
				}
				
			}
			$jsonmenu=rtrim($jsonmenu,',');
			$jsonmenu.=']},';
		}
		$jsonmenu=rtrim($jsonmenu,',');
		$jsonmenu.=']}';
		$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->access_token;
		$result =$this->http_request($url,$jsonmenu);
		$result = json_decode($result,true);//json转换成数组
		$result['json']=$jsonmenu;
		return $result;
	}
    
    /**
     * 删除自定义菜单
     */
	public function deletemenu(){
		$url="https://api.weixin.qq.com/cgi-bin/menu/delete?access_token=".$this->access_token;
		$result =$this->http_request($url);
		$result = json_decode($result,true);
		return $result;
	}
	
	 /**
     * 主动发送文本消息
     */
	public function send_text($openid,$content){
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$this->access_token;
		$data=array('touser'=>$openid,'msgtype'=>'text','text'=>array('content'=>$content));
		//保护中文，微信api不支持中文转义的json结构
		array_walk_recursive($data, function(&$value){
			$value = urlencode($value);
		});
		$data = urldecode(json_encode($data));
		$res=$this->http_request($url,$data);
		return json_decode($res);
	}
	
	/**
	 * 主动发送图文消息
	 */
	public function send_news($openid,$content){
		$url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$this->access_token;
		$data=array('touser'=>$openid,'msgtype'=>'news','news'=>array('articles'=>$content));
		//保护中文，微信api不支持中文转义的json结构
		array_walk_recursive($data, function(&$value){
			$value = urlencode($value);
		});
		$data = urldecode(json_encode($data));
		$res=$this->http_request($url,$data);
		return json_decode($res);
	}
	
	//发送群发消息
	public function Mass(){
		$url = "https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=".$this->access_token;
		
	}
	
	//模板消息
	public function templetmsg($data){
		$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$this->access_token;
		//保护中文，微信api不支持中文转义的json结构
		array_walk_recursive($data, function(&$value){
			$value = urlencode($value);
		});
		$data = urldecode(json_encode($data));
		dump($data);
		$res=$this->http_request($url,$data);
		dump($res);die;
		return json_decode($res);
	}
	
	//obj转换成数组
	public function objtoarr($obj){
		$ret = array();
		foreach($obj as $key =>$value){
			if(gettype($value) == 'array' || gettype($value) == 'object'){
				$ret[$key] = $this->objtoarr($value);
			}
			else{
				$ret[$key] = $value;
			}
		}
		return $ret;
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