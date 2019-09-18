<?php
/**
 * 后台管理原账号业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class AdminModel extends Model{
    /**
     * 添加管理员账号
     * @param  array data_需要添加的数据
     * @return array 返回验证结果
     */
    public function adminAdd($data_ = array()){
        $result = array('status'=>1,'msg'=>'添加成功');
        $data   = array(
            'username'       => $data_['username'] ? $data_['username'] : '',
            'password'       => $data_['password'] ? $data_['password'] : '',
            'repeatpassword' => $data_['repeatpassword'] ? $data_['repeatpassword'] : '',
            'lock'           => intval($data_['lock']) == 1 ? 1 : 0,
            'group_id'        => intval($data_['group_id'])
        );
        /*数据验证*/
        $model  = D("Admin");
        $rules  = array(
                array('username','require','必须输入用户名'),
                array('username','','账号已存在！',0,'unique',1),
                array('password','require','必须输入密码'),
                array('repeatpassword','require','必须输入确认密码'),
                array('repeatpassword','password','确认密码不正确。',0,'confirm'),
                array('group_id','/^[1-9]\d*$/','请选择管理员身份')
        );
        if($model->validate($rules)->create($data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $model->getError()
            );
            return $result;
        }
        $group_id = $data['group_id'];unset($data['group_id']);unset($data['repeatpassword']);
        $data['password'] = md5($data['password']);
        //获取用户数据
        $id = M("Admin")->add($data);
        if($id === false){
            $result = array('status'=>0,'msg'=>'添加失败');
        }else{
            $result['id'] = $id;
            /*插入角色-管理员表*/
            M('Auth_group_access')->add(array('uid'=>$id,'group_id'=>$group_id));
            M('Admin_data')->add(array('admin_id'=>$id,'name'=>$data_['name']));
        }
        return $result;
    }    
    
    /**
     * 修改管理员账号
     * @param  array data_需要添加的数据
     * @return array 返回验证结果
     */
    public function adminUpdate($data_ = array()){
        $result = array('status'=>1,'msg'=>'修改成功');
        $data   = array(
            'id'             => intval($data_['id']),
            'username'       => $data_['username'] ? $data_['username'] : '',
            'password'       => $data_['password'] ? $data_['password'] : '',
            'repeatpassword' => $data_['repeatpassword'] ? $data_['repeatpassword'] : '',
            'group_id'       => intval($data_['group_id'])
        );
        $save_data = array(
            'lock'           => intval($data_['lock']) == 1 ? 1 : 0
        );
        /*数据验证*/
        $model  = D("Admin");
        $rules  = array(
            array('id','/^[1-9]\d*$/','请选择管理员id'),
            array('group_id','/^[1-9]\d*$/','请选择管理员身份')
        );
        if(intval($data_['is_user'])){//修改账号
            $r = M('Admin')->where(array('username'=>$data['username']))->getField('id');
            if($r && $r != $data['id']){
                return array(
                    'status' => 0,
                    'msg'    => '账号存在'
                );
            }
            $rules[] = array('username','require','必须输入用户名');
            $save_data['username'] = $data['username'];
        }
        if(intval($data_['is_pwd'])){//修改密码
            $rules = array_merge($rules , array(
                array('password','require','必须输入密码'),
                array('repeatpassword','require','必须输入确认密码'),
                array('repeatpassword','password','确认密码不正确。',0,'confirm')
            ));
            $save_data['password'] = md5($data_['password']);
        }
        if($model->validate($rules)->create($data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $model->getError()
            );
            return $result;
        }
        $group_id = $data['group_id'];
        //获取用户数据
        $r = M("Admin")->where(array('id'=>$data['id']))->save($save_data);
        if($r === false){
            $result = array('status'=>0,'msg'=>'修改失败');
        }else{
            /*插入角色-管理员表*/
            M('Auth_group_access')->where(array('uid'=>$data['id']))->save(array('group_id'=>$group_id));
            M('Admin_data')->where(array('admin_id'=>$data['id']))->save(array('name'=>$data_['name']));
        }
        return $result;
    }
    
   /**
    * 登录验证
    * @param array data需要验证的数据 
    * @return array 返回验证结果
    */
   public function loginValidate($login_data = array()){
     	$result = array('status'=>1,'msg'=>'登录成功');
   	    /*数据验证*/
   	    $model  = D("Admin");
        $rules  = array(
            array('username','require','必须输入账号'),
            array('password','require','必须输入密码')       
        );
		if($model->validate($rules)->create($login_data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $model->getError()
            );
            return $result;
        } 
		//获取用户数据
		$admin_data = M("Admin")->where(array('username'=>$login_data['username']))->find();
		//密码连接加密
		$password   = md5($login_data['password']);
		if($password == $admin_data['password']){
		    /*记录用户登录的数据*/
		    $login_data = array(		        
		        'login_time' => date('Y-m-d H:i:s'),
		        'login_ip'   => get_client_ip(1)
		    );
		    $r = M('Admin')->where(array('id'=>$admin_data['id']))->save($login_data);
		    unset($admin_data['password']);
			$result['admin_data'] = $admin_data;
		}else{
			$result = array('status'=>0,'msg'=>'密码不匹配,请重新输入');
		}
		return $result;
   }
}

?>