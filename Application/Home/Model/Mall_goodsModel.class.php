<?php
/**
 * 商城商品模块业务逻辑
 * @author 幸福无期
 * @email 597089187@qq.com
 */
namespace Home\Model;
use Think\Model;
class Mall_goodsModel extends Model{ 
  /**
   * 获取首页商品 和商品分类进行关联
   * @access public
   * @param  array $category  商品分类     二维数组 
   * @param  array $goods     商品数据     二维数组
   * @param  array $goods     所有店铺信息 二维数组    
   * @return array $result 执行结果
   */ 
    public function getIndexGoods($categorys = array() , $goods = array() , $shop_data = array()){ 
        /*去掉四级分类*/ 
        foreach($categorys as $k => $v){
            if($v['level'] != '4'){
                $temp_categorys[$v['id']] = $v;   
            }
            $categorys_[$v['id']] = $v;
        }
        /*商品和店铺关联*/
        foreach($goods as $k => $v){
        	  /*获取商品所属店铺的域名*/
        	  if($shop_data[$v['member_id']]['status'] != '1'){
                continue;
            } 
            $v['domain'] = $shop_data[$v['member_id']]['domain'] ? $shop_data[$v['member_id']]['domain'] : $v['member_id'];
            //获取分类的顶级id
            $top_id = D('MallCategory')->getTopCategory($v['cat_id'] , $categorys_);
            $temp_goods[$top_id][] = $v;                 	
        };        
        //获取子集树
        $categorys = get_child($temp_categorys , 'pid' , 2);
        foreach($categorys as $k => $v){
           $categorys[$k]['goods'] =  $temp_goods[$v['id']];
        }
        return $categorys;
    }   

  /**
   * 获取商户商品 并且获取商品下的sku 采用分页
   * @access public
   * @param  array $limit 分页数据 
   * @return array 结果
   */ 
    public function getGoodsAndSku($seller_id , $limit = array(0 , 10)){
        /*先获取商家的商品信息*/
        $goods = M('Mall_goods')
              ->order('id desc')
               ->where(array('member_id'=>$seller_id))
               ->limit($limit[0],$limit[1])
               ->select();
        //总计数据条数
	      $count  = M('Mall_goods')->where(array('member_id'=>$seller_id))->count();
	      /*在获取商品下的的所有的sku组合信息*/
	      $goods_id = array_column($goods , 'id');
	      $goods_id = implode(',' , $goods_id);
	      $sku  = M('Sku')->where(array('goods_id'=>array('in',$goods_id)))->select();
	      /*获取商品拥有的sku属性*/
	      $attr = M('Mall_goods_baseattr')
		        ->where(array('goods_id'=>array('in',$goods_id)))
		        ->field('attr_id,goods_id,attr_name')
		        ->select();
        foreach($sku as $k => $v){
            $sku[$k]['sku_value'] = unserialize($v['sku_value']);
        }
        $temp = array();
        foreach($attr as $k => $v){
            $item = implode(',' , $v);          
            if($temp[$item]){
                unset($attr[$k]);
                continue;
            }
            $temp[$item] = true;
        }        
	      return array(
            'status'    => 1,
            'msg'       => 'ok',
            'totalRows' => $count,
            'data'      => array(
                'goods_data' => $goods,
                'sku'        => $sku,
                'attr'       => array_values($attr)
            )
	      );
    }

  /**
   * 根据sku编码获取商品
   * @access public
   * @param  array $limit 分页数据 
   * @return array 结果
   */     
    public function getGoodsBySearch($seller_id , $search = array() , $limit = array(0 , 10)){
        $condition = array('seller_id' => $seller_id);
        if($search['sku_code']){
            $condition['sku_code'] = array('like',"%$sku_code%");
        }        
        $sku  = M('Sku')->where($condition)->select();
        if(empty($sku)){
            return array(
                'status'    => 1,
                'msg'       => 'ok',
                'totalRows' => 0,
                'data'      => array()
            );
        }else{
            $goods_ids = array_unquie(array_column($sku , 'goods_id'));
            $goods_ids = implode(',' , $goods_ids);          
        }
        /*获取商品*/      
        $condition = array('seller_id' => $seller_id);
        if(issset($goods_ids)){
            $condition['id'] = array('in',$goods_ids);
        }          
        if($search['cat_id']){
            $condition['cat_id'] = $search['cat_id'];
        }  
        if($search['goods_name']){
            $condition['goods_name'] = array('like',"%{$search['goods_name']}%");
        }             
        $goods = M('Mall_goods')->where($condition)->limit($limit[0],$limit[1])->order('id desc')->select();
        $goods_ids = array_unquie(array_column($goods , 'id'));
        $goods_ids = implode(',' , $goods_ids);
        /*获取商品拥有的sku属性*/
        $attr = M('Mall_goods_baseattr')
            ->where(array('goods_id'=>array('in',$goods_ids)))
            ->field('attr_id,goods_id,attr_name')
            ->select();
        foreach($sku as $k => $v){
            $sku[$k]['sku_value'] = unserialize($v['sku_value']);
        }
        $temp = array();
        foreach($attr as $k => $v){
            $item = implode(',' , $v);          
            if($temp[$item]){
                unset($attr[$k]);
                continue;
            }
            $temp[$item] = true;
        } 
        return array(
            'status'    => 1,
            'msg'       => 'ok',
            'totalRows' => $count,
            'data'      => array(
                'goods_data' => $goods,
                'sku'        => $sku,
                'attr'       => array_values($attr)
            )
        );        
    }

  /**
   * 添加商城商品
   * @access public
   * @param  array $data    商品基本信息 一维数组 
   * @param  array $goods_gallery 商品相册     二维数组 array(array('图片描述','上传路径'))  
   * @return array $result 执行结果
   */ 
    public function goodsAdd($data = array() , $goods_gallery = array()){
	   	  $data_goods = array(
	   		    'member_id'     => '',//卖家id
            'cat_id'        => '',//商品分类
            'goods_name'    => '',//商品名称
            'goods_price'   => '',//商品价格
            'brand_id'      => '',//商品品牌
            'goods_number'  => '',//库存
            'goods_thumb'   => '',//商品缩略图
            'goods_img'     => '',//商品主图
            'shop_cat'      => '',//所属店铺分类
            'goods_model'   => '',//商品型号
            'templet_id'	  => '', //运费模板id
            'sku_code'      => '',
            'term'          => '',
            'unit_unit'     => '',
            'unit'          => '',
            'unit_value'    => '',
            'goods_unit_value' => '',
        );
        $data_goods  = array_intersect_key($data , $data_goods); //获取键的交集 
        $data_goods['status']      = 1;
        $data_goods['goods_code']  = empty($data['goods_code'])?setnum(12):$data['goods_code'];//设置货号
        $data_goods['create_time'] = date('Y-m-d H:i:s');//设置上架时间  
        $data_goods['update_time'] = $data_goods['create_time'];//设置上架时间
        $id = M('Mall_goods')->add($data_goods);//添加数据到商品表	
        if($id !== false){ 
            /*添加商品相册*/
            if($goods_gallery){
                $r = $this->goodsGallery($goods_gallery , $id);                        
            } 
            $goods_data = array(
                'goods_id'      => $id,
                'goods_content' => $data['goods_content'],
                'special'       => $data['special'],//产品特性
                'spec'          => $data['spec'],//规格
                'uses'          => $data['uses'] //用途
            );
            M('Mall_goods_data')->add($goods_data);       
        }else{
            return array(
                'status' => 0,
                'msg'    => '商品添加失败'
            );
        }
        return array(
            'status'   => 1,
            'msg'      => '商品添加成功',
            'goods_id' => $id
        );
  }

  /**
   * 修改商品
   * @access public
   * @param  array $data_goods    商品基本信息 一维数组 
   * @param  array $attr          筛选属性     一维数组 array('销售属性id'=>'属性值id')
   * @param  array $goods_gallery 商品相册     二维数组 array(array('图片描述','上传路径'))   
   * @param  array $rent          租期         二维数组 
   * @return array $result 执行结果
   */ 
    public function goodsUpdate($data = array() , $goods_gallery = array()){
        $data_goods = array(
	   		    'member_id'     => '',//卖家id
            'cat_id'        => '',//商品分类
            'goods_name'    => '',//商品名称
            'goods_price'   => '',//商品价格
            'brand_id'      => '',//商品品牌
            'goods_number'  => '',//库存
            'goods_thumb'   => '',//商品缩略图
            'goods_img'     => '',//商品主图
            'shop_cat'      => '',//所属店铺分类
            'goods_model'   => '',//商品型号
            'templet_id'	=> '', //运费模板id
            'goods_unit_value' => '',
            'term'          => '',
            'unit_unit'     => '',
            'unit_value'    => ''            
        );
        $data_goods  = array_intersect_key($data , $data_goods); //获取键的交集  
        $id        = $data['id'];
        $member_id = $data['member_id'];
        $data_goods['update_time'] = date('Y-m-d H:i:s');//设置上架时间
        $r = M('Mall_goods')->where(array('id'=>$id,'member_id'=>$member_id))->save($data_goods);//修改数据到商品表
        if($r === false){
           return array(
              'status' => 0,
              'msg'    => '商品插入数据库失败'
           );           
        }
        $goods_data = array(
            'goods_content' => $data['goods_content'],
            'special'       => $data['special'],//产品特性
            'spec'          => $data['spec'],//规格
            'uses'          => $data['uses'] //用途
        );
        M('Mall_goods_data')->where(array('goods_id'=>$id))->save($goods_data);
        if($goods_gallery['old']){
            $r = $this->goodsGallery($goods_gallery['old']);//商品相册修改
        }
        if($goods_gallery['new']){
            $r = $this->goodsGallery($goods_gallery['new'] , $id);//商品相册添加
        }
        return array(
            'status' => 1,
            'msg'    => '商品修改成功'
        );
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
	      /*删除商品属性*/
	      M('Mall_goods_baseattr')->where(array('goods_id'=>$goods_id))->delete();
	      /*删除sku*/
	      M('Sku')->where(array('goods_id'=>$goods_id))->delete();
	      /*删除sku_value*/
	      M('Sku_value')->where(array('goods_id'=>$goods_id))->delete();
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
   * 检测商品数据 合法性
   * @access public
   * @param  int   $goods_id 商品id
   * @return array $result 执行结果
   */
    public function checkGoodsData($data , $type = 1){
    	  if($type == 2){
        	  if(intval($data['id']) == 0){
        		    return array('status' => '0' , 'msg' => 'id错误');
        	  }
        }
        if($data['templet_id']){
	          $templet_data = M('Shipping_templet')->where(array('id'=>$data['templet_id']))->find();
	          if(empty($templet_data)){
		            return array(
		               'status' => 0,
		               'msg'    => '运费模板不存在'
		            );
	          } 
        }    
    	  $validate_model = $type == 1 ? self::MUST_VALIDATE : self::EXISTS_VALIDATE;
        /*验证数据*/
        $goods = D("Mall_goods");
        $rules = array(
            array('cat_id','/^[1-9]\d*$/','请选择所属分类',$validate_model),
            array('goods_name','require','必须输入商品名',$validate_model),
            //array('goods_number','/^[1-9]\d*$/','必须输入商品数量',self::MUST_VALIDATE),
            //array('sbcj','require','必须输入设备厂家',self::MUST_VALIDATE),
            //array('goods_price','require','请输入商品价格',self::MUST_VALIDATE),
            //array('goods_price','/^[0-9]+(.[0-9]{1,2})?$/','请输入正确的价格'),
            array('brand_id','/^[1-9]\d*$/','请选择商品品牌'),           
        );
        if($templet_data['free_status'] == 1){        	
            $rules[] = array('goods_unit_value','/^[0-9]+(.[0-9]{1,2})?$/',"选择正确的商品单位值{$templet_data['free_status']}",$validate_model);
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


/***************************************商品属性************************************************/

   /**
    * 检查商品筛选属性
    * @access public
    * @param  array $attr   筛选属性     二维数组 array('attr_id'=>array('值'...'值'))
    * @return array $result 执行结果
    */
    public function checkAttr($cat_id , $attr){
    	$cat_id = intval($cat_id);
        if(!is_array($attr) || count($attr) <= 0 || $cat_id != 0){
           return array(
               'status' => 1
           );
        }
        $attr_ids = array_keys($attr);
        $cat_attr = M('Attrbute')
		         ->where(array('attr_id' => array('in' , $attr_ids) , 'cat_id' => $cat_id))
		         ->field('attr_id')
		         ->select();
        $attr_ids_ = array_column($cat_attr, 'attr_id');
        if(count(array_diff($attr_ids, $attr_ids_)) > 0){
           return array(
               'status' => 0,
               'msg'    => '筛选属性错误'
           );
        }
        return array(
           'status' => 1
        );
    }

   /**
    * 分拣出商品属性的增加 删除数据
    * @access public
    * @param  array $old_attr  商品已经拥有的原始数据
    * @param  int   $new_attr  商品新的属性数据
    */    
    public function getAttrHandleType($old_attr , $new_attr){
        $attr_value = array();
        foreach($old_attr as $v){
            foreach($v as $v1){
                $attr_value[$v1] = $v1;
            }
        }
        $temp = array();
        foreach($new_attr as $k => $v){
            foreach($v as $k1 => $v1){
                if(isset($attr_value[$v1['attr_value_id']])){
                    unset($new_attr[$k][$k1]);
                } 
                $temp[$v1['attr_value_id']] = $v1['attr_value_id'];
            }
        }
        $delete_attr = array_diff_key($attr_value , $temp);
        return array(
            'delete' => array_keys($delete_attr),
            'add'    => $new_attr
        );
    }

   /**
    * 商品筛选属性添加
    * @access public
    * @param  array $attr      商品筛选属性数据  二维数组 array('attr_id'=>array('值'...'值'))
    * @param  int   $goods_id  商品id
    * @param  int   $seller_id 卖家id  
    */
    public function goodsAttrAdd($attr = array() , $goods_id , $seller_id){
    	  $goods_id  = intval($goods_id);
    	  $seller_id = intval($seller_id);
        if(!is_array($attr) || count($attr) <= 0 || $goods_id == 0 || $seller_id == 0){
           return true;    
        }
        /*添加筛选属性*/
        $values = array();
        $fields = array('`attr_id`','`attr_name`','`attr_value`','`attr_value_id`','`goods_id`','`seller_id`');
        /*获取属性名称*/
        $attr_id   = array_keys($attr);
        $attr_name = D('Attrbute')->getAttrDataById($attr_id , 'attr_id,attr_name');
        $attr_name = array_column($attr_name , 'attr_name' , 'attr_id');
        foreach($attr as $k => $v){
            foreach($v as $k1 => $v1){
                $arr = array(
                   'attr_id'       => $k,
                   'attr_name'     => $attr_name[$k],
                   'attr_value'    => $v1['attr_value'],
                   'attr_value_id' => intval($v1['attr_value_id']),
                   'goods_id'      => $goods_id,
                   'seller_id'     => $seller_id
                );
                $values[] = "('" . implode("','",$arr) . "')";
           }
        }
        if(count($values) <= 0){
            return true;
        }
        $sql = "INSERT INTO `tp_mall_goods_baseattr` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
        $r   = M()->execute($sql);
        return $r;
    }   

   /**
    * 商品筛选属性删除 根据attr_value_id
    * @access public
    * @param  array $attr_value_id 商品属性值id array(id1,...)
    * @param  int   $seller_id     商家id
    */
    public function attrDeleteByAttrValueId($attr_value_id = array() , $goods_id , $seller_id){
    	  if(!is_array($attr_value_id) || count($attr_value_id) <= 0){
            return true;
    	  }
        $attr_value_id = implode(',' , $attr_value_id);
        $condition = array(
            'seller_id' => $seller_id,
            'goods_id'  => $goods_id,
            'attr_value_id' => array('in' , $attr_value_id)
        );
    	  $r = M('Mall_goods_baseattr')->where($condition)->delete();
	   	  return true;
    }

   /**
    * 根据商品id获取商品信息
    * @access public
    * @param  int   $id 商品id
    */
    public function getGoodsDataById($id , $field = ''){
       	$id    = is_array($id) ? $id : array($id);
     	  if(empty($id)){
            return array();
     	  }
       	$id    = implode(',', $id);
       	$model = M('Mall_goods')->where(array('id'=>array('in',$id)));
       	$field && ($model = $model->field($field));	
       	$data  = $model->select();
       	return $data;
    }

/******************************************sku*************************************************/

   /**
    * 检查商品sku
    * @access public
    * @param  array $sku    sku属性  多维数组  
      array(
	      array(
	          'price'     => 100,
	          'sku_value' => array(
		          '2129'      => '白色',
		          '2130'      => '聚丙烯1',
		          '2131'      => '空箱',
		          '2132'      => '仅为配件'
	          )
	      )
      )
    * @param  array $attr   筛选属性     二维数组 array('2129'=>array('值',...'值'))
    * @param  array $type_id  是否有属性 1有 2无
    * @return array $result 执行结果
    */
    public function checkSku($sku , $attr , $type = 1){
        foreach($sku as $v){
       	    if(!preg_match('/^([0-9]+.[0-9]{1,2}?$)|(^[1-9]\d*$)/', trim($v['price']))){
       	        return array(
	                   'status' => 0,
	                   'msg'    => '价格错误'
	              );	 
       	    }
       	    if(!isset($v['unit'])){
       	        return array(
	                  'status' => 0,
	                  'msg'    => '单位错误'
	              );	 
       	    }
       	    if(!isset($v['unit_value'])){
       	        return array(
	                  'status' => 0,
	                  'msg'    => '单位值错误'
	              );	 
       	    }
       	    if(!$v['sku_value'] || count($v['sku_value']) <= 0){
                continue;
       	    }
            if($type == 1 && count(array_diff_key($v['sku_value'] , $attr)) > 0){
	              return array(
	                  'status' => 0,
	                  'msg'    => 'sku属性错误'
	              );
            }
        }
        return array(
           'status' => 1
        );
    }   

   /**
    * 检查商品sku
    * @access public
    * @param  array $sku    sku属性  多维数组  
      array(
	      array(
	          'price'     => 100,
	          'sku_value' => array(
		          '2129'      => '白色',
		          '2130'      => '聚丙烯1',
		          '2131'      => '空箱',
		          '2132'      => '仅为配件'
	          )
	      )
      )
    * @param  array $attr   筛选属性     二维数组 array('2129'=>array('值',...'值'))
    * @param  array $type_id  是否有属性 1有 2无
    * @return array $result 执行结果
    */
    public function checkUpdateSku($sku , $attr , $type = 1){
        foreach($sku as $v){
       	    if(isset($v['price']) && !preg_match('/^([0-9]+.[0-9]{1,2}?$)|(^[1-9]\d*$)/', trim($v['price']))){
       	        return array(
	               'status' => 0,
	               'msg'    => '价格错误'
	              );	 
       	    }
       	    /*if(isset($v['number']) && intval($v['number'] == 0)){
       	        return array(
	               'status' => 0,
	               'msg'    => '数量错误'
	            );	 
       	    }*/
       	    if(!$v['sku_value'] || count($v['sku_value']) <= 0){
                continue;
       	    }
            if($type == 1 && count(array_diff_key($v['sku_value'] , $attr)) > 0){
	              return array(
	                  'status' => 0,
	                  'msg'    => 'sku属性错误'
	              );
            }
        }
        return array(
           'status' => 1
        );
    }   

   /**
    * 商品筛选属性sku组合添加
    * @access public
    * @param  array $attr     商品筛选属性组合数据  二维数组 
    * @param  array $goods_id 商品id
    * @param  array $type     是否有属性 1有 2无
    */
    public function goodsSkuAdd($sku , $goods_id , $seller_id ,$type = 1){
        if(!is_array($sku) || count($sku) <= 0){
           return true;    
        }
        foreach($sku as $v){
            $sku_data = array(
           	   'goods_id'  => $goods_id,
               'number'    => intval($v['number']),
               'price'     => $v['price'],
               'sku_code'  => $v['sku_code'],
               'sku_value' => serialize($v['sku_value']),
               'term'      => intval($v['term']),
               'unit'      => $v['unit'],
               'unit_unit' => $v['unit_unit'],
               'unit_value'=> $v['unit_value'],
               'seller_id' => $seller_id 
            );
            $sku_id = M('Sku')->add($sku_data);
            if($type == 2 || $sku_id === false){
                continue;
            }
            foreach($v['sku_value'] as $k1 => $v1){
	            $sku_value_data = array(
	           	    'goods_id'       => $goods_id,
	                'sku_id'         => $sku_id,
	                'attr_id'        => $k1,
	                'attr_value_id'  => intval($v1['attr_value_id']),  
	                'attr_value'     => $v1['attr_value'],  
	            );
	            M('Sku_value')->add($sku_value_data);  
            }

        }
        return true; 
    }  


   /**
    * 商品筛选属性sku组合修改
    * @access public
    * @param  array $attr     商品筛选属性组合数据  二维数组 array(array('id'=>'','goods_id'=>'','price'=>....),....)
    * @param  array $goods_id 商品id
    */
    public function goodsSkuUpdate($sku , $goods_id , $type = 1){
        if(!is_array($sku) || count($sku) <= 0){
           return true;    
        }
        /*修改组合属性*/
        foreach($sku as $k => $v){
        	  $save = array();
        	  isset($v['price'])      && ($save['price']      = $v['price']);
        	  isset($v['number'])     && ($save['number']     = intval($v['number']));
        	  isset($v['term'])       && ($save['term']       = intval($v['term']));
        	  isset($v['unit'])       && ($save['unit']       = $v['unit']);
            isset($v['unit_unit'])  && ($save['unit_unit']  = $v['unit_unit']);
            isset($v['unit_value']) && ($save['unit_value'] = $v['unit_value']);
       	    M('Sku')->where(array('sku_id'=>$v['sku_id'] , 'goods_id' => $goods_id))->save($save);   
        }
    } 

   /**
    * 删除商品的sku
    * @access  public
    * @param   int       $seller_id 卖家id  
    * @param   int|array $sku_id    sku_id    
    * @return         
    */ 
    public function deleteSku($seller_id , $sku_id , $goods_id){
        $sku_id = is_array($sku_id) ? $sku_id : array($sku_id);
        $sku_id = implode(',' , $sku_id);
        $r = M('Sku')->where(array('seller_id'=>$id,'sku_id'=>array('in',$sku_id)))->delete();
        if(!$r){
            return;
        }
        $sku_value = M('Sku_value')
            ->where(array('sku_id'=>array('in',$sku_id)))
            ->field('sku_id,attr_id,attr_value,attr_value_id,goods_id')
            ->select();
        $sku_value = array_column($sku_value , 'attr_value_id');     
        $r = M('Sku_value')->where(array('sku_id'=>array('in',$sku_id)))->delete();  
        $all_value = M('Sku_value')
            ->where(array('goods_id' => $goods_id))
            ->field('attr_value_id')
            ->select();       
        //不存在的商品选择的属性
        $all_value = array_column($all_value, 'attr_value_id'); 
        $delete    = array_diff($sku_value , $all_value);
        $this->attrDeleteByAttrValueId($delete , $goods_id , $seller_id); 
    }
    
   /**
    * 删除商品的sku 根据商品拥有的属性
    * @access  public
    * @param   int       $seller_id  卖家id  
    * @param   int|array $goods_id   商品id    
    * @return         
    */     
    public function deleteSkuByGoodsAttr($seller_id , $goods_id){
        $data = M('Mall_goods_baseattr')
            ->where(array('goods_id'=>$goods_id , 'seller_id'=>$seller_id))
            ->field('attr_id,attr_value_id')
            ->select();
        if(empty($data)){
            return;
        }
        /*先获取商品属性表里面存在 在sku表里面却不存在的数据 这些数据需要删除*/
        $attr_value_id = array_column($data , 'attr_value_id');   
        $attr_value_id = implode(',' , $attr_value_id);
        $condition     = array('goods_id'=>$goods_id,'attr_value_id'=>array('not in',$attr_value_id));
        $sku_value     = M('Sku_value')->where($condition)->field("id,sku_id,attr_value_id")->select();
        if(empty($sku_value)){
            return;
        }
        $sku_id        = array_column($sku_value , 'sku_id'); 
        $sku_value_id  = array_column($sku_value , 'id'); 
        //删除sku表
        M('Sku')->where(array('sku_id'=>implode(',' , $sku_id)))->delete();
        //删除sku值表
        M('Sku_value')->where(array('id'=>implode(',' , $sku_value_id)))->delete();
    }

   /**
    * 获取商品下的的sku
    * @access  public
    * @param   int $goods_id 商品id  
    * @return         
    */   
    public function getGoodsSku($goods_id){
        $goods_id   = intval($goods_id);
    	  if(!$goods_id){
            return array();
    	  }
    	  //获取商品sku
        $goods_sku  = M('Sku')->where(array('goods_id'=>$goods_id))->select();
    	  if(count($goods_sku) <= 0){
            return array();
    	  }    
    	  //获取商品sku组合值    
        $attr_temp  = M('Sku_value')->where(array('goods_id'=>$goods_id))->select();
        if(count($attr_temp) <= 0){
            return $goods_sku;
    	  }    
        foreach($attr_temp as $v){
            $attr_value[$v['sku_id']][] = $v;
    	  }
    	  //关联
    	  foreach($goods_sku as $k => $v){
            $goods_sku[$k]['sku_value'] = $attr_value[$v['sku_id']];
    	  }
    	  return $goods_sku;
    }
}