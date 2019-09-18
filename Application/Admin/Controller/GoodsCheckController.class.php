<?php
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class GoodsCheckController extends CommonController{	
    
/*******************************************未通过审核的商品*****************************************************/
	
	/*
	 * 未通过审核的共享商品页
	 * */	
	public function noPassGoodsList(){
	    if(IS_AJAX){
	        $goods_ids = '';
	        $where     = array();
	        $where['check_status'] = 0;
	        $where['is_check']     = 1;
	        $cat_ids = I('cat_ids');
	        if($cat_ids){
	            $where['cat_id'] = array('in' , $cat_ids);
	        }
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
	            $data = M('Category')->select();
	            $cats = getTree($data,$cat_id);
	            $cats[]['id'] = $cat_id;
	            foreach($cats as $v){
	                $cat_ids[] = intval($v['id']);//分类集组装 转换成int
	            }
	            $where['cat_id'] = array('in' , implode(',' , $cat_ids));//复合条件组装
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
	        $list     = M('Goods')->order('id desc,sort')->limit($firstRow,$listRows)->where($where)->select();
	        $this->ajaxReturn(array('data'=>$list,'total'=>M('Goods')->where($where)->count()));
	    }
	    $this->display();
	}
	
	/*
	 * 获取未通过审核的审核意见
	 * */	
	public function getCheckData(){
	    if(IS_AJAX){
	        $id   = intval(I('id'));
	        $data = M('Goods_check')->where(array('goods_id'=>$id))->field('content,time')->find();
	        $this->ajaxReturn($data);
	    }
	}
	
/*******************************************需要审核的商品*****************************************************/	

	/*
	 * 需要审核的商品页
	 * */
	public function checkList(){
	    if(IS_AJAX){
	        $goods_ids = '';
	        $where     = array();
	        $where['is_check']     = 0;
	        $cat_ids = I('cat_ids');
	        if($cat_ids){
	            $where['cat_id'] = array('in' , $cat_ids);
	        }
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
	            $data = M('Category')->select();
	            $cats = getTree($data,$cat_id);
	            $cats[]['id'] = $cat_id;
	            foreach($cats as $v){
	                $cat_ids[] = intval($v['id']);//分类集组装 转换成int
	            }
	            $where['cat_id'] = array('in' , implode(',' , $cat_ids));//复合条件组装
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
	        $list     = M('Goods')->order('id desc,sort')->limit($firstRow,$listRows)->where($where)->select();
	        $this->ajaxReturn(array('data'=>$list,'total'=>M('Goods')->where($where)->count()));
	    }
	    $this->display();
	}
	
	/*
	 * 审核共享商品
	 * */
	public function goodsCheck(){
	    if(IS_AJAX){
	        $data   = $_POST;
	        $result = D('GoodsCheck')->goodsCheck($data);
	        $this->ajaxReturn($result);
	    }
	}
	
	/*
	 * 获取商品其他数据
	 * */	
	public function getGoodsData(){
	    if(IS_AJAX){
	        $id            = I('id');
	        $goods         = M('Goods')->where(array('id'=>$id))->find();//取出商品
	        $goods_data    = M('Goods_data')->where(array('goods_id'=>$id))->find();
	        //商品品牌
	        $brand_name    = M('Goods_brand')->where(array('id'=>$goods['brand_id']))->getField('brand_name');
	        //商品分类
	        $cat_name      = M('Category')->where(array('id'=>$goods['cat_id']))->getField('cat_name');
	        //取出商品相册
	        $goods_gallery = M('Goods_gallery')->where(array('goods_id'=>$id))->select();
	        //租期
	        $goods_rent    = M("Goods_rent")->where(array('goods_id'=>$id))->select();
	        //区域
	        $area = M('Area')
    	          ->where(array('area_no'=>array('in' , $goods['province'].','.$goods['city'].','.$goods['area'])))
    	          ->field('area_no,area_name,id,area_level')
    	          ->select();
	        $goods_data['goods_content'] = html_entity_decode($goods_data['goods_content']);
	        $goods_data['cat_name'] = $cat_name;
	        $this->ajaxReturn(array(
	            'goods_data'    => $goods_data,
	            'brand_name'    => $brand_name,
	            'goods_gallery' => $goods_gallery,
	            'goods_rent'    => $goods_rent,
	            'area'          => $area
	        ));
	    }
	}
}