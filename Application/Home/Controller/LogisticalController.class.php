<?php
/*
 * 物流服务
 * */
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class LogisticalController extends Controller{	 
    public function _initialize(){
        if(empty($_SESSION['member_data'])){
            $this->redirect('Member/login');
        }
        $redis = new \Com\Redis();
        $help = D('HelpCategory')->redisCatName($redis);
        $this->assign('help' , get_child($help));
    }
    
    
    public function logisticsDetails(){
        $id         = I('order_id');
        $member_id  = $_SESSION['member_data']['id'];
        //查询物流公司相关信息
        $mall_order = M('Mall_order')
                ->where(array('id'=>$id))
                ->field('company_code,air_way_code,order_sn')
                ->find();
        $kd =M('Kuaidi')
            ->where(array('code'=>$mall_order['company_code']))
            ->find();

        $air_way_code =$mall_order['air_way_code'];//运单号
        $test=70183103385420;//运单号LogisticCode
        $ship=$kd['code'];//快递公司编码
        $ship ='HTKY';//快递公司编码ShipperCode
        $order_id   =$mall_order['order_sn'];//订单号
        include 'logistics.class.php';//引入接口文件
        //调用查询物流轨迹
        $logisticResult=getOrderTracesByJson($test,$ship,$order_id);
        $arr= json_decode($logisticResult,true);
         //物流状态: 0-无轨迹 2-在途中，3-签收,4-问题件
        $state = $arr['State'];
        $logistics= $arr['Traces'];
        $log = array_reverse($logistics,true);
        //查询快递公司
        $company=M('Kuaidi')->where(array('code'=>$ship))->find();
        $field =  'o.id,o.order_sn,o.pay_status,o.address,o.name,o.content,o.total_price,b.goods_price,b.goods_thumb,b.comment_status,b.send_status,b.number,b.status,b.total_price as goods_total_price';
        $order_data = M('Mall_order as o')
                    ->join("tp_mall_order_data as b on order_id =o.id")
                    ->where(array('o.id'=>$id,'o.member_id'=>$member_id))
                    ->field($field)
                    ->select();            
      
        $this->assign('com',$company);
        $this->assign('state',$state);
        $this->assign('arr',$arr);
        $this->assign('logistics',$log); 
        $this->assign('order_data',$order_data);
        $this->display();
    }   

}