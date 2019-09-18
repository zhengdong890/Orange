<?php
namespace Home\Addons;
use Think\Controller;
/*
 * 商城商品统计处理
 * */
class MallCountAddon extends Controller{
	public function mall_goods_count(){
		$redis = new \Com\Redis();
        $goods = $redis->get('goods' , 'array');//获取redis的缓存
        if(!$goods){
        	$goods = M('Mall_goods')
                    ->where(array('status'=>1))
                    ->select();
        $redis->set('goods',serialize($goods));//设置redis的缓存
        }
	}
}