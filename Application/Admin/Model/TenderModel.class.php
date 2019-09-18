<?php
/**
 * 融资招标业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class TenderModel extends Model{
  /**
   * 添加融资招标
   * @access public
   * @param  array $data   融资招标信息 一维数组    
   * @return array $result 执行结果
   */ 
  public function tenderAdd($data_){     
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
            'chanzhi'        => $data_['chanzhi'], //企业产值
            'yusuan'         => $data_['yusuan'], //计划投入预算
            'des'            => $data_['des'], //产品描述
            'case'           => $data_['case'],//需求金额
            'type'           => intval($data_['type']), //需求类型
            'modelnum'       => $data_['modelnum'],
            'create_time'    => date('Y-m-d H:i:s'),
            'update_time'    => date('Y-m-d H:i:s')
        );
        /*验证数据*/
        $model  = D('Tender_lease');
        $rules  = array(
            array('title','require','必须输入项目命名',self::MUST_VALIDATE),
            array('kh_name','require','必须输入客户名称',self::MUST_VALIDATE),
            array('area','require','必须输入项目地址',self::MUST_VALIDATE),
            array('contact_people','require','必须输入联系人',self::MUST_VALIDATE),
            array('job','require','必须输入职位',self::MUST_VALIDATE),
            array('phone','require','必须输入联系电话',self::MUST_VALIDATE),   
            array('chanzhi','require','必须输入企业产值',self::MUST_VALIDATE),
            array('case','require','必须输入需求金额',self::MUST_VALIDATE),
            array('des','require','必须输入产品描述',self::MUST_VALIDATE),
            array('type','require','必须需求类型',self::MUST_VALIDATE),
            array('type',array(1,2,3),'需求类型设置错误！',self::MUST_VALIDATE,'in')
        );       
        if($model->validate($rules)->create($data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $model->getError()
            );
            return $result;
        }
        $r = M('Tender_lease')->add($data);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }
        return $result;
  }

  /**
   * 修改融资招标
   * @access public
   * @param  array $data   融资招标信息 一维数组    
   * @return array $result 执行结果
   */ 
  public function tenderUpdate($data_){     
        $result = array(
            'status' => 1,
            'msg'    => '数据修改成功'
        );
        $data = array(
            'id'             => $data_['id'],
            'title'          => $data_['title'], //项目命名
            'kh_name'        => $data_['kh_name'], //客户名称
            'area'           => $data_['area'],//项目地址
            'contact_people' => $data_['contact_people'], //联系人
            'job'            => $data_['job'], //职位
            'phone'          => $data_['phone'], //联系电话
            'chanzhi'        => $data_['chanzhi'], //企业产值
            'yusuan'         => $data_['yusuan'], //计划投入预算
            'des'            => $data_['des'], //产品描述
            'case'           => $data_['case'],//需求金额
            'type'           => intval($data_['type']), //需求类型
            'modelnum'       => $data_['modelnum'],
            'update_time'    => date('Y-m-d H:i:s')  
        );
        /*验证数据*/
        $model  = D('Tender_lease');
        $rules  = array(
            array('id','/^[1-9]\d*$/','请选择id',self::MUST_VALIDATE),
            array('title','require','必须输入项目命名',self::MUST_VALIDATE),
            array('kh_name','require','必须输入客户名称',self::MUST_VALIDATE),
            array('area','require','必须输入项目地址',self::MUST_VALIDATE),
            array('contact_people','require','必须输入联系人',self::MUST_VALIDATE),
            array('job','require','必须输入职位',self::MUST_VALIDATE),
            array('phone','require','必须输入联系电话',self::MUST_VALIDATE),   
            array('chanzhi','require','必须输入企业产值',self::MUST_VALIDATE),
            array('case','require','必须输入需求金额',self::MUST_VALIDATE),
            array('des','require','必须输入产品描述',self::MUST_VALIDATE),
            array('type','require','必须需求类型',self::MUST_VALIDATE),
            array('type',array(1,2,3),'需求类型设置错误！',self::MUST_VALIDATE,'in')
        );       
        if($model->validate($rules)->create($data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $model->getError()
            );
            return $result;
        }
        $r = M('Tender_lease')->save($data);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据修改失败'
            );
        }
        return $result;
  } 
  
  /**
   * 融资招标审核
   * @access public
   * @param  array $data   审核数据
   * @return array $result 执行结果
   */
  public function tenderCheck($data = array()){
      $save_data = array(
          'id'            => intval($data['id']),
          'check_status'  => intval($data['check_status']) == 1 ? 1 : 0,
          'is_check'      => 1,
          'check_content' => $data['check_content']?$data['check_content']:'',
          'check_time'    => date('Y-m-d H:i:s')
      );
      if(!$save_data['id']){
          return array(
              'status' => 0,
              'msg'    => 'id错误'
          );
      }
      $r = M('Tender_lease')->save($save_data);
      if($r === false){
          return array(
              'status' => 0,
              'msg'    => '审核失败'
          );
      }else{
          return array(
              'status' => 1,
              'msg'    => '审核成功',
          );
      }
  }
  
  /**
   * 删除融资招标
   * @access public
   * @param  int   $id     融资招标id 
   * @return array $result 执行结果
   */ 
  public function tenderDelete($id){ 
       $result = array(
          'status' => 0,
          'msg'    => '删除成功'
       );     
       if(!preg_match('/^[1-9]\d*$/', $id)){
           return array(
             'status' => 0,
             'msg'    => '请选择正确的id'
           );
       }
       $r = M('Tender_lease')->where(array('id'=>$id))->delete();
       if($r === false){
          $result = array(
              'status' => 0,
              'msg'    => '删除失败'
          ); 
       }
       return $result;
  } 

  /**
   * 新增融资招标公司
   * @access public
   * @param  array $data   融资招标公司数据内容
   * @return array $result 执行结果
   */ 
  public function companyAdd($data_){
        $result = array(
            'status' => 1,
            'msg'    => '数据添加成功'
        );  
        $data = array(
        	'type_id'  => intval($data_['type_id']),
            'brand_id' => intval($data_['brand_id']),
            'area_id' => intval($data_['area_id']),
            'name'     => $data_['name'],          
            'content'  => $data_['content'],
            'url'      => $data_['url'],
            'keyword'  => $data_['keyword'],
            'is_tj'    => $data_['is_tj']
        ); 
        /*验证数据*/
        $model = D('Tender');
        $rules = array(
            array('type_id','/^[1-9]\d*$/','请选择公司类型'),
            array('brand_id','/^[1-9]\d*$/','请选择品牌'),
            array('area_id','/^[1-9]\d*$/','请选择省份'),
            array('name','require','必须输入公司名称'),
            array('content','require','必须输入公司主营描述')
        );
        if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
        }
        if($data_['img']){
           $data['img'] = $data_['img'];
        }
        if($data_['tj_thumb']){
          $data['tj_thumb']=$data_['tj_thumb'];
        }
        $id = M('Tender_company')->add($data);
        if($id === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }
        return $result;
  }

  /**
   * 融资招标公司修改
   * @access public
   * @param  array $data   融资招标公司数据内容
   * @return array $result 执行结果
   */ 
  public function companyUpdate($data_){
        $result = array(
            'status' => 1,
            'msg'    => '数据修改成功'
        );  
        $data = array(
        	  'id'       => intval($data_['id']),
        	  'type_id'  => intval($data_['type_id']),
            'brand_id' => intval($data_['brand_id']),
            'area_id' => intval($data_['area_id']),
            'name'     => $data_['name'],          
            'content'  => $data_['content'],
            'url'      => $data_['url'],
            'keyword'  => $data_['keyword'],
            'is_tj'    => $data_['is_tj']== 1 ? 1 : 0
        ); 
        /*验证数据*/
        $model = D('Tender_company');
        $rules = array(
            array('id','/^[1-9]\d*$/','请选择id'),
            array('type_id','/^[1-9]\d*$/','请选择公司类型'),
            array('brand_id','/^[1-9]\d*$/','请选择品牌'),
            array('area_id','/^[1-9]\d*$/','请选择省份'),
            array('name','require','必须输入公司名称'),
            array('content','require','必须输入公司主营描述')
        );
        if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
        }
        if($data_['img']){
        	$data['img'] = $data_['img'];
        	$old_thumb   = M('Tender_company')->where(array('id'=>$data['id']))->getField('img');
        }
        if($data_['tj_thumb']){
          $data['tj_thumb'] = $data_['tj_thumb'];
          $old_thumb   = M('Tender_company')->where(array('id'=>$data['id']))->getField('tj_thumb');
        }
        $id = $data['id'];unset($data['id']);
        $r  = M('Tender_company')->where(array('id'=>$id ))->save($data);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据修改失败'
            );
        }else{
            if($data['img']){
        	    unlink($old_thumb);
            }
        }
        return $result;
  }

  /**
   * 删除融资招标公司
   * @access public
   * @param  int   $id     融资招标公司id 
   * @return array $result 执行结果
   */ 
  public function companyDelete($id){ 
       $result = array(
          'status' => 0,
          'msg'    => '删除成功'
       );     
       if(!preg_match('/^[1-9]\d*$/', $id)){
           return array(
             'status' => 0,
             'msg'    => '请选择正确的id'
           );
       }
       $r = M('Tender_company')->where(array('id'=>$id))->delete();
       if($r === false){
          $result = array(
              'status' => 0,
              'msg'    => '删除失败'
          ); 
       }
       return $result;
  }

  /**
   * 获取融资招标公司
   * @access public
   * @return array $result 执行结果
   */ 
  public function getCompany(){
      $result = M('tender_company as a')
              ->join('left join tp_area as b on a.area_id=b.id')
              ->field('a.*,b.area_name')
              ->select();
      return $result;
  } 

  /**
   * 获取一个融资招标公司
   * @access public
   * @param  int   $data   融资招标公司id 
   * @return array $result 执行结果
   */ 
  public function getOneCompany($id){
      $result = M('tender_company')->where(array('id'=>$id))->find();
      return $result;
  } 

   /**
   * 获取融资招标banner
   * @access public
   */ 
   public function getBanner(){
   	   	$banners = M('Tender_banner')->order('sort')->select();
        return $banners;
   } 

  /**
   * 新增融资招标banner
   * @access public
   * @param  array $data   融资招标banner新增数据
   * @return array $result 执行结果
   */ 
  public function tenderBannerAdd($data_){
        $result = array(
            'status' => 1,
            'msg'    => '数据添加成功'
        );  
        $data = array(
        	'sort   '     => intval($data_['sort']),
            'status'      => intval($data_['status']),
            'create_time' => time(),
            'desc'        => $data_['desc'],
            'link'        => $data_['link'],
            'thumb'       => $data_['thumb']
        ); 
        /*验证数据*/
        $model = D('Tender_banner');
        $rules = array(
            array('thumb','require','必须选择图片')
        );
        if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
        }
        $r  = M('Tender_banner')->add($data);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据修改失败'
            );
        }
        return $result;
  }

  /**
   * 融资招标banner修改
   * @access public
   * @param  array $data   融资招标banner修改数据
   * @return array $result 执行结果
   */ 
  public function tenderBannerUpdate($data_){
        $result = array(
            'status' => 1,
            'msg'    => '数据修改成功'
        );  
        $data = array(
        	'id'       => intval($data_['id']),
        	'sort   '  => intval($data_['sort']),
            'status'   => intval($data_['status']),
            'desc'     => $data_['desc'],
            'link'     => $data_['link']
        ); 
        /*验证数据*/
        $model = D('Tender_banner');
        $rules = array(
            array('id','/^[1-9]\d*$/','请选择id')
        );
        if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
        }
        if($data_['thumb']){
        	$data['thumb'] = $data_['thumb'];
        	$old_thumb     = M('Tender_banner')->where(array('id'=>$data['id']))->getField('thumb');
        }
        $id = $data['id'];unset($data['id']);
        $r  = M('Tender_banner')->where(array('id'=>$id ))->save($data);
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据修改失败'
            );
        }else{
            if($data['thumb']){
        	    unlink($old_thumb);
            }
        }
        return $result;
  }

  /**
   * 删除融资招标banner
   * @access public
   * @param  int   $id     融资招标banner id 
   * @return array $result 执行结果
   */ 
  public function tenderBannerDelete($id){ 
       $result = array(
          'status' => 0,
          'msg'    => '删除成功'
       );     
       if(!preg_match('/^[1-9]\d*$/', $id)){
           return array(
             'status' => 0,
             'msg'    => '请选择正确的id'
           );
       }
       $r = M('Tender_banner')->where(array('id'=>$id))->delete();
       if($r === false){
          $result = array(
              'status' => 0,
              'msg'    => '删除失败'
          ); 
       }
       return $result;
  } 
}