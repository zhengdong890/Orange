<?php
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class SearchController extends Controller {
    public function _initialize(){
        $redis = new \Com\Redis();
        $member_id = $_SESSION['member_data']['id'];
        /*获取导航缓存*/
        Hook::add('getNav','Home\\Addons\\NavAddon');
        Hook::listen('getNav');
        $navs = $redis->get('navs' , 'array');//获取redis的缓存
        /*获取购物车统计缓存*/
        Hook::add('totalCart','Home\\Addons\\TotalAddon');
        Hook::listen('totalCart');
        $this->assign('cart_total' , $_SESSION['cart_total']);
        /*底部帮助*/
        Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
        Hook::listen('getFooterHelp');
        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
        $this->assign('navs' , $navs );
        $this->assign('help' , get_child($help));
    }
    
    public function index(){
        $type = I('type');
        if($type == 1){
            $this->redirect('goodsSearch?keyword='.I('keyword'));
        }else
        if($type == 2){
            $this->redirect('mallGoodsSearch?keyword='.I('keyword'));
        }else
        if($type == 3){
            $this->redirect('shopSearch?keyword='.I('keyword'));
        }
    }
    
    public function goodsSearch(){
        $keyword = I('keyword');
        $pid     = intval(I('pid'))? intval(I('pid')) : 72; //上级id
        $cat_id  = intval(I('cat_id'));//当前分类id
        $paramer = I();//获取查询参数
        /*品牌查询*/
        $where['brand_name']   = array('like', '%'.$keyword.'%');
        $brand = M('Goods_brand')->where($where)->select();
        $where = array();
        $where['a.goods_name'] = array('like', '%'.$keyword.'%');
        $where['a.status'] = 1;
        $where['a.is_check'] = 1;
        /*生成查询条件*/
        if($paramer['price']){//价格区间
            $price_arr = explode('-' , $paramer['price']);
            if($price_arr[1] == 'max'){
                $where['a.goods_price'] = array('egt' , $price_arr[0]);
            }else
                if($price_arr[1] == 'min'){
                $where['a.goods_price'] = array('elt' , $price_arr[0]);
            }else{
                $where['a.goods_price'] = array(between,array($price_arr[0],$price_arr[1]));
            }
        }
        /*价格区间段*/
        $price = array(
            array('500-min','0-500'),
            array('500-5000','500-5000'),
            array('5000-50000','5000-5万'),
            array('50000-100000','5万-10万'),
            array('100000-max','10万以上'),
        );
        /*分页*/
        $count = M('Goods as a')->where($where)->count();//统计商品数量
        $Page  = new \Think\Page($count,8);// 实例化分页类 传入总记录数和每页显示的记录数(5)
        $show  = $Page->getPage();
        $goods = M('Goods as a')
               ->where($where)
               ->field('id,goods_name,goods_price,goods_thumb,sale_num,member_id')
               ->limit($Page->firstRow.','.$Page->listRows)
               ->order('sort')
               ->select();
        /*获取商品店铺*/
        $seller_ids = array();
        foreach($goods as $k => $v){
            $seller_ids[] = $v['member_id'];
        }       
        $seller_ids = array_unique($seller_ids);
        $seller_ids = implode(',' , $seller_ids);     
        $shop_name  = M('Shop_data')->where(array('member_id'=>array('in' , $seller_ids)))->Field('member_id,shop_name')->select();        
        foreach($shop_name as $k => $v){
            $shop_name[$v['member_id']] = $v;
        }
        foreach($goods as $k => &$v1){
            $v1['shop_name'] = $shop_name[$v1['member_id']]['shop_name'];
        } 
        /*近期热租商品*/
        $rents = M('Goods')->limit(0,5)->order('sort')->select();
        /*url参数格式组装*/
        foreach($paramer as $k => $v){
            $paramer[$k] = "$k=$v";
        }
        unset($paramer['p']);
        $html  = pageHtml(U('goodsSearch') , $show , implode('&' , $paramer));
        $this->assign('paramers',json_encode(I()));
        $this->assign('html',$html);
        $this->assign('cat_id',$cat_id);
        $this->assign('price',$price);
        $this->assign('count',$count);
        $this->assign('goods' , $goods);
        $this->assign('rents',$rents);
        $this->display();              
    } 
    
    public function mallGoodsSearch(){
        $keyword = I('keyword');
        /*先查询是否为sku*/
        $sku_data = M('sku')->where(array('sku_code'=>$keyword))->find();
        if(!empty($sku_data)){
            $domain = M('Shop_data')->where(array('member_id'=>$sku_data['seller_id']))->getField('domain');
            header("Location:http://{$domain}.orangesha.com/shangpin-{$sku_data['goods_id']}.html");
        }
        $pid     = intval(I('pid'))? intval(I('pid')) : 72; //上级id
        $cat_id  = intval(I('cat_id'));//当前分类id
        $paramer = I();//获取查询参数
        $where['a.goods_name'] = array('like', '%'.$keyword.'%');
        $where['a.status'] = 1;
        /*生成查询条件*/
        if($paramer['price']){//价格区间
            $price_arr = explode('-' , $paramer['price']);
            if($price_arr[1] == 'max'){
                $where['a.goods_price'] = array('egt' , $price_arr[0]);
            }else
                if($price_arr[1] == 'min'){
                $where['a.goods_price'] = array('elt' , $price_arr[0]);
            }else{
                $where['a.goods_price'] = array(between,array($price_arr[0],$price_arr[1]));
            }
        }
        /*价格区间段*/
        $price = array(
            array('500-min','0-500'),
            array('500-5000','500-5000'),
            array('5000-50000','5000-5万'),
            array('50000-100000','5万-10万'),
            array('100000-max','10万以上'),
        );
        /*分页*/
        $count = M('Mall_goods as a')->where($where)->count();//统计商品数量
        $Page  = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(5)
        $show  = $Page->getPage();
        $goods = M('Mall_goods as a')
               ->where($where)
               ->field('id,goods_name,goods_price,goods_thumb,sale_num,member_id,comment_number')
               ->limit($Page->firstRow.','.$Page->listRows)
               ->order('sort')
               ->select();
        /*获取所有店铺的信息  更新*/
        $redis = new \Com\Redis();
        $all_shop_data = $redis->get('all_shop_data' , 'array');//获取redis的缓存
        foreach($goods as $k=>$v){            
            if($all_shop_data[$v['member_id']]['status']){
                $goods[$k]['shop_name'] = $all_shop_data[$v['member_id']]['shop_name'];
                $goods[$k]['domain']    = $all_shop_data[$v['member_id']]['domain']?$all_shop_data[$v['member_id']]['domain']:$v['member_id'];
            }else{
                unset($goods[$k]);
            }
        }
        /*近期热卖商品*/
        $hot_goods = M('Mall_goods')->where(array('status'=>1))->limit(0,5)->order('sort')->select();
        /*url参数格式组装*/
        foreach($paramer as $k => $v){
            $paramer[$k] = "$k=$v";
        }
        unset($paramer['p']);
        $html  = pageHtml(U('mallGoodsSearch') , $show , implode('&' , $paramer));
        $this->assign('paramers',json_encode(I()));
        $this->assign('html',$html);
        $this->assign('cat_id',$cat_id);
        $this->assign('price',$price);
        $this->assign('count',$count);
        $this->assign('goods' , $goods);
        $this->assign('hot_goods',$hot_goods);
        $this->display();
    }
    
    /*
     * 店铺搜索
     * */
    public function shopSearch(){
        $keyword   = I('keyword');
        $shop_data = M('Shop_data')->where(array('shop_name'=>array('like', '%'.$keyword.'%')))->where(array('status'=>1))->Field('member_id,domain')->find();
        if($shop_data['member_id']){  
            $domain =  $shop_data['domain']?$shop_data['domain']:$shop_data['member_id'];
            header("Location: http://$domain.orangesha.com");    
        }else{
            $this->redirect("Index/index");
        }
    }
}