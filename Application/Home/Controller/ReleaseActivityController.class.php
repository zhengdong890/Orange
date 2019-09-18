<?php
/*
 * 卖家中心 营销活动
 * */  
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class ReleaseActivityController extends Controller {	
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
     * 活动列表
     * */    
    public function activityList(){
       if(IS_POST && IS_AJAX){
            $data      = I();
            $firstRow  = isset($data['firstRow'])?intval($data['firstRow']) : 0;
            $listRows  = isset($data['listRows'])?intval($data['listRows']) : 10;        
            $seller_id = $_SESSION['member_data']['id'];
            $result    = D('ReleaseActivity')->getActivityList($seller_id , array($firstRow,$listRows));
            $this->ajaxReturn($result);
       }        
    }

    /*
     * 添加活动
     * */
    public function activityAdd(){
       if(IS_POST && IS_AJAX){
            $data   = I();
            $data['seller_id'] = $_SESSION['member_data']['id'];
            $r = D('ReleaseActivity')->checkActivity($data);
            if($r['status'] == 0){
                $this->ajaxReturn($r);
            }
            $result = D('ReleaseActivity')->activityAdd($data);
            $this->ajaxReturn($result);
       }
    }
    
        
    /*
     * 修改活动
     * */
    public function activityUpdate(){
        if(IS_AJAX){
            $data      = I();
            dump($data);
            die;
            $seller_id = $_SESSION['member_data']['id'];
            $r = D('ReleaseActivity')->checkActivity($data);
            if($r['status'] == 0){
                $this->ajaxReturn($r);
            }
            $result = D('ReleaseActivity')->activityUpdate($data , $seller_id);
            $this->ajaxReturn($result);
        }
    }

    /*
     * 删除营销活动
     * */
    public function activityDelete(){
        if(IS_AJAX){
            $id     = intval(I('id'));
            if(!$id){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'msg'    => '请输入优惠券id'
                ));die;
            }
            $result = D('ReleaseActivity')->activityDelete($id , $_SESSION['member_data']['id']);
            $this->ajaxReturn($result);
        }
    }
    //营销活动暂停
    public function pause(){
        if(IS_AJAX){
            $data = I();
            if(empty($data['status'])){
                $this->ajaxReturn(array(
                        'status'=>0,
                        'msg'   =>'该状态不存在'
                    ));die;
            }
            if(empty($data['id'])){
                $this->ajaxReturn(array(
                        'status'=>0,
                        'msg'   =>'该活动id不存在'
                    ));die;
            }
            $result = D('ReleaseActivity')
                        ->pause($data['id'],$data['status'],$_SESSION['member_data']['id']);
            $this->ajaxReturn($result);

        }
    }
}