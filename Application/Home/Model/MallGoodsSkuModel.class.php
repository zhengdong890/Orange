<?php
 /**
 * 商品sku组合属性模块
 * @author 幸福无期
 * @email 597089187@qq.com
 */
namespace Home\Model;
use Think\Model;
class MallGoodsSkuModel extends Model{   
   protected $tableName = 'sku'; //切换检测表

   /**
    * 获取属性 根据属性组合id
    * @access  public
    * @param   int|array $id 属性组合id
    * @return         
    */   
    public function getSkuById($id , $field = ''){
        if(!is_array($id)){
            $ids = array($id);
        }else{
            $ids = $id;
        }
        $ids   = implode(',' , $ids);
        $model = M('Sku')->where(array('sku_id'=>array('in' , $ids)));
        $field && ($model = $model->field($field));    
    	$data  = $model->select();
        if(is_array($id)){
            return $data;
        }else{
            return $data[0];
        }
    }

   /**
    * 获取属性 根据sku_code
    * @access  public
    * @param   int|array $id 属性组合id
    * @return         
    */   
    public function getSkuByCode($sku_code , $field = ''){
        if(!is_array($sku_code)){
            $sku_code = array($sku_code);
        }
        $sku_code = implode(',' , $sku_code);
        $model = M('Sku')->where(array('sku_code'=>array('in' , $sku_code)));
        $field && ($model = $model->field($field));    
    	$data  = $model->select();
        return $data;
    } 

   /**
    * 检测商品库存数量 并修改销售量
    * @access  public
    * @param   int $id     属性组合id
    * @param   int $number 更改数量 
    * @return         
    */ 
    public function checkNumber($id , $number){
    	$id     = intval($id);
    	$number = intval($number);
    	if($id == 0 || $number == 0){
            return array('status' => 0 , 'msg' => '商品数量错误');
    	}

        /*更新商品库存*/
        $model_sku = M('sku');
        $model_sku->number = array('exp',"number-{$number}");//库存减少
        $r = $model_sku->where(array('sku_id'=>$id ,'number'=>array('egt',$number)))->save();
        if($r === false || $r == 0){
	        return array('status' => 0 , 'msg' => '库存不足');
        } 
        return array('status' => 1);
    }

   /**
    * 修改商品销售量
    * @access  public
    * @param   int $id     属性组合id
    * @param   int $number 更改数量 
    * @return         
    */ 
    public function changeNumber($id , $number){
    	$id     = intval($id);
    	$number = intval($number);
    	if($id == 0 || $number == 0){
            return false;
    	}
        $r = M('Sku')->where(array('sku_id'=>$id))->setInc('sale_num',$number);
        $goods_id = M('Sku')->where(array('sku_id'=>$id))->getField('goods_id');
        M('Mall_goods')->where(array('id'=>$goods_id))->setInc('sale_num',$number);
    }
}