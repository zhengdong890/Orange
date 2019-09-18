<?php
/*
 * 卖家QQ客服模块
 * */
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class SellerQQController extends Controller {
    public function qqList(){    
            
    }
    
    public function qqEdit(){
        if(IS_AJAX){
            $data              = I();      
            $data['seller_id'] = $_SESSION['member_data']['id'];           
            $r = M('Seller_qq')->where(array('seller_id'=>$data['seller_id']))->getField('id');
            if($r){
                $result = D('SellerQQ')->qqEdit($data);
            }else{
                $result = D('SellerQQ')->qqAdd($data);
            }            
            $this->ajaxReturn($result);
        }else{
            $data = M('Seller_qq')->where(array('seller_id'))->find();
            $this->assign('data' , $data);
            $this->display();
        }
    }
}
