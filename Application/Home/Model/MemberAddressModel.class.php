<?php
namespace Home\Model;
use Think\Model;
/**
 * 商品模块业务逻辑
 * @author 幸福无期
 */
class MemberAddressModel extends Model{     
	protected $tableName = 'Member_address'; //关闭检测字段
    
   /**
    * 获取会员收货地址列表 不分页
    * @param  array $condition 获取条件    
    * @return array 返回操作结果
    */
    public function getAddressList($condition = array() , $order = 'id desc' , $limit = array()){
        $model = M('Member_address');
        !empty($condition) && ($model = $model->where($condition));        
        !empty($limit)     && ($model = $model->limit($limit[0] , $limit[1]));
        !empty($order)     && ($model = $model->order($order));
        $data = $model->select(); 
        return $data === false ? array() : $data;          
    }

   /**
    * 检测收货地址
    * @param  array $data  添加的运费模板数据 
    * @param  int   $type  1增加 2修改
    * @return array 返回操作结果和过滤后的数据
    */   
    public function checkAddress($data = array() , $type = 1){
   	    if(empty($data)){
            return array('status' => 0 , 'msg' => '数据不能为空');  
   	    }
        if($type  == 2){
        	if(intval($data['id']) == 0){
        		return array('status' => '0' , 'msg' => 'id错误');
        	}
        }
        /*验证数据*/
        $validate_model = $type == 1 ? self::MUST_VALIDATE : self::EXISTS_VALIDATE;
        $model = D("Member_address");
        $rules = array(
            array('member_id','require','member_id不能为空',$validate_model),
            array('name','require','请填写收货人姓名',$validate_model),
            array('telnum','/^1[123456789]\d{9}$/i','手机号码格式不正确',$validate_model),
            array('address','require','请填写收货地址',$validate_model),
            array('address_xx','require','请填写收货详细地址',$validate_model),           
            array('name','require','必须输入模板名称',$validate_model),
            array('province','/^[1-9]+[0-9]*$/i','必须选择省',$validate_model),
            array('city','/^[1-9]+[0-9]*$/i','必须选择市',$validate_model)     
        );
        if($model->validate($rules)->create($data) === false){
            return array('status' => 0,'msg' => $model->getError());
        }  
        return array('status' => 1);    
   }

   /**
    * 添加收货地址
    * @param  array  data   收货地址数据
    * @return array result 返回注册结果
    */
   public function addressAdd($data = array()){
   	    /*收货地址数据*/
        $data_ = array(
            'member_id'  => '',//买家id
            'name'       => '',//姓名
            'telnum'     => '',//电话	
            'address'    => '',//地址	
            'address_xx' => '',//地址详情
            'telnum_by'  => '',
            'province'   => '',
            'city'       => '',
            'area'       => '',
        );
        $data  = array_intersect_key($data , $data_); //获取键的交集
        $id    = M('Member_address')->add($data);
        if($id === false){
            return array('status'=>0,'msg'=>'添加失败');
        }
        return array('status'=>1,'msg'=>'添加成功');
   }

   /**
    * 修改收货地址
    * @param  array  data   收货地址数据
    * @return array result 返回注册结果
    */
    public function addressUpdate($data = array() , $member_id){
   	    /*收货地址数据*/
        $data_ = array(
            'name'       => '',//姓名
            'telnum'     => '',//电话	
            'address'    => '',//地址	
            'address_xx' => '',//地址详情
            'telnum_by'  => '',
            'province'   => '',
            'city'       => '',
            'area'       => '',
        );
        $id    = $data['id'];unset($data['id']);
        $data  = array_intersect_key($data , $data_); //获取键的交集
        $r     = M('Member_address')->where(array('id'=>$id,'member_id'=>$member_id))->save($data);
        if($r === 0){
            return array('status'=>0,'msg'=>'您暂无这个收货地址');
        }
        if($r === false){
            return array('status'=>0,'msg'=>'修改失败');
        }
        return array('status'=>1,'msg'=>'修改成功');
    }

   /**
    * 删除收货地址
    * @param  int   address_id   收货地址id
    * @return array result       返回删除结果
    */
   public function addressDelete($address_id){
      	$result = array('status'=>1,'msg'=>'删除成功');
      	if(!$address_id){
            return array('status'=>1,'msg'=>'请选择需要删除的id');
      	}
        $r = M('Member_address')->where(array('id'=>$address_id))->delete();
        if($r === false){
            $result = array('status'=>0,'msg'=>'删除失败');
        }
        return $result;
   }

   /**
    * 设置默认收货地址
    * @param  int   address_id   收货地址id
    * @param  int   member_id    会员id    
    * @return array result       返回结果
    */
   public function addressUse($address_id,$member_id){
      	$result = array('status'=>1,'msg'=>'操作成功');
      	if(!$address_id){
            return array('status'=>1,'msg'=>'请选择需要设置的id');
      	}
      	if(!$member_id){
            return array('status'=>1,'msg'=>'会员id不能为空');
      	}
      	M('Member_address')->where(array('is_use'=>1,'member_id'=>$member_id))->save(array('is_use'=>0));
        $r = M('Member_address')->where(array('id'=>$address_id))->save(array('is_use'=>1));
        if($r === false){
            $result = array('status'=>0,'msg'=>'操作失败');
        }
        return $result;
   } 
}