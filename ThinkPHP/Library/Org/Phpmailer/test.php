<?php
/**
* by www.phpddt.com
*/
header("content-type:text/html;charset=utf-8");
ini_set("magic_quotes_runtime",0);
require 'class.phpmailer.php';
try {
	$mail = new PHPMailer(true); 
	$mail->IsSMTP();
	$mail->SMTPDebug=1;
	$mail->CharSet='UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码
	$mail->SMTPAuth   = true;                  //开启认证
	$mail->Port       = 25;         
	$mail->STMPSecure = 'SSL';           
	$mail->Host       = "smtp.163.com"; //"smtp.qq.com"; 
	$mail->Username   = "14789514293@163.com";//"1025163131@qq.com";
	$mail->Password   = "yanjin597089187";   //"gsvriinmaakibcgf";      
	//$mail->IsSendmail(); //如果没有sendmail组件就注释掉，否则出现“Could  not execute: /var/qmail/bin/sendmail ”的错误提示
	$mail->AddReplyTo("14789514293@163.com","mckee");//回复地址
	$mail->From       = "14789514293@163.com";
	$mail->FromName   = "yanjin";
	$to = "1025163131@qq.com";
	$mail->AddAddress($to);
	$mail->Subject  = "验证码";
	$body = "验证码:123456 ";
	$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; //当邮件不支持html时备用显示，可以省略
	$mail->WordWrap   = 80; // 设置每行字符串的长度
	//$mail->AddAttachment("f:/test.png");  //可以添加附件
	$mail->MsgHTML($body);
	$mail->IsHTML(true); 
	$mail->Send();
	echo '邮件已发送';
} catch (phpmailerException $e) {
	print_r("邮件发送失败：".$mail->ErrorInfo);
}

/*             $mail= new PHPMailer();
	$body= "我终于发送邮件成功了！呵呵";	
	$mail->IsSMTP();//采用SMTP发送邮件	
	$mail->Host       = "smtp.163.com";//邮件服务器
	$mail->SMTPDebug  = 0;
	//使用SMPT验证
	$mail->SMTPAuth   = true;
	$mail->Username   = "14789514293@163.com";//SMTP验证的用户名称
	$mail->Password   = "yanjin597089187";//SMTP验证的秘密
	$mail->CharSet  = "utf-8";//设置编码格式
	$mail->Subject    = "测试";//设置主题
             $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
	$mail->SetFrom('14789514293@163.com', 'test');//设置发送者
	$mail->MsgHTML($body);//采用html格式发送邮件
	//接受者邮件名称
	$mail->AddAddress("597089187@qq.com", "test");//发送邮件
	if(!$mail->Send()) {
	    var_dump("Mailer Error: " . $mail->ErrorInfo);
	} else {
	       echo "Message sent!";
	}*/
?>