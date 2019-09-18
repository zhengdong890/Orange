<?php
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class SellerCommentController extends Controller {	
    public function _initialize(){
        if(IS_AJAX){
            if(empty($_SESSION['member_data'])){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请等待'));die;
            }            
        }
 
    }
    
    public function getCommentList(){
        if(IS_AJAX){
            $data      = I();
            $member_id = $_SESSION['member_data']['id'];
            $firstRow  = $data['firstRow'];
            $listRows  = $data['listRows'];
            /*获取评价信息*/
            $data  = M('Mall_goods_comment')
                   ->limit($firstRow,$listRows)
                   ->select();
            $count = M('Mall_goods_comment')
                   ->count();
            $result = array(
                'data'      => $data,
                'totalRows' => $count
            );
            echo json_encode($result);
        }
    }
}