<?php
namespace Home\Model;
use Think\Model;
/**
 * 卖家QQ客服模块业务逻辑
 * @author 幸福无期
 */
class SellerQQModel extends Model{   
    protected $tableName='Seller_qq'; //切换检测字段
   /**
    * qq编辑
    * @param  array data需要编辑的数据 
    * @return array 返回验证结果
    */
   public function qqEdit($data){
       $data['id'] = intval($data['id']);
       if(!$data['id']){
           return array('status'=>0,'msg'=>'id错误');
       }
       $data['seller_id'] = intval($data['seller_id']);
       if(!$data['seller_id']){
           return array('status'=>0,'msg'=>'卖家id错误');    
       }
       $data['qq_number'] = intval($data['qq_number']);
       if(!$data['qq_number']){
           return array('status'=>0,'msg'=>'qq错误');
       }
       $update_data = array(
           'qq_number' => $data['qq_number']   
       );
       $r = M('Seller_qq')->where(array('id'=>$data['id'],'seller_id'=>$data['seller_id']))->save($update_data);
       if($r === false){
           return array('status'=>0,'msg'=>'操作失败');
       }
       return array('status'=>0,'msg'=>'操作成功');
   }
   
   /**
    * qq添加
    * @param  array data需要添加的数据
    * @return array 返回验证结果
    */
   public function qqAdd($data){
       $data['seller_id'] = intval($data['seller_id']);
       if(!$data['seller_id']){
           return array('status'=>0,'msg'=>'卖家id错误');
       }
       $data['qq_number'] = intval($data['qq_number']);
       if(!$data['qq_number']){
           return array('status'=>0,'msg'=>'qq错误');
       }
       $add_data = array(
           'seller_id' => $data['seller_id'],
           'qq_number' => $data['qq_number']
       );
       $r = M('Seller_qq')->save($add_data);
       if($r === false){
           return array('status'=>0,'msg'=>'操作失败');
       }
       return array('status'=>0,'msg'=>'操作成功');
   }
}