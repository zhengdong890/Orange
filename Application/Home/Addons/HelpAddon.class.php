<?php
namespace Home\Addons;
use Think\Controller;
/*
 * 帮助缓存处理
 * */
class helpAddon extends Controller{
    /**
     * 主页商城商品redis缓存处理
     */
    public function getFooterHelp(){
        $redis = new \Com\Redis();
        $help = $redis->get('footer_help' , 'array');
        if(!$help){
            $help = M('Help_category')
	              ->field("id,pid,name,thumb")
	              ->where(array('status'=>1))
	              ->order('sort')
                  ->select();
            $help = get_child($help);
            $redis->set('footer_help' , $help);
        }
    }
}