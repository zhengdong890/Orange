<?php
/*
 * 找回密码模块
 * */
namespace Home\Controller;
use Think\Controller;
use Org\Msg\SendMsg;
use Org\Phpmailer\SendMail;
header("content-type:text/html;charset=utf-8");
class FindPasswordController extends Controller {      
    /*
     * 第一步
     * 选择找回的账号
     * */
    public function getTelNum(){
       // echo md5(md5('a12345678Yz9BMwe9pv1481252679'));
        if(IS_AJAX){
            $telnum = I('telnum');
            $member = M('Member')->where(array('username'=>$telnum))->field('id,is_security')->find();
            if(!$member['id']){
                $this->ajaxReturn(array('status'=>0,'msg'=>'账号不存在'));
            }else{
                $_SESSION['find_member_id']     = $member['id'];
                $_SESSION['find_member_telnum'] = $telnum;
                /*获取拥有的验证方式*/
                $email =  M('Member_data')->where(array('member_id'=>$member['id']))->getField('email');
                $type = array(
                    'is_security' => $member['is_security'],//是否设置了密保
                    'is_email'    => $email ? '1' : '0'
                );
                $this->ajaxReturn(array('status'=>'1','msg'=>'ok','data'=>$type));
            }
        }
    }
    
/**************************************手机号码找回密码************************************************/    
    
    /* 1
     * 生成手机短信验证码(找回密码)
     * */
    public function telnumReplacementPwdCode(){
        if(IS_AJAX){
            $member_id = $_SESSION['find_member_id'];
            $telnum    = $_SESSION['find_member_telnum'];
            if(!$member_id || !$telnum){
                $this->ajaxReturn(array('status'=>'0','msg'=>'已过期'));die;
            }
            if($_SESSION['find_telnum_time']){
                if((time() - $_SESSION['find_telnum_time']) <= 60){
                    $this->ajaxReturn(array('status'=>0,'msg'=>'短信发送间隔为1分钟'));
                    die;
                }
            }
            unset($_SESSION['find_telnum_code']);
            unset($_SESSION['find_telnum_code_state']);
            $_SESSION['find_telnum_time'] = time();//保存找回密码的短信验证码的生成时间
            $_SESSION['find_telnum_code'] = setnum(6,'n','0123456');//保存生成的找回密码的短信验证码
            $msg = new SendMsg();
            $sms_param = json_encode(array('product'=>'e橙优品','code'=>$_SESSION['find_telnum_code']));
            $r = $msg->send($sms_param  , $telnum , '变更验证' , "SMS_39255187");
            if($r['result']['success']){
                $this->ajaxReturn(array('status'=>1,'msg'=>'发送成功'));
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>$r['sub_msg']));
            }
            $this->ajaxReturn(array('status'=>1,'msg'=>'发送成功'));
        }
    }
    
    /* 1
     * 验证 生成手机短信验证码(修改密码)是否正确
     * */
    public function checkTelnumCode(){
        if(IS_AJAX){
            $member_id = $_SESSION['find_member_id'];
            if(!$member_id){
                $this->ajaxReturn(array('status'=>'0','msg'=>'已过期'));die;
            }
            $data = I();
            if($_SESSION['find_telnum_code'] != $data['phone_code']){
                $this->ajaxReturn(array('status'=>'0','msg'=>'短信验证码错误'));die;
            }else{
                unset($_SESSION['find_telnum_code']);
                $_SESSION['find_telnum_code_state'] = 1;//标志为验证成功
            }
            $this->ajaxReturn(array('status'=>'1','msg'=>'success'));
        }
    }
    
    /*
     * 手机号码
     * 重置密码
     * */
    public function telnumReplacementPassword(){
        if(IS_AJAX){
            $data = I();
            $member_id = $_SESSION['find_member_id'];
            if(!$member_id){
                $this->ajaxReturn(array('status'=>'0','msg'=>'已过期'));die;
            }
            if(!$_SESSION['find_telnum_code_state']){
                $this->ajaxReturn(array('status'=>'0','msg'=>'验证未通过'));die;
            }
            $data['id'] = $member_id;
            $result     = D('Member')->replacementPassword($data);
            if($result['status']){
                unset($_SESSION['find_telnum_code']);
                unset($_SESSION['find_telnum_code_state']); 
            }
            $this->ajaxReturn($result);
        }
    }  

    
    /* 
     * 获取生成手机短信验证码(找回密码) 剩余的时间
     * */
    public function getTelnumCodeTime(){
        if($_SESSION['find_telnum_time']){
            if((time() - $_SESSION['find_telnum_time']) >= 60){
                $this->ajaxReturn(array('status'=>1,'msg'=>'ok','data'=>0));
            }else{
                $this->ajaxReturn(array('status'=>1,'msg'=>'ok','data'=>60 - time() + $_SESSION['find_telnum_time']));
            }
        }else{
            $this->ajaxReturn(array('status'=>1,'msg'=>'ok','data'=>0));
        }
    }
       
    
/****************************************邮箱找回密码************************************************/

    /*
     * 第一步
     * 发送邮件
     * */
    public function sendEmail(){
        if(IS_AJAX){
            if($_SESSION['find_pwd_email_time']){
                if((time() - $_SESSION['find_pwd_email_time']) <= 60){
                    $this->ajaxReturn(array('status'=>0,'msg'=>'发送间隔为1分钟'));
                    die;
                }
            }           
            if(!$_SESSION['find_member_id']){                
                $this->ajaxReturn(array('status'=>'0','msg'=>'已过期'));die;
            }else{
                $id = $_SESSION['find_member_id'];
            }            
            $email    = M('Member_data')->where(array('member_id'=>$id))->getField('email');
            $username = M('Member')->where(array('id'=>$id))->getField('username');
            $code     = setnum(6,'n','0123456');
            $body     = "尊敬的{$username}：<br/>您正在通过邮箱找回密码<br/>验证码 : {$code}<br/><p style='text-align:left'>e橙优品</p>";
            $config   = array(
                'username'    => '18476746058@163.com',
                'password'    => 'yanjin19910728',
                'from'        => '18476746058@163.com',
                'from_name'   => 'e橙优品',
                'to_mail'     => $email,
                'subject'     => '找回密码',
            );
            $send = new SendMail($config);
            $send->send($body);
            if($send){
                $_SESSION['find_pwd_email_time'] = time();
                $_SESSION['find_pwd_email_code'] = $code;
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
     * 第二步
     * 验证邮箱验证码
     * */
    public function emailCodeCheck(){
        if(IS_AJAX){
            $code = I('code');
            if(!$_SESSION['find_pwd_email_code']){
                $this->ajaxReturn(array('status'=>'0','msg'=>'已过期'));die;
            }
            if($_SESSION['find_pwd_email_code'] != $code){
                $this->ajaxReturn(array('status'=>'0','msg'=>'验证码错误'));die;
            }
            unset($_SESSION['find_pwd_email_code']);
            $_SESSION['find_pwd_email_state'] = 1;//标志为验证成功            
            $this->ajaxReturn(array('status'=>'1','msg'=>'success'));
        }    
    }
    
    /*
     * 第三步
     * 重置密码
     * */
    public function emailReplacementPassword(){
        if(IS_AJAX){
            $member_id = $_SESSION['find_member_id'];
            if(!$member_id || !$_SESSION['find_pwd_email_state']){
                $this->ajaxReturn(array('status'=>'0','msg'=>'验证已经过期'));die;
            }
            $data = I();
            $data['id'] = $member_id;
            $result     = D('Member')->replacementPassword($data);
            $this->ajaxReturn($result);
        }else{
            $this->display();
        }
    }
    
/******************************************根据密保找回密码*****************************************/
    
    /*
     * 第一步
     * 根据会员账号获取已经设置的密保问题
     * */
    public function usernameGetSecurity(){
        if(IS_AJAX){
            $member_id = $_SESSION['find_member_id'];
            if(!$member_id){
                $this->ajaxReturn(array('status'=>'0','msg'=>'已过期'));die;
            }
            $data     = C('SECURITY');
            $list_    = M('Member_security')->where(array('member_id'=>$member_id))->field('question , answer , member_id')->select();
            $list     = array();
            $security = array();
            foreach($list_ as $k => &$v){
                $list[] = array(
                    'question_id' => $v['question'],
                    'question'    => $data[$v['question']]
                );
                $security[$v['qusetion']] = $v['answer'];
            }
            unset($_SESSION['security_check_state']);
            if(count($list) < 0){
                $this->ajaxReturn(array('status'=>1,'msg'=>'暂未设置'));
            }else{
                $_SESSION['find_pwd_security']  = $security;
                $this->ajaxReturn(array('status'=>1,'msg'=>'success','data'=>$list));
            }
        }
    }
    
    /*
     * 第二步
     * 检测密保问题是否正确
     * */
    public function checkSecurity(){
        $member_id = $_SESSION['find_member_id'];
        if(!$member_id){
            $this->ajaxReturn(array('status'=>'0','msg'=>'已过期'));die;
        }
        $security = $_SESSION['find_pwd_security'];
        $data     = I();
        foreach($security as $k => $v){
            if($data[$k] != $v){
                $this->ajaxReturn(array('status'=>0,'msg'=>'密保验证不通过'));die;
            }
            unset($security[$k]);
        }
        if(count($security[$k]) > 0){
            $this->ajaxReturn(array('status'=>0,'msg'=>'密保验证不通过'));die;
        }
        $_SESSION['security_check_state'] = 1;//标志为验证成功
        $this->ajaxReturn(array('status'=>1,'msg'=>'success'));
    }
    
    /*
     * 第三步
     * 重置密码
     * */
    public function replacementPassword(){
        $member_id = $_SESSION['find_member_id'];
        if(!$member_id || !$_SESSION['security_check_state']){
            $this->ajaxReturn(array('status'=>'0','msg'=>'已过期'));die;
        }
        $data['id'] = $member_id;
        $result     = D('Member')->replacementPassword($data);
        $this->ajaxReturn($result);
    }    
}
