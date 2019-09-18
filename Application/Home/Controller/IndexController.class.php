<?php
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=u tf-8");
class IndexController extends Controller {   
   public function index(){  
        $member_id = $_SESSION['member_data']['id'];     
        $redis = new \Com\Redis(); 		  
        /*商城商品分类缓存*/
        $mall_categorys = $redis->get('mall_category' , 'array');
        /*首页商城商品缓存   更新*/
        Hook::add('getIndexGoods','Home\\Addons\\MallGoodsAddon');
        Hook::listen('getIndexGoods');
        $mall_goods = $redis->get('index_mall_goods' , 'array');
        /*获取购物车统计缓存   更新*/        
        Hook::add('totalCart','Home\\Addons\\TotalAddon');
        Hook::listen('totalCart',$member_id);
        $this->assign('cart_total' , $_SESSION['cart_total']);
        /*获取首页Seo缓存  更新*/
        Hook::add('navSeo','Home\\Addons\\SeoAddon');
        $param = array($redis , 1);
        Hook::listen('navSeo', $param);
        $seo = $redis->get('nav_seo1' , 'array');   
        /*获取导航缓存  更新*/
        Hook::add('getNav','Home\\Addons\\NavAddon');
        Hook::listen('getNav');
        $navs = $redis->get('navs' , 'array');//获取redis的缓存       
	    /*底部帮助  更新*/
	    Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
        Hook::listen('getFooterHelp');
        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
        /*获取所有店铺的二级域名*/
        $shop_data = $redis->get('all_shop_data' , 'array');//获取redis的缓存        
        //广告位1
        $ad = M('Ad')->limit(0,3)->select();
        //组装首页商品    
        $mall_categorys = D('Mall_goods')->getIndexGoods($mall_categorys , $mall_goods , $shop_data);
        //获取新闻
        $news   = D('News')->getNews(array('type'=>1,'status'=>1,'seo_news'=>0));
	    //获取公告
        $notice = D('News')->getNews(array('type'=>2));           
	    //获取规则
        $rule   = D('News')->getNews(array('type'=>3));       
        //获取友情链接
        $friends = M('Friendlink')->where(array('status'=>'y'))->select();
        $banners = M("Banner")->where(array('type'=>2,'status'=>1))->order('sort')->select(); //banner
        $tender  = D('Tender')->getIndexTender();//融资租凭
        /*团购信息*/
        $time    = time();
        $group   = M('Group_goods')
                 ->where(array('ad_1'=>'1','is_check'=>1,'check_status'=>1,'start_time'=>array('elt',$time),'end_time'=>array('egt',$time)))
                 ->limit(0,1)
                 ->find();
        //采购
        $purchase = M('Purchase')->where(array('status'=>1))->select();
        $this->assign('purchase',$purchase);     
        $this->assign('friend',$friends);  
        $this->assign('mall_categorys',$mall_categorys);
        $this->assign('mall_goods',$mall_categorys);
        $this->assign('seo',$seo);
        $this->assign('ad',$ad);
        $this->assign('navs',$navs);
	    $this->assign('help' , $help);
	    $this->assign('seo',$seo);
	    $this->assign('all_shop_domain' , $all_shop_domain);
	    $this->assign('group',$group);        
        $this->assign('banners',$banners);
        $this->assign('tender' , $tender);
        $this->assign('news',$news);
        $this->assign('notice',$notice);
        $this->assign('rule',$rule);
        $this->assign('member_data' , $_SESSION['member_data']);
        $this->display();
   }
}