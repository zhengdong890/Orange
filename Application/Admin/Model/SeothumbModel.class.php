<?php
namespace Admin\Model;
use Think\Model;
class SeothumbModel extends Model{
		 public function addnews($data_){ 
  	    $result = array(
            'status' => 1,
            'msg'    => '数据添加成功'
        ); 
        $data = array(
            'thumb'  => isset($data_['thumb'])? $data_['thumb'] : '',
            'time'   =>time()
        );    

        $id = M('Seothumb')->add($data);
        if($id === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }
        return $result;
  }
 public function update($data_){
     $result = array(
            'status' => 1,
            'msg'    => '数据修改成功'
        ); 
        $data = array(
            'id'      => $data_['id']
        );    
        if($data_['thumb']){
            $data['thumb'] = $data_['thumb'];
            $old_thumb = M('Seothumb')->where(array('id'=>$data_['id']))->getField('thumb');
        }
      
        $r = M('Seothumb')->where(array('id'=>$data_['id']))->save($data);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }else{
            if($data_['thumb']){
                unlink($old_thumb);
            }
        }
        return $result;
  }
}