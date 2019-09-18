<?php
/*
 * 权限规则 模块
 * */
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html; charset=utf-8");
class AuthController extends CommonController{  
	/*规则列表*/
	public function rule(){
		$list = M('Auth_rule')->select();
		$list = get_child($list);
		$this->assign('list' , $list);
		$this->display();
	}
	
	public function ruleList(){
/*
	    for($i= 1031;$i<=1100;$i++){
	        $register_data = array(
	            'username'        => '1847674'.$i, //账号
	            'password'        => 'y123456789', //密码
	            'repeat_password' => 'y123456789' //确认密码
	        );
	        $result = D('Home/Member')->register($register_data);//注册
	    }
*/
	    
	    $this->display();
	}
	
	public function getRules(){
	    $list = M('Auth_rule')->select();
	    $list = get_child($list);
	    $this->ajaxReturn($list);
	}
	
	/*添加规则*/ 
	public function ruleAdd(){
		if(IS_POST){
			$data = I();
			$result = D('Auth')->ruleAdd($data);
		    $this->ajaxReturn($result);
		}
	}
	
	/*编辑规则*/
	public function ruleUpdate(){
		if(IS_POST){			
			$data=I();
			$r = M('Auth_rule')->where(array('id'=>$data['id']))->save($data);
			if($r !== false){
				$this->ajaxReturn(array('status'=>'1','msg'=>'ok'));
			}else{
				$this->ajaxReturn(array('status'=>'0','msg'=>'error'));
			}
		}
	}
	
	/*管理员列表*/
	public function admin(){
		$where="a.id=b.uid and b.group_id=c.id";
		$field="a.id,a.username,a.login_time,a.login_ip,a.lock,c.title";
	    $list=M('Admin')->table("tp_admin a,tp_auth_group_access b,tp_auth_group c")->where($where)->Field($field)->select();//获取所有管理员		
		$a=new \Com\My_Page();
		$list=$a->pages($list,$pagesize=15,$url='');
		$this->list=$list['list'];
		$this->page=$list['page'];
		$this->display();
	}
	
	/*添加管理员账号*/
	public function adminAdd(){
		if(IS_POST){
			$data=I();
			/*验证数据*/
			$role = D("Admin");
			$rules= array(
					array('username','require','必须输入用户名'),
					array('username','/^[_0-9a-z]{6,16}$/i','账号只能为英文、数字、下划线6-16位字符'),
					array('username','','账号已存在！',0,'unique',1),
					array('password','require','必须输入密码'),
					array('password','require','必须输入重复密码'),
					array('password','/^[_0-9a-z]{6,16}$/i','密码只能为英文、数字、下划线6-16位字符'),
					array('repeatpassword','password','确认密码不正确。',0,'confirm'),
					array('groupid','/^[1-9]\d*$/','请选择管理员身份'),
			);
			if(!$role->validate($rules)->create($data)){
				$this->error($role->getError(),'admin_add');
				die;
			}
			/*管理员表需要的数据*/
			$data_admin=array('username'=>$data['username'],'password'=>md5($data['password']),'register_time'=>date('Y-m-d H:i:s'),'register_ip'=>get_client_ip(1),'remark'=>$data['remark'],'lock'=>$data['lock']);
			$id=M('Admin')->add($data_admin);//添加管理员
			if($id){
				/*插入角色-管理员表*/
				M('Auth_group_access')->add(array('uid'=>$id,'group_id'=>$data['groupid']));
				$data_admin_data=array('telnum'=>$data['telnum'],'name'=>$data['name'],'admin_id'=>$id);
				$id=M('Admin_data')->add($data_admin_data);//添加管理员附表信息
				if($id){
					$this->success("添加成功",'admin');
				}else{
					$this->error("管理员信息表添加失败",'admin');
				}
			}else{
				$this->error("添加失败",'admin_add');
			}
		}else{
			$this->groups=M("Auth_group")->select();//取出所有分组
			$this->display();
		}
	}
	
    /*ajax更改用户锁定状态*/
    public function lock_change(){
      if(IS_POST){
            $id=I('id');
            $lock=M('Admin')->where(array('id'=>$id))->getField('lock');
            $lock=$lock==1?'0':($lock==0?'1':0);
            $a=M('Admin')->where(array('id'=>$id))->save(array('lock'=>$lock));  
            if($a){
                echo '操作成功';
            }else{
            	echo '操作失败';
            }        
      }
    }

}

?>
