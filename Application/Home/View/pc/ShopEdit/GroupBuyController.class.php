<?php
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class GroupBuyController extends CommonController{

   public function groupBuyList(){
   	   $list = M('Group_buy')->select();
   	   $this->assign('list' , $list);
       $this->display();
   }

   public function groupBuyAdd(){
       if(IS_POST){
           $data = array(
               'title'      => I('title'),
               'start_time' => I('start_time'),
               'end_time'   => I('end_time'),
               'status'     => I('status'),
               'time'       => date('Y-m-d H:i:s')
           );
           $result = D('GroupBuy')->groupBuyAdd($data);
           if($result['status']){
               $this->success('添加成功','GroupBuyList');
           }else{
           	   $this->error($result['msg'],'GroupBuyAdd');
           }
       }else{
           $this->display();
       }      
   }

   public function groupBuyUpdate(){
        if(IS_POST){
           $data   = I();
           $result = D('GroupBuy')->groupBuyUpdate($data);
           $this->ajaxReturn($result);
       }else{
       	   $id    = I('id');
       	   $group = M('Group_buy')->where(array('id'=>$id))->find();
       	   $this->assign('group' , $group);
           $this->display();
       }  
   }  
    
   /**
    * 团购申请列表
    */
    public function goodsApplicationList(){
	    $data = M('Group_list')
	          ->field(array('id,goods_group_price,goods_id,group_img,status'))
	          ->select();	
	    foreach($data as $k => $v){
	    	$goods_ids[] = $v['goods_id'];
	    }   
	    $goods_ids   = implode(',' , $goods_ids);
	    $goods_data_ = M('Mall_goods')
			         ->where(array('id'=>array('in' , $goods_ids)))
			         ->field('id,goods_name,goods_price')
			         ->select();
		$goods_data = array();	         
        foreach ($goods_data_ as $k => $v) {
        	$goods_data[$v['id']] = $v;
        }
        foreach($data as $k => $v){
	    	$data[$k]['goods_name']  = $goods_data[$v['goods_id']]['goods_name'];
	    	$data[$k]['goods_price'] = $goods_data[$v['goods_id']]['goods_price'];
	    } 
	    $this->assign('data',$data);   	   
        $this->display();
    }  

   /**
    * 团购申请审核
    */
    public function groupBuyDetail(){
    	if(IS_POST){
            $data   = I();
            $result = D('GroupBuy')->groupBuyCheck($data);
            $this->ajaxReturn($result);
    	}else{
			$id   = I('id');  
			$data = M('Group_list')->where(array('id'=>$id))->find();
			$goods_data = M('Mall_goods')
						->where(array('id'=>$id))
						->field('goods_name,goods_price')
						->find();
	        $this->assign('goods_data',$goods_data); 						
	        $this->assign('data',$data); 	   	   
	        $this->display();    		
    	}
    }   
}