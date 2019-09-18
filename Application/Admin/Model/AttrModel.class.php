<?php
/**
 * 商品属性模块业务逻辑
 * @author 幸福无期
 * @email  597089187@qq.com
 */

namespace Admin\Model;
use Think\Model;
class AttrModel extends Model{
  protected $tableName = 'Attrbute'; 
  /**
   * 添加商品属性
   * @access public
   * @param  array $data   商品属性 一维数组   
   * @return array $result 执行结果
   */ 
    public function attrAdd($data_ = array()){ 	    
        $data = array(
        	'cat_id'     => intval($data_['cat_id']),
          'attr_name'  => $data_['attr_name'],
          'attr_value' => is_array($data_['attr_value']) ? implode("\\r\\n" , $data_['attr_value']) : $data_['attr_value']
        );
      /*验证数据*/
      $model = D("Attrbute");
      $rules = array(
        	array('cat_id','/^[1-9]\d*$/','请选择所属分类'),
          array('attr_name','require','必须输入属性名称',self::EXISTS_VALIDATE)
      );
      if($model->validate($rules)->create($data) === false){
          return array(
              'status' => 0,
              'msg'    => $model->getError()
          );
      }
		  $r = M('Attrbute')->add($data);
		  if($r !== false){
			    $fields = array(
	            '`attr_id`',
	            '`attr_value`',
	            '`cat_id`'
	        );
	        $attr_value = !is_array($data_['attr_value']) ? explode("\\r\\n" , $data_['attr_value']) : $data_['attr_value'];
	        foreach($attr_value as $v){
	        	$v = array($r , addslashes($v) , $data['cat_id']);
	            $values[] = "('" . implode("','",$v) . "')";
	        }
	        $sql = "INSERT INTO `tp_attrbute_value` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
	        M()->execute($sql);
			    return(array('status'=>'1','msg'=>'ok','id'=>$r));
		  }else{
			    return(array('status'=>'0','msg'=>'操作失败'));
		  }
  }

  /**
   * 修改商品属性
   * @access public 
   * @param  array $data     商品属性 一维数组   
   * @return array $result   执行结果
   */ 
  public function attrUpdate($data_ = array()){
  	    $attr_id   = intval($data_['attr_id']);
  	    $old_attr  = $data_['old_data'];
  	    $new_attr  = $data_['new_data'];
  	    $attr_name = $data_['attr_name'];
  	    if(!$attr_id){
            return(array('status' => '0','msg' => 'attr_id错误')); 
  	    }
  	    $cat_id    = M('Attrbute')->where(array('attr_id'=>$attr_id))->getField('cat_id');
  	    $attr_save = array();
  	    isset($attr_name) && ($attr_save['attr_name']  = $attr_name);
  	    /*修改属性值*/
  	    if(isset($old_attr)){
	        foreach($old_attr as $v){
                $r = M('attrbute_value')
                ->where(array('attr_id'=>$attr_id,'attr_value_id'=>$v['attr_value_id']))
                ->save(array('attr_value'=>$v['attr_value']));
	        }
        }      
        /*增加属性值*/
        if(isset($new_attr)){
	        foreach($new_attr as $v){
                $r = M('attrbute_value')->add(array('cat_id'=>$cat_id,'attr_id'=>$attr_id,'attr_value'=>$v));
	        }
        }
        if(isset($new_attr) || isset($old_attr)){
        	$value = M('attrbute_value')
		        ->where(array('attr_id'=>$attr_id))
		        ->field('attr_value')
		        ->select();
	        $value = array_column($value , 'attr_value');
	        $attr_save['attr_value'] = implode("\\r\\n" , $value);
        }
	    if(count($attr_save) > 0){
            $r  = M('Attrbute')
	            ->where(array('cat_id'=>$cat_id,'attr_id'=>$attr_id))
	            ->save($attr_save);
        }  
        if($r !== false){
			return(array('status'=>'1','msg'=>'ok','id'=>$r));
		}else{
			return(array('status'=>'0','msg'=>'操作失败'));
		} 
    }

  /**
   * 删除属性
   * @access public
   * @param  array $id     属性id
   * @return array $result 执行结果
   */
    public function attrDelete($id){
        $id = intval($id);
        if(!$id){
          return array(
              'status' => 0,
              'msg'    => 'id错误'
          );
        }
        $r = M('Attrbute')->where(array('attr_id'=>$id))->delete();
        if($r === false){
            return array(
                'status' => 0,
                'msg'    => '删除失败'
            );
        }
        M('Attrbute_value')->where(array('attr_id'=>$id))->delete();
        return array(
            'status' => 1,
            'msg'    => '删除成功'
        );
    }

  /**
   * 删除属性值
   * @access public
   * @param  array $id     属性值id
   * @return array $result 执行结果
   */
    public function attrValueDelete($id){
        $id = intval($id);
        if(!$id){
          return array(
              'status' => 0,
              'msg'    => 'id错误'
          );
        }
        $r = M('Attrbute_value')->where(array('attr_value_id'=>$id))->delete();
        if($r === false){
            return array(
                'status' => 0,
                'msg'    => '删除失败'
            );
        }
        return array(
            'status' => 1,
            'msg'    => '删除成功'
        );
    } 

  /**
   * 同步属性到下一级
   * @access public
   * @param  int   $cat_id 分类id
   * @return array $result 执行结果
   */
    public function updateAttrToNext($cat_id){
        //$cat_id  = 2941;
        if(!$cat_id){
            return array('status' => 0 , 'msg' => '分类id不能为空');
        }
        /*获取下一级所有子类*/
        $cat_ids = M('Mall_category')
                 ->where(array('pid'=>$cat_id))
                 ->field("id")
                 ->select();         
        if(count($cat_ids) <= 0){
           return array('status' => 0 , 'msg' => '无子类');
        }
        /*获取当前分类的属性*/
        $attr_temp = M('Attrbute as a')
            ->join('tp_attrbute_value as b on a.attr_id=b.attr_id' , 'left')
            ->field('a.attr_name,a.attr_id,b.attr_value')
            ->where(array('a.cat_id'=>$cat_id))
            ->select();
        foreach($attr_temp as $v){
        	if(!isset($attr[$v['attr_id']])){
                $attr[$v['attr_id']] = $v;
                $attr[$v['attr_id']]['attr_value'] = array($v['attr_value']);
                continue;
        	}
            $attr[$v['attr_id']]['attr_value'][] = $v['attr_value'];
        }
        if(count($attr) <= 0){
            return array('status' => 0 , 'msg' => '当前分类暂未添加属性');
        }  
        /*获取属性值*/
        $attr = array_all_column($attr, 'attr_name');
        /*获取子类属性*/
        $cat_ids    = array_column($cat_ids, 'id');
        $child_attr = array();
        $child_temp = M('Attrbute')
              ->where(array('cat_id'=>array('in',implode(',' , $cat_ids))))
              ->field('attr_id,attr_name,cat_id')
              ->select();
        if(empty($child_temp)){
            $child_attr = array_flip($cat_ids);
            foreach($child_attr as $k => $v){
                $child_attr[$k] = array();
            } 
        }else{
            foreach($child_temp as $v){
                $child_attr[$v['cat_id']][$v['attr_name']] = $v;
            }    
            foreach($cat_ids as $v){
                if(isset($child_attr[$v])){
                    continue;
                }
                $child_attr[$v] = array();
            }     
        }

        foreach($child_attr as $k => $v){
            /*需要增加的数据*/
            $diff = array_diff_key($attr, $v);
            if(count($diff) > 0){
                foreach($diff as $k1 => $v1){
                    D('Attr')->attrAdd(array(
                        'cat_id'     => $k,
                        'attr_name'  => $k1,
                        'attr_value' => $v1['attr_value']
                    ));
                }
            }
            /*需要删除的数据*/
            $diff = array_diff_key($v , $attr);
            if(count($diff) > 0){
                foreach($diff as $k => $v1){
                   $this->attrDelete($v1['attr_id']);
                }              
            }
            /*相同的数据*/
            $intersect = array_intersect_key($v , $attr);           
            if(count($intersect) > 0){
                foreach($intersect as $k1 => $v1){
                    //属性值同步处理
                    //$this->updateAttrValueToNext($attr[$k1]['attr_id'] , $v1['attr_id']);
                }               
            }  
        } 
        return array('status'=>1,'msg'=>'操作成功');                   
    }

  /**
   * 更新属性值到下一级
   * @access public
   * @param  int   $attr_id  属性id
   * @return array $result   执行结果
   */
    public function updateAttrValueToNext($attr_id , $next_attr_id){
        if(!$attr_id || !$son_attr_id){
            return ;
        }
        $data = M('Attrbute_value')
            ->where(array('attr_id'=>$attr_id))
            ->field(array('attr_value'))
            ->select();
        M('Attrbute_value')->where(array('next_attr_id'=>$next_attr_id))->delete();
        foreach($data as $v){
            M('Attrbute_value')->add(array(
                'attr_id'    => $next_attr_id,
                'attr_value' => $v['attr_value']
            ));
        }  
    }     

  /**
   * 属性分配
   * @access public
   * @param  int    $cat_id   分类id
   * @param  int    $type_id  属性所属分类id
   * @param  array  $attr     属性
   * @return array  $result   执行结果
   */
    public function attrAllocation($cat_id , $attr){
    	$cat_id = !is_array($cat_id) ? array($cat_id) : $cat_id;
    	$cat_id = implode(',' , $cat_id);
        $attr = implode(',' , $attr);
        $r = M('Mall_category')
            ->where(array('id' => array('in' , $cat_id)))
            ->setField('filter_attr' , $attr);
        if($r === false){
        	return array('status'=>0 , 'msg'=>'操作失败');
        }
        return array('status'=>1 , 'msg'=>'操作成功');
    }  

  /**
   * 属性分配删除
   * @access public
   * @param  int    $cat_id   分类id
   * @param  int    $attr_id  属性id
   * @return array  $result   执行结果
   */
    public function attrAllocationDelete($cat_id , $attr_id){
        $data = M('Mall_category')->where(array('id' => $cat_id))->getField('filter_attr'); 
        $data = explode(',' , $data);
        if(empty($data)){
            return array('status'=>1 , 'msg'=>'操作成功');
        }
        $data = array_diff($data, array($attr_id));
        $data = implode(',' , $data);
        $r = M('Mall_category')
            ->where(array('id' => $cat_id))
            ->setField('filter_attr' , $data);
        if($r === false){
        	return array('status'=>0 , 'msg'=>'操作失败');
        }
        return array('status'=>1 , 'msg'=>'操作成功');
    }            
}

