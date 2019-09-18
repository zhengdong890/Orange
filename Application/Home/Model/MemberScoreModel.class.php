<?php
namespace Home\Model;
use Think\Model;
/**
 * 会员积分模块业务逻辑
 * @author 幸福无期
 */
class MemberScoreModel extends Model{ 
	protected $tableName = 'Member_score_history'; //切换表

   /**
    * 积分处理
    * @param  int   $member_id  会员id
    * @param  int   $type 操作类型
    * @param  array $data 附加数据
    * @return array result     返回结果
    */    
    public function score($member_id , $type , $data){
    	$member_id = intval($member_id);
    	if($member_id == 0){
            return array('status' => 0 , 'msg' => '会员id不合法');
    	}
        $score_config = C('SCORE_CONFIG'); 
	   	$score        = $score_config[$type];
	   	if(empty($score)){
            return array('status' => 0 , 'msg' => '该积分不存在');
	   	}
	   	/*登录 或者 分享*/
	   	if($type == 'LOGIN' || $type == 'SHARE'){
	   		$score['time']--;
	   		$time      = strtotime(date('Y-m-d')) - 24 * 3600 * $score['time'];
	   		$condition = array(
	   			'member_id' => $member_id ,
	   			'type'      => $score['code'],
	   			'time'      => array('egt' , $time)
	   	    );
            $number = M('Member_score_history')
                    ->where($condition)
                    ->count();        
            if($number >= $score['number']){
                return array('status' => 0 , 'msg' => '积分已经领取');   
            }
	   	}else
	   	if($type == 'BUY'){
	   	    /*采购*/
            $buy_condition = $score['condition'];
            $buy_condition = krsort($score['condition']);
            foreach($buy_condition as $k => $v){
                if($data['money'] >= $v){
                    $key = $k;
                    break;
                }
            }
            if(!isset($key)){
                return array('status' => 0 , 'msg' => '暂无符合条件的积分领取'); 
            }
            $score['score'] = $buy_condition[$key];
	   	}else{
	   		/*一次性获取的积分*/
	   		$n = M('Member_score_history')
		       ->where(array('member_id' => $member_id , 'type' => $score['code']))
		   	   ->count();
	   		if($n > 0){
	   			return array('status' => 0 , 'msg' => '积分已经领取'); 
	   		}
	   	}        
	   	//积分改变
	   	$r = $this->scoreChange($member_id , $score['score']);
	   	if($r['status'] == 0){
            return $r;
	   	}
	   	//积分历史
	   	$r = $this->scoreHistory($member_id , $score['score'] , $score['code']);
	   	return $r;
    }

   /**
    * 用户积分改变
    * @param  int   $member_id  会员id
    * @param  int   $score  
    * @param  int   $flag 1 表示增加 2表示减少
    * @return array result     返回结果
    */   
    public function scoreChange($member_id , $score , $flag = 1){
        if(!$member_id){
            return array('status'=>0,'msg'=>'会员id不能为空');
        }  
        $score = intval($score);
        if($score == 0){
            return array('status'=>0,'msg'=>'分数错误');
        }
        if($flag == 1){//增加
        	$model = M('Member');
	        $model->score     = array('exp',"score+$score");
	        $model->use_score = array('exp',"use_score+$score");
            $r = $model
               ->where(array('id'=>$member_id))
               ->save();
        }else
        if($flag == 2){//减少
            $r = M('Member')
               ->where(array('id'=>$member_id))
               ->setDec('use_score' , $score);
        }
        if($r === false){
            return array('status'=>0,'msg'=>'操作失败');
        }
        return array('status'=>1,'msg'=>'ok');
    }

   /**
    * 积分历史
    * @param  int   $member_id  会员id
    * @param  int   $score  
    * @param  int   $type 操作类型
    * @param  int   $flag 1 表示增加 2表示减少
    * @return array result     返回结果
    */   
    public function scoreHistory($member_id , $score , $type , $flag = 1){
        $data = array(
            'member_id' => $member_id,
            'score'     => $score,
            'type'      => $type,
            'flag'      => $flag,
            'time'      => time() 
        );
        $result = M('Member_score_history')->add($data);
        return $result;
    }

   /**
    * 积分过期处理 过期时间为1年
    * @param  int   $member_id  会员id
    * @return array result     返回结果
    */  
    public function scoreOver($member_id){
    	$member_id = intval($member_id);
    	if($member_id == 0){
            return array('status' => 0 , 'msg' => '会员id不合法');
    	}
        $time = strtotime ("-1 year");
        $condition = array(
	   		'member_id' => $member_id ,
	   		'time'      => array('elt' , $time),
	   		'is_over'   => 1
	   	);
	   	//获取过期积分
        $score = M('Member_score_history')->where($condition)->sum('score');
        //积分标志为过期
        $r     = M('Member_score_history')->where($condition)->setField('is_over' , 2);
        if($r === false){
            return array('status' => 0 , 'msg' => '处理失败');
        }
        if($score > 0){
        	$model = M('Member');
	        $model->over_score = array('exp',"over_score+$score");//过期积分增加
	        $model->use_score  = array('exp',"use_score-$score");//可用积分减少
            $model->where(array('id'=>$member_id))->save();
        }
        return array('status' => 1  , 'msg' => '处理成功' , 'number' => $r);
    }    
}