<?php
$alipay_config = array(
	/*↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓*/
	//合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
   'partner'    => '2088421397638306',
   //收款支付宝账号，以2088开头由16位纯数字组成的字符串，一般情况下收款账号就是签约账号
   'seller_id'  => '2088421397638306',
   // MD5密钥，安全检验码，由数字和字母组成的32位字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
   'key'        => 'dc9i4sum24yfzxabz4kfhf2usp4ilz50',
   // 服务器异步通知页面路径  需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
   'notify_url' => 'http://www.orangesha.com/index.php/Home/Alipay/notifyUrl.html',
   // 页面跳转同步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
   'return_url' => 'http://www.orangesha.com/index.php/Home/Alipay/successUrl.html',
   //签名方式
   'sign_type'  => strtoupper('MD5'),
   //字符编码格式 目前支持 gbk 或 utf-8
   'input_charset' => strtolower('utf-8'),
   //ca证书路径地址，用于curl中ssl校验
   'cacert' => getcwd().'\\cacert.pem',
   //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
   'transport' => 'http',
   // 支付类型 ，无需修改
   'payment_type' => 1,
   // 产品类型，无需修改
   'service' => 'create_direct_pay_by_user',
   /* ↓↓↓↓↓↓↓↓↓↓请在这里配置防钓鱼信息，如果没开通防钓鱼功能，为空即可↓↓↓↓↓↓↓↓↓↓*/
   // 防钓鱼时间戳  若要使用请调用类文件submit中的query_timestamp函数
   'anti_phishing_key' => '',
   // 客户端的IP地址 非局域网的外网IP地址，如：221.0.0.1
   'exter_invoke_ip' => ''

); 

$msg_config = array(
    'appkey'    => '23529491',
    'appsecret' => 'f8a47ebec505e82960c231246253b06d'
);

$config = array(
   'DEFAULT_THEME' => ismobile()?'pc':'pc',
   'alipay_config' => $alipay_config,
   'msg_config'    => $msg_config,
   'SESSION_TYPE' => 'Redis', //session保存类型
   'SESSION_PREFIX' => 'sess_', //session前缀
   'REDIS_HOST' => '127.0.0.1', //REDIS服务器地址
   'REDIS_PORT' => 6379, //REDIS连接端口号
   'SESSION_EXPIRE' => 3600, //SESSION过期时间
   'APP_ID' => 101365596,
   'APP_KEY' => '751784200782a4a432f64aff0de9bc66',
   'CALLBACK' => 'http://orangesha.com/index.php/Home/Member/login',
   'SCOPE' => 'get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo'
); 
return $config;
