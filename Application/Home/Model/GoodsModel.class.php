<?php
namespace Home\Model;
use Think\Model;
/**
 * 共享商品模块业务逻辑
 * @author 幸福无期
 */
class GoodsModel extends Model{  
  
  /**
   * 添加共享商品
   * @access public
   * @param  array $data_goods    商品基本信息 一维数组 
   * @param  array $goods_gallery 商品相册     二维数组 array(array('图片描述','上传路径'))
   * @param  array $rent          租期         二维数组    
   * @return array $result 执行结果
   */ 
  public function goodsAdd($data = array() ,  $goods_gallery = array() , $rent = array()){
        $result = array(
            'status' => 1,
            'msg'    => '添加成功'
        );
        $data_goods = array(
            'member_id'     => $data['member_id'],//卖家id
            'cat_id'        => $data['cat_id'],//商品分类
            'goods_name'    => $data['goods_name'],//商品名称
            'goods_price'   => $data['goods_price'],//商品价格
            'brand_id'      => $data['brand_id'],//商品品牌
            'goods_thumb'   => $data['goods_thumb'],//商品缩略图
            'goods_img'     => $data['goods_img'],//商品主图
            'min_rent'      => intval($data['min_rent'])?intval($data['min_rent']):1,//最小租期
            'max_rent'      => $data['max_rent'],//最大租期
            'rent_dw'       => intval($data['rent_dw']),//租期单位
            'province'      => $data['province'],//省
            'city'          => $data['city'],//市
            'area'          => $data['area'],//区            
            'status'        => $data['status'],//商品状态
            'sort'          => $data['sort']?$data['sort']:0,//商品排序
            'cat_filter_id' => $data['cat_filter_id'],              
            'sbcs'          => $data['sbcs'],//设备成色
            'sbcj'          => $data['sbcj'],//设备厂家
            'phone'         => $data['phone'],//电话
            'goods_model'   => $data['goods_model'],//设备型号
            'buy_time'      => $data['buy_time'],//购置时间
        ); 
        if($data['rent_dw'] != 4){ //非委托项目需要输入数量 库存
            $data_goods['goods_number'] = $data['goods_number'];
            $data_goods['deposit']      = intval($data['deposit']);
            $data_goods['safest']       = intval($data['safest']);
        }
        $data_goods['goods_code']  = empty($data['goods_code'])?setnum(12):$data['goods_code'];//设置货号
        $data_goods['create_time'] = date('Y-m-d H:i:s');//设置添加时间 
        $data_goods['update_time'] = $data_goods['create_time'];//设置编辑时间
        $id = M('Goods')->add($data_goods);//添加数据到商品表
        if($id !== false){
            if($data_goods['rent_dw'] != 4){
                $r = $this->goodsRent($rent , $id); //租期处理   
            }            
            /*添加商品相册*/
            if($goods_gallery){
                $r = $this->goodsGallery($goods_gallery , $id);                        
            }   
            M('Goods_data')->add(array('goods_id'=>$id,'goods_content'=>$data['goods_content']));       
        }else{
            $result = array(
             'status' => 0,
             'msg'    => '商品添加失败'
            );
        }
        return $result;
  }

  /**
   * 修改商品
   * @access public
   * @param  array $data_goods    商品基本信息 一维数组 
   * @param  array $baseattr      销售属性     一维数组 array('销售属性id'=>'属性值id')
   * @param  array $extendattr    扩展属性     二维数组 array(array('属性id','属性值'))
   * @param  array $goods_gallery 商品相册     二维数组 array(array('图片描述','上传路径'))   
   * @param  array $rent          租期         二维数组 
   * @return array $result 执行结果
   */ 
  public function goodsUpdate($data = array() , $goods_gallery = array() , $rent = array()){
        $result = array(
            'status' => 1,
            'msg'    => '编辑成功'
        );
        $id        = $data['id'];
        $member_id = $data['member_id'];
        $data_goods = array(
            'cat_id'        => $data['cat_id'],//商品分类
            'goods_name'    => $data['goods_name'],//商品名称
            'goods_price'   => $data['goods_price'],//商品价格
            'brand_id'      => $data['brand_id'],//商品品牌
            'min_rent'      => intval($data['min_rent'])?intval($data['min_rent']):1,//最小租期
            'max_rent'      => $data['max_rent'],//最大租期
            'rent_dw'       => intval($data['rent_dw']),//租期单位
            'province'      => $data['province'],//省
            'city'          => $data['city'],//市
            'area'          => $data['area'],//区            
            'status'        => $data['status'],//商品状态
            'cat_filter_id' => $data['cat_filter_id'],              
            'sbcs'          => $data['sbcs'],//设备成色
            'sbcj'          => $data['sbcj'],//设备厂家
            'phone'         => $data['phone'],//电话
            'goods_model'   => $data['goods_model'],//设备型号
            'buy_time'      => $data['buy_time'],//购置时间
            'is_check'      => 0 
        );
        if($data['rent_dw'] != 4){ //非委托项目需要输入数量 库存
            $data_goods['goods_number'] = $data['goods_number'];
            $data_goods['deposit']      = intval($data['deposit']);
            $data_goods['safest']       = intval($data['safest']);
        }
        if($data['goods_thumb']){
            $data_goods['goods_thumb'] = $data['goods_thumb'];
        }
        if($data['goods_img']){
            $data_goods['goods_img'] = $data['goods_img'];
        }
        $data_goods['update_time'] = date('Y-m-d H:i:s');//设置上架时间
        $r = M('Goods')->where(array('id'=>$id,'member_id'=>$member_id))->save($data_goods);//修改数据到商品表
        if($r === false || $r == 0){
           $result = array(
              'status' => 0,
              'msg'    => '商品插入数据库失败'
           );
        }
        if($goods_gallery['old']){
            $r = $this->goodsGallery($goods_gallery['old']);//商品相册修改
        }
        if($goods_gallery['new']){
            $r = $this->goodsGallery($goods_gallery['new'] , $data_goods['id']);//商品相册添加
        }
        if($data_goods['rent_dw'] != 4){
            $r = $this->goodsRent($rent , $data_goods['id'] , $type = 2); //商品租期处理
        }
        M("Goods_data")
        ->where(array('goods_id'=>$id))
        ->save(array('goods_content'=>$data['goods_content']));
        return $result;
  }

  /**
   * 商城商品删除
   * @access public
   * @param  int   $goods_id 商品id
   * @return array $result 执行结果
   */ 
  public function goodsDelete($goods_id){
  	  $result = array(
         'status' => 1,
         'msg'    => '商品删除成功'
      );
  	  $goods_id = intval($goods_id);
  	  if(!$goods_id){
     	return array(
           'status' => 0,
           'msg'    => 'id错误'
        );
      }
      /*删除商品数据*/
      //获取商品缩略图
      $imgs = M('Goods')->where(array('id'=>$goods_id))->Field('goods_thumb,goods_img')->find();
      $r    = M('Goods')->where(array('id'=>$goods_id))->delete();
	  if($r !== false){
	  	  /*先删除商品相册*/
	  	  $this->goodsGalleryDelete($goods_id , 2);
	      /*删除商品扩展属性*/
	      M('Goods_extendattr')->where(array('goods_id'=>$goods_id))->delete();
	      /*删除商品租期数据*/
	      M('Goods_rent')->where(array('goods_id'=>$goods_id))->delete();
	      /*删除商品data表数据*/
	      M('Goods_data')->where(array('goods_id'=>$goods_id))->delete();
		  unlink($imgs['goods_thumb'],$imgs['goods_img']);//删除图片
	  }else{
		  $result = array(
		     'status' => 0,
		     'msg'    => '商品删除失败'
		  );
	  }
	  return $result;
  } 
  
  /**
   * 更改商品上下架状态
   * @access public
   * @param  array $data 数据
   */
  public function statusChange($data){
      $result = array(
          'status' => 1,
          'msg'    => '修改成功'
      );
      $sql_arr = array(
          'status'   => " SET status = CASE id"
      );
      $ids = array();
      foreach($data as $k => $v){
          $ids[] = $v['id'];
          $sql_arr['status'] .= " WHEN {$v['id']} THEN '{$v['status']}'";
      }
      $ids = implode(',' , $ids);
      $sql_arr['status'] .= ' END';
      $sql = "UPDATE tp_goods".$sql_arr['status']." where id IN ($ids)";
      $r   = M()->execute($sql);
      if($r === false){
          $result = array(
              'status' => 0,
              'msg'   => '修改失败'
          );
      }
      return $result;
  }

  /**
   * 商品相册属性处理
   * @access public
   * @param  array $gallery   商品相册数据
   * @param  array $goods_id  商品id
   */ 
  public function goodsGallery($gallery , $goods_id){
      /*添加商品相册*/
      if($goods_id){
           $values = array();
           $fields = array('`gallery_img`','`goods_id`');
           foreach($gallery as $k => $v){
              $v['goods_id'] = $goods_id;
              $values[]      = "('" . implode("','",$v) . "')";
           }
           $sql = "INSERT INTO `tp_goods_gallery` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
           $r   = M()->execute($sql);   
           if($r === false){
              return false;
           }          
      }else{
           /*修改商品相册*/
           foreach($gallery as $k => $v){
                $save_data = array(array(
                    'gallery_remark' => $v['gallery_remark']
                ));
                if($v['gallery_img']){
                     $save_data['gallery_img'] = $v['gallery_img'];
                }
                $r = M('Goods_gallery')->where(array('id'=>$v['id']))->save($save_data);
           }          
      }
  }

  /**
   * 删除商品相册
   * @access public
   * @param  int   $id 相册id||商品id
   * @return array $result 执行结果
   */  
  public function goodsGalleryDelete($id , $type = 1){ 
     if(!$id){
     	return array(
           'status' => 0,
           'msg'    => 'id错误'
        );
     }
     $result = array(
         'status' => 1,
         'msg'    => '删除成功'
     );  
     if($type == 2){
        $oldimg = M('Goods_gallery')->where(array('goods_id'=>$id))->field('gallery_img')->select(); 
	    $r      = M('Goods_gallery')->where(array('goods_id'=>$id))->delete();         	
     }else
     if($type == 1){
	  	 $oldimg = M('Goods_gallery')->where(array('id'=>$id))->Field('gallery_img')->find();
		 $r      = M('Goods_gallery')->where(array('id'=>$id))->delete($id);    	
     }
 	 if($r !== false){  
 	    foreach ($oldimg as $v) {
 	      	unlink($v);//删除图片
 	    }  
	 }else{
		$result = array(
           'status' => 1,
           'msg'    => '删除失败'
        );
     } 
     return $result;
  }

  /**
   * 商品租期处理
   * @access public
   * @param  array $rent     商品租期属性数据
   * @param  int   $goods_id 商品id
   */ 
  public function goodsRent($rent , $goods_id , $type = 1){
      if(count($rent) <= 0){
          return false;
      }
      if($type == 1){
          /*增加数据*/          
          $values = array();
          $fields = array('`start`','`end`','`goods_rent_price`','`goods_id`');
          foreach($rent as $k => $v){
              $v['goods_id'] = $goods_id;
              $values[]      = "('" . implode("','",$v) . "')";
          }
          $sql = "INSERT INTO `tp_goods_rent` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
          $r   = M()->execute($sql);         
      }else
      if($type == 2){
          foreach ($rent as $k => $v){
              $rent_ids[] = $k;
          }
          $ids    = M('Goods_rent')->where(array('goods_id'=>$goods_id))->field('id')->select();
          $delete = array();//删除的数据
          foreach ($ids as $k => $v) {
              if(in_array($v['id'] , $rent_ids)){
                  //需要保存的数据
                  M('Goods_rent')->where(array('id'=>$v['id']))->save(array(
                      'start' => $rent[$v['id']]['start'],
                      'end'   => $rent[$v['id']]['end'],
                      'goods_rent_price' => $rent[$v['id']]['goods_rent_price']
                    )
                  );
                  unset($rent[$v['id']]);
              }else{
                  //需要删除的数据
                  $delete[] = $v['id'];
                  unset($rent[$v['id']]);
              }
          }
          /*增加数据*/
          if(count($rent) > 0){
              $values = array();
              $fields = array('`start`','`end`','`goods_rent_price`','`goods_id`');
              foreach($rent as $k => $v){
                  $v['goods_id'] = $goods_id;
                  $values[]      = "('" . implode("','",$v) . "')";
              }
              $sql = "INSERT INTO `tp_goods_rent` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
              $r   = M()->execute($sql);         
          }
          /*删除数据*/
          $delete = implode(',' , $delete);
          if($delete){
             $r = M('goods_rent')->where(array('id'=>array('in' , $delete)))->delete();
          }         
      }   
  }

  /**
   * 检测商品数据 合法性
   * @access public
   * @param  int   $goods_id 商品id
   * @return array $result 执行结果
   */
  public function checkGoodsData($data){
      /*验证数据*/
      $goods = D("Goods");
      $data['province'] = $data['province'] ? $data['province'] : '';
      $data['city'] = $data['city'] ? $data['city'] : '';
      $data['deposit'] = $data['deposit'] === '' || !isset($data['deposit'])?100:intval($data['deposit']);
      $data['safest']  = $data['safest'] === '' || !isset($data['safest'])?100:intval($data['safest']);
      $rules = array(
          array('cat_id','/^[1-9]\d*$/','请选择所属分类',self::MUST_VALIDATE),
          array('goods_name','require','必须输入商品名',self::MUST_VALIDATE),
          array('phone','require','必须输入电话',self::MUST_VALIDATE),
          array('buy_time','require','须输入购置时间',self::MUST_VALIDATE),
          array('sbcj','require','必须输入设备厂家',self::MUST_VALIDATE),
          array('goods_price','require','请输入商品价格',self::MUST_VALIDATE),
          array('goods_price','/^[0-9]+(.[0-9]{1,2})?$/','请输入正确的价格',self::MUST_VALIDATE),
          array('province','require','必须输入一级地区',self::MUST_VALIDATE),
          array('city','require','必须输入二级地区',self::MUST_VALIDATE),
          array('brand_id','/^[1-9]\d*$/','请选择商品品牌',self::MUST_VALIDATE)       
      );
      if($data['rent_dw'] != 4){ //非委托项目需要输入数量
          $rules[] = array('goods_number','/^[1-9]\d*$/','请输入正确的数量');
          $rules[] = array('deposit',array(0,1,2),'押金选择不正确！',2,'in');
          $rules[] = array('safest',array(0,1),'保险选择不正确！',2,'in');
      }
      if($goods->validate($rules)->create($data) === false){
          $result = array(
              'status' => 0,
              'msg'    => $goods->getError()
          );
          return $result;
      }
      return array(
          'status' => 1
      );
  }
  
  /**
   * 检查租期数据是否正确
   * @access public
   * @param  array $rent     商品租期属性数据
   * @param  int   $goods_id 商品id
   * @return array $result 执行结果
   */   
  public function checkRent($min_rent , $max_rent, $rent){
      $min_rent = intval($min_rent);
      $max_rent = intval($max_rent);
      if($min_rent >= $max_rent){
          return array(
              'status' => 0,
              'msg'    => '租期最小值只能小于最大值'
          );
      }
      if($min_rent <= 0){
          return array(
              'status' => 0,
              'msg'    => '租期最小值至少为1'
          );
      }
      if(!$max_rent){
          return array(
              'status' => 0,
              'msg'    => '必须输入租期最大值'
          );          
      }
      $result = array(
          'status' => 1  
      );
      /*检测租期区间优惠段*/
      foreach($rent as $k => $v){
          if($v['start'] < $min_rent - 1){
              $result = array(
                  'status' => 0,
                  'msg'    => '租期优惠段开始时间必须大于最小租期'
              );
              break;
          }
          if($v['end'] > $max_rent){
              $result = array(
                  'status' => 0,
                  'msg'    => '租期优惠段开始时间必须小于最大租期'
              );
              break;
          }
          if($v['start'] > $v['end']){
              $result = array(
                  'status' => 0,
                  'msg'    => '租期优惠段开始时间必须小于结束时间'
              );
              break;
          }
          foreach($rent as $k1 => $v1){
              if($v['start'] <= $v1['end'] && $v['start'] >= $v1['start'] && $k != $k1){
                  $result = array(
                      'status' => 0,
                      'msg'    => '租期优惠段设置错误'
                  );
              }
              if($v['end'] <= $v1['end'] && $v['end'] >= $v1['start'] && $k != $k1){
                  $result = array(
                      'status' => 0,
                      'msg'    => '租期优惠段设置错误'
                  );
                  break;
              }
          }
      }
      return $result;
  }

 /**
  * 主页商品获取
  * @return array 返回结果
  */
  public function getIndexGoods(){
      $ids   = M('Goods_model')->where(array('id'=>6))->getField("goods_ids");    
      $goods = M("Goods")->where(array('id'=>array('in',$ids)))->limit(0,8)->select(); 
      return $goods;
  }
  
  /**
   * 获取热租商品
   * @return array 返回结果
   */
  public function getHotGoods(){
      $goods = M("Goods")->where(array('status'=>1))->sort('sale_num desc')->limit(0,8)->select();
      return $goods;
  } 
  


  /**
    * 获取商品租约
    * @param int   $goods_id 商品id
    * @param array data      租约信息
    */
   public function getGoodsRent($goods_id){
       $goods_id = intval($goods_id);
       $data     = M("Goods_rent")->where(array('goods_id'=>$goods_id))->select();
       return $data;
   }
   
   
   /**
    * 统计商品租金
    * @param  int   $goods_data   出租时间
    * @param  array $rent_data    出租优惠区间段  
    * @param  float $goods_price  商品价格
    * @return array 返回结果
    */
   public function totalRent($rent_time , $rent_data , $goods_price){ 
      $rent_case  = 0; //租金金额
      $times      = $rent_time;
      foreach($rent_data as $k => $v){          
          if($v['end'] < $rent_time){
              $time = $v['end'] - $v['start'] + 1;  
              $rent_case += $v['goods_rent_price'] * $time; //租金金额累计   
              $times -=  $time;       
          }else
          if($v['start'] <= $rent_time){
              $time = $rent_time - $v['start'] + 1; 
              $rent_case += $v['goods_rent_price'] * $time; //租金金额累计 
              $times -=  $time;                 
          }                                      
      }
      $rent_case += $goods_price * $times;
      return $rent_case;
   } 
   
 
   
/******************************************预留代码*****************************************************/   
  
   /**
    * 商品销售属性处理
    * @access public
    * @param  array $extendattr 商品销售属性数据
    * @param  array $type       处理类型 1添加 2修改 默认为1
    */
   public function goodsAttr($baseattr , $type = 1 , $id){
       /*修改销售属性*/
       if($type == 1){
           $fields = array('`attr_id`','`attr_value`','`thumb`','`goods_id`');
           foreach($baseattr as $k => $v){
               $v['goods_id'] = $id;
               $values[]      = "('" . implode("','",$v) . "')";
           }
           $sql = "INSERT INTO `tp_goods_baseattr` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
           $r   = M()->execute($sql);
       }
   }
   
   /**
    * 商品扩展属性处理
    * @access public
    * @param  array $extendattr 商品扩展属性数据
    * @param  array $goodsid    商品id
    */
   public function goodsExtendAttr($extendattr , $goods_id){
       if(count($extendattr) <= 0){
           return false;
       }
       /*添加扩展属性*/
       if($goods_id){
           $values = array();
           $fields = array('`attr_id`','`attr_value`','`goods_id`');
           foreach($extendattr as $k => $v){
               $v['goods_id'] = $goods_id;
               $values[]      = "('" . implode("','",$v) . "')";
           }
           $sql = "INSERT INTO `tp_goods_extendattr` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
           $r   = M()->execute($sql);
           if($r === false){
               return false;
           }
       }else{
           /*修改扩展属性*/
           foreach($extendattr as $k => $v){
               $r = M('Goods_extendattr')->where(array('goods_extendattr_id'=>$v['goods_extendattr_id']))->save(array(
                   'attr_value' => $v['attr_value']
               ));
           }
       }
   }
   
   /**
    * 检查扩展属性
    * @access protected
    * @param  array $extendattr    扩展属性     二维数组 array(array('属性id','属性值'))
    * @return array $result 执行结果
    */
   protected function checkExtendAttr($extendattr , $type = 'add'){
       $goods = D("Goods");
       if($type == 'add'){
           $rules = array(
               array('attr_id','require','必须输入扩展属性id'),
               array('attr_id','/^[1-9]+[0-9]*/','扩展属性id格式不正确'),
               array('attr_value','require','必须输入扩展属性值'),
           );
       }else{
           $rules = array(
               array('goods_extendattr_id','require','必须输入扩展属性id'),
               array('goods_extendattr_id','/^[1-9]+[0-9]*/','扩展属性id格式不正确'),
               array('attr_value','require','必须输入扩展属性值'),
           );
       }
       foreach ($extendattr as $k => $v) {
           if($goods->validate($rules)->create($v) === false){
               return false;
           }
       }
       return true;
   }   
   
   /**
    * 获取商品扩展属性
    * @param int   $goods_id 商品id
    * @param array data      扩展属性
    */
   public function getGoodsExtendAttr($goods_id){
       $goods_id = intval($goods_id);
       $data     = M('goods_extendattr as a')
       ->join("tp_attrbute as b on a.attr_id=b.attr_id")
       ->where(array('goods_id'=>$goods_id))
       ->field("a.*,b.attr_name")
       ->select();
       return $data;
   }
   
   /**
    * 根据分类获取扩展属性
    * @param int   $cat_id 商品分类id
    * @param array data    扩展属性
    */
  public function getCatExtendAttr($cat_id){
    $filter_extendattr=M('Category')->where(array("id"=>$cat_id))->getField('filter_extendattr');
    $ids=$filter_extendattr;
    //获取第一级属性
    $pdata=M('Attrbute as a')
         ->where(array("attr_id"=>array('in',$filter_extendattr)))
         ->select();
    //获取子集属性
    $cdata_=M('Attrbute as a')
          ->where(array("pid"=>array('in',$filter_extendattr)))
          ->select();
    foreach($cdata_ as $v){
      $ids.=','.$v['attr_id'];
    }
    /*获取扩展属性value值*/
    $extendattr_value_= M('Attrbute_value')
                      ->where(array('attr_id'=>array('in',$ids)))
            ->select();
    foreach($extendattr_value_ as $v){
      $extendattr_value[$v['attr_id']][]=$v;
    } 
    foreach($cdata_ as $v){
      $data=$v;
      $data['all_value']=$extendattr_value[$v['attr_id']];
      $cdata[$v['pid']][]=$data;
      
    }
    $filter_extendattr=explode(',',$filter_extendattr);
    foreach($pdata as $v){
      $goods_attr[$v['attr_id']]['attr_name']=$v['attr_name'];
      $goods_attr[$v['attr_id']]['attr_id']=$v['attr_id'];
      $goods_attr[$v['attr_id']]['attr_input_type']=$v['attr_input_type'];
      $goods_attr[$v['attr_id']]['all_value']=$extendattr_value[$v['attr_id']];
      $goods_attr[$v['attr_id']]['child']=$cdata[$v['attr_id']];
    } 
    return $goods_attr;
  } 

  /*获取商品扩展属性*/
  public function get_extendattr($goods_id,$cat_id){
          $filter_extendattr=M('Category')->where(array("id"=>$cat_id))->getField('filter_extendattr');
          $cat_extendattr_= M('Attrbute as a')
                          ->join('tp_attrbute_value as t on a.attr_id=t.attr_id')
                          ->field('t.attr_value,t.id,a.attr_id,a.attr_name,a.type,a.attr_input_type')  
                          ->where(array('_complex'=>array(
			                          		      'a.attr_id'=>array('in',$filter_extendattr),
			                          			  'a.pid'=>array('in',$filter_extendattr),
			                          			   '_logic'=>'OR',
	                          		              ),
                          		       'a.attr_input_type'=>2,                        		       
                          		  ))
                          ->select();
          foreach($cat_extendattr_ as $v){
              $cat_extendattr[$v['attr_id']]['attr'][]=$v;
              $cat_extendattr[$v['attr_id']]['attr_input_type']=$v['attr_input_type'];
              $cat_extendattr[$v['attr_id']]['attr_name']=$v['attr_name'];
              
          }
          /*获取商品已有的扩展属性*/
          $goods_attr= M('Goods_extendattr')
                       ->where(array('goods_id'=>$goods_id))
                       ->select(); 
          /*获取手动输入*/
          $cat_extendattr1_= M('Attrbute as a')
                            ->where(array('a.attr_id'=>array('in',$filter_extendattr),'attr_input_type'=>1))                           
                            ->select();
          foreach($cat_extendattr1_ as $v){
              $cat_extendattr1[$v['attr_id']]=$v['attr_name'];
          }        
          foreach($goods_attr as $k=>$v){
              if($cat_extendattr[$v['attr_id']]['attr_input_type']=='2'){
                  $goods_attr[$k]['attr_name']=$cat_extendattr[$v['attr_id']]['attr_name'];
                  $goods_attr[$k]['attr_input_type']=$cat_extendattr[$v['attr_id']]['attr_input_type'];
              }else{
                  $goods_attr[$k]['attr_name']=$cat_extendattr1[$v['attr_id']];
                  $goods_attr[$k]['attr_input_type']='1';
              }             
              $goods_attr[$k]['all_value']=$cat_extendattr[$v['attr_id']]['attr'];
          }
          return $goods_attr;
    }    
}