<?php
namespace Home\Model;
use Think\Model;
/**
 * 新闻模块业务逻辑
 * @author 幸福无期
 */
class NewsModel extends Model{ 
	protected $tableName = 'News'; //切换表

   /**
    * 获取新闻
    * @param  int   $type 新闻类型
    * @param  int   $type 操作类型
    * @param  array $data 附加数据
    * @return array result     返回结果
    */    
    public function getNews($condition = array()  , $order = 'id desc'){
        $news = M('News')
	      ->where($condition)
	      ->order($order)
	      ->select();
	    return $news;
    }
}