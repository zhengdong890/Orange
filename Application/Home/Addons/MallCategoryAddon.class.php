<?php
namespace Home\Addons;
use Think\Controller;
/*
 * 商城商品分类缓存处理
 * */
class MallCategoryAddon extends Controller{
    /**
     * 商城商品分类redis缓存处理
     */
    public function getCategory(){
        $redis = new \Com\Redis();
        $categorys = $redis->get('mall_category' , 'array');//获取redis的缓存
        //dump($categorys);
        if(!$categorys){
            //获取商品分类
            $categorys  = M("Mall_category")
                        ->where(array('status'=>'1'))
                        ->order('sort')
                        ->select();
            $mall_category_tree = array();
            foreach($categorys as $v){
                if($v['level'] != '4'){
                	$mall_category_tree[] = $v;
                }
            }  
            $mall_category_tree = get_child($mall_category_tree);
          
            //设置redis的缓存                    
            $redis->set('mall_category_tree',serialize($mall_category_tree));
            $redis->set('mall_category',serialize($categorys));//设置redis的缓存
        }
    }

    /**
     * 商城商品分类redis缓存更新
     */
    public function updateCategory(){
        $redis = new \Com\Redis();
        //获取商品分类
        $categorys  = M("Mall_category")
                    ->where(array('status'=>'1'))
                    ->order('sort')
                    ->select();
        $mall_category_tree = array();
        foreach($categorys as $v){
            if($v['level'] != '4'){
                $mall_category_tree[] = $v;
            }
        }  
        $mall_category_tree = get_child($mall_category_tree);
        //设置redis的缓存                    
        $redis->set('mall_category_tree',serialize($mall_category_tree));
        $redis->set('mall_category',serialize($categorys));//设置redis的缓存
    }

    /**
     * 商城商品 设备商城 redis缓存处理
     */
    public function getEquipmentCategory(){
    	$this->getCategory();//分类缓存更新
    	$redis = new \Com\Redis();
        $categorys          = $redis->get('index_mall_category' , 'array');//获取所有分类
        $equipment_category = array();
        $in_array = array(2,3,4,5,34);
        foreach($categorys as $v){
            if(in_array($v['id'] , $in_array) || in_array($v['pid'] , $in_array)){
                $equipment_category[] = $v;
            }
        }        
        $redis->set('equipment_mall_category',serialize($equipment_category));//设置redis的缓存
    }

    /**
     * 商城商品 工具超市 redis缓存处理
     */
    public function getToolCategory(){
        $this->getCategory();//分类缓存更新
        $redis     = new \Com\Redis();
        $categorys = $redis->get('index_mall_category' , 'array');//获取所有分类
        $tool      = array();
        $in_array  = array(43 , 50);
        foreach($categorys as $v){
            if(in_array($v['id'] , $in_array) || in_array($v['pid'] , $in_array)){
                $tool_category[] = $v;
            }
        }        
        $redis->set('tool_mall_category',serialize($tool_category));//设置redis的缓存
    }    
}
