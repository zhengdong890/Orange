<?php
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class ShippingAddressController extends Controller {
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
     * 地址库
     * */
    public function addressList(){
    	  $this->display();	  	
    }
    
    /*
     *获取地址库
     * */
    public function getAddressList(){
    	if(IS_AJAX){
            $data     = I();
       	    $firstRow = intval($data['firstRow']);
       	    $listRows = intval($data['listRows']);
       	    $data     = D('ShippingAddress')
       	              ->getAddressList($seller_id , $condition = array('seller_id'=>$_SESSION['member_data']['id']) , array($firstRow , $listRows));
            $area_no = array();
            foreach($data['data'] as $v){
                $area_no[] = $v['province'];
                $area_no[] = $v['city'];
            } 
            $area_no = implode(',' , $area_no); 
            $area    = M('Area')
                ->where(array('area_no'=>array('in' , $area_no)))
                ->field("area_no,area_name")
                ->select(); 
            $area = array_column($area , 'area_name' , 'area_no');
            array_walk($data['data'] , function(&$v , $k , $area){
                $v['province_name'] = $area[$v['province']];
                $v['city_name']     = $area[$v['city']]; 

            } , $area);
	        echo json_encode($data); 
    	}
    }

    public function addressAdd(){
    	if(IS_AJAX){
    		$member_id = $_SESSION['member_data']['id'];
    		$data      = I();
    		$result    = D('ShippingAddress')->addressAdd($data , $member_id);
    		$this->ajaxReturn($result);
    	}
    }

    public function addressUpdate(){
    	if(IS_AJAX){
    		$member_id = $_SESSION['member_data']['id'];
    		$data      = I();
    		$result    = D('ShippingAddress')->addressUpdate($data , $member_id);
    		$this->ajaxReturn($result);
    	}
    }
      //删除地址库
  public function delArea(){
   if(IS_AJAX){
        $address_id = I('address_id');
        $result = D("ShippingAddress")->address_del($address_id);
        $this->ajaxReturn($result);
      }
  }
}