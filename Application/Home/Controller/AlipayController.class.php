<?php
/*
 * 支付宝支付
 * */  
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class AlipayController extends Controller {	  
    /*
	 *  支付宝支付订单
	 * */ 
    public function alipayPayment(){
    	if(empty($_SESSION['order_pay_data']) || !$_SESSION['pay_sn']){
            exit('订单不存在');
    	}
    	if($_SESSION['order_pay_data']['is_can_pay'] == 0){
            echo "<script>alert('该商户还在测试开店期，还没开通在线支付，采购请直接联系店家');</script>";
            echo "<script>history.go(-1);</script>";
            die;
    	}
    	$pay_price = $_SESSION['order_pay_data']['pay_price'];
    	$pay_sn    = $_SESSION['pay_sn'].setnum(5);
        unset($_SESSION['order_pay_data']  , $_SESSION['pay_sn']);
        $this->alipay($pay_sn , '商城订单' , $pay_price);
    }

   /**
    * 支付宝支付 
    * @param  array $out_trade_no 订单编号  
    * @param  array $subject      订单名称 
    * @param  array $total_fee    订单价格  
    * @param  array $body         描述         
    * @return array 返回结果
    */     
    public function alipay($out_trade_no ,  $subject , $total_fee , $body = ''){
        $alipay_config = C('alipay_config');//获取支付宝配置
        //构造要请求的参数数组，无需改动
		$parameter = array(
			"service"           => $alipay_config['service'],
			"partner"           => $alipay_config['partner'],
			"seller_id"         => $alipay_config['seller_id'],
			"payment_type"	    => $alipay_config['payment_type'],
			"notify_url"	    => $alipay_config['notify_url'],
			"return_url"	    => $alipay_config['return_url'],
			"anti_phishing_key" => $alipay_config['anti_phishing_key'],
			"exter_invoke_ip"   => $alipay_config['exter_invoke_ip'],
			"out_trade_no"	    => $out_trade_no,//订单编号
			"subject"	        => $subject,//订单名称
			"total_fee"	        => $total_fee,//订单价格
			"body"	            => $body,//商品描述
			"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
			//其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.kiX33I&treeId=62&articleId=103740&docType=1
		    //如"参数名"=>"参数值"	
		);
        $alipaySubmit  = new \Org\Alipay\AlipaySubmit($alipay_config); 
        $html_text     = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        echo $html_text;
    }

   /**
    * 支付宝支付成功异步通知         
    * @return array 返回结果
    */ 
    public function notifyUrl(){
    	$alipay_config = C('alipay_config');//获取支付宝配置
		//计算得出通知验证结果
		$alipayNotify  = new \Org\Alipay\AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();
		if($verify_result) {//验证成功		    
			$out_trade_no = $_POST['out_trade_no'];
	        $out_trade_no = substr($out_trade_no , 0 , -15);
	        $type         = substr($out_trade_no , 0 , 2);          
	        $pay_type     = substr($out_trade_no , 2 , 1);
	        $order_id     = substr($out_trade_no , 3);
			//支付宝交易号
			$trade_no = $_POST['trade_no'];
			//交易状态
			$trade_status = $_POST['trade_status'];			
		    if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
		        if($type == 'fx'){//分享订单
		            D('Order')->changePayState($pay_type , $order_id);
		        }else
		        if($type == 'sc'){//商城订单
		            D('Mall_order')->changePayState($pay_type , $order_id , $trade_no , 1);
		        }                
		    }
			echo "success";		
		}else{
		    echo "fail";
		}
    }

   /**
    * 支付宝支付成功通知         
    * @return array 返回结果
    */ 
    public function successUrl(){
    	$alipay_config = C('alipay_config');//获取支付宝配置
		//计算得出通知验证结果
		$alipayNotify  = new \Org\Alipay\AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyReturn();
		if($verify_result){//验证成功
			$out_trade_no = $_GET['out_trade_no'];
	        $out_trade_no = substr($out_trade_no , 0 , -15);
	        $type         = substr($out_trade_no , 0 , 2);          
	        $pay_type     = substr($out_trade_no , 2 , 1);
	        $order_id     = substr($out_trade_no , 3);
			//交易状态
			$trade_status = $_GET['trade_status'];	
			//支付宝交易号
			$trade_no = $_GET['trade_no'];
		    if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
		    	if($type == 'fx'){
		            D('Order')->changePayState($pay_type , $order_id);
		        }else
		        if($type == 'sc'){
		            D('Mall_order')->changePayState($pay_type , $order_id , $trade_no);
		        }
		    }else{
		        echo "trade_status=".$_GET['trade_status'];
		    }
			echo "支付成功<br />";
		}else{
		    //验证失败
		    //如要调试，请看alipay_notify.php页面的verifyReturn函数
		    echo "支付失败";
		}	
    }    
}