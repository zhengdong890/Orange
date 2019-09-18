<?php
namespace Home\Controller;
use Think\Controller;
use my_weixin\WechatAuth;
use Org\PhpQrcode\Qrcode;
header("content-type:text/html;charset=utf-8");
class WechatPayController extends Controller{
	/*
	 * 生成微信支付二维码
	 * */
    public function createPayCode(){
    	if(!IS_AJAX){
    		die;
    	}
    	if(empty($_SESSION['order_pay_data']) || !$_SESSION['pay_sn']){
            exit('订单不存在');
    	}
    	if($_SESSION['order_pay_data']['is_can_pay'] == 0){
    		$this->ajaxReturn(array(
    			'status' => 0,
    			'msg'    =>'该商户还在测试开店期，还没开通在线支付，采购请直接联系店家'
    		));
    	}    	
    	$pay_price    = $_SESSION['order_pay_data']['pay_price'];
    	$out_trade_no = $_SESSION['pay_sn'].setnum(5);
    	$config = C('wechat_pay_config');
    	//统一支付接口类
		$unifiedOrder = new \Org\Wechat\UnifiedOrder($config);
		$unifiedOrder->setParameter("body","订单支付");//商品描述岚樨微支付平台
		$unifiedOrder->setParameter("out_trade_no",$out_trade_no);//商户订单号
		$unifiedOrder->setParameter("total_fee",100 * $pay_price);//总金额（微信支付以人民币“分”为单位）
		$unifiedOrder->setParameter("notify_url",$config['NOTIFY_URL']);//通知地址
		$unifiedOrder->setParameter("trade_type","NATIVE");//交易类型 扫码支付
		$unifiedOrder->setParameter("product_id","158");//商品ID
		$result = $unifiedOrder->getResult();
		//生成二维码
		$code_url = $this->createPhpQrcode($result['code_url']);
		$order_id = $_SESSION['order_pay_data']['id'];
		unset($_SESSION['order_pay_data']  , $_SESSION['pay_sn']);
		$this->ajaxReturn(array('status'=>1,'msg'=>'ok','data'=>$code_url,'id'=>$order_id));
    }

	/**
	 * 微信支付异步通知
	 */
	public function wxPayNotify(){
		$postStr      = $GLOBALS["HTTP_RAW_POST_DATA"];
		$array        = $this->xmlToArray($postStr);//获取回调参数，orderno
		$out_trade_no = $array['out_trade_no'];	
		if($array['result_code'] == 'SUCCESS' && $array['return_code'] == 'SUCCESS'){
	        $out_trade_no = substr($out_trade_no , 0 , -15);
	        $type         = substr($out_trade_no , 0 , 2);          
	        $pay_type     = substr($out_trade_no , 2 , 1);
	        $order_id     = substr($out_trade_no , 3);
			//交易号
			$trade_no     = $array['transaction_id'];
	        if($type == 'fx'){//分享订单
	            D('Order')->changePayState($pay_type , $order_id);
	        }else
	        if($type == 'sc'){//商城订单
	            D('Mall_order')->changePayState($pay_type , $order_id , $trade_no , 2);
	        }                		
			echo "success";//不能修改
		}else{
			echo 'fall';//不能修改
		}
	}

	public function xmlToArray($xml){
		//将XML转为array
		$array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		return $array_data;
	}

	public function createPhpQrcode($url){
		//生成随机文件名
		$save_name = setnum(12 , 'n');
		//保存地址
		$save_path = "Public/qrcode/".$save_name.".png";
		//容错级别
		$errorCorrenctionLevel = 'l';
		//二维码图片大小
		$matrixPointSize = 6;
		//生成二维码
		Qrcode::png($url , $save_path , $errorCorrenctionLevel , $matrixPointSize , 2);
		return C('STATIC_URL').'/qrcode/'.$save_name.".png";
		/*
		//图片logo加入二维码
		$logo   = 'Public/a.jpg';
		$qrcode = 'Public/qrcode.png';
		$logo = imagecreatefromstring(file_get_contents($logo));
		$qrcode = imagecreatefromstring(file_get_contents($qrcode));
		$QR_width = imagesx($qrcode);//二维码图片宽度   
		$QR_height = imagesy($qrcode);//二维码图片高度   
		$logo_width = imagesx($logo);//logo图片宽度   
		$logo_height = imagesy($logo);//logo图片高度   
		$logo_qr_width = $QR_width / 5;   
		$scale = $logo_width/$logo_qr_width;   
		$logo_qr_height = $logo_height/$scale;   
		$from_width = ($QR_width - $logo_qr_width) / 2;   
		//重新组合图片并调整大小   
		imagecopyresampled($qrcode, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,$logo_qr_height, $logo_width, $logo_height);   
		//保存生成的二维码
		imagepng($qrcode, 'Public/helloweixin.png');   
		echo '<img src="Public/helloweixin.png">';  */ 
	}
}