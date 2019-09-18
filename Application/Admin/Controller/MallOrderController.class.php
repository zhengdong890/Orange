<?php
/*
 * 商城商品订单
 * */
namespace Admin\Controller;
use Com\Auth;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class MallOrderController extends CommonController{
   /**
    * 订单详情页
    */
    public function orderDetail(){
    	$id = I('order_id');
    	if(empty($id)){
            exit('请输入正确的id');
    	}
    	$data = D('MallOrder')->getOrderDataById($id);
    	if(empty($data)){
            exit('订单不存在');
    	}  	
    	/*获取买家店铺信息*/
    	$shop_data = D('ShopData')->getShopDataByMemberId($data['order']['seller_id']);
    	$this->assign('data' , $data);
    	$this->assign('shop_data' , $shop_data[0]);
    	$this->display();
    }   

/**************************************所有商城订单**************************************************/    
    public function orderList(){
        $this->display();
    } 

	/*
	 * 获取所有订单列表
	 * */	   
    public function getOrderList(){
	    if(IS_AJAX){
	    	$data = I();
	        $start_time = strtotime('1980-02-02');
	        $end_time   = strtotime('2222-02-02');	        
            $data['order_sn']    && ($condition['order_sn']    = $data['order_sn']);
            $data['status']      && ($condition['status']      = $data['status']);
            $data['pay_status']  && ($condition['pay_status']  = $data['pay_status']);
            $data['send_status'] && ($condition['send_status'] = $data['send_status']);
            $data['start_time']  && ($start_time = $data['tart_time']);
            $data['end_time']    && ($end_time   = $data['end_time']);            
	        $condition['create_time']   = array('between' , array($start_time , $end_time));
	        $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
	        $listRows = intval(I('listRows'))?intval(I('listRows')):10;
	        $data = D('MallOrder')->orderDataPageList($condition , array($firstRow , $listRows));
	        $this->ajaxReturn($data);
	    }else{
	    	$this->display();
	    }    
    }

/**************************************公对公转账************************************************/
	
	/*
	 * 获取公对公转账 订单
	 * */	   
    public function publicOrderList(){
	    if(IS_AJAX){
	    	$data = I();
	        $condition = array(
                'pay_model' => 4
	        );
	        $start_time = strtotime('1980-02-02');
	        $end_time   = strtotime('2222-02-02');	        
            $data['order_sn']    && ($condition['order_sn']    = $data['order_sn']);
            $data['status']      && ($condition['status']      = $data['status']);
            $data['send_status'] && ($condition['send_status'] = $data['send_status']);
            $data['start_time']  && ($start_time = $data['tart_time']);
            $data['end_time']    && ($end_time   = $data['end_time']);            
	        $condition['create_time']   = array('between' , array($start_time , $end_time));
	        $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
	        $listRows = intval(I('listRows'))?intval(I('listRows')):10;
	        $data = D('MallOrder')->orderDataPageList($condition , array($firstRow , $listRows));
	        $this->ajaxReturn($data);
	    }else{
	    	$this->display();
	    }    
    }

	/*
	 * 设置订单状态为已支付
	 * */	    
    public function setOrderPayStatus(){
        if(IS_AJAX){
        	$id = intval(I('order_id'));
        	if($id === 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'id不能为空'));
        	}
        	$pay_status = 1;
        	$result = D('MallOrder')->setOrderPayStatus($id , $pay_status);
        	$this->ajaxReturn($result);
        }
    }

/**************************************商城订单审核**************************************************/	

	/*
	 * 订单审核列表
	 * */
	public function checkList(){
	    $this->display();
	}
	 
	/*获取共享审核订单*/
	public function getCheckList(){
	    if(IS_AJAX){
	        $where['status'] = 0;
	        $start_time = '1980-02-02';
	        $end_time   = '2222-02-02';
	        if(I('start_time')){
	            $start_time = I('start_time');
	        }
	        if(I('end_time')){
	            $end_time = I('end_time');
	        }
	        $where['time']   = array(between,array($start_time , $end_time));
	        $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
	        $listRows = intval(I('listRows'))?intval(I('listRows')):10;
	        $list     = M('Mall_order')->order('id desc')->limit($firstRow,$listRows)->where($where)->select();
	        foreach($list as $v){
	            $ids[] = $v['seller_id'];
	        }
	        $ids = implode(',' , array_unique($ids));
	        /*获取卖家信息*/
	        $seller_data_  = M('Member_data')
	        ->where(array('member_id'=>array('in' , $ids)))->field("member_id,telnum,nickname,name")
	        ->select();
	        foreach($seller_data_ as $k => $v){
	            $seller_data[$v['member_id']] = $v;
	        }
	        foreach($list as $k => $v){
	            $list[$k]['seller_data'] = $seller_data[$v['seller_id']];
	        }
	        $this->ajaxReturn(array('data'=>$list,'total'=>M('Order')->where($where)->count()));
	    }
	}
	 
	/*
	 * 审核订单
	 * */
	public function orderCheck(){
	    if(IS_POST){
	        $data   = I();
	        $result = D('MallOrder')->orderCheck($data);
	        $this->ajaxReturn($result);
	    }
	}	
	
	public function changePayState(){
	    if(IS_POST){
	        $id     = intval(I('id'));
	        $status = I('pay_status') == 1 ? 1 : 0;
	        if(!$id){
	            $this->ajaxReturn(array('status'=>0,'msg'=>'请输入订单id'));
	        }
	        $r  = M('Mall_order')->where(array('id'=>$id))->save(array('pay_status'=>$status));
	        if($r !== false){
	            M('Mall_order_data')->where(array('order_id'=>$id))->save(array('pay_status'=>$status));
	            $this->ajaxReturn(array('status'=>1,'msg'=>'操作成功'));
	        }else{
	            $this->ajaxReturn(array('status'=>1,'msg'=>'操作失败'));
	        }
	    }	    
	}
}