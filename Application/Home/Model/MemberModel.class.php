<?php
namespace Home\Model;
use Think\Model;
/**
 * 商品模块业务逻辑
 * @author 幸福无期
 */
class MemberModel extends Model{   
    /**
    * 登录验证
    * @param array data需要验证的数据 
    * @return array 返回验证结果
    */
   public function loginValidate($login_data = array()){
     	$result = array('status'=>1,'msg'=>'登录成功');
   	    /*数据验证*/
   	    $member = D("member");
        $rules  = array(
            array('username','require','必须输入手机号码'),
            array('username','/^1[34578]\d{9}$/i','手机号码格式不正确'),
            array('password','require','必须输入密码'),
            array('password','/^[_0-9a-z]{6,16}$/i','密码只能为英文、数字、下划线8-16位字符')        
        );
		if($member->validate($rules)->create($login_data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $member->getError()
            );
           return $result;
        } 
		//获取用户数据
		$user_data = M("Member")->where(array('username'=>$login_data['username']))->find();
		if(empty($user_data)){
		    return array('status'=>0,'msg'=>'账号不存在');
		}		
		if(!$user_data['lock']){
		    return array('status'=>0,'msg'=>'您的账户已经被锁定,请联系客服');
		}
		//密码连接加密
		$password  = empty($user_data['salt'])?md5(md5($login_data['password'])):md5(md5($login_data['password'].$user_data['salt']));
		if($password == $user_data['password']){
		    /*记录用户登录的数据*/
		    $login_data = array(		        
		        'login_time' => date('Y-m-d H:i:s'),
		        'login_ip'   => get_client_ip(1)
		    );
		    $r = M('Member')->where(array('id'=>$user_data['id']))->save($login_data);
			$data = M('Member_data')
				  ->where(array('member_id'=>$user_data['id']))
				  ->field("member_id as id,nickname,headimg,qq,zuoji,email,sex")
				  ->find();
		    $data['username']    = $user_data['username'];
		    $data['is_identity'] = $user_data['is_identity'];
		    $data['is_security'] = $user_data['is_security'];
			$result['user_data'] = $data;
			//登录送积分
	   		$r = D('MemberScore')->score($user_data['id'] , 'LOGIN');
	   		//处理过期积分
	   		D('MemberScore')->scoreOver($user_data['id']);
	   		$result['score'] = $r;
		}else{
			$result = array('status'=>0,'msg'=>'密码不匹配,请重新输入');
		}
		return $result;
   }
      
   /**
    * 注册
    * @param array register_data 注册数据
    * @return array 返回注册结果
    */
   public function register($register_data){
   	    $result = array('status'=>1,'msg'=>'注册成功');
	   	/*验证数据*/
        $member = D("member");
        $rules  = array(
            array('username','require','必须输入手机号码'),
            array('username','','手机号码已存在！',0,'unique',1),
            array('username','/^1[34578]\d{9}$/i','手机号码格式不正确'),
            array('password','require','必须输入密码'),
            array('password','/^[_0-9a-z]{6,16}$/i','密码只能为英文、数字、下划线8-16位字符'),
            array('repeat_password','require','必须输入重复密码'),          
            array('repeat_password','password','确认密码不正确。',0,'confirm')        
        );
        if($member->validate($rules)->create($register_data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $member->getError()
            );
            return $result;
        } 

	   	//获取辅助加密字段随机值
	   	unset($register_data['repeat_password']);
	   	$register_data['salt']     = setnum(10);//密码加密辅助字段
	   	$register_data['password'] = md5(md5($register_data['password'] . $register_data['salt']));
	   	$id = M("Member")->add($register_data);
	   	if($id !== false){
	   		$data = array(
	   			'member_id'     => $id,
                'register_ip'   => get_client_ip(1),
                'register_time' => date('Y-m-d H:i:s'),
                'telnum'        => $register_data['username']
	   		);
	   		$result['member_id'] = $id;
	   		if(M('Member_data')->add($data) === false){
	   			$result = array('status'=>0,'msg'=>'注册失败');
	   		}else{
	   			//注册送积分
	   			$r = D('MemberScore')->score($id , 'REGISTER');
	   		}
	   	}else{
	   		$result = array('status'=>0,'msg'=>'注册失败');
	   	}
	   	return $result;
   }

   /**
    * 修改密码
    * @param array data 修改密码提交的资料
    * @return array 返回结果
    */
   public function passwordUpdate($data){
       $result = array('status'=>1,'msg'=>'修改成功');
       /*获取旧密码*/
       $old_data = M("Member")->where(array('id'=>$data['id']))->field('password,salt')->find();
       $old_pwd  = md5(md5($data['old_password'] . $old_data['salt']));
       if($old_pwd != $old_data['password']){
           return array(
               'status' => 0,
               'msg'    => '旧密码错误',
               'code'   => 1
           );
       }
       /*验证数据*/
       $member = D("member");
       $rules  = array(           
           array('password','require','必须输入新密码',self::EXISTS_VALIDATE),
           array('password','/^[_0-9a-z]{6,16}$/i','密码只能为英文、数字、下划线8-16位字符'),
           array('repeat_password','require','必须输入重复密码',self::EXISTS_VALIDATE),
           array('repeat_password','password','确认密码不正确。',0,'confirm')
       );
       if($member->validate($rules)->create($data) === false){
           $result = array(
               'status' => 0,
               'msg'    => $member->getError()
           );
           return $result;
       }       
       //获取辅助加密字段随机值
       unset($data['repeat_password']);unset($data['old_password']);
       $data['salt']     = setnum(10);//密码加密辅助字段
       $data['password'] = md5(md5($data['password'] . $data['salt']));
       $r = M("Member")->save($data);
       if($r === false){
           $result = array('status'=>0,'msg'=>'修改失败');
       }
       return $result;
   }

   /**
    * 重置密码
    * @param array data 重置密码提交的资料
    * @return array 返回结果
    */
   public function replacementPassword($data){
       $result = array('status'=>1,'msg'=>'修改成功');
       $data['password'] = $data['password']?$data['password']:'';
       $data['repeat_password'] = $data['repeat_password']?$data['repeat_password']:'';
       /*验证数据*/
       $member = D("member");
       $rules  = array(
           array('password','require','必须输入新密码',self::EXISTS_VALIDATE),
           array('password','/^[_0-9a-z]{6,16}$/i','密码只能为英文、数字、下划线8-16位字符'),
           array('repeat_password','require','必须输入重复密码',self::EXISTS_VALIDATE),
           array('repeat_password','password','确认密码不正确。',0,'confirm')
       );
       if($member->validate($rules)->create($data) === false){
           $result = array(
               'status' => 0,
               'msg'    => $member->getError()
           );
           return $result;
       }      
       //获取辅助加密字段随机值
       unset($data['repeat_password']);
       $data['salt']     = setnum(10);//密码加密辅助字段       
       $data['password'] = md5(md5($data['password'] . $data['salt']));
       $r = M("Member")->save($data);
       if($r === false){
           $result = array('status'=>0,'msg'=>'修改失败');
       }
       return $result;
   }
   
   /**
    * 根据id判是否企业认证
    * @param  int   $member_id 商家id    
    * @return array 返回结果
    */
    public function isRenzhengById($member_id){
        $member_id = intval($member_id);
        $r = M('Member')->where(array('id'=>$member_id))->getField('is_renzheng');
        return $r ? true : false ;
    } 
}