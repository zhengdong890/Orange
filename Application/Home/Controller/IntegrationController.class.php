<?php
/*
 * 集成项目模块
 * */
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class IntegrationController extends Controller {
    public function _initialize(){
        $redis = new \Com\Redis();
        /*底部帮助*/
        Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
        Hook::listen('getFooterHelp');
        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
        /*获取Seo缓存*/
        $redis = new \Com\Redis();
		Hook::add('navSeo','Home\\Addons\\SeoAddon');
		$param = array($redis , 7);
        Hook::listen('navSeo', $param);
		$seo = $redis->get('nav_seo7' , 'array');
        $this->assign("seo",$seo);
        $this->assign('help' , $help);
    }
    
    /*
     * 集成项目
     * */    
    public function index(){	
        /*分页*/
        $count = M('Integrated_lease')->count();
        $Page  = new \Think\Page($count,8);// 实例化分页类 
        $show  = $Page->getPage();
        $fild  ='*';
        $list  = M('Integrated_lease')->field($fild)->limit($Page->firstRow.','.$Page->listRows)->select();
		foreach( $list as $ky=>$vy ){
			$list[$ky]['create_time']=date('Y-m-d',$vy['create_time']);
		}
		$html  = pageHtml('http://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['PATH_INFO'] , $show);
		/*获取banner*/
	    $conpany = D('conpanyimg')->where(array('flag'=>array('eq',0)))->select();
		$thumb = M('Integrated_banner')->where(array('id'=>3))->getField('thumb');
		/*中标集成项目*/
		$select = M('Integrated_select')
                  ->where(array('status'=>1))
                  ->select();
        $img= D('Integrated_company')->where(array('is_tj'=>1))->select();
        $this->assign('img',$img);
		$this->assign("list",$list);
		$this->assign("select",$select);
		$this->assign("conpany",$conpany);
        $this->assign('html',$html);
		$this->assign("thumb",$thumb);
		$this->assign('list_json',json_encode($list,JSON_FORCE_OBJECT));
		$this->display();	
	}
	
	/*
	 * 集成商库
	 * */
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
	    $count = M('Integrated_company')->where($where)->count();
	    $Page  = new \Think\Page($count,10);// 实例化分页类
	    $show  = $Page->getPage();
	    $fild  ='id,name,img,content,area_id,keyword,url';
	    $list  = M('Integrated_company')->where($where)->limit($Page->firstRow.','.$Page->listRows)->field($fild)->select();
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
	    $page  = pageHtml(U('Integration/tenancy') , $show, implode('&' , $paramer));
	    $this->assign('paramers',json_encode(I()));
	    $this->assign("list",$list);
	    $this->assign("type",$type);
	    $this->assign("brand",$brand);
	    $this->assign("area",$area[0]);
	    $this->assign("page",$page);
	    $this->display();
	}	
	
  /**
   * 集成项目信息申请添加
   * @access public
   */ 
   public function integratedAdd(){
   	   if(IS_POST){
   	   	   $data   = I();
           $result = D('Integrated')->integratedAdd($data);
           $this->ajaxReturn($result);
   	   }	   
   }
}
