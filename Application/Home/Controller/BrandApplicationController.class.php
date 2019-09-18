<?php
/*
 * 品牌申请模块
 * */
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class BrandApplicationController extends Controller {    
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
	 * 品牌申请
	 * */
    public function application(){
        if(IS_POST){ 
        	$data      = I();
        	$data['member_id'] = $_SESSION['member_data']['id'];       	
        	$r = D('BrandApplication')->checkApplication($data);
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
	        if($info){
	            /*商标注册证*/
	            if($info['trademark_register_img']){        
	                $data['trademark_register_img'] = $upload->rootPath.$info['trademark_register_img']['savepath'].$info['trademark_register_img']['savename'];
	            } 
	            /*受理通知书*/
	            if($info['notice_img']){        
	                $data['notice_img'] = $upload->rootPath.$info['notice_img']['savepath'].$info['notice_img']['savename'];
	            }    	            
	        }else{
		        /*$this->ajaxReturn(array(
		           'status' => 0,
		           'msg'    => $upload->getError()		           
		        ));*/
		    }       	
            $r = D('BrandApplication')->application($data);
            $this->ajaxReturn($r);
        }
    }
}