<?php
namespace Home\Controller;
use Think\Controller;
class PriceController extends Controller {
    public function allPrice(){
    	$goods_id = intval(I('goods_id'));
    	$goods_id = $goods_id?$goods_id:1;//当前商品
    	/*获取所有商品*/
    	$goods = M('Goods')->select();
    	/*获取时间段*/
    	$times  = M('Time')->field('id,time,name')->select();
    	/*获取商品所有记录的价格*/
    	$prices_ = M('Goods_price')->where(array('goods_id'=>$goods_id))->select();
        foreach($prices_ as $v){
            $prices[date('Y-m-d',$v['time'])][$v['time_id']] = $v;
        }
        /*获取需要查询的日期 默认为 今天 和前4天*/        
        if(!I('end_time') && !I('start_time')){
            $end_time   = strtotime(date('Y-m-d'));  
            $start_time = $end_time - 4 * 86400;    
        }else{
            $end_time   = I('end_time') ? strtotime(I('end_time')) : strtotime(date('Y-m-d'));       
            $start_time = I('start_time') ? strtotime(I('start_time')) : $end_time - 86400 * 30;
        }
        /*日期和数据关联*/
        $total_day  = ($end_time - $start_time) / 86400; //总天数
        $date       = array();
        for($i = 0 ; $i <= $total_day ; $i++){
            $strtotime = $start_time + $i  * 86400;
            $key  = date('Y-m-d',$strtotime);
            $data = array();
            foreach($times as $v){
                $v['price']   = $prices[$key][$v['id']]['price'];
                $v['time_id'] = $v['id'];
                $v['id']      = $prices[$key][$v['id']]['id'];
                $data[] = $v;
            }
            $date[$key] = array(
                'date' => strtotime($key),
                'week' => date('w', strtotime($key));
                'data' => $data
            );  
        }
    	$this->assign('now_day' , strtotime(date('Y-m-d')));
    	$this->assign('goods_id' , $goods_id);
    	$this->assign('times' , $times);
    	$this->assign('goods' , $goods);
    	$this->assign('data' , $date);
        $this->display();
    }

    public function setPrice(){
    	if(IS_POST){
    		$data = I();
    		if($data['id']){
    			$result = D('GoodsPrice')->goodsPriceupdate($data);
    		}else{
    			$result = D('GoodsPrice')->goodsPriceAdd($data);
    		}
    		$this->ajaxReturn($result);
    	}
    }
}