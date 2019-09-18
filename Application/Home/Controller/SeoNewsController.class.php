<?php
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class SeoNewsController extends Controller {
    public function index(){
      $id = I('id')?I('id'):'77';
    	$news = M('News');//新闻查询
    	$xin_news = $news 
            ->where(array('type'=>1,'seo_news'=>1))
            ->order('create_time desc')
            ->limit(20)->select();
        //新闻详情
        $data=$news->where(array('id'=>$id))->find();

       //获取最新10条最新商品
        $goods = M('Shop_data')
            ->where(array('tp_mall_goods.status'=>1))
            ->join('tp_mall_goods ON tp_shop_data.member_id=tp_mall_goods.member_id')
            ->order('create_time desc')
            ->limit(20)->select();
            //获取最新10个店铺
            $shop = M('Shop_data')->where(array('status'=>1))->order('time desc')->limit(20)->select();

        //读取文件
         $dir = "/data/wwwroot/default/Public/Seo/";  //要获取的目录
        // var_dump(is_dir($dir));
            //先判断指定的路径是不是一个文件夹
          if (is_dir($dir)){
               if ($dh = opendir($dir)){
                  while (($file = readdir($dh))!= false){
                        //文件名的全路径 包含文件名
             $filePath = 'http://orangesha.com/Public/Seo/'.$file;
             // http://static.grainger.cn/product_images_new/220/4F0/2016031625.jpg
             //echo "<img src='".$filePath."'/>";
             //echo "$filePath";
             //echo "<br>";
             $arr[]=$filePath;
             
                }
                //统计图片数量
            $count =count($arr);
            //获取随机图片
            $k =mt_rand(0,$count);
            $k1 =mt_rand(0,$count);
            $k2 =mt_rand(0,$count);
            $k3 =mt_rand(0,$count);
            $k4 =mt_rand(0,$count);
            $k5 =mt_rand(0,$count);


            $img = $arr[$k];
            $img1 = $arr[$k1];
            $img2 = $arr[$k2];
            $img3 = $arr[$k3];
            $img4 = $arr[$k4];
            $img5 = $arr[$k5];


            //echo "<img src='".$img."'/>";
           closedir($dh);
            }
     }
        $this->assign('data',$data);
        $this->assign('img',$img);
        $this->assign('img1',$img1);
        $this->assign('img2',$img2);
        $this->assign('img3',$img3);
        $this->assign('img4',$img4);
        $this->assign('img5',$img5);
    	  $this->assign('news',$xin_news);
    	  $this->assign('goods',$goods);
    	  $this->assign('shop',$shop);
        $this->display('news');
    }
}