<?php
/**
 * 积分商品模块业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class IntegrationGoodsModel extends Model{
   protected $tableName = 'Integration_goods';
    
   /**
    * 添加商品
    * @access public
    * @param  array $data_goods    商品基本信息 一维数组 
    * @return array $result 执行结果
    */ 
    public function goodsAdd($data = array()){
        $data_goods = array(
            'goods_name'    => '',//商品名称
            'goods_price'   => '',//商品价格
            'goods_market_price'   => '',//商品市场价格
            'goods_thumb'   => '',//商品缩略图
            'goods_img'     => '',//商品主图
            'status'        => '',
            'sort'          => ''
        );
        $data_goods  = array_intersect_key($data , $data_goods); //获取键的交集 
        $data_goods['create_time'] = time();//设置上架时间  
        $id = M('Integration_goods')->add($data_goods);//添加数据到商品表  
        if($id === false){                           
            $result = array(
                'status' => 0,
                'msg'    => '商品添加失败'
            );
        }else{
            $result = array(
                'status' => 1,
                'msg'    => '商品添加成功'
            );
        }
        return $result;
  }

  /**
   * 修改商品
   * @access public
   * @param  array $data_goods    商品基本信息 一维数组 
   * @param  array $goods_gallery 商品相册     二维数组 array(array('图片描述','上传路径'))   
   * @return array $result 执行结果
   */ 
    public function goodsUpdate($data = array() , $goods_gallery = array()){
        $data_goods = array(
            'goods_name'    => '',//商品名称
            'goods_price'   => '',//商品价格
            'goods_market_price'   => '',//商品市场价格
            'goods_thumb'   => '',//商品缩略图
            'goods_img'     => '',//商品主图
            'status'        => '',
            'sort'          => ''
        );
        $id = $data['id'];
        $data_goods  = array_intersect_key($data , $data_goods); //获取键的交集 
        $r  = M('Integration_goods')->where(array('id'=>$id))->save($data_goods);//修改数据到商品表
        if(!$r){
           return array(
              'status' => 0,
              'msg'    => '商品插入数据库失败'
           );           
        }
        return array(
            'status' => 1,
            'msg'    => '商品修改成功'
        );
    }

  /**
   * 商城商品删除
   * @access public
   * @param  int   $goods_id 商品id
   * @return array $result 执行结果
   */ 
    public function goodsDelete($goods_id){
  	    $result = array(
           'status' => 1,
           'msg'    => '商品删除成功'
        );
  	    $goods_id = intval($goods_id);
  	    if(!$goods_id){
     	      return array(
                'status' => 0,
               'msg'    => 'id错误'
            );
        }
        /*删除商品数据*/
        //获取商品缩略图
        $imgs = M('Integration_goods')->where(array('id'=>$goods_id))->Field('goods_thumb,goods_img')->find();
        $r    = M('Integration_goods')->where(array('id'=>$goods_id))->delete();
	      if($r !== false){
		        unlink($imgs['goods_thumb'],$imgs['goods_img']);//删除图片
  	    }else{
    		  $result = array(
    		     'status' => 0,
    		     'msg'    => '商品删除失败'
    		  );
  	    }
	      return $result;
    }
  
  /**
   * 检测商品数据 合法性
   * @access public
   * @param  int   $goods_id 商品id
   * @return array $result 执行结果
   */
    public function checkGoodsData($data , $type = 1){
        $valide = $type == 1 ? self::MUST_VALIDATE : self::EXISTS_VALIDATE;
        /*验证数据*/
        $goods = D("Integration_goods");
        $rules = array(
            array('goods_name','require','必须输入商品名',$valide),
            array('goods_price','require','请输入商品价格',$valide),
            array('goods_price','/^[1-9]+[0-9]*?$/','请输入正确的价格',$valide),
            array('goods_market_price','require','请输入商品市场价格',$valide),
            array('goods_market_price','/^[0-9]+(.[0-9]{1,2})?$/','请输入正确的市场价格',$valide)
        );
        if($goods->validate($rules)->create($data) === false){
            $result = array(
              'status' => 0,
              'msg'    => $goods->getError()
            );
            return $result;
        }
        return array(
          'status' => 1
        );
    }
  
  /**
   * 商品批量修改排序
   * @access public
   * @param  array $data   排序的商品数据
   * @return array $result 执行结果
   */
  public function sortAllChange($data){
      $result = array(
          'status' => 1,
          'msg'    => '修改成功'
      );
      $sql_arr = array(
          'sort'   => " SET sort = CASE id"
      );
      $ids = array();
      foreach($data as $k => $v){
          $ids[] = $k;
          $v = intval($v);
          $sql_arr['sort'] .= " WHEN {$k} THEN '{$v}'";
      }
      $ids = implode(',' , $ids);
      $sql_arr['sort'] .= ' END';
      $sql = "UPDATE tp_integration_goods".$sql_arr['sort']." where id IN ($ids)";
      $r   = M()->execute($sql);
      if($r === false){
          $result = array(
              'status' => 0,
              'msg'   => '添加失败'
          );
      }
      return $result;
  }  

}

?>