<?php
/*
 * 商家退货 退款 换货处理
 * */
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
use Org\Msg\SendMsg;
header("content-type:text/html;charset=utf-8");
class SellerRefundController extends Controller {    
    public function _initialize(){        	
    	$seller_id = $_SESSION['member_data']['id'];
    	$condition = array(
            'seller_id' => $seller_id
    	);            
        $limit     = array(
            0,
            10
        );
        $order  = 'id desc';  

       	    $model  = M('Refund_goods');
            !empty($condition) && ($model = $model->where($condition));
            $model  = $model->limit($limit[0] , $limit[1]);
            !empty($order)     && ($model = $model->order($order));
            $data   = $model->select(); 
   	      //  dump($data);

     

        if(empty($_SESSION['member_data'])){
            header("Location:http://www.orangesha.com/login.html");
        }
        $id    = $_SESSION['member_data']['id'];  
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

    /*
     * 获取售后申请列表
     * */    
    public function getRefundList(){
        if(IS_AJAX){
        	$data      = I();       	
        	$seller_id = $_SESSION['member_data']['id'];

        	$condition = array(
                'seller_id' => $seller_id
        	);            
            $limit     = array(
                intval($data['firstRow']),
                intval($data['listRows']) == 0 ? 10 : intval($data['listRows'])
            );
            $order  = 'id desc';  


       	    $model  = M('Refund_goods');
            !empty($condition) && ($model = $model->where($condition));
            $model  = $model->limit($limit[0] , $limit[1]);
            !empty($order)     && ($model = $model->order($order));
            $data   = $model->select();  
            $count  = $model->where($condition)->count();   
	        $this->ajaxReturn(array(
	        	'status'    => 1,
	        	'msg'       => 'ok',
	            'data'      => $data,
	            'totalRows' => $count
	        ));  
        }else{
        	$this->display();
        }
    }

    /*
     * 获取售后申请详情
     * */    
    public function refundDetail(){
    	$seller_id = $_SESSION['member_data']['id'];  
    	$id = intval(I('id'));
    	if($id == 0){
            exit('id错误');
    	}
        $refund_goods = M('Refund_goods')
	        ->where(array('id'=>$id,'seller_id'=>$seller_id))
	        ->order('id desc')
	        ->find();
	    if(empty($refund_goods)){
            exit('售后单不存在');
	    }
	    $where = array('id'=>$refund_goods['order_data_id']);
	    $order_data = M('Mall_order_data')
            ->where($where)
            ->field('goods_name,goods_price')
            ->find(); 
	    $because = C('REFUND_BECAUSE');  
        $because = $because[$refund_goods['type']];    
	    $this->assign('refund_goods' , $refund_goods); 
	    $this->assign('refund_goods_json' , json_encode($refund_goods)); 
	    $this->assign('order_data_json' , json_encode($order_data)); 
	    $this->assign('order_data' , $order_data); 
        $this->display();
    }

    /*
     *商家审核售后
     * */
    public function checkRefund(){
        if(IS_AJAX){
            $member_id = $_SESSION['member_data']['id'];  
            $id        = intval(I('id'));
            $status    = intval(I('status'));
            $refund    = M('Refund_goods')
                       ->where(array('id'=>$id))
                       ->find();   
            if(empty($refund)){
                $this->ajaxReturn(array('status'=>0,'msg'=>'该售后单不存在'));
            }
            if($refund['status'] == $status){
                $this->ajaxReturn(array('status'=>0,'msg'=>'该售后单已经审核'));
            }
            if($refund['status'] != 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'该售后单状态无法审核'));
            }
            if(in_array(1 , array(-1,2))){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'msg'    => '售后申请单状态不正确'
                ));
            }      
            /*退款申请审核*/   
            if($refund['type'] == 1){
                $result = D('RefundGoods')->checkRefundCase($status , $refund);
            }
            /*换货申请审核*/
            if($refund['type'] == 2){
                $result = D('RefundGoods')->checkRefundGoods($status , $refund);
            }
            /*退款退货申请审核*/   
            if($refund['type'] == 3){
                $result = D('RefundGoods')->checkRefundCaseGoods($status , $refund);
            }
            $this->ajaxReturn($result);            
        }    
    }

    /*
     * 商家确认退款
     * */
    public function refundCase(){
        if(IS_AJAX){
            $member_id = $_SESSION['member_data']['id'];  
            $id        = intval(I('id'));//售后 退款单id
            $refund    = M('Refund_goods')
                       ->where(array('id'=>$id,'seller_id'=>$member_id))
                       ->find();   
            if(empty($refund)){
                $this->ajaxReturn(array('status'=>0,'msg'=>'该售后单不存在'));
            }
            if($refund['status'] != 1 || $refund['type'] != 1){
                $this->ajaxReturn(array('status'=>0,'msg'=>'该售后单状态无法退款'));
            }         
            //生成退款单
            $r = D('RefundCase')->refundCaseAdd($refund);        
        }    
    }
}