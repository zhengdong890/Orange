<?php
namespace Shop\Addons;
use Think\Controller;
/*
 * 卖家缓存管理
 * */
class SellerAddon extends Controller{
    /**
     * 更新所有的卖家店铺信息
     */
    public function allShopDataUpdate(){
        $redis = new \Com\Redis();
        $shop_data_ = M('Shop_data')->field('member_id,domain,shop_name,status')->select();
        foreach($shop_data_ as $k => $v){
            $shop_data[$v['member_id']] = $v;
        }
        $redis->set('all_shop_data' , serialize($shop_data));//设置redis的缓存
    }

    /**
     * 更新一家的店铺信息
     */
    public function allShopDataUpdateOne($seller_id){
        if(!empty($seller_id)){
            $redis = new \Com\Redis();
            $data  = M('Shop_data')
                ->field('member_id,domain,shop_name,status')
                ->where(array('member_id'=>$seller_id))
                ->find();
            $shop_data = $redis->get('all_shop_data', 'array');
            $shop_data[$seller_id] = $data;  
            $redis->set('all_shop_data' , serialize($shop_data));//设置redis的缓存
        }
    }

    /**
     * 根据店铺账号id设置店铺数据 缓存处理
     */    
    public function setShopDataById($seller_id){
        if(!empty($seller_id)){
            $redis = new \Com\Redis();
            $shop_data = M('Shop_data')
                ->where(array('member_id' => $seller_id))
                ->find();
            $comment_number = $shop_data['comment_number']?$shop_data['comment_number']:1;
            $shop_data['logistical'] = sprintf("%.1f" , $shop_data['logistical']/$comment_number);
            $shop_data['service'] = sprintf("%.1f" , $shop_data['service']/$comment_number);
            $shop_data['desc_score'] = sprintf("%.1f" , $shop_data['desc_score']/$comment_number);
            $shop_data['address'] = M('Businesses_application')->where(array('member_id'=>$seller_id))->getField('address');
            $redis->set('shop_data'.$seller_id , serialize($shop_data));//设置redis的缓存
        }
    }

    /**
     * 获取商家的导航分类  缓存处理
     */
    public function getNav($seller_id){
        $redis = new \Com\Redis();
        $navs  = $redis->get('shop_nav'.$seller_id , 'array');//获取redis的缓存
        if(!$navs && $seller_id){
            $navs  = M('Shopping')
                   ->where(array('member_id'=>$seller_id,'status'=>1))
                   ->order('rsort asc')
                   ->select();  
            $redis->set('shop_nav'.$seller_id , serialize($navs));//设置redis的缓存
        }
    }
    
    /**
     * 获取商家的导航分类样式  缓存处理
     */
    public function getNavCss($seller_id){
        $redis = new \Com\Redis();
        $nav_css = $redis->get('nav_css'.$seller_id , 'array');//获取redis的缓存
        if(!$nav_css && $seller_id){
            $nav_css = M('Shop_nav_css')
                     ->where(array('member_id'=>$seller_id))
                     ->getField('background_color');      
            $redis->set('nav_css'.$seller_id , serialize($nav_css));//设置redis的缓存
        }
    }
}