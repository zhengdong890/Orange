<?php
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class AllshareController extends Controller {	
   public function index(){
		 $where['pid'] = 72;		 		 
		 $Getid = M('category')->field('id')->where($where)->select();
		 $arr =array(); 
		 foreach( $Getid as $k=>$v ){
			 $arr[$k]=$v['id'];
			 			 
		 }
	
		 $goodstr = implode(",",$arr);
		
		 $tiaoJ['cat_id'] = array('IN',$goodstr);		 
		 $goodsData=  M('goods')->where($tiaoJ)->select();	
		 $this->assign(goods_tj,$goodsData);
	
		 $this->display();
	} 
 	

}