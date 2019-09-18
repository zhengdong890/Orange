<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class MallApplicationController extends Controller { 
   /**
    * 商城申请列表
    * @access public
    */
    public function mallApplicationList(){
    	if(IS_AJAX){
            $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
            $listRows = intval(I('listRows'))?intval(I('listRows')):10;
            $condition = array('status'=>0);
            $list_ = M('Mall_application')->limit($firstRow,$listRows)->order('id desc')->where($condition)->select();
            $total = M('Mall_application')->where($condition)->count();
            $ids   = array();
            foreach($list_ as $v){
               $ids[]  = $v['seller_id'];
               $list[$v['seller_id']] = $v;
            }
            $ids  = implode(',' , $ids);
            $data = M('Businesses_application')
                 ->where(array('member_id'=>array('in',$ids)))
                 ->field("member_id,name")
                 ->select();
            foreach($data as $v){
               $list[$v['member_id']]['name'] = $v['name'];
            }
            $this->ajaxReturn(array('data'=>$list,'total'=>$total));
    	}else{
    		$this->display();
    	}
    }
   
   /**
    * 获取商城审核列表数据
    * @access public
    */
   public function getmallApplications(){
       if(IS_AJAX){
           $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
           $listRows = intval(I('listRows'))?intval(I('listRows')):10;
           $list_ = M('Mall_application')->limit($firstRow,$listRows)->order('id desc')->select();
           $total = M('Mall_application')->count();
           $ids   = array();
           foreach($list_ as $v){
               $ids[]  = $v['seller_id'];
               $list[$v['seller_id']] = $v;
           }
           $ids  = implode(',' , $ids);
           $data = M('Businesses_application')
                 ->where(array('member_id'=>array('in',$ids)))
                 ->order('id desc')
                 ->field("member_id,name")
                 ->select();
           foreach($data as $v){
               $list[$v['member_id']]['name'] = $v['name'];
           }
           $this->ajaxReturn(array('data'=>$list,'total'=>$total));
       }
   }
   
   /**
    * 获取店铺详细数据
    * @access public
    */   
   public function getShopData(){
       if(IS_AJAX){
           $seller_id   = intval(I('seller_id'));//卖家账号id
           if($seller_id){           
               $shop_data   = M('Shop_data')->where(array('member_id'=>$seller_id))->find();
               $seller_data = M('Businesses_application')->where(array('member_id'=>$seller_id))->find();
           }           
           $this->ajaxReturn(array($shop_data?$shop_data:array() , $seller_data?$seller_data:array()));
       }    
   }
   
   /**
    * 商城申请审核
    * @access public
    */
    public function mallApplication(){
       if(IS_AJAX){
            $data   = I();
            //卖家账号的id
            $seller_id = M('Mall_application')->where(array('id'=>$data['id']))->getField('seller_id');
            /*检测商城申请的数据*/
            $result = D('MallApplication')->checkMallApplication($data);
            if(!$result['status']){//数据验证未通过
               $this->ajaxReturn($result);die;
            }    
            /*检测店铺基本信息合法性*/
            $shop_id = M('Shop_data')->where(array('member_id'=>$seller_id))->getField('id');
            if(!$shop_id && $data['status'] == 1){//审核通过
            	$data['member_id'] = $seller_id;
	            $r = D('ShopData')->checkShopData($data , 1);
	            if($r['status'] == 0){
	                $this->ajaxReturn($r);
	            }
            }
            /*商城申请审核*/
            $result = D('MallApplication')->mallApplicationCheck($data , $seller_id);
            if(!$result['status'] || !intval($data['status'])){//审核失败 or 认证未通过
                $this->ajaxReturn($result);die;
            }            
            /*****审核成功 店铺信息图片上传*****/              
            //上传图片
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728 ;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
            // 上传文件
            $info = $upload->upload();
            if($info){
                if($info['thumb']){//获取缩略图片路径
                   $data['thumb'] = $upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];
                }
            }else
		    if($upload->getError() != '没有文件被上传！' && $id){
		        $this->ajaxReturn(array(
		           'status' => 0,
		           'msg'    => $upload->getError()		           
		        ));die;
		    }
           	//添加店铺的信息
           	if(!$shop_id && $data['status'] == 1){
           	    $result = D('ShopData')->shopDataAdd($data);//增加 	
           	}             
            /*更新缓存*/                         
            if($result['status']){
                Hook::add('allShopDataUpdateOne','Shop\\Addons\\SellerAddon');
                Hook::listen('allShopDataUpdateOne' , $seller_id);
                Hook::add('setShopDataById','Shop\\Addons\\SellerAddon');
                Hook::listen('setShopDataById' , $seller_id); 
            }
            $this->ajaxReturn($result);
        }else{
            $id   = I('id');
            $data = M('Mall_application')->where(array('id'=>$id))->find();
            $seller_data = M('Businesses_application')->where(array('member_id'=>$data['seller_id']))->find();
            $shop_data   = M('Shop_data')->where(array('member_id'=>$data['seller_id']))->find();
            $this->assign('shop_data' , $shop_data);
            $this->assign('data' , $data);
            $this->assign('seller_data' , $seller_data);
            $this->display();           
        }
    }  
}