<?php
/**
 * SEO模块业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class SeoModel extends Model{
    public function seoUpdate($data_){
        $result = array(
            'status' => 1,
            'msg'    => '修改成功'
        );
        $data = array(
            'id'          => $data_['id'],
            'title'       => $data_['title'],
            'keyword'     => $data_['keyword'],
            'description' => $data_['description']
        );
        $model  = D('Seo');
        $rules  = array(
            array('id','/^([1-9]\d*)|0+$/','id不能为空'),
            array('title','require','必须输入描述',self::EXISTS_VALIDATE),
            array('keyword','require','必须输入关键字',self::EXISTS_VALIDATE),
            array('description','require','必须输入描述',self::EXISTS_VALIDATE)
        );
        if($model->validate($rules)->create($data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $model->getError()
            );
            return $result;
        }     
        $r = M('Seo')->save($data);
        if($r === false){
            $result = array(
                'status' => 1,
                'msg'    => '修改失败'
            );
        }
        return $result;
    }
}