<?php
/**
 * 共享商品seo业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class GoodsSeoModel extends Model{
    protected $tableName='Goods_seo';
    /**
     * 商品seo添加
     * @access public
     * @return array $result 执行结果
     */
    public function goodsSeoAdd($data_){
        $data = array(
            'goods_id'  => intval($data_['goods_id']),
            'seller_id' => intval($data_['seller_id']),
            'title'     => $data_['title'],
            'keyword'   => $data_['keyword'],
            'desc'      => $data_['desc']
        );   
        /*验证数据*/
        $model  = D('Goods_seo');
        $rules  = array(
            array('goods_id','/^[1-9]\d*$/','商品id不正确',self::MUST_VALIDATE),
            array('title','require','请输入标题',self::MUST_VALIDATE),
            array('keyword','require','请输入关键字',self::MUST_VALIDATE),
            array('desc','require','请输入描述',self::MUST_VALIDATE)
        );
        if($model->validate($rules)->create($data) === false){
            return array(
                'status' => 0,
                'msg'    => $model->getError()
            );
        }
        $id = M('Goods_seo')->add($data);
        if($id === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }
        return array(
            'status' => 1,
            'msg'    => '数据添加成功',
            'id'     => $id
        );
    }
    
    /**
     * 商品seo编辑
     * @access public
     * @return array $result 执行结果
     */
    public function goodsSeoUpdate($data_){
        $data = array(
            'id'        => intval($data_['id']),
            'title'     => $data_['title'],
            'keyword'   => $data_['keyword'],
            'desc'      => $data_['desc']
        );
        /*验证数据*/
        $model  = D('Goods_seo');
        $rules  = array(
            array('id','/^[1-9]\d*$/','id不正确',self::MUST_VALIDATE),
            array('title','require','请输入标题',self::MUST_VALIDATE),
            array('keyword','require','请输入关键字',self::MUST_VALIDATE),
            array('desc','require','请输入描述',self::MUST_VALIDATE)
        );
        if($model->validate($rules)->create($data) === false){
            return array(
                'status' => 0,
                'msg'    => $model->getError()
            );
        }
        $id = M('Goods_seo')->save($data);
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
}