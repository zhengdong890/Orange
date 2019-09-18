<?php
namespace Shop\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class GoodsController extends Controller {	
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
        if(!isset($shop_data['status'])){
            exit('该店铺不存在');
        }
        if($shop_data['status'] == 0){
            exit('该店铺已关闭');    
        }
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
        /*底部帮助  缓存更新处理 */
        Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
        Hook::listen('getFooterHelp');
        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
        $this->assign('shop_data' , $shop_data);
        $this->assign('help' , $help);
        $this->assign('shop_nav' , $shop_nav);
        $this->assign('shop_css' , $nav_css);
        $this->assign('cart_total' , $_SESSION['cart_total']);
        $this->assign('domain' , $domain);        
    }
    
    /*根据导航显示商品*/
    public function goodsList(){
        $seller_id = $this->seller_id;
        $cat_id  = intval(I('cat_id'));//当前分类id
        $paramer = I();//获取查询参数
        $where['a.member_id'] = $seller_id;
        $where['a.status']    = 1;
        $where['a.shop_cat']  = $cat_id; 
        /*url参数格式组装*/
        foreach($paramer as $k => $v){
            $paramer[$k] = "$k=$v";
        }        
        $crumb = M('shop_category')->where(array('id'=>$cat_id))->getField('name');
        /*分页*/
        $count = M('Mall_goods as a')->where($where)->count();
        $Page  = new \Think\Page($count,8);// 实例化分页类 传入总记录数和每页显示的记录数(5)
        $show  = $Page->getPage();
        $goods = M('Mall_goods as a')
               ->where($where)
               ->field('id,goods_name,goods_price,goods_thumb,sale_num,shop_cat,status,comment_number')
               ->limit($Page->firstRow.','.$Page->listRows)
               ->order('sort')
               ->select();
        unset($paramer['p']);
        $shopcat = M('shop_category')->where(array('member_id'=>$seller_id,'status'=>1))->order('sort asc')->select();
        $shopcat = getLayer($shopcat);
        $this->assign('shopcat',$shopcat);
        $html  = pageHtml('http://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['PATH_INFO'] , $show , implode('&' , $paramer));
        $uinfo= $_SESSION['member_data'];
        $this->assign('uinfo',$uinfo);
        $this->assign('crumb',$crumb);;
        $this->assign('paramers',json_encode(I()));
        $this->assign('html',$html);
        $this->assign('cat_id',$cat_id);
        $this->assign('count',$count);
        $this->assign('goods' , $goods);
        $this->assign('seller_id' , $seller_id);
        $this->display();
    }
    
    /*
     * 商品详情页
     * */    
    public function goods(){
        $seller_id = $this->seller_id;
        $goods_id  = I('goods_id'); //商品id
       
        $goods     = M('Mall_goods')->where(array('id'=>$goods_id,'member_id'=>$seller_id,'status'=>1))->find(); //商品信息   
        if(!$goods['id']){
            exit('商品不存在');    
        }
        $goods_data = M('Mall_goods_data')->where(array('goods_id'=>$goods_id))->find();
        /*商品相册*/
        $goods_gallery = M("Mall_goods_gallery")->where(array('goods_id'=>$goods_id))->select();
        if($goods_gallery){
            array_unshift($goods_gallery,array('gallery_img'=>$goods['goods_thumb']));
        }else{
            $goods_gallery[0] = array('gallery_img' => $goods['goods_thumb']);
        }     
        $goods_hot  = M("Mall_goods")->limit(0,4)->where(array('member_id'=>$seller_id,'status'=>1))->select();  //最热 
        $area_str ="{$goods['province']},{$goods['city']},{$goods['area']}";
        $area = array();
        /*获取商品区域*/
        if($goods['province']){	        
	        $area = M('Area')
		          ->where(array('area_no'=>array('in',$area_str)))
		          ->Field('area_level,area_name')->select();        	
		}
		/*品牌*/
		$goods['brand_name'] = M('Goods_brand')->where(array('id'=>$goods['brand_id']))->getField('brand_name');		 
		$shopcat = M('shop_category')->where(array('member_id'=>$seller_id,'status'=>1))->order('sort asc')->select();		
		$shopcat = getLayer($shopcat);
		/*SEO*/
		$goods_seo = M('Mall_goods_seo')->where(array('goods_id'=>$goods_id))->find();
		//获取商品拥有的属性
        $goods_attr = M('Mall_goods_baseattr')->where(array('goods_id'=>$goods_id))->order('attr_id')->select();
        foreach($goods_attr as $v){
            if(!isset($temp[$v['attr_id']])){
                $temp[$v['attr_id']] = array(
                   'attr_id'   => $v['attr_id'],
                   'attr_name' => $v['attr_name'],
                   'seller_id' => $v['seller_id'],
                   'goods_id'  => $v['goods_id']
                );
            }
            $temp[$v['attr_id']]['data'][] = array(
               'attr_value_id' => $v['attr_value_id'],
               'attr_value'    => $v['attr_value']
            );          
        }
         //店家优惠卷
        $time1 = date('Y-m-d H:i:s');
        $map['end_time'] =array('gt',$time1);
        $shopjuan = M('Shop_coupons')
                    ->where(array('seller_id'=>$seller_id))
                    ->where($map)
                    ->select();
        //商品评价统计
        $goods_comments =M('Mall_goods_comment')
                    ->where(array('goods_id'=>$goods_id,'level'=>array('in','1,2,3')))
                    ->count();
        
        $this->assign('goods_comments',$goods_comments); 
        $this->assign('shop_coupons',$shopjuan);
        $sku = D('Home/Mall_goods')->getGoodsSku($goods_id);
        $this->assign('attr',$temp);
        $this->assign('sku',$sku);
        $this->assign('goods_attr',$goods_attr);
		$this->assign('goods_seo',$goods_seo);
		$this->assign('shopcat',$shopcat);		    
		$this->assign('area',$area);  
        $this->assign('goods',$goods);
        $this->assign('goods_data',$goods_data);
        $this->assign('goods_hot',$goods_hot);
        $this->assign('seller_id' , $seller_id);
        $this->assign('goods_gallery',$goods_gallery);//html_entity_decode
        $this->display();
    }
    
    /* 所有商品列表*/
    public function allgoods(){
        //echo "店铺商品页面";
        $seller_id = $this->seller_id;
        $paramer   = I();
        foreach($paramer as $k => $v){
            $paramer[$k] = "$k=$v";
        }
        if($pri = I('price')){
            $arr =explode('-', $pri);
            $low = $arr['0']?$arr['0']:0;
            $hie = $arr['1']?$arr['1']:1000000000;
            $where['goods_price']  = array('between',array($low,$hie));
            $count = M('mall_goods')->where(array('member_id'=>$seller_id,'status'=>1))->where($where)->count();
            $this->assign('num',$count); 
        }else{
            $count = M('mall_goods')->where(array('member_id'=>$seller_id,'status'=>1))->count();
            $this->assign('num',$count); 
            
        }
               
        $Page  = new \Think\Page($count,12);// 实例化分页类 传入总记录数和每页显示的记录数(5)
        $show  = $Page->getPage();
        //刷选商品
        $where=array('member_id'=>$seller_id,'status'=>1);
        //按照价格降序排列
         if($price = I('price_')){
            if($pri = I('price')){
                $arr =explode('-', $pri);
                $low = $arr['0']?$arr['0']:0;
                $hie = $arr['1']?$arr['1']:1000000000;
                $where['goods_price']  = array('between',array($low,$hie));
            }
            $order='goods_price desc';
            $goods =$this->Mall_goods_($where,$order,$Page);
            //按照销量降序排列
        }elseif($sales = I('sales')){
            if($pri = I('price')){
                $arr =explode('-', $pri);
                $low = $arr['0']?$arr['0']:0;
                $hie = $arr['1']?$arr['1']:1000000000;
                $where['goods_price']  = array('between',array($low,$hie));
            }
            $order='sale_num desc';
            $goods =$this->Mall_goods_($where,$order,$Page);
            //按照最新商品进行排序
        }elseif($news = I('xin')){
            if($pri = I('price')){
                $arr =explode('-', $pri);
                $low = $arr['0']?$arr['0']:0;
                $hie = $arr['1']?$arr['1']:1000000000;
                $where['goods_price']  = array('between',array($low,$hie));
            }
            $order='update_time desc';
            $goods =$this->Mall_goods_($where,$order,$Page);
            //按照最佳人气进行排序
        }elseif($mem = I('man')){
            if($pri = I('price')){
                $arr =explode('-', $pri);
                $low = $arr['0']?$arr['0']:0;
                $hie = $arr['1']?$arr['1']:1000000000;
                $where['goods_price']  = array('between',array($low,$hie));
            }
            $order='comment_number desc';
            $goods =$this->Mall_goods_($where,$order,$Page);
            //只显示在售商品
        }elseif($on_sale = I('on_sale')){
            $order='id desc,sort';
            
            $goods =$this->Mall_goods_($where,$order,$Page);
        }elseif($pri = I('price')){
            $arr =explode('-', $pri);
            $low = $arr['0']?$arr['0']:0;
            $hie = $arr['1']?$arr['1']:1000000000;
            $where['goods_price']  = array('between',array($low,$hie));
            $order='id desc,sort';
            $goods =$this->Mall_goods_($where,$order,$Page);
        }elseif($cat_id=I('cat_id')){
                $cat_ids = M('Shop_category')->where(array('pid'=>$cat_id))->Field('id')->select();
                $cat_ids = array_column($cat_ids, 'id');
                array_push($cat_ids , $cat_id);
                $cat_ids = implode($cat_ids , ',');
                $where=array('member_id'=>$seller_id,'shop_cat'=>array('in' , $cat_ids),'status'=>1);
                $order='id desc,sort';
                $goods =$this->Mall_goods_($where,$order,$Page);
                
        }elseif($brand_id = I('brand_id')){
            
            $where=array('brand_id'=>$brand_id);
            $order='id desc,sort';
            $goods =$this->Mall_goods_($where,$order,$Page);

        }else{
            $order='id desc,sort';
            $goods =$this->Mall_goods_($where,$order,$Page);
        }
        /*url参数格式组装*/
        $paramer  = I();//获取查询参数
        foreach($paramer as $k => $v){
            $paramer[$k] = "$k=$v";
        }
        unset($paramer['p']);
        $html  = pageHtml('http://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['PATH_INFO'] , $show , implode('&' , $paramer));
        
        $uinfo= $_SESSION['member_data'];
         //店家优惠卷
        $shopjuan = M('Shop_coupons')
                    ->where(array('seller_id'=>$seller_id))
                    ->select();
        //查询店铺信息
        $shop_data = M('Shop_data')
                ->where(array('member_id'=>$seller_id))
                ->field('domain')
                ->find();
        //品牌
         $goods_brand = M('Mall_goods as a')
               ->where(array('member_id'=>$seller_id))
               ->field('brand_id')
               ->select();
        $brand_ids = array_column($goods_brand,'brand_id');
        $brand_ids = implode(',', $brand_ids);
        $map['id']=array('in',$brand_ids);

        $goods_brand = M('Goods_brand')
                    ->where($map)
                    ->field('id,brand_name')
                    ->select();

        //当前分类的下级分类
        //

        $this->assign('shop_coupons',$shopjuan);
        $this->assign('uinfo',$uinfo);
        $this->assign('allgoods',$goods);
        $this->assign('page',$html);
        $banner = M('Shop_banner')->where(array('member_id'=>$seller_id))->getField('thumb');

        $shopcat = M('shop_category')
                ->where(array('member_id'=>$seller_id,'status'=>1))
                ->order('sort asc')
                ->select();
        
        $shopcat = getLayer($shopcat);
        
        $cat_name = M('shop_category')
                ->where('pid>0')
                ->where(array('member_id'=>$seller_id,'status'=>1))
                ->order('sort asc')
                ->select();

        $shop_css = M('shop_nav_css')->where(array('member_id'=>$seller_id))->getField('background_color');
        $this->assign('brand_name',$goods_brand);//品牌

        $this->assign('cat_name',$cat_name);//分类
        $this->assign('paramers',$paramer);
        $this->assign('shop_css',$shop_css);
        $this->assign('shopcat' , $shopcat);
        $this->assign('seller_id',$seller_id);
        $this->assign('banner' , $banner);
        $this->display();
    }
    public function Mall_goods_($where,$order,$Page){
      $goods = M('Mall_goods as a')
               ->where($where)
               ->field('id,brand_id,goods_name,goods_price,goods_thumb,sale_num,member_id,comment_number')
               ->limit($Page->firstRow.','.$Page->listRows)
               ->order($order)
               ->select();
      return $goods;
    }  
    
    /*根据分类显示商品*/
    public function showgoods(){
        $seller_id = $this->seller_id;
        $cat_id = I('get.cat_id');
        $cat_ids = M('Shop_category')->where(array('pid'=>$cat_id))->Field('id')->select();
        $cat_ids = array_column($cat_ids, 'id');
        array_push($cat_ids , $cat_id);
        $cat_ids = implode($cat_ids , ',');
        
        $goods_list =  M('mall_goods')->where(array('member_id'=>$seller_id,'shop_cat'=>array('in' , $cat_ids),'status'=>1))->select();
        $shop_data = M('Shop_data')->where(array('member_id'=>$seller_id))->find();
        $shopcat = M('shop_category')->where(array('member_id'=>$seller_id,'status'=>1))->order('sort asc')->select();
        $shopcat = getLayer($shopcat);
        $banner = M('Shop_banner')->where(array('member_id'=>$seller_id))->getField('thumb');
        $shop_css = M('shop_nav_css')->where(array('member_id'=>$seller_id))->getField('background_color');
         //店家优惠卷
        $shopjuan = M('Shop_coupons')
                    ->where(array('seller_id'=>$seller_id))
                    ->select(); 
        $this->assign('shop_coupons',$shopjuan);
        $uinfo= $_SESSION['member_data'];
        $this->assign('uinfo',$uinfo);
        $this->assign('shop_data',$shop_data);
        $this->assign('shop_css',$shop_css);
        $this->assign('banner' , $banner);
        $this->assign('shopcat' , $shopcat);
        $this->assign('seller_id' , $seller_id);
        if( $goods_list ){
            $this->assign('goods_list',$goods_list);
        }
        $this->display();
    }
    public function company(){

       // echo "公司简介";
        $seller_id = $this->seller_id;
        //dump($seller_id);
        $shop_des = M('Shop_view')
            ->where(array('seller_id'=>$seller_id))
            ->find();
            //dump($shop_des);
        //获取店铺名
        $shop = M('Shop_data')->where(array('member_id'=>$seller_id))->find();
         //店家优惠卷
        $shopjuan = M('Shop_coupons')
                    ->where(array('seller_id'=>$seller_id))
                    ->select(); 
        $this->assign('shop_coupons',$shopjuan);
        $this->assign('shop_des',$shop_des);
        $this->assign('shop',$shop);
        $this->display();
    }
}