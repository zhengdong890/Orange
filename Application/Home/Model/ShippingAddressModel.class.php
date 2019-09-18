<?php
namespace Home\Model;
use Think\Model;
/**
 * 商家地址库模块业务逻辑
 * @author 幸福无期
 */
class ShippingAddressModel extends Model{ 
   protected $tableName = 'Shipping_address'; //切换检测表
   /**
    * 获取地址库
    * @param  int   $seller_id 卖家id
    * @param  array $condition 获取条件    
    * @return array 返回操作结果
    */
    public function getAddressList($seller_id = '' , $condition = array() , $limit = array() , $order = 'id desc'){
        $model = M('Shipping_address');
        !empty($condition) && ($model = $model->where($condition));        
        !empty($limit)     && ($model = $model->limit($limit[0] , $limit[1]));
        !empty($order)     && ($model = $model->order($order));
        $data   = $model->where(array('seller_id'=>$_SESSION['member_data']['id']))->select(); 
        $count  = $model->where($condition)->count();   
        return array(
            'data'      => $data,
            'totalRows' => $count
        );  
    }

   /**
    * 添加地址库
    * @param  array $data_ 添加的地址库数据 
    * @param  int   $seller_id 卖家id
    * @return array 返回操作结果
    */
    public function addressAdd($data_ = array() , $seller_id = ''){
        /*地址库数据*/
        $data = array(
            'seller_id'    => intval($seller_id),
            'name'         => $data_['name'],//联系人
            'province'     => $data_['province'],//省
            'city'         => $data_['city'],//市
            'address'      => $data_['address'],//详细地址
            'postcode'     => $data_['postcode'],//邮政编码
            'phone'        => $data_['phone'],//电话
            'mobile_phone' => $data_['mobile_phone'],//手机
            'company'      => $data_['company']//公司名称
        );
        /*验证数据*/
        $model = D("Shipping_address");
        $rules = array(
            array('seller_id','/^[1-9]\d*$/','商家id错误',self::MUST_VALIDATE),
            array('name','require','必须输入联系人',self::MUST_VALIDATE),
            array('province','require','必须选择省',self::MUST_VALIDATE),
            array('city','require','必须选择市',self::MUST_VALIDATE),
            array('address','require','必须输入详细地址',self::MUST_VALIDATE),
            array('postcode','require','必须输入邮政编码',self::MUST_VALIDATE),
            array('phone','require','必须输入电话',self::MUST_VALIDATE),
            array('mobile_phone','require','必须输入手机',self::MUST_VALIDATE),
            array('mobile_phone','/^1[34578]\d{9}$/i','手机号码格式不正确'),
            array('company','require','必须输入公司地址',self::MUST_VALIDATE)
        );
        if($model->validate($rules)->create($data) === false){
            return array('status' => 0,'msg' => $model->getError());
        } 
        $id = M("Shipping_address")->add($data);
        if($id === false){
            $result = array(
               'status' => 0,
               'msg'    => '添加失败'
            );
        }
        return array(
           'status' => 1,
           'msg'    => '添加成功'
        );
    }


   /**
    * 修改地址库
    * @param  array $data_     修改的地址库数据 
    * @param  int   $seller_id 卖家id
    * @return array 返回操作结果
    */
    public function addressUpdate($data_ = array() , $seller_id = ''){
        $seller_id = intval($seller_id);
        $id        = intval($data_['id']);
        //验证id
        if(!$seller_id){
          return array('status'=>0,'商家id错误');   
        }
        $id = intval($id);
        if(!$id){
          return array('status'=>0,'地址id错误');
        }
        /*地址库数据*/
        isset($data_['name'])         && ($data['name']         = $data_['name']);
        isset($data_['province'])     && ($data['province']     = $data_['province']);
        isset($data_['city'])         && ($data['city']         = $data_['city']);
        isset($data_['address'])      && ($data['address']      = $data_['address']);
        isset($data_['postcode'])     && ($data['postcode']     = $data_['postcode']);
        isset($data_['phone'])        && ($data['phone']        = $data_['phone']);
        isset($data_['mobile_phone']) && ($data['mobile_phone'] = $data_['mobile_phone']);
        isset($data_['company'])      && ($data['company']      = $data_['company']);
        /*验证数据*/
        $model = D("Shipping_address");
        $rules = array(
        	array('name','require','必须输入联系人'),
            array('province','require','必须选择省'),
            array('city','require','必须选择市'),
            array('address','require','必须输入详细地址'),
            array('postcode','require','必须输入邮政编码'),
            array('phone','require','必须输入电话'),
            array('mobile_phone','require','必须输入手机'),
            array('company','require','必须输入公司地址')
        );
        if($model->validate($rules)->create($data) === false){
            return array('status' => 0,'msg' => $model->getError());
        } 
        $r = M("Shipping_address")->where(array('id'=>$id , 'seller_id'=>$seller_id))->save($data);
        if($r === false){
            $result = array(
               'status' => 0,
               'msg'    => '保存失败'
            );
        }
        return array(
           'status' => 1,
           'msg'    => '保存成功'
        );
   }

   public function address_del($address_id){
        $result = array('status'=>1,'msg'=>'删除成功');
        if(!$address_id){
            return array('status'=>0,'msg'=>'请选择需要删除的id');

        }
        $r = M('Shipping_address')->where(array('id'=>$address_id))->delete();
        if($r===false){
            $result = array('status'=>0,'msg'=>'删除失败');
        }
        return $result;
     }
}