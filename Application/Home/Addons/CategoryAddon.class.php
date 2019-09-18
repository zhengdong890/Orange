<?php
namespace Home\Addons;
use Think\Controller;
/*
 * 共享商品分类缓存处理
 * */
class categoryAddon extends Controller{
    /**
     * 主页共享商品分类redis缓存处理
     */
    public function getCategory(){
        $redis = new \Com\Redis();
        $categorys = $redis->get('index_cattegory' , 'array');//获取redis的缓存
        if(!$categorys){
            $categorys = M("Category")->where(array('status'=>'1'))->order('sort')->select();//获取商品分类
            $redis->set('index_cattegory',serialize($categorys));//设置redis的缓存
        }
    }
}