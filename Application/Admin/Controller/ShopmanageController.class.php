<?php
/*
 * 共享商品订单
 * */
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class ShopmanageController extends CommonController{
   public function shopList(){//店铺列表
	   
	    $r=self::get_page('shop_data','id,member_id,shop_name,desc,thumb,time',$amap,6,"test",8,'id asc');
	    $show =$r['pg']; 
		
		$ziduan=('id,username');
        foreach($r['result'] as $kk=>$vv){ 
		  $r['result'][$kk]['time'] = date('Y-m-d H:i:s',$vv['time']);		  
		  $sellerdata =  D('member')->where($ziduan)->find($vv['member_id']);
		   $r['result'][$kk]['username'] = $sellerdata['username'];
		}	
		$list =$r['result'];	
	    $this->assign('page',$show);// 赋值分页输出
		$this->assign("list",$list);	   
        $this->display();
   }

   public function index(){
   	
   	 $this->display();
   }
   

   public function test(){
	    if(I('post.')){
		    session('shopmap',null);
		    $map=array();
			$amap=array();
			$json_data = I('post.');
             		
			foreach($json_data as $k => $v){
					if($v){
						$v = trim($v);
						//店铺名称搜索
						if($k == 'out_trade_no'){
							$map['shop_name'] = $v;//组合商品名称搜索条件
							
						}
						//卖家账号搜索
				        if($k == 'name'){
							$map['member_id']= M('member')->where(array('username'=>$v))->getField('id');
					        
							//$map['member_id'] = $getData['id'];
							
							//$map['shop_name'] = $v;//组合商品名称搜索条件
							
						}
						//开始搜索时间
						if($k == 'start_time'){
							//dump(strtotime($v));exit;
							$map['time'] = array('egt',strtotime($v));//组合类别搜索条件
						
						}
						//截止搜索时间
						if($k == 'end_time'){							
							$map['time'] = array('elt',strtotime($v));//组合类别搜索条件
						
						}
					}
			   }	
                  
			  if($map){
					$amap = $map; 
					session('shopmap',$amap);		 
			  }
	
		}		
		if( session('shopmap') ){					
		  $amap = session('shopmap');	
			//dump($amap);exit;
		}     		
		$r=self::get_page('shop_data','id,member_id,shop_name,desc,thumb,time',$amap,6,"test",8,'id asc');		
		//dump($r);die;
		//$show =$r['pg']; 
		//$list =$r['result'];
		$ziduan=('id,username');
		foreach($r['result'] as $kk=>$vv){ 
		  $r['result'][$kk]['time'] = date('Y-m-d H:i:s',$vv['time']);
		   
		   $sellerdata =  D('member')->where($ziduan)->find($vv['member_id']);
		   $r['result'][$kk]['username'] = $sellerdata['username'];
		}
		
		$data=json_encode($r);//将整个数组转换成json编码的数组
        $this->ajaxReturn($data); 
	}
   
    public function get_page($table='',$field='',$maps=array(),$limitRows=6,$action="tes",$pgnum=8,$order='id asc'){
        $data =D($table);
		import("Think.AjaxPage");
		$fild=$field;
		$map = $maps; 
		$count=$data->where($map) ->count();		
		$limitRows = 5; // 设置每页记录数
		$Page = new \Think\AjaxPage($count, $limitRows,$action);// 实例化分页类 传入总记录数和每页显示的记录数
        $Page->rollPage   = $pgnum;	
        $nowPage = isset($_GET['p'])?$_GET['p']:1;
        $list['result'] = $data->field($fild)->where($map)->order($order)->page($nowPage.','.$Page->listRows)->select();       
			
		$show = $Page->show();
	
	    $list['pg']=$show;//将分页显示也放到数组里
		
		return $list;
	}
   
    public function orderdetial(){//查看订单详情
	     if(I('get.id')){
			 $id = I('get.id');
		     $fild=('id,goods_name,goods_price,total_price,goods_thumb,status,send_status,member_id,seller_id,rent_time,rent_number');
			 
			 $map['order_id']=$id;
			 $orderGoods = D('order_data')->field($fild)->where($map)->select();
			 foreach( $orderGoods as $k=>$v ){
				 $myfild=('id,username');
				 $username = D('member')->field($myfild)->find($v['seller_id']);
				 $orderGoods[$k]['username'] = $username['username'];			 
				 $orderGoods[$k]['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
				 
			 }
			
			 $this->assign('orderGoods',$orderGoods);			 
			 $this->display(); 			 			 
		 }	   
    }
   
       public function delsession(){//清空查询条件session
			 if(IS_AJAX){
				 $value = I('post.value');				
				 if($value==2){
					 if(session('shopmap')){
						 session('shopmap',null);
					 
					 }				 
				 }				 			 
			 }	   
        }
		
		public function deleteorder(){//删除订单
			 if(IS_AJAX){
				 $jsonarr = array('msg'=>'ok');
				 $aid = I('post.id');
				 $getdel = D('order_data')->delete($aid);
				 if($getdel){
					 $this->ajaxReturn($jsonarr);					 
				 }else{
					 $this->ajaxReturn(array('msg'=>'error'));					 
				 }		 			 
			 }	   
        }
		
		
		public function seeshop(){//查看会员店铺
			 if(IS_AJAX){
				 $jsonarr = array('msg'=>'ok');
				 $aid = I('post.id');
				 $arr=array('id'=>$aid,'see'=>'seeshop');

				 session('member_data',$arr);
				 $getdel = session('member_data');
				// $getdel = D('order_data')->delete($aid);
				 if($getdel){
					 $this->ajaxReturn($jsonarr);					 
				 }else{
					 $this->ajaxReturn(array('msg'=>'error'));					 
				 }		 			 
			 }	   
        }		
		
		public function hiddenshop(){//屏蔽店铺
			 if(IS_AJAX){				 
				 $aid = I('post.id');
				 $fild = ('id,username');
				 $getName = D('member')->field($fild)->find($aid);
				
				 $getdel = D('member')->where("id={$aid}")->setField('is_hidden',1);
				// dump(getdel);exit;
				 if( $getdel ){
					  $jsonarr = array('msg'=>'ok','username'=>$getName['username']);
					  $this->ajaxReturn($jsonarr);						
				 }else{
					 $this->ajaxReturn(array('msg'=>'error'));					 
				 }		 			 
			 }	   
        }
		
		public function openshop(){//屏蔽店铺
			 if(IS_AJAX){				 
				 $aid = I('post.id');
				 $fild = ('id,username');
				 $getName = D('member')->field($fild)->find($aid);
				
				 $getdel = D('member')->where("id={$aid}")->setField('is_hidden',0);
				// dump(getdel);exit;
				 if( $getdel ){
					  $jsonarr = array('msg'=>'ok','username'=>$getName['username']);
					  $this->ajaxReturn($jsonarr);						
				 }else{
					 $this->ajaxReturn(array('msg'=>'error'));					 
				 }		 			 
			 }	   
        }
		
		public function checkclass(){//屏蔽店铺
			 if(IS_AJAX){				 
				 $zid = I('post.zid');
				 $aziduan = ('id,username,is_hidden');	
                 $where['is_hidden'] = 1;				 
				 $getdel = D('member')->field($aziduan)->where($where)->select();
				// dump(getdel);exit;
				 $getarr = '';
				 if( $getdel ){
				   foreach( $getdel as $k=>$v ){					 
					  $getarr[]=$v['id'];						   										
					  $jsonarr = array('msg'=>'ok','getarr'=>$getarr);					 
				   }
 				    $this->ajaxReturn($jsonarr);	
				 }else{
					 $this->ajaxReturn(array('msg'=>'error'));					 
				 }		 			 
			 }	   
        }
}