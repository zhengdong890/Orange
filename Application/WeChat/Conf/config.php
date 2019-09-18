<?php
return array(
	//'配置项'=>'配置值'
	'USER_AUTH_ON'=>true,//是否开启验证
	'USER_AUTH_TYPE' => 2,//验证类型（1：登录验证2：时时验证）
	'USER_AUTH_KEY' => 'uid', //用户认证识别号
	'NOT_AUTH_CONTROLLER' => 'Index', //无需认证的控制器
	'NOT_AUTH_ACTION' => 'logout', //无需认证的方法
    'APP_GROUP_LIST' => 'Home,Admin', //分组	
    'DEFAULT_GROUP'  => 'Admin', //默认分组
	'APP_GROUP_MODE'=>1,
    'DB_TYPE'=>'mysql',
	//数据库配置
    'DB_HOST'   => '120.76.130.106', // 服务器地址
	'DB_NAME'   => 'orange', // 数据库名
	'DB_PORT'   => '3306', // 端口
	'DB_USER'   => 'root',// 用户名
	'DB_PWD'    => 'Chengcheng83225308!@', // 密码	
	'DB_PREFIX' => 'tp_', // 数据库表前缀 
    //'SHOW_PAGE_TRACE'=>'true',
	//'URL_MODEL' =>2,	
   
    /**
* 微信配置常量
*/
const TOKEN = 'orangesha';
//微信后台填写的token
//const APPID = 'wxa783dcee4292c6e3';
const APPID = '';
//受理商ID，身份标识
const MCHID = '';
//商户支付密钥Key。审核通过后，在微信发送的邮件中查看（新版本中需要自己填写）
const KEY = '';
//JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看
//const APPSECRET = 'fdaf701458acdb93b926ae2f1b179729';
const APPSECRET = '';
//=======【JSAPI路径设置】===================================
//获取access_token过程中的跳转uri，通过跳转将code传入jsapi支付页面
const JS_API_CALL_URL = 'http://XXXXXXX/Weixinpay/main.html';
//=======【异步通知url设置】===================================
//异步通知url，商户根据实际开发过程设定
const NOTIFY_URL = '';

//=======【curl超时设置】===================================
//本例程通过curl使用HTTP POST方法，此处可修改其超时时间，默认为30秒
const CURL_TIMEOUT = 30;
//=======【证书路径设置】=====================================
//证书路径,注意应该填写绝对路径（可以不修改）
const SSLCERT_PATH = 'http://xxx/Public/cacert/apiclient_cert.pem';
const SSLKEY_PATH = 'http://xxx/Public/cacert/apiclient_key.pem';
//文件夹名字
const WENJIANNAME='myself';   
);