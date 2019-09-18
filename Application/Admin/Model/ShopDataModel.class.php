<?php
/**
 * 商家店铺模块业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */
namespace Admin\Model;
use Think\Model;
class ShopDataModel extends Model{
  protected $tableName = 'Shop_data'; //关闭检测字段
  
   /**
    * 卖家店铺信息 增加
    * @access public
    * @param  array $shop_data 店铺信息
    * @return array $result    执行结果
    */  
    public function shopDataAdd($shop_data){
    	//数据过滤
        $data_filter = array(
            'member_id'      => '',//卖家id
            'shop_name'      => '',//店铺名字
            'desc'           => '',//店铺描述
            'domain'         => '',//店铺域名
            'logistical'     => '',//物流服务
            'service'        => '',//服务态度
            'desc_score'     => '',//店铺域名
            'comment_number' => '',//店铺评论数量
            'qq'             => '', //店铺qq
            'thumb'          => '',
            'is_sign'        => ''
        );
        $data  = array_intersect_key($shop_data , $data_filter); //获取键的交集 
        $data['time'] = time();//添加时间 
        $seller_id = $data['member_id'];
	    $r  = M('Shop_data')->add($data);
	    if($r === false){
	        return array(
	            'status' => 0,
	            'msg'    => '增加店铺信息失败'
	        );
	    }
	    //添加样式表
        M('Shop_nav_css')->add(array('member_id'=>$seller_id));
        //添加banner表
        M('Shop_banner')->add(array('member_id'=>$seller_id));
        return array(
            'status' => 1,
            'msg'    => '增加店铺信息成功'
        );     
  }

   /**
    * 卖家店铺信息 更新
    * @access public
    * @param  array $shop_data 店铺信息
    * @return array $result    执行结果
    */  
    public function shopDataUpdate($shop_data){
    	//数据过滤
        $data_filter = array(
            'member_id'      => '',//卖家id
            'shop_name'      => '',//店铺名字
            'desc'           => '',//店铺描述
            'domain'         => '',//店铺域名
            'logistical'     => '',//物流服务
            'service'        => '',//服务态度
            'desc_score'     => '',//店铺域名
            'comment_number' => '',//店铺评论数量
            'qq'             => '', //店铺qq
            'is_sign'        => '',
            'thumb'          => ''
        );
        $data  = array_intersect_key($shop_data , $data_filter); //获取键的交集  
        $id    = $shop_data['id'];
        $r     = M('Shop_data')->where(array('id' => $id))->save($data);//修改数据到商品表
        if($r === false){
            return array(
               'status' => 0,
               'msg'    => '商品插入数据库失败'
            );           
        }
        $seller_id = M('Shop_data')->where(array('id' => $id))->getField('member_id');
        return array(
            'status'    => 1,
            'seller_id' => $seller_id,
            'msg'       => 'ok'
        ); 
    } 

   /**
    * 检测店铺信息合法性
    * @access public
    * @param  array $data    店铺信息数据
    * @param  int   $type    信息处理类型 1用于增加 2用于编辑修改
    * @return array $result  执行结果
    */     
    public function checkShopData($data , $type = 1){
        if($type == 2 && intval($data['id']) == 0){
            return array('status' => '0' , 'msg' => 'id错误');
        }
        /*检测二级域名*/
        if(!($type == 2 && !isset($data['domain']))){
            $r = $this->checkDomain($data['domain'] , $id);
            if($r['status'] == 0){
                return $r;
            }
        }
        $validate_model = $type == 1 ? self::MUST_VALIDATE : self::EXISTS_VALIDATE;
        /*验证数据*/
        $model  = D('Mall_application');
        $rules  = array(
            array('member_id','/^([1-9]\d*)|0+$/','商家id错误',$validate_model),
            array('shop_name','require','店铺名字',$validate_model),
            array('desc','require','店铺描述',$validate_model),
            array('is_sign','/^0|1$/','签约状态不正确',$validate_model)
        );
        if($model->validate($rules)->create($data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $model->getError()
            );
            return $result;
        }
        return array(
            'status' => 1
        );
    }

   /**
    * 检测店铺二级域名 合法性
    * @access public
    * @param  array $domain 域名名字
    * @param  array $id     店铺id
    * @return array $result 
    */     
    public function checkDomain($domain , $id){
    	/*不能使用的域名*/
    	$no_use = array('admin','houtai','shop','home','wechat');
        if(in_array($domain , $no_use)){
            return array('status' => 0 , 'msg' => '该域名不能使用');
        }
        if(!preg_match('/^[0-9a-zA-Z]+$/', $domain)){
            return array('status' => 0 , 'msg' => '域名格式错误');
        }
        $r = M('Shop_data')->where(array('domain'=>$domain))->getField('id');
        if($id){
	        if($r && $r != $id){
	            return array('status' => 0 , 'msg' => '该域名已经存在');
	        }        	
        }else{
	        if($r){
	            return array('status' => 0 , 'msg' => '该域名已经存在');
	        }        	
        }
        return array('status' => 1);
    }

   /**
    * 根据会员账号id获取店铺信息
    * @access public
    * @param  int|array $member_id 会员id
    * @return array     $data 执行结果
    */
    public function getShopDataByMemberId($member_id , $field = '*'){
    	if(empty($member_id)){
            return array();
    	}
    	if(is_array($member_id)){
    		$member_id = implode(',' , $member_id);
    	}
        $data = M('Shop_data')
            ->where(array('member_id'=>array('in' , $member_id)))
            ->field($field)
            ->select();
        return $data;
    }    
}