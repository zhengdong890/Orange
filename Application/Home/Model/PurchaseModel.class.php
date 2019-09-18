<?php
/**
 * 批量采购业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Home\Model;
use Think\Model;
class PurchaseModel extends Model{
  /**
   * 添加批量采购
   * @access public
   * @param  array $data   批量采购信息 一维数组    
   * @return array $result 执行结果
   */ 
  public function purchaseAdd($data_,$img1='',$img2='',$img3=''){     
        $result = array(
            'status' => 1,
            'msg'    => '数据添加成功'
        );
        $member_id =$_SESSION['member_data']['id'];
        $province = $data_['province'];
        $city     = $data_['city'];

        $province = M('Area')->where(array('area_no'=>$province))->field('area_name')->find();
        $city = M('Area')->where(array('area_no'=>$city))->field('area_name')->find();

        $province =$province['area_name'];
        $city =$city['area_name'];

        //处理传过来的省和市
        $data_['area']=$province.' '.$city;
        //处理传过来的是时间
        $data_['time']=str_replace('T', '  ', $data_['time']);
        $data_['cat_id'];
        $cat_name=M('Mall_category')->where(array('id'=>$data_['cat_id']))->field('cat_name')->find();
        $data_['cat_name']=$cat_name['cat_name'];
        //采购编号
        $number =setnum(8);
        $price_low = $data_['price_low'];
        $price_hei = $data_['price_hei'];
        if(!is_numeric($price_low) && !is_numeric($hei)){
            $result = array(
            'status' => 0,
            'msg'    => '请输入期望价格的合法数字'
        );
            return $result;
            exit;
            die;
        }
        if($price_hei<=$price_low){
            return array('status'=>0,'msg'=>'价格大小书写不正确');
            exit;
        }
        $price_range =$price_low.'-'.$price_hei;
        $data = array(
            'title'          => $data_['title'], //项目命名
            'number'         => $number,//项目编号
            'kh_name'        => $data_['name'], //客户名称
            'catid'          => $data_['cat_id'],
            'price_type1'    => $data_['price_type1'],
            'price_type2'    => $data_['price_type2'],
            'price_range'    => $price_range,
            'area'           => $data_['area'],//项目地址
            'contact_people' => $data_['name'], //联系人
            'phone'          => $data_['phone'], //联系电话
            'des'            => $data_['des'], //产品描述
            'num'            => $data_['num'],
            'cat_name'       => $data_['cat_name'],
            'unit'           => $data_['unit'],
            'province'       => $data_['province'],
            'city'           => $data_['city'],
            'modelnum'       => $data_['modelnum'],
            'create_time'    => date('Y-m-d H:i:s'),
            'deadline'       => $data_['time'],
            'member_id'      => $member_id
        );
        
        /*验证数据*/
        $model  = D('Purchase');
        $rules  = array(
            array('title','require','必须输入项目命名',self::MUST_VALIDATE),
            array('kh_name','require','必须输入联系人名称',self::MUST_VALIDATE),
            array('area','require','必须输入项目地址',self::MUST_VALIDATE),
            array('phone','/^1([0-9]{9})/','请输入正确的手机号码',self::MUST_VALIDATE),
            array('kh_name','/^[\x7f-\xff]+$/','联系人书写不正确',self::MUST_VALIDATE),
            array('contact_people','require','必须输入联系人',self::MUST_VALIDATE),
            //array('job','require','必须输入职位',self::MUST_VALIDATE),
            array('phone','require','必须输入联系电话',self::MUST_VALIDATE),   
            //array('chanzhi','require','必须输入企业产值',self::MUST_VALIDATE),
            //array('case','require','必须输入需求金额',self::MUST_VALIDATE),
            array('des','require','必须输入产品描述',self::MUST_VALIDATE),
            //array('type','require','必须需求类型',self::MUST_VALIDATE),
            //array('type',array(1,2,3),'需求类型设置错误！',self::MUST_VALIDATE,'in')
        );       
        if($model->validate($rules)->create($data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $model->getError()
            );
            return $result;
        }

        $r = M('Purchase')->add($data);
        if($img1 || $img2 || $img3){
            $img=array(
                    'purchase_id'=> "$r",
                    'thumb'      =>$img1,
                    'thumb1'     =>$img2,
                    'thumb2'     =>$img3,
                    'time'       =>time()
 
                );
            $s= M('Purchase_img')->add($img);

        }
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }
        return $result;
   }
   //立即报价添加
   public function offeradd($data_,$member_id,$img1='',$img2='',$img3=''){
        $result = array(
                'status'=>1,
                'msg'   =>'添加成功'
            );
        if(!is_numeric($data_['offer_price'])){
            $result = array(
            'status' => 0,
            'msg'    => '请输入期望价格的合法数字'
        );
            return $result;
            exit;
            die;
        }
        if($data_['id']==''){
            return array(
            'status' => 0,
            'msg'    => '项目id不能为空'
        );
            die;exit;
        }
        $data = array(
                'pur_id'    =>  $data_['id'],
                'member_id' =>  $member_id,
                'offer'    =>  $data_['offer'],//1.产品单价2.价格范围3.价格描述
                'offer_price'=> $data_['offer_price'],//报价
                'price_type1'=> $data_['price_type1'],//含税
                'price_type2'=> $data_['price_type2'],//含运费
                'data_des'   => $data_['data_des'],//详细信息
                'linkman'       => $data_['name'],//联系人
                'mobile_phone'=>$data_['mobile_phone'],//手机号码
                'time'        =>time()
            );
        //验证数据
        $model = D('Purchase_offer');
        $rules = array(
            array('offer_price','require','必须输入报价',self::MUST_VALIDATE),
            array('name','require','必须输入联系人',self::MUST_VALIDATE),
            array('name','/^[\x7f-\xff]+$/','联系人书写不正确',self::MUST_VALIDATE),
            array('mobile_phone','require','必须输入联系电话',self::MUST_VALIDATE),
            array('mobile_phone','/^1([0-9]{9})/','请输入正确的手机号码',self::MUST_VALIDATE),

            );
        if($model->validate($rules)->create($data) === false){
            $result = array(
                'status' => 0,
                'msg'    => $model->getError()
            );
            return $result;
        }
        $r = M('Purchase_offer')->add($data);
        if($img1 || $img2 || $img3){
            $img=array(
                    'offer_id'=> "$r",
                    'img1'      =>$img1,
                    'img2'     =>$img2,
                    'img3'     =>$img3,
                    'time'       =>time()
 
                );
            $s= M('Purchase_offer_img')->add($img);

        }
        if($r === false){
            $result = array(
                'status' => 0,
                'msg'    => '数据添加失败'
            );
        }
        return $result;

   }
}