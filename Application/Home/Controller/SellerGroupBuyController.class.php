<?php
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class SellerGroupBuyController extends Controller {
	public function _initialize(){
        if(empty($_SESSION['member_data'])){
            $this->ajaxReturn(array(
                'status' => 0,
                'msg'   => '请登录'
            ));die;
        }
    }

    /*
     * 团购申请
     * */
    public function groupBuyAdd(){
        if(IS_AJAX){
            $data      = I();
            $seller_id = $_SESSION['member_data']['id'];
            /*检测商品是否存在*/
            $r = M('Mall_goods')->where(array('id'=>$data['goods_id'],'member_id'=>$seller_id))->getField('id');
            if(!$r){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'msg'    => '商品不存在'
                ));die;
            }
            /*检测基础数据合法性*/
            $r = D('GroupBuy')->checkData($data);
            if(!$r['status']){
                $this->ajaxReturn($r);die;
            }            
            /*检测上首页 时间是否和其他首页商品重叠*/
            $start_time = strtotime($data['start_time']);
            $end_time   = $start_time + intval($data['time']) * 24 * 3600;
            if($data['ad_1'] == 1 && D('GroupBuy')->checkIndex($data['goods_id'] , $start_time , $end_time)){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'msg'    => '首页 该时间段已经有其他商品了'
                ));die;
            }
            /*检测上推荐位 时间是否和其他推荐位商品重叠*/
            if($data['ad_1'] ==2 && D('GroupBuy')->checkTuijian($data['goods_id'] , $start_time , $end_time)){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'msg'    => '推荐位 该时间段已经有其他商品了'
                ));die;
            }      
            
            $group_id = M('Group_goods')->where(array('seller_id'=>$seller_id,'goods_id'=>$data['goods_id']))->getField('id');
            //上传图片
            $upload           = new \Think\Upload();// 实例化上传类
            $upload->maxSize  = 3145728 ;// 设置附件上传大小
            $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
            // 上传文件
            $info = $upload->upload();
            if($info) {
                if($info['img']){//获取缩略图片路径
                    $data['thumb'] = $upload->rootPath.$info['img']['savepath'].'thumb_'.$info['img']['savename'];
                    $data['img']   = $upload->rootPath.$info['img']['savepath'].$info['img']['savename'];
                    //生成缩略图
                    $image = new \Think\Image();
                    $image->open($data['img']);
                    $image->thumb(600, 600)->save($data['thumb']);
                }
                if($info['img_1']){//首页图片                  
                    $data['img_1']   = $upload->rootPath.$info['img_1']['savepath'].$info['img_1']['savename'];
                }
                if($info['img_2']){//推荐位图片
                    $data['img_2']   = $upload->rootPath.$info['img_2']['savepath'].$info['img_2']['savename'];
                }
            }else{
                if(($upload->getError() != '没有文件被上传！' && $group_id) || !$group_id){
                    $this->ajaxReturn(array(
                        'status' => 0,
                        'msg'    => $upload->getError(),
                        $group_id
                    ));die;
                }
            }     
            if(!$group_id){
                $result = D('GroupBuy')->groupBuyAdd($seller_id , $data);
            }else{
                $result = D('GroupBuy')->groupBuyUpdate($group_id , $data);
            }
            if(!$result['status']){
               unlink($data['img_1']);
               unlink($data['img_2']);
               unlink($data['thumb']);
               unlink($data['img']);
            }
            $this->ajaxReturn($result);
        }
    }
    
    /*
     * 获取商品团购数据
     * */
    public function getGroupGoods(){
        if(IS_AJAX){
            $group_id  = intval(I('group_id'));
            if(!$group_id){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'msg'    => '团购id错误'
                ));
            }
            $seller_id = $_SESSION['member_data']['id'];
            $data = M('Group_goods')
                  ->where(array('id'=>$group_id,'member_id'=>$seller_id))
                  ->find(); 
            if($data['id']){
                $data['now_time'] = time();
            }else{
                $data = '暂无数据';
            }           
            $this->ajaxReturn(array(
                'status' => 1,
                'msg'    => 'ok',
                'data'   => $data
            ));
        }
    }
    
    /*
     * 获取已有的团购时间段
     * */
    public function getGroupTime(){
        if(IS_AJAX){
            $index = M('Group_goods')
                   ->field('start_time , end_time')
                   ->where(array('ad_1'=>'1','is_check'=>1,'check_status'=>1,'end_time'=>array('egt',time())))
                   ->select();
            $tuijian = M('Group_goods')
                     ->field('start_time , end_time')
                     ->where(array('ad_1'=>'2','is_check'=>1,'check_status'=>1,'end_time'=>array('egt',time())))
                     ->select();
            $this->ajaxReturn(array(
                'status' => 1,
                'msg'    => 'ok',
                'data'   => array(
                    'index'   => $index,
                    'tuijian' => $tuijian
                )
            ));            
        }             
    }
}