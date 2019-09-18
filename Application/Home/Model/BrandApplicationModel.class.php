<?php
 /**
 * 品牌申请模块业务逻辑
 * @author 幸福无期
 * @email 597089187@qq.com
 */
namespace Home\Model;
use Think\Model;
class BrandApplicationModel extends Model{     
    protected $tableName = 'Brand_application'; //切换检测表 

   /**
    * 品牌申请
    * @access  public
    * @param   int $cat_id 商品分类id  
    * @return         
    */   
    public function application($data_){
        $data = array(
            'member_id'              => '', //卖家id
            'cat_id'                 => '', //品牌分类
            'brand_name'             => '', //品牌名称
            'brand_en_name'          => '', //品牌英文名称
            'register_code'          => '', //商标注册号
            'brand_person'           => '', //品牌所有人
            'trademark_register_img' => '', //商标注册证
            'notice_img'             => ''  //受理通知书
        );       
        $data  = array_intersect_key($data_ , $data); //获取键的交集 
        if(empty($data['member_id'])){
            return array(
                'status' => 0,
                'msg'    => '会员id错误,请登录'
            );
        } 
        /*
        if(empty($data['trademark_register_img'])){
            return array(
                'status' => 0,
                'msg'    => '商标注册证错误'
            );
        }
        if(empty($data['notice_img'])){
            return array(
                'status' => 0,
                'msg'    => '受理通知书错误'
            );
        }*/
        $data['create_time'] = time();//申请时间 
        $id = M('Brand_application')->add($data); 
        if($id === false){
            return array(
                'status' => 0,
                'msg'    => '保存数据失败'
            );    
        }
        return array(
            'status' => 1,
            'msg'    => '申请成功'
        );  
    }
    
   /**
    * 品牌申请数据检测
    * @access  public
    * @param   array $data 品牌申请数据
    * @return         
    */      
    public function checkApplication($data){
    	if(empty($data['brand_name']) && empty($data['brand_en_name'])){
            return array(
                'status' => 0,
                'msg'    => '请输入品牌名称'
            );            
    	}
        /*验证数据*/
        $model = D("Brand_application");
        $rules = array(
            array('register_code','require','商标注册号',self::MUST_VALIDATE),
            array('brand_person','require','品牌所有人',self::MUST_VALIDATE),
            array('cat_id','require','请选择正确的类目',self::MUST_VALIDATE)
        );            
        if($model->validate($rules)->create($data) === false){
            return array(
                'status' => 0,
                'msg'    => $model->getError()
            );
        }
        //查看品牌是否存在
    	$brand = M('Goods_brand')
	    	->where(array('brand_name'=>$application['brand_name']))
	    	->getField('id');
	    if(!empty($brand)){
            $r = M('Mall_category_brand')
                ->where(array('brand_id'=>$brand,'cat_id'=>$data['cat_id']))
                ->find();
            if(!empty($r)){
	            return array(
	                'status' => 0,
	                'msg'    => '商品分类下该品牌已存在'
	            );                 
            }
	    }  	
        return array(
           'status' => 1
        );             
    }
}