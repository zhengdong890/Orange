<?php
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class Purchase_offerController extends CommonController{
		/**
    * 立即报价列表
    * @access public
    */
   public function offerList(){
        $id = I('id');
        $img = M('Purchase_offer_img')
              ->where(array('offer_id'=>$id))
              ->find();
              $img1 = $img['img1'];
              $img2 = $img['img2'];
              $img3 = $img['img3'];
        if($img){
          unlink($img1);
          unlink($img2);
          unlink($img3);
          M('Purchase_offer_img')->where(array('purchase_id'=>$id))->delete();
              }
          M('Purchase_offer')->delete($id);
       if(IS_AJAX){

           $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
           $listRows = intval(I('listRows'))?intval(I('listRows')):10;
           $list     = M('Purchase_offer as p')
           			->join('tp_purchase as o ON o.id=p.pur_id')
                 	->order('p.time desc')
                 	->field('p.id,o.title,p.linkman,o.cat_name,p.offer_price,p.offer,p.price_type1,p.price_type2,p.mobile_phone,p.status,p.time')
                	->limit($firstRow,$listRows)
                 	->select();
        	foreach($list as $k=>$v){
        			$create_time = date('Y-m-d H:i:s',$v['time']);
        			$list[$k]['create_time']=$create_time;
        	}
        	//dump($list);
           $this->ajaxReturn(array('data'=>$list,'total'=>M('Purchase_offer')->count()));
       }else{

           $this->display();
       }
   }

   //立即报价详情审核
   public function details(){
    $data= I();
    $id = $data['id'];
    $pur=M('Purchase_offer as p')
    	->join('tp_purchase as o ON o.id=p.pur_id')
    	->join('tp_purchase_offer_img as i ON i.offer_id=p.id')
        ->where(array('p.id'=>$id))
        ->field('p.id,o.title,p.linkman,o.cat_name,o.num,o.unit,o.deadline,p.offer_price,p.offer,p.price_type1,p.price_type2,p.mobile_phone,p.status,p.time,p.data_des,i.img1,i.img2,i.img3')
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
      $r= M('Purchase_offer')
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
      $r= M('Purchase_offer')
            ->where(array('id'=>$data['id']))
            ->save(array('status'=>$data['status']));
      if($r){
        $this->ajaxReturn(array('status'=>1,'msg'=>'取消审核成功'));

      }else{
        $this->ajaxReturn(array('status'=>0,'msg'=>'取消审核失败'));
      }
        }
         
   }
}  