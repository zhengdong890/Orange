<?php
/*
 * 支付宝退款
 * */  
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class AlipayRefundController extends Controller {	  
   /**
    * 支付宝退款
    * @param  var   $batch_no   退款批次号 
    * @param  int   $trade_no   支付宝交易流水号   
    * @param  float $total_fee  每笔价格  
    * @param  int   $batch_num  退款笔数必填，参数detail_data的值中，“#”字符出现的数量加1，最大支持1000 笔（即“#”字符出现的数量999个）
    * @param  var $desc  描述        
    * @return array 返回结果
    */     
    public function refund($batch_no , $trade_no , $case , $desc ,  $batch_num = 1 ){
        $alipay_config        = C('alipay_config');//获取支付宝配置
        $alipay_refund_config = C('alipay_refund_config');//支付宝退款配置
        $alipay_config        = array_merge($alipay_config , $alipay_refund_config);
        $detail_data          = "$trade_no^$case^$desc";
        //构造要请求的参数数组，无需改动
		$parameter = array(
			"service"           => 'refund_fastpay_by_platform_pwd',
			"partner"           => $alipay_config['partner'],
			"notify_url"	    => $alipay_config['notify_url'],
			"seller_user_id"	=> trim($alipay_config['partner']),
			"refund_date"	    => date("Y-m-d H:i:s",time()),
			"batch_no"	        => $batch_no,
			"batch_num"	        => $batch_num,
			"detail_data"	    => $detail_data,
		    "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
        $alipaySubmit  = new \Org\Alipay\AlipaySubmit($alipay_config); 
        $html_text     = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        echo $html_text; 
    }

   /**
    * 支付宝退款异步通知         
    * @return array 返回结果
    */ 
    public function notifyUrl(){
        $alipay_config        = C('alipay_config');//获取支付宝配置
        $alipay_refund_config = C('alipay_refund_config');//支付宝退款配置
        $alipay_config        = array_merge($alipay_config , $alipay_refund_config);
        $alipayNotify  = new \Org\Alipay\AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if($verify_result) {//验证成功
	        $batch_no       = $_POST['batch_no'];
		    //批量退款数据中转账成功的笔数
		    $success_num    = $_POST['success_num'];
			//批量退款数据中的详细信息
			$result_details = $_POST['result_details'];		
			$id             = substr($out_trade_no , 10);
		    D('Mall_order')->changeRetundState($id);                
			echo "success";		
		}else{
		    echo "fail";
		}
    } 
}