<?php
 /**
 * 品牌申请模块业务逻辑
 * @author 幸福无期
 * @email 597089187@qq.com
 */
namespace Admin\Model;
use Think\Model;
class BrandApplicationModel extends Model{     
    protected $tableName = 'Brand_application'; //切换检测表 

   /**
    * 品牌申请审核
    * @access  public
    * @param   int $id 审核id  
    * @param   int $status 审核状态
    * @param   var $content 审核内容        
    * @return         
    */   
    public function checkApplication($id , $status , $content){
        $status = $status == 1 ? 1 : 0;   
        if($id == 0){
            return array(
                'status' => 0,
                'msg'    => 'id错误'
            ); 
        }     
        //获取申请数据
        $application = M('Brand_application')->where(array('id'=>$id))->find();
        //不能重复审核
        if($application['status'] == 1){
            return array(
                'status' => 0,
                'msg'    => '已经审核了'
            ); 
        }
        /*更新审核结果*/
        $r = M('Brand_application')
           ->where(array('id'=>$id))
           ->save(array('status'=>$status,'content'=>$content,'check_status'=>1)); 
        if($r === false){
            return array(
                'status' => 0,
                'msg'    => '审核失败'
            );    
        }
        /*查询该品牌是否存在*/
        $brand_id = M('Goods_brand')->where(array('brand_name'=>$application['brand_name']))->getField('id');
        if(isset($brand_id)){
        	//商品品牌已经存在
        	$cat = explode(',' , $application['cat_id']);
        	foreach($cat as $v){
	            //增加分类品牌
	            $id = D('MallCategoryBrand')->brandAdd(array(
	                'cat_id'   => $v,
	                'brand_id' => $brand_id
	            ));
	        }    
	        $code = 1;   
        }else{
	        if($status == 1){
	            //增加品牌
	            $r = D('Goods_brand')->brandAdd(array(
	                'brand_name'    => $application['brand_name'],
	                'brand_en_name' => $application['brand_en_name'],
	                'status'        => 1
	            ));
	            $cat = explode(',' , $application['cat_id']);
	            $brand_id = $r['id'];
	            foreach($cat as $v){
		            //增加分类品牌
		            $id = D('MallCategoryBrand')->brandAdd(array(
		                'cat_id'   => $v,
		                'brand_id' => $brand_id
		            ));
		        }
		        $code = 2;   
	        }        	
        }
        return array(
            'status' => 1,
            'code'   => $code,
            'msg'    => '审核成功'
        );  
    }
}