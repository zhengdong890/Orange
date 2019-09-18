<?php
namespace Home\Model;
use Think\Model;
/**
 * 商城商品购物车模块业务逻辑
 * @author 幸福无期
 */
class MallCartModel extends Model{ 
   protected $tableName = 'Mall_cart'; //切换检测表
   /**
    * 商品加入购物车
    * @param array data      购物车数据 
    * @param int   member_id 会员id
    * @return array 返回操作结果
    */
    public function cartAdd($data = array() , $member_id = ''){
        $member_id = intval($member_id);
        foreach($data as $v){
            $v = array(
                'goods_id'  => intval($v['goods_id']),
                'number'    => intval($v['number']),
                'member_id' => $member_id,
                'time'      => date("Y-m-d H:i:s"),
                'sku_id'    => intval($v['sku_id'])
            );
            $id = M("Mall_cart")->add($v);
        }
        if($id === false){
            return array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }
        return array(
            'status' => 1,
            'msg'    => '商品已经成功加入购物车'
        );
    }
    
    /*
     * 检测购物车商品 合法性
     * */
    public function checkCartData($data , $goods_id , $member_id){
        if($goods_id == 0){
            return array(
                'status' => 0,
                'msg'    => '商品id错误'
            );
        }
        if(empty($data)){
            return array(
                'status' => 0,
                'msg'    => '不能为空'
            );
        }
        $r = M('Mall_goods')->where(array('id'=>$goods_id,'status'=>1))->field('id,member_id')->find();
        //商品是否存在
        if(!$r['id']){
            return array(
                'status' => 0,
                'msg'    => '商品不存在'
            );
        }  
        //该商家是否进行了企业认证
        if(!D('Home/Member')->isRenzhengById($r['member_id'])){
            return array(
                'status' => 0,
                'msg'    => '该商家未进行企业认证'
            );            
        } 
        //是否是自己的商品
        if($member_id == $r['member_id']){
            return array(
                'status' => 0,
                'msg'    => '不能购买自己的商品'
            );
        }
        /*检测sku*/
        $rules = array(
            array('number','require','必须选择商品数量',self::MUST_VALIDATE),
            array('number','/^[1-9]\d*$/','请选择正确的数量',self::MUST_VALIDATE),
            array('sku_id','/^[1-9]\d*$/','sku_id错误',self::MUST_VALIDATE),
        );
        $model      = D("Mall_cart");
        $sku_id     = array();
        $sku_number = array();
        foreach($data as $v){
            $v = array(
                'number'  => intval($v['number']),
                'sku_id'  => intval($v['sku_id'])
            );
            if($model->validate($rules)->create($v) === false){
                return array('status' => 0,'msg' => $model->getError());
            } 
            $sku_id[] = $v['sku_id'];  
            $sku_number[$v['sku_id']] = $v['number'];       
        }
        /*检测库存*/
        $sku_data = D('Home/MallGoodsSku')->getSkuById($sku_id , 'sku_id,number');
        if(count($sku_data) != count($data)){
            return array('status' => 0 , 'msg' => 'sku_id不存在');
        }
        foreach($sku_data as $v){
            if($v['number'] < $sku_number[$v['sku_id']]){
               // return array('status' => 0 , 'msg' => '库存不足');
            }
        }
        return array('status' => 1);
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
    * 根据会员id获取购物车的所有商品
    * @param  int       $member_id 会员id 
    * @return array     返回操作结果
    */
    public function getCartByMemberId($member_id){
    	if(intval($member_id) == 0){
            $cart = $_COOKIE['mall_cart'];
            $cart = unserialize($cart);
            if(empty($cart)){
                return array('data'=>array()); 
            } 
            $sku_id = implode(',' , array_column($cart , 'sku_id'));
            $sku    = M('Sku')
	            ->where(array('sku_id'=>array('in' , $sku_id)))
	            ->field("sku_id,sku_code,term,price as goods_price")
	            ->select();
	        $sku    = array_all_column($sku  , 'sku_id');
	        foreach($cart as $k => $v){
	        	$cart[$k]['cart_id']     = $k;
                $cart[$k]['sku_code']    = $sku[$v['sku_id']]['sku_code'];
                $cart[$k]['term']        = $sku[$v['sku_id']]['term'];
                $cart[$k]['goods_price'] = $sku[$v['sku_id']]['goods_price'];
	        }
            $cart_data = $cart;
    	}else{
	    	/*获取购物车商品*/
	        $cart_data = M('Mall_cart as c')
	            ->join('tp_sku as s on c.sku_id=s.sku_id' , 'left')
	            ->where(array('c.member_id'=>$member_id))
	            ->field("c.id as cart_id,c.goods_id,c.sku_id,c.number,s.sku_code,s.term,s.price as goods_price,s.number as goods_number")
	            ->select();
	        if(empty($cart_data)){
	            return array(); 
	        }    		
    	}
        /*获取商品数据*/
        $goods_id = implode(',' , array_column($cart_data , 'goods_id' , 'goods_id'));
        $goods = M('Mall_goods')
            ->where(array('id'=>array('in',$goods_id)))
            ->field('id,goods_name,goods_thumb,member_id,goods_price')
            ->select();
        $goods = array_all_column($goods , 'id');
        /*获取商品所属店铺信息*/
        $seller_id = array_column($goods , 'member_id');
        $shop_data = D('ShopData')->getShopDataByMemberId($seller_id , $field = 'member_id,shop_name');
        if(!empty($shop_data)){
        	$shop_data = array_column($shop_data , 'shop_name' , 'member_id');
        }
        foreach($cart_data as &$v){
            $v['goods_name']  = $goods[$v['goods_id']]['goods_name'];
            $v['goods_thumb'] = $goods[$v['goods_id']]['goods_thumb'];
            $v['member_id']   = $goods[$v['goods_id']]['member_id'];
            $v['shop_name']   = $shop_data[$v['member_id']];
            if($v['sku_id'] == 0){
            	$v['sku_code'] = '无';
            	$v['term']     = '无';
                $v['goods_price'] = $goods[$v['goods_id']]['goods_price'];
            }
        }
        return $cart_data;
    }       

   /**
    * 获取购物车的商品
    * @param  int       $member_id 会员id 
    * @param  array|int $goods_ids 商品id 
    * @return array     返回操作结果
    */
    public function getCart($member_id , $cart_id){
    	$cart_id = is_array($cart_id) ? $cart_id : array($cart_id);
       	$cart_id = implode(',' , $cart_id);
       	$data    = M('Mall_cart')
                 ->field("id,goods_id,number,sku_id")
                 ->where(array('id'=>array('in',$cart_id) , 'member_id'=>$member_id))
                 ->select();
        return $data;
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
       $goods_id     = M('Mall_cart')->where(array('id'=>$cart_id))->getField('sku_id');
       $goods_number = M('Sku')
                     ->where(array('sku_id'=>$goods_id))
                     ->getField('number');
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

/*******************************************以前商品预留功能***************************************/

   /**
    * 商品加入购物车
    * @param array cart_data 购物车数据 
    * @return array 返回操作结果
    */
   public function cartOldAdd($cart_data = array()){
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
}