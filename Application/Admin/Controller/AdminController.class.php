<?php
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class AdminController extends Controller {
    /*
     * 后台管理员列表
     * */
    public function adminList(){
        if(IS_AJAX){
            $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
            $listRows = intval(I('listRows'))?intval(I('listRows')):10;
            $list_    = M('Admin as a')
                      ->join('tp_admin_data as b on a.id=b.admin_id')
                      ->field('a.id,a.username,a.lock,a.login_ip,a.login_time,b.name')
                      ->limit($firstRow,$listRows)
                      ->select();
            $ids      = array();
            $list     = array();
            foreach($list_ as $v){
                $ids[] = $v['id'];
                $list[$v['id']] = $v;
            }
            $ids = implode(',' , $ids) ? implode(',' , $ids) : '';
            $group = M('Auth_group_access as a')
                   ->join('tp_auth_group as b on b.id=a.group_id')
                   ->where(array('a.uid'=>array('in' , $ids)))
                   ->field('a.uid,b.title,a.group_id')
                   ->select();
            foreach($group as $v){
                $list[$v['uid']]['group_name'] = $v['title'];
                $list[$v['uid']]['group_id'] = $v['group_id'];
            }
            $this->ajaxReturn(array('data'=>$list,'total'=>M('Admin')->count()));
        }else{
            $this->display();
        }       
    }  
    
    /*
     * 添加管理员账号
     * */
    public function adminAdd(){
        if(IS_POST){
            $data   = I();
            $result = D('Admin')->adminAdd($data);
            $this->ajaxReturn($result);
        }
    }

    /*
     * 修改管理员账号
     * */
    public function adminUpdate(){
        if(IS_POST){
            $data   = I();
            $result = D('Admin')->adminUpdate($data);
            $this->ajaxReturn($result);
        }
    }
    
    /*
     * 管理员锁定状态改变
     * */    
    public function lockChange(){
        if(IS_POST){
            $id   = intval(I('id'));
            $lock = intval(I('lock')) == 1 ? 1 : 0 ;
            if(!$id){
                $this->ajaxReturn(array('status'=>0,'msg'=>'id错误'));die;
            }
            $r = M('Admin')->where(array('id'=>$id))->save(array('lock'=>$lock));
            if($r !== false){
                $this->ajaxReturn(array('status'=>1,'msg'=>'ok'));die;
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'失败'));die;
            }
        }  
    }
    
    /*登陆*/
    public function login(){
        if(IS_POST){
            $data = I();
            if(empty($data['code'])){
                $this->ajaxReturn(array('status'=>0,'msg'=>'验证码不能为空'));die;
            }
            if(strtolower($_SESSION['code']) != strtolower($data['code'])){
                $this->ajaxReturn(array('status'=>0,'msg'=>'验证码错误'));die;
            }
            $result = D('Admin')->loginValidate($data);
            if($result['status']){//验证通过
                $_SESSION['admin'] = $result['admin_data'];
            }
            $this->ajaxReturn($result);
        }
        $this->display();
    }
     
    public function passwordUpdate(){
        session_start;
        $admin = $_SESSION['admin'];
        if(empty($admin)){
            $this->redirect('Index/login');
        }
        if(IS_AJAX){
            $password=M('Admin')->where('id='.$admin['id'])->find();
            $password=$password['password'];
            $data=$_POST;
            session_start();
            $code=strtolower($_SESSION['code']);
            if($code!=strtolower($data['code'])){
                echo '验证码错误';
            }else{
                if(md5($data['oldpassword'])!=$password){
                    echo '原始密码错误';
                }else
                    if($data['password1']!=$data['password2']){
                    echo '两次密码不一致';
                }else{
                    $data1=array('id'=>$admin['id'],'password'=>md5($data['password1']),'update_time'=>date('Y-m-d H:i:s'));
                    M('Admin')->save($data1);
                    session_start();
                    unset($_SESSION['admin']);
                    echo 1;
                }
            }
        }else{
            $this->display();
        }
    }
    
    //退出
    public function logut(){
        session_start();
        session(null);
        $this->redirect('login');
    }
    
    public function code(){
        $a = new \Think\ValidateCode();
        $a->doimg();
        $code=$a->getCode();
        unset($_SESSION['code']);
        session_start();
        $_SESSION['code']=$code;
    }    
}