<?php
/*
 * 新闻资讯
 * */
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class NewsController extends Controller {
    public function _initialize(){
    	$member_id = $_SESSION['member_data']['id'];
        $redis = new \Com\Redis();
        /*底部帮助*/
        Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
        Hook::listen('getFooterHelp');
        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
        /*获取购物车统计缓存*/
        Hook::add('totalCart','Home\\Addons\\TotalAddon');
        Hook::listen('totalCart',$member_id);
        $this->assign('cart_total' , $_SESSION['cart_total']);
        $this->assign('help' , $help);
    }
    
    public function index(){  
      //查询分页
        $news=M('News');
        $num=I('num');
        $count=$news->where(array('type'=>1,'seo_news'=>0))->count();//统计数量
        //dump($count);
        $num1=8;
        $page       = new \Think\Page($count,$num1);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $page->setConfig('header','个会员');
                $page->setConfig('prev','上一页');
                $page->setConfig('next','下一页');
                $page->setConfig('first','首页');
                $page->setConfig('last','末页');
                $show = $page->show();
        //$show       = $Page->show();// 分页显示输出
                //共几页
                $aa=ceil($count/$num1);

        //新闻查询
               // $num=2;
                if ($num) {
                   $news=$news->where(array('type'=>1,'seo_news'=>0))->order('create_time desc')->limit(12)->page($num)->select();
                }else{
                    $news=$news->where(array('type'=>1,'seo_news'=>0))->order('create_time desc')->limit($page->firstRow.','.$page->listRows)->select();
                }
        
        //dump($news);

        $this->assign('news',$news);
        $this->assign('num',$aa);
        $this->assign('header',$header);

        $this->assign('page',$show);// 赋值分页输出
        $this->assign('count',$count);

        $this->display();
    }
     public function index2(){
      //公告查询
        //查询分页
        $news=M('News');
        $num=I('num');
        $count=$news->where(array('type'=>2,'seo_news'=>0))->count();//统计数量
        //dump($count);
        $num1=8;
        $page       = new \Think\Page($count,$num1);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $page->setConfig('header','个会员');
                $page->setConfig('prev','上一页');
                $page->setConfig('next','下一页');
                $page->setConfig('first','首页');
                $page->setConfig('last','末页');
                $show = $page->show();
        //$show       = $Page->show();// 分页显示输出
                //共几页
                $aa=ceil($count/$num1);

        //新闻查询
               // $num=2;
                if ($num) {
                   $notice=$news->where(array('type'=>2,'seo_news'=>0))->order('create_time desc')->limit(12)->page($num)->select();
                }else{
                    $notice=$news->where(array('type'=>2,'seo_news'=>0))->order('create_time desc')->limit($page->firstRow.','.$page->listRows)->select();
                }
        
        //dump($news);

        $this->assign('news',$news);
        $this->assign('num',$aa);
        $this->assign('header',$header);

        $this->assign('page',$show);// 赋值分页输出
        $this->assign('count',$count);
        $this->assign('notice',$notice);
   
        $this->display();
    }
     public function index3(){
      
       //查询分页
        $news=M('News');
        $num=I('num');
        $count=$news->where(array('type'=>3,'seo_news'=>0))->count();//统计数量
      
        $num1=8;
        $page       = new \Think\Page($count,$num1);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $page->setConfig('header','个会员');
                $page->setConfig('prev','上一页');
                $page->setConfig('next','下一页');
                $page->setConfig('first','首页');
                $page->setConfig('last','末页');
                $show = $page->show();
        //$show       = $Page->show();// 分页显示输出
                //共几页
                $aa=ceil($count/$num1);

        //新闻查询
               // $num=2;
                if ($num) {
                   $rule=$news->where(array('type'=>3,'seo_news'=>0))->order('create_time desc')->limit(12)->page($num)->select();
                }else{
                    $rule=$news->where(array('type'=>3,'seo_news'=>0))->order('create_time desc')->limit($page->firstRow.','.$page->listRows)->select();
                }
        
        //dump($news);

        $this->assign('num',$aa);
        $this->assign('header',$header);

        $this->assign('page',$show);// 赋值分页输出
        $this->assign('count',$count);
        $this->assign('rule',$rule);

        $this->display();
    }

    /*
     * 新闻资讯列表
     * */
    public function getNewsList(){
    	if(IS_AJAX){
           $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
           $listRows = intval(I('listRows'))?intval(I('listRows')):10;  
           $list     = M('News')
                     ->limit($firstRow,$listRows)
                     ->where(array('type'=>1 , 'status'=>1))
                     ->select();
           $this->ajaxReturn(
           	   array(
           		   'data'  => $list,
           		   'total' => M('News')->where(array('type'=>1 , 'status'=>1))->count()
           	   )
           );
        }
    }
    
    /*
     * 新闻资讯详情
     * */
    public function news_info(){
        $id = intval(I('id'));
        if($id){
            $data = M('News')
                  ->where(array('id'=>$id,'status'=>1,'seo_news'=>0))
                  ->find(); 
            $this->assign('data' , $data);
            foreach ($data as $v) {
              $type=$v['type'];

            }
            //获取最新10新闻
            $news = M('News');
            $xin_news = $news ->where(array('type'=>1,'seo_news'=>0))->order('create_time desc')->limit(20)->select();
            //获取最新10条最新商品
            $goods = M('Shop_data')
            ->join('tp_mall_goods ON tp_shop_data.member_id=tp_mall_goods.member_id')
            ->order('create_time desc')
            ->limit(20)->select();
            //获取最新10个店铺
            $shop = M('Shop_data')->where(array('status'=>1))->order('time desc')->limit(20)->select();
            $this->assign('news',$xin_news);
            $this->assign('goods',$goods);
            $this->assign('shop',$shop);

            $this->assign('type',$type);
            $this->display();
        }
    }
}