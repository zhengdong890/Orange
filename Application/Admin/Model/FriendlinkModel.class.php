<?php
namespace Admin\Model;
use Think\Model;
class FriendlinkModel extends Model{
	 public function friendadd($data_){ 
  	    $result = array(
            'status' => 1,
            'msg'    => '数据添加成功'
        ); 
        $data = array(
  
            'name'   => $data_['name'],
            'is_show' =>($data_['is_show']),
            'sort'   => intval($data_['sort']),
            'thumb'  => isset($data_['thumb'])? $data_['thumb'] : '',
            'url'=> $data_['url'],
        );    

        $id = M('Friendlink')->add($data);
        if($id === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }
        return $result;
  }
  public function friendupdate($data_){
     $result = array(
            'status' => 1,
            'msg'    => '数据修改成功'
        ); 
        $data = array(
            'id'      => $data_['id'],  
            'name'    => $data_['name']?$data_['name']:'',
            'is_show'  => $data_['is_show'],
            'sort'    => intval($data_['sort']),
            'url'=> $data_['url']
        );    
        if($data_['thumb']){
            $data['thumb'] = $data_['thumb'];
            $old_thumb = M('Friendlink')->where(array('id'=>$data_['id']))->getField('thumb');
        }
      
        $r = M('Friendlink')->where(array('id'=>$data_['id']))->save($data);
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