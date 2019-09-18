<?php
/*
 * 商家店铺管理模块
 * */  
namespace Admin\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class ShopDataController extends Controller {  
   /**
    * 商家店铺页
    * @access public
    */   
   public function sellerList(){
       $this->display();
   }
   
   /*获取商家店铺*/
   public function getShopListData(){
       if(IS_AJAX){
           $shop_name = I('shop_name');
           $where['shop_name'] = array('like',"%$shop_name%");
           $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
           $listRows = intval(I('listRows'))?intval(I('listRows')):10;
           $list_    = M('Shop_data')->order('id desc')->where($where)->limit($firstRow,$listRows)->select();
           $seller_ids = array();
           $list       = array();
           foreach($list_ as $v){
               array_push($seller_ids , $v['member_id']);
               $list[$v['member_id']] = $v;
           }
           $seller_ids = implode(',' , $seller_ids);
           $counts     = M('Mall_goods')
                       ->where(array('member_id'=>array('in' , $seller_ids)))
                       ->field('member_id,count(member_id) as goods_number')
                       ->group('member_id')
                       ->select();         
           foreach($counts as $v){
               $list[$v['member_id']]['goods_number'] = $v['goods_number'];
               array_push($seller_ids , $v['member_id']);
           }
           $this->ajaxReturn(array('data'=>$list,'total'=>M('Shop_data')->where($where)->count()));
       }
   }

   /*
    * 店铺详情
    * */
    public function shopDetail(){
        $member_id   = intval(I('member_id'));
        $shop_data   = M('Shop_data')->where(array('member_id'=>$member_id))->field("shop_name")->find();
        $member_data = M('member_data')->where(array('member_id'=>$member_id))->field("telnum")->find();
        $data = array('shop_data'=>$shop_data,'member_data'=>$member_data);
        $this->assign('data' , $data);
        $this->display();
    }
   
   /*更改店铺状态*/
    public function changeShopStatus(){
        if(IS_AJAX){
            $id     = I('id');
            $status = I('status');
            $r = M('Shop_data')->where(array('id'=>$id))->save(array('status'=>$status));
            if($r === false){
                $this->ajaxReturn(array('status'=>0,'msg'=>'error'));
            }else{
                $seller_id = M('Shop_data')->where(array('id'=>$id))->getField('member_id');
                $redis = new \Com\Redis();
                /*更新一家店铺信息的缓存*/
                Hook::add('allShopDataUpdateOne','Shop\\Addons\\SellerAddon');
                Hook::listen('allShopDataUpdateOne' , $seller_id);
                Hook::add('setShopDataById','Shop\\Addons\\SellerAddon');
                Hook::listen('setShopDataById' , $seller_id);                   
                $this->ajaxReturn(array('status'=>1,'msg'=>'ok'));
            }
       }
    }

   /*
    * 修改店铺信息
    * */   
    public function shopUpdate(){
        if(IS_AJAX){
            $data = I();
            unset($data['thumb']);
            /*检测数据合法性*/
            $r = D('ShopData')->checkShopData($data , 2);
            if($r['status'] == 0){
                $this->ajaxReturn($r);
            }
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
            if($upload->getError() != '没有上传的文件！'){
                $this->ajaxReturn(array(
                     'status' => 0,
                     'msg'    => $upload->getError()               
                ));die;
            }
            $r = D('ShopData')->shopDataUpdate($data);
            if($r['status']){ 
                $redis = new \Com\Redis();
                /*更新一家店铺信息的缓存*/
                Hook::add('allShopDataUpdateOne','Shop\\Addons\\SellerAddon');
                Hook::listen('allShopDataUpdateOne' , $r['seller_id']);
                Hook::add('setShopDataById','Shop\\Addons\\SellerAddon');
                Hook::listen('setShopDataById' , $r['seller_id']);  
            }     
            $this->ajaxReturn($r);       
        }else{
            $member_id = intval(I('seller_id'));
            if($member_id == 0){
                exit('id错误');
            }
            $data = M('Shop_data')
                ->where(array('member_id'=>$member_id))
                ->find();
            if(empty($data)){
                exit('店铺不存在');
            }
            $this->assign('data' , $data);
            $this->assign('json_data' , json_encode($data));
            $this->display();            
        }     
    }
}