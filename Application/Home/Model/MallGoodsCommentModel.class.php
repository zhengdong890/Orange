<?php
namespace Home\Model;
use Think\Model;
/**
 * 共享商品评论业务逻辑
 * @author 幸福无期
 */
class MallGoodsCommentModel extends Model{  
    protected $tableName='Mall_goods_comment'; //关闭检测字段

   /**
    * 根据商品id获取商品评论列表
    * @access public
    * @param  int   $goods_id 商品id   
    * @return array 评论列表
    */ 
    public function getCommentListById($goods_id , $limit = array() , $order = ''){ 
       	if(empty($limit)){
            $limit = array(0 , 10);
       	}
       	if(!empty($goods_id)){
            $condition = array('goods_id' => $goods_id,'level'=>array('in','1,2,3'));
       	}else{
	        return  array(
	        	'status'    => 0,
	        	'msg'       => '商品id错误'
	        );
       	}
       	$order  = 'id desc';  
       	$model  = M('Mall_goods_comment')->where($condition)->order($order);
        $model  = $model->limit($limit[0] , $limit[1]);
        $data   = $model->select();  
        if(empty($data)){
	        return  array(
	        	'status'    => 1,
	        	'msg'       => 'ok',
	            'data'      => array(),
	            'totalRows' => 0
	        );
        } 
        $count  = M('Mall_goods_comment')->where($condition)->count(); 
        /*评论用户信息获取*/
        $member_ids  = array_column($data , 'member_id');
        $member_ids  = implode(',' , $member_ids);
        $member_data = M('Member_data')
	        ->where(array('member_id'=>array('in' , $member_ids)))
	        ->field('member_id,headimg,telnum,nickname')
	        ->select();
	    $member_data = array_all_column($member_data , 'member_id');
        foreach($data as &$v){
            $v['member_data'] = $member_data[$v['member_id']];
        }
        return  array(
        	'status'    => 1,
        	'msg'       => 'ok',
            'data'      => $data,
            'totalRows' => $count
        );  
    }

   /**
    * 根据商品id统计商品评论
    * @access public
    * @param  int   $goods_id 商品id   
    * @return array 评论统计
    */ 
    public function getGoodsCommentTotal($goods_id){
        $goods_id = intval($goods_id);
        if($goods_id == 0){
            return array(
                'status' => 0,
                'msg'    => '请输入商品id'
            );
        }
        $total  = array();
        //差评
        $total['level_1'] = M('Mall_goods_comment')
	        ->where(array('goods_id'=>$goods_id,'level'=>array('in','1')))
	        ->count();
	    //中评
        $total['level_2'] = M('Mall_goods_comment')
	        ->where(array('goods_id'=>$goods_id,'level'=>array('in','2')))
	        ->count();
	    //好评
        $total['level_3'] = M('Mall_goods_comment')
	        ->where(array('goods_id'=>$goods_id,'level'=>array('in','3')))
	        ->count();
        return $total;
    }
    
  /**
   * 添加共享商品评论
   * @access public
   * @param  int   $id     订单详情id
   * @param  array $data   评论信息 一维数组   
   * @return array $result 执行结果
   */ 
  public function commentAdd($id , $data_ = array()){
        $result = array(
            'status' => 1,
            'msg'    => '评论成功'
        );     
        if(!intval($id)){
            return array(
                'status' => 0,
                'msg'    => '订单详情id不正确'
            );
        }
        $data = array(
            'member_id'  => intval($data_['member_id']),
            'goods_id'   => intval($data_['goods_id']),
            'time'       => date('Y-m-d H:i:s'),
            'content'    => $data_['content'],
            'level'      => $data_['level'],
            'desc'       => $data_['desc'],
            'logistical' => $data_['logistical'],
            'service'    => $data_['service'],
            'thumb'      => $data_['thumb']
        );
        /*验证数据*/
        $moedel = D("Mall_goods_comment");
        $rules  = array(
            array('member_id','/^[1-9]\d*$/','会员id错误',self::MUST_VALIDATE),
            array('goods_id','/^[1-9]\d*$/','商品id错误',self::MUST_VALIDATE),
            array('content','require','必须输入评论内容',self::MUST_VALIDATE),
            array('level','/^1|2|3$/','评分不正确',self::MUST_VALIDATE),
            array('desc','/^1|2|3|4|5$/','描述相符评论不正确',self::MUST_VALIDATE),
            array('logistical','/^1|2|3|4|5$/','物流服务设置不正确',self::MUST_VALIDATE),
            array('service','/^1|2|3|4|5$/','服务态度置不正确',self::MUST_VALIDATE),
        );
        if($moedel->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $moedel->getError()
           );
           return $result;
        } 
        $r = M('Mall_goods_comment')->add($data);//添加数据
        if($r !== false){                 
            M('Mall_order_data')->where(array('id'=>$id,'member_id'=>$data['member_id']))->save(array('comment_status'=>1,'status'=>2));  
            $order_id = M('Mall_order_data')->where(array('id'=>$id,'member_id'=>$data['member_id']))->Field('order_id,seller_id')->find();
            $count = M('Mall_order_data')->where(array('order_id'=>$order_id['order_id'],'comment_status'=>0))->count();
            if($count == 0){
                M('Mall_order')->where(array('id'=>$order_id['order_id']))->save(array('comment_status'=>1,'status'=>2));  
            }
            /*卖家店铺评分处理*/
            $sql = "UPDATE tp_shop_data SET 
                        logistical=logistical+{$data['logistical']},
                        service=service+{$data['service']},
                        desc_score=desc_score+{$data['desc']},
                        comment_number=comment_number+1
                    WHERE member_id={$order_id['seller_id']}";
            M()->execute($sql);
        }else{
            $result = array(
             'status' => 0,
             'msg'    => '评论失败'
            );
        }
        return $result;
  }
}