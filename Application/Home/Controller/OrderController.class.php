<?php
/*
 * 共享商品订单处理
 * */  
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class OrderController extends Controller {	
	public function _initialize(){
        if(empty($_SESSION['member_data'])){
            header("Location:http://www.orangesha.com/login.html");
        }
        $id = $_SESSION['member_data']['id'];  
        $redis = new \Com\Redis();       
        $help = D('HelpCategory')->redisCatName($redis);  
        $this->assign('help' , get_child($help));
        $this->assign('order_total' , $_SESSION['order_total']);
        $this->assign('cart_total' , $_SESSION['cart_total']);
    }
    
    public function orderList(){
    	$this->display();
    }

    public function getOrder(){
       if(IS_AJAX){
       	   $seller_id = $_SESSION['member_data']['id'];
		   $firstRow  = $data['firstRow'];
	   	   $listRows  = $data['listRows'];
	   	   $order_    = M('Order')->where(array('seller_id'=>$seller_id))->select();
	   	   $order_ids = array();
	   	   foreach($order_ as $k => $v){
              $order_ids[]     = $v['id']; 
              $order[$v['id']] = $v;
	   	   }
	   	   $order_ids  = implode(',' , $order_ids);
	   	   $order_data = M('Order_data')
	   	               ->where(array('order_id'=>array('in' , $order_ids)))
	   	               ->limit($firstRow,$listRows)
	   	               ->order('order_id')
	   	               ->select();           
	       $count  = M('Order_data')->where(array('order_id'=>array('in' , $seller_id)))->count();
	       $result = array(
	            'data'      => array(
	            	'order'      => $order,
	            	'order_data' => $order_data
	            ),
	            'totalRows' => $count
	       );  
	       echo json_encode($result);       	
       }
    }
    
    /*
     * 订单重新付款
     * */      
    public function orderAgainPay(){
        $member_id = $_SESSION['member_data']['id'];
        $order_id  = intval(I('order_id'));
        $data   = M('Order')->where(array('id'=>$order_id,'member_id'=>$member_id))->find();
        if($data['status'] == 0){
            echo '请稍后，客服会马上联系你再次核对你的订单!';die;
        }
        if(!$data){
            exit('订单不存在');
        }
        if($data['pay_status'] != 0){
            exit('订单已经支付');
        }
        $pay_sn = 'fx'.setnum(10).'1'.$data['id'].setnum(4 , 'n');//支付编号
        A('Alipay')->alipay($pay_sn, '共享订单' , 0.01/*$pay_data['total_price']*/);
    }

    /*
	 * 订单增加
	 * */ 
    public function orderAdd(){
    	$member_id = $_SESSION['member_data']['id'];         
        $data      = I();
        $addres_id = $data['address_id'];
        if(!$addres_id){
            exit('请输入收货地址');    
        }
        $order = $_SESSION['order_data'];
        if(!$order){
            exit('已过期');
        }
        /*获取留言*/
        foreach($data as $k => $v){
           if(substr($k , 0 , 7) == 'remark_'){
               $seller_id = substr($k , 7);
               $remark[$seller_id] = $v;
           }
        } 
        /*订单地址*/
        $address = M('Member_address')->where(array('member_id'=>$member_id,'id'=>$addres_id))->Field('address,telnum,name')->find();
        /*留言加入订单*/
        foreach($order[0] as $k => $v){
            $order[0][$k]['remark']  = $remark[$k];
            $order[0][$k]['address'] = $address['address'];
            $order[0][$k]['telnumn'] = $address['telnumn'];
            $order[0][$k]['name']    = $address['name'];
        }        
        $result   = D('Order')->orderAdd($order[0] , $order[1]);//生成订单  
        if($result['status'] && $cart_ids){
           $cart_ids = $_SESSION['cart_ids'];
           M('Cart')->where(array('id'=>array('in' , $cart_ids),'member_id'=>$member_id))->delete();    
        }
        unset($_SESSION['order_data']);
        $this->redirect('completePayment'); 
        /*    
        $order_id = $result['data']['pay_id'] ? $result['data']['pay_id'] : array_pop($result['data']['order_id']);
        $pay_sn   = 'fx'.setnum(10).$result['data']['order_type'].$order_id.setnum(4);//支付编号
        //A('Alipay')->alipay($pay_sn, '共享订单' , $result['data']['total_price']);*/
    }
    
    /*
     * 直接租
     * */
    public function buy(){
        $member_id = $_SESSION['member_data']['id'];
        $goods_id = intval(I('goods_id'));//商品id
        $rent_number = intval(I('rent_number'));
        $rent_time = intval(I('rent_time'));
        $data[$goods_id] = array(
            'goods_id'    => $goods_id,
            'rent_number' => $rent_number,
            'rent_time'   => $rent_time           
        );
        /*获取购买的商品信息*/
        $goods_data = M('Goods')
                    ->where(array('id'=>$goods_id))
                    ->field("id,goods_price,goods_number,goods_name,min_rent,max_rent,member_id,goods_thumb,rent_dw,safest,deposit")
                    ->find();
        $goods_data = array($goods_data);
        if(count($goods_data) < 0){
            exit('无该商品');
        }
        if($rent_time <= 0 || $rent_time < $goods_data[0]['min_rent'] || $rent_time > $goods_data[0]['max_rent']){
            exit('租期不正确');
        }
        if($rent_number <= 0 || $rent_number > $goods_data[0]['goods_number']){
            exit('数量不正确');
        }        
        /*获取商品租金优惠区间*/
        $goods_rent = M('Goods_rent')->where(array('goods_id'=>$goods_id))->select();             
        $order  = $this->getOrderData($goods_data , $data , $goods_rent);//获取订单数据
        foreach($order[0] as $k => $v){
            $order_list[$k]         = $v;
            $order_list[$k]['data'] = $order[1][$k];
        }
        /*获取收货地址*/
        $address = M('Member_address')->where(array('member_id'=>$member_id))->select();
        $_SESSION['order_data'] = $order;
        $this->assign('address' , $address);
        $this->assign('address_json' , count($address)?json_encode($address):'{}');
        $this->assign('order_list' , $order_list);        
        $this->display('orderConfirm');   
    }
    
    /*
	 * 订单确认
	 * */  
    public function orderConfirm(){
        if(IS_POST){
            $member_id = $_SESSION['member_data']['id']; 
            $data      = I(); 
            /*从购物车中获取商品购买数据*/
            $goods_ids = $data['goods_id'];
            $carts_    = D('Cart')->getCart($member_id , $data['goods_id']);
            if(!$carts_){
              return array('status'=>0,'msg'=>'购物车无该商品');
            }
            $goods_ids = array();
            foreach($carts_ as $k=>$v){
                $goods_ids[] = $v['goods_id'];   
                $cart_ids[]  = $v['id'];
                $carts[] = $v;
            }
            $cart_ids = implode(',' , $cart_ids);
            /*获取购买的商品信息*/
            $goods_data = M('Goods')
                        ->where(array('id'=>array('in',$goods_ids)))
                        ->field("id,goods_price,goods_number,goods_name,min_rent,max_rent,member_id,goods_thumb,rent_dw,safest,deposit")
                        ->select();
            /*获取商品租金优惠区间*/
            $goods_rent = M('Goods_rent')->where(array('goods_id'=>array('in',$goods_ids)))->select();
            $order  = $this->getOrderData($goods_data , $carts , $goods_rent);//获取订单数据  
            foreach($order[0] as $k => $v){
                $order_list[$k]         = $v;
                $order_list[$k]['data'] = $order[1][$k];
            }
            /*获取收货地址*/
            $address = M('Member_address')->where(array('member_id'=>$member_id))->select();
            $_SESSION['order_data'] = $order;
            $_SESSION['cart_ids'] = $cart_ids;
            $this->assign('address' , $address);
            $this->assign('address_json' , count($address)?json_encode($address):'{}');
            $this->assign('order_list' , $order_list);
            $this->assign('goods_ids' , implode(',' , $goods_ids));
            $this->display();            
        }
    }
    
   /**
    * 生成订单数据 按商家生成订单号 
    * @param  array $goods_data 商品信息  
    * @param  array $buy_data 购买信息   
    * @return array 返回结果
    */     
    protected function getOrderData($goods_data_ , $buy_data ,$goods_rent_){
        foreach($goods_data_ as $k => $v){
            $goods_data[$v['id']] = $v;
        }
        foreach($goods_rent_ as $k => $v){
            $goods_rent[$v['goods_id']][] = $v;
        }
        $member_id  = $_SESSION['member_data']['id'];//买家id
        $name = M('Member_data')->where(array())->getField('name'); 
        /*获取订单数据*/
        $order      = array();
        $order_data = array();
        foreach($buy_data as $k => $v){
            $seller_id = $goods_data[$v['goods_id']]['member_id'];
            $seller_id = $seller_id ? $seller_id : 0;//卖家id
            $rent_time = $v['rent_time'];          
            if($goods_rent[$v['goods_id']]){
                $prices = D('Goods')->totalRent($rent_time , $goods_rent[$v['goods_id']] , $goods_data[$v['goods_id']]['goods_price']);                              
            }else{
                $prices = $goods_data[$v['goods_id']]['goods_price'] * $rent_time;
            }
            $prices = $prices * $v['rent_number'];
            /*计算押金*/
            $deposit_config = array(
                '0' => 0,
                '1' => 3,
                '2' => 6
            );
            $deposit = $goods_data[$v['goods_id']]['deposit'];
            if($deposit_config[$deposit]){
                $deposit_case = $deposit_config[$deposit] * $goods_data[$v['goods_id']]['goods_price'];
            }else{
                $deposit_case = 0;
            }
            /*计算保险 出租保险费率计算：1-6月  千分2.5 7-12月  千分3 13-24月  千分4.5*/
            $safest = 0;
            if($goods_data[$v['goods_id']]['safest']){
                if($rent_time <= 6){
                    $safest = $goods_data[$v['goods_id']]['goods_price'] * 2.5/1000;
                }else 
                if($rent_time <= 12){
                    $safest = $goods_data[$v['goods_id']]['goods_price'] * 3/1000;
                }else 
                if($rent_time <= 24){
                    $safest = $goods_data[$v['goods_id']]['goods_price'] * 4.5/1000;
                }
            }
            $prices = $prices + $deposit_case + $safest;
            $order_data[$seller_id][] = array(
                'goods_id'    => $v['goods_id'],
                'rent_number' => $v['rent_number'],
                'goods_thumb' => $goods_data[$v['goods_id']]['goods_thumb'],
                'goods_name'  => $goods_data[$v['goods_id']]['goods_name'],
                'rent_time'   => $rent_time,
                'goods_price' => $goods_data[$v['goods_id']]['goods_price'],
                'total_price' => $prices,
                'rent_dw'     => $goods_data[$v['goods_id']]['rent_dw'],
                'safest'      => $safest, //保险
                'deposit'     => $deposit_case // 押金
            );
            /*商家订单总价统计*/
            if($order[$seller_id]['total_price']){
                $order[$seller_id]['total_price'] += $prices;
            }else{
                $order[$seller_id]['total_price'] = $prices;
            }
            /*订单编号*/
            if(!$order[$seller_id]['order_sn']){
                $order[$seller_id]['order_sn'] = setnum(10);
            }
            /*购买人id*/
            if(!$order[$seller_id]['member_id']){
                $order[$seller_id]['member_id'] = $member_id;
            }
            /*商家id*/
            if(!$order[$seller_id]['seller_id']){
                $order[$seller_id]['seller_id'] = $seller_id;
            }
            /*添加时间*/
            if(!$order[$seller_id]['time']){
                $order[$seller_id]['time'] = date('Y-m-d H:i:s');
            }
            /*买家姓名*/
            if(!$order[$seller_id]['name']){
                $order[$seller_id]['name'] = $name;
            }         
        }
        return array($order,$order_data);
    }  

   /**
    * 取消订单  
    * @return array 返回结果
    */ 
    public function orderDelete(){
        if(IS_AJAX){
            $member_id = $_SESSION['member_data']['id'];//买家id
            $id     = intval(I('id'));
            $result = D('Order')->orderDelete($member_id  , $id);
            /*更新订单统计缓存*/
            if($result['status']){
            	$_SESSION['order_total']['1,0,0'] = $_SESSION['order_total']['1,0,0'] - 1;
            }
            $this->ajaxReturn($result);
        }
    }

   /**
    * 确认收货  
    * @return array 返回结果
    */ 
    public function recipient(){
        if(IS_AJAX){
            $member_id = $_SESSION['member_data']['id'];//买家id
            $id     = intval(I('id'));
            $result = D('Order')->recipient($member_id  , $id);
            /*更新订单统计缓存*/
            if($result['status']){
            	$_SESSION['order_total']['1,1,1'] = $_SESSION['order_total']['1,1,1'] - 1;
            	$_SESSION['order_total']['1,1,2'] = $_SESSION['order_total']['1,1,1'] + 1;
            }
            $this->ajaxReturn($result);
        }
    }

    /**
     * 发货
     * @return array 返回结果
     */
    public function sendGoods(){
        if(IS_AJAX){
            $member_id = $_SESSION['member_data']['id'];//买家id
            $id = I('id');
            $r  = D('Order')->sendGoods($id , $member_id);
            $this->ajaxReturn($r);
        }  
    }
}