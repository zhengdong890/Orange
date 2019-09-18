<?php
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=u tf-8");
class EquipmentRentController extends Controller {   
   public function index(){    
        $member_id = $_SESSION['member_data']['id'];
        $banners    = M("Banner")->where(array('type'=>1,'status'=>1))->order('sort')->select(); //banner   
        $redis = new \Com\Redis(); 
        $mall_goods_ = $redis->get('index_mall_goods' , 'array');
        /*获取购物车统计缓存   更新*/        
        Hook::add('totalCart','Home\\Addons\\TotalAddon');
        Hook::listen('totalCart',$member_id);
        $this->assign('cart_total' , $_SESSION['cart_total']);
        /*获取首页Seo缓存  更新*/
		$redis = new \Com\Redis();
		Hook::add('navSeo','Home\\Addons\\SeoAddon');
		$param = array($redis , 2);
        Hook::listen('navSeo', $param);
		$seo = $redis->get('nav_seo2' , 'array');  
        /*获取导航缓存  更新*/
        Hook::add('getNav','Home\\Addons\\NavAddon');
        Hook::listen('getNav');
        $navs = $redis->get('navs' , 'array');//获取redis的缓存  
        /*分享商品分类   更新*/ 
        Hook::add('getCategory','Home\\Addons\\CategoryAddon');
        Hook::listen('getCategory');      
        $categorys = $redis->get('index_cattegory' , 'array');//获取redis的缓存
        /*分享商品   更新*/
        Hook::add('getIndexGoods','Home\\Addons\\GoodsAddon');
        Hook::listen('getIndexGoods'); 
        $goods = $redis->get('index_goods' , 'array');//获取redis的缓存     
	    /*底部帮助  更新*/
	    Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
        Hook::listen('getFooterHelp');
        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
        $this->assign('seo',$seo);
        $this->assign('navs',$navs);
	    $this->assign('help' , $help);
	    $this->assign('categorys', get_child($categorys));
	    $this->assign('seo',$seo);  
        $this->assign('goods' , $goods);
        $this->assign('banners',$banners);
        $this->display();
   }
}