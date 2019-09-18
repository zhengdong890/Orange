<?php
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");

/**
 *融资租凭项目
 */
class MallController extends Controller{
	/**
	 * 商品列表
	 */
	public function index(){
		$Mall =D('mall_goods');
		$fild =  'id ,goods_name,goods_price,goods_thumb,detail_id';
		$MallData = $Mall->field($fild)->select();
		
		$arr1  = array();
		$arr2 = array();
		$arr3  = array();
		$arr4  = array();
		
		
	
		foreach( $MallData as $k3=>$v3 ){
			$arr1[] = $v3; 
			unset($MallData[$k3]);
			if( $k3==7 ){
				break;
				
			}						
		}
  		
		foreach( $MallData as $k4=>$v4 ){
			$arr2[] = $v4; 
			unset($MallData[$k4]);
			if( $k4==15 ){
				break;
				
			}						
		}
		
		
		foreach( $MallData as $k5=>$v5 ){
			$arr3[] = $v5; 
			unset($MallData[$k5]);
			if( $k5==23 ){
				break;
				
			}						
		}
		
		foreach( $MallData as $k6=>$v6 ){
			$arr4[] = $v6; 
			unset($MallData[$k6]);
			if( $k6==31 ){
				break;
				
			}						
		}
		
	
		$this->assign('arr1',$arr1);
		$this->assign('arr2',$arr2);
		$this->assign('arr3',$arr3);
		$this->assign('arr4',$arr4);
		
		$this->display();
	}
	public function panicbuy(){
        
		$cat =D('groupbuy');
		$map = array("display" =>1,"status"=>1,"position"=>1);
		$fild = 'id,num,name,modelnum,shownum';
		$cats = $cat ->field($fild)-> where($map)->limit(6)->order('id desc')->select();
		echo json_encode($cats);//将整个数组转换成json编码的数组
         //dump($cats );die;
		//$data = json_encode($cats); 
        //$this->ajaxReturn($data);

        //self::json(200,'提交成功',$cats);


	}
	public static function json($code,$message='',$data=array()){
		if(!is_numeric($code)){
               return '';
		}
		$result = array(
			'code' =>$code,
			'message'=> $message,
			'data' => $data
   
			);

        echo json_encode($result);
        exit;  

	}
}	