<?php
namespace Admin\Model;
use Think\Model;
class KuaiDiModel extends Model{
	 public function Kuaidiadd($data_){ 
  	    $result = array(
            'status' => 1,
            'msg'    => '数据添加成功'
        ); 
        $data = array(
            'company'   => $data_['company'],
            'code' =>$data_['code'],
            'phone'   => $data_['phone'],
            'create_time'=> time()
        );
        $id = M('KuaiDi')->add($data);
        if($id === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }

        return $result;
  }
  public function Kuaidiupdate($data_){
     $result = array(
            'status' => 1,
            'msg'    => '数据修改成功'
        ); 
        $data = array(
            'id'      => $data_['id'],  
            'company'    => $data_['company']?$data_['company']:'',
            'code'  => $data_['code'],
            'phone'    => $data_['phone'],
             'create_time'=> time()
        );    

      
        $r = M('Kuaidi')->where(array('id'=>$data_['id']))->save($data);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }
        return $result;
  }




}