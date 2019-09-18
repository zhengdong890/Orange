<?php
namespace Home\Model;
use Think\Model;
/**
 * 商家运费模板模块业务逻辑
 * @author 幸福无期
 */
class ShippingTempletModel extends Model{ 
   protected $tableName = 'Shipping_templet'; //切换检测表
   
   /**
    * 获取运费模板
    * @param  int   $seller_id 卖家id
    * @param  array $condition 获取条件    
    * @return array 返回操作结果
    */
    public function getShippingTempletList($seller_id = '' , $condition = array() , $limit = array() , $order = 'id desc'){
        $model = M('Shipping_templet');
        !empty($condition) && ($model = $model->where($condition));        
        !empty($limit)     && ($model = $model->limit($limit[0] , $limit[1]));
        !empty($order)     && ($model = $model->order($order));
        $data   = $model->where(array('seller_id'=>$_SESSION['member_data']['id']))->select(); 
        $count  = $model->where($condition)->count();   
        return array(
            'data'      => $data,
            'totalRows' => $count
        );  
    }

   /**
    * 检测运费模板
    * @param  array $data_   添加的运费模板数据 
    * @param  int   $type    1增加 2修改
    * @return array 返回操作结果和过滤后的数据
    */   
    public function checkTemplet($data_ = array() , $type = 1){
   	    if(empty($data_)){
            return array('status' => 0 , 'msg' => '数据不能为空');  
   	    }
        /*运费模板数据*/
        $data = array(
        	'name'             => '', //模板名称
        	'province'         => '',//省
        	'city'             => '',//市
        	'free_status'      => '',//是否包邮 1自定义邮费 2卖家承担运费
        	'free_condition'   => '',//指定包邮条件 1按件数 2按金额 3按数量+金额
        	'start_number'     => '',//首件数
        	'start_price'      => '',//首件价格
        	'add_number'       => '', //加价数量
        	'add_price'        => '',//加价价格
        	'unit'             => '' //计价方式
        );
        if($type  == 2){
        	if(intval($data_['id']) == 0){
        		return array('status' => '0' , 'msg' => 'id错误');
        	}
        	$data['id'] = '';
        }
        $data  = array_intersect_key($data_ , $data); //获取键的交集  
        /*验证数据*/
        $validate_model = $type == 1 ? self::MUST_VALIDATE : self::EXISTS_VALIDATE;
        $model = D("Shipping_templet");
        $rules = array(
            array('name','require','必须输入模板名称',$validate_model),
            array('province','require','必须选择省',$validate_model),
            array('city','require','必须选择市',$validate_model),
            array('free_status','require','必须选择是否包邮',$validate_model),
            array('unit','/^1|2|3$/i','必须选择计价方式',$validate_model)
        );
        if($data['free_status'] == 1){
            $rules = array_merge($rules , array( 
                array('start_number','/^[1-9]\d*$/i','首件数不正确',$validate_model),
                array('start_price','/^([1-9]\d{0,9}|0)([.]?|(\.\d{1,2})?)?$/','首件价格格式错误',$validate_model),
                array('add_number','/^[1-9]\d*$/i','加价数量不正确',$validate_model),
                array('add_price','/^([1-9]\d{0,9}|0)([.]?|(\.\d{1,2})?)$/','加价格式错误',$validate_model)
            ));
        }
        if($model->validate($rules)->create($data) === false){
            return array('status' => 0,'msg' => $model->getError());
        }  
        return array('status' => 1,'data' => $data);    
   }

   /**
    * 添加运费模板
    * @param  array $data_     添加的运费模板数据 
    * @param  int   $seller_id 卖家id
    * @return array 返回操作结果
    */
    public function templetAdd($data = array()){
        $id = M("Shipping_templet")->add($data);
        if($id === false){
            $result = array(
               'status' => 0,
               'msg'    => '添加失败'
            );
        }
        return array(
           'status' => 1,
           'msg'    => '添加成功',
           'id'     => $id
        );
    }

   /**
    * 修改运费模板
    * @param  array $data_     添加的运费模板数据 
    * @param  int   $seller_id 卖家id
    * @return array 返回操作结果
    */
    public function templetUpdate($data = array() , $seller_id){
    	//验证id
        $id = intval($data['id']);unset($data['id']);              
        $r  = M("Shipping_templet")->where(array('id'=>$id , 'seller_id'=>$seller_id))->save($data);
        if($r === false){
            $result = array(
               'status' => 0,
               'msg'    => '修改失败'
            );
        }
        return array(
           'status' => 1,
           'msg'    => '修改成功'
        );
    }

   /**
    * 删除运费模板
    * @param  int   $id        需要删除的运费模板id
    * @param  int   $seller_id 卖家id
    * @return array 返回操作结果
    */
    public function templetDelete($id , $seller_id){
    	$id        = intval($id);
    	$seller_id = intval($seller_id);
    	//验证id
    	if($id  == 0){
            return array('status' => '0' , 'msg' => '运送地址id错误');
    	}
    	if($seller_id  == 0){
            return array('status' => '0' , 'msg' => '商家id错误');
    	}  
        $where     = array('templet_id'=>$id , 'seller_id'=>$seller_id);
    	$n         = M('Shipping_templet_data')->where($where)->count();  
        if($n > 0){
        	return array(
	           'status' => 0,
	           'msg'    => '该运费模板下有运送地址,不能删除'
	        );
        }
        $where     = array('id'=>$id , 'seller_id'=>$seller_id);
        $r = M('Shipping_templet')->where($where)->delete();
        if($r == false ){
	        return array(
	           'status' => 0,
	           'msg'    => '删除失败'
	        );
        }
        return array(
           'status' => 1,
           'msg'    => '删除成功'
        ); 
    }

   /**
    * 计算运费模板价格 同一个商家
    * @param  int   $seller_id 商家id
    * @param  array $buy_data  购买信息 
    * array(
    *      'order'      => array('city' => '收货市'),
    *      'order_data' => array(
    *           array('number'='购买数量','total_price'=>'购买总价','templete_id'=>'运费模板id'),....
    *      )   
    * )
    * @return array 返回操作结果
    */   
    public function getTempletPrice($seller_id , $buy_data){
    	if(empty($buy_data)){
    		return 0;
    	}
        $shipping_price = 0;//运费
        /*1 根据商品 选择的运费模板 合并同一模板下的不同商品 同一买家不同的商品同一运费模板应该合并计算*/
        $new_buy_data = array();        
        //获取所有模板数据
        $templet_id   = array_column($buy_data['order_data'] , 'templet_id');
        $templet_id   = implode(',' , $templet_id);
        $templet_all  = M("Shipping_templet")
            ->where(array('id' => array('in' , $templet_id) , 'seller_id' => $seller_id))
            ->select();
        $templet_all  = array_all_column($templet_all , 'id');    
        foreach($buy_data['order_data'] as $k => $v){
        	if(!isset($new_buy_data[$v['templet_id']])){
        		//获取模板信息
        		$templet = $templet_all[$v['templet_id']];
		        //卖家承担运费 或者该模板不存在
		        if(empty($templet) || $templet['free_status'] == 2){
		            continue;
		        }	
		        $new_buy_data[$v['templet_id']] = $v;
		        $new_buy_data[$v['templet_id']]['shipping'] = $templet;	        
        	}else{
                $new_buy_data[$v['templet_id']]['number'] += $v['number'];
                $new_buy_data[$v['templet_id']]['total_price'] += $v['total_price'];
        	}             
        }           
        /*2 是否满足包邮条件 在$new_buy_data里面去掉符合包邮的模板数据*/      
        foreach($new_buy_data as $k => $v){
        	//根据运费模板id 获取指定包邮条件数据
            $shipping_free = $this->getTempletFreeDataByTempleteId($k , $seller_id);
            if($shipping_free === false){
                continue;
            }
        	//判断是否包邮
            $r = $this->isFree($shipping_free , $v , $buy_data['order']['city']);
            if($r){
            	//包邮
                unset($new_buy_data[$k]);	  
            }
        }      
        /*3 根据买家收货地址 计算运费 在$new_buy_data里面去掉计算过运费的模板数据*/
        foreach($new_buy_data as $k => $v){
	        //根据运费模板id 获取运送地址数据
	        $templete_data = $this->getTempletDataByTempleteId($k , $seller_id);
	        if($templete_data === false){
                continue;
	        }
        	//根据买家收货地址 计算运费
            $price = $this->getTempletDataPrice($templete_data , $v , $buy_data['order']['city']);
            if($price !== false){
                $shipping_price += $price;
                unset($new_buy_data[$k]);
            }
        }
        /*4 剩下的模板运费 按默认的设置计算运费*/
        foreach($new_buy_data as $k => $v){
	        //先计算 首计价方式内 的价格
	        $start_price = $v['shipping']['start_price'];
	        //在计算续费
	        $else_price  = 0;
	        //续费 计价方式 的数量 (件,重量,体积)
	        $else_number = $v['number'] - $v['shipping']['start_number'];
	        if($else_number > 0){
	            $else_price = ceil($else_number / $v['shipping']['add_number']) * $v['shipping']['add_price'];
	        }
	        $shipping_price = $shipping_price + $start_price + $else_price;
        }
        return $shipping_price; 
    }

/*******************************************运费模板运送地址*************************************************/

   /**
    * 检测运费模板每个运送地址的数据 合法性
    * @param  array $data   运送地址详细 数据 
    * @param  int   $type   1增加 2修改
    * @return array 返回操作结果
    */    
    public function checkTempletData($data = array() , $type = 1 , $seller_id){
        if(empty($data)){
            return array('status'=>0 , 'msg' => '数据不能为空');
        }
        /*验证*/
        $model = D("Shipping_templet_data");
        $validate_model = $type == 1 ? self::MUST_VALIDATE : self::EXISTS_VALIDATE;
        $rules = array(
            array('province','require','必须选择省',$validate_model),
            array('city','require','必须选择市',$validate_model),
            array('start_number','/^[1-9]\d*$/i','首件数不正确',$validate_model),
            array('start_price','/^[1-9][0-9]*(.[0-9]{1,2})?$/','首件价格格式错误',$validate_model),
            array('add_number','/^[1-9]\d*$/i','加价数量不正确',$validate_model),
            array('add_price','/^[1-9][0-9]*(.[0-9]{1,2})?$/','加价格式错误',$validate_model)
        );
        $temp = array(
    		'province'       => '',//省
        	'city'           => '',//市
        	'start_number'   => '',//首件数
        	'start_price'    => '',//首件价格
        	'add_number'     => '', //加价数量
        	'add_price'      => ''//加价价格                
        );
        if($type == 2){
        	array_unshift($rules , array('id','/^[1-9]\d*$/i','运送地址id不正确',self::MUST_VALIDATE));
        	$temp['id'] = '';
        }
        foreach($data as $k => $v){
        	$v['id'] = $k;
        	$templet = array_intersect_key($v, $temp);
        	if($model->validate($rules)->create($templet) === false){
	            return array('status' => 0,'msg' => $model->getError());
	        }  
	        if($type == 2){continue;}
        	$data[$k]['seller_id']  = $seller_id;
        }
        return array('status'=>1,'data'=>$data);
    }

   /**
    * 添加运费模板 每个收货地址的详细数据 
    * @param  array $data_      添加每个运送地址数据  格式 array(array('province'=>,.....),....array());
    * @param  int   $templet_id 运费模板id
    * @param  int   $seller_id  卖家id
    * @return array 返回操作结果
    */
    public function templetDataAdd($data = array() , $templet_id){
    	if(empty($data)){
            return array('status'=>1); 
    	}
    	//验证id
    	if($templet_id  == 0){
            return array('status' => '0' , 'msg' => '运费模板id错误');
    	}
        foreach($data as $v){
        	$v['templet_id'] = $templet_id;
            $r = M('Shipping_templet_data')->add($v);
        }
        return array(
           'status' => 1,
           'msg'    => '添加成功'
        ); 
    }

   /**
    * 修改运费模板 每个运送地址详细数据 
    * @param  array $data_  修改每个运送地址数据  格式 array('id1'=>array('province'=>,.....),....'idn'=>array('province'=>,.....));
    * @param  int   $seller_id 卖家id
    * @return array 返回操作结果
    */
    public function templetDataUpdate($data = array() , $seller_id){
    	if(empty($data)){
            return array('status'=>1); 
    	}  
        foreach($data as $k => $v){
        	$id = $k;
        	unset($v['id']);
            $r  = M('Shipping_templet_data')->where(array('id'=>$id,'seller_id'=>$seller_id))->save($v);
        }
        if($r == false ){
	        return array(
	           'status' => 0,
	           'msg'    => '保存失败'
	        );
        }
        return array(
           'status' => 1,
           'msg'    => '保存成功'
        ); 
    }

   /**
    * 删除运费模板 每个运送地址详细数据 
    * @param  int   $id        需要删除的运送地址id
    * @param  int   $seller_id 卖家id
    * @return array 返回操作结果
    */
    public function templetDataDelete($id , $seller_id){
    	$id        = intval($id);
    	$seller_id = intval($seller_id);
    	//验证id
    	if($templet_id  == 0){
            return array('status' => '0' , 'msg' => '运送地址id错误');
    	}
    	if($seller_id  == 0){
            return array('status' => '0' , 'msg' => '商家id错误');
    	}  
    	$r = M('Shipping_templet_data')->where(array('id'=>$id , 'seller_id'=>$seller_id))->delete();     
        if($r == false ){
	        return array(
	           'status' => 0,
	           'msg'    => '删除失败'
	        );
        }
        return array(
           'status' => 1,
           'msg'    => '删除成功'
        ); 
    }

   /**
    * 根据运费模板id 获取运送地址详细数据 
    * @param  int   $templet_id 模板id
    * @param  int   $seller_id 卖家id
    * @return array 返回操作结果
    */
    public function getTempletDataByTempleteId($templet_id , $seller_id){
        if(empty($templet_id)){
            return false;
        }
        $data = M('Shipping_templet_data')
            ->where(array('templet_id'=>$templet_id , 'seller_id'=>$seller_id))
            ->select();
        return $data;
    }

   /**
    * 根据买家的收货地址 和商家设置的运送地址 计算运费
    * @param  array  $templete 商家设置的运送地址
    * @param  array  $buy_data 购买信息
    * @param  var    $city     收货市
    * @return int|bool  返回计算结果
    */     
    public function getTempletDataPrice($templete = array() , $buy_data = array() , $city){
    	if(!is_array($templete) || !is_array($buy_data)){
            return false;
    	}
    	/*先搜索 买家的收货地址 在商家设置的运送地址 的数据*/
        foreach($templete as $k => $v){
            if($v['city'] == $city){
                $templete_data = $v;
                break;
            }
        } 
        /*买家的收货地址 在商家设置的运送地址 没有匹配到*/
        if(!isset($templete_data)){
            return false;
        } 
        /*计算价格*/
        //1 先计算 首计价方式内 的价格
        $start_price = $templete['start_price'];
        //2 在计算续费
        $else_price  = 0;
        //续费 计价方式 的数量 (件,重量,体积)
        $else_number = $buy_data['number'] - $templete['start_number'];
        if($else_number > 0){
            $else_price = ceil($else_number / $templete['add_number']) * $templete['add_price'];
        }
        return $start_price + $else_price; 
    }

/**********************************************指定包邮条件***********************************************/

   /**
    * 检测运费模板每个指定包邮条件数据的 合法性
    * @param  array $data   指定包邮条件数据 
    * @param  int   $type   1增加 2修改
    * @return array 返回操作结果
    */    
    public function checkTempletFreeData($data = array() , $type = 1){
        if(empty($data)){
            return array('status'=>0 , 'msg' => '数据不能为空');
        }
        /*验证*/
        $model = D("Templet_free_condition");
        $validate_model = $type == 1 ? self::MUST_VALIDATE : self::EXISTS_VALIDATE;
        $rules = array(
            array('province','require','必须选择省',$validate_model),
            array('city','require','必须选择市',$validate_model),
            array('send_type','/^1|2$/i','必须选择运送方式',$validate_model),
            array('free_condition','/^1|2$/i','必须选择包邮条件',$validate_model),
            array('free_condition_c','require','必须选择包邮值',$validate_model),
        );
        if($type == 2){//修改
        	array_unshift($rules , array('id','/^[1-9]\d*$/i','指定包邮条件id不正确',self::MUST_VALIDATE));
        }
        foreach($data as $k => $v){
        	if($model->validate($rules)->create($v) === false){
	            return array('status' => 0,'msg' => $model->getError());
	        }  
        }
        return array('status'=>1);
    }

   /**
    * 添加指定包邮条件数据
    * @param  array $data_      添加指定包邮条件数据  格式 array(array('province'=>,.....),....array());
    * @param  int   $templet_id 运费模板id
    * @param  int   $seller_id 卖家id    
    * @return array 返回操作结果
    */
    public function templetFreeAdd($data_ = array() , $templet_id , $seller_id){
    	if(empty($data_)){
            return array('status'=>1, 'msg'=>'添加成功'); 
    	}
    	//验证id
    	if(intval($templet_id)  == 0){
            return array('status' => '0' , 'msg' => '运费模板id错误');
    	}
    	if(intval($seller_id)  == 0){
            return array('status' => '0' , 'msg' => '卖家id错误');
    	}
        $data = array(
        	'province'         => '',//省
        	'city'             => '',//市
        	'send_type'        => '',//运送方式
        	'free_status'      => '',//是否包邮 1自定义邮费 2卖家承担运费
        	'free_condition'   => '',//指定包邮条件 1按件数 2按金额 3按数量+金额
        	'free_condition_c' => '',//指定包邮条件值
        	'seller_id'        => '',
        	'templet_id'       => ''
        );
        foreach($data_ as $v){
        	$v['templet_id'] = $templet_id;
        	$v['seller_id']  = $seller_id;
        	$v  = array_intersect_key($v , $data);
            $id = M('Templet_free_condition')->add($v);
        }
        if($id === false){
	        return array(
	           'status' => 0,
	           'msg'    => '添加失败'
	        ); 
        }
        return array(
           'status' => 1,
           'msg'    => '添加成功'
        ); 
    }    

   /**
    * 修改指定包邮条件数据
    * @param  array $data       添加指定包邮条件数据  格式 array(array('province'=>,.....),....array());
    * @param  int   $seller_id  卖家id
    * @return array 返回操作结果
    */
    public function templetFreeUpdate($data_ = array() , $seller_id){
    	if(empty($data_)){
            return array('status'=>1 , 'msg'=>'添加成功'); 
    	}  
    	$data = array(
        	'province'         => '',//省
        	'city'             => '',//市
        	'send_type'        => '',//运送方式
        	'free_status'      => '',//是否包邮 1自定义邮费 2卖家承担运费
        	'free_condition'   => '',//指定包邮条件 1按件数 2按金额 3按数量+金额
        	'free_condition_c' => '',//指定包邮条件值
        );
        foreach($data_ as $k => $v){
        	$id = $k;
        	unset($v['id']);
        	$v  = array_intersect_key($v , $data);
            $r  = M('Templet_free_condition')->where(array('id'=>$id,'seller_id'=>$seller_id))->save($v);
        }
        if($r == false ){
	        return array(
	           'status' => 0,
	           'msg'    => '保存失败'
	        );
        }
        return array(
           'status' => 1,
           'msg'    => '保存成功'
        );
    }  

   /**
    * 删除指定包邮条件
    * @param  int   $id        需要删除的指定包邮条件id
    * @param  int   $seller_id 卖家id
    * @return array 返回操作结果
    */
    public function templetFreeDelete($id , $seller_id){
    	$id        = intval($id);
    	$seller_id = intval($seller_id);
    	//验证id
    	if(intval($id)  == 0){
            return array('status' => '0' , 'msg' => '指定包邮条件id错误');
    	}
    	if(intval($seller_id)  == 0){
            return array('status' => '0' , 'msg' => '卖家id错误');
    	} 
    	$r = M('Templet_free_condition')->where(array('id'=>$id , 'seller_id'=>$seller_id))->delete();     
        if($r == false ){
	        return array(
	           'status' => 0,
	           'msg'    => '删除失败'
	        );
        }
        return array(
           'status' => 1,
           'msg'    => '删除成功'
        ); 
    }  

   /**
    * 根据运费模板id获取指定包邮数据
    * @param  int   $templet_id 模板id
    * @param  int   $seller_id 卖家id
    * @return array 返回操作结果
    */
    public function getTempletFreeDataByTempleteId($templet_id , $seller_id){
        if(empty($templet_id)){
            return false;
        }
        $data = M('Templet_free_condition')
            ->where(array('templet_id'=>$templet_id , 'seller_id'=>$seller_id))
            ->select();
        return $data;
    } 
    
   /**
    * 判断购买的商品是否符合指定包邮
    * @param  array  $free      指定包邮数据
    * @param  array  $buy_data  购买信息
    * @param  var    $city      收货市
    * @return array  返回操作结果
    */     
    public function isFree($free = array() , $buy_data = array() , $city){
    	if(!is_array($free) || !is_array($buy_data)){
            return false;
    	}
    	/*先搜索 购买收货地址是否 在指定包邮范围内*/
        foreach($free as $k => $v){
        	$v['city'] = ',' . $v['city'] . ',';
            if(strpos($v['city'] , $city) !== false){
                $free_data = $v;
                break;
            }
        } 
        /*如果不在指定包邮范围内*/
        if(!isset($free_data)){
            return false;
        } 
        /*按件数 指定包邮*/
        if($free_data['free_condition'] == 1 && $free_data['free_condition_c'] > $buy_data['number']){
            return false;
        }  
        /*按金额 指定包邮*/
        if($free_data['free_condition'] == 2 && $free_data['free_condition_c'] > $buy_data['total_price']){
            return false;
        }
        return true; 
    }
}