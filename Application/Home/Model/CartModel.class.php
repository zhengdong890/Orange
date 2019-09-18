<?php
namespace Home\Model;
use Think\Model;
/**
 * 共享商品购物车模块业务逻辑
 * @author 幸福无期
 */
class CartModel extends Model{   
   /**
    * 商品加入购物车
    * @param array cart_data 购物车数据 
    * @return array 返回操作结果
    */
   public function cartAdd($cart_data = array()){
       $result = array(
           'status' => 1,
           'msg'    => '商品已经成功加入购物车'
       );
       $data = array(
           'goods_id'    => intval($cart_data['goods_id']),
           'rent_time'   => $cart_data['rent_time'],
           'member_id'   => intval($cart_data['member_id']),
           'time'        => date("Y-m-d H:i:s"),
           'rent_number' => intval($cart_data['rent_number'])
       );
       /*验证数据*/
       $cart  = D("Cart");
       /*获取商品数据*/
       if($data['goods_id']){
           $goods = M("Goods")
                    ->where(array('id'=>$data['goods_id']))
                    ->field("min_rent,max_rent")
                    ->find(); 
           if(!$goods){
               return array('status' => 0,'msg' => '商品不存在');
           }      
       }
       $rules = array(
            array('member_id',array(0),'必须输入会员id！',2,'notin'),
            array('rent_number','require','必须选择商品数量'),
            array('rent_time',"{$goods['min_rent']},{$goods['max_rent']}",'租期范围不正确',0,'between')
       );
       if($cart->validate($rules)->create($data) === false){
           return array('status' => 0,'msg' => $cart->getError());
       } 

       $id = M("Cart")->add($data);
       if($id === false){
           $result = array(
               'status' => 0,
               'msg'    => '数据添加失败'
           );
       }
       return $result;
   }

   /**
    * 批量删除购物车
    * @param  int    $member_id 会员id
    * @param  array  $cart_ids_ 删除的购物车数据
    * @return array  返回操作结果
    */
   public function cartAllDelete($member_id , $cart_ids_ = array()){
       $member_id = intval($member_id);
       if(!$member_id){
           return array(
               'status' => 0,
               'msg'    => '请传入会员id'
           );
       }
       if(count($cart_ids_) < 0){
           return array(
               'status' => 0,
               'msg'    => '请传入需要删除的购物车数据'
           );
       }
       foreach($cart_ids_ as $k => $v){
           $cart_ids[] = intval($v);
       }
       $cart_ids = implode(',' , $cart_ids);       
       $r = M("Cart")->where(array('id'=>array('in' , $cart_ids),'member_id'=>$member_id))->delete();
       if($r === false){
           $result = array(
               'status' => 0,
               'msg'    => 'error'
           );
       }
       return array(
           'status' => 1,
           'msg'    => 'ok'
       );
   }
   
   /**
    * 获取购物车的商品
    * @param  int       $member_id 会员id 
    * @param  array|int $goods_ids 商品id 
    * @return array     返回操作结果
    */
   public function getCart($member_id , $goods_ids){
       if(is_array($goods_ids)){
       	   $goods_ids = implode(',' , $goods_ids);
       	   $carts = M('Cart')
                  ->field("id,goods_id,rent_time,rent_number")
                  ->where(array('member_id'=>$member_id,'goods_id'=>array('in',$goods_ids)))
                  ->select();
       }else{
           $carts = M('Cart')
                  ->field("id,goods_id,rent_time,rent_number")
                  ->where(array('member_id'=>$member_id,'goods_id'=>$goods_ids))
                  ->find();
       }
       if(count($carts) <= 0){
          return false;
       }
       return $carts;
   }
   
   /**
    * 商品数量修改 
    * @param  int    $cart_id   购物车id 
    * @param  int    $number    修改数量
    * @return array  $result    返回操作结果
    */   
   public function cartChangeNumber($cart_id , $number){
       $cart_id  = intval($cart_id);
       if(!$cart_id){
       	   return array(
               'status' => 0,
               'msg'    => '请输入购物车id'
       	   );
       }
       $number  = intval($number);
       if(!$number){
           return array(
               'status' => 0,
               'msg'    => '请输入正确的数量'
           );
       }
       $goods_id     = M('cart')->where(array('id'=>$cart_id))->getField('goods_id');
       $goods_number = M('Goods')
	                 ->where(array('id'=>$goods_id))
	                 ->getField('goods_number');
       if($number > $goods_number){
           return array(
               'status' => 0,
               'msg'    => '库存不足'
       	   );
       }
       $save_data['number'] = $number;
	   $r = M('Cart')->where(array('id'=>$cart_id))->save($save_data);
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
    * 商品数量修改
    * @param  int    $cart_id   购物车id
    * @param  int    $number    修改数量
    * @return array  $result    返回操作结果
    */
   public function cartChangeTime($cart_id , $rent_time){
       $cart_id  = intval($cart_id );
       if(!$cart_id){
           return array(
               'status' => 0,
               'msg'    => '请输入购物车id'
           );
       }
       $rent_time = intval($rent_time);
       if(!$rent_time){
           return array(
               'status' => 0,
               'msg'    => '请输正确的租期'
           );
       }
       $goods_id = M('cart')->where(array('id'=>$cart_id))->getField('goods_id');
       $goods    = M('Goods')
                 ->where(array('id'=>$goods_id))
                 ->field('min_rent,max_rent')
                 ->find();
       if($rent_time > $goods['max_rent'] ||$rent_time < $goods['min_rent']){
           return array(
               'status' => 0,
               'msg'    => '请选择正确的租期'
           );
       }
       $save_data['rent_time'] = $rent_time;
       $r = M('Cart')->where(array('id'=>$cart_id))->save($save_data);
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
}