<?php
/**
 * 广告位模块业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class AdModel extends Model{
  /**
   * 添加广告
   * @access public
   * @param  array $data   基本信息 一维数组   
   * @return array $result 执行结果
   */ 
  public function goodsAdAdd($data = array()){
        $result = array(
            'status' => 1,
            'msg'    => '添加成功'
        );
        $data_ad = array(
            'type_id'    => intval($data['type_id']),//广告位置类型
            'goods_id'   => intval($data['goods_id']),
            'ad_thumb'   => $data['ad_thumb'],//
            'ad_img'     => $data['ad_img'],//
        );
        $data_ad['create_time'] = time();//设置添加时间 
        $data_ad['update_time'] = $data_ad['create_time'];//设置编辑时间
        $id = M('Ad')->add($data_ad);//添加数据到商品表
        if($id === false){              
           $result = array(
              'status' => 0,
              'msg'    => '添加失败'
           );
        }
        return $result;
  }

  /**
   * 修改广告
   * @access public
   * @param  array $data   基本信息 一维数组  
   * @return array $result 执行结果
   */ 
    public function goodsAdUpdate($data = array()){
        $result = array(
          'status' => 1,
          'msg'    => '修改成功'
        );
        $ad_goods = array(
            'id'         => intval($data['id']),//
            'type_id'    => intval($data['type_id']),//广告位置类型
            'goods_id'   => intval($data['goods_id'])
        );
        if(!$ad_goods['id']){
           return array(
              'status' => 0,
              'msg'    => 'id错误'
           );
        }
        if(!$ad_goods['goods_id']){
           return array(
              'status' => 0,
              'msg'    => 'goods_id错误'
           );
        }
    		if($data['ad_thumb']){
    		    $ad_goods['ad_thumb'] = $data['ad_thumb'];
    		}
    		if($data['ad_img']){
    		    $ad_goods['ad_img']   = $data['ad_img'];
    		}		
        $ad_goods['update_time'] = $ad_goods['create_time'];//设置编辑时间
        $r = M('Ad')->save($ad_goods);//修改数据到商品表
        if($r === false){
           $result = array(
              'status' => 0,
              'msg'    => '插入数据库失败'
           );
        }
        return $result;
  }
}

?>