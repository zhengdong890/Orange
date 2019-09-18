<?php
/*
 * 会员信息
 * */
namespace Home\Controller;
use Think\Controller;
use Org\Msg\SendMsg;
header("content-type:text/html;charset=utf-8");
class MemberDataController extends Controller {    
    public function _initialize(){       
        if(empty($_SESSION['member_data'])){
            $this->ajaxReturn(array('status'=>0,'msg'=>'请先登录'));
        };
    }
    
    
/******************************************修改密码******************************************************/
    
    /*
     * 修改密码
     * */
    public function passwordUpdate(){
        if(IS_AJAX){
            $data = I();
            /*手机验证码验证*/
            if(!$_SESSION['pwd_code_time']){
                $this->ajaxReturn(array('status'=>'0','msg'=>'请先发送短信'));die;
            }
            if(time() - $_SESSION['pwd_code_time'] > 600){
                $this->ajaxReturn(array('status'=>'0','msg'=>'短信验证码已经过期'));die;
            }
            if(!$_SESSION['pwd_phone_code_state']){
                $this->ajaxReturn(array('status'=>'0','msg'=>'短信验证码未通过验证'));die;
            }
            $data['id'] = $_SESSION['member_data']['id'];
            $result = D('Member')->passwordUpdate($data);
            if($result['status']){
                unset($_SESSION['member_data']);
            }
            $this->ajaxReturn($result);
        }else{
            $this->display();
        }
    }
    
    /*
     * 生成手机短信验证码(修改密码)
     * */
    public function createPasswordUpdateCode(){
        if(IS_AJAX){
            $member_id = $_SESSION['member_data']['id'];
            if($_SESSION['pwd_code_time']){
                if((time() - $_SESSION['pwd_code_time']) <= 60){
                    $this->ajaxReturn(array('status'=>0,'msg'=>'短信发送间隔为1分钟'));
                    die;
                }
            }
            $telnum  = M('Member')->where(array('id'=>$member_id))->getField('username');
            session_start();
            unset($_SESSION['pwd_phone_code']);
            $_SESSION['pwd_code_time']  = time();//保存修改密码的短信验证码的生成时间
            $_SESSION['pwd_phone_code'] = setnum(6,'n','0123456');//保存生成的修改密码的短信验证码
            $msg = new SendMsg();
            $sms_param = json_encode(array('product'=>'e橙优品','code'=>$_SESSION['pwd_phone_code']));
            $r = $msg->send($sms_param  , $telnum , '变更验证' , "SMS_39255187");
            if($r['result']['success']){
                $this->ajaxReturn(array('status'=>1,'msg'=>'发送成功'));
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'发送失败',$r));
            }
        }
    }
    
    /*
     * 验证 生成手机短信验证码(修改密码)是否正确
     * */
    public function checkPasswordUpdateCode(){
        if(IS_AJAX){
            $data = I();
            if(!$data['phone_code'] || $_SESSION['pwd_phone_code'] != $data['phone_code']){
                $this->ajaxReturn(array('status'=>'0','msg'=>'短信验证码错误'));die;
            }else{
                unset($_SESSION['pwd_phone_code']);
                $_SESSION['pwd_phone_code_state'] = 1;
            }
            $this->ajaxReturn(array('status'=>'1','msg'=>'success'));
        }
    }
    
    /*
     * 获取生成手机短信验证码(修改密码) 剩余的时间
     * */
    public function getPwdCodeTime(){
        if($_SESSION['pwd_code_time']){
            if((time() - $_SESSION['pwd_code_time']) >= 60){
                $this->ajaxReturn(array('status'=>1,'msg'=>'ok','data'=>0));
            }else{
                $this->ajaxReturn(array('status'=>1,'msg'=>'ok','data'=>60 - time() + $_SESSION['pwd_code_time']));
            }
        }else{
            $this->ajaxReturn(array('status'=>1,'msg'=>'ok','data'=>0));
        }
    }
    
/******************************************更换手机号码******************************************************/     
    
    /*
     * 第一步
     * 生成 旧的手机号码短信验证码(更换手机号码)
     * */
    public function createChangePhoneCode(){
        if(IS_AJAX){
            $member_id = $_SESSION['member_data']['id'];
            if($_SESSION['change_code_time']){
                if((time()-$_SESSION['change_code_time']) <= 60){
                    $this->ajaxReturn(array('status'=>0,'msg'=>'短信发送间隔为1分钟'));
                    die;
                }
            }
            $telnum  = M('Member')->where(array('id'=>$member_id))->getField('username');
            session_start();
            unset($_SESSION['change_phone_code']);
            unset($_SESSION['change_phone']);
            $_SESSION['change_code_time']  = time();//保存生成时间
            $_SESSION['change_phone_code'] = setnum(6,'n','0123456');//保存生成的验证码
            $msg = new SendMsg();
            $sms_param = json_encode(array('product'=>'手机号码','code'=>$_SESSION['change_phone_code']));
            $r = $msg->send($sms_param  , $telnum , '变更验证' , "SMS_39255187");
            if($r['result']['success']){
                $this->ajaxReturn(array('status'=>1,'msg'=>'发送成功'));
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'发送失败',$r));
            }
        }
    }  
    
    /*
     * 第二步
     * 验证 旧的手机号码短信验证码(更换手机号码)是否正确
     * */
    public function checkChangePhoneCode(){
        if(IS_AJAX){
            $data = I();
            if(!$data['phone_code'] || $_SESSION['change_phone_code'] != $data['phone_code']){
                $this->ajaxReturn(array('status'=>'0','msg'=>'短信验证码错误'));die;
            }else{
                unset($_SESSION['change_phone_code']);
                $_SESSION['change_phone_code_state'] = 1;//标记为验证成功
            }
            $this->ajaxReturn(array('status'=>'1','msg'=>'success'));
        }
    }
    
    /*
     * 第三步
     * 生成 新的手机号码短信验证码(更换手机号码)
     * */
    public function createNewPhoneCode(){
        if(IS_AJAX){
            $member_id = $_SESSION['member_data']['id'];
            $telnum    = I('telnum');
            /*验证手机号码格式*/
            if(!preg_match('/^1[34578]\d{9}$/', $telnum)){
                $this->ajaxReturn(array('status'=>'0','msg'=>'电话号码格式不正确'));
            }
            /*验证是否注册*/
            $user_id   = M("Member")->where(array('username'=>$telnum))->find();
            if($user_id){
                $this->ajaxReturn(array('status'=>'0','msg'=>'该电话号码已经被注册'));
            }
            /*发送间隔限制 1分钟*/
            if($_SESSION['new_code_time']){
                if((time()-$_SESSION['new_code_time']) <= 60){
                    $this->ajaxReturn(array('status'=>0,'msg'=>'短信发送间隔为1分钟'));
                    die;
                }
            }           
            session_start();
            unset($_SESSION['new_phone_code']);
            unset($_SESSION['new_phone']);
            $_SESSION['new_phone']      = $telnum;//保存手机号码
            $_SESSION['new_code_time']  = time();//保存生成时间
            $_SESSION['new_phone_code'] = setnum(6,'n','0123456');//保存生成的验证码           
            $msg = new SendMsg();
            $sms_param = json_encode(array('product'=>'手机号码','code'=>$_SESSION['new_phone_code']));
            $r = $msg->send($sms_param  , $telnum , '变更验证' , "SMS_39285065");
            if($r['result']['success']){
                $this->ajaxReturn(array('status'=>1,'发送成功'));
            }else{
                $this->ajaxReturn(array('status'=>0,'发送失败',$r));
            }
        }
    }
    
    /*
     * 第四步
     * 修改手机号码
     * */
    public function changePhone(){
        if(IS_AJAX){
            $member_id = $_SESSION['member_data']['id'];
            $data = I();
            $telnum = $_SESSION['new_phone'];
            /*手机验证码是否过期*/
            if(time() - $_SESSION['new_code_time'] > 600 || !$telnum){
                $this->ajaxReturn(array('status'=>'0','msg'=>'验证码已经过期','code'=>4));
                die;
            }
            /*验证码是否正确*/
            if($data['code'] != $_SESSION['new_phone_code']){
                $this->ajaxReturn(array('status'=>0,'msg'=>'短信验证码不正确','code'=>1));
                die;
            }
            $r = M('Member')->where(array('id'=>$member_id))->save(array('username'=>$telnum));
            if($r === false){
            	M('Member_data')->where(array('member_id'=>$member_id))->save(array('telnum'=>$telnum));
                $this->ajaxReturn(array('status'=>'0','msg'=>'操作失败','code'=>6));
                die;
            }else{
                $_SESSION['member_data']['username'] = $telnum;
            }
            $this->ajaxReturn(array('status'=>'1','msg'=>'success','code'=>7));
        }else{
            $this->display();
        }
    }
    
    /*
     * 获取生成手机短信验证码(更换手机号码) 剩余的时间
     * */
    public function getChangeCodeTime(){
        if($_SESSION['new_code_time']){
            if((time() - $_SESSION['new_code_time']) >= 60){
                $this->ajaxReturn(array('status'=>1,'msg'=>'ok','data'=>0));
            }else{
                $this->ajaxReturn(array('status'=>1,'msg'=>'ok','data'=>60 - time() + $_SESSION['new_code_time']));
            }
        }else{
            $this->ajaxReturn(array('status'=>1,'msg'=>'ok','data'=>0));
        }
    }
}