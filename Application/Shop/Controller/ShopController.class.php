<?php
namespace Shop\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class ShopController extends Controller {
    public function _initialize(){
        /*二级域名处理  获取对应的商家id*/
        $domain    = SUB_DOMAIN; //获取当前地址的二级域名
        $seller_id = M('Shop_data')->where(array('domain'=>$domain))->getField('member_id');
        if(!$seller_id){
            $seller_id = M('Member')->where(array('id'=>$domain))->getField('id');
            if(!$seller_id){
                echo '页面无法找到';die;
            }
        }  
        $this->seller_id = $seller_id;       
    }

    
    /*店铺收藏*/
    public function shopcllect(){    	
        if(IS_AJAX){
            $data['member_id'] = $_SESSION['member_data']['id'];

            if(!$data['member_id']){
                $this->ajaxReturn(array('msg'=>'nologin'));
            }
            $seller_id = I('title');
            $data['seller_id'] = $seller_id;
            $data['status'] = 1;
            $data['time'] = time();
            $shop=M('shop_data')->where(array('member_id'=>$data['seller_id']))->find();
            $data['thumb']=$shop['thumb'];
            $data['shop_name']=$shop['shop_name'];
            $data['domain']=$shop['domain'];
            $data['desc']=$shop['desc'];
            $is_set = M('shop_collect')->where(array('seller_id'=>$data['seller_id'],'member_id'=>$data['member_id']))->find();
            if( $is_set ){
                $this->ajaxReturn(array('msg'=>'onemore'));
            }else{
                $addData = M('shop_collect')->add($data);
                if( $addData ){
                    $this->ajaxReturn(array('msg'=>'ok'));
                }
                	
            }  
        }
    }
    //收藏商品
     public function goodcllect(){      
        if(IS_AJAX){
            $data['member_id'] = $_SESSION['member_data']['id'];
            
            if(!$data['member_id']){
                $this->ajaxReturn(array('msg'=>'nologin'));//请登入
            }
            $data['seller_id'] = $this->seller_id;
            $data['goods_id'] = I('goods_id');
            $data['status'] = 1;
            $data['time'] = time();
            $ma=M('mall_goods')->where(array('id'=>$data['goods_id']))->find();
            $data['goods_name']=$ma['goods_name'];
            $data['goods_price']=$ma['goods_price'];
            $data['goods_thumb']=$ma['goods_thumb'];
            $data['goods_number']=$ma['goods_number'];
            $is_set = M('goods_collect')->where(array('goods_id'=>$data['goods_id'],'member_id'=>$data['member_id']))->find();
            if( $is_set ){
                $this->ajaxReturn(array('msg'=>'onemore'));
            }else{
                $addData = M('goods_collect')->add($data);
                if( $addData ){
                    $this->ajaxReturn(array('msg'=>'ok'));
                }
                    
            }  
        }
    }

    
    /*获取联系卖家信息*/
    public function getseller(){
        if( $_GET['title'] ){
            $seller_id = $this->seller_id;
            $memInfo = M('member_data')->where(array('member_id'=>$seller_id))->find();
                if( $memInfo ){
                $this->assign('memInfo',$memInfo);
            }
        }
        $this->display();
    }
    
    /*商品搜索*/
    public function search(){
        if( IS_AJAX){
            $seller_id = $this->seller_id;
            $search = trim(I('post.search'));
            $condition["goods_name"] = array('like','%'.$search.'%');
            $condition["member_id"] = $seller_id;
            $id =  M('mall_goods')->where($condition)->getField('id');
            if( $id ){
                $this->ajaxReturn(array('msg'=>'ok','goods_id'=>$id));
            }else{
                $this->ajaxReturn(array('msg'=>'no'));
            }
        }
    }       
}