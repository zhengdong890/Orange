<?php
namespace Home\Addons;
use Think\Controller;
/*
 * 共享商品缓存处理
 * */
class goodsAddon extends Controller{
    /**
     * 主页共享商品分类redis缓存处理
     */
    public function getIndexGoods(){
        $redis = new \Com\Redis();
        $goods = $redis->get('index_goods' , 'array');//获取redis的缓存
        if(!$goods){
            $number = 16;
            $ids    = M('Goods_model')->where(array('id'=>6))->getField("goods_ids");    
            $goods  = M("Goods")
                    ->where(array('id'=>array('in',$ids),'is_check'=>1,'check_status'=>1,'status'=>1))
                    ->order('sort')
                    ->limit(0,$number)
                    ->select(); 
            $redis->set('index_goods',serialize($goods));//设置redis的缓存
        }
    }
    
    /**
     * 主页共享商品redis缓存更新
     */
    public function IndexGoodsUpdate($id){
        $redis = new \Com\Redis();
        if(!$id){
            $redis->redis->delete('index_goods');
            return;
        }
        $goods = $redis->get('index_goods' , 'array');//获取redis的缓存
        foreach($goods as $k=>$v){
            if($v['id'] == $id){
                $redis->redis->delete('index_goods');
                break;
            }
        }
    
    }    
}