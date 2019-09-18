<?php
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class GroupBuyController extends Controller {
    public function _initialize(){
        $redis = new \Com\Redis();
        $member_id = $_SESSION['member_data']['id'];
        /*获取Seo缓存*/
		Hook::add('navSeo','Home\\Addons\\SeoAddon');
		$param = array($redis , 5);
        Hook::listen('navSeo', $param);
		$seo = $redis->get('nav_seo5' , 'array');
        /*获取购物车统计缓存*/
        Hook::add('totalCart','Home\\Addons\\TotalAddon');
        Hook::listen('totalCart',$member_id);
        $this->assign('cart_total' , $_SESSION['cart_total']);
        /*底部帮助*/
        Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
        Hook::listen('getFooterHelp');
        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
        $this->assign('help' , $help);
    }
 
    public function index(){
        /*
        $time = time();
        M('Group_goods')->where('id!=0')->save(array(
            'start_time' => $time,
            'time'       => 10,
            'end_time'   => $time + 10*24*3600            
        ));*/
        $time    = time();
        /*获取首页推荐位*/
        $group   = M('Group_goods')
                 ->where(array('ad_1'=>'1','is_check'=>1,'check_status'=>1,'start_time'=>array('elt',$time),'end_time'=>array('egt',$time)))
                 ->limit(0,1)
                 ->select();
        /*获取普通推荐位*/
        $group_1 = M('Group_goods')
                 ->where(array('ad_1'=>'2','is_check'=>1,'check_status'=>1,'start_time'=>array('elt',$time),'end_time'=>array('egt',$time)))
                 ->limit(0,2)
                 ->select();
        //首页推荐位和普通推荐位合并         
        $group_1 = array_merge($group , $group_1);
        $tj_number = count($group_1);
        /*获取非推荐位*/
        $group_2 = M('Group_goods')
                 ->where(array('ad_1'=>'0','is_check'=>1,'check_status'=>1,'start_time'=>array('elt',$time),'end_time'=>array('egt',$time)))
                 ->limit(0,8)
                 ->select();
        $group = array_merge($group_1 , $group_2);
        $goods_ids = array();
        foreach($group as $k => $v){
            $goods_ids[] = $v['goods_id'];
        }    	
        $goods_ids = implode(',' ,$goods_ids);	   
        $goods_data_ = array();
        $goods_data = array();
        if($goods_ids){
            /*获取商品信息*/
            $goods_data_ = M('Mall_goods')
                         ->where(array('id'=>array('in' , $goods_ids)))
                         ->field('id,goods_name,goods_price,brand_id,sale_num,goods_number')
                         ->select();
            $goods_data = array();
            foreach ($goods_data_ as $k => $v) {
                $goods_data[$v['id']] = $v;
                if($v['brand_id']){
                    $brand_ids[$v['brand_id']] = $v['brand_id'];
                }
            }            
        }
        /*获取商品品牌*/
	    $brand_ids = implode(',' , $brand_ids);
	    $brand     = array();
	    if($brand_ids){
	        $brand_ = M('Goods_brand')
    		        ->where(array('id'=>array('in' , $brand_ids)))
    		        ->field('id,brand_name')
    			    ->select();
			foreach ($brand_ as $k => $v) {
        	    $brand[$v['id']] = $v['brand_name'];       	
            }     	    	
	    }
		/*商品信息关联*/	    	
	    foreach($group as $k => $v){
	        $group[$k]['goods_name']  = $goods_data[$v['goods_id']]['goods_name'];
	        $group[$k]['goods_price'] = $goods_data[$v['goods_id']]['goods_price'];
	        $group[$k]['sale_num']    = $goods_data[$v['goods_id']]['sale_num'];
	        $group[$k]['goods_number']= $goods_data[$v['goods_id']]['goods_number'];
	        $brand_id = $goods_data[$v['goods_id']]['brand_id'];
	        if($brand_id && $group_1[$k]){
	            $group_1[$k]['brand_name'] = $brand[$brand_id];
	        }else
            if($group_1[$k]){
	            $group_1[$k]['brand_name'] = '';
	        }
	    }       

	    /*获取所有店铺的信息  更新*/
	    $redis = new \Com\Redis();
	    $all_shop_data = $redis->get('all_shop_data' , 'array');//获取redis的缓存
	    foreach($group as $k=>$v){
	        $group[$k]['shop_name'] = $all_shop_data[$v['seller_id']]['shop_name'];
	        $group[$k]['domain']    = $all_shop_data[$v['seller_id']]['domain']?$all_shop_data[$v['seller_id']]['domain']:$v['seller_id'];
	    }
	    $group_1 = array_slice($group, 0 , $tj_number);
	    $group_2 = array_slice($group, $tj_number);
	    /*单品推荐位置*/
	    $goods_ids = M('Mall_goods_model')->where(array('id'=>2))->getField('goods_ids');
	    $goods = M('Mall_Goods')
	           ->where(array('id'=>array('in' , $goods_ids)))
	           ->order('sort')
        	   ->limit(0,5)
        	   ->select(); 
	    foreach($goods as $k=>$v){	       
	        $goods[$k]['domain'] = $all_shop_data[$v['member_id']]['domain']?$all_shop_data[$v['member_id']]['domain']:$v['member_id'];
	    }

	    $this->assign('goods',$goods); 
	    $this->assign('group_1',$group_1);
	    $this->assign('group_1_json',json_encode($group_1));
	    $this->assign('group_2',$group_2);
	    $this->assign('group_2_json',json_encode($group_2));
	    $this->assign('seo',$seo); 
        $this->display();
    }  
    
    public function beSoldOut(){
        $time        = time();
        $end_time    = time() + 30 * 24 * 3600;
        $srart_time  = $time;
        $group_2 = M('Group_goods')
                ->where(array('ad_1'=>'0','is_check'=>1,'check_status'=>1,'start_time'=>array('elt',$srart_time),'end_time'=>array('between',"$srart_time,$end_time")))
                ->limit(0,8)
                ->select();
        $goods_ids = array();
        foreach($group_2 as $k => $v){
            $goods_ids[] = $v['goods_id'];
        }
        $goods_ids = implode(',' ,$goods_ids);
        $goods_data_ = array();
        $goods_data = array();
        if($goods_ids){
            /*获取商品信息*/
            $goods_data_ = M('Mall_goods')
            ->where(array('id'=>array('in' , $goods_ids)))
            ->field('id,goods_name,goods_price,brand_id,sale_num,goods_number')
            ->select();
            $goods_data = array();
            foreach ($goods_data_ as $k => $v) {
                $goods_data[$v['id']] = $v;
                if($v['brand_id']){
                    $brand_ids[$v['brand_id']] = $v['brand_id'];
                }
            }
        }
        /*商品信息关联*/
        foreach($group_2 as $k => $v){
            $group_2[$k]['goods_name']  = $goods_data[$v['goods_id']]['goods_name'];
            $group_2[$k]['goods_price'] = $goods_data[$v['goods_id']]['goods_price'];
            $group_2[$k]['sale_num']    = $goods_data[$v['goods_id']]['sale_num'];
            $group_2[$k]['goods_number']= $goods_data[$v['goods_id']]['goods_number'];
        }
        /*获取所有店铺的信息  更新*/
        $redis = new \Com\Redis();
        Hook::add('allShopData','Shop\\Addons\\SellerAddon');
        Hook::listen('allShopData');
        $all_shop_data = $redis->get('all_shop_data' , 'array');//获取redis的缓存
        foreach($group_2 as $k=>$v){
            $group_2[$k]['shop_name'] = $all_shop_data[$v['seller_id']]['shop_name'];
            $group_2[$k]['domain']    = $all_shop_data[$v['seller_id']]['domain']?$all_shop_data[$v['seller_id']]['domain']:$v['seller_id'];
        }
        $this->assign('group_2',$group_2);
        $this->assign('group_2_json',json_encode($group_2));
        $this->display();
    }

    public function activityTrailer(){
        //echo "活动预告...";
        $time        = time();
    

        //今日主打
       $goods = M('Group_goods')
        ->where(array('tp_shop_data.status'=>1,'tp_group_goods.is_check'=>1,'tp_group_goods.start_time'=>array('elt',$time),'tp_group_goods.end_time'=>array('egt',$time),'tp_group_goods.ad_1'=>0,'tp_group_goods.check_status'=>1))
        ->join('tp_shop_data ON tp_shop_data.member_id=tp_group_goods.seller_id')
        ->field('tp_shop_data.thumb,tp_group_goods.title,tp_group_goods.img,tp_shop_data.domain,tp_group_goods.goods_id')
        ->limit(8) 
        ->select();
        //明天预告
        //$time_m=time() +24 * 3600;
        $goods_m = M('Group_goods')
       ->where(array('tp_shop_data.status'=>1,'tp_group_goods.is_check'=>1,'tp_group_goods.start_time'=>array('elt',$time),'tp_group_goods.end_time'=>array('egt',$time),'tp_group_goods.ad_1'=>0,'tp_group_goods.check_status'=>1))
        ->join('tp_shop_data ON tp_shop_data.member_id=tp_group_goods.seller_id')
        ->field('tp_shop_data.thumb,tp_group_goods.title,tp_group_goods.img,tp_shop_data.domain,tp_group_goods.goods_id')
        ->order('tp_group_goods.start_time desc')
        ->limit(8) 
        ->select();

         //后天预告
        //$time_h=time() +48 * 3600;
        $goods_h = M('Group_goods')
       ->where(array('tp_shop_data.status'=>1,'tp_group_goods.is_check'=>1,'tp_group_goods.start_time'=>array('elt',$time),'tp_group_goods.end_time'=>array('egt',$time),'tp_group_goods.ad_1'=>0,'tp_group_goods.check_status'=>1))
        ->join('tp_shop_data ON tp_shop_data.member_id=tp_group_goods.seller_id')
        ->field('tp_shop_data.thumb,tp_group_goods.title,tp_group_goods.img,tp_shop_data.domain,tp_group_goods.goods_id')
        ->order('tp_group_goods.time desc')
        ->limit(8) 
        ->select();
        $this->assign('goods',$goods);
        $this->assign('goods_m',$goods_m);
        $this->assign('goods_h',$goods_h);


        $this->display();
    }
}