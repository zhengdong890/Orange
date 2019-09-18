<?php
namespace Home\Controller;
use Think\Controller;
use my_weixin\WechatAuth;
use my_weixin\jssdk;
header("content-type:text/html;charset=utf-8");
class Play_bigwheelController extends Controller{		
	/*大转盘活动*/
	public function bigwheel(){
		if(empty($_SESSION['openid'])){
			$wechat=new WechatAuth();
			$data=$wechat->getuserdata();
			$openid=$data['openid'];
			session_start();
			$_SESSION['openid']=$openid;
		}
		$this->display();
	}
	
	/*大转盘活动中奖结果*/
	public function result(){
		if(IS_POST){
			session_start();
			if(!empty($_SESSION['openid'])){
				$data['openid']=$_SESSION['openid'];
				$where=array('openid'=>$data['openid']);
				$a=M('Wechatfans')->where($where)->find();
				if(empty($a)){
					$data['error']='您还未关注我们，请关注再进行此操作';
				}else{
					$a=M('Bigwheel')->where($where)->find();
					if($a){
						$data["status"]=1;
					}else{
						//奖品
						$arr=array('1'=>'特等奖:10000000元','2'=>'一等奖:1000000元','3'=>'二等奖:100000元',
								'4'=>'三等奖:5000元','5'=>'1000元','6'=>'幸运奖:5元');
						//设置概率数组
						$arr_1=array(1,4,20,105,170,700);
						//获取今天时间
						$time=date("Y-m-d",time());
						$todaytime=strtotime($time);
						//获取昨天时间
						$time=date("Y-m-d",strtotime("-1 day"));
						$yesteryday=strtotime($time);
						//获取前天时间
						$time=date("Y-m-d",strtotime("-2 day"));
						$daybefore=strtotime($time);
						/*特等奖处理(2天出现一次)*/
						$where=array('time'=>$yesteryday,'score'=>1);
						$a=M('Bigwheel')->where($where)->find();//查询昨天是否出现过特等奖
						if(!empty($a)){//如果昨天出现特等奖
							$arr_1['0']=0;//特等奖消除
						}else{
							$where=array('time'=>$daybefore,'score'=>1);
							$a=M('Bigwheel')->where($where)->find();//查询前天是否出现过特等奖
							if(!empty($a)){//如果前天出现特等奖
								$arr_1['0']=0;//特等奖消除
							}else{
								$where=array('time'=>$todaytime,'score'=>1);
								$a=M('Bigwheel')->where($where)->find();//查询今天是否出现过特等奖
								if(!empty($a)){//如果今天出现特等奖
									$arr_1['0']=0;//特等奖消除
								}
							}
						}
						/*一等奖处理(一天出现一次)*/
						$where=array('time'=>$yesteryday,'score'=>2);
						$a=M('Bigwheel')->where($where)->find();//查询昨天是否出现过一等奖
						if(!empty($a)){//如果昨天出现一等奖
							$arr_1['1']=0;//一等奖消除
						}else{
							$where=array('time'=>$todayday,'score'=>2);
							$a=M('Bigwheel')->where($where)->find();//查询今天天是否出现过一等奖
							if(!empty($a)){//如果今天天出现一等奖
								$arr_1['1']=0;//一等奖消除
							}
						}
						/*二等奖处理(一天出现二次)*/
						$where=array('time'=>$time,'score'=>3);
						$a=M('Bigwheel')->where($where)->count();
						if($a>=2){
							$arr_1['2']=0;
						}
						/*三等奖处理(一天出现三次)*/
						$where=array('time'=>$time,'score'=>4);
						$a=M('Bigwheel')->where($where)->count();
						if($a>=3){
							$arr_1['3']=0;
						}
						$result=get_rand($arr_1)+1;//抽奖结果
						$data["score"]=$result;//记录中了几等奖
						$data["time"]=$todaytime;//获取今天时间
						$data["result"]=$arr["$result"];//根据几等奖设置结果
						$data['num']=setnum();//设置随机编码
						M('Bigwheel')->add($data);//中奖信息存入数据库恭喜您获得
						$data["k"]=$result;//返回给js的抽奖结果
						/*session结果*/
						session_start();
						$_SESSION['data']=$data;
							
					}
				}
			}else{
				$data['error']='请使用微信浏览器';
			}
			$this->ajaxReturn($data);
		}
	}
	
	/*大转盘活动中奖结果发送费用户*/
	public function sendresult(){
		if(IS_POST){
			session_start();
			$data=$_SESSION['data'];
			$wechat=new WechatAuth();
			$k=$data['k'];
			$result=substr($data["result"],10);
			$arr=array('1'=>'特等奖','2'=>'一等奖','3'=>'二等奖','4'=>'三等奖','5'=>'四等奖','6'=>'幸运奖');
			$content="中奖编码:".$data['num'].'\n'."恭喜您赢取:".$arr["$k"]."(".$result."),";
			$wechat->send_text($data['openid'],$content);
			unset($_SESSION['data']);
		}
	}
}