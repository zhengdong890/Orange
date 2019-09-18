<?php
/*
 * 工具超市
 * */
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class ToolController extends Controller {  
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
    
    public function goodsList(){
        $pid     = intval(I('pid'))? intval(I('pid')) : 43; //上级id
        $cat_id  = intval(I('cat_id'));//当前分类id
        $paramer = I();//获取查询参数
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
        if($paramer['cat_id']){//商品分类
            $where['a.cat_id'] = $paramer['cat_id'];
        }
        if($paramer['brand_id']){//商品品牌
            $where['a.brand_id'] = $paramer['brand_id'];
        }
        if($paramer['province']){//商品区域
            $where['a.province'] = $paramer['province'];
        }
        if($paramer['city']){//商品区域
            $where['a.city'] = $paramer['city'];
        }           
        /*价格区间段*/
        $price = array(
          array('500-min','0-500'),
          array('500-5000','500-5000'),
          array('5000-50000','5000-5万'),
          array('50000-100000','5万-10万'),
          array('100000-max','10万以上'),
        );  
        $cats = M('Mall_category')
              ->where(array('pid'=>$pid))
              ->field('id,cat_name,router')
              ->order('sort')
              ->select();     
        $cat_ids  = array();
        $now_cat  = M('Mall_category')->where(array('id'=>$pid))->Field('router,cat_name')->find();
        $crumb[]  = array($now_cat['router'] , $now_cat['cat_name']);
        foreach ($cats as $k => $v) {
            $cat_ids[] = $v['id'];          
            if($cat_id == $v['id']){
                $crumb[] = array($v['router'] , $v['cat_name']);
            }
        }
        $where['cat_id'] = array('in' , $cat_id ? "$cat_id" : implode(',' , $cat_ids));
        /*分页*/
        $count = M('Mall_goods as a')->where($where)->count();//统计商品数量 
        $Page  = new \Think\Page($count,48);// 实例化分页类 传入总记录数和每页显示的记录数(5)
        $show  = $Page->getPage();
        $goods = M('Mall_goods as a')
                 ->where($where)
                 ->field('id,goods_name,goods_price,goods_thumb,sale_num,member_id,comment_number')
                 ->limit($Page->firstRow.','.$Page->listRows)
                 ->order('id desc,sort')
                 ->select();
        /*获取所有店铺的信息  更新*/
        $redis = new \Com\Redis();
        Hook::add('allShopData','Shop\\Addons\\SellerAddon');
        Hook::listen('allShopData');
        $all_shop_data = $redis->get('all_shop_data' , 'array');//获取redis的缓存
        foreach($goods as $k=>$v){
            if($all_shop_data[$v['member_id']]['status']){
                $goods[$k]['shop_name'] = $all_shop_data[$v['member_id']]['shop_name'];
                $goods[$k]['domain']    = $all_shop_data[$v['member_id']]['domain']?$all_shop_data[$v['member_id']]['domain']:$v['member_id'];
            }else{
                unset($goods[$k]);
            }
         }       
        /*获取品牌*/
        $cat_ids   = implode(',' , $cat_ids);
        $brand_ids = array();
        $brand_arr = M('Mall_category_brand')
                   ->where(array('cat_id'=>array('in' , $cat_ids)))
                   ->field('brand_id')
                   ->select();
        foreach($brand_arr as $k => $v){
            $brand_ids[$v['brand_id']] = $v['brand_id'];
        }
        $brand_ids = implode(',' , $brand_ids);
        $brands    = M('Goods_brand')
                   ->where(array('id'=>array('in' , $brand_ids),'status'=>1))
                   ->field('id,brand_name,brand_thumb')
                   ->select();  
        $categorys = M('Mall_category')->where(array('pid'=>$pid))->field('id,cat_name,router')->select();
        /*近期热卖商品*/
        $hot_goods = M('Mall_goods')->limit(0,5)->order('sort')->select();
        foreach($hot_goods as $k=>$v){
            $hot_goods[$k]['domain'] = $all_shop_data[$v['member_id']]['domain']?$all_shop_data[$v['member_id']]['domain']:$v['member_id'];
        }
        /*url参数格式组装*/
        foreach($paramer as $k => $v){
            $paramer[$k] = "$k=$v";
        }
        unset($paramer['p']);unset($paramer['m']);unset($paramer['cat_id']);unset($paramer['pid']);
        $html  = pageHtml('http://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['PATH_INFO'] , $show , implode('&' , $paramer));

        /*商城 工具超市分类缓存   更新*/
        Hook::add('getToolCategory','Home\\Addons\\MallCategoryAddon');
        Hook::listen('getToolCategory');
        $tool_category = $redis->get('tool_mall_category' , 'array');
        $tool_category = get_child($tool_category);
        /*商城商品  redis缓存数据*/
        $cat_id = $cat_id ? $cat_id : $pid;
        Hook::add('mallCategorysSeo','Home\\Addons\\SeoAddon');
        Hook::listen('mallCategorysSeo',$redis);
        $seo = $redis->get('seo_mall_category' . $cat_id , 'array');//获取redis的缓存  
        $this->assign('paramers',json_encode(I()));
        $this->assign('crumb',$crumb);
        $this->assign('tool_category',$tool_category);
        $this->assign('html',$html);
        $this->assign('cat_id',$cat_id);
        $this->assign('pid',$pid);
        $this->assign('price',$price);
        $this->assign('brands',$brands);
        $this->assign('count',$count);  
        $this->assign('cats',$cats);    
        $this->assign('categorys',$categorys);             
        $this->assign('goods' , $goods);
        $this->assign('hot_goods',$hot_goods);
        $this->assign('seo',$seo);
        $this->display();    
  }
    
  /**
    *商品分类
    */          
  public function categoryList(){     
      $redis = new \Com\Redis();
      /*商城商品分类缓存   更新*/
      Hook::add('getCategory','Home\\Addons\\MallCategoryAddon');
      Hook::listen('getCategory');
      $mall_categorys = $redis->get('index_mall_category' , 'array');
      /*首页商城商品缓存   更新*/
      Hook::add('getIndexGoods','Home\\Addons\\mallGoodsAddon');
      Hook::listen('getIndexGoods');
      $mall_goods_ = $redis->get('index_mall_goods' , 'array');
      /*获取所有店铺的二级域名  更新*/
      Hook::add('allShopData','Shop\\Addons\\SellerAddon');
      Hook::listen('allShopData');
      $all_shop_domain = $redis->get('all_shop_data' , 'array');//获取redis的缓存
      /*商城分类和商城商品关联*/
      foreach($mall_categorys as $k=>$v){//二级分类的父类
          if($v['pid'] != 0){
              $catids_arr[$v['id']] = $v['pid'];
          }
      }
      foreach($mall_goods_ as $k=>$v){
          if($all_shop_domain[$v['member_id']]['status']){
              $v['domain'] = $all_shop_domain[$v['member_id']]['domain'] ? $all_shop_domain[$v['member_id']]['domain'] : $v['member_id'];
              $pid = $catids_arr[$v['cat_id']];
              $mall_goods[$pid][] = $v;
          }
      }
      $mall_categorys = get_child($mall_categorys);
      foreach ($mall_categorys as $k => $v) {         
          $mall_categorys[$k]['goods'] = $mall_goods[$v['id']];
      }    
      /*获取Seo缓存*/
      Hook::add('mallCategorysSeo','Home\\Addons\\seoAddon');
      Hook::listen('mallCategorysSeo',$redis);
      $seo = $redis->get('seo_mall_category' , 'array');
      $this->assign('seo',$seo);
      $this->assign('mall_goods' , $mall_categorys);
      $this->display();              
  }
}