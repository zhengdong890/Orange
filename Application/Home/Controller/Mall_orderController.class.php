<?php
/*
 * 商城商品订单处理
 * */  
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class Mall_orderController extends Controller {	
	public function _initialize(){
        if(empty($_SESSION['member_data'])){
            if(IS_AJAX){
	            $this->ajaxReturn(array(
	                'status' => 0,
	                'msg'    => '请登录'
	            ));
            }else{
            	header("Location:http://www.orangesha.com/login.html");
            }
        }
        if(IS_GET){
	        $id = $_SESSION['member_data']['id'];  
	        $redis = new \Com\Redis();       
	        unset($_SESSION['order_total']);
	        /*订单数量统计处理*/
	        Hook::add('totalOrder','Home\\Addons\\TotalAddon');
	        Hook::listen('totalOrder',$id);
	        /*购物车统计处理*/
	        Hook::add('totalCart','Home\\Addons\\TotalAddon');
	        Hook::listen('totalCart',$id);    
	        /*底部帮助*/
	        Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
	        Hook::listen('getFooterHelp');
	        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
	        $this->assign('help' , $help);
	        $this->assign('order_total' , $_SESSION['order_total']);
	        $this->assign('cart_total' , $_SESSION['cart_total']);
        }
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

/***********************************************下单流程******************************************************/
    /*
     * 快速下单
     * */
    public function quickBuy(){
    	$member_id = $_SESSION['member_data']['id'];
        $data = I('data');
        if(empty($data)){
            exit('清输入正确的sku');
        }
        $buy_data = array();
        foreach($data as $v){
        	$value    = explode('_' , $v);
        	$sku_code = $value[0];
        	$number   = intval($value[1]);
        	if($number <= 0){
                exit('清输入正确的数量');
        	}
        	if(!isset($buy_data[$sku_code])){
	            $buy_data[$sku_code] = array(
	                'sku_code' => $sku_code,
	                'number'   => $number
	            );        		
        	}else{
                $buy_data[$sku_code]['number'] += $number;
        	}
        }
        /*获取商品sku信息*/
        $sku_code  = array_keys($buy_data);
        $goods_sku = D('MallGoodsSku')->getSkuByCode($sku_code);
        if(count($goods_sku) != count($sku_code)){
            exit('sku错误');
        }
        foreach($goods_sku as $v){
        	if($v['seller_id'] == $member_id){
                exit('您不能购买自己的商品');
        	}
            $buy_data[$v['sku_code']]['goods_id'] = $v['goods_id']; 
            $buy_data[$v['sku_code']]['sku_id']   = $v['sku_id']; 
        }
        /*获取购买商品的其他信息*/
        $goods_ids  = array_unique(array_column($goods_sku , 'goods_id'));
        $field      = "id,goods_price,goods_number,goods_name,member_id,templet_id,goods_thumb";
        $goods_data = D('Mall_goods')->getGoodsDataById($goods_ids , $field);
        //组装订单数据                 
        $order  = $this->getOrderData($goods_data , $goods_sku , $buy_data);
        /*获取优惠券*/
	    $seller_id = array_column($goods_data , 'member_id');
	    $seller_id = array_unique($seller_id);
	    $coupon    = D('MemberCoupon')->getCoupons($member_id , $seller_id);                    
        /*组装 订单数据 用于显示*/
        $coupon_json = array();
        foreach($order['order'] as $k => $v){
        	//获取店铺信息
        	$shop_data = M('Shop_data')->where(array('member_id'=>$k))->find();
        	$order['order'][$k]['pay_price'] = $v['total_price'];
            $order_list[$k]              = $v;
            $order_list[$k]['data']      = $order['order_data'][$k];
            $order_list[$k]['shop_name'] = $shop_data['shop_name'];                 
            //过滤不满足条件的优惠券
            foreach($coupon[$k] as $k1 => $v1){               	
	        	if($v['total_price'] < $v1['max']){
                    unset($coupon[$k][$k1]);
	        	} 
            }
            if(!empty($coupon[$k])){
            	$coupon_json[$k] = $coupon[$k];
                $order_list[$k]['coupon'] = $coupon[$k];                  	
            }else{
                $order_list[$k]['coupon'] = array();    
            }   
        } 
        $_SESSION['order'] = $order;
        $_SESSION['cart_ids'] = $cart_ids;
        //获取收货地址
        $address = D('MemberAddress')
                 ->getAddressList(array('member_id'=>$member_id) , 'is_use desc');
        $this->assign('address' , $address);
        $this->assign('address_json' , count($address)?json_encode($address):'{}');
        $this->assign('coupon_json' , json_encode($coupon_json));
        $this->assign('order_list' , $order_list);
        $this->assign('total_price' , array_sum(array_column($order_list, 'total_price')));
        $this->assign('goods_ids' , implode(',' , $goods_ids));
        $this->assign('buy_number' , count($buy_data));
        $this->display('orderConfirm');        
    }  

    /*
     * 1 直接买
     * */
    public function buy(){
        $member_id = $_SESSION['member_data']['id'];
        $goods_id  = intval(I('goods_id'));//商品id
        $sku_data  = I('sku');
        if(empty($sku_data)){
            exit('请选择购买信息');
        }
        /*组装获取的购买 sku信息*/
        $buy_data = array();
        $sku_id   = array();
        foreach($sku_data as $v){
        	$value = explode('_' , $v);
            $buy_data[] = array(
            	'goods_id' => $goods_id,
                'sku_id'   => intval($value[0]),
                'number'   => intval($value[1])
            );
            $sku_id[] = intval($value[0]);
        }      
        $goods_sku  = D('MallGoodsSku')->getSkuById($sku_id); //获取购买的商品sku信息
        if(in_array($member_id , array_column($goods_sku , 'seller_id'))){
            exit('您不能购买自己的商品');
        }
        /*检测请求sku合法性*/ 
        $temp = array_unique(array_column($goods_sku , 'goods_id'));
        if(count($temp) > 1 || array_pop($temp) != $goods_id){
             exit('非法操作');
        }
        //获取购买商品的其他信息
        $field      = "id,goods_price,goods_number,goods_name,member_id,templet_id,goods_thumb";
        $goods_data = D('Mall_goods')->getGoodsDataById(array($goods_id) , $field);
        //组装订单数据                 
        $order  = $this->getOrderData($goods_data , $goods_sku , $buy_data);  
	    /*获取优惠券 处理*/
        $seller_id = $goods_data[0]['member_id'];
        $coupon    = D('MemberShopCoupon')->getCoupons($member_id , $seller_id);                    
        /*组装 订单数据 用于显示*/
        $coupon_json = array();
        foreach($order['order'] as $k => $v){
        	$order['order'][$k]['pay_price'] = $v['total_price'];
            //过滤不满足条件的优惠券
            foreach($coupon[$k] as $k1 => $v1){               	
	        	if($v['total_price'] < $v1['max']){
                    unset($coupon[$k][$k1]);
	        	} 
            }
            $coupon_json[$k] = empty($coupon[$k]) ? array() : $coupon[$k];
            //获取店铺信息
        	$shop_data = M('Shop_data')->where(array('member_id'=>$k))->find();
            $order_list[$k]              = $v;
            $order_list[$k]['data']      = $order['order_data'][$k];
            $order_list[$k]['shop_name'] = $shop_data['shop_name']; 
            $order_list[$k]['coupon']    = $coupon[$k];                       
        } 
        $_SESSION['order'] = $order;
        //获取收货地址
        $address = D('MemberAddress')
                 ->getAddressList(array('member_id'=>$member_id) , 'is_use desc');
        $this->assign('address' , $address);
        $this->assign('address_json' , count($address)?json_encode($address):'{}');
        $this->assign('coupon_json' , json_encode($coupon_json));
        $this->assign('order_list' , $order_list);
        $this->assign('total_price' , array_sum(array_column($order_list, 'total_price')));
        $this->assign('goods_ids' , implode(',' , $goods_ids));
        $this->assign('buy_number' , count($buy_data));
        $this->display('orderConfirm'); 
    }   

    /*
	 * 1 购物车下单 - 订单确认
	 * */  
    public function orderConfirm(){
        if(IS_POST){
            $member_id = $_SESSION['member_data']['id']; 
            $data      = I();
            /*从购物车中获取商品购买数据*/
            $temp_cart = D('MallCart')->getCart($member_id , $data['cart_id']);
            if(!$temp_cart){
            	exit('购物车无该商品');
            }
            $goods_ids = array();
            foreach($temp_cart as $k => $v){
                $goods_ids[] = $v['goods_id'];   
                $cart_ids[]  = $v['id'];
                $carts[]     = $v;
                $sku_id[]    = $v['sku_id'];//sku_id
            }
            $cart_ids   = implode(',' , $cart_ids);
            //获取购买的商品信息
            $field      = "id,goods_price,goods_number,goods_name,member_id,templet_id,goods_thumb";
            $goods_data = D('Mall_goods')->getGoodsDataById($goods_ids , $field);
            //获取购买的商品sku信息
            $goods_sku  = D('MallGoodsSku')->getSkuById($sku_id);  
            if(in_array($member_id , array_column($goods_sku , 'seller_id'))){
                exit('您不能购买自己的商品');
            }  
            //获取订单数据                 
            $order  = $this->getOrderData($goods_data , $goods_sku , $carts); 
	        /*获取优惠券*/
	        $seller_id = array_column($goods_data , 'member_id');
	        $seller_id = array_unique($seller_id);
	        $coupon    = D('MemberShopCoupon')->getCoupons($member_id , $seller_id);
	        //获取平台优惠券
	        //$shop_coupon = D('MemberCoupon')->getCoupons($member_id);                    
            /*组装 订单数据 用于显示*/
            $coupon_json = array();
            foreach($order['order'] as $k => $v){
            	//获取店铺信息
            	$shop_data = M('Shop_data')->where(array('member_id'=>$k))->find();
            	$order['order'][$k]['pay_price'] = $v['total_price'];
                $order_list[$k]              = $v;
                $order_list[$k]['data']      = $order['order_data'][$k];
                $order_list[$k]['shop_name'] = $shop_data['shop_name'];                 
                //过滤不满足条件的优惠券
                foreach($coupon[$k] as $k1 => $v1){               	
		        	if($v['total_price'] < $v1['max']){
	                    unset($coupon[$k][$k1]);
		        	} 
                }
                if(!empty($coupon[$k])){
                	$coupon_json[$k] = $coupon[$k];
                    $order_list[$k]['coupon'] = $coupon[$k];                  	
                }else{
                    $order_list[$k]['coupon']      = array();    
                }   
            } 
            $_SESSION['order'] = $order;
            $_SESSION['cart_ids'] = $cart_ids;
            
            //获取收货地址
            $address = D('MemberAddress')
                     ->getAddressList(array('member_id'=>$member_id) , 'is_use desc');
            $this->assign('address' , $address);
            $this->assign('address_json' , count($address)?json_encode($address):'{}');
            $this->assign('coupon_json' , json_encode($coupon_json));
            $this->assign('order_list' , $order_list);
            $this->assign('total_price' , array_sum(array_column($order_list, 'total_price')));
            $this->assign('goods_ids' , implode(',' , $goods_ids));
            $this->assign('buy_number' , count($data['cart_id']));
            $this->display();            
        }
    }

    /*
	 * 2 确认后 订单入库
	 * */     
    public function orderAdd(){
    	$member_id = $_SESSION['member_data']['id']; 
        $data      = I();
        $order = $_SESSION['order'];
        if(empty($order)){
            exit('订单不存在');
        }      
        $order_price      = array_sum(array_column($order['order'] , 'total_price'));//订单总计价格
        $shop_coupon_case = 0;//店铺优惠券总计减免金额
        $coupon_case      = 0;//平台优惠券总计减免金额

        /*买家选择的订单地址处理*/
        $addres_id = intval($data['address_id']);
        if(empty($addres_id)){
            exit('请选择收货地址');
        }
        $address = M('Member_address')
	        ->where(array('member_id'=>$member_id,'id'=>$addres_id))
	        ->Field('id,address,telnum,name,city,address_xx')
	        ->find();
        if(empty($address)){
            exit('地址不存在');
        } 

        /*买家留言处理 按商家获取*/
        foreach($data as $k => $v){
           if(substr($k , 0 , 7) == 'remark_'){
               $order_id          = substr($k , 7);
               $remark[$order_id] = $v;
           }
        } 

        /*1 店铺优惠券处理 按商家分开处理*/       
        if(!empty($data['member_coupon']) && $data['member_coupon'][0] != 0){
        	/*获取优惠券*/
	        $coupons = $data['member_coupon'];
	        foreach($coupons as $k => $v){
	            $value = explode('_' , $v);
	            $member_coupon[$value[0]][] = array(
	            	'seller_id' => $value[0],//卖家id
	            	'id'        => $value[1] //
	            );
	        }
	        //计算店铺优惠券减免的金额
	        $coupon_data = D('MemberShopCoupon')->getShopCouponCase($member_id , $member_coupon , $order['order']);
	        if($coupon_data === false){
	            exit('优惠券选择错误');
	        }
	        $shop_coupon_case = $shop_coupon_case + array_sum($coupon_data);
        } 
        
        /*2 平台优惠券处理*/
        /*if(!empty($data['member_coupon_id'])){  
            //先减去优惠券减免的金额     	
	        $price = $order_price - $shop_coupon_case;
	        //判断是否能使用选择的平台优惠券 并返回优惠的金额
	        $coupon_case = D('MemberCoupon')->getCouponCase($member_id , $data['member_coupon_id'] , $price);
	        if($coupon_case === false){
	            exit('不满足使用平台优惠券的条件');
	        }
        }*/        

        /*订单数据 按商家分开组装*/       
        $order_html_data  = array();//html显示订单支付数据 
        foreach($order['order'] as $k => $v){
        	$order['order'][$k]['address_id'] = $address['id'];//收货地址id
        	$order['order'][$k]['city']       = $address['city'];//收货地址详细 市
            $order['order'][$k]['remark']     = $remark[$k];//留言
            $order['order'][$k]['address']    = $address['address'] . " {$address['address_xx']}";
            $order['order'][$k]['tel_num']    = $address['telnum'];//收货人电话
            $order['order'][$k]['name']       = $address['name'];//收货人姓名
            /*使用的店铺优惠券信息 放入订单数据里面*/
            if(!empty($coupon_data)){
            	//使用会员优惠券的id
                $member_coupon_id = implode(',' , array_column($member_coupon[$k] , 'id'));
                $order['order'][$k]['member_shop_coupon_id'] = $member_coupon_id;
                //使用会员优惠券减免的金额
                $order['order'][$k]['shop_coupon_price'] = $coupon_data[$k];
                //实际支付价格 减去优惠券减免的金额
                $coupon_data[$k] = $order['order'][$k]['pay_price'] > $coupon_data[$k] ? $coupon_data[$k] : $order['order'][$k]['pay_price'];//避免出现负数价格 最低减到0元
                $order['order'][$k]['pay_price'] -= $coupon_data[$k];
            }           
            $order_html_data[$k] = $v;//订单
            $order_html_data[$k]['data'] = $order['order_data'][$k];//订单详情   
        }

        /*根据选择的收货地址 购买商品的信息 计算运费*/
        //循环计算每个商家的运费
        foreach($order['order_data'] as $k => $v){
        	$buy_data = array('order'=>$order['order'][$k],'order_data'=>$v);
            $shipping_price = D('ShippingTemplet')->getTempletPrice($k , $buy_data);
            //运费
            $order['order'][$k]['shipping_price'] = $shipping_price;
            //实际支付价格 加上运费
            $order['order'][$k]['pay_price'] += $shipping_price;         
        }

        /*订单入库*/
        $result = D('Mall_order')->orderAdd($order['order'] , $order['order_data'] , $member_id);//生成订单
        if(!$result['status']){//提交失败
        	exit($result['msg']);
        }
        //获取订单号
        $order_id = $result['data']['pay_id'] ? $result['data']['pay_id'] : array_pop($result['data']['order_id']);
        //获取支付编号  
        $pay_sn   = 'sc'.$result['data']['order_type'].$order_id;    
    	//优惠券状态改变 设置为已经使用
    	if(!empty($coupon_data)){
    	    M('Member_shop_coupon')->where(array('id'=>array('in' , $member_coupon_id)))->save(array('status'=>2));
        }               	
        unset($_SESSION['order']);//清空订单
        //清空购物车
        if($_SESSION['cart_ids']){
            M('Mall_cart')
            ->where(array('id'=>array('in' , $_SESSION['cart_ids']),'member_id'=>$member_id))
            ->delete(); 
            unset($_SESSION['cart_total']);
            unset($_SESSION['cart_ids']);           	
        }
        /*订单支付统计*/ 
        $order_total['id'] = $order_id; 
        $order_total['pay_price'] = array_sum(array_column($order['order'] , 'pay_price'));
        $order_total['pay_price'] = sprintf("%.2f", $order_total['pay_price']);//转两位小数
        /*商户是否签约 只要有一家商户未签约则此次不允许交易*/
        $seller_ids = array_column($order['order'] , 'seller_id');
        $shop_data  = D('ShopData')->getShopDataByMemberId($seller_ids , 'member_id,is_sign');
        $is_signs   = array_column($shop_data , 'is_sign' , 'member_id');
        if(in_array(0 , $is_signs)){
           $order_total['is_can_pay']  = 0;
        }else{
        	$order_total['is_can_pay']  = 1;
        }

        $_SESSION['pay_sn']         = $pay_sn;//支付编号
        $_SESSION['order_pay_data'] = $order_total;
        $this->assign('pay_price' , $order_total['pay_price']);//支付价格
        $this->assign('address' , $address);//地址
        $this->assign('order_data' , $order_html_data);
        //输出确认支付页面
        $this->display('paymentConfirm');
    }

    /*
     * 订单重新付款
     * */
    public function orderAgainPay(){
        $member_id = $_SESSION['member_data']['id'];
        $order_id  = intval(I('order_id'));
        $result    = D('Mall_order')->orderIsCanPay($member_id , $order_id);
        if($result['status'] == 0){
            exit($result['msg']);
        }
        $order   = $result['data'];         
        $address = array(
            'address' => $order['address'],
            'name'    => $order['name'],
            'telnum'  => $order['telnum']
        );
        $pay_sn = 'sc'.'1'.$order_id;//支付编号 
        $shop_data  = D('ShopData')->getShopDataByMemberId(array($order['seller_id']) , 'member_id,is_sign');
        $is_can_pay = $shop_data[$order['seller_id']] == 1 ? 1 : 0;//是否能够支付
        $_SESSION['pay_sn']         = $pay_sn;//支付编号
        $_SESSION['order_pay_data'] = array(
        	'pay_price'  => $order['pay_price'],
        	'is_can_pay' => $is_can_pay,
        	'id'         => $order_id
        );
        $this->assign('pay_price' , $order['pay_price']);//支付价格 
        $this->assign('address' , $address);//地址
        $this->assign('order_data' , array($order));   
        //输出确认支付页面
        $this->display('paymentConfirm');
    }

   /**
    * 设置订单支付方式 为线下公对公
    */
    public function setOrdePayModelFour(){
        if(IS_AJAX){
	        $member_id = $_SESSION['member_data']['id'];
	        if(empty($_SESSION['order_pay_data']) || !$_SESSION['pay_sn']){
                $this->ajaxReturn(array('status'=>0,'msg'=>'订单不存在'));
    	    }
            if($_SESSION['order_pay_data']['is_can_pay'] == 0){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'msg'    =>'该商户还在测试开店期，还没开通在线支付，采购请直接联系店家'
                ));
            }
    	    $order_id  = $_SESSION['order_pay_data']['id'];
	        $result    = D('Mall_order')->setOrdePayModel($member_id , $order_id , 4);
	        $this->ajaxReturn($result);        	
        }
          
    }

    /*
	 * 设置订单收货地址 返回运费
	 * */     
    public function setAddress(){
        if(IS_AJAX){
        	$order = $_SESSION['order'];
        	if(empty($order)){
		        $this->ajaxReturn(array('status'=>0,'msg'=>'订单不存在'));
		    }  
            $id        = intval(I('id')); 
            $member_id = $_SESSION['member_data']['id']; 
            $address   = M('Member_address')
	            ->where(array('id' => $id , 'member_id' => $member_id))
	            ->Field('id,address,telnum,name')
	            ->find();
	        if(empty($address)){
	            $this->ajaxReturn(array('status'=>0,'msg'=>'地址不存在'));
	        } 
	        $total_price = array_column($order[0] , 'total_price');
	        $total_price = array_sum($total_price);        
            /*根据选择的收货地址 计算运费*/
            $shipping = array();//运费
		    foreach($order['order_data'] as $k => $v){
		    	$buy_data = array('order'=>$order['order'][$k],'order_data'=>$v);
		        //运费
		        $shipping[$k] = D('ShippingTemplet')->getTempletPrice($k , $buy_data);
		    }
		    $this->ajaxReturn(array(
		    	'status' => 1,
		    	'msg'    => 'ok',
		    	'data'   => $shipping
		    ));
        }
    }
    
   /**
    * 生成订单数据 按商家生成订单号 
    * @param  array $goods_data 商品基本信息  
    * @param  array $goods_sku  商品sku信息  
    * @param  array $buy_data   购买信息   
    * @return array 返回结果
    */     
    protected function getOrderData($goods_data , $goods_sku ,$buy_data){
    	/*获取商品卖家店铺信息 用于判断否签约*/
    	$seller_ids = array_column($goods_data , 'member_id');
        //商品信息处理 便于后面获取  
        $goods_data = array_all_column($goods_data , 'id');
        //商品sku信息处理 便于后面获取
        $goods_sku  = array_all_column($goods_sku , 'sku_id');
        //买家id
        $member_id  = $_SESSION['member_data']['id'];
        //买家名字
        $name       = M('Member_data')->where(array('member_id'=>$member_id))->getField('nickname'); 
        /*获取订单数据*/
        $order      = array();//订单
        $order_data = array();//订单详情
        foreach($buy_data as $k => $v){
        	//卖家id
            $seller_id = $goods_data[$v['goods_id']]['member_id'];
            $seller_id = $seller_id ? $seller_id : 0;
            //购买数量
            $number    = $v['number'];
            //购买总价
            $price     = $goods_sku[$v['sku_id']]['price'] * $v['number'];
            //订单详情按商家组装
            $order_data[$seller_id][] = array(
                'goods_id'    => $v['goods_id'],//商品id
                'number'      => $v['number'],//购买数量
                'member_id'   => $member_id,//购买会员id
                'total_price' => $price,//购买总价
                'goods_price' => $goods_sku[$v['sku_id']]['price'],//商品单价
                'goods_name'  => $goods_data[$v['goods_id']]['goods_name'],//商品名称
                'goods_thumb' => $goods_data[$v['goods_id']]['goods_thumb'],//商品图片
                'seller_id'   => $seller_id,//卖家id
                'sku_id'      => $v['sku_id'],//商品sku_id
                'sku_code'    => $goods_sku[$v['sku_id']]['sku_code'],//商品sku订货号
                'attr'        => $goods_sku[$v['sku_id']]['sku_value'],
                'templet_id'  => $goods_data[$v['goods_id']]['templet_id'],
                'create_time' => time(),//下单时间
                'name'        => $name
            );    
            /*商家订单总价统计*/
            if($order[$seller_id]['total_price']){
                $order[$seller_id]['total_price'] += $price;
            }else{
                $order[$seller_id]['total_price'] = $price;
            }
            //订单编号
            !isset($order[$seller_id]['order_sn'])  && ($order[$seller_id]['order_sn']  = setnum(10));
            //购买人id
            !isset($order[$seller_id]['member_id']) && ($order[$seller_id]['member_id'] = $member_id);
            //商家id
            !isset($order[$seller_id]['seller_id']) && ($order[$seller_id]['seller_id'] = $seller_id);
            //添加时间
            !isset($order[$seller_id]['create_time']) && ($order[$seller_id]['create_time'] = time());
            //买家姓名
            !isset($order[$seller_id]['name']) && ($order[$seller_id]['name'] = $name);
            //订单状态
            $order[$seller_id]['status'] = 1;
            $order[$seller_id]['is_check'] = 1;    
        }
        return array(
        	'order'      => $order , 
        	'order_data' => $order_data
        );
    }

   /**
    * 取消订单  
    * @return array 返回结果
    */ 
    public function orderDelete(){
        if(IS_AJAX){
            $member_id = $_SESSION['member_data']['id'];//买家id
            $id     = intval(I('id'));
            $result = D('Mall_order')->orderDelete($member_id  , $id);
            /*更新订单统计缓存*/
            if($result['status']){
            	$_SESSION['order_total']['2,0,0'] = $_SESSION['order_total']['2,0,0'] - 1;
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
            $result = D('Mall_order')->recipient($member_id  , $id);
            /*更新订单统计缓存*/
            if($result['status']){
            	$_SESSION['order_total']['2,1,1'] = $_SESSION['order_total']['2,1,1'] - 1;
            	$_SESSION['order_total']['2,1,2'] = $_SESSION['order_total']['2,1,1'] + 1;
            }
            $this->ajaxReturn($result);
        }
    } 

/*******************************************以前商品预留功能***************************************/
    /*
     * 订单增加
     * */     
    public function orderOldAdd(){
        $member_id = $_SESSION['member_data']['id']; 
        $data      = I();
        $addres_id = intval($data['address_id']);
        if(!$addres_id){
            exit('请选择收货地址');
        }
        /*订单地址*/
        $address = M('Member_address')->where(array('member_id'=>$member_id,'id'=>$addres_id))->Field('id,address,telnum,name')->find();
        if(!$address['id']){
            exit('地址不存在');
        }
        $order = $_SESSION['order_data'];
        if(!$order){
            exit('已过期');
        }      
        /*获取留言*/
        foreach($data as $k => $v){
           if(substr($k , 0 , 7) == 'remark_'){
               $order_id          = substr($k , 7);
               $remark[$order_id] = $v;
           }
        } 
        /*留言加入订单*/
        $order_data = array();//订单支付详情组装
        $total_price = 0;
        foreach($order[0] as $k => $v){
            $order[0][$k]['remark']  = $remark[$k];
            $order[0][$k]['address'] = $address['address'];
            $order[0][$k]['tel_num'] = $address['telnum'];
            $order[0][$k]['name']    = $address['name'];
            $order_data[$k] = $v;
            $order_data[$k]['data'] = $order[1][$k];
            $total_price += $v['total_price'];
        }
        $result = D('Mall_order')->orderOldAdd($order[0] , $order[1]);//生成订单
        if($result['status']){
            unset($_SESSION['order_data']);
            $cart_ids = $_SESSION['cart_ids'];
            if($_SESSION['cart_ids']){
                M('Mall_cart')->where(array('id'=>array('in' , $cart_ids),'member_id'=>$member_id))->delete(); 
                unset($_SESSION['cart_total']);
                unset($_SESSION['cart_ids']);               
            }
        }else{
            echo $result['msg'];die;
        }
        $order_id = $result['data']['pay_id'] ? $result['data']['pay_id'] : array_pop($result['data']['order_id']);
        $pay_sn   = 'sc'.setnum(10).$result['data']['order_type'].$order_id.setnum(4);//支付编号
        $_SESSION['pay_sn']      = $pay_sn;
        $_SESSION['order_data']  = $order_data;
        $_SESSION['total_price'] = $total_price;
        $_SESSION['address']     = $address;
        $this->redirect('paymentOldConfirm');
    } 

    /*
     * 订单支付
     * */ 
    public function paymentOldConfirm(){
        if(!$_SESSION['order_data']){
            exit('订单不存在');
        }
        $order_data  = $_SESSION['order_data'];
        $total_price = $_SESSION['total_price'];
        $address     = $_SESSION['address'];
        $this->assign('total_price' , $total_price);
        $this->assign('address' , $address);
        $this->assign('order_data' , $order_data);
        $this->display();
    }

    public function paymentOld(){
        if(!$_SESSION['order_data']){
            exit('订单不存在');
        }
        $order_data  = $_SESSION['order_data'];
        $total_price = 0.01;//$_SESSION['total_price'];
        $pay_sn   = $_SESSION['pay_sn'];
        unset($_SESSION['order_data'] , $_SESSION['total_price'] , $_SESSION['address'] , $_SESSION['pay_sn']);
        A('Alipay')->alipay($pay_sn, '商城订单' , $total_price);
    }     

        /*
     * 订单确认
     * */  
    public function orderOldConfirm(){
        if(IS_POST){
            $member_id = $_SESSION['member_data']['id']; 
            $data      = I(); 
            /*从购物车中获取商品购买数据*/
            $carts_    = D('MallCart')->getCart($member_id , $data['goods_id']);
            if(!$carts_){
                return array('status'=>0,'msg'=>'购物车无该商品');
            }
            $goods_ids = array();
            foreach($carts_ as $k => $v){
                $goods_ids[] = $v['goods_id'];   
                $cart_ids[]  = $v['id'];
                $carts[]     = $v;
            }
            $cart_ids = implode(',' , $cart_ids);
            /*获取购买的商品信息*/
            $goods_data = M('Mall_goods')
                        ->where(array('id'=>array('in',$goods_ids)))
                        ->field("id,goods_price,goods_number,goods_name,member_id,goods_thumb")
                        ->select();
            $order  = $this->getOrderOldData($goods_data , $carts);//获取订单数据            
            foreach($order[0] as $k => $v){
                $order_list[$k]         = $v;
                $order_list[$k]['data'] = $order[1][$k];
            }                   
            /*获取收货地址*/
            $address = M('Member_address')->where(array('member_id'=>$member_id))->select();
            $_SESSION['order_data'] = $order;
            $_SESSION['cart_ids']   = $cart_ids;
            $this->assign('address' , $address);
            $this->assign('address_json' , count($address)?json_encode($address):'{}');
            $this->assign('order_list' , $order_list);
            $this->assign('total_price' , array_sum(array_column($order_list, 'total_price')));
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
    protected function getOrderOldData($goods_data , $buy_data){
        $goods_data = array_all_column($goods_data , 'id');
        $member_id  = $_SESSION['member_data']['id'];//买家id
        $name       = M('Member_data')->where(array('id'=>$member_id))->getField('name'); 
        /*获取订单数据*/
        $order      = array();
        $order_data = array();

        foreach($buy_data as $k => $v){
            $seller_id = $goods_data[$v['goods_id']]['member_id'];
            $seller_id = $seller_id ? $seller_id : 0;//卖家id
            $number    = $v['number'];
            $price     = $goods_data[$v['goods_id']]['goods_price'] * $v['number'];
            $order_data[$seller_id][] = array(
                'goods_id'    => $v['goods_id'],
                'number'      => $v['number'],
                'member_id'   => $member_id,
                'total_price' => $price,
                'goods_price' => $goods_data[$v['goods_id']]['goods_price'],
                'goods_name'  => $goods_data[$v['goods_id']]['goods_name'],
                'goods_thumb' => $goods_data[$v['goods_id']]['goods_thumb'],
                'seller_id'   => $seller_id,
                'templet_id'  => $goods_data[$v['goods_id']]['templet_id'],
                'create_time' => time()
            );    
            /*商家订单总价统计*/
            if($order[$seller_id]['total_price']){
                $order[$seller_id]['total_price'] += $price;
            }else{
                $order[$seller_id]['total_price'] = $price;
            }
            //订单编号
            isset($order[$seller_id]['order_sn'])  && ($order[$seller_id]['order_sn']  = setnum(10));
            //购买人id
            isset($order[$seller_id]['member_id']) && ($order[$seller_id]['member_id'] = $member_id);
            //商家id
            isset($order[$seller_id]['seller_id']) && ($order[$seller_id]['seller_id'] = $seller_id);
            //添加时间
            isset($order[$seller_id]['time']) && ($order[$seller_id]['time'] = date('Y-m-d H:i:s'));
            //买家姓名
            isset($order[$seller_id]['name']) && ($order[$seller_id]['name'] = $name);
            $order[$seller_id]['is_check'] = 1;       
        }
        return array($order , $order_data);
    }    
}