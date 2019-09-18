<?php
namespace Admin\Controller;
use Com\Auth;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class AreaController extends CommonController{    
    /* 
     * ajax获取地区数据
     * */
    public function getArea(){
	   if(IS_AJAX){
	   		$data = $_POST;
	   		$area = M('Area')
	   		      ->where(array('parent_no'=>$data['area_no']))
	   		      ->field('area_no,area_name,id')
	   		      ->select();
	   		$result="<option value='0'>请选择...</option>";
	   		foreach($list as $v){
	   			$result.="<option value={$v['area_no']}>{$v['area_name']}</option>";
	   		}
	   		$this->ajaxReturn($area);
	   }
    }
}