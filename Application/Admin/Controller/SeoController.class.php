<?php
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class SeoController extends CommonController {
  /**
   * 导航seo列表页
   * @access public  
   */ 
   public function navList(){ 
       $list = M('Nav')->select();
       $this->assign('list' , $list);
       $this->display();
   }

  /**
   * 导航seo修改
   * @access public  
   */    
   public function navSeoUpdate(){
       if(IS_AJAX){
           $data   = I();
           $result = D('Nav')->navSeoUpdate($data);
           if($result['status']){
                /*更新缓存*/
                $redis = new \Com\Redis();
                $redis->redis->delete('nav_seo' . $data['id']);
           }
           $this->ajaxReturn($result);
       }else{
           $id   = I('id');
           $data = M('Nav')->where(array('id'=>$id))->find();
           $this->assign('data' , $data); 
           $this->display();
       }
   }
   
    public function categoryList(){
        $data = D('Category')->getCategory();
        $seo_ = M('Seo_category')->select();
        foreach($seo_ as $k => $v){
            $seo[$v['cat_id']] = $v; 
        }
        foreach($data as $k => $v){
            $data[$k]['seo'] = $seo[$v['id']];
        }
        $list = tree_1($data);
        $this->assign('list' , $list);
        $this->display();
    }   

    public function webCategory(){
        $cat_id = intval(I('cat_id'));
        if($cat_id){
            $cat_name = M('Category')->where(array('id'=>$cat_id))->getField('cat_name');
            $cat_seo  = M('Seo_category')->where(array('cat_id'=>$cat_id))->find();
            if(!$cat_seo){
                $cat_seo['id'] = M('Seo_category')->where(array('id'=>$cat_id))->add(array('cat_id'=>$cat_id));
            }
            $this->assign('cat_name' , $cat_name);
            $this->assign('cat_seo' , $cat_seo);
            $this->display();
        }
    }
    
    public function seoCatUpdate(){
        if(IS_AJAX){
            $data   = I();
            $result = D('SeoCategory')->seoCatUpdate($data);
            if($result['status'] && $result['number'] > 0){
                /*更新缓存*/
                $redis = new \Com\Redis();
                $redis->redis->delete('seo_category' . $data['cat_id']);
            }
            $this->ajaxReturn($result);            
        }
    }
}