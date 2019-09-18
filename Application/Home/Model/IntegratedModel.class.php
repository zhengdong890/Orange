<?php
/**
 * 集成项目业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Home\Model;
use Think\Model;
class IntegratedModel extends Model{
  protected $tableName='Integrated_lease'; //关闭检测字段

  /**
   * 添加集成项目
   * @access public
   * @param  array $data   集成项目信息 一维数组    
   * @return array $result 执行结果
   */ 
  public function integratedAdd($data_){     
        $result = array(
            'status' => 1,
            'msg'    => '数据添加成功'
        );          
        $data = array(
        	'title'          => $data_['title'], //项目命名
        	'kh_name'        => $data_['kh_name'], //客户名称
            'area'           => $data_['area'],//项目地址
            'contact_people' => $data_['contact_people'], //联系人
            'job'            => $data_['job'], //职位
            'phone'          => $data_['phone'], //联系电话
            'project_time'   => $data_['project_time'], //项目时间
            'is_use'         => $data_['is_use'] !== '' ? intval($data_['is_use']) : 100, //机器使用经验
            'chanzhi'        => $data_['chanzhi'], //企业产值
            'yusuan'         => $data_['yusuan'], //计划投入预算
            'type'           => is_array($data_['type'])?array_unique($data_['type']):array(), //项目类型
            'des'            => $data_['des'], //产品描述
            'brand_name'     => $data_['brand_name'], //指定品牌
            'jinrong'        => $data_['jinrong'] !== '' ? intval($data_['jinrong']) : 100, //金融服务
            'content'        => $data_['content'], //项目描述                                          
            'creat_time'     => date('Y-m-d H:i:s'),
            'update_time'    => date('Y-m-d H:i:s')
        );  
        foreach($data['type'] as $k => $v){
            if($data['type'][$k] == '0'){
                unset($data['type'][$k]);    
            }
        }
        if(count($data['type']) <=0 || count(array_diff($data['type'] , array(1,2,3,4))) > 0){
            return array(
                'status' => 0,
                'msg'    => '请输入正确的项目类型'
            );
        }
        /*验证数据*/
        $model  = D('Integrated');
        $rules  = array(
            array('title','require','必须输入项目命名',self::MUST_VALIDATE),
            array('kh_name','require','必须输入客户名称',self::MUST_VALIDATE),
            array('area','require','必须输入项目地址',self::MUST_VALIDATE),
            array('contact_people','require','必须输入联系人',self::MUST_VALIDATE),
            array('job','require','必须输入职位',self::MUST_VALIDATE),
            array('phone','require','必须输入联系电话',self::MUST_VALIDATE),
            array('project_time','require','必须输入项目时间',self::MUST_VALIDATE),
            array('is_use','require','必须输入机器使用经验',self::MUST_VALIDATE),
            array('is_use',array(0,1),'机器使用经验设置错误！',self::MUST_VALIDATE,'in'),
            array('chanzhi','require','必须输入企业产值',self::MUST_VALIDATE),
            array('yusuan','require','必须输入计划投入预算',self::MUST_VALIDATE),
            array('des','require','必须输入产品描述',self::MUST_VALIDATE),
            array('brand_name','require','必须输入指定品牌',self::MUST_VALIDATE),
            array('jinrong','require','必须输入金融服务',self::MUST_VALIDATE),
            array('jinrong',array(1,2,3,4),'金融服设置错误！',self::MUST_VALIDATE,'in'),
            array('content','require','必须输入项目描述  ',self::MUST_VALIDATE)
        );
        $data['type'] = implode(',' , $data['type']);
        if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
        }
        $id = M('Integrated_lease')->add($data);
        if($id === false){
           $result = array(
              'status' => 0,
              'msg'    => '数据添加失败'
           ); 
        }
        return $result;
  }
}