<?php
namespace Shop\Model;
use Think\Model;
/**
 * 商城商品购物车模块业务逻辑
 * @author 幸福无期
 */
class MallCartModel extends Model{  
    protected $tableName='Mall_cart'; //切换检测表
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
           'goods_id'  => intval($cart_data['goods_id']),
           'number' => $cart_data['number'],
           'member_id' => intval($cart_data['member_id']),
           'time'      => date("Y-m-d H:i:s")
       );
       /*验证数据*/
       $cart  = D("Mall_cart");
       /*获取商品数据*/
       if($data['goods_id']){
           $goods = M("Mall_goods")
                    ->where(array('id'=>$data['goods_id']))
                    ->field("goods_number")
                    ->find(); 
           if(!$goods){
               return array('status' => 0,'msg' => '商品不存在');
           }      
       }else{
           return array('status' => 0,'msg' => '请输入商品id');
       }
       $rules = array(
            array('member_id',array(0),'必须输入会员id！',2,'notin'),
            array('member_id','/^[1-9]\d*$/','会员id错误'),
            array('number','require','必须选择商品数量'),
            array('number','/^[1-9]\d*$/','请选择正确的数量'),
       );
       if($cart->validate($rules)->create($data) === false){
           return array('status' => 0,'msg' => $cart->getError());
       } 
       $id = M("Mall_cart")->add($data);
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
       $r = M("Mall_cart")->where(array('id'=>array('in' , $cart_ids),'member_id'=>$member_id))->delete();
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
       	   $carts = M('Mall_cart')
                  ->field("goods_id,number")
                  ->where(array('member_id'=>$member_id,'goods_id'=>array('in',$goods_ids)))
                  ->select();
       }else{
           $carts = M('Mall_cart')
                  ->field("goods_id,number")
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
       $goods_id     = M('Mall_cart')->where(array('id'=>$cart_id))->getField('goods_id');
       $goods_number = M('Mall_goods')
                     ->where(array('id'=>$goods_id))
                     ->getField('goods_number');
       if($number > $goods_number){
           return array(
               'status' => 0,
               'msg'    => '库存不足'
           );
       }
       $save_data['number'] = $number;
       $r = M('Mall_cart')->where(array('id'=>$cart_id))->save($save_data);
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