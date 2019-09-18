<?php
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
use Org\Msg\SendMsg;
header("content-type:text/html;charset=utf-8");
class MemberAddressController extends Controller {    
    public function _initialize(){       
        if(empty($_SESSION['member_data'])){
            header("Location:http://www.orangesha.com/login.html");
        }
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

    /* 
     * 会员地址管理
     * */
    public function memberAddress(){
    	if(IS_AJAX){//添加地址
            $data   = I();
            $data['member_id'] = $_SESSION['member_data']['id'];
            $r      = D('MemberAddress')->checkAddress($data);
            if($r['status'] == 0){
                $this->ajaxReturn($r);	
            }
            $result = D('MemberAddress')->addressAdd($data);
            $this->ajaxReturn($result);
    	}else{
	    	session_start();
	    	$member_id = $_SESSION['member_data']['id'];
	    	$address   = M('Member_address')->where(array('member_id'=>$member_id))->select();
	    	$this->assign('address',$address);
            $this->assign('address_json',json_encode($address));
	    	$this->display();    		
    	}
    }

    /* 
     * 会员地址修改
     * */
    public function addressUpdate(){
    	if(IS_AJAX){//添加地址
    		$data = I();
    		$r    = D('MemberAddress')->checkAddress($data , 2);
            if($r['status'] == 0){
                $this->ajaxReturn($r);	
            }
            $member_id = $_SESSION['member_data']['id'];
            $result    = D('MemberAddress')->addressUpdate($data , $member_id);
            $this->ajaxReturn($result);
    	}else{
    		$id = I('id');
	    	$member_id = $_SESSION['member_data']['id'];
	    	$address   = M('Member_address')->where(array('member_id'=>$member_id))->select();
	    	$data = M('Member_address')->where(array('id'=>$id,'member_id'=>$member_id))->find();
	    	$this->assign('data',$data);
	    	$this->assign('address',$address);
	    	$this->display();    		
    	}
    }

    /* 
     * ajax删除地址
     * */    
    public function addressDelete(){
        if(IS_AJAX){
        	$address_id = I('id');
        	$result     = D("MemberAddress")->addressDelete($address_id);
        	$this->ajaxReturn($result);
        }
    }
    
    /* 
     * ajax设置默认收货地址
     * */    
    public function addressUse(){
        if(IS_AJAX){
        	$address_id = I('id');
	    	$member_id  = $_SESSION['member_data']['id'];
        	$result     = D("MemberAddress")->addressUse($address_id,$member_id);
        	$this->ajaxReturn($result);
        }
    }
}