<?php
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class PurchaseController extends CommonController{   
   /**
    * 批量采购信息列表
    * @access public
    */
   public function purchaseList(){
        $id = I('id');
        $img = M('Purchase_img')
              ->where(array('purchase_id'=>$id))
              ->find();
              $img1 = $img['thumb'];
              $img2 = $img['thumb'];
              $img3 = $img['thumb'];
        if($img){
          unlink($img1);
          unlink($img2);
          unlink($img3);
          M('Purchase_img')->where(array('purchase_id'=>$id))->delete();
              }
          M('Purchase')->delete($id);
       if(IS_AJAX){

           $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
           $listRows = intval(I('listRows'))?intval(I('listRows')):10;
           $list     = M('Purchase')
                     ->order('update_time desc')
                     ->limit($firstRow,$listRows)
                     ->select();
           $this->ajaxReturn(array('data'=>$list,'total'=>M('Purchase')->count()));
       }else{

           $this->display();
       }
   }
   
  /**
   * 批量采购信息添加
   * @access public
   */ 
   public function purchaseAdd(){
   	   if(IS_POST){
   	   	   $data   = I();
           $result = D('Purchase')->purchaseAdd($data);
           $this->ajaxReturn($result);
   	   } 	   
   }


  /**
   * 批量采购信息修改
   * @access public
   */ 
   public function purchaseUpdate(){
   	   if(IS_POST){
   	   	   $data   = I();
           $result = D('Purchase')->purchaseUpdate($data);
           $this->ajaxReturn($result);
   	   }else{
        $data =I();
        $r=M('Purchase')
              ->where(array('id'=>$data['id']))
               ->field('id,title,cat_name,status,des,area,create_time,kh_name,phone,num,unit,deadline,price_range,price_type1,price_type2')
              ->find();

            //批量采购所属类目
           //所属类目获取
     /*商城商品分类缓存   更新*/
            //获取商品分类
            $categorys  = M("Mall_category")
                        ->where(array('status'=>'1'))
                        ->order('sort')
                        ->select();
            $mall_category_tree = array();
            foreach($categorys as $v){
                if($v['level'] != '2'){
                  $mall_category_tree[] = $v;
                }
            }  
            $mall_category_tree = get_child($mall_category_tree);

        
        $this->assign('mall',$mall_category_tree);
        $this->assign('data',$r);
        $this->display();
       }
   }

  /**
   * 批量采购信息删除
   * @access public
   */ 
   public function purchaseDelete(){
   	   if(IS_POST){
   	   	  $id     = I('id');
          die;
   	   	  $result = D('Purchase')->purchaseDelete($id);
          $this->ajaxReturn($result);
   	   }
   }   
/**************************************批量采购审核*******************************************/
   /**
    * 批量采购审核列表
    * @access public
    */
   public function checkList(){
       if(IS_AJAX){
           $where    = array(
               'is_check'     => 0,
               'check_status' => 0
           );
           $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
           $listRows = intval(I('listRows'))?intval(I('listRows')):10;
           $field    = '*';
           $list     = M('Purchase')->limit($firstRow,$listRows)->field($field)->where($where)->select();
           $this->ajaxReturn(array('data'=>$list,'total'=>M('purchase _lease')->count()));
       }else{
           $this->display();
       }
   }
   //批量采购详情
   public function details(){
    $data= I();
    $id = $data['id'];
    $pur=M('Purchase')
        ->where(array('id'=>$id))
        ->field('id,title,cat_name,des,status,area,create_time,kh_name,phone,num,unit,deadline,price_range,price_type1,price_type2')
        ->find();
     
    $this->assign('data',$pur);
    $this->display();
   }

   public function check_x(){
    //审核采购信息
      $data = I();
      if($data['id']==''){
        $this->ajaxReturn(array('status'=>0,'msg'=>'请传id过来'));
      }
      $r= M('Purchase')
            ->where(array('id'=>$data['id']))
            ->save(array('status'=>$data['status']));
      if($r){
        $this->ajaxReturn(array('status'=>1,'msg'=>'审核成功'));

      }else{
        $this->ajaxReturn(array('status'=>0,'msg'=>'审核失败'));
      }
      
   }

   public function check_no(){
        if(IS_AJAX){
         $data = I();
          if($data['id']==''){
        $this->ajaxReturn(array('status'=>0,'msg'=>'请传id过来'));
      }
      $r= M('Purchase')
            ->where(array('id'=>$data['id']))
            ->save(array('status'=>$data['status']));
      if($r){
        $this->ajaxReturn(array('status'=>1,'msg'=>'取消审核成功'));

      }else{
        $this->ajaxReturn(array('status'=>0,'msg'=>'取消审核失败'));
      }
        }
         
   }
    
   /**
    * 批量采购审核
    * @access public
    */
   public function purchaseCheck(){
       if(IS_POST){
           $data   = I();
           $result = D('purchase')->purchaseCheck($data);
           $this->ajaxReturn($result);
       }
   }
    
   
  /**
   * 批量采购公司列表
   * @access public
   */ 
   public function companyList(){
   	   $companys  = D('Purchase')->getCompany();
   	   $this->assign('companys' , $companys);
       $this->display();
   }

  /**
   * 新增批量采购公司
   * @access public
   * @return array $result 执行结果
   */ 
   public function companyAdd(){
   	   if(IS_POST){
   	   	    $data   = I();		
	   	    /*上传图片*/
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 3145728 ;// 设置附件上传大小
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
			// 上传文件
			$info = $upload->upload();
			if($info) {
				$data['img'] = $upload->rootPath.$info['img']['savepath'].$info['img']['savename'];//获取图片路径								
			}	
      if($info) {
        $data['tj_thumb'] = $upload->rootPath.$info['tj_thumb']['savepath'].$info['tj_thumb']['savename'];//获取图片路径               
      } 
           $result = D('Purchase')->companyAdd($data);
           $this->ajaxReturn($result);
   	   }else{
   	       $company_type  = M('Company_type')->select();
   	       $company_brand = M('Company_brand')->select();
   	       $province      = M('Area')->where(array('area_level'=>1))->select();
   	       $this->assign('company_type' , $company_type);
   	       $this->assign('company_brand' , $company_brand);
   	       $this->assign('province' , $province);
           $this->display();
       }
   }

  /**
   * 修改批量采购公司信息
   * @access public
   * @return array $result 执行结果
   */ 
   public function companyUpdate(){
   	   if(IS_POST){
   	   	    $data   = I();		
	   	    /*上传图片*/
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 3145728 ;// 设置附件上传大小
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
			// 上传文件
			$info = $upload->upload();
			if($info) {
				$data['img'] = $upload->rootPath.$info['img']['savepath'].$info['img']['savename'];//获取图片路径								
			}	
        if($info) {
        $data['tj_thumb'] = $upload->rootPath.$info['tj_thumb']['savepath'].$info['tj_thumb']['savename'];//获取图片路径               
      } 
           $result = D('Purchase')->companyUpdate($data);
           $this->ajaxReturn($result);
   	   }else{
   	   	   $id            = I('id');
   	   	   $company       = D('Purchase')->getOneCompany($id);
   	   	   $company_type  = M('Company_type')->select();
   	   	   $company_brand = M('Company_brand')->select();
   	   	   $province      = M('Area')->where(array('area_level'=>1))->select();
   	   	   $this->assign('company' , $company);
   	   	   $this->assign('company_type' , $company_type);
   	   	   $this->assign('company_brand' , $company_brand);
   	   	   $this->assign('province' , $province);
   	   	   $this->display();
   	   }      
   } 

  /**
   * 删除批量采购公司
   * @access public
   */ 
   public function companyDelete(){
   	   if(IS_POST){
   	   	  $id     = I('id');
   	   	  $result = D('Purchase')->companyDelete($id);
          $this->ajaxReturn($result);
   	   }
   }

   /**
   * 批量采购页banner
   * @access public
   */ 
   public function purchaseBannerList(){
   	   	$list = D('Purchase')->getBanner();
        $this->assign('list' , $list);
   	   	$this->display();
   } 

  /**
   * 添加批量采购页banner
   * @access public
   * @return array $result 执行结果
   */ 
   public function purchaseBannerAdd(){
   	   if(IS_POST){
   	   	    $data   = I();		
	   	    /*上传图片*/
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 3145728 ;// 设置附件上传大小
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
			// 上传文件
			$info = $upload->upload();
			if($info) {
				$data['thumb'] = $upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];//获取图片路径								
			}	
            $result = D('purchase')->purchaseBannerAdd($data);
            $this->ajaxReturn($result);
   	   }else{
   	   	   $this->display();
   	   }      
   }

  /**
   * 修改批量采购页banner
   * @access public
   * @return array $result 执行结果
   */ 
   public function purchaseBannerUpdate(){
   	   if(IS_POST){
   	   	    $data   = I();		
	   	    /*上传图片*/
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 3145728 ;// 设置附件上传大小
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
			// 上传文件
			$info = $upload->upload();
			if($info) {
				$data['thumb'] = $upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];//获取图片路径								
			}	
            $result = D('Purchase')->purchaseBannerUpdate($data);
            $this->ajaxReturn($result);
   	   }else{
   	   	   $id      = I('id');
   	   	   $banner  = M('purchase_banner')->where(array('id'=>$id))->find();
   	   	   $this->assign('banner' , $banner);
   	   	   $this->display();
   	   }      
   }

    /*ajax更改商品品牌状态*/
    public function changeStatus(){
      if(IS_POST){
            $id     = I('id');
            $status = I('status') == 1 ? 1 : 0;
            $r = M('purchase_company')
               ->where(array('id'=>$id))
               ->save(array('is_tj'=>$status));
            if($r === false){
                $result = array(
                    'status' => 0,
                    'msg'    => 'error'
                );
            }else{
                $result = array(
                    'status' => 1,
                    'msg'    => '确认修改？'
                );
            }
            $this->ajaxReturn($result);
        }
    }


  /**
   * 删除融资页banner删除
   * @access public
   */ 
   public function purchaseBannerDelete(){
   	   if(IS_POST){
   	   	  $id     = I('id');
   	   	  $result = D('Purchase')->purchaseBannerDelete($id);
          $this->ajaxReturn($result);
   	   }
   }            
}