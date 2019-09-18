<?php
/**
 * 商品模块业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class MallGoodsModel extends Model{
   protected $tableName='Mall_goods';
    
  /**
   * 添加商品
   * @access public
   * @param  array $data_goods    商品基本信息 一维数组 
   * @param  array $goods_gallery 商品相册     二维数组 array(array('图片描述','上传路径'))  
   * @return array $result 执行结果
   */ 
  public function goodsAdd($data = array() , $goods_gallery = array()){
        $result = array(
            'status' => 1,
            'msg'    => '商品添加成功'
        );
	   	$data_goods = array(
	   	    'cat_id'        => $data['cat_id'],//商品分类
	   	    'goods_name'    => $data['goods_name'],//商品名称
	   	    'goods_price'   => $data['goods_price'],//商品价格
	   	    'brand_id'      => $data['brand_id'],//商品品牌
	   	    'goods_number'  => $data['goods_number'],//库存
	   	    'goods_thumb'   => $data['goods_thumb'],//商品缩略图
	   	    'goods_img'     => $data['goods_img'],//商品主图	   	   
	   	    'province'      => $data['province'],//省
	   	    'city'          => $data['city'],//市
	   	    'area'          => $data['area'],//区
	   	    'status'        => $data['status'],//商品状态
	   	    'sort'          => $data['sort'],//商品排序
	   	    'cat_filter_id' => $data['cat_filter_id'],	   	    
	   	    'sbcj'          => $data['sbcj'],//设备厂家
	   	    'goods_model'   => $data['goods_model']//设备型号	
	   	);
        $data_goods['goods_code']  = empty($data['goods_code'])?setnum(12):$data['goods_code'];//设置货号
        $data_goods['create_time'] = date('Y-m-d H:i:s');//设置添加时间 
        $data_goods['update_time'] = $data_goods['create_time'];//设置编辑时间
        $id = M('Mall_goods')->add($data_goods);//添加数据到商品表
        if($id !== false){                           
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
   * @param  array $goods_gallery 商品相册     二维数组 array(array('图片描述','上传路径'))   
   * @return array $result 执行结果
   */ 
  public function goodsUpdate($data = array() , $goods_gallery = array()){
        $result = array(
            'status' => 1,
            'msg'    => '商品修改成功'
        );
        $data_goods = array(
            'cat_id'        => $data['cat_id'],//商品分类
            'goods_name'    => $data['goods_name'],//商品名称
            'goods_price'   => $data['goods_price'],//商品价格
            'brand_id'      => $data['brand_id'],//商品品牌
            'goods_number'  => $data['goods_number'],//库存
            'province'      => $data['province'],//省
            'city'          => $data['city'],//市
            'area'          => $data['area'],//区
            'status'        => $data['status'],//商品状态
            'sort'          => $data['sort'],//商品排序
            'cat_filter_id' => $data['cat_filter_id'],
            'sbcj'          => $data['sbcj'],//设备厂家
            'goods_model'   => $data['goods_model'],//设备型号
            'sale_num'      => intval($data['sale_num']),//销售数量
            'comment_number'=> intval($data['comment_number'])//评论数量
        );
        if($data['goods_thumb']){
            $data_goods['goods_thumb'] = $data['goods_thumb'];
        }
        if($data['goods_img']){
            $data_goods['goods_img'] = $data['goods_img'];
        }
        $id        = $data['id'];
        $member_id = $data['member_id'];
        $data_goods['update_time'] = date('Y-m-d H:i:s');//设置上架时间
        $r = M('Mall_goods')->where(array('id'=>$id))->save($data_goods);//修改数据到商品表
        if(!$r){
           return array(
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
        $r = M('Mall_goods_data')->where(array('goods_id'=>$id))->save($goods_data);
        if($goods_gallery['old']){
            $r = $this->goodsGallery($goods_gallery['old']);//商品相册修改
        }
        if($goods_gallery['new']){
            $r = $this->goodsGallery($goods_gallery['new'] , $id);//商品相册添加
        }
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
   * 检测商品数据 合法性
   * @access public
   * @param  int   $goods_id 商品id
   * @return array $result 执行结果
   */
  public function checkGoodsData($data){
      /*验证数据*/
      $goods = D("Mall_goods");
      $rules = array(
          array('cat_id','/^[1-9]\d*$/','请选择所属分类',self::MUST_VALIDATE),
          array('goods_name','require','必须输入商品名',self::MUST_VALIDATE),
          array('sbcj','require','必须输入设备厂家',self::MUST_VALIDATE),
          array('goods_price','require','请输入商品价格',self::MUST_VALIDATE),
          array('goods_price','/^[0-9]+(.[0-9]{1,2})?$/','请输入正确的价格'),
          array('province','require','必须输入一级地区',self::MUST_VALIDATE),
          array('brand_id','/^[1-9]\d*$/','请选择商品品牌',self::MUST_VALIDATE),
          array('city','require','必须输入二级地区',self::MUST_VALIDATE)
      );
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
   * 商品批量修改排序
   * @access public
   * @param  array $data   排序的商品数据
   * @return array $result 执行结果
   */
  public function sortAllChange($data){
      $result = array(
          'status' => 1,
          'msg'    => '修改成功'
      );
      $sql_arr = array(
          'sort'   => " SET sort = CASE id"
      );
      $ids = array();
      foreach($data as $k => $v){
          $ids[] = $k;
          $v = intval($v);
          $sql_arr['sort'] .= " WHEN {$k} THEN '{$v}'";
      }
      $ids = implode(',' , $ids);
      $sql_arr['sort'] .= ' END';
      $sql = "UPDATE tp_mall_goods".$sql_arr['sort']." where id IN ($ids)";
      $r   = M()->execute($sql);
      if($r === false){
          $result = array(
              'status' => 0,
              'msg'   => '添加失败'
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
          $sql = "INSERT INTO `tp_mall_goods_baseattr` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
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

  
/******************************************预留*************************************************/
  
  /**
   * 检查扩展属性
   * @access protected
   * @param  array $extendattr    扩展属性     二维数组 array(array('属性id','属性值'))  
   * @return array $result 执行结果
   */  
  protected function checkExtendAttr($extendattr , $type = 'add'){
      $goods = D("Mall_goods_extendattr");
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
  

	/*获取商品销售属性*/
	public function get_baseattr_add($cat_id){
		//取出该商品的销售属性特征量组合
		$filter_attr=M('Category')->where(array("id"=>$cat_id))->getField('filter_attr');
		$cat_attr= M('Attrbute as a')
				   ->join('tp_attrbute_value as t on a.attr_id=t.attr_id')
				   ->field('t.attr_value,a.attr_id,a.attr_name,a.type,t.id')
				   ->where(array('a.attr_id'=>array('in',$filter_attr)))
				   ->select();
		$goods_attrs=array();
		foreach($cat_attr as $v){
			$goods_attrs[$v['attr_id']]['attr_id']=$v['attr_id'];
			$goods_attrs[$v['attr_id']]['attr_name']=$v['attr_name'];
			$goods_attrs[$v['attr_id']]['type']=$v['type'];
			$goods_attrs[$v['attr_id']]['attr_value'][$v['attr_value']]['value']=$v['attr_value'];
			$goods_attrs[$v['attr_id']]['attr_value'][$v['attr_value']]['id']=$v['id'];
		}
		return $goods_attrs;
	}
	
	/*获取商品扩展属性*/
	public function get_extendattr_add($cat_id){
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
	
    /*获取商品销售属性*/
    public function get_baseattr($goods_id,$cat_id){
          //取出该商品的销售属性特征量组合
          $filter_attr=M('Category')->where(array("id"=>$cat_id))->getField('filter_attr');
          $cat_attr= M('Attrbute as a')
                     ->join('tp_attrbute_value as t on a.attr_id=t.attr_id')
                     ->field('t.attr_value,a.attr_id,a.attr_name,a.type')
                     ->where(array('a.attr_id'=>array('in',$filter_attr)))
                     ->select();

          /*获取商品已有的销售属性*/
          $goods_attr_= M('Goods_baseattr')
                        ->where(array('goods_id'=>$goods_id))
                        ->select();
          foreach($goods_attr_ as $v){
              $goods_attr[]=$v['attr_value'];
              $goods_attr_[$v['attr_value']]['goods_attr_id']=$v['goods_attr_id'];  
              $goods_attr_[$v['attr_value']]['thumb']=$v['thumb'];  
          }             
          $goods_attrs=array();
          foreach($cat_attr as $v){
              $goods_attrs[$v['attr_id']]['attr_id']=$v['attr_id'];
              $goods_attrs[$v['attr_id']]['attr_name']=$v['attr_name'];
              $goods_attrs[$v['attr_id']]['type']=$v['type'];
              $goods_attrs[$v['attr_id']]['attr_value'][$v['attr_value']]['value']=$v['attr_value'];
              $goods_attrs[$v['attr_id']]['attr_value'][$v['attr_value']]['id']=$v['id'];
              $goods_attrs[$v['attr_id']]['attr_value'][$v['attr_value']]['thumb']=$goods_attr_[$v['attr_value']]['thumb'];
              $goods_attrs[$v['attr_id']]['attr_value'][$v['attr_value']]['goods_attr_id']=$goods_attr_[$v['attr_value']]['goods_attr_id'];
              if(in_array($v['attr_value'],$goods_attr)){
                  $goods_attrs[$v['attr_id']]['attr_value'][$v['attr_value']]['check']="checked='chedked'";
              }          
          }   
          return $goods_attrs; 
    }
      
    /*获取商品扩展属性*/
    public function get_extendattr($goods_id,$cat_id){
          $filter_extendattr=M('Mall_category')->where(array("id"=>$cat_id))->getField('filter_extendattr');
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

?>