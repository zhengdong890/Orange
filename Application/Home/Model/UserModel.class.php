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
   public function login_validate($data){
     	$result['msg']='';
	   	$result['state']=true;
   	    /*数据验证*/
		$a=new libraries\Validate();
		$rules= array(
				array('require','username','必须输入账号'),
				array('require','password','必须输入密码'),
				array('no_existe','username','账号不存在','sql'=>array('table'=>$this->db->dbprefix('member'),'db'=>$this->db))
		);
		$a->autoValidation($data,$rules);//验证数据
		$error=$a->get_error();//获取错误
		if($error){//数据验证未通过
			$result['state']=false;
			$result['msg']=$error;
	        return $result;
		}
		//获取辅助加密字段
		$sql="select * from {$this->db->dbprefix('member')} where username='{$data['username']}' limit 0,1";
		$query=$this->db->query($sql);
		$user_data=$query->row_array();
		//密码连接加密
		$password=empty($user_data['salt'])?md5(md5($data['password'])):md5(md5($data['password'].$user_data['salt']));
		if($password==$user_data['password']){
			$result['msg']='登录成功';
			$result['user_data']=array('id'=>$user_data['id'],'nickname'=>$user_data['nickname'],'salt'=>$user_data['salt']);
		}else{
			$result['state']=0;
			$result['msg']='密码不匹配,请重新输入';
		}
		return $result;
   }
      
   /**
    * 注册
    * @param array register_data 注册数据
    * @return array 返回注册结果
    */
   public function register($register_data){
	   	$result['msg']='';
	   	$result['state']=1;
	   	/*数据验证*/
	   	        /*验证数据*/
        $member = D("member");
        echo 1;die;
        $rules = array(
            array('username','require','必须输入账号'),
            array('username','','账号已存在！',0,'unique',1),
            array('password','require','必须输入密码'),
            array('repeat_password','require','必须输入重复密码'),          
            array('repeat_password','password','确认密码不正确。',0,'confirm'),
            array('password','/^[_0-9a-z]{6,16}$/i','密码只能为英文、数字、下划线6-16位字符'),
        );
        if($member->validate($rules)->create($data_goods) === false){
           $result = array(
             'status' => 0,
             'msg'    => $goods->getError()
           );
           return $result;
        } 
        echo json_encode($result); die;
	   	//添加注册默认数据
	   	$register_data['modelid']=6;
	   	$register_data['status']=1;//转态
	   	$register_data['groupid']=1;//分组
	   	$register_data['regdate']=time();//注册时间   
	   	$this->load->helper('get_ip');
	   	$register_data['regip']=get_client_ip();//注册ip
	   	//获取辅助加密字段随机值
	   	$this->load->helper('get_number');
	   	$register_data['nickname']=setnum(6);
	   	unset($register_data['repeat_password']);
	   	$register_data['salt']=setnum(10);//密码加密辅助字段
	   	$register_data['stu_id']=date('Ymd').setnum(4,'0123456789');//学号
	   	$register_data['password']=md5(md5($register_data['password'].$register_data['salt']));
	   	$r=$this->db->insert('member',$register_data);//注册
	   	if($r!==false){
	   		$result['state']=true;
	   		$result['msg']='注册成功';
	   	}else{
	   		$result['state']=false;
	   		$result['msg']='注册失败';
	   	}
	   	return $result;
   }
}