<?php
/*
 * 商城商品seo
 * */
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class MallGoodsSeoController extends Controller {
    /*
     * 获取商品列表
     * */
    public function goodsList(){
	   if(IS_AJAX){
	       $goods_ids = '';
	       $where = array();
	       $where['is_check'] = 1;
	       $where['check_status'] = 1;
	       /*关键字查询*/
	       $keyword = I('keyword');
	       if($keyword){
	           $where_keyword = array(
	               'goods_name' => array('like',"%$keyword%"),
	               'goods_code '=> $keyword
	           );
	           $where_keyword['_logic'] = 'OR';
	       }
	       /*共享商品分类查询*/
	       $cat_id = intval(I('cat_id'));
	       if($cat_id){
	           $data = M('Mall_category')->select();
	           $cats = getTree($data,$cat_id);
	           $cats[]['id'] = $cat_id;
	           foreach($cats as $v){
	               $cat_ids[] = intval($v['id']);//分类集组装 转换成int
	           }
	           $where['cat_id'] = array('in' , implode(',' , $cat_ids));//复合条件组装
	       }
	       /*加入推荐查询*/
	       $model_id = I('model_id');
	       if($model_id){
	           $goods_ids = M('Mall_goods_model')->where(array('id'=>$model_id))->getField('goods_ids');
	       }
	       if(intval($keyword)){
	           $goods_ids .= ',' . intval($keyword);
	       }
	       if($goods_ids){
	           $where_keyword['id'] = array('in' , $goods_ids);
	       }
	       if($where_keyword){
	           $where['_complex'] = $where_keyword;
	       }
	       $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
	       $listRows = intval(I('listRows'))?intval(I('listRows')):10;
	       $list     = M('Mall_goods')
        	         ->order('id desc,sort')
        	         ->limit($firstRow,$listRows)
        	         ->where($where)
        	         ->field('id,goods_name')
        	         ->select();
	       foreach($list as $v){
	           $ids[] = $v['id'];
	       }
	       $ids = implode(',' , $ids);
	       if($ids){
	           $seo = M('Mall_goods_seo')->where(array('goods_id'=>array('in' , $ids)))->select();    
	       }
	       $this->ajaxReturn(array('data'=>$list,'seo'=>$seo,'total'=>M('Mall_goods')->where($where)->count()));	       
	   }else{
	       $this->display();
	   }   	        
    } 
    
    /*
     * 共享商品seo修改
     * */
    public function goodsSeoUpdate(){
        if(IS_AJAX){
            $data = I();
            if(intval($data['id'])){
                $r = D('MallGoodsSeo')->goodsSeoUpdate($data);   
            }else{
                $data['seller_id'] = M('Mall_goods')->where(array('id'=>intval($data['goods_id'])))->getField('member_id');
                if($data['seller_id'] === null){
                    $this->ajaxReturn(array('status'=>0 , 'msg'=>'该商品不存在'));die;    
                }
                $r = D('MallGoodsSeo')->goodsSeoAdd($data);
            }
            $this->ajaxReturn($r);
        }
    }
}
