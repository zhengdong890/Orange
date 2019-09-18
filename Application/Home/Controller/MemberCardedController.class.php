<?php
/*
 * 身份认证
 * */
namespace Home\Controller;
use Think\Controller;
use Org\Msg\SendMsg;
header("content-type:text/html;charset=utf-8");
class MemberCardedController extends Controller {    
    public function _initialize(){       
        if(empty($_SESSION['member_data'])){
            $this->ajaxReturn(array('status'=>0,'msg'=>'请先登录'));
        };
    }
    
    /*
     * 第一步
     * 生成 手机号码短信验证码(身份验证)
     * */
    public function createQulificationCode(){
        if(IS_AJAX){
            $member_id = $_SESSION['member_data']['id'];
            if($_SESSION['qulification_code_time']){
                if((time()-$_SESSION['qulification_code_time']) <= 60){
                    $this->ajaxReturn(array('status'=>0,'msg'=>'短信发送间隔为1分钟'));
                    die;
                }
            }
            $telnum  = M('Member')->where(array('id'=>$member_id))->getField('username');
            session_start();
            unset($_SESSION['qulification_phone_code']);
            unset($_SESSION['qulification_phone']);
            $_SESSION['qulification_code_time']  = time();//保存生成时间
            $_SESSION['qulification_phone_code'] = setnum(6,'n','0123456');//保存生成的验证码
            $msg = new SendMsg();
            $sms_param = json_encode(array('product'=>'身份证','code'=>$_SESSION['qulification_phone_code']));
            $r = $msg->send($sms_param  , $telnum , '变更验证' , "SMS_39275128");
            $this->ajaxReturn($r);
        }
    }

    /*
     * 第二步
     * 验证 手机号码短信验证码(身份验证)是否正确
     * */
    public function checkQulificationCode(){
        if(IS_AJAX){
            $data = I();
            if($_SESSION['qulification_code_time']){
                if((time()-$_SESSION['qulification_code_time']) >= 600){
                    $this->ajaxReturn(array('status'=>0,'msg'=>'短信已经过期'));
                    die;
                }
            }else{
                $this->ajaxReturn(array('status'=>'0','msg'=>'请先发送短信'));die;
            }
            if($_SESSION['qulification_phone_code'] != $data['phone_code']){
                $this->ajaxReturn(array('status'=>'0','msg'=>'短信验证码错误'));die;
            }else{
                unset($_SESSION['qulification_phone_code']);
                $_SESSION['qulification_phone_code_state'] = 1;//标记为验证成功
            }
            $this->ajaxReturn(array('status'=>'1','msg'=>'success'));
        }
    }
    
    /*
     * 第三步
     * 身份信息提交申请
     * */    
    public function identityQualification(){
        if(IS_AJAX){
            if(!$_SESSION['qulification_phone_code_state']){
                $this->ajaxReturn(array('status'=>'0','msg'=>'短信验证码未通过验证'));die;
            }   
            $data      = I();
            $member_id = $_SESSION['member_data']['id'];
            /*上传图片*/
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 2145728 ;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
            // 上传文件
            $info = $upload->upload();
            if($info) {
                if($info['carded_thumb1']){
                    $data['carded_thumb1'] = $upload->rootPath.$info['carded_thumb1']['savepath'].$info['carded_thumb1']['savename'];
                }
                if($info['carded_thumb2']){
                    $data['carded_thumb2'] = $upload->rootPath.$info['carded_thumb2']['savepath'].$info['carded_thumb2']['savename'];
                }
                if($info['carded_thumb3']){
                    $data['carded_thumb3'] = $upload->rootPath.$info['carded_thumb3']['savepath'].$info['carded_thumb3']['savename'];
                }
            }else{
		       $this->ajaxReturn(array(
		           'status' => 0,
		           'msg'=>$upload->getError()		           
		       ));die;
		    }            
            $result    = D('MemberCarded')->qualification($data , $member_id);
            if($result['status']){
                unset($_SESSION['qulification_phone_code_state']);
            }
            $this->ajaxReturn($result);
        }else{
            $this->display();
        }
    }
}