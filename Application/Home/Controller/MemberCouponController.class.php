<?php
/*
 * 会员中心 优惠券处理
 * */  
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class MemberCouponController extends Controller {	
	public function _initialize(){
        if(empty($_SESSION['member_data'])){
            header("Location:http://www.orangesha.com/login.html");
        }
        $redis = new \Com\Redis();
        /*底部帮助*/
        Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
        Hook::listen('getFooterHelp');
        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
        $this->assign('help' , get_child($help));
    }

    public function myCoupon(){
        //echo "我的优惠卷...";
        $member_id= $_SESSION['member_data']['id'];
        $coupon = M('Member_shop_coupon as m');

       //全部优惠卷
        $all_coupon = $coupon->where(array('m.member_id'=>$member_id))->count();
       //已用优惠卷
            $is_use_coupon = $coupon->where(array('m.status'=>2,'m.member_id'=>$member_id))->count();
           
        //可用优惠卷 
            $map=array();
            $time1 = date('Y-m-d H:i:s');
            $map['m.end_time']=array('gt',$time1);
            $use_coupon = $coupon
                ->where(array('m.status'=>1,'m.member_id'=>$member_id))
                ->where($map)
                ->count();
        //过期优惠券
            $time1 = date('Y-m-d H:i:s');
            $mapp['m.end_time']=array('lt',$time1);
            $old_coupon =$coupon
                        ->where(array('member_id'=>$member_id))
                        ->where($mapp)
                        ->count();
            $this->assign('use_coupon',$use_coupon);
            $this->assign('is_use_coupon',$is_use_coupon);
            $this->assign('old_coupon',$old_coupon);
       //dump($member_id);
       //查询个人优惠卷
       if(I('use_')){
            //可用优惠卷
            $map=array();
            $time1 = date('Y-m-d H:i:s');
            $map['m.end_time']=array('gt',$time1);
            $where=array('m.member_id'=>$member_id,'m.status'=>1);
            $coupons = $this->card_bag_data($map,$where);
            $use = I('use_');
            $this->assign('use',$use);
        }elseif(I('is_use')){
            //已用优惠卷
            $where=array('m.member_id'=>$member_id,'m.status'=>2);
            $map=array();
            $coupons = D('ShopCoupons')->card_bag($map,$where);
            $use = I('is_use');
                    $this->assign('is_use',$use);

        }elseif(I('time')){

            //过期优惠券
            $time1 = date('Y-m-d H:i:s');
            $map['m.end_time']=array('lt',$time1);
            $where=array('m.member_id'=>$member_id);
            $coupons = $this->card_bag_data($map,$where);
            $use = I('time');
            $this->assign('time',$use);

        }else{
            //$time1 = date('Y-m-d H:i:s');
           // $map['m.end_time']=array('lt',$time1);
            $where=array('m.member_id'=>$member_id);
            $coupons = $this->card_bag_data($map=array(),$where);
        }
        
        $this->assign('date',$date);
        $this->assign('all_coupon',$all_coupon);
        $this->assign('mem_coupon',$coupons);
        $this->display();
    }
    public function card_bag_data($map=array(),$where){
        $coupon = M('Member_shop_coupon as m')
                ->join('tp_shop_data as s ON s.member_id=m.seller_id')
                ->field('m.id,m.coupon_name,m.max,m.benefit_price,m.status,m.start_time,m.end_time,s.shop_name,s.thumb')
                ->where($where)
                ->where($map)
                ->order('m.start_time desc')
                ->select();
        return $coupon;
    }
    //删除优惠卷
    public function coupon_del(){
       if(IS_AJAX){
            $data = I();
            $id = M('Member_shop_coupon')->where(array('id'=>$data['id']))->delete();
            if($id){
                $this->ajaxReturn(array('status'=>1,'msg'=>'删除成功'));
            }

         }

    }
    public function addcoupon(){
        //个人优惠卷添加
        if(IS_AJAX){
            $data = I();
            $data['member_id']=$_SESSION['member_data']['id'];
            $result = D('MemberCoupon')->couponAdd($data);
            $this->ajaxReturn($result);
        }

    }
  
}