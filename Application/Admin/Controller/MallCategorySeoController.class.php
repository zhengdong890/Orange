<?php
/*
 * 商城商品分类seo
 * */
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class MallCategorySeoController extends CommonController {
    public function categoryList(){
    	if(IS_POST){
   	    	$cat_id  = I('id');
   	    	$cats    = D('Mall_category')->getNextCategory($cat_id);
            $cat_ids = implode(',' , array_column($cats, 'id'));
            if(!$cat_ids){
                $this->ajaxReturn(array(
                    'status' => 1,
                    'msg'    => 'ok',
                    'data'   => $cat_ids
                ));
            }
            $seo = M('Mall_category_seo')
		         ->where(array('cat_id' => array('in' , $cat_ids)))
		         ->select();
		    $seo = array_all_column($seo , 'cat_id');
            foreach($cats as &$v){
                 $v['seo'] = $seo[$v['id']] ? $seo[$v['id']] : array();
            }
            $this->ajaxReturn(array(
                'status' => 1,
                'msg'    => 'ok',
                'data'   => $cats,               
            ));
   	    }else{
   	    	$this->display();
   	    }      
    } 

    public function seoUpdate(){
    	if(IS_AJAX){
            $data   = I();
            if($data['id']){
                $result = D('MallCategorySeo')->seoUpdate($data);
            }else{
            	$result = D('MallCategorySeo')->seoAdd($data);
            }           
            if($result['status']){
            	$cat_id = M('Mall_category_seo')
		            	->where(array('id'=>$data['id']))
		            	->getField('cat_id');
                /*更新缓存*/
                $redis = new \Com\Redis();
                $redis->redis->delete('mall_category_seo' . $cat_id);
            }
            $this->ajaxReturn($result);
    	}else{
            $id     = I('id');
    		$cat_id = I('cat_id');
    		if($id){
                $data = M('Mall_category_seo')
	                  ->where(array('id' => $id))
	                  ->find();	                  
    		}else{
    			$data = array('cat_id'=>$cat_id);
    		}
    		$this->assign('cat_seo' , $data);
   	        $this->display();
   	    }  
    }
}