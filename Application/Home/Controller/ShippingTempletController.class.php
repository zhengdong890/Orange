<?php
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class ShippingTempletController extends Controller {
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

    /*物流工具页面*/
	public function logisticsTool(){
        $title = I('title');
        //dump($title);				
		$member_id = $_SESSION['member_data']['id'];
	       
	    $area = M('Area')->where(array('parent_no'=>0))->select();
	    foreach($area as $k=>$v){
	      $area[$k]['child']=M('Area')->where(array('parent_no'=>$v['area_no']))->select();
	     
	    }
	    //dump($area);
	    $this->assign('area',$area);
		$this->assign('member_id' , $member_id);
	    $this->assign('title',$title);
		
		$this->display();
	}

    /*根据模板id获取物流模板其他数据*/    
    public function getShipping(){
        if(IS_AJAX){
        	$templet_id = intval(I('id'));
        	if($templet_id == 0){
        		$this->ajaxReturn(array('status'=>0,'msg'=>'模板id错误'));
        	}
        	$seller_id = $_SESSION['member_data']['id'];
        	//根据运费模板id获取指定包邮数据
        	$free_data = D('ShippingTemplet')->getTempletFreeDataByTempleteId($templet_id , $seller_id);
        	//根据运费模板id 获取运送地址详细数据 
        	$templet_data = D('ShippingTemplet')->getTempletDataByTempleteId($templet_id , $seller_id);
        	$this->ajaxReturn(array(
                'status' => 1,
                'msg'    => 'ok',
                'data'   => array(
                    'free_data'    => $free_data ? $free_data : array(),
                    'templet_data' => $templet_data ? $templet_data : array()
                )
        	));
        }
    }

    /*
     *获取商家所有的运费模板
     * */
    public function getShippingTemplet(){
    	if(IS_AJAX){
    		$seller_id = $_SESSION['member_data']['id'];
       	    $data = D('ShippingTemplet')
       	          ->getShippingTempletList($seller_id , $condition = array('seller_id'=>$_SESSION['member_data']['id'])); 
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
                $v['province'] = $area[$v['province']];
                $v['city']     = $area[$v['city']]; 
            } , $area);      
       	    $this->ajaxReturn($data);
    	}
    }

    /*
     *获取运费模板
     * */
    public function getShippingTempletList(){
    	if(IS_AJAX){
            $data     = I();
       	    $firstRow = intval($data['firstRow']);
       	    $listRows = intval($data['listRows']);
       	    $data     = D('ShippingTemplet')
       	              ->getShippingTempletList($seller_id , $condition = array('seller_id'=>$_SESSION['member_data']['id']) , array($firstRow , $listRows));
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
                $v['province'] = $area[$v['province']];
                $v['city']     = $area[$v['city']]; 
            } , $area);
	        echo json_encode($data); 
    	}
    }

    /*
     *添加运费模板
     * */    
    public function templetAdd(){
    	if(IS_AJAX){
    		$member_id = $_SESSION['member_data']['id'];
    		$data      = I();
    		/*	
            $data      = array(
                'new_templet' => '{"name" : "1","province" : "2","city" : "3","free_status" : "4","free_condition" : "5","start_number" : "6","start_price" : "7","add_number" : "8","add_price": "9"}',

                'new_templet_data' =>  '{"1":{"province" : "1","city" : "2","start_number" : "3","start_price" : "4","add_number" : "5","add_price": "6"}}'  
            ); */   		
    		if(empty($data)){
                $this->ajaxReturn(array('status'=>0 , 'msg'=>'请传入数据'));
		    }				
            /*新增的运费模板*/    		
    		$new_templet   = is_string($data['new_templet']) ? json_decode(htmlspecialchars_decode($data['new_templet']) , true) : $data['new_templet'];//运费模板

    		if(isset($new_templet)){
    			/*检测运费模板*/
	    		$result    = D('ShippingTemplet')->checkTemplet($new_templet);
	    		if($result['status'] == '0'){
	                $this->ajaxReturn($result);
	    		}
	    		$new_templet  = $result['data'];
	    		$new_templet['seller_id'] = $member_id;//商家id
    		}else{
    			$this->ajaxReturn(array('status'=>0 , 'msg'=>'运费模板不能为空'));
    		}   	

            /*新的运费模板下的运送地址*/
            $new_templet_data = $data['new_templet_data'];
            if(!empty($new_templet_data)){	            
	            $new_templet_data = is_string($data['new_templet_data']) ? json_decode(htmlspecialchars_decode($data['new_templet_data']) , true) : $data['new_templet_data'];
                foreach($new_templet_data as $k => $v){
                    if(isset($v['province']) && is_array($v['province'])){
                    	$new_templet_data[$k]['province'] = implode(',' , $v['province']);
                    }
                    if(isset($v['city']) && is_array($v['city'])){
                    	$new_templet_data[$k]['city'] = implode(',' , $v['city']);
                    }
	            }
               	/*检测运费模板*/
	    		$result    = D('ShippingTemplet')->checkTempletData($new_templet_data , 1 , $member_id);
	    		if($result['status'] == '0'){	    			
	                $this->ajaxReturn($result);
	    		}
	    		$new_templet_data = $result['data'];
            }else{
            	if($data['free_status'] == 1){
                    $this->ajaxReturn(array('status'=>0 , 'msg'=>'运费模板下的地址不能为空'));
            	} 			
    		}

            /*新的运费模板下 指定包邮条件数据*/
            $new_free_condition = $data['new_free_condition'];
            if(!empty($new_free_condition)){	      
                $new_free_condition = is_string($data['new_free_condition']) ? json_decode(htmlspecialchars_decode($data['new_free_condition']) , true) : $data['new_free_condition'];//运费模板下的 指定包邮条件
                //运费模板下的运送地址
	            foreach($new_free_condition as $k => $v){
                    if(is_array($v['province'])){
                    	$new_free_condition[$k]['province'] = implode(',' , $v['province']);
                    }
                    if(is_array($v['city'])){
                    	$new_free_condition[$k]['city'] = implode(',' , $v['city']);
                    }
	            }
    			/*检测指定包邮条件*/
	    		$result    = D('ShippingTemplet')->checkTempletFreeData($new_free_condition);
	    		if($result['status'] == '0'){	    			
	                $this->ajaxReturn($result);
		    	}
    	    }else{
    			//$this->ajaxReturn(array('status'=>0 , 'msg'=>'运费模板下的地址不能为空'));
    		}  
    		/*处理运费模板*/
    		$result    = D('ShippingTemplet')->templetAdd($new_templet);
    		if($result['status'] == '0'){    				    			    			
                $this->ajaxReturn($result);
    		}
            $templet_id = $result['id']; 
            //处理运费模板下的运送地址
            if(!empty($new_templet_data)){
            	$result = D('ShippingTemplet')->templetDataAdd($new_templet_data , $templet_id);
            }  
            //处理运费模板下的指定包邮条件
            if(!empty($new_free_condition)){                       
                $result = D('ShippingTemplet')->templetFreeAdd($new_free_condition , $templet_id , $member_id);
            }
    		$this->ajaxReturn($result);
    	}
    }

    /*
     *修改运费模板
     * */  
    public function templetUpdate(){
    	if(IS_AJAX){
			$member_id  = $_SESSION['member_data']['id'];
			$data       = I();	
			$templet_id = intval($data['templet_id']);
			/*
			$data      = array(
	            'old_templet' => '{"id":17,"name" : "严锦","province" : "22","city" : "33","free_status" : "44","free_condition" : "55","start_number" : "66","start_price" : "77","add_number" : "88","add_price": "99"}',
	            'old_templet_data' =>  '{"18":{"province" : "11","city" : "21","start_number" : "31","start_price" : "41","add_number" : "51","add_price": "61"}}',
	            'templet_id' => '17', 
	            'new_templet_data' =>  '{"1":{"province" : "1","city" : "2","start_number" : "3","start_price" : "4","add_number" : "5","add_price": "6"}}' 
	        );*/
			if(empty($data)){
	            $this->ajaxReturn(array('status'=>0 , 'msg'=>'请传入非空数据'));
			}			    		
	        /*旧的需要修改的运费模板*/    		
			$old_templet = is_string($data['old_templet']) ? json_decode(htmlspecialchars_decode($data['old_templet']) , true) : $data['old_templet'];//运费模板
			if(isset($old_templet)){
				/*检测运费模板*/
	    		$result  = D('ShippingTemplet')->checkTemplet($old_templet , 2);
	    		if($result['status'] == '0'){
	                $this->ajaxReturn($result);
	    		}
	    		$old_templet = $result['data'];
	    		$result      = D('ShippingTemplet')->templetUpdate($old_templet , $member_id);
			}

	        /*新的运费模板下的运送地址*/
	        $new_templet_data = is_string($data['new_templet_data']) ? json_decode(htmlspecialchars_decode($data['new_templet_data']) , true) : $data['new_templet_data'];//运费模板下的运送地址
			if(isset($new_templet_data)){
				foreach($new_templet_data as $k => $v){
                    if(isset($v['province']) && is_array($v['province'])){
                    	$new_templet_data[$k]['province'] = implode(',' , $v['province']);
                    }
                    if(isset($v['city']) && is_array($v['city'])){
                    	$new_templet_data[$k]['city'] = implode(',' , $v['city']);
                    }
	            }
				/*检测运费模板下的运送地址*/
	    		$result    = D('ShippingTemplet')->checkTempletData($new_templet_data , 1 , $member_id);
	    		if($result['status'] == '0'){
	                $this->ajaxReturn($result);
	    		}
	    		$new_templet_data = $result['data'];
	    		$n = M('Shipping_templet')->where(array('id'=>$templet_id,'seller_id'=>$member_id))->getField('id');
	    		if(empty($n)){
                    $this->ajaxReturn(array('status'=> 0 , 'msg'=>'id不存在'));
	    		}
	    		$result = D('ShippingTemplet')->templetDataAdd($new_templet_data , $templet_id);
			}

	        /*旧的 需要修改的运费模板下的运送地址*/
	        $old_templet_data = is_string($data['old_templet_data']) ? json_decode(htmlspecialchars_decode($data['old_templet_data']) , true) : $data['old_templet_data'];//运费模板下的运送地址
			if(isset($old_templet_data)){
				foreach($old_templet_data as $k => $v){
                    if(isset($v['province']) && is_array($v['province'])){
                    	$old_templet_data[$k]['province'] = implode(',' , $v['province']);
                    }
                    if(isset($v['city']) && is_array($v['city'])){
                    	$old_templet_data[$k]['city'] = implode(',' , $v['city']);
                    }
	            }
				/*检测运费模板*/
	    		$result    = D('ShippingTemplet')->checkTempletData($old_templet_data , 2);
	    		if($result['status'] == '0'){
	                $this->ajaxReturn($result);
	    		}
	    		$old_templet_data = $result['data'];
	    		$result    = D('ShippingTemplet')->templetDataUpdate($old_templet_data , $member_id);
			}		

            /*新的运费模板下 指定包邮条件数据*/
            $new_free_condition = $data['new_free_condition'];
            $new_free_condition = is_string($data['new_free_condition']) ? json_decode(htmlspecialchars_decode($data['new_free_condition']) , true) : $data['new_free_condition'];//运费模板下的 指定包邮条件
    		if(isset($new_free_condition)){
	            foreach($new_free_condition as $k => $v){
                    if(is_array($v['province'])){
                    	$new_free_condition[$k]['province'] = implode(',' , $v['province']);
                    }
                    if(is_array($v['city'])){
                    	$new_free_condition[$k]['city'] = implode(',' , $v['city']);
                    }
	            }
    			/*检测指定包邮条件*/
	    		$result    = D('ShippingTemplet')->checkTempletFreeData($new_free_condition);
	    		if($result['status'] == '0'){	    			
	                $this->ajaxReturn($result);
	    		}
	    		$n = M('Shipping_templet')->where(array('id'=>$templet_id,'seller_id'=>$member_id))->getField('id');
	    		if(empty($n)){
                    $this->ajaxReturn(array('status'=> 0 , 'msg'=>'id不存在'));
	    		}
	    		$result = D('ShippingTemplet')->templetFreeAdd($new_free_condition , $templet_id , $member_id);
    		}	

            /*旧的 需要修改的运费模板下的指定包邮条件数据*/
            $old_free_condition = $data['old_free_condition'];
            $old_free_condition = is_string($data['old_free_condition']) ? json_decode(htmlspecialchars_decode($data['old_free_condition']) , true) : $data['old_free_condition'];//运费模板下的 指定包邮条件
    		if(isset($old_free_condition)){
	            foreach($old_free_condition as $k => $v){
                    if(isset($v['province']) && is_array($v['province'])){
                    	$old_free_condition[$k]['province'] = implode(',' , $v['province']);
                    }
                    if(isset($v['city']) && is_array($v['city'])){
                    	$old_free_condition[$k]['city'] = implode(',' , $v['city']);
                    }
	            }
    			/*检测指定包邮条件*/
	    		$result    = D('ShippingTemplet')->checkTempletFreeData($old_free_condition , 2);
	    		if($result['status'] == '0'){
	                $this->ajaxReturn($result);
	    		}
	    		$result    = D('ShippingTemplet')->templetFreeUpdate($old_free_condition , $member_id);
    		}    		
			$this->ajaxReturn($result); 
		}   	
    }

    /*
     * 获取运费模板
     * */    
    public function getTemplet(){
    	if(IS_AJAX){
    		$member_id = $_SESSION['member_data']['id'];
    		$data = M('Shipping_templet')->select();
    		$this->ajaxReturn(array('status'=>1,'data'=>$data));
    	}
    }

   /**
    * 删除运费模板
    */
    public function templetDelete(){
    	if(IS_AJAX){
    		$id = I('id');
    		$member_id = $_SESSION['member_data']['id'];
    		$r = D('ShippingTemplet')->templetDelete($id , $member_id);
    		$this->ajaxReturn($r);
    	}
    }

   /**
    * 删除运费模板 每个运送地址详细数据 
    */
    public function templetDataDelete(){
    	if(IS_AJAX){
    		$id = I('id');
    		$member_id = $_SESSION['member_data']['id'];
    		$r = D('ShippingTemplet')->templetDataDelete($id , $member_id);
    		$this->ajaxReturn($r);
    	}
    } 
    
   /**
    * 删除指定包邮条件
    */
    public function templetFreeDelete(){
    	if(IS_AJAX){
    		$id = I('id');
    		$member_id = $_SESSION['member_data']['id'];
    		$r = D('ShippingTemplet')->templetFreeDelete($id , $member_id);
    		$this->ajaxReturn($r);
    	}
    }   
}