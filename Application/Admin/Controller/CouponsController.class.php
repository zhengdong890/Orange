<?php
/*
 * 平台优惠券
 * */
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class CouponsController extends CommonController{	
    public function couponList(){
        if(IS_AJAX && IS_POST){
            $data = I();
            $start_time = '1980-02-02';
            $end_time   = '2222-02-02';  
            $data['name']        && ($condition['name'] = array('like',"%{$data['name']}%"));        
            $data['start_time']  && ($start_time = $data['tart_time']);
            $data['end_time']    && ($end_time   = $data['end_time']);            
            $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
            $listRows = intval(I('listRows'))?intval(I('listRows')):10;
            $condition['start_time'] = array('elt',$end_time);
            $condition['end_time']   = array('egt',$start_time);
            $data = D('Coupons')->couponDataPageList($condition , array($firstRow , $listRows));
            $this->ajaxReturn($data);
        }else{
            $this->display();
        }
    } 

	/*
	 * 添加平台优惠券
	 * */	
    public function couponAdd(){
        if(IS_AJAX && IS_POST){
            $data = I();
            $r = D('Coupons')->checkCoupon($data);
            if(!$r['status']){
                $this->ajaxReturn($r);
            } 
            $r = D('Coupons')->couponAdd($data);
            $this->ajaxReturn($r);
        }else{
        	$this->display();
        }
    }

	/*
	 * 修改平台优惠券
	 * */	
    public function couponUpdate(){
        if(IS_AJAX && IS_POST){
            $data = I();
            $r = D('Coupons')->checkCoupon($data , 2);
            if(!$r['status']){
                $this->ajaxReturn($r);
            } 
            $r = D('Coupons')->couponUpdate($data);
            $this->ajaxReturn($r);             
        }else{
        	$id = intval(I('id'));
        	if($id == 0){
                exit('id错误');
        	}
        	$data = D('Coupons')->getCouponsById($id);
        	$this->assign('data' , $data);
            $this->assign('json_data' , json_encode($data));
        	$this->display();
        }
    }

	/*
	 * 删除平台优惠券
	 * */	
    public function couponDelete(){
        if(IS_AJAX && IS_POST){
            $id = intval(I('id'));
            if($id == 0){
            	$this->ajaxReturn(array('status' => 0 , 'msg' => 'id错误'));
            }
            $r = D('Coupons')->couponDelete($id);
            $this->ajaxReturn($r);
        }
    }         
}