<?php
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class GoodshaController extends Controller {	
   public function index(){
		 $GetId= $_GET['category'];		 
		 $where['pid'] = $GetId;		 		 
		 $goodlist = M('category')->field('id')->where($where)->select();
		 //$Goods = M();
		 $arr =array(); 
		 foreach( $goodlist as $k=>$v ){
			 $arr[$k]=$v['id'];
			 			 
		 }
	
		  $goodstr = implode(",",$arr);
		
		 $tiaoJ['cat_id'] = array('IN',$goodstr);		 
		 $Getgoods=  M('goods')->where($tiaoJ)->select();	
		
		 $this->assign('goodlist',$Getgoods); 
	
		 $this->display();
	} 
 public function lists(){ 
 
		$CatId= $_GET['category'];	
		$fild['cat_id'] = $CatId;
		$detailGoods=  M('goods')->where($fild)->select();
		//$arrData = array();
        foreach( $detailGoods as $kk =>$vv ){
			 
			$detailGoods['goods_thumb'] = substr($detailGoods['goods_thumb'], 1);                            
			 			 
		}
		
		
		$this->assign('goodlist',$detailGoods);

		$this->display();		

	}
 public function detail(){
		 $GetId= $_GET['id'];	
		 $mapK['id'] =  $GetId;
		 $fild = 'goods_thumb';
         $Img = M('goods')->field($fild)->where($mapK)->find();
		 $goodImg = substr($Img['goods_thumb'],1);
		 $this->assign('goodImg',$goodImg );
			 	
		 $this->display(); 
			 
		 }

		 	
 public function Goodsha(){
	 $map1['pid']=72;	 
	 $map2['pid']=81;
	 $map3['pid']=91;
	 $map4['pid']=92;
	 $map5['pid']=102;
	 $map6['pid']=106;
	 $map7['pid']=110;
	 $map8['pid']=114;
	 $map9['pid']=118;
	 $map10['pid']=118;
	 $map11['pid']=122;
	 $map12['pid']=126;
	 $data1 =   D('category')->where($map1)->select();
	 $data2 =   D('category')->where($map2)->select();
	 $data3 =   D('category')->where($map3)->select();
	 $data4 =   D('category')->where($map4)->select();
	 $data5 =   D('category')->where($map5)->select();
	 $data6 =   D('category')->where($map6)->select();
	 $data7 =   D('category')->where($map7)->select();
	 $data8 =   D('category')->where($map8)->select();
	 $data9 =   D('category')->where($map9)->select();
	 $data10 =   D('category')->where($map10)->select();
	 $data11 =   D('category')->where($map11)->select();
	 
	 $this->assign('goodlist1',$data1);
	 $this->assign('goodlist2',$data2);
	 $this->assign('goodlist3',$data3);
	 $this->assign('goodlist4',$data4);
	 $this->assign('goodlist5',$data5);
	 $this->assign('goodlist6',$data6);
	 $this->assign('goodlist7',$data7);
	 $this->assign('goodlist8',$data8);
	 $this->assign('goodlist9',$data9);
	 $this->assign('goodlist10',$data10);
	 $this->assign('goodlist11',$data11); 
		

	 	
		 $this->display(); 
			 
		 }

}