<?php
namespace Org\Phpmailer;
include 'class.phpmailer.php';
class SendMail {
    private $config = array(
        'smtp_debug'  => 1, 
        'charset'     => 'UTF-8', //设置邮件的字符编码，这很重要，不然中文乱码
        'smtp_auth'   => true, //开启认证
        'port'        => 25, 
        'stmp_secure' => 'SSL', 
        'host'        => 'smtp.163.com', 
        'username'    => '',//设置 SMTP 用户名
        'password'    => '',//设置 SMTP 密码 
        'from'        => '',//发件人地址
        'from_name'    => 'e橙优品',//发件人姓名
        'to_mail'     => '',
        'subject'     => '',
        'wordwrap'    => 80
    );
    
    private $error = ''; //错误信息
    
    /**
     * 构造方法
     * @param array  $config 配置
     */    
    public function __construct($config) {
        /* 获取配置 */
        $this->config  = array_merge($this->config, $config);
    }
    
    /**
     * 发送邮件
     */    
    public function send($body){
        try {
        	$mail = new PHPMailer(true); 
        	$mail->IsSMTP();
        	$mail->SMTPDebug  = $this->smtp_debug;
        	$mail->CharSet    = $this->charset; //设置邮件的字符编码，这很重要，不然中文乱码
        	$mail->SMTPAuth   = $this->smtp_auth;//开启认证
        	$mail->Port       = $this->port;         
        	$mail->STMPSecure = $this->stmp_secure;         
        	$mail->Host       = $this->host; 
        	$mail->Username   = $this->username; //设置 SMTP 用户名
        	$mail->Password   = $this->password; //设置 SMTP 密码 
        	$mail->From       = $this->from; //发件人地址
        	$mail->FromName   = $this->from_name;//发件人姓名
        	$mail->AddAddress($this->to_mail);
        	$mail->Subject  = $this->subject; //邮件主题
        	$mail->WordWrap = $this->wordwrap;//80; // 设置每行字符串的长度  
        	$mail->MsgHTML($body); //
        	$mail->IsHTML(true); 
        	$mail->Send();
        	return true;
        }catch (phpmailerException $e){  
            $this->error = $mail->ErrorInfo;
        	return false;
        }
    }
    
    public function getError(){;
        return $this->error;
    }
    
    /**
     * 使用 $this->name 获取配置
     * @param  string $name 配置名称
     * @return multitype    配置值
     */
    public function __get($name) {
        return $this->config[$name];
    }
    
    public function __set($name,$value){
        if(isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }
}