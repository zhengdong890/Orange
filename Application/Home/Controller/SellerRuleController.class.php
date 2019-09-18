<?php
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class SellerRuleController extends Controller {
	public function _initialize(){
        if(empty($_SESSION['member_data'])){
            header("Location:http://www.orangesha.com/login.html");
        }
        $redis = new \Com\Redis();
        /*底部帮助*/
        Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
        Hook::listen('getFooterHelp');
        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
        /*获取所有店铺的二级域名  更新*/
        $all_shop_domain = $redis->get('all_shop_data' , 'array');//获取redis的缓存
        $id = $_SESSION['member_data']['id'];
        $this->assign('domain' , $all_shop_domain[$id]['domain']?$all_shop_domain[$id]['domain']:$id);
        $this->assign('help' , $help);
    }

    public function ruleList(){
        $id = I('id');   
	   	$data = M('Seller_rule')->where(array('status'=>1))->select();  
	   	$list = get_child($data);
        $this->assign('pid' , $pid);
        $file =$list[0]['child'][1]['content'];
        //dump($list[0]['child'][1]['content']);
        file_put_contents('abc.txt' , $list[0]['child'][1]['content']);

	   	$this->assign('list' , $list);
        $this->assign('id',$id);
	   	$this->display();
    }
}