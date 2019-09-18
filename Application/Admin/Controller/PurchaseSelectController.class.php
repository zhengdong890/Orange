<?php
/*
 * 中标批量采购
 * */
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class PurchaseSelectController extends Controller {
  /**
   * 中标批量采购列表页
   * @access public  
   */ 
   public function selectList(){ 
       if(IS_AJAX){
           $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
           $listRows = intval(I('listRows'))?intval(I('listRows')):10;  
           $list     = M('Purchase_select')
                     ->limit($firstRow,$listRows)
                     ->select();
           $this->ajaxReturn(array('data'=>$list,'total'=>M('News')->count()));
       }else{
           $this->display();
       }       
   }
   
   /**
    * 中标批量采购添加页
    * @access public
    */
   public function selectAdd(){
       if(IS_AJAX){
           $data = I();
           $r    = D('PurchaseSelect')->selectAdd($data);
           $this->ajaxReturn($r);            
       }else{
           $this->display();
       }
   }
   
   /**
    * 中标批量采购修改页
    * @access public
    */
   public function selectUpdate(){
       if(IS_AJAX){
           $data = I();
           $r    = D('PurchaseSelect')->selectUpdate($data);
           $this->ajaxReturn($r);
       }else{
           $id   = I('id');
           $data = M('Purchase_select')->where(array('id'=>$id))->find();
           $this->assign('data' , json_encode($data));
           $this->display();
       }
   }
   
   /**
    * 删除中标批量采购
    * @access public
    */
   public function selectDelete(){
       if(IS_AJAX){
           $id = I('id');
           $r  = D('PurchaseSelect')->selectDelete($id);
           $this->ajaxReturn($r);
       }else{
           $this->display();
       }
   }   
   
}