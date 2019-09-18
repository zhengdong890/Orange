<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +--------融资 --------------------------------------------------------------
// |  融资租凭项目前端
// +----------------------------------------------------------------------

namespace Home\Controller;

/**
 *融资租凭项目
 */
class FinanceController extends HomeController{
	/**
	 * 商品列表
	 */
	public function index(){
		
		$r=self::get_page('tender_lease','id,title,modelnum,create_time,content',array("display"=>1,"status"=>1,"verify"=>0),9,"test",4,'id desc');
		for($i=0;$i<count($r['result']);$i++){
              $r['result'][$i]['create_time']=date('Y-m-d');
		}
		
		$show =$r['pg']; 
		$list =$r['result'];
		
		dump($show);exit;
	    $this->assign('page',$show);// 赋值分页输出
		$this->assign("financelist",$list);
		$this->display();

	}
	/**
	**AJax例表



	**/
	public function test(){

		$r=self::get_page('tender_lease','id,title,modelnum,create_time,content',array("display"=>1,"status"=>1,"verify"=>0),9,"test",4,'id desc');
		for($i=0;$i<count($r['result']);$i++){
              $r['result'][$i]['create_time']=date('Y-m-d');
		}
		//dump($r);die;
		$show =$r['pg'];  
		$list =$r['result'];
		$data=json_encode($r);//将整个数组转换成json编码的数组
        $this->ajaxReturn($data);
	}
	/**
     **$table表
     **$field 字段集
     **$map 查询条件数组
     **$limitRows


	**/
	public function get_page($table='',$field='',$maps=array(),$limitRows=6,$action="tes",$pgnum=7,$order='id desc'){
        $data =D($table);
		import("Think.AjaxPage");
		$fild=$field;
		$map = $maps;
  
		$count=$data->where($map) ->count();
		$limitRows = 16; // 设置每页记录数
		$Page = new \Think\AjaxPage($count, $limitRows,$action);// 实例化分页类 传入总记录数和每页显示的记录数
        $Page->rollPage   = $pgnum;	
        $nowPage = isset($_GET['p'])?$_GET['p']:1;
        $list['result'] = $data->field($fild)->where($map)->order($order)->page($nowPage.','.$Page->listRows)->select();       
		$show = $Page->show();
		dump($show);exit;
	    $list['pg']=$show;//将分页显示也放到数组里
		return $list;
	}
	
	public function tenancy(){
		
        $fild = 'id,pid,name,title';
		$cat = D('category');
		$cats =$cat->field($fild)->where('pid=216')->select();

        $data =D('finance_company');
		import("Think.AjaxPage");
		
		$fild='id,cpname,content';
		$map = array("display"=>1);
  
		$count=$data->where($map) ->count();
		$limitRows = 16; // 设置每页记录数
		$Page = new \Think\AjaxPage($count, $limitRows,"tes");// 实例化分页类 传入总记录数和每页显示的记录数
		$Page->rollPage   = 3;	
        //$limit_value = $page->firstRow . "," . $page->listRows;
        //dump($limit_value);die;
        $nowPage = isset($_GET['p'])?$_GET['p']:1;
        $list = $data->field($fild)->where($map)->order('id desc')->page($nowPage.','.$Page->listRows)->select();
		//$list = $data->field($fild)->where($map)->order('id desc')->limit(6)->select();		
		//$Page->rollPage   = 4;		
		//$page->setConfig('first','首页');
        //$this->lastSuffix && $this->config['last'] = $this->totalPages;//去掉总页数显示
        
		$show = $Page->show();
		
		//dump($show);dump($list);die;
	    $this->assign('page',$show);// 赋值分页输出
	    $this->assign('cat',$cats);
		$this->assign("tenancylist",$list);
		$this->display();

	}
	public function tes(){  //分页Ajax
		
		$data =D('finance_company');
		import("Think.AjaxPage");
		$fild='id,cpname,content';
		$map = array('display'=>1);
  
		$count=$data->where($map) ->count();
		$limitRows = 16; // 设置每页记录数
		$Page = new \Think\AjaxPage($count, $limitRows,"tes");// 实例化分页类 传入总记录数和每页显示的记录数
        $Page->rollPage   = 3;	
        //$limit_value = $page->firstRow . "," . $page->listRows;
         //$this->lastSuffix && $this->config['last'] = $this->totalPages;//去掉总页数显示
        //dump($limit_value);die;
         $nowPage = isset($_GET['p'])?$_GET['p']:1;
        $list['result'] = $data->field($fild)->where($map)->order('id desc')->page($nowPage.','.$Page->listRows)->select();
		       
		$show = $Page->show();
	    $list['pg']=$show;//将分页显示也放到数组里
		$jdata=json_encode($list);//将整个数组转换成json编码的数组
		// echo $jdata;
         //dump($jdata);die;
        $this->ajaxReturn($jdata);
	}
	

	public function lists($category){
		$gt=I('get.');//获取表单提交的数据
		$cataid=get_cateidbyname($category);
		$map=array("category_id"=>$cataid['id'],"display"=>1,"status"=>1);
		$good=D("Goods");

		$pg=empty($gt['p'])?1:$gt['p'];
		$fild='id,name,title,cover_id,price,sales,uid,create_time';
		$list=$good->field($fild)->where($map)->order('create_time')->page($pg.',15')->select();
		$count=$good->where($map)->count();//获取符合规定的数据总数
		$Page = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
		$show = $Page->show();// 分页显示输出
		$this->assign('page',$show);// 赋值分页输出
		$this->assign("goodlist",$list);
		$this->display();
	}

	/**
	 * 商品详情页
	 * @param $id 商品id
	 */
	public function detail($id){
		$map["id"]=$id;
		$map["status"]=1;
		$map["display"]=1;
		$info=M('Goods')->where($map)->find();
		if(empty($info)){
			$this->assign("mes","商品不存在或已下架");
		}else{
			$info["pictures"]=explode(',',$info["pictures"]);
			$info["attr"]=(array)json_decode($info["attr"]);
		}
		$this->assign("info",$info);
		$this->display();
	}

}
