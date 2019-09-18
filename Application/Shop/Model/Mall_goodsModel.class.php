<?php
/**
 * 商城商品模块业务逻辑
 * @author 幸福无期
 * @email 597089187@qq.com
 */
namespace Shop\Model;
use Think\Model;
class Mall_goodsModel extends Model{ 
  /**
   * 添加商城商品
   * @access public
   * @param  array $data_goods    商品基本信息 一维数组 
   * @param  array $baseattr      销售属性     一维数组 array('销售属性id'=>'属性值id')
   * @param  array $extendattr    扩展属性     二维数组 array(array('属性id','属性值'))
   * @param  array $goods_gallery 商品相册     二维数组 array(array('图片描述','上传路径'))  
   * @return array $result 执行结果
   */ 
  public function goodsAdd($data = array() , $baseattr = array() , $extendattr = array() , $goods_gallery = array()){
        $result = array(
            'status' => 1,
            'msg'    => '商品添加成功'
        );
        /*提取共享商品表(goods)需要的数据*/
        $xb = array(
           'cat_id','goods_name','goods_describe','goods_price','goods_code','goods_sn',
           'goods_number','goods_credit','status','sort','cat_filter_id','deposit','safest',
           'brand_id','province','city','area','goods_thumb','goods_img','is_new','shop_cat',
           'member_id','goods_model','yunfei','fwbz','sbcj'
        );
        foreach($xb as $v){
           $data_goods["$v"] = $data["$v"]?$data["$v"]:'';
        }
        $data_goods['status'] = 1;
        /*验证数据*/
        $goods = D("Mall_goods");
        $rules = array(
            array('member_id','/^[1-9]\d*$/','商家id错误'),
            array('goods_name','require','必须输入商品名',self::EXISTS_VALIDATE),
            array('goods_price','require','请输入商品价格',self::EXISTS_VALIDATE),
            array('goods_price','/^[0-9]+(.[0-9]{1,2})?$/','请输入正确的价格'),
            array('goods_img','require','必须输入商品主图',self::EXISTS_VALIDATE),
            array('cat_id','/^[1-9]\d*$/','请选择所属分类',self::EXISTS_VALIDATE), 
            array('brand_id','/^[1-9]\d*$/','请选择商品品牌'),
            array('province','require','必须输入一级地区',self::EXISTS_VALIDATE),
            array('city','require','必须输入二级地区',self::EXISTS_VALIDATE)
        );
        if($goods->validate($rules)->create($data_goods) === false){
           $result = array(
               'status' => 0,
               'msg'    => $goods->getError()
           );
           return $result;
        } 
        if(!$this->checkExtendAttr($extendattr)){
           $result = array(
             'status' => 0,
             'msg'    => '扩展属性有错误'
           );
           return $result;
        }
        $data_goods['goods_code']  = empty($data['goods_code'])?setnum(12):$data['goods_code'];//设置货号
        $data_goods['create_time'] = date('Y-m-d H:i:s');//设置上架时间      
        $id = M('Mall_goods')->add($data_goods);//添加数据到商品表	
        if($id !== false){ 
            /*添加销售属性*/
            if($baseattr){
                $r = $this->goodsAttr($baseattr , $id);                   
            }                   
            $r = $this->goodsExtendAttr($extendattr , $id);  //添加扩展属性     
            /*添加商品相册*/
            if($goods_gallery){
                $r = $this->goodsGallery($goods_gallery , $id);                        
            } 
            $goods_data = array(
                'goods_id'      => $id,
                'goods_content' => $data['goods_content'],
                'goods_weight'  => $data['goods_weight'],
                'ensure'        => $data['ensure'],
                'watts'         => $data['watts'],
                'precise'       => $data['precise'],
                'measurement'   => $data['measurement'],
                'uses'          => $data['uses']
            );
            M('Mall_goods_data')->add($goods_data);       
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
  public function goodsUpdate($data = array() , $baseattr = array() , $extendattr = array() , $goods_gallery = array()){
        $result = array(
            'status' => 1,
            'msg'    => '商品修改成功'
        );
        /*提取商品表(goods)需要的数据*/
        $xb = array(
           'id','cat_id','goods_name','goods_describe','goods_remark',
           'goods_price','goods_code','goods_sn','goods_number','goods_credit',
           'status','sort','cat_filter_id','deposit','safest','brand_id','province',
            'city','area','is_new','shop_cat','goods_model','yunfei','fwbz','sbcj'
        );
        foreach($xb as $v){
            $data_goods["$v"] = $data["$v"]?$data["$v"]:'';
        }
        if($data['goods_thumb']){
            $data_goods['goods_thumb'] = $data['goods_thumb'];
        }
        if($data['goods_img']){
            $data_goods['goods_img'] = $data['goods_img'];
        }
        /*验证数据*/
        $goods = D("Mall_goods");
        $rules = array(       
            array('id','/^[1-9]\d*$/','请选择商品id'),
            array('goods_name','require','必须输入商品名',self::EXISTS_VALIDATE),
            array('goods_price','require','请输入商品价格',self::EXISTS_VALIDATE),
            array('goods_price','/^[0-9]+(.[0-9]{1,2})?$/','请输入正确的价格'),
            array('cat_id','/^[1-9]\d*$/','请选择所属分类'),
            array('brand_id','/^[1-9]\d*$/','请选择商品品牌'),
            array('province','require','必须输入一级地区',self::EXISTS_VALIDATE),
            array('city','require','必须输入二级地区',self::EXISTS_VALIDATE)
        );
        if($goods->validate($rules)->create($data_goods) === false){
           $result = array(
             'status' => 0,
             'msg'    => $goods->getError()
           );
           return $result;
        } 
        if(!$this->checkExtendAttr($extendattr,'update')){
           $result = array(
             'status' => 0,
             'msg'    => '扩展属性有错误'
           );
           return $result;
        }
        $data_goods['update_time'] = date('Y-m-d H:i:s');//设置上架时间
        $r = M('Mall_goods')->save($data_goods);//修改数据到商品表
        if($r === false){
           $result = array(
              'status' => 0,
              'msg'    => '商品插入数据库失败'
           );
        }
        $goods_data = array(
            'goods_content' => $data['goods_content'],
            'goods_weight'  => $data['goods_weight'],
            'ensure'        => $data['ensure'],
            'watts'         => $data['watts'],
            'precise'       => $data['precise'],
            'measurement'   => $data['measurement'],
            'uses'          => $data['uses']
        );
        M('Mall_goods_data')->where(array('goods_id'=>$data_goods['id']))->save($goods_data);
        if($data_goods['goods_thumb'] && $data_goods['goods_img']){
        	$data = M('Goods')
	        	  ->where(array('id'=>$data_goods['id']))
	        	  ->field("goods_thumb,goods_img")
	        	  ->find();
        	if($data_goods['goods_thumb']){
        		unlink($data['goods_thumb']);
        	}
        	if($data_goods['goods_img']){
        		unlink($data['goods_img']);
        	}
        }
        if($goods_gallery['old']){
            $r = $this->goodsGallery($goods_gallery['old']);//商品相册修改
        }
        if($goods_gallery['new']){
            $r = $this->goodsGallery($goods_gallery['new'] , $data_goods['id']);//商品相册添加
        }
        $r = $this->goodsExtendAttr($extendattr); //商品扩展属性处理
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
      $imgs = M('Mall_goods')->where(array('id'=>$goods_id))->Field('goods_thumb,goods_img')->find();
      $r    = M('Mall_goods')->where(array('id'=>$goods_id))->delete();
	  if($r !== false){
	  	  /*先删除商品相册*/
	  	  $this->goodsGalleryDelete($goods_id , 2);
	      /*删除商品扩展属性*/
	      M('Mall_goods_extendattr')->where(array('goods_id'=>$goods_id))->delete();
	      /*删除商品data表数据*/
	      M('Mall_goods_data')->where(array('goods_id'=>$goods_id))->delete();
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
      $sql = "UPDATE tp_mall_goods".$sql_arr['status']." where id IN ($ids)";
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
   * 商品销售属性处理
   * @access public
   * @param  array $extendattr 商品销售属性数据
   * @param  array $type       处理类型 1添加 2修改 默认为1
   */ 
  public function goodsAttr($baseattr , $type = 1){
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
           $sql = "INSERT INTO `tp_mall_goods_extendattr` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
           $r   = M()->execute($sql); 
           if($r === false){
              return false;
           } 
      }else{
          /*修改扩展属性*/
          foreach($extendattr as $k => $v){
              $r = M('Mall_goods_extendattr')->where(array('goods_extendattr_id'=>$v['goods_extendattr_id']))->save(array(
                  'attr_value' => $v['attr_value']
              ));
          } 
      }         
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
           $sql = "INSERT INTO `tp_mall_goods_gallery` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
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
                $r = M('Mall_goods_gallery')->where(array('id'=>$v['id']))->save($save_data);
                if($r !== false){
                  unlink($v['oldgallery_img']);
                }
           }          
      }
  }

  /**
   * 删除商品相册
   * @access public
   * @param  int   $id 相册id||商品id(根据商品id批量删除)
   * @param  int   $type 1删除一个 2根据商品id批量删除
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
        $oldimg = M('Mall_goods_gallery')->where(array('goods_id'=>$id))->field('gallery_img')->select(); 
	    $r      = M('Mall_goods_gallery')->where(array('goods_id'=>$id))->delete();         	
     }else
     if($type == 1){
	  	 $oldimg = M('Mall_goods_gallery')->where(array('id'=>$id))->Field('gallery_img')->find();
		 $r      = M('Mall_goods_gallery')->where(array('id'=>$id))->delete($id);    	
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
   * 团购处理
   * @access protected
   * @param  int   $int    商品id    
   * @param  array $data_   团购数据    
   * @return array $result 执行结果
   */    
  public function goodsGroup($id , $data_){
      $id   = intval($id);
      $data = array(
          'goods_group_price' => $data_['goods_group_price']
      );
      if($data_['group_img']){
          $data['group_img'] = $data_['group_img'];
      }
      /*验证数据*/
      $model = D("Group_list");
      $rules = array(
            array('id','/^[1-9]\d*$/','请选择商品id'),
            array('goods_group_price','/^[0-9]+(.[0-9]{1,2})?$/','请输入正确的价格'),
      );
      if($model->validate($rules)->create($data) === false){
           $result = array(
             'status' => 0,
             'msg'    => $model->getError()
           );
           return $result;
      } 
      $r  = M('Group_list')->where(array('goods_id'=>$id))->find();
      if($r){
      	  $r = M('Group_list')->where(array('goods_id'=>$id))->save($data);   
      }else{
      	  $data['goods_id'] = $id;
      	  $r = M('Group_list')->add($data);   
      }  
      if($r === false){
      	$result = array(
           'status' => 1,
           'msg'    => '操作失败'
        );
      } 
      return  $result;   
  }

  /**
   * 检查扩展属性
   * @access protected
   * @param  array $extendattr    扩展属性     二维数组 array(array('属性id','属性值'))  
   * @return array $result 执行结果
   */  
  protected function checkExtendAttr($extendattr , $type = 'add'){
      $goods = D("Mall_goods");
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
    * 主页商品获取
    * @param array data需要验证的数据 
    * @return array 返回验证结果
    */
   public function getIndexGoods(){
        $categorys  = M("Mall_category")
                    ->where(array('status'=>'1'))
                    ->order('sort')
                    ->select();//获取商品分类
        $cats       = array();
        $catids_arr = array();
        foreach($categorys as $k=>$v){
            $cats[] = $v['id'];
            if($v['pid'] != 0){
                $catids_arr[$v['id']] = $v['pid'];
            }
        }
        $categorys = get_child($categorys);//商品树形结构
        /*获取首页推荐商品*/
        $goods_ids = M('Mall_goods_model')->where(array('id'=>1))->getField('goods_ids');
        //取出商品
        $where = array(
        	'cat_id' => array('in',implode(',',$cats)),
        	'id'     => array('in',$goods_ids)
        );
        $goods_ = M("Mall_goods")
                ->where($where)
                ->order('sort')
                ->select();
        foreach($goods_ as $k=>$v){
           $pid = $catids_arr[$v['cat_id']]; 
           $goods[$pid][] = $v; 
        }
        foreach ($categorys as $k => $v) {
           $categorys[$k]['goods'] = $goods[$v['id']];  
        }
        return $categorys;
   }

   /**
    * 获取商品扩展属性
    * @param int   $goods_id 商品id
    * @param array data      扩展属性
    */
   public function getGoodsExtendAttr($goods_id){
       $goods_id = intval($goods_id);
       $data     = M('Mall_goods_extendattr as a')
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
    $filter_extendattr=M('Mall_category')->where(array("id"=>$cat_id))->getField('filter_extendattr');
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
        $filter_extendattr = M('Mall_category')->where(array("id"=>$cat_id))->getField('filter_extendattr');
        $cat_extendattr_ = M('Attrbute as a')
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
          $goods_attr= M('Mall_goods_extendattr')
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