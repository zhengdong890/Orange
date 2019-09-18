<?php
/*
 * 店铺导航管理
 * */  
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class ShopNavController extends Controller {	
	public function _initialize(){
        if(empty($_SESSION['member_data'])){
            header("Location:http://www.orangesha.com/login.html");
        }
        $redis = new \Com\Redis();
          /*底部帮助*/
        Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
        Hook::listen('getFooterHelp');
         $help = $redis->get('footer_help' , 'array');//获取redis的缓存
        /*获取所有店铺的二级域名  更新*/
        $all_shop_domain = $redis->get('all_shop_data' , 'array');//获取redis的缓存
        $id = $_SESSION['member_data']['id'];
        $this->assign('domain' , $all_shop_domain[$id]['domain']?$all_shop_domain[$id]['domain']:$id);
         $this->assign('help' , $help);
    }
    
    /*
     * 导航列表
     * */
    public function navList(){
        if(IS_POST){
            $data   = I(); 
            $result = array('status'=>1,'msg'=>'ok');
            if($data['update']){
                $result = D('ShopNav')->navsUpdate($data['update'] , $_SESSION['member_data']['id']);
            }
            if($data['add']){
                $result = D('ShopNav')->navsAdd($data['add'] , $_SESSION['member_data']['id']);
            }
            if($result['status']){
                $redis = new \Com\Redis();
                $redis->redis->delete('shop_nav'.$_SESSION['member_data']['id']);
            }
            $this->ajaxReturn($result);
        }else{
            $member_id = $_SESSION['member_data']['id'];
            $categorys = M('Shop_category')
                       ->where(array('member_id'=>$member_id))
                       ->select();
            $navs_     = M('Shopping')->where(array('member_id'=>$member_id))->select();
            foreach($navs_ as $v){
                $navs[$v['cat_id']] = $v;   
            }
            foreach($categorys as $k => $v){
                if($navs[$v['id']]){
                    $categorys[$k]['rsort']  = $navs[$v['id']]['rsort'];
                    $categorys[$k]['check']  = $navs[$v['id']]['status']?'checked':'';
                    $categorys[$k]['nav_id'] = $navs[$v['id']]['id'];                    
                }
                $categorys[$k]['status'] = $navs[$v['id']]['status'];               
            }
            $css = M('shop_nav_css')->where(array('member_id'=>$member_id))->getField('background_color');
            $this->assign('categorys' , get_child($categorys));
            $this->assign('css' , $css);
            $this->assign('nav_json' , json_encode($navs));
            $this->assign('categorys_json' , json_encode($categorys));
            $this->display();            
        }
    }
    
    public function navCss(){
        if(IS_AJAX){
            $color = I('post.color');
            $member_id = $_SESSION['member_data']['id'];
            $rel = M('shop_nav_css')->where(array('member_id'=>$member_id))->setField('background_color',$color);
            if( $rel ){
                $redis = new \Com\Redis();
                $redis->redis->delete('nav_css'.$_SESSION['member_data']['id']);
                $this->ajaxReturn(array('status'=>1,'msg'=>'ok'));
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'error'));
            }
             
        }
    }
}