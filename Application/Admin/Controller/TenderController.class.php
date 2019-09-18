<?php
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class TenderController extends CommonController{   
   /**
    * 融资招标信息列表
    * @access public
    */
   public function tenderList(){
       if(IS_AJAX){
           $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
           $listRows = intval(I('listRows'))?intval(I('listRows')):10;
           $list     = M('Tender_lease')->limit($firstRow,$listRows)->select();
           $this->ajaxReturn(array('data'=>$list,'total'=>M('Tender_lease')->count()));
       }else{
           $this->display();
       }
   }
   
  /**
   * 融资招标信息添加
   * @access public
   */ 
   public function tenderAdd(){
   	   if(IS_POST){
   	   	   $data   = I();
           $result = D('Tender')->tenderAdd($data);
           $this->ajaxReturn($result);
   	   } 	   
   }

  /**
   * 融资招标信息修改
   * @access public
   */ 
   public function tenderUpdate(){
   	   if(IS_POST){
   	   	   $data   = I();
           $result = D('Tender')->tenderUpdate($data);
           $this->ajaxReturn($result);
   	   }
   }

  /**
   * 融资招标信息删除
   * @access public
   */ 
   public function tenderDelete(){
   	   if(IS_POST){
   	   	  $id     = I('id');
   	   	  $result = D('Tender')->tenderDelete($id);
          $this->ajaxReturn($result);
   	   }
   }   
/**************************************集成项目审核*******************************************/
   /**
    * 集成项目审核列表
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
           $list     = M('Tender_lease')->limit($firstRow,$listRows)->field($field)->where($where)->select();
           $this->ajaxReturn(array('data'=>$list,'total'=>M('Tender _lease')->count()));
       }else{
           $this->display();
       }
   }
    
   /**
    * 集成项目审核
    * @access public
    */
   public function TenderCheck(){
       if(IS_POST){
           $data   = I();
           $result = D('Tender')->tenderCheck($data);
           $this->ajaxReturn($result);
       }
   }
    
   /**
    * 融资招标信息删除
    * @access public
    */
   public function integratedDelete(){
       if(IS_POST){
           $id     = I('id');
           $result = D('Integrated')->integratedDelete($id);
           $this->ajaxReturn($result);
       }
   }
   
  /**
   * 融资招标公司列表
   * @access public
   */ 
   public function companyList(){
   	   $companys  = D('Tender')->getCompany();
   	   $this->assign('companys' , $companys);
       $this->display();
   }

  /**
   * 新增融资招标公司
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
			if($info['img']) {
				$data['img'] = $upload->rootPath.$info['img']['savepath'].$info['img']['savename'];//获取图片路径								
			}	
      if($info['tj_thumb']){
        $data['tj_thumb']=$upload->rootPath.$info['tj_thumb']['savepath'].$info['tj_thumb']['savename'];
      }
           $result = D('Tender')->companyAdd($data);
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
   * 修改融资招标公司信息
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
			if($info['img']) {
				$data['img'] = $upload->rootPath.$info['img']['savepath'].$info['img']['savename'];//获取图片路径
       } 
       if($info['tj_thumb']){
        $data['tj_thumb'] = $upload->rootPath.$info['tj_thumb']['savepath'].$info['tj_thumb']['savename'];//获取图片路径 
       }  	
           $result = D('Tender')->companyUpdate($data);
           $this->ajaxReturn($result);
   	   }else{
   	   	   $id            = I('id');
   	   	   $company       = D('Tender')->getOneCompany($id);
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
   * 删除融资招标公司
   * @access public
   */ 
   public function companyDelete(){
   	   if(IS_POST){
   	   	  $id     = I('id');
   	   	  $result = D('Tender')->companyDelete($id);
          $this->ajaxReturn($result);
   	   }
   }

   /**
   * 融资招标页banner
   * @access public
   */ 
   public function tenderBannerList(){
   	   	$list = D('Tender')->getBanner();
        $this->assign('list' , $list);
   	   	$this->display();
   } 

  /**
   * 添加融资招标页banner
   * @access public
   * @return array $result 执行结果
   */ 
   public function tenderBannerAdd(){
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
            $result = D('Tender')->tenderBannerAdd($data);
            $this->ajaxReturn($result);
   	   }else{
   	   	   $this->display();
   	   }      
   }

  /**
   * 修改融资招标页banner
   * @access public
   * @return array $result 执行结果
   */ 
   public function tenderBannerUpdate(){
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
            $result = D('Tender')->tenderBannerUpdate($data);
            $this->ajaxReturn($result);
   	   }else{
   	   	   $id      = I('id');
   	   	   $banner  = M('tender_banner')->where(array('id'=>$id))->find();
   	   	   $this->assign('banner' , $banner);
   	   	   $this->display();
   	   }      
   }
    /*ajax更改商品品牌状态*/
    public function changeStatus(){
      if(IS_POST){
            $id     = I('id');
            $status = I('status') == 1 ? 1 : 0;
            $r = M('Tender_company')
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
   public function tenderBannerDelete(){
   	   if(IS_POST){
   	   	  $id     = I('id');
   	   	  $result = D('Tender')->tenderBannerDelete($id);
          $this->ajaxReturn($result);
   	   }
   }            
}