<?php
namespace Home\Addons;
use Think\Controller;
/*
 * 导航缓存管理
 * */
class NavAddon extends Controller{
    /**
     * 主页共享商品分类redis缓存处理
     */
    public function getNav(){
        $redis = new \Com\Redis();
        $navs  = $redis->get('navs' , 'array');//获取redis的缓存
        if(!$navs){
            $navs = M('Nav')->where(array('status'=>1))->order('sort')->select();
            $redis->set('navs',serialize($navs));//设置redis的缓存
        }
    }
}