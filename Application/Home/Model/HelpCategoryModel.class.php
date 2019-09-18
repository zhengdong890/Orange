<?php
namespace Home\Model;
use Think\Model;
/**
 * 帮助模块
 * @author 幸福无期
 */
class HelpCategoryModel extends Model{
    protected $tableName='Help_category'; //切换检测表
    /**
     * 获取帮助分类  
     * @access public
     * @return array $result 执行结果
     */
    public function getHelpFooter(){
        $help = M('Help_category')
              ->field("id,pid,name,thumb")
              ->where(array('status'=>1))
              ->order('sort')
              ->select();	
		return get_child($help);
    }
  public function redisCatName($redis){
        $help = $redis->get('footer_help' , 'array');
      if(!$help){
          $help = M('Help_category')->field("id,pid,name,thumb")->order('sort')->select();            
          $redis->set('footer_help',serialize($help));
      }
      return $help;
    }
}