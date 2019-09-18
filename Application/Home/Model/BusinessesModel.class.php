<?php
namespace Home\Model;
use Think\Model;
/**
 * 企业模块业务逻辑
 * @author 幸福无期
 */
class BusinessesModel extends Model{   
    protected $tableName='Businesses_application'; //关闭检测字段
   /**
    * 企业提交认证信息
    * @param array data需要验证的数据 
    * @return array 返回验证结果
    */
   public function addData($data_){
        $result = array(
            'status' => 1,
            'msg'    => '添加成功'
        );  
        $data = array(
        	'member_id'     => intval($data_['member_id']),
        	'name'          => $data_['name'],
        	'address'       => $data_['address'],
        	'contact'       => $data_['contact'],
        	'place_contact' => $data_['place_contact'],
        	'contact_phone' => $data_['contact_phone'],
        	'bus_lice_type' => intval($data_['bus_lice_type']),
        	'bus_lice'      => $data_['bus_lice'] ? $data_['bus_lice'] : '',
        	'permit'        => $data_['permit'] ? $data_['permit'] : '',
            'time'          => time()
        ); 
        if($data['bus_lice_type'] == 2){
            $data = array_merge($data , array(
               'code'          => $data_['code']? $data_['code'] : '',
        	   'registration'  => $data_['registration']? $data_['registration'] : '' 
            ));
        }
        /*验证数据*/
        $model = D('Businesses_application');
        $rules = array(
        	array('member_id','/^[1-9]\d*$/','member_id错误',self::EXISTS_VALIDATE),
            array('name','require','公司名称不能为空',self::EXISTS_VALIDATE),
            array('address','require','公司地址不能为空',self::EXISTS_VALIDATE),
            array('contact','require','联系方式不能为空',self::EXISTS_VALIDATE),
            array('place_contact','require','指定联系人不能为空',self::EXISTS_VALIDATE),
            array('contact_phone','require','指定联系人电话不能为空',self::EXISTS_VALIDATE),
            array('bus_lice_type','/^1|2$/','请选择正确的认证类型',self::EXISTS_VALIDATE),
            array('bus_lice','require','请上传营业执照',self::EXISTS_VALIDATE),
            array('permit','require','请上传开户许可证',self::EXISTS_VALIDATE)
        );
        if($data['bus_lice_type'] == 2){
            $rules = array_merge($rules , array(
                array('code','require','请上传组织机构代码证'),
                array('registration','require','请上传税务登记证')
            ));
        }
        if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
        }
        $r  = M('Businesses_application')->add($data);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }
        return $result;
   }
   
   /**
    * 企业重新提交认证信息
    * @param array data需要验证的数据
    * @return array 返回验证结果
    */
   public function Update($data_){
       $result = array(
           'status' => 0,
           'msg'    => '提交成功'
       );
       $data = array(
           'member_id'     => intval($data_['member_id']),
           'name'          => $data_['name'],
           'address'       => $data_['address'],
           'contact'       => $data_['contact'],
           'place_contact' => $data_['place_contact'],
           'contact_phone' => $data_['contact_phone'],
           'bus_lice_type' => intval($data_['bus_lice_type']),
           'bus_lice'      => $data_['bus_lice'] ? $data_['bus_lice'] : '',
           'permit'        => $data_['permit'] ? $data_['permit'] : '',
           'time'          => time(),
           'check_status'  => 0
       );
       if($data['bus_lice_type'] == 2){
            $data = array_merge($data , array(
               'code'          => $data_['code']? $data_['code'] : '',
        	   'registration'  => $data_['registration']? $data_['registration'] : '' 
            ));
       }
       /*验证数据*/
       $model = D('Businesses_application');
       $rules = array(
           array('member_id','/^[1-9]\d*$/','member_id错误',self::EXISTS_VALIDATE),
           array('name','require','公司名称不能为空',self::EXISTS_VALIDATE),
           array('address','require','公司地址不能为空',self::EXISTS_VALIDATE),
           array('contact','require','联系方式不能为空',self::EXISTS_VALIDATE),
           array('place_contact','require','指定联系人不能为空',self::EXISTS_VALIDATE),
           array('contact_phone','require','指定联系人电话不能为空',self::EXISTS_VALIDATE),
           array('bus_lice_type','/^1|2$/','请选择正确的认证类型',self::EXISTS_VALIDATE),
           array('bus_lice','require','请上传营业执照',self::EXISTS_VALIDATE),
           array('permit','require','请上传开户许可证',self::EXISTS_VALIDATE)
       );
       if($data['bus_lice_type'] == 2){
           $rules = array_merge($rules , array(
               array('code','require','请上传组织机构代码证',self::EXISTS_VALIDATE),
               array('registration','require','请上传税务登记证',self::EXISTS_VALIDATE)
           ));
       }
       if($model->validate($rules)->create($data) === false){
           $result = array(
               'status' => 0,
               'msg'    => $model->getError()
           );
           return $result;
       }
       $r  = M('Businesses_application')->where(array('member_id'=>$data['member_id']))->save($data);
       if($r === false){
           $result = array(
               'status' => 0,
               'msg'    => '数据添加失败'
           );
       }
       return $result;
   }   
}