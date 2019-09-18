<?php
namespace Home\Controller;
use Think\Controller;
class Member_mypointsController extends Controller{
	//我的积分
	public function myPoints(){
		$data = $_SESSION['member_data'];
		$member_id =$data[id];
		$mem = M('member')->where(array('id'=>$member_id))->find();
		$this->assign('mem',$mem);
		//$time = time();
		//三个月时间
		$be = mktime(0,0,0,date('m')-3,1,date('y'));
		$map['time'] = array('gt',$b);
		//积分查询
		$score =M('Member_score_history');
		$count      = $score->where(array('is_over'=>1,'member_id'=>$member_id))->count();
		$num1=8;
        $page       = new \Think\Page($count,$num1);// 实例化分页类 
        $show  = $page->getPage();
        $page->setConfig('header','个会员');
                $page->setConfig('prev','上一页');
                $page->setConfig('next','下一页');
                $page->setConfig('first','首页');
                $page->setConfig('last','末页');
                $show = $page->show();
		$mem_score= $score
					->where(array('is_over'=>1,'member_id'=>$member_id))
					->order('time desc')
					->limit($page->firstRow.','.$page->listRows)
					->select();
		$aa=ceil($count/$num1);
		$this->assign('num',$aa);
		$this->assign('page',$show);// 赋值分页输出
		$this->assign('sco',$mem_score);
		$this->display();
	}
}