<?php
/**
 * banner管理逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class BannerModel extends Model{
    /**
     * 添加banner
     * @access public
     * @param  array $data_  数据
     * @return array $result 执行结果
     */    
    public function bannerAdd($data_){
        $data = array(
            'banner_thumb'  => $data_['banner_thumb'],
            'type'          => intval($data_['type']),
            'sort'          => intval($data_['sort']),
            'url'           => $data_['url'] 
        );    
        /*验证数据*/
        $model = D("Banner");
        $rules = array(
            array('banner_thumb','require','必须上传图片',self::EXISTS_VALIDATE),
            array('type',array(1,2),'banner类型不正确',2,'in')
        );
        if($model->validate($rules)->create($data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $model->getError()
            );
            return $result;
        }
		$r = M('Banner')->add($data);
		if($r !== false){
			return(array('status'=>'1','msg'=>'ok','id'=>$r));
		}else{
			return(array('status'=>'0','msg'=>'操作失败'));
		}
    }

    /**
     * 修改banner
     * @access public
     * @param  array $data_  数据
     * @return array $result 执行结果
     */    
    public function bannerUpdate($data_){
        $data = array(
            'id'   => intval($data_['id']),
            'sort' => intval($data_['sort']),
            'url'  => $data_['url'] 
        );  
        if($data_['banner_thumb']){
            $data['banner_thumb'] = $data_['banner_thumb'];   
        }  
        /*验证数据*/
        $model = D("Banner");
        $rules = array(
            array('id','/^[1-9]\d*$/','请选择id'),
            array('banner_thumb','require','必须上传图片',self::EXISTS_VALIDATE)
        );
        if($model->validate($rules)->create($data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $model->getError()
            );
            return $result;
        }
        $r = M('Banner')->save($data);
        if($r !== false){
            return(array('status'=>'1','msg'=>'ok'));
        }else{
            return(array('status'=>'0','msg'=>'操作失败'));
        }
    } 

    /**
     * 删除banner
     * @access public
     * @param  array $id bannerid
     * @return array $result 执行结果
     */    
    public function bannerDelete($id){
        $id = intval($id);
        if(!$id){
            return(array('status'=>'0','msg'=>'id错误'));
        }
        $old_thumb = M('Banner')->where(array('id'=>$id))->getField('banner_thumb');
        $r = M('Banner')->where(array('id'=>$id))->delete();
        if($r !== false){            
            unlink($old_thumb);//删除图片
            return(array('status'=>'1','msg'=>'ok'));
        }else{
            return(array('status'=>'0','msg'=>'操作失败'));
        }
    }        
}