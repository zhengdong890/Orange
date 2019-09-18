<?php
namespace Home\Addons;
use Think\Controller;
/*
 * seo缓存处理
 * */
class SeoAddon extends Controller{
    /**
     * 导航seo redis缓存处理
     */
    public function navSeo($param){
    	list($redis , $nav_id) = $param;
        $data  = $redis->get('nav_seo' . $nav_id , 'array');//获取redis的缓存
        if(!$data){
            $data = M('Nav')->where(array('id'=>$nav_id))->find();
            $redis->set('nav_seo' . $nav_id , serialize($data));//设置redis的缓存
        }
    }
  
    
    /**
     * 共享商品分类seo redis缓存处理
     */
    public function categorySeo($arr){
        $redis  = $arr[0];
        $cat_id = $arr[1];
        $data = $redis->get('seo_category' . $cat_id  , 'array');//获取redis的缓存
        if(!$data){
            $data = M('Seo_category')->where(array('cat_id'=>$cat_id))->find();
            $redis->set('seo_category' . $cat_id ,serialize($data));//设置redis的缓存
        }
    }    
}