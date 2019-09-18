<?php
/**
 * 团购设置业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Home\Model;
use Think\Model;
class GroupBuyModel extends Model{
    protected $tableName='Group_goods';
    /**
     * 团购申请
     * @access public
     * @param  int   $seller_id  商家id
     * @param  array $data_  申请数据
     * @return array $result 执行结果
     */
    public function groupBuyAdd($seller_id , $data_){
        $data = array(
            'seller_id'   => intval($seller_id),
            'title'       => $data_['title'],
            'goods_id'    => $data_['goods_id'],
            'start_time'  => $data_['start_time'],
            'time'        => intval($data_['time']),
            'group_price' => $data_['group_price'],//团购价格
            'create_time' => time(),
            'ad_1'        => intval($data_['ad_1'])
        );
        if(!$data['seller_id']){
            return array('status' => 0,'msg' => '卖家id错误');
        }
        if($data['ad_1'] == 1){//如果是首页 需要上传首页的图片和推荐位的图片
            $data['img_1'] = $data_['img_1'];
            $data['img_2'] = $data_['img_2'];
            if(!$data['img_1']){
                return array(
                    'status' => 0,
                    'msg'    => '请上传首页图片'
                );
            }
            if(!$data['img_2']){
                return array(
                    'status' => 0,
                    'msg'    => '请上传推荐位图片',
                    $data['img_2']
                );
            }
        }else
        if($data['ad_1'] == 2){//如果是推荐位  需要上传推荐位的图片
            $data['img_2'] = $data_['img_2'];
            if(!$data['img_2']){
                return array(
                    'status' => 0,
                    'msg'    => '请上传推荐位图片'
                );
            }
        }else{
            $data['img']   = $data_['img'];
            $data['thumb'] = $data_['thumb'];
            if(!$data['img']){
                return array(
                    'status' => 0,
                    'msg'    => '请上传图片'
                );
            };
        }
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time']   = $data['start_time'] + $data['time'] * 24 * 3600;
        $id = M('Group_goods')->add($data);
        if($id === false){
            return array(
                'status' => 0,
                'msg'    => '申请失败'
            );            
        }
        M('Mall_goods')->where(array('id'=>$data['goods_id']))->save(array('group_id'=>$id));
        return array(
            'status' => 1,
            'msg'    => '申请成功'
        );
    }
    
    /**
     * 团购申请修改
     * @access public
     * @param  int   $group_id  团购id
     * @param  array $data_  申请数据
     * @return array $result 执行结果
     */
    public function groupBuyUpdate($group_id , $data_){
        $data = array(
            'id'           => intval($group_id),
            'title'        => $data_['title'],
            'goods_id'     => $data_['goods_id'],
            'start_time'   => $data_['start_time'],
            'time'         => intval($data_['time']),
            'group_price'  => $data_['group_price'],//团购价格
            'create_time'  => time(),
            'is_check'     => 0,//标记为未审核
            'check_status' => 0,
            'ad_1'         => intval($data_['ad_1'])
        );
        if(!$data['id']){
            return array('status' => 0,'msg' => '团购id错误');
        }             
        if($data['ad_1'] == 1){//如果是首页 需要上传首页的图片和推荐位的图片
            if($data_['img_1']){
                $data['img_1'] = $data_['img_1'];
            }
            if($data_['img_2']){
                $data['img_2'] = $data_['img_2'];
            }
        }else
        if($data['ad_1'] == 2){//如果是推荐位  需要上传推荐位的图片
            if($data_['img_2']){
                $data['img_2'] = $data_['img_2'];
            }
        }else
        if($data['ad_1'] == 0){
            if($data_['img']){
                $data['img']   = $data_['img'];
                $data['thumb'] = $data_['thumb'];                
            }
        }
        $data['start_time'] = strtotime($data['start_time']);  
        $data['end_time']   = $data['start_time'] + $data['time'] * 24 * 3600;
        $id = M('Group_goods')->save($data);
        if($id === false){
            return array(
                'status' => 0,
                'msg'    => '申请失败',
            );
        }
        return array(
            'status' => 1,
            'msg'    => '申请成功',
        );
    } 
    
    /*
     * 检测基础数据合法性
     * */    
    public function checkData($data_){
        $data = array(
            'title'       => $data_['title'],
            'goods_id'    => $data_['goods_id'],
            'start_time'  => $data_['start_time'],
            'time'        => $data_['time'],
            'group_price' => $data_['group_price'],//团购价格
            'ad_1'        => $data_['ad_1']
        );
        if(strtotime($data['start_time']) < time()){
            return array(
                'status' => 0,
                'msg'    => '开始时间不能小于当前时间'
            );
        }
        /*验证数据*/
        $model  = D('Group_goods');
        $rules  = array(
            array('title','require','请输入团购标题',self::MUST_VALIDATE),
            array('goods_id','/^[1-9]\d*$/','商品id错误',self::MUST_VALIDATE),
            array('start_time','isDate','请输入正确的开始时间！',1,'function',true),
            array('group_price','require','请输入团购价格',self::MUST_VALIDATE),
            array('group_price','/^[0-9]+(.[0-9]{1,2})?$/','请输入正确的团购价格',self::MUST_VALIDATE),
            array('time',array(7,15,30),'持续时间设置错误！',self::MUST_VALIDATE,'in'),
            array('ad_1',array(0,1,2),'投放位置设置错误！',self::MUST_VALIDATE,'in')
        );
        if($model->validate($rules)->create($data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $model->getError(),
                $data_['start_time']
            );
            return $result;
        }
        return array(
            'status' => 1
        );
    }
    
    /*
     * 检测上首页是否和其他商家时间重叠
     * */
    public function checkIndex($id , $start_time , $end_time){
        $sql = " SELECT id,goods_id FROM `tp_group_goods`  WHERE
        (`ad_1` = 1 AND  `is_check` = 1 AND `check_status` = 1)
        AND
        (
        (`start_time` <= $start_time AND `end_time` >= $start_time)
        OR
        (`start_time` <= $end_time AND `end_time` >= $end_time)
        ) LIMIT 0,1";
        $r = M('Group_goods')->query($sql);
        if($r['goods_id'] == $id){
            return true;
        }
        return $r['goods_id'] ? true :false;
    }    
    
    /*
     * 检测上推荐位是否和其他商家时间重叠
     * */
    public function checkTuijian($id , $start_time , $end_time){
        $sql = " SELECT id,goods_id FROM `tp_group_goods`  WHERE
        (`ad_1` = 2 AND  `is_check` = 1 AND `check_status` = 1)
        AND
        (
        (`start_time` <= $start_time AND `end_time` >= $start_time)
        OR
        (`start_time` <= $end_time AND `end_time` >= $end_time)
        )";
        $r = M('Group_goods')->query($sql);
        foreach($r as $v){
            if($v['goods_id'] == $id){
                return true;
            }
        }
        return count($r) > 2 ? true :false;
    }
}