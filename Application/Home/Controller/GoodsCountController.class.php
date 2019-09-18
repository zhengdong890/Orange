<?php
/*
 * 设备商城
 * */
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class GoodsCountController extends Controller {

	public function index(){
        set_time_limit(0);
        $cate = M("Mall_category")->field('id')->order('id')->where(array('status'=>1))->select();
        foreach($cate as $k=>$v){
            $cat_id = $v['id'];
            $number = M('Mall_goods as a')
                                    ->join('tp_shop_data as s ON s.member_id=a.member_id')
                                    ->join('left join tp_sku as k on k.goods_id=a.id')
                                    ->where(array('s.status'=>1,'a.status'=>1,'cat_id'=>$cat_id))
                                    ->count();//统计商品数量
           $add= M('Mall_category')
                   ->where(array('id'=>$cat_id))
                   ->save(array('cat_id'=>$cat_id,'num'=>$number));
           echo $add;        
       }

}

}	