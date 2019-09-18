<?php
/*
 * 密保问题模块
 * */
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
use Org\Msg\SendMsg;
header("content-type:text/html;charset=utf-8");
class MemberSecurityController extends Controller {   
    public function _initialize(){          
        if( empty($_SESSION['member_data'])){
            $this->ajaxReturn(array('status'=>0,'msg'=>'请先登录'));
        };
    }
    
    /*
     * 获取会员已经设置的密保
     * */
    public function getSecurity(){
        if(IS_AJAX){
            $data = C('SECURITY');
            $list = M('Member_security')->where(array('member_id'=>$_SESSION['member_data']['id']))->field('question as question_id')->select();
            foreach($list as $k => &$v){
                $v['question'] = $data[$v['qusetion_id']];
            }
            if(count($list) < 0){
                $this->ajaxReturn(array('status'=>1,'msg'=>'暂未设置','data'=>$list));
            }else{
                $this->ajaxReturn(array('status'=>1,'msg'=>'success','data'=>$list));
            }
        }
    }
    
/******************************************添加密保******************************************/   
    
    /*
     * 第一步 1
     * 生成 手机号码短信验证码(添加密保)
     * */
    public function createSecurityCode(){
        if(IS_AJAX){
            $member_id = $_SESSION['member_data']['id'];
            if($_SESSION['security_code_time']){
                if((time()-$_SESSION['security_code_time']) <= 60){
                    $this->ajaxReturn(array('status'=>0,'msg'=>'短信发送间隔为1分钟'));
                    die;
                }
            }
            $telnum  = M('Member')->where(array('id'=>$member_id))->getField('username');
            session_start();
            unset($_SESSION['security_phone_code']);
            unset($_SESSION['security_phone']);
            $_SESSION['security_code_time']  = time();//保存生成时间
            $_SESSION['security_phone_code'] = setnum(6,'n','0123456');//保存生成的验证码
            $msg = new SendMsg();
            $sms_param = json_encode(array('product'=>'密保设置','code'=>$_SESSION['security_phone_code']));
            $r = $msg->send($sms_param  , $telnum , '变更验证' , "SMS_39275128");
            if($r['result']['success']){
                $this->ajaxReturn(array('status'=>1,'msg'=>'发送成功'));
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'发送失败',$r));
            }
        }
    }
    
    /*
     * 第一步 2
     * 验证 手机号码短信验证码(添加密保)是否正确
     * */
    public function checkSecurityCode(){
        if(IS_AJAX){
            $data = I();
            if(!$data['phone_code'] || $_SESSION['security_phone_code'] != $data['phone_code']){
                $this->ajaxReturn(array('status'=>'0','msg'=>'短信验证码错误'));die;
            }else{
                unset($_SESSION['security_phone_code']);
                $_SESSION['security_phone_code_state'] = 1;//标记为验证成功
            }
            $this->ajaxReturn(array('status'=>'1','msg'=>'success'));
        }
    }   
    
    /*
     * 第二步 1
     * 获取密保的问题
     * */
    public function getQuestion(){
        if(IS_AJAX){
            $data = C('SECURITY');
            $this->ajaxReturn(array('status'=>'1','msg'=>'success','data'=>$data));
        }
    } 
    
    /*
     * 第二步 2
     * ajax提交设置的密保问题 答案
     * */
    public function securityAdd(){
        if(IS_AJAX){
            if(!$_SESSION['security_phone_code_state']){
                $this->ajaxReturn(array('status'=>'0','msg'=>'短信验证码未通过验证'));die;
            }
            $data   = I();
            $result = D('MemberSecurity')->securityAdd($data , $_SESSION['member_data']['id']);
            if($result['status']){
                $_SESSION['member_data']['is_secuirty'] = 1;
                unset($_SESSION['security_phone_code_state']); 
            }
            $this->ajaxReturn($result);
        }
    } 
    

/******************************************修改密保******************************************/   
}