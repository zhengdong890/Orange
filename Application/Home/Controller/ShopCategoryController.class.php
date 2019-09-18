<?php
/*
 * 店铺分类管理
 * */  
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class ShopCategoryController extends Controller {	
	public function _initialize(){
        if(empty($_SESSION['member_data'])){
            if(IS_AJAX || IS_POST){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'msg'   => '请登录'
                ));
            }else{
                header("Location:http://www.orangesha.com/login.html");
            }
        }
        if(IS_GET){
	        $redis = new \Com\Redis();
	        /*底部帮助*/
	        Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
	        Hook::listen('getFooterHelp');
	        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
	        $all_shop_domain = $redis->get('all_shop_data' , 'array');//获取redis的缓存
	        /*获取所有店铺的二级域名  更新*/
	        $all_shop_domain = $redis->get('all_shop_data' , 'array');//获取redis的缓存
	        $id = $_SESSION['member_data']['id'];
	        $this->assign('domain' , $all_shop_domain[$id]['domain']?$all_shop_domain[$id]['domain']:$id);
	        $this->assign('help' , get_child($help));
        }
    }
    
    /*
     * 分类列表
     * */
    public function categoryList(){
        if(IS_POST){
            $data   = I();   
            $result = array('status'=>1,'msg'=>'ok');
            if($data['update']){
                 $result = D('ShopCategory')->categorysUpdate($data['update'] , $_SESSION['member_data']['id']);
                 if($result['status']){
                     foreach($data['update'] as $v){
                         $navdata[] = array(
                             'cat_id'    => $v['id'],
                             'nav_name'  => $v['name'],
                         );
                     }
                     $result = D('ShopNav')->navsNameUpdate($navdata , $_SESSION['member_data']['id']);
                     if($result['status']){
                         $redis = new \Com\Redis();
                         $redis->redis->delete('shop_nav'.$_SESSION['member_data']['id']);
                     }
                 }
            }
            if($data['add']){
                $result = D('ShopCategory')->categorysAdd($data['add'] , $_SESSION['member_data']['id']);
            }
            $this->ajaxReturn($result);
        }else{
            $member_id = $_SESSION['member_data']['id'];
            $categorys = M('Shop_category')->where(array('member_id'=>$member_id))->select();
            $this->assign('categorys' , get_child($categorys));
            $this->assign('categorys_json' , json_encode($categorys));
            $this->display();            
        }
    }
    
    /*
     * 添加分类
     * */
    public function categoryAdd(){
        if(IS_AJAX){
            $member_id = $_SESSION['member_data']['id'];
            $catdata['name'] =  I('post.catname');
            $catdata['sort']= I('post.sort');
            $catdata['pid'] = 0;
            $catdata['member_id'] = $member_id;
            $catdata['time'] =time();
            $catdata['status'] = 1;
            $results = M('shop_category')->add($catdata);
            if($results !== false){
                $this->ajaxReturn(array('status'=>1,'msg'=>'ok'));
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'error'));
            }
        }else{			   
			$this->display();
		}
    }   
    
    /*
     * 删除分类
     * */
    public function categoryDelete(){
        if(IS_POST){
            $id = intval(I('id'));   
            $result = D('ShopCategory')->categorysDelete($id , $_SESSION['member_data']['id']);
            if($result['status']){
                $result = D('ShopNav')->navDelete($id , $_SESSION['member_data']['id']);
                if($result['status']){
                    $redis = new \Com\Redis();
                    $redis->redis->delete('shop_nav'.$_SESSION['member_data']['id']);
                }
            }
            $this->ajaxReturn($result);
        }
    }

    public function getCategory(){
    	if(IS_POST && IS_AJAX){
            $pid = I('id');
            $condition = array(
                'member_id' => $_SESSION['member_data']['id'],
                'pid'       => $pid
            );
            $data = M('Shop_category')->where($condition)->order('sort')->select();
            $this->ajaxReturn(array(
                'status' => 1,
                'msg'    => 'ok',
                'data'   => $data
            ));
    	}
    }
}