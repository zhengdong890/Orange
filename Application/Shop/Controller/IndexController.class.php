<?php
namespace Shop\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=u tf-8");
class IndexController extends Controller {       
    public function _initialize(){
        $redis = new \Com\Redis();
        $member_id = $_SESSION['member_data']['id'];
        /*二级域名处理  获取对应的商家id*/
        $domain    = SUB_DOMAIN; //获取当前地址的二级域名			
        $seller_id = M('Shop_data')->where(array('domain'=>$domain))->getField('member_id');
        if(!$seller_id){
            $seller_id = M('Member')->where(array('id'=>$domain))->getField('id');
            if(!$seller_id){
                echo '页面无法找到';die;  
            }
        }  
        $this->seller_id = $seller_id;   
        /*店铺数据  缓存更新处理*/
        $shop_data = $redis->get('shop_data'.$seller_id , 'array');
        /*店铺导航  缓存更新处理*/
        Hook::add('getNav','Shop\\Addons\\SellerAddon');
        Hook::listen('getNav',$seller_id);
        $shop_nav = $redis->get('shop_nav'.$seller_id , 'array');
        /*店铺导航样式  缓存更新处理*/
        Hook::add('getNavCss','Shop\\Addons\\SellerAddon');
        Hook::listen('getNavCss',$seller_id);
        $nav_css = $redis->get('nav_css'.$seller_id , 'array');
        /*获取购物车统计  缓存更新处理*/
        Hook::add('totalCart','Home\\Addons\\TotalAddon');
        Hook::listen('totalCart',$member_id);
        $cart_total = $_SESSION['cart_total'];
	    /*底部帮助  更新*/
	    Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
        Hook::listen('getFooterHelp');
        //dump($shop_data);
        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
        $this->assign('shop_data' , $shop_data);
        $this->assign('help' , $help);
        $this->assign('shop_nav' , $shop_nav);
        $this->assign('shop_css' , $nav_css);
        $this->assign('cart_total' , $_SESSION['cart_total']);
        $this->assign('domain' , $domain);        
    }
    
	public function index(){
	    $seller_id = $this->seller_id;
		/*获取轮播*/
		$banner = M('Shop_banner')->where(array('member_id'=>$seller_id))->getField('thumb');     
        /*获取畅销商品*/
        $hot_goods = M('Mall_goods')
		           ->where(array('member_id'=>$seller_id,'is_new'=>2))
		           ->field('id,goods_name,goods_price,goods_thumb,sale_num')
		           ->order('sort')
                   ->where(array('status'=>1))
		           ->limit(0,8)
		           ->select();	   
        /*获取最新商品*/
        $new_goods = M('Mall_goods')
		           ->where(array('member_id'=>$seller_id,'is_new'=>1))
		           ->field('id,goods_name,goods_price,goods_thumb,sale_num')
		           ->order('id desc,sort')
                   ->where(array('status'=>1))
		           ->limit(0,8)
		           ->select();	 
        /*店铺内商品分类*/
		$shopcat = M('shop_category')
        		 ->where(array('member_id'=>$seller_id,'status'=>1))
        		 ->order('sort asc')
        		 ->select();
        //店家优惠卷
        $time1 = date('Y-m-d H:i:s');
        $map['end_time'] =array('gt',$time1);
        $shopjuan = M('Shop_coupons')
                    ->where(array('seller_id'=>$seller_id))
                    ->where($map)
                    ->select();	
        $this->assign('shop_coupons',$shopjuan);
		$shopcat = getLayer($shopcat);
		$uinfo= $_SESSION['member_data'];		
		$this->assign('uinfo',$uinfo);
		$this->assign('shopcat',$shopcat);	  		   				   
        $this->assign('seller_id' , $seller_id);
        $this->assign('banner' , $banner);
        $this->assign('hot_goods' , $hot_goods);
        $this->assign('new_goods' , $new_goods);
        $this->display();
    }
     public function couponAdd(){
        //个人优惠卷添加
        if(IS_POST){
            $member_id = $_SESSION['member_data']['id'];
            $coupon_id =  I('coupon_id');
            $result = D('Home/MemberShopCoupon')->couponAdd($member_id , $coupon_id);
            $this->ajaxReturn($result);
        }

    }
}