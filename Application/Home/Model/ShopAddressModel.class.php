<?php
/**
 *  商家地址库设置业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Home\Model;
use Think\Model;
class ShopAddressModel extends Model{
  protected $tableName='Shop_address';
  /**
   * 添加商家地址库
   * @access public
   * @param  array $data_ 地址数据
   * @return array $result 执行结果
   */
  public function shopAddressAdd($data_){
      $data = array(
          'seller_id'     => intval($data_['seller_id']),
          'contact'       => $data_['contact'],
          'area'          => $data_['area'],
          'address'       => $data_['address'],
          'zip_code'      => $data_['zip_code'],
          'phone'         => $data_['phone'],
          'tel_num'       => $data_['tel_num'],
          'company_name'  => $data_['company_name']
      );
      /*数据验证*/
      $model = D("Shop_address");
      $rules = array(
          array('seller_id','/^[1-9]\d*$/','卖家id不正确'),
          array('contact','require','必须输入联系人',self::EXISTS_VALIDATE),
          array('area','require','必须输入地区',self::EXISTS_VALIDATE),
          array('address','require','必须输入地址',self::EXISTS_VALIDATE),
          array('zip_code','require','必须输入邮政编码',self::EXISTS_VALIDATE),
          array('phone','require','必须输入电话号码',self::EXISTS_VALIDATE),
          array('tel_num','require','必须输入手机号码',self::EXISTS_VALIDATE)
      );
      if($model->validate($rules)->create($data) === false){
          $result = array(
              'status' => 0,
              'msg'    => $model->getError()
          );
          return $result;
      }
      $id = M('Shop_address')->add($data);   
      if($id === false){
          return array(
              'status' => 0,
              'msg'    => '操作失败'
          );
      }
      return array(
          'status' => 1,
          'msg'    => '操作成功'
      );
  } 
  
  /**
   * 修改商家地址库
   * @access public
   * @param  int   $seller_id 卖家id
   * @param  array $data_ 地址数据
   * @return array $result 执行结果
   */
  public function shopAddressUpdate($seller_id , $data_){
      $data = array(
          'id'            => intval($data_['id']),
          'contact'       => $data_['contact'],
          'area'          => $data_['area'],
          'address'       => $data_['address'],
          'zip_code'      => $data_['zip_code'],
          'phone'         => $data_['phone'],
          'tel_num'       => $data_['tel_num'],
          'company_name'  => $data_['company_name']
      );
      /*数据验证*/
      $model = D("Shop_address");
      $rules = array(
          array('id','/^[1-9]\d*$/','id不正确'),
          array('contact','require','必须输入联系人',self::EXISTS_VALIDATE),
          array('area','require','必须输入地区',self::EXISTS_VALIDATE),
          array('address','require','必须输入地址',self::EXISTS_VALIDATE),
          array('zip_code','require','必须输入邮政编码',self::EXISTS_VALIDATE),
          array('phone','require','必须输入电话号码',self::EXISTS_VALIDATE),
          array('tel_num','require','必须输入手机号码',self::EXISTS_VALIDATE)
      );
      if($model->validate($rules)->create($data) === false){
          $result = array(
              'status' => 0,
              'msg'    => $model->getError()
          );
          return $result;
      }
      unset($data['id']);
      $r = M('Shop_address')->where(array('id'=>intval($data_['id']),'seller_id'=>$seller_id))->save($data);
      if($r === false){
          return array(
              'status' => 0,
              'msg'    => '操作失败'
          );
      }
      return array(
          'status' => 1,
          'msg'    => '操作成功'
      );
  } 
  
  /**
   * 删除商家地址库
   * @access public
   * @param  int   $seller_id 卖家id
   * @param  int   $id 地址id
   * @return array 执行结果
   */
  public function shopAddressDelete($seller_id , $id){
      $seller_id = intval($seller_id);
      if(!$seller_id){
          return array('status'=>0,'卖家id不正确');   
      }
      $id = intval($id);
      if(!$id){
          return array('status'=>0,'地址id不正确');
      }
      $r = M('Shop_address')->where(array('id'=>$id,'seller_id'=>$seller_id))->delete();
      if($r === false){
          return array(
              'status' => 0,
              'msg'    => '操作失败'
          );
      }
      return array(
          'status' => 1,
          'msg'    => '操作成功'
      );
  } 
  
  /**
   * 修改地址是发货还是退货地址
   * @access public
   * @param  int   $seller_id 卖家id
   * @param  int   $id 地址id
   * @param  int   $type
   * @return array 执行结果
   */
  public function changeType($seller_id , $id , $type){
      $seller_id = intval($seller_id);
      if(!$seller_id){
          return array('status'=>0,'卖家id不正确');
      }
      $id = intval($id);
      if(!$id){
          return array('status'=>0,'地址id不正确');
      }
      $type = $type == 1 ? 1 : 2; 
      $r = M('Shop_address')->where(array('id'=>$id,'seller_id'=>$seller_id))->save(array('type'=>$type));
      if($r === false){
          return array(
              'status' => 0,
              'msg'    => '操作失败'
          );
      }
      return array(
          'status' => 1,
          'msg'    => '操作成功'
      );
  }
   public function address_del($address_id){
    $result = array('status'=>1,'msg'=>'删除成功');
    if(!$address_id){
      return array('status'=>1,'msg'=>'请选择需要删除的id');

    }
    $r = M('Shipping_address')->where(array('id'=>$address_id))->delete();
    if($r===false){
      $result = array('status'=>0,'msg'=>'删除失败');
    }
    return $result;
   }  
}