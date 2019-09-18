<?php
/**
 * 营销活动业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Home\Model;
use Think\Model;
class ReleaseActivityModel extends Model{
    protected $tableName = 'Release_activity';

  /**
   * 获取营销活动列表 采用分页
   * @access public
   * @param  array $limit 分页数据 
   * @return array 结果
   */     
    public function getActivityList($seller_id , $limit = array(0 , 10)){
        $condition = array(
            'seller_id' => $seller_id
        );
        $data  = M('Release_activity')->where($condition)->limit($limit[0],$limit[1])->select();
        $count = M('Release_activity')->where($condition)->count();
        return array(
            'status'    => 1,
            'msg'       => 'ok',
            'totalRows' => $count,
            'data'      => $data
        );        
    }

    /**
     * 添加活动
     * @access public
     * @param  array $activity_data 优惠数据
     * @return array $result 执行结果
     */
    public function activityAdd($activity_data){
        /*优惠券数据*/
        $data = array(
            'seller_id'     => '',//卖家id
            'title'         => '',//活动名称
            'start_time'    => '',//开始时间
            'end_time'      => '',//结束时间
            'scope'         => '',//指定范围 1全店 2指定商品
            'goods_id'      => '',//指定商品id
            'min_max'       => '',//购买至多少才优惠
            'favourable'    => '' //优惠方式(1打折,2减多少)-优惠值
        );
        $data = array_intersect_key($activity_data , $data); //获取键的交集 
        $data['favourable'] = $activity_data['favourable_type'].'-'.$data['favourable'];
        $r    = M('Release_activity')->add($data);
        if($r === false){
            return array(
                'status' => 0,
                'msg'    => '添加失败'
            );
        }
        return array(
            'status' => 1,
            'msg'    => '添加成功'
        );             
    }

    /**
     * 检测活动数据
     * @param  array $data_   优惠券数据
     * @param  int   $type    1增加 2修改
     * @return array 返回操作结果和过滤后的数据
     */   
    public function checkActivity($data = array() , $type = 1){
   	    if(empty($data)){
            return array('status' => 0 , 'msg' => '数据不能为空');  
   	    } 
        /*验证数据*/
        $validate_model = $type == 1 ? self::MUST_VALIDATE : self::EXISTS_VALIDATE;
	    $model = D("Release_activity");
	    $rules = array(	          
	        array('title','require','必须输入活动名称',self::EXISTS_VALIDATE),
	        array('start_time','isDate','请输入正确的生效时间！',self::EXISTS_VALIDATE,'function',true),
	        array('end_time','isDate','请输入正确的过期时间！',self::EXISTS_VALIDATE,'function',true),
	        array('scope','/^1|2$/','指定范围错误',$validate_model),
	        array('min_max','/^[0-9]+(.[0-9]{1,2})?$/','请输入正确的购买至多少',$validate_model),
            array('favourable_type','/^1|2$/','优惠方式错误',$validate_model),
	    );
        $type == 1 && (array_unshift($rules , 'seller_id','/^[1-9]\d*$/','卖家id错误',$validate_model));
        $type == 2 && (array_unshift($rules , 'id','/^[1-9]\d*$/','活动id错误',self::MUST_VALIDATE));
        if($data['scope'] == 2){
            array_unshift($rules , array('goods_id','require','商品id错误',$validate_model));
        }
        /*验证 活动生效时间 生效时间 必须小于过期时间*/
        if(strtotime($data['start_time']) >= strtotime($data['end_time'])){
            return array('status' => 0 , 'msg' => '生效时间必须小于过期时间');
        }
        /*验证优惠值*/
        if($data['favourable_type'] == 1 && !preg_match('/^[0-9]+(.[0-9]{1,2})?/', $data['favourable'])){
            return array(
                'status' => 0,
                'msg'    => '打折值错误'
            );                
        }else
        if($data['favourable_type'] == 2){
            if(!preg_match('/^[0-9]+(.[0-9]{1,2})?/', $data['favourable'])){
                return array(
                    'status' => 0,
                    'msg'    => '打折值错误'
                ); 
            }
            if($data['favourable'] >= $data['min_max']){
                return array(
                    'status' => 0,
                    'msg'    => '打折值不能比购买价格小'
                ); 
            }
        }    
	    if($model->validate($rules)->create($data) === false){
	        return array(
	            'status' => 0,
	            'msg'    => $model->getError()
	        );
	    }
        return array('status' => 1);    
    }
    public function activityDelete($id,$member_id){
        $r = M('Release_activity')
                    ->where(array('id'=>$id,'seller_id'=>$member_id))
                    ->delete();
        if($r===false){
            return array(
                    'status'=>0,
                    'msg'   =>'删除失败'

                );
        }else{
            return array(
                    'status' => 1,
                    'msg'    =>'删除成功'

                );
        }
    }

    public function pause($id,$status,$member_id){
            if(empty($member_id)){
                return array(
                        'status'=>0,
                        'msg'   =>'用户不存在'
                    );
            }
                $sta = M('Release_activity')
                        ->where(array('seller_id'=>$member_id,'id'=>$id))
                        ->field('status')
                        ->find();
                if($sta['status']==1){
                    $status=0;

                }else{
                    $status=1;
                }

            $r =  M('Release_activity')
                        ->where(array('seller_id'=>$member_id,'id'=>$id))
                        ->save(array('status'=>$status));
            if($r===false){
                return array(
                        'status'=>0,
                        'msg'   =>'暂停失败'
                    );
            }else{
                
                if($status==1){
                    $msg='开启';
                }else{
                    $msg='暂停';
                }
                return array(
                        'status'=>1,
                        'data'  =>$data,
                        'msg'   =>$msg
                    );
            }

    }
    //营销活动修改
    public function activityUpdate($data_,$member_id){
            if(empty($member_id)){
                    return array(
                            'status'=>0,
                            'msg'   =>'用户不存在'
                        );

            }
        /*优惠券数据*/
        $data = array(
            'title'         => '',//活动名称
            'start_time'    => '',//开始时间
            'end_time'      => '',//结束时间
            'scope'         => '',//指定范围 1全店 2指定商品
            'goods_id'      => '',//指定商品id
            'min_max'       => '',//购买至多少才优惠
            'favourable'    => '' //优惠方式(1打折,2减多少)-优惠值
        );

        $data = array_intersect_key($data_, $data);//获取键值交集
        $data['favourable'] = $activity_data['favourable_type'].'-'.$data['favourable'];
        $r    = M('Release_activity')
              ->where(array('seller_id'=>$member_id,'id'=>$id))
              ->save($data);
        if($r===false){
            return array(
                    'status'=>0,
                    'msg'   =>'修改失败'

                );
        }else{
            return array(
                    'status'=>1,
                    'mag'   =>'修改成功'
                );
        }

    }

}