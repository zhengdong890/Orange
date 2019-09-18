<?php
/*
 * 中标集成项目
 * */
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class TenderSelectController extends Controller {
  /**
   * 中标融资租赁列表页
   * @access public  
   */ 
   public function selectList(){ 
       if(IS_AJAX){
           $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
           $listRows = intval(I('listRows'))?intval(I('listRows')):10;  
           $list     = M('Tender_select')
                     ->limit($firstRow,$listRows)
                     ->select();
           $this->ajaxReturn(array('data'=>$list,'total'=>M('News')->count()));
       }else{
           $this->display();
       }       
   }
   
   /**
    * 中标融资租赁添加页
    * @access public
    */
   public function selectAdd(){
       if(IS_AJAX){
           $data = I();
           $r    = D('TenderSelect')->selectAdd($data);
           $this->ajaxReturn($r);            
       }else{
           $this->display();
       }
   }
   
   /**
    * 中标融资租赁修改页
    * @access public
    */
   public function selectUpdate(){
       if(IS_AJAX){
           $data = I();
           $r    = D('TenderSelect')->selectUpdate($data);
           $this->ajaxReturn($r);
       }else{
           $id   = I('id');
           $data = M('Tender_select')->where(array('id'=>$id))->find();
           $this->assign('data' , json_encode($data));
           $this->display();
       }
   }
   
   /**
    * 删除中标融资租赁
    * @access public
    */
   public function selectDelete(){
       if(IS_AJAX){
           $id = I('id');
           $r  = D('TenderSelect')->selectDelete($id);
           $this->ajaxReturn($r);
       }else{
           $this->display();
       }
   }   
   
}