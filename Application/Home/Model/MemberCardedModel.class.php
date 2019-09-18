<?php
namespace Home\Model;
use Think\Model;
/**
 * 会员身份认证信息
 * @author 幸福无期
 */
class MemberCardedModel extends Model{
    protected $tableName='member_carded'; //切换检测表
    /**
     * 身份认证信息上传
     * @access public
     * @param  array $data_      身份信息
     * @param  int   $member_id  会员id
     * @return array $result     执行结果
     */
    public function qualification($data_ , $member_id){
        $member_id = intval($member_id);
        if(!$member_id){
            return array(
                'status' => 0,
                'msg'    => '请输入会员id'
            );
        }
        $id = M('Member_carded')->where(array('member_id'=>$member_id))->getField('id');
        if(!$id){
            $result = $this->qualificationAdd($data_ , $member_id);    
        }else{
            $result = $this->qualificationUpdate($id , $data_ , $member_id);
        }  
        return $result;
    }
    
    protected function qualificationAdd($data_ , $member_id){
        $data = array(
            'name'          => $data_['name'],
            'carded_code'   => $data_['carded_code'],
            'carded_thumb1' => $data_['carded_thumb1']?$data_['carded_thumb1']:'',
            'carded_thumb2' => $data_['carded_thumb2']?$data_['carded_thumb2']:'',
            'carded_thumb3' => $data_['carded_thumb3']?$data_['carded_thumb3']:'',
            'member_id'     => intval($member_id)
        );
        /*验证数据*/
        $moedel = D("Member_carded");
        $rules  = array(
            array('name','require','必须输入真实姓名'),
            array('carded_code','require','必须输入身份证号码'),
            array('member_id','/^[1-9]\d*$/','请输入会员id'),
            array('carded_thumb1','require','必须输入身份证反面',self::EXISTS_VALIDATE),
            array('carded_thumb2','require','必须输入身份证正面',self::EXISTS_VALIDATE),
            array('carded_thumb3','require','必须输入持身份证正面头部照',self::EXISTS_VALIDATE)
        );
        if($moedel->validate($rules)->create($data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $moedel->getError()
            );
            return $result;
        }
        $r = M('Member_carded')->add($data);
        if($r === false){
            return array(
                'status' => 0,
                'msg'    => '失败'
            );
        }else{
            return array(
                'status' => 1,
                'msg'    => 'ok'
            );
        }
    }
    
    protected function qualificationUpdate($id , $data_ , $member_id){
        $data = array(
            'name'          => $data_['name'],
            'carded_code'   => $data_['carded_code'],
            'is_check'      => 0
        );
        /*验证数据*/
        $moedel = D("Member_carded");
        $rules  = array(
            array('name','require','必须输入真实姓名'),
            array('carded_code','require','必须输入身份证号码'),
            array('carded_code','/^[1-9]\d*$/','身份证号码格式不正确')
        );
        if($data_['carded_thumb1']){
            $data['carded_thumb1'] = $data_['carded_thumb1']; 
        }
        if($data_['carded_thumb2']){
            $data['carded_thumb2'] = $data_['carded_thumb2'];
        }
        if($data_['carded_thumb3']){
            $data['carded_thumb3'] = $data_['carded_thumb3'];
        }
        if($moedel->validate($rules)->create($data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $moedel->getError()
            );
            return $result;
        }
        $r = M('Member_carded')->where(array('id'=>$id))->save($data);
        if($r === false){
            return array(
                'status' => 0,
                'msg'    => '失败'
            );
        }else{
            return array(
                'status' => 1,
                'msg'    => 'ok'
            );
        }
    }    
}