<?php
/*
 * 卖家中心商品处理
 * */  
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class Seller_orderController extends Controller {	
	public function _initialize(){
        if(empty($_SESSION['member_data'])){
            header("Location:http://www.orangesha.com/login.html");
        }
        $redis = new \Com\Redis();
        /*底部帮助*/
        Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
        Hook::listen('getFooterHelp');
        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
        $all_shop_domain = $redis->get('all_shop_data' , 'array');//获取redis的缓存
        $id = $_SESSION['member_data']['id'];       
        $this->assign('domain' , $all_shop_domain[$id]['domain']?$all_shop_domain[$id]['domain']:$id);
        $this->assign('help' , $help);
    }
    
    /* 
     * 商城订单列表
     * */
    public function orderList(){
		$member_id = $_SESSION['member_data']['id'];
		 $this->assign('member_id' , $member_id);
	   	 $this->display();          	
    }

    /* 
     * 获取订单
     * */    
    public function getOrderData(){
    	$get_order_model = array(
            'getThreeMonthOrder'  => 'getThreeMonthOrder',
            'getWaitPayOrder'     => 'getWaitPayOrder',
            'getWaitSendOrder'    => 'getWaitSendOrder',
            'getSendOrder'        => 'getSendOrder',
            'getWaitCommentOrder' => 'getWaitCommentOrder',
            'getSuccessOrder'     => 'getSuccessOrder',
            'getCloseOrder'       => 'getCloseOrder',
            'getThreeMonthBeforeOrder' => 'getThreeMonthBeforeOrder'            
    	);
    	//获取数据类型
    	$type      = I('type');
    	if(!isset($get_order_model[$type])){
    		$this->ajaxReturn(array('status'=>0,'msg'=>'类型错误'));
    	}
    	//获取分页条件
    	$firstRow  = intval($data['firstRow']);
        $listRows  = intval($data['listRows']);
    	//商家id
    	$seller_id = $_SESSION['member_data']['id'];
        $result    = D('Mall_order')->$get_order_model[$type]($seller_id , array($firstRow , $listRows)); 
        $result['status'] = 1;       
        $this->ajaxReturn($result);         
    }

    /* 
     * 获取等待发货的订单
     * */    
    public function getWaitSend(){
    	//商家id
    	$seller_id = $_SESSION['member_data']['id'];
    	//获取分页条件
    	$firstRow  = intval($data['firstRow']);
        $listRows  = intval($data['listRows']);
        $result    = D('Mall_order')->getWaitSend($seller_id , array($firstRow , $listRows)); 
        echo json_encode($result);          
    }
	
	public function assess_manage(){				
		$member_id = $_SESSION['member_data']['id'];
		$this->assign('member_id' , $member_id);
		$this->display();
	}
	
	public function delivery(){				
		$member_id = $_SESSION['member_data']['id'];
		$this->assign('member_id' , $member_id);
		$this->display();
	}
	
	public function sellerIndex(){	    	
		$member_id = $_SESSION['member_data']['id'];
        $nopay = M('Mall_order')->where(array('seller_id'=>$member_id,'pay_status'=>0,'status'=>1))->count();
        $nosend = M('Mall_order')->where(array('seller_id'=>$member_id,'status'=>1,'pay_status'=>1,'send_status'=>0))->count();
		/*
		$todayTime = date('y-m-d',time());		
		$starttime = strtotime($todayTime);
		$endtime = $starttime + 60*60*24;	               	
        $jiaoyiok = M('Mall_order_data')->where(array('seller_id'=>$member_id,'status'=>1,'send_status'=>2,'create_time'=>array('gt',$starttime),'create_time'=>array('lt',$endtime)))->count();				
		*/
		$jiaoyiok = M('Mall_order_data')->where(array('seller_id'=>$member_id,'status'=>1,'send_status'=>2))->count();				
		$prices = M('Mall_order_data')->field('id,seller_id,status,send_status,total_price')->where(array('seller_id'=>$member_id,'status'=>1,'send_status'=>2))->select();				
		foreach( $prices as $vv ){
			$totalprice += $vv['total_price'];
		}		
        $redis = new \Com\Redis();
        /*店铺数据  缓存更新处理*/
        $seller_id = $_SESSION['member_data']['id'];
        $shop_data = $redis->get('shop_data'.$seller_id , 'array');
        $this->assign('shop_data' , $shop_data);
        $visitor_num = M('visitor_count')->where(array('member_id'=>$member_id))->getField('visitor_num');						
		$visitor_ip = M('visitor_count')->where(array('member_id'=>$member_id))->getField('ip_num');						
		$visi_num = intval($visitor_num);
		$shop_status = M('mall_application')->where(array('seller_id'=>$member_id))->getField('check_status');
		//店铺公告
		$goods_check = M('Mall_goods_check')
					->where(array('seller_id'=>$seller_id))
					->order('time desc')
					->select();
		//统计店铺公告数量
		$goods_check_num=M('Mall_goods_check')
						->where(array('seller_id'=>$seller_id))
						->order('time desc')
						->limit(4)
						->count();
		$num=4-$goods_check_num;
		$num = abs($num);

		$news_notice =M('News')
					->where(array('type'=>2,'seo_news'=>0))
					->order('create_time desc')
					->limit($num)
					->select();
		
		$this->assign('num',$goods_check_num);
		$this->assign('news_notice',$news_notice);
		$this->assign('goods_check',$goods_check);
		$this->assign('shop_status' , $shop_status);
		$this->assign('visitor_ip' , $visitor_ip);
		$this->assign('visi_num' , $visi_num);
		$this->assign('nopay' , $nopay);
		$this->assign('totalprice' , $totalprice);
		$this->assign('nosend' , $nosend);
		$this->assign('jiaoyiok' , $jiaoyiok);
		$this->assign('shop_data' , $shop_data);
		$this->assign('member_id' , $member_id);
		$this->display();
	}

	/**
	 * 批量发货
	 */
	public function sendAllGoods(){
    	$order_id = I('id');
    	if(empty($order_id) || !is_array($order_id)){
            exit('清输入正确的订单id');
    	}
    	$order_id  = implode(',' , $order_id);
    	$seller_id = $_SESSION['member_data']['id'];
    	//获取等待发货的订单
    	$condition = array(
    		'a.id'          => array('in' , $order_id),
    		'a.seller_id'   => $seller_id,
    		'a.pay_status'  => 1,
    		'a.send_status' => 0
    	);
    	$field = "a.id,a.address_id,a.order_sn,a.address,
    	b.goods_name,b.goods_thumb,goods_price,b.order_id,b.total_price";
        $temp  = M('Mall_order as a')
            ->join('tp_mall_order_data as b on a.id=b.order_id')
	        ->where($condition)
	        ->field($field)
	        ->order('b.order_id desc,a.address_id')
	        ->select();
	    if(empty($temp)){
	    	exit('订单不存在');
	    }   
	    //按订单进行组装
	    $order = array();
	    $address_id = '';
	    foreach($temp as $v){
	    	if(!isset($order[$v['order_id']])){
                $order[$v['order_id']] = array(
                	'order_id'   => $v['order_id'],
                    'order_sn'   => $v['order_sn'],
                    'address_id' => $v['address_id'],
                    'address'    => $v['address']
                );
                /*按收货地址分组 相同的收货地址 则只在第一个订单前面显示地址*/
                if($address_id != $v['address_id']){
	    		    $address_id = $v['address_id'];
	    		    $order[$v['order_id']]['is_show'] = '1';//是否显示发货运单号输入框
	    	    }else{
	    	    	$order[$v['order_id']]['is_show'] = '0';
	    	    }
	    	}
            $order[$v['order_id']]['data'][] = $v;     
	    }
	    $kd = M('Kuaidi')->select();//快递 信息 
	    $this->assign('order' , $order);
	    $this->assign('kd',$kd);
	    $this->display();
	}	

	/**
	 * 发货
	 */
	public function sendGoods(){
	    if(IS_AJAX){
	        $member_id = $_SESSION['member_data']['id'];//卖家id
	        $data = I('data');

	        $data = json_decode(htmlspecialchars_decode($data) , true);
	        $r    = D('Mall_order')->sendGoods($data , $member_id);
	        $this->ajaxReturn($r);
	    }else{
	    	$order_id = I('id');
	    	if(empty($order_id) || !is_array($order_id)){
	            exit('清输入正确的订单id');
	    	}
	    	$order_id  = implode(',' , $order_id);
	    	$seller_id = $_SESSION['member_data']['id'];
	    	//获取等待发货的订单
	    	$condition = array(
	    		'a.id'          => array('in' , $order_id),
	    		'a.seller_id'   => $seller_id,
	    		'a.pay_status'  => 1,
	    		'a.send_status' => 0
	    	);
	    	$field = "a.id,a.address_id,a.order_sn,a.address,a.create_time,
	    	b.goods_name,b.goods_thumb,goods_price,b.order_id,b.total_price";
	        $order = M('Mall_order as a')
	            ->join('tp_mall_order_data as b on a.id=b.order_id')
		        ->where($condition)
		        ->field($field)
		        ->order('b.order_id desc,a.address_id')
		        ->select();
		    if(empty($order)){
		    	exit('订单不存在');
		    }
		    //查询快递公司
		    $kd = M('Kuaidi')->select();
		    $this->assign('kd',$kd);     
		    $this->assign('order' , $order);
		    $this->display();	    	
	    }
	}
}