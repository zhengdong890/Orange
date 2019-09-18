<?php
/*
 * 邮箱模块
 * */
namespace Home\Controller;
use Think\Controller;
use Org\Msg\SendMsg;
use Org\Phpmailer\SendMail;
header("content-type:text/html;charset=utf-8");
class MemberEmailController extends Controller {
    public function _initialize(){    
        if(ACTION_NAME != 'emailCheck' && empty($_SESSION['member_data'])){
            $this->ajaxReturn(array('status'=>0,'msg'=>'请先登录'));
        };
    }

/******************************************设置邮箱******************************************/ 
    
    /*
     * 第一步
     * 生成 手机号码短信验证码(邮箱绑定)
     * */
    public function CreateEmailBindingCode(){
        if(IS_AJAX){
            $member_id = $_SESSION['member_data']['id'];
            if($_SESSION['email_code_time']){
                if((time()-$_SESSION['email_code_time']) <= 60){
                    $this->ajaxReturn(array('status'=>0,'msg'=>'短信发送间隔为1分钟'));
                    die;
                }
            }
            $telnum  = M('Member')->where(array('id'=>$member_id))->getField('username');
            session_start();
            unset($_SESSION['email_phone_code']);
            $_SESSION['email_code_time']  = time();//保存生成时间
            $_SESSION['email_phone_code'] = setnum(6,'n','0123456');//保存生成的验证码
            $msg = new SendMsg();
            $sms_param = json_encode(array('product'=>'邮箱设置','code'=>$_SESSION['email_phone_code']));
            $r = $msg->send($sms_param  , $telnum , '变更验证' , "SMS_39360127");
            if($r['result']['success']){
                $this->ajaxReturn(array('status'=>1,'msg'=>'发送成功'));
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'发送失败',$r));
            }
        }
    }
    
    /*
     * 第二步
     * 验证 手机号码短信验证码(邮箱绑定)是否正确
     * */
    public function checkEmailBindingCode(){
        if(IS_AJAX){
            $data = I();
            if(!$data['phone_code'] || $_SESSION['email_phone_code'] != $data['phone_code']){
                $this->ajaxReturn(array('status'=>'0','msg'=>'短信验证码错误'));die;
            }else{
                unset($_SESSION['email_code_time']);
                $_SESSION['email_phone_code_state'] = 1;//标记为验证成功
            }
            $this->ajaxReturn(array('status'=>'1','msg'=>'success'));
        }
    }  
    
    /*
     * 第三步
     * 验证码
     */
    public function getCode(){
        $a = new \Think\ValidateCode();
        $a->doimg();
        $code = $a->getCode();
        unset($_SESSION['email_code']);
        session_start();
        $_SESSION['email_code']=$code;
    } 
    
    /*
     * 第三步
     * 提交邮箱地址发送邮件
     * */
    public function addEmail(){
        if(IS_AJAX){
             $email    = I('email');
             $id       = $_SESSION['member_data']['id'];
             $username = $_SESSION['member_data']['username'];
             if(strtolower($_SESSION['email_code']) != strtolower(I('code'))){
                 $this->ajaxReturn(array('status'=>'0','msg'=>'验证码错误'));die;
             }
             if(!preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $email)){
                 $this->ajaxReturn(array('status'=>'0','msg'=>'邮箱格式不正确'));die;
             }
             if(!$_SESSION['email_phone_code_state']){
                 $this->ajaxReturn(array('status'=>'0','msg'=>'短信验证码未通过验证'));die;
             }
             $time   = time();
             $get    = array(  
                 'token' => md5(sha1(md5($email.$time).$id).sha1($time)),
                 'id'    => $id,
                 'time'  => $time,
                 'email' => $email,                                 
             );      
             $get    = implode('-' , $get);
             $url    = "http://www.orangesha.com/Home/MemberEmail/emailCheck?token=$get";
             $body   = "尊敬的{$username}：<br/>感谢您在我站注册了新帐号 <br/>请点击链接绑定您的帐号。<br/><a href='$url' target='_blank'>$url</a><br/>如果以上链接无法点击，请将它复制到你的浏览器地址栏中进入访问，该链接24小时内有效。<br/>如果此次绑定请求非你本人所发，请忽略本邮件。<br/><p style='text-align:left'>e橙优品</p>";
             $config = array(
                 'username'    => '18476746058@163.com',
                 'password'    => 'yanjin19910728',
                 'from'        => '18476746058@163.com',
                 'from_name'   => 'e橙优品',
                 'to_mail'     => $email,
                 'subject'     => '邮箱绑定操作',
             );
             $send = new SendMail($config);
             $send->send($body);
             if($send){
                 unset($_SESSION['email_phone_code_state']);
                 $this->ajaxReturn(array(
                     'status' => 1,
                     'msg'    => 'success'
                 ));
             }else{
                 $this->ajaxReturn(array(
                     'status' => 0,
                     'msg'    => $send->getError()
                 ));
             }
        }
    }
    
    /*
     * 第四步
     * 验证邮箱地址
     * */    
    public function emailCheck(){
        $data = I('token');
        $data = explode('-' , $data);
        $get  = array(
            'token' => $data[0],
            'id'    => $data[1],
            'time'  => $data[2],
            'email' => $data[3],
        );
        if(!$get['time'] || (time() - $get['time']) > 24 * 3600){
            echo '邮件已过期';die;
        }
        if(!intval($get['id'])){
           echo 'id错误'; die;
        }
        if(!preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $get['email'])){
            echo 'email格式错误';die;
        }
        if(!$get['token'] || md5(sha1(md5($get['email'].$get['time']).$get['id']).sha1($get['time'])) != $get['token']){
            echo '签名错误';die;
        }
        $r = M('Member_data')->where(array('member_id'=>$get['id']))->save(array('email'=>$get['email']));
        if($r !== false){
            echo '验证成功';
            //$this->redirect('Member_center/accountSafety');
        }else{
            '验证失败';
        }
    }
}