<?php
namespace Home\Addons;
use Think\Controller;
/*
 * 商城商品缓存处理
 * */
class MallGoodsAddon extends Controller{
    /**
     * 主页商城商品redis缓存处理
     */
    public function getIndexGoods(){
        $redis = new \Com\Redis();
        $mall_goods = $redis->get('index_mall_goods' , 'array');//获取redis的缓存
        if(!$mall_goods){
            $goods_ids = M('Mall_goods_model')
                       ->where(array('id'=>1))
                       ->getField('goods_ids');
            //取出商品
            $goods  = M("Mall_goods")
                    ->where(array('id'  => array('in',$goods_ids),'status'=>1))
                    ->order('id desc,sort')
                    ->select();       
            $redis->set('index_mall_goods',$goods);//设置redis的缓存
        }
    }
    
    /**
     * 主页商城商品redis缓存更新
     */    
    public function IndexGoodsUpdate($id){
        $redis = new \Com\Redis();
        $mall_goods = $redis->get('index_mall_goods' , 'array');//获取redis的缓存
        foreach($mall_goods as $k=>$v){
            if($v['id'] == $id){
                $redis->redis->delete('index_mall_goods');
            }
        }        
    }
}