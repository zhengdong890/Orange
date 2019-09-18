<?php
namespace Home\Model;
use Think\Model;
/**
 * 退款 换货 退货退款
 * type 1退款 2换货 3退货退款
 * @author 幸福无期
 */
class RefundGoodsModel extends Model{ 
    protected $tableName = 'refund_goods'; 

   /**
    * 退款
    * @return array 返回结果
    */
    public function refundCase($data_){
        $data = array(
            'order_data_id' => $data_['order_data_id'],
            'member_id'     => $data_['member_id'],
            'seller_id'     => $data_['seller_id'],
            'goods_id'      => $data_['goods_id'],
            'trade_no'      => $data_['trade_no'],
            'case'          => $data_['case'],//退款价格
            'beacuse'       => intval($data_['beacuse']), //申请原因
            'type'          => 1, //申请类型
            'content'       => $data_['content'],//说明
            'thumb'         => $data_['thumb'],
            'create_time'   => time()
        );   
        $id = M('Refund_goods')->add($data);
        if($id === false){
            return array(
                'status' => 0,
                'msg'    => '申请失败'
            );
        }
        return array(
            'status' => 1,
            'msg'    => '申请成功'
        );                  
    } 

   /**
    * 退款退货
    * @return array 返回结果
    */
    public function refundCaseGoods($data_){
        $data = array(
            'case'        => $data_['case'],//退款价格
            'beacuse'     => intval($data_['beacuse']), //申请原因
            'type'        => 3, //申请类型
            'content'     => $data_['content'],//说明
            'thumb'       => $data_['thumb'],
            'create_time' => time()
        );  
        $id = M('Refund_goods')->add($data);
        if($id === false){
            return array(
                'status' => 0,
                'msg'    => '申请失败'
            );
        }
        return array(
            'status' => 1,
            'msg'    => '申请成功'
        );        
    }

   /**
    * 换货
    * @return array 返回结果
    */
    public function refundGoods_($data_){
        $data = array(
            'beacuse'     => intval($data_['beacuse']), //申请原因
            'type'        => 2, //申请类型
            'content'     => $data_['content'],//说明
            'thumb'       => $data_['thumb'],
            'create_time' => time()
        ); 
        $id = M('Refund_goods')->add($data);
        if($id === false){
            return array(
                'status' => 0,
                'msg'    => '申请失败'
            );
        }
        return array(
            'status' => 1,
            'msg'    => '申请成功'
        );    
    }

   /**
    * 检测 提交的 退换货款 数据 合法性
    * @return array 返回结果
    */
    public function checkRefund($data){
        $data['type'] = intval($data['type']);
        /*验证数据*/
        $model = D("Refund_goods");
        $rules = array(
            array('type',array(0,1),'类型设置错误',self::MUST_VALIDATE,'in'),
            array('beacuse','/^[1-9]\d*$/','请选择申请原因',self::MUST_VALIDATE),
        );
        $temp  = array();
        if($data['type'] == 1 || $data['type'] == 3){
            $temp = array(
                array('case','require','请输入正确的价格',self::MUST_VALIDATE),
                array('case','/^[0-9]+(.[0-9]{1,2})?$/','请输入正确的价格'),
            ); 
        }
        $rules = array_merge($rules , $temp);
        if($model->validate($rules)->create($data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $model->getError()
            );
            return $result;
        }
        return array(
          'status' => 1
        );
    }
}