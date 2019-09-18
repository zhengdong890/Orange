<?php
namespace Home\Addons;
use Think\Controller;
/*
 * 商城商品分类seo缓存处理
 * */
class MallCategorySeoAddon extends Controller{
    /**
     * 商城商品分类redis缓存处理
     */
    public function getSeo($cat_id){
        $redis = new \Com\Redis();
        $seo   = $redis->get('mall_category_seo' . $cat_id , 'array');//获取redis的缓存
        //if(!$seo){
            //获取商品分类
            $seo  = M("Mall_category_seo")
                  ->where(array('cat_id' => $cat_id))
                  ->find();                     
            $redis->set('mall_category_seo' . $cat_id ,serialize($seo));//设置redis的缓存
        //}
    }
}