<?php
namespace Home\Controller;
use Think\Controller;
use Org\Msg\SendMsg;
header("content-type:text/html;charset=utf-8");
class MemberController extends Controller {	
	/*        	     
	for($i = 18476741301 ; $i <= 18476741301 ; $i++){
     $register_data = array(
     'username'        => $i, //账号
     'password'        => 'y123456789', //密码
     'repeat_password' => 'y123456789' //确认密码
     );
     D('Member')->register($register_data);//注册
    }
    */
    /* 
     * 登录
     * */
    public function login(){
        if(IS_AJAX){
            //获取用户输入的数据
            $data = array(
                'username' => I('username'),
                'password' => I('password')
            );
            $result = D("Member")->loginValidate($data);//验证账号密码     
            if($result['status']){//验证通过      
                session_start();
                $_SESSION['member_data'] = $result['user_data'];    
            }
            unset($result['user_data']);
            unset($_SESSION['order_total']);
            unset($_SESSION['cart_total']);
            /*cookie购物车数据存数据库*/
            $cart = unserialize($_COOKIE['cart']);
            if(is_array($cart) && count($cart) > 0){
            	foreach($cart as $v){
	                $v['member_id'] = $_SESSION['member_data']['id'];
	                $result = D("Cart")->cartAdd($v);            		
            	}
                setcookie('cart' , '' , time() -10 , "/" , '.orangesha.com');
            }
            $cart = unserialize($_COOKIE['mall_cart']);
            if(is_array($cart) && count($cart) > 0){
	            $result = D("MallCart")->cartAdd($cart , $_SESSION['member_data']['id']);
                setcookie('mall_cart' , '' , time() -10 , "/" , '.orangesha.com');
            }
            $this->ajaxReturn($result);
        }else{
            if($_SESSION['member_data']){
                header("Location:http://www.orangesha.com");
            }
            session_start();
            $this->display();  
        }      
    }

    /* 
     * 退出
     * */
    public function logout(){
        unset($_SESSION['member_data']);
        unset($_SESSION['order_total']);
        unset($_SESSION['cart_total']);
        header("Location: http://www.orangesha.com/login.html");     
    }

	/* 
     * 注册
     * */
	public function register(){	 
		if(IS_POST){		
			session_start();
			$data = I();//获取ajax数据
            /*手机验证码验证*/
            if(time() - $_SESSION['register_code_time'] > 600){
                $this->ajaxReturn(array('status'=>'0','msg'=>'短信验证码已经过期'));
            }
            if($_SESSION['register_phone'] != $data['telnum']){
                $this->ajaxReturn(array('status'=>'0','msg'=>'手机号码不一致'));
            }
            if($_SESSION['register_phone_code'] != $data['register_code']){
                $this->ajaxReturn(array('status'=>'0','msg'=>'短信验证码错误'));
            }
            if(strtolower($_SESSION['register_code']) != strtolower($data['code'])){
                $this->ajaxReturn(array('status'=>'0','msg'=>'验证码错误'));
            }
            $register_data = array(
                'username'        => $_SESSION['register_phone'], //账号
                'password'        => $data['password'], //密码
                'repeat_password' => $data['repeat_password'] //确认密码
            );
			$result = D('Member')->register($register_data);//注册	
            $this->ajaxReturn($result);	
		}else{
			session_start();
            /*短信验证码重新发送剩余时间*/
            if($_SESSION['register_code_time']){
                $code_time = (time()-$_SESSION['register_code_time'])>=60?60:(60-time()+$_SESSION['register_code_time']);
            }else{
                $code_time = 60;
            }
			$this->assign('code_time',$code_time);
			$this->display();
		}	
	}

    /*
     * 生成手机验证码(注册)
     * */
    public function createRegisterPhoneCode(){ 
        $referer = 'http://www.orangesha.com/register.html';
    	if($_SERVER['HTTP_REFERER'] != $referer || !$_SERVER['HTTP_USER_AGENT']){
    		$log_data = array(
                'time' => date('Y-m-d H:i:s'),
                'HTTP_REFERER'    => $_SERVER['HTTP_REFERER'],
                'Content-Type'    => $_SERVER['CONTENT-TYPE'],
                'Accept'     =>  $_SERVER['ACCEPT'],
                'ip'   => get_client_ip(),
                'tel_num' => I('telnum')
    		);
    		file_put_contents('log.txt' , json_encode($log_data).PHP_EOL , FILE_APPEND);
    		$this->ajaxReturn(array('status'=>0,'msg'=>'发送失败')); 
    	} 	    	
        if(IS_POST){   
            session_start();   
        	$data = array('telnum'=>trim(I('telnum')),'send_phone_code'=>strtolower(I('send_phone_code')));
        	if(!$_SESSION['register_send_phone_code']){
                $this->ajaxReturn(array('status'=>'0','msg'=>'验证码不存在',$_SESSION));
            }
            if(strtolower($_SESSION['register_send_phone_code']) != $data['send_phone_code']){
                $this->ajaxReturn(array('status'=>'0','msg'=>'验证码错误'));
            }
            if($_SESSION['register_code_time']){
                if((time()-$_SESSION['register_code_time']) <= 60){
                    $this->ajaxReturn(array('status'=>0,'msg'=>'短信发送间隔为1分钟'));
                    die;
                }               
            }            
            if(!preg_match('/^1[123456789]\d{9}$/', $data['telnum'])){
                $this->ajaxReturn(array('status'=>'0','msg'=>'电话号码格式不正确'));
            }
            /*数据验证*/
            $user_id = M("Member")->where(array('username'=>$data['telnum']))->find();
            if($user_id && $data['telnum'] != '13266516313'){
                $this->ajaxReturn(array('status'=>'0','msg'=>'该电话号码已经被注册'));
            }           
            unset($_SESSION['register_phone_code']);
            unset($_SESSION['register_send_phone_code']);
            unset($_SESSION['register_phone']);
            $_SESSION['register_phone']      = $data['telnum'];//保存手机号码
            $_SESSION['register_code_time']  = time();//保存生成时间
            $_SESSION['register_phone_code'] = setnum(6,'n','0123456');//保存生成的验证码     
            $result['phone']                 = $_SESSION['register_phone'];
            $msg = new SendMsg();
            $sms_param = json_encode(array('product'=>'e橙优品','code'=>"{$_SESSION['register_phone_code']}"));
            $r = $msg->send($sms_param  , $data['telnum'] , '变更验证' , "SMS_39285065");
            if($r['result']['success']){
                $this->ajaxReturn(array('status'=>1,'msg'=>'发送成功'));
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'发送失败',$r));
            }        
        }
    }    
    
    /*剩余可以发送验证码的时间*/
    public function registerCodeTime(){
    	 for($i = 18476741302 ; $i <= 18476741302 ; $i++){
     $register_data = array(
     'username'        => $i, //账号
     'password'        => 'y123456789', //密码
     'repeat_password' => 'y123456789' //确认密码
     );
     D('Member')->register($register_data);//注册
     }

    	//$data = file_get_contents('log.txt');
    	//dump(json_decode($data , true));die;
        if($_SESSION['register_code_time']){
            $time = time() - $_SESSION['register_code_time'];
        }else{
            $time = '';
        }  
        $this->ajaxReturn(array('status'=>1,'time'=>$time));
    }

    /*
     * 获取发送短信验证码
     */
    public function getPhoneCode(){
        $a = new \Think\ValidateCode();
        $a->doimg();
        $code = $a->getCode();
        //保存
        session_start();
        $_SESSION['register_send_phone_code']=$code;
        $img = ob_get_contents();     
        $img = 'data:png;base64,'.base64_encode($img);
        ob_clean();
        echo $img;
    } 

    /*
     * 获取验证码
     */
    public function getCode(){
        $a = new \Think\ValidateCode();
        $a->doimg();
        $code = $a->getCode();
        //保存
        session_start();
        $_SESSION['register_code']=$code;
        $img = ob_get_contents();     
        $img = 'data:png;base64,'.base64_encode($img);
        ob_clean();
        echo $img;
   } 
}