<?php
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class CollectController extends Controller {	
    public function _initialize(){
        if(empty($_SESSION['member_data'])){
            $this->ajaxReturn(array(
                'status' => 0,
                'msg'   => '请登录'
            ));die;
        }
    }

    /*店铺收藏*/
    public function shopcllect(){       
        if(IS_AJAX){
            $data['member_id'] = $_SESSION['member_data']['id'];

            if(!$data['member_id']){
                $this->ajaxReturn(array('msg'=>'nologin'));
            }
            $data['seller_id'] = I('seller_id');
            $data['status'] = 1;
            $data['time'] = time();
            $shop=M('shop_data')->where(array('member_id'=>$data['seller_id']))->find();
            $data['thumb']=$shop['thumb'];
            $data['shop_name']=$shop['shop_name'];
            $data['domain']=$shop['domain'];
            $data['desc']=$shop['desc'];
            $is_set = M('shop_collect')->where(array('seller_id'=>$data['seller_id'],'member_id'=>$data['member_id']))->find();
            if( $is_set ){
                $this->ajaxReturn(array('msg'=>'onemore'));
            }else{
                $addData = M('shop_collect')->add($data);
                if( $addData ){
                    $this->ajaxReturn(array('msg'=>'ok'));
                }
                    
            }  
        }
    }
    //收藏商品
     public function goodcllect(){      
        if(IS_AJAX){
            $data['member_id'] = $_SESSION['member_data']['id'];
            
            if(!$data['member_id']){
                $this->ajaxReturn(array('msg'=>'nologin'));//请登入
            }
            $data['goods_id'] = I('goods_id');
            $data['status'] = 1;
            $data['time'] = time();
            $ma=M('mall_goods')->where(array('id'=>$data['goods_id']))->find();
            $data['goods_name']=$ma['goods_name'];
            $data['goods_price']=$ma['goods_price'];
            $data['goods_thumb']=$ma['goods_thumb'];
            $data['goods_number']=$ma['goods_number'];
            $is_set = M('goods_collect')->where(array('goods_id'=>$data['goods_id'],'member_id'=>$data['member_id']))->find();
            if( $is_set ){
                $this->ajaxReturn(array('msg'=>'onemore'));
            }else{
                $addData = M('goods_collect')->add($data);
                if( $addData ){
                    $this->ajaxReturn(array('msg'=>'ok'));
                }
                    
            }  
        }
    }
    
    /*
     * 获取收藏的店铺
     * */
    public function shopCollect(){
        if(IS_AJAX){
            $member_id    = $_SESSION['member_data']['id'];
            $collect_shop = M('Shop_collect')->where(array('member_id'=>$member_id))->select();
            $seller_id    = array_column($collect_shop, 'seller_id');
            $seller_id    = implode($seller_id , ',');
            $shop_data_   = M('Shop_data')->where(array('member_id'=>array('in',$seller_id)))->select();
            foreach($shop_data_ as $v){
                $shop_data[$v['member_id']] = $v;
            }
            foreach($collect_shop as $k => &$v){
                $v['shop_name'] = $shop_data[$v['seller_id']]['shop_name'];
            }
            $this->ajaxReturn(array('status'=>1,'msg'=>'ok','data'=>$collect_shop));
        }
    }
    
    /*
     * 获取收藏的商品
     * */
    public function goodsCollect(){
        if(IS_AJAX){
            $member_id     = $_SESSION['member_data']['id'];
            $collect_goods = M('Goods_collect')->where(array('member_id'=>$member_id))->select();
            /*提取收藏的共享商品 和商城商品*/
            $goods          = array();
            $mall_goods     = array();
            $goods_ids      = array();
            $mall_goods_ids = array();
            foreach($collect_goods as $v){
                if($v['is_mall'] == 1){
                    $mall_goods[$v['goods_id']] = $v;
                    $goods_ids[]                = $v['goods_id'];
                }else
                if($v['is_mall'] == 0){
                    $goods[$v['goods_id']] = $v;
                    $mall_goods_ids[]      = $v['goods_id'];
                }
            }
            /*获取共享商品信息*/
            $goods_ids    = implode($goods_ids , ',');
            $goods_data   = M('Goods')->where(array('id'=>array('in',$goods_ids)))->select();
            /*获取商城商品信息*/
            $mall_goods_ids  = implode($mall_goods_ids , ',');
            $mall_goods_data = M('Mall_goods')->where(array('id'=>array('in',$mall_goods_ids)))->select();
            $all_goods = array('share'=>$goods_data,'mall'=>$mall_goods_data);
            $this->ajaxReturn(array('status'=>1,'msg'=>'ok','data'=>$all_goods));
        }
    }    
}
