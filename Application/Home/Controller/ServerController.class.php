<?php
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class ServerController extends Controller {	
    public function server(){
        $redis = new \Com\Redis();
		/*获取Seo缓存*/
		Hook::add('navSeo','Home\\Addons\\SeoAddon');
		$param = array($redis , 4);
        Hook::listen('navSeo', $param);
		$seo = $redis->get('nav_seo4' , 'array');
        /*底部帮助*/
        Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
        Hook::listen('getFooterHelp');
        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
        
        $this->assign('help' , $help);
        $this->assign('seo',$seo);
        $this->display();
    }
    public function request(){
        //批量采购所属类目
           //所属类目获取
     /*商城商品分类缓存   更新*/
        $redis = new \Com\Redis();

        Hook::add('getCategory','Home\\Addons\\MallCategoryAddon');
        Hook::listen('getCategory');
        $mall_categorys = $redis->get('mall_category' , 'array');

        foreach($mall_categorys as $k => $v){
            if($v['level'] != '2'){
                $temp_categorys[$v['id']] = $v;   
            }
        }

        $mall_categorys = get_child($temp_categorys);
        
        $this->assign('mall',$mall_categorys);
        $this->display();
    }
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
            $this->ajaxReturn($area);
       }
   }
    

}