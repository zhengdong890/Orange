<?php
/*
 * 买家退货 退款 换货处理
 * */
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
use Org\Msg\SendMsg;
header("content-type:text/html;charset=utf-8");
class MemberRefundController extends Controller {    
    public function _initialize(){   
        if(empty($_SESSION['member_data'])){
            if(IS_AJAX || IS_POST){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'msg'   => '请登录'
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
    
    /*
     * 退货 退款 换货 申请页面
     * */
    public function refund(){
        $order_data_id = intval(I('id'));
        $member_id     = $_SESSION['member_data']['id'];
        if($order_id === 0){
            exit('请输入正确的订单号');
        }
        $where    = array(
            'id'        => $order_data_id,
            'member_id' => $member_id
        );
        $order_data = M('Mall_order_data')
            ->where($where)
            ->field('id,order_id,seller_id,goods_id,status,pay_status,service_status,send_status,goods_price,number,goods_thumb,total_price')
            ->find();
        /*当前商品订单 是否申请了售后*/
        if($order_data['service_status'] == 0){//未申请
        	$refund_type = array(
                '1' => '仅退款',
                '2' => '换货',
                '3' => '退货退款'
        	);
	        //根据商品订单获取 商品订单的售后类型
	        $refund_type_ = D('RefundGoods')->getRefundTypeByOrder($order_data); 
	        $refund_type_ = array_flip($refund_type_);
	        $refund_type  = array_intersect_key($refund_type , $refund_type_);
	        $this->assign('refund_type' , $refund_type);           
        }else{
        	/*申请了 获取当前申请的信息*/
            $refund_goods = M('Refund_goods')
                ->where(array('order_data_id'=>$order_data_id,'member_id'=>$member_id))
                ->order('id desc')
                ->find();   
            $because = C('REFUND_BECAUSE');  
            $because = $because[$refund_goods['type']];         
            $this->assign('refund_goods' , $refund_goods); 
            $this->assign('because' , $because);         
        }   
        $redis = new \Com\Redis();     
        //获取商家店铺信息
        Hook::add('getShopData','Shop\\Addons\\SellerAddon');
        Hook::listen('getShopData',$member_id);
        $shop_data = $redis->get('shop_data'.$member_id , 'array');  
        //获取订单信息
        $order = M('Mall_order')->where(array('id'=>$order_data['order_id']))->find();
        $this->assign('order' , $order);
        $this->assign('order_data' , $order_data);
        $this->assign('shop_data' , $shop_data);
        $this->display();
    }

    /*
     * 退货 退款 换货申请
     * */
    public function refundApplication(){
        if(IS_AJAX){
            $data     = I();
            $order_id = intval($data['id']);
            if($order_id === 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请输入正确的订单号'));
            }
            //检测 提交数据的合法性
            $r = D('RefundGoods')->checkRefund($data);
            if($r['status'] == 0){
                $this->ajaxReturn($r);
            }
            //检测 订单状态 是否支持此操作           
            $where    = array(
                'id'        => $order_id,
                'member_id' => $_SESSION['member_data']['id']
            );
            $order_data = M('Mall_order_data')
                        ->where($where)
                        ->field('id,order_id,seller_id,goods_id,status,pay_status,total_price')
                        ->find();
            //检测 提交的 售后是否满足条件
            $r = D('RefundGoods')->isFulfilCondition($type , $order_data); 
            if($r['status'] == 0){
                $this->ajaxReturn($r);
            }   
               
            //上传图片
            $upload           = new \Think\Upload();// 实例化上传类
            $upload->maxSize  = 3145728 ;// 设置附件上传大小
            $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
            // 上传文件
            $info = $upload->upload();
            if($info) {        
                $data['thumb'] = $upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];
            }   
            $order = M('Mall_order')
                   ->where(array('id'=>$order_data['order_id']))
                   ->field('order_sn,total_price,name')
                   ->find();  
            $data = array_merge($data , array(
                'member_id'     => $_SESSION['member_data']['id'],
                'seller_id'     => $order_data['seller_id'],
                'order_id'      => $order_data['order_id'],
                'order_data_id' => $order_data['id'],
                'goods_id'      => $order_data['goods_id'],
                'order_sn'      => $order['order_sn'],
                'order_price'   => $order['total_price'],
                'buy_name'      => $order['name'],
                'case'          => $order_data['total_price']
            )); 
            $type_func = array('1'=>'refundCase','2'=>'refundCaseGoods','3'=>'refundGoods_'); 
            $r = D('RefundGoods')->$type_func[$data['type']]($data); 
            $this->ajaxReturn($r);
        }
    }

    /*
     * 撤销 售后申请
     * */
    public function revoke(){
        if(IS_AJAX){
        	$member_id = $_SESSION['member_data']['id'];
            $id        = intval(I('refund_id'));
            if($id == 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'售后单id错误'));
            }
            $refund = M('Refund_goods')
                ->where(array('id'=>$id,'member_id'=>$member_id))
                ->find();
            if(empty($refund)){
                $this->ajaxReturn(array('status'=>0,'msg'=>'售后单不存在'));
            }
            if($refund['status'] == 2){
                 $this->ajaxReturn(array('status'=>0,'msg'=>'售后单已经完成了'));
            }
            $r = D('RefundGoods')->revoke($refund);
            $this->ajaxReturn($r);
        }
    }
    public function refund_money(){
        //退款中
        $order_data_id = intval(I('id'));//商品详情id
        $r= $this->refund_goods_($order_data_id);
        $this->assign('because',$r['because']);         
        $this->assign('refund_goods' , $r['refund_goods']);
        $this->assign('order_data',$r['order_data']);
        $this->display();
    }
    public function refund_success(){
        //成功退款
        $order_data_id = intval(I('id'));//商品详情id
        $r= $this->refund_goods_($order_data_id);
        $this->assign('because',$r['because']);         
        $this->assign('refund_goods' , $r['refund_goods']);
        $this->assign('order_data',$r['order_data']);
        $time = $r['refund_goods']['create_time']+172800;
        $this->assign('time',$time);
        $this->display();

    }

    private function refund_goods_($order_data_id){
        //退款、退货调用此方法，简介代码
        $member_id =$_SESSION['member_data']['id'];
        $order_data =  M('Mall_order_data as m')
                ->join('tp_mall_order as o ON o.id=m.order_id')
                ->join('tp_shop_data as s ON s.member_id=m.member_id')
                ->where(array('m.id'=>$order_data_id,'m.member_id'=>$member_id))
                ->field('m.id,m.goods_name,m.goods_thumb,s.shop_name,o.tel_num,o.order_sn,m.create_time,m.goods_price,m.number,m.total_price,o.shipping_price')
                ->find();
        //dump($order_data);
        $refund_goods =M('Refund_goods')
                ->where(array('order_data_id'=>$order_data_id))
                ->find();
        $because = C('REFUND_BECAUSE');  
        $because = $because[$refund_goods['type']];
        //return array($order_data,$refund_goods,$because);
        return array(
                'order_data'  =>$order_data,
                'refund_goods'=>$refund_goods,
                'because'     =>$because
            );
    }
    public function refund_goods(){
        //退款退货中...
        $order_data_id = intval(I('id'));//商品详情id
        $r= $this->refund_goods_($order_data_id);
        $this->assign('because',$r['because']);         
        $this->assign('refund_goods' , $r['refund_goods']);
        $this->assign('order_data',$r['order_data']);
        $this->display();
    }
    public function return_goods(){
        //退款退货中...
        $order_data_id = intval(I('id'));//商品详情id
        $r= $this->refund_goods_($order_data_id);
        $this->assign('because',$r['because']);         
        $this->assign('refund_goods' , $r['refund_goods']);
        $this->assign('order_data',$r['order_data']);
        $this->display();
    }

   
}