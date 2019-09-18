<?php
/**
 * 权限管理逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class AuthModel extends Model{
    protected $tableName='Auth_rule';
    public $rules = array();
    //默认配置
    protected $_config = array(
        'AUTH_ON' => true, //认证开关
        'AUTH_TYPE' => 1, // 认证方式，1为时时认证；2为登录认证。
        'AUTH_GROUP' => 'tp_auth_group', //用户组数据表名
        'AUTH_GROUP_ACCESS' => 'tp_auth_group_access', //用户组明细表
        'AUTH_RULE' => 'tp_auth_rule', //权限规则表
        'AUTH_USER' => 'tp_admin'//用户信息表
    );
    
    /**
     * 添加规则
     * @access public
     * @param  array $data_  规则数据
     * @return array $result 执行结果
     */    
    public function ruleAdd($data_){
        $data = array(
            'pid'       => intval($data_['pid']) ? intval($data_['pid']) : '0',
            'name'      => $data_['name'] ? $data_['name'] : '',
            'title'     => $data_['title'] ? $data_['title'] : '',
            'condition' => $data_['condition'] ? $data_['condition'] : '',
            'level'     => intval($data_['pid']) == 0 ? 1 : 2
        );    
        /*验证数据*/
        $model = D("Auth_rule");
        $rules = array(
            array('name','require','必须输入规则名称',self::EXISTS_VALIDATE)
        );
        if($model->validate($rules)->create($data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $model->getError()
            );
            return $result;
        }
		$r = M('Auth_rule')->add($data);
		if($r !== false){
			return(array('status'=>'1','msg'=>'ok','id'=>$r));
		}else{
			return(array('status'=>'0','msg'=>'操作失败'));
		}
    }
    
    //获得用户组，外部也可以调用
    public function getGroups($uid) {
        static $groups = array();
        if (isset($groups[$uid]))
            return $groups[$uid];
        $user_groups = M()->table($this->_config['AUTH_GROUP_ACCESS'] . ' a')->where("a.uid='$uid' and g.status='1'")->join($this->_config['AUTH_GROUP']." g on a.group_id=g.id")->select();
        $groups[$uid]=$user_groups?$user_groups:array();
        return $groups[$uid];
    }
    
    //获得权限$name 可以是字符串或数组或逗号分割， uid为 认证的用户id， $or 是否为or关系，为true是， name为数组，只要数组中有一个条件通过则通过，如果为false需要全部条件通过。
    public function check($name, $uid, $relation='or') {
        if (!$this->_config['AUTH_ON'])
            return true;
        $authList = $this->getAuthList($uid);
        if (is_string($name)) {
            if (strpos($name, ',') !== false) {
                $name = explode(',', $name);
            } else {
                $name = array($name);
            }
        }
        $list = array(); //有权限的name
        foreach ($authList as $val) {
            if (in_array($val, $name))
                $list[] = $val;
        }
        if ($relation=='or' and !empty($list)) {
            return true;
        }
        $diff = array_diff($name, $list);
        if ($relation=='and' and empty($diff)) {
            return true;
        }
        return false;
    }
    
    public function getAuth($uid){
        //读取用户所属用户组
        $groups = $this->getGroups($uid);
        $ids = array();
        foreach ($groups as $g) {
            $ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
        }
        $ids = array_unique($ids);
        if (empty($ids)) {
            $_authList[$uid] = array();
            return array();
        }        
        //读取用户组所有权限规则
        $map=array(
            'id'=>array('in',$ids),
            'status'=>1
        );
        $rules = M()->table($this->_config['AUTH_RULE'])->where($map)->select();
        return $rules;
    }
    
    //获得权限列表
    protected function getAuthList($uid) {
        static $_authList = array();
        if (isset($_authList[$uid])) {
            return $_authList[$uid];
        }
        if(isset($_SESSION['_AUTH_LIST_'.$uid])){
            return $_SESSION['_AUTH_LIST_'.$uid];
        }
        //读取用户所属用户组
        $groups = $this->getGroups($uid);
        $ids = array();
        foreach ($groups as $g) {
            $ids = array_merge($ids, explode(',', trim($g['rules'], ',')));
        }
        $ids = array_unique($ids);
        if (empty($ids)) {
            $_authList[$uid] = array();
            return array();
        }

        //读取用户组所有权限规则
        $map=array(
            'id'=>array('in',$ids),
            'status'=>1
        );       
        $rules = M()->table($this->_config['AUTH_RULE'])->where($map)->select();
        //循环规则，判断结果。
        $authList = array();
        foreach ($rules as $r) {
            if (!empty($r['condition'])) {
                //条件验证
                $user = $this->getUserInfo($uid);
                $command = preg_replace('/\{(\w*?)\}/', '$user[\'\\1\']', $r['condition']);
                //dump($command);//debug
                @(eval('$condition=(' . $command . ');'));
                if ($condition) {
                    $authList[] = $r['name'];
                }
            } else {
                //存在就通过
                $authList[] = $r['name'];
            }
        }
        $_authList[$uid] = $authList;
        if($this->_config['AUTH_TYPE']==2){
            //session结果
            $_SESSION['_AUTH_LIST_'.$uid]=$authList;
        }
        return $authList;
    }
    
    //获得用户资料,根据自己的情况读取数据库
    protected function getUserInfo($uid) {
        static $userinfo=array();
        if(!isset($userinfo[$uid])){
            $userinfo[$uid]=M()->table($this->_config['AUTH_USER'])->find($uid);
        }
        return $userinfo[$uid];
    }
}

?>