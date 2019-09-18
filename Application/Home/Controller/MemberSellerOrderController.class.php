<?php
/* *
 * 买家订单管理
 */
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class MemberSellerOrderController extends Controller {
    public function _initialize(){       
        if(empty($_SESSION['member_data'])){
            header("Location:http://www.orangesha.com/login.html");
        }
        $id = $_SESSION['member_data']['id'];  
        $redis = new \Com\Redis();       
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
        $this->assign('help' , get_child($help));
        $this->assign('order_total' , $_SESSION['order_total']);
        $this->assign('cart_total' , $_SESSION['cart_total']);
    }

    /* 
     * 订单列表
     * */
    public function orderList(){
        $this->display();      
    }
    
    /*获取共享订单*/
    public function getOrderList(){
        if(IS_AJAX){
           $data      = I();
       	   $seller_id = $_SESSION['member_data']['id'];
       	   /*查询条件*/
       	   $where['seller_id'] = $seller_id;
       	   $where['status']    = array('neq' , 0);
       	   if(I('order_sn')){
       	       $where['order_sn'] = I('order_sn');
       	   }
       	   if(I('status') === 0 || I('status')){
       	       $where['status'] = intval(I('status'));
       	   }
       	   if(I('send_status') === 0 || I('send_status')){
       	       $where['send_status'] = intval(I('send_status'));
       	   }
       	   $start_time = '1980-02-02';
       	   $end_time   = '2222-02-02';
       	   if(I('start_time')){
       	       $start_time = I('start_time');
       	   }
       	   if(I('end_time')){
       	       $end_time = I('end_time');
       	   }
       	   $where['time']   = array(between,array($start_time , $end_time));
       	   
		   $firstRow  = $data['firstRow'];
	   	   $listRows  = $data['listRows'];
	   	   $order_    = M('Order')->where($where)->select();
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
	       $count  = M('Order_data')->where($where)->count();
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
     * 卖家发货
     * */
    public function sendGoods(){
        if(IS_AJAX){
            $id = I('id');
            $r  = D('Order')->sendGoods();
            $this->ajaxReturn($r);
        }
    }
}