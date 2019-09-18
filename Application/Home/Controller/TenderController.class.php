<?php
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class TenderController extends Controller {	
    public function _initialize(){
        $redis = new \Com\Redis();
        /*底部帮助*/
        Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
        Hook::listen('getFooterHelp');
        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
        /*获取Seo缓存*/
		$redis = new \Com\Redis();
		Hook::add('navSeo','Home\\Addons\\SeoAddon');
		$param = array($redis , 6);
        Hook::listen('navSeo', $param);
		$seo = $redis->get('nav_seo6' , 'array');
        $this->assign('help' , $help);
    }
    
    public function index(){	
        /*分页*/
        $count = M('Tender_lease')->count();
        $Page  = new \Think\Page($count,8);// 实例化分页类 
        $show  = $Page->getPage();
        $fild  ='*';
        $list  = M('Tender_lease')->field($fild)->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach( $list as $ky=>$vy ){
			$list[$ky]['create_time']=date('Y-m-d',$vy['create_time']);
		}	
        unset($paramer['p']);
        $html  = pageHtml('http://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['PATH_INFO'] , $show);
		/*获取banner*/
		$thumb = M('Tender_banner')->where(array('id'=>3))->getField('thumb');
		$conpany = D('conpanyimg')->where(array('flag'=>array('eq',0)))->select();
		/*中标融资租赁*/
		$select = M('Tender_select')
        		->where(array('status'=>1))
        		->select();

        //融资商标图片
        $img=D('Tender_company')->where(array('is_tj'=>1))->select();
        //dump($img);
        $this->assign('img',$img);
		$this->assign("seo",$seo);
		$this->assign("conpany",$conpany);
		$this->assign("list",$list);
		$this->assign("select",$select);
		$this->assign('html',$html);
		$this->assign("thumb",$thumb);
		$this->assign('list_json',json_encode($list,JSON_FORCE_OBJECT));
		$this->display();
	}
	
	public function tenancy(){
	    $paramer = I();//获取查询参数
	    $where   = array();
	    if($paramer['type_id']){//行业
	        $where['type_id'] = $paramer['type_id'];
	    }
	    if($paramer['area_id']){//区域
	        $where['area_id'] = $paramer['area_id'];
	    }
	    if($paramer['brand_id']){//品牌
	        $where['brand_id'] = $paramer['brand_id'];
	    }
	    /*分页*/
	    $count = M('Tender_company')->where($where)->count();
	    $Page  = new \Think\Page($count,10);// 实例化分页类
	    $show  = $Page->getPage();
	    $fild  ='*';
	    $list  = M('Tender_company')->where($where)->limit($Page->firstRow.','.$Page->listRows)->field($fild)->select();
	    /*获取区域*/
	    $area_ = M('Area')->where(array('area_level'=>1))->select();
	    foreach($area_ as $v){
	        $area[$v['id']] = $v;
	    }
	    foreach($list as $k=>$v){
	        $list[$k]['area_name'] = $area[$v['area_id']]['area_name'];
	    }
	    /*获取类型*/
	    $type = M('Company_type')->select();
	    /*获取品牌*/
	    $brand = M('Company_brand')->select();
	    /*url参数格式组装*/
	    foreach($paramer as $k => $v){
	        $paramer[$k] = "$k=$v";
	    }
	    $area = array_chunk($area, 8);
	    unset($paramer['p']);
	    $page  = pageHtml(U('Tender/tenancy') , $show, implode('&' , $paramer));
	    $this->assign("seo",$seo);
	    $this->assign('paramers',json_encode(I()));
	    $this->assign("list",$list);
	    $this->assign("type",$type);
	    $this->assign("brand",$brand);
	    $this->assign("area",$area[0]);
	    $this->assign("page",$page);
	    $this->display();
	}	
}