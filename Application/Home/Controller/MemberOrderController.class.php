<?php
/* *
 * 买家订单管理
 */
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class MemberOrderController extends Controller {
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
    	$type = I('type')? I('type') : 1;
    	$this->assign('type',$type);
        $this->display();      
    }

    /* 
     * 获取共享订单
     * */
    public function getOrderData(){
        if(IS_AJAX){
            $data     = I();
            $where    = array(
                'member_id' => $_SESSION['member_data']['id'],
                'status'    => array('neq' , 0)
            );
            if(isset($data['pay_status'])){
                $where['pay_status'] = intval($data['pay_status']);
            }
            if(isset($data['send_status'])){
                $where['send_status'] = intval($data['send_status']);
            }  
            if(isset($data['is_comment'])){
                $where['is_comment'] = intval($data['is_comment']);
            }
            if(isset($data['status'])){
                $where['status'] = intval($data['status']);
            }
            $firstRow = $data['firstRow'];
            $listRows = $data['listRows'];               
            /*获取订单信息*/
            $order_ = M('Order')
                    ->where($where)
                    ->limit($firstRow,$listRows)
                    ->field('id,order_sn,time,pay_status,is_comment,status,send_status')                
                    ->select();
            $count = M('Order')
                   ->where($where)
                   ->count();         
  	        foreach($order_ as $k => $v){
                $order[$v['id']] = $v;
                $order_ids[] = $v['id'];
  	        }
            /*获取订单详细信息*/
            $order_ids = implode(',' , $order_ids);
            if($order_ids){
               $order_data_ = M('Order_data')
                            ->where(array('order_id'=>array('in' , $order_ids)))
                            ->select();
               foreach($order_data_ as $k => $v){
                   $order_data[$v['order_id']][] = $v;
               }
            }          
            $result = array(
                'data'      => array($order,$order_data),
                'totalRows' => $count
            );  
            echo json_encode($result);
        }	
    }
/*[Fri Mar 03 15:25:30.671399 2017] [:error] [pid 30270] [client 119.139.196.98:62257] PHP Fatal error:  Allowed memory size of 134217728 bytes exhausted (tried to allocate 20844 bytes) in /data/wwwroot/default/ThinkPHP/Library/Think/Image/Driver/Gd.class.php on line 70, referer: http://www.orangesha.com/index.php/Home/Member_goods/goodsAdd/cat_id/176*/
    /* 
     * 获取订单详情
     * */
    public function orderDetail(){
        $member_id  = $_SESSION['member_data']['id'];
        $id         = I('order_id');
        $field =  'o.id,o.order_sn,o.air_way_code,o.shop_coupon_price,o.shipping_price,o.create_time,o.pay_status,o.address,o.name,o.content,o.total_price,b.goods_price,b.goods_thumb,b.order_id,b.comment_status,b.send_status,b.goods_name,b.number,b.sku_id,k.term,b.status,b.total_price as goods_total_price';

            $order_data = M('Mall_order as o')
                        ->join("tp_mall_order_data as b ON b.order_id =o.id")
                        ->join("tp_sku as k ON b.sku_id=k.sku_id")
                        ->where(array('o.id'=>$id,'o.member_id'=>$member_id))
                        ->field($field)
                        ->select();
        $this->assign('type',$type);           
 
      //计算总金额
      $total_price=0;
      foreach($order_data as $v){
            $total_price = $total_price+($v['goods_total_price']*$v['number']);
        }
    //计算运费金额
        $shipping_price=0;
        foreach($order_data as $v){
            $shipping_price = $shipping_price+($v['shipping_price']);
        }
    //计算优惠价格
        $shop_coupon_price=0;
        foreach($order_data as $v){
            $shop_coupon_price = $shop_coupon_price+$v['shop_coupon_price'];
        }

        $this->assign('shipping_price',$shipping_price);
        $this->assign('shop_coupon_price',$shop_coupon_price);
        $this->assign('total_price',$total_price);
        $this->assign('fu_price',$fu_price);

        $this->assign('order_data' , $order_data);
        $this->display();
    }

    /* 
     * 获取商城订单
     * */
    public function getMallOrderData(){
        if(IS_AJAX){
            $data     = I();
            $where    = array(
                'member_id' => $_SESSION['member_data']['id'],
                'status'    => array('neq' , 0)
            );
            if(isset($data['pay_status'])){
                $where['pay_status'] = intval($data['pay_status']);
            }
            if(isset($data['send_status'])){
                $where['send_status'] = intval($data['send_status']);
            }  
            if(isset($data['is_comment'])){
                $where['comment_status'] = intval($data['is_comment']);
            }
            if(isset($data['status'])){
                $where['status'] = intval($data['status']);
            }
            $firstRow = $data['firstRow'];
            $listRows = $data['listRows'];               
            /*获取订单信息*/
            $order_ = M('Mall_order')
                    ->where($where)
                    ->limit($firstRow,$listRows)
                    ->field('id,order_sn,create_time,pay_status,seller_id,comment_status,shipping_price,status,send_status')
                    ->order('id desc')                
                   ->select();
            foreach($order_ as $k=>$v){
                $order_[$k]['shop']=M('Shop_data')->where(array('member_id'=>$v['seller_id']))->field('shop_name')->find();
            }
            $count = M('Mall_Order')
                   ->where($where)
                   ->count();         
            foreach($order_ as $k => $v){
                $order[$v['id']] = $v;
                $order_ids[] = $v['id'];
            }
            /*获取订单详细信息*/
            $order_ids = implode(',' , $order_ids);
            if($order_ids){
               $order_data_ = M('Mall_order_data')
                            ->where(array('order_id'=>array('in' , $order_ids)))
                            ->order('id desc')
                            ->select();
               foreach($order_data_ as $k => $v){
                   $order_data[$v['order_id']][] = $v;
               }
            }          
            $result = array(
                'data'      => array($order,$order_data),
                'totalRows' => $count
            );  
            echo json_encode($result);
        }	
    } 

    public function orderRefund(){
        //echo "这是退货方法...";
        $member_id  = $_SESSION['member_data']['id'];
        $member_name =$_SESSION['member_data']['username'];
        //dump($member_id);
        $order_sn = I('order_sn');
        if ($order_sn=="") {
            exit('订单号不存在');
        }
        $type = I('type');
        $this->assign('type',$type);
        //dump($order_sn);
        //商城订单查询
        $order = M('Mall_order')->where(array('order_sn'=>"$order_sn"))->where(array('member_id'=>$member_id))->select();
       // dump($order);
        $name=$order[0]['name'];
        $status=$order[0]['status'];
        $this->assign('status',$status);
        $pay_status=$order[0]['pay_status'];
        $this->assign('pay_status',$pay_status);
        $send_status=$order[0]['send_status'];
        $this->assign('send_status',$send_status);
        $this->assign('name',$name);
        $order_id = $order[0]['id'];
       // dump($order_id);
        $this->assign('order_id',$order_id);
        //s商城订单详情查询
        $order_data = M('Mall_order_data')->where(array('order_id'=>$order_id))->select();
        //dump($order_data);
        $goods_id = $order_data[0]['goods_id'];

         if($order_data[0]['status'] == 2 || $order_data[0]['pay_status'] != 1){
            exit('该订单状态不允许此操作');
        }

        $time = $order_data[0]['create_time'];
        $this->assign('time',$time);
        $goods_thumb=$order_data[0]['goods_thumb'];
        $this->assign('goods_thumb',$goods_thumb);

       //商城商品查询
        $goods= M('Mall_goods')->where(array('goods_id'=>$goods_id))->find();
        $member = $goods['member_id'];
        $describe = $goods['goods_describe'];
        $this->assign('describe',$describe);
      
        //店铺查询
        $shop=M('Shop_data')->where(array('member_id'=>$member))->find();
        //dump($shop);
        $shop_name=$shop['shop_name'];
        $this->assign('shop_name',$shop_name);
        $goods_name = $order_data[0]['goods_name'];
        $goods_price = $order_data[0]['goods_price'];
        $total_price = $order_data[0]['total_price'];
        $number = $order_data[0]['number'];
        $this->assign('goods_name',$goods_name);
        $this->assign('goods_price',$goods_price);
        $this->assign('total_price',$total_price);
        $this->assign('order_sn',$order_sn);
        $this->assign('number',$number);
        $this->assign('number_name',$number_name);
        $this->display();
    }

    //个人中心订单删除
    public function orderDel(){
        if(IS_AJAX){
            $data = I();
            $mall_order = M('Mall_order')->where(array('id'=>$data['order_id']))->delete();
            $mall_order_data = M('mall_order_data')->where(array('order_id'=>$data['order_id']))->delete();
            if($mall_order && $mall_order_data){
                $this->ajaxReturn(array('status'=>1,'msg'=>'删除成功'));
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'删除失败'));
            }
        }
    }

    /**
     *获取订单支付状态
     */
    public function getOrderPayStatus(){
        if(IS_AJAX && IS_POST){
            $member_id  = $_SESSION['member_data']['id'];
            $id = intval(I('id'));
            $status = M('Mall_order')
                ->where(array('id'=>$id,'member_id'=>$member_id))
                ->getField('pay_status');
            if($status === false || !isset($status)){
                $this->ajaxReturn(array('status'=>0,'msg'=>'订单不存在'));
            }
            $this->ajaxReturn(array('status'=>1,'msg'=>'success','pay_status'=>$status));
        }
    }
}