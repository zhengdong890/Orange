<?php
namespace Home\Model;
use Think\Model;
/**
 * 共享商品评论业务逻辑
 * @author 幸福无期
 */
class GoodsCommentModel extends Model{  
    protected $tableName='Goods_comment'; //关闭检测字段

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
            'time'       => time(),
            'content'    => $data_['content'],
            'level'      => $data_['level'],
            'desc'       => $data_['desc'],
            'logistical' => $data_['logistical'],
            'service'    => $data_['service']
        );
        /*验证数据*/
        $moedel = D("Goods_comment");
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
        $r = M('Goods_comment')->add($data);//添加数据
        if($r !== false){                 
            M('Order_data')->where(array('id'=>$id,'member_id'=>$data['member_id']))->save(array('is_comment'=>1,'status'=>2));  
            $order_id = M('Order_data')->where(array('id'=>$id,'member_id'=>$data['member_id']))->Field('order_id,seller_id')->find();
            $count = M('Order_data')->where(array('order_id'=>$order_id['order_id'],'is_comment'=>0))->count();
            if($count == 0){
                M('Order')->where(array('id'=>$order_id['order_id']))->save(array('is_comment'=>1,'status'=>2));  
            }
        }else{
            $result = array(
             'status' => 0,
             'msg'    => '评论失败'
            );
        }
        return $result;
    }
}