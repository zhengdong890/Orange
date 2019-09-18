<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Hook;
/**
 * 企业认证
 * @author 幸福无期
 * @email  597089187@qq.com
 */
header("content-type:text/html;charset=utf-8");
class BusinessesApplicationController extends Controller {
   /**
    * 企业认证申请列表
    * @access public  
    */ 
    public function qualificationList(){ 
	    $this->display();
    }
   
   /**
    * 获取商家企业认证申请列表数据
    * @access public  
    */ 
    public function getQualifications(){ 
       if(IS_AJAX){
           $where = array();
           $name  = I('name');
           if($name){
               $where['name'] = array('like',"%$name%");
           }
           $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
           $listRows = intval(I('listRows'))?intval(I('listRows')):10;
           $list = M('Businesses_application')
                 ->where($where)
                 ->limit($firstRow,$listRows)
                 ->order('time desc')
                 ->select();
           $this->ajaxReturn(array('data'=>$list,'total'=>M('Businesses_application')->where($where)->count()));
       }
    }
   
   /**
    * 企业认证审核
    * @access public  
    */ 
    public function qualification(){ 
   	    if(IS_POST){
           $data = I();
           $r    = D('BusinessesApplication')->checkQualificationData($data);
           if($r['status'] == 0){
               $this->ajaxReturn($r);
           }
           $result = D('BusinessesApplication')->qualification($data);
           $this->ajaxReturn($result);
   	    }
    }
}