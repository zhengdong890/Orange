<?php
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class GroupBuyController extends Controller {
/******************************************需要审核的团购************************************************/
  
   /*
    * 获取需要审核的商品申请
    * */
   public function checkList(){
       if(IS_AJAX){
           $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
           $listRows = intval(I('listRows'))?intval(I('listRows')):10;
           $where    = array(
               'check_status' => 0,
               'is_check'     => 0
           );
           $list     = M('Group_goods')
                     ->limit($firstRow,$listRows)
                     ->where($where)
                     ->select();
           if(count($list) == 0){
               $this->ajaxReturn(array('data'=>array(),'total'=>0));          
           }
           foreach($list as $k => $v){
               $goods_ids[] = $v['goods_id'];
           }
           $goods_ids   = implode(',' , $goods_ids);
           $goods_data_ = M('Mall_goods')
                        ->where(array('id'=>array('in' , $goods_ids)))
                        ->field('id,goods_name,goods_price')
                        ->select();
           $goods_data = array();
           foreach ($goods_data_ as $k => $v) {
               $goods_data[$v['id']] = $v;
           }
           foreach($list as $k => $v){
               $list[$k]['goods_name']  = $goods_data[$v['goods_id']]['goods_name'];
               $list[$k]['goods_price'] = $goods_data[$v['goods_id']]['goods_price'];
           }
           $this->ajaxReturn(array('data'=>$list,'total'=>M('Group_goods')->where(array('id'=>array('in' , $goods_ids)))->count()));  
       }else{
           $this->display();
       }
   }
   
   /*
    * 商品团购审核
    * */   
   public function groupBuyCheck(){
       if(IS_AJAX){
           $data   = I();
           $result = D('GroupBuy')->groupBuyCheck($data);
           $this->ajaxReturn($result);
       }
   }

/******************************************审核通过的团购商品************************************************/   

   /*
    * 
    * */
   public function groupByList(){
       if(IS_AJAX){
           $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
           $listRows = intval(I('listRows'))?intval(I('listRows')):10;
           $where    = array(
               'check_status' => 1,
               'is_check'     => 1
           );
           $list     = M('Group_goods')
                     ->limit($firstRow,$listRows)
                     ->where($where)
                     ->select();
           foreach($list as $k => $v){
               $goods_ids[] = $v['goods_id'];
           }
           $goods_ids   = implode(',' , $goods_ids);
           $goods_data_ = M('Mall_goods')
                        ->where(array('id'=>array('in' , $goods_ids)))
                        ->field('id,goods_name,goods_price')
                        ->select();
           $goods_data = array();
           foreach ($goods_data_ as $k => $v) {
               $goods_data[$v['id']] = $v;
           }
           $now_time = time();
           foreach($list as $k => $v){
               $list[$k]['goods_name']  = $goods_data[$v['goods_id']]['goods_name'];
               $list[$k]['goods_price'] = $goods_data[$v['goods_id']]['goods_price'];
               $list[$k]['is_guoqi']    = $v['end_time'] < $now_time ? 1 : 0;
           }
           $this->ajaxReturn(array('data'=>$list,'total'=>M('Group_goods')->where($where)->count()));
       }else{
           $this->display();
       }
   }
   
/******************************************审核不通过的团购商品************************************************/
   
   /*
    * 获取需要审核的商品申请
    * */
   public function noPassgroupByList(){
       if(IS_AJAX){
           $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
           $listRows = intval(I('listRows'))?intval(I('listRows')):10;
           $where    = array(
               'check_status' => 0,
               'is_check'     => 1
           );
           $list     = M('Group_goods')
                     ->limit($firstRow,$listRows)
                     ->where($where)
                     ->select();
           foreach($list as $k => $v){
               $goods_ids[] = $v['goods_id'];
           }
           $goods_ids   = implode(',' , $goods_ids);
           $goods_data_ = M('Mall_goods')
                        ->where(array('id'=>array('in' , $goods_ids)))
                        ->field('id,goods_name,goods_price')
                        ->select();
           $goods_data = array();
           foreach ($goods_data_ as $k => $v) {
               $goods_data[$v['id']] = $v;
           }
           foreach($list as $k => $v){
               $list[$k]['goods_name']  = $goods_data[$v['goods_id']]['goods_name'];
               $list[$k]['goods_price'] = $goods_data[$v['goods_id']]['goods_price'];
           }
           $this->ajaxReturn(array('data'=>$list,'total'=>M('Group_goods')->where($where)->count()));
       }else{
           $this->display();
       }
   }   
   
   /*
    * 团购商品编辑
    * */
   public function groupBuyUpdate(){
       if(IS_AJAX){
           $data = I();
           //上传图片
           $upload = new \Think\Upload();// 实例化上传类
           $upload->maxSize = 3145728 ;// 设置附件上传大小
           $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
           $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
           // 上传文件
           $info = $upload->upload();
           if($info) {
               if($info['img']){
                    $data['thumb'] = $upload->rootPath.$info['img']['savepath'].'thumb_'.$info['img']['savename'];
                    $data['img']   = $upload->rootPath.$info['img']['savepath'].$info['img']['savename'];
                    //生成缩略图
                    $image = new \Think\Image();
                    $image->open($data['img']);
                    $image->thumb(600, 600)->save($data['thumb']);
               }
               if($info['img_1']){
                   $data['img_1'] = $upload->rootPath.$info['img_1']['savepath'].$info['img_1']['savename'];
               }
               if($info['img_2']){
                   $data['img_2'] = $upload->rootPath.$info['img_2']['savepath'].$info['img_2']['savename'];
               }
           }else{
               $this->ajaxReturn(array(
                   'status' => 0,
                   'msg'    => $upload->getError()
               ));die;
           }
           $result = D('GroupBuy')->groupBuyUpdate($data); 
           $result[] = $_FILES;
           $this->ajaxReturn($result);
       }
   }  
}