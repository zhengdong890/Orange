<?php
/**
 * 新闻资讯业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class NewsModel extends Model{
  /**
   * 添加新闻资讯
   * @access public
   * @param  array $data   新闻资讯数据
   * @return array $result 执行结果
   */ 
  public function newsAdd($data_){  
        $data = array(
            'title'       => $data_['title'],
            'keyword'     => $data_['keyword'],
            'description' => $data_['description'],
            'content'     => $data_['content'],
            'img_url'     => $data_['img_url'],
            'status'      => intval($data_['status']) == 1 ? 1 : 0,
            'create_time' => time(), 
            'update_time' => time(),
            'seo_news'    =>$data_['seo_news']?$data_['seo_news']:'0',
            'type'        => ($data_['type'] == 2 || $data_['type'] == 3 || $data_['type'] == 4) ? $data_['type'] : 1
        );    
        /*验证数据*/
        $model  = D('News');
        $rules  = array(
            array('title','require','请输入文章标题',self::MUST_VALIDATE),
            array('keyword','require','请输入文章关键字',self::MUST_VALIDATE),
            array('description','require','请输入描述',self::MUST_VALIDATE),
            array('content','require','请输入文章内容',self::MUST_VALIDATE),
        );
        if($model->validate($rules)->create($data) === false){
           return array(
             'status' => 0,
             'msg'    => $model->getError()
           );
        } 
        $id = M('News')->add($data);
        if($id === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }
        return array(
            'status' => 1,
            'msg'    => '数据添加成功'
        );   	    
  }
  
  /**
   * 修改新闻资讯
   * @access public
   * @param  array $data   新闻资讯数据
   * @return array $result 执行结果
   */
  public function newsUpdate($data_){
      $data = array(
          'id'          => intval($data_['id']),
          'title'       => $data_['title'],
          'keyword'     => $data_['keyword'],
          'description' => $data_['description'],
          'content'     => $data_['content'],
          'img_url'     => $data_['img_url'],
          'status'      => intval($data_['status']) == 1 ? 1 : 0,
          'update_time' => time()
      );
      /*验证数据*/
      $model  = D('News');
      $rules  = array(
          array('id','/^[1-9]\d*$/','id不正确'),
          array('title','require','请输入文章标题',self::MUST_VALIDATE),
          array('keyword','require','请输入文章关键字',self::MUST_VALIDATE),
          array('description','require','请输入描述',self::MUST_VALIDATE),
          array('content','require','请输入文章内容',self::MUST_VALIDATE),
      );
      if($model->validate($rules)->create($data) === false){
          return array(
              'status' => 0,
              'msg'    => $model->getError()
          );
      }
      $id = M('News')->save($data);
      if($id === false){
          $result = array(
              'status' => 0,
              'msg'    => '修改失败'
          );
      }
      return array(
          'status' => 1,
          'msg'    => '修改成功'
      );
  }  
  
  /**
   * 删除新闻资讯
   * @access public
   * @param  array $id     新闻资讯id
   * @return array $result 执行结果
   */
  public function newsDelete($id){
      $id = intval($id);
      if(!$id){
          return array(
              'status' => 0,
              'msg'    => 'id错误'
          );
      }
      $r = M('News')->where(array('id'=>$id))->delete();
      if($r === false){
          return array(
              'status' => 0,
              'msg'    => '删除失败'
          );
      }
      return array(
          'status' => 1,
          'msg'    => '删除成功'
      );
  }
  
}