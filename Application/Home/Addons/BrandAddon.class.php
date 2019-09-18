<?php
namespace Home\Addons;
use Think\Controller;
/*
 * 商品分类 品牌缓存处理
 * */
class BrandAddon extends Controller{
    /**
     * 商品品牌增加 缓存
     */
    public function brandAdd(){
        $redis  = new \Com\Redis();
        $brands = M('Goods_brand')
                ->where(array('status'=>1))
                ->field('id,brand_name,brand_thumb')
                ->select(); 
        $brands = array_all_column($brands, 'id');         
        $redis->set('brands',serialize($brands));//设置redis的缓存
    }

    /**
     * 商品分类品牌 增加 缓存
     */
    public function mallCategoryBrandAdd(){
        $redis      = new \Com\Redis();
    	$brands     = $redis->get('brands', 'array');
    	$brands     = array_column($brands , 'brand_name' , 'id');
        $cat_brands = M('Mall_category_brand')->select(); 
        foreach($cat_brands as &$v){
            $v['brand_name'] = $brands[$v['brand_id']];    
        }
        $redis->set('mall_category_brands',serialize($cat_brands));//设置redis的缓存
    }    

    /**
     * 商品品牌 redis缓存更新处理
     */
    public function updateBrand(){
        $redis  = new \Com\Redis();
        $brands = $redis->get('brands' , 'array');//获取redis的缓存
        if(!$brands){//缓存不存在则更新
            $brands    = M('Goods_brand')
                       ->where(array('status'=>1))
                       ->field('id,brand_name,brand_thumb')
                       ->select(); 
            $brands    = array_all_column($brands, 'id');
            $redis->set('brands',serialize($brands));//设置redis的缓存
        }
    }

    /**
     * 商城商品分类品牌 redis缓存更新处理
     */
    public function updateMallCategoryBrand(){
        $redis      = new \Com\Redis();
        $cat_brands = $redis->get('mall_category_brands' , 'array');//获取redis的缓存
        if(!$cat_brands){//缓存不存在则更新
        	$this->updateBrand();
        	$brands     = $redis->get('brands', 'array');
        	$brands     = array_column($brands , 'brand_name' , 'id');
            $cat_brands = M('Mall_category_brand')->select(); 
	        foreach($cat_brands as &$v){
	            $v['brand_name'] = $brands[$v['brand_id']];    
	        }
            $redis->set('mall_category_brands',serialize($cat_brands));//设置redis的缓存
        }      
    } 

    /**
     * 共享商品分类品牌 redis缓存更新处理
     */
    public function updateCategoryBrand(){
        $redis      = new \Com\Redis();
        $cat_brands = $redis->get('category_brands' , 'array');//获取redis的缓存
        if(!$cat_brands){//缓存不存在则更新
            $cat_brands = M('Category_brand')->select();
            $redis->set('category_brands',serialize($cat_brands));//设置redis的缓存
        }      
    }       
}