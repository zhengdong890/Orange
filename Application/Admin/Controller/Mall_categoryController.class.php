<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class Mall_categoryController extends Controller {
   /**
    * 商品分类列表页 
    */ 
    public function categoryList(){
   	    if(IS_POST){
            $cat_id = I('id');
   	    	$list   = D('Mall_category')->getNextCategory($cat_id);
            $this->ajaxReturn($list);
   	    }else{
   	    	$this->display();
   	    }   		   	
    }

   /**
    * 商品属性列表页 
    */ 
    public function categoryAttrList(){
   	    if(IS_POST){
   	    	$cat_id = I('id');
   	    	$list   = D('Mall_category')->getNextCategory($cat_id);
            $this->ajaxReturn($list);
   	    }else{
   	    	$this->display();
   	    }   		   	
    } 

    public function getCategory(){
        if(IS_AJAX){
	   	    $redis = new \Com\Redis();
	   		/*商城商品分类缓存   更新*/
	        Hook::add('getCategory','Home\\Addons\\MallCategoryAddon');
	        Hook::listen('getCategory');
	        $categorys = $redis->get('mall_category' , 'array');
	        foreach($categorys as $k => $v){
	            if($v['level'] == '4'){
	                unset($categorys[$k]); 
	            }
	        }
            $list = get_child($categorys);
            $this->ajaxReturn($list);
        }
    }   
   
   /**
    * 添加商城商品分类
    * @access public  
    */ 
    public function categoryAdd(){
	   	if(IS_POST){
	   		$data   = $_POST;		
	   	    /*上传图片*/
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 3145728 ;// 设置附件上传大小
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
			// 上传文件
			$info = $upload->upload();
			if($info) {
				$data['cat_thumb'] = $upload->rootPath.$info['cat_thumb']['savepath'].$info['cat_thumb']['savename'];//获取图片路径				
				if($info['cat_thumb_1']){
					$data['cat_thumb_1'] = $upload->rootPath.$info['cat_thumb_1']['savepath'].$info['cat_thumb_1']['savename'];//获取图片路径
				}	
				if($info['index_thumb']){
					$data['index_thumb'] = $upload->rootPath.$info['index_thumb']['savepath'].$info['index_thumb']['savename'];//获取图片路径
				}			
			}	
			$data['level'] = M('Mall_category')->where(array('id'=>$data['pid']))->getField('level');
			$data['level']++;
	   		$result = D('Mall_category')->categoryAdd($data);
	   		if($result['status']){
		   		$redis  = new \Com\Redis();
		   		//品牌更新缓存
	            Hook::add('mallCategoryBrandAdd','Home\\Addons\\BrandAddon');
		        Hook::listen('mallCategoryBrandAdd');
		        /*商城商品分类缓存更新*/
		        Hook::add('updateCategory','Home\\Addons\\MallCategoryAddon');
		        Hook::listen('updateCategory');	
	   		}
	   		$this->ajaxReturn($result);
	   	}else{
	   		$cat_id = I('cat_id');
	   		$this->assign('cat_id' , $cat_id);
	   		$redis = new \Com\Redis();
	   		/*商城商品分类缓存   更新*/
	        Hook::add('getCategory','Home\\Addons\\MallCategoryAddon');
	        Hook::listen('getCategory');
	        $categorys = $redis->get('mall_category' , 'array');
	        foreach($categorys as $k => $v){
	            if($v['level'] == '4'){
	                unset($categorys[$k]); 
	            }
	        }
	   		$this->assign('categorys' , get_child($categorys));
	   	    /*商品类型*/
		   	$goods_type = D('Goods_type')->getGoodsType();
		   	$goods_type = tree_1($goods_type);
		   	$this->assign('goods_type' , $goods_type);
		   	$sort       = M('Mall_category')->max('sort');
		   	$this->assign('sort' , $sort +1 );
		   	/*商品品牌*/
	   		$brands = M('Goods_brand')->field('id,brand_name')->select();
	   		$this->assign('brands' , $brands);
	   		$this->display();
	   	}
    }

  /**
   * 修改商品分类
   * @access public  
   */    
    public function categoryUpdate(){
        if(IS_POST){
	   		    $data   = $_POST;	   		   			   		
	   	      //上传图片
            $upload = new \Think\Upload();// 实例化上传类
			      $upload->maxSize = 3145728;// 设置附件上传大小
			      $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			      $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
			      // 上传文件
			      $info = $upload->upload();
			      if($info) {
				        if($info['cat_thumb']){
					          $data['cat_thumb'] = $upload->rootPath.$info['cat_thumb']['savepath'].$info['cat_thumb']['savename'];//获取图片路径
				        }
				        if($info['cat_thumb_1']){
					          $data['cat_thumb_1'] = $upload->rootPath.$info['cat_thumb_1']['savepath'].$info['cat_thumb_1']['savename'];//获取图片路径
				        }
				        if($info['index_thumb']){
					          $data['index_thumb'] = $upload->rootPath.$info['index_thumb']['savepath'].$info['index_thumb']['savename'];//获取图片路径
				        }
			      }	
			      if($data['filter_extendattr']){
				        $data['filter_extendattr'] = implode(',',$data['filter_extendattr']);//扩展属性封成字符串
			      }
			      $data['level'] = M('Mall_category')->where(array('id'=>$data['pid']))->getField('level');
			      $data['level']++;
			      $result = D('Mall_category')->categoryUpdate($data);
			      $redis = new \Com\Redis();
            //品牌 更新缓存
            Hook::add('mallCategoryBrandAdd','Home\\Addons\\BrandAddon');
	          Hook::listen('mallCategoryBrandAdd');
	          /*商城商品分类缓存更新*/
	          Hook::add('updateCategory','Home\\Addons\\MallCategoryAddon');
	          Hook::listen('updateCategory');   	
			      $this->ajaxReturn($result);
	     	}else{
	   		    $id       = I('id');
	   		    $category = M('Mall_category')->where(array('id'=>$id))->find(); 
	   		    $p_name   = M('Mall_category')->where(array('id'=>$category['pid']))->find(); 
	   		    $this->assign('category' , $category);   
	   		    $this->assign('p_name' , $p_name); 		
	   		    $extends  = M('Attrbute')
			          ->where(array('attr_id'=>array('in',$category['filter_extendattr'])))
			          ->select();
			      $this->assign('filter_extendattr' , $extends);
		     	  $redis = new \Com\Redis(); 
	   		    /*商城商品分类缓存   更新*/
	          Hook::add('getCategory','Home\\Addons\\MallCategoryAddon');
	          Hook::listen('getCategory');
	          $categorys = $redis->get('mall_category' , 'array');
	          foreach($categorys as $k => $v){
	              if($v['level'] == '4'){
	                  unset($categorys[$k]); 
	              }
	          }
	   		    $this->assign('categorys' , get_child($categorys));	   		
	   		    /*商品类型*/
	   		    $goods_type = M('Goods_type')->select();
	   		    $this->assign('goods_type' , tree_1($goods_type));
	   		    /*商品品牌*/
	   	    	$brands = M('Goods_brand')->field('id,brand_name')->select();
	   		    $this->assign('brands' , $brands);
	   		    /*商品分类品牌*/
	   		    $cat_brand_ = M('Mall_category_brand')->where(array('cat_id'=>$id))->select();
	   		    foreach ($cat_brand_ as $k => $v) {
	   			      $cat_brand[] = $v['brand_id'];
	   		    }
	   		    $this->assign('cat_brand' , implode(',' , $cat_brand));
	   		    $this->display();
	   	  }
    }
   
   /*ajax删除商品分类*/
   public function categoryDelete(){
   	  if(IS_POST){
   	  	  $id     = I('id');
   	  	  $result = D('Mall_category')->categoryDelete($id);
   	  	  if($result['status']){
	   	  	  $redis = new \Com\Redis();
	   	  	  $redis->redis->delete('mall_goods');
	   	  	  /*商城商品分类缓存更新*/
		      Hook::add('updateCategory','Home\\Addons\\MallCategoryAddon');
		      Hook::listen('updateCategory');
   	  	  }
   	  	  $this->ajaxReturn($result);
   	  }
   }

   /*品牌同步到子类*/
   public function brandSonUpdate(){
   	  if(IS_POST){
   	  	  $cat_id = I('id');
   	  	  /*获取分类下的品牌*/
   	  	  $brand_ids = M('Mall_category_brand')
   	  	             ->where(array('cat_id'=>$cat_id))
   	  	             ->field("brand_id")
   	  	             ->select();
   	  	  $values = array();
   	  	  //获取子集分类           
   	  	  $ids_   = M('Mall_category')->where(array('pid'=>$cat_id))->field("id")->select();
          /*同步到子集分类*/
          $fields = array('`cat_id`','`brand_id`');
          foreach($ids_ as $k => $v){
          	  $ids[]         = $v['id'];
          	  $arr['cat_id'] = $v['id'];
          	  foreach($brand_ids as $k1 => $v1){
                  $arr['brand_id'] = $v1['brand_id'];
                  $values[]        = "('" . implode("','",$arr) . "')";
          	  }        	               
          }
          /*先删除*/
          $ids = implode(',' , $ids);
          M('Mall_category_brand')->where(array('cat_id'=>array('in',$ids)))->delete();
          /*增加*/
          $sql = "INSERT INTO `tp_mall_category_brand` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
          $r  = M()->execute($sql); 
          $result = array('status'=>1,'msg'=>'操作成功');       
          $this->ajaxReturn($result);
   	    }
    }

    /*
     * 商品分类单位
     * */
    public function unitUpdate(){
        if(IS_AJAX && IS_POST){
        	$data = I();
        	if(empty($data['id'])){
        		$this->ajaxReturn(array('status' => 0 , 'msg' => 'id错误'));
        	}
        	$r = D('Mall_category')->unitUpdate($data);
        	$this->ajaxReturn($r);
        }else{
        	$id   = intval(I('id'));
        	if($id == 0){
        		exit("商品分类不能为空");
        	}
        	$data = M('Mall_category')->where(array('id'=>$id))->field('id,cat_name,unit')->find();
        	if(empty($data)){
                exit("商品分类不存在");
        	}
        	$data['unit'] = explode(',' , $data['unit']);
        	$this->assign('data' , $data);
        	$this->display();
        }
    }

   /*商品分类单位 同步到子类*/
   public function unitSonUpdate(){
   	    if(IS_POST){
   	  	  $result = array('status'=>1,'msg'=>'操作成功');      
   	  	  $cat_id = I('id');        
   	  	  $unit   = M('Mall_category')->where(array('id'=>$cat_id))->getField('unit');
          $r = M('Mall_category')->where(array('pid'=>$cat_id))->save(array('unit'=>$unit));
          if($r == false){
              $result = array('status'=>0,'msg'=>'操作失败');      
          }          
          $this->ajaxReturn($result);
   	    }
    }

    /*
     * 商品分类单位 中的单位
     * */
    public function unitUnitUpdate(){
        if(IS_AJAX && IS_POST){
        	$data = I();
        	if(empty($data['id'])){
        		$this->ajaxReturn(array('status' => 0 , 'msg' => 'id错误'));
        	}
        	$r = D('Mall_category')->unitUnitUpdate($data);
        	$this->ajaxReturn($r);
        }else{
        	$id   = intval(I('id'));
        	if($id == 0){
        		exit("商品分类不能为空");
        	}
        	$data = M('Mall_category')->where(array('id'=>$id))->field('id,cat_name,unit_unit')->find();
        	if(empty($data)){
                exit("商品分类不存在");
        	}
        	$data['unit_unit'] = explode(',' , $data['unit_unit']);
        	$this->assign('data' , $data);
        	$this->display();
        }
    }   

   /*商品分类单位中的单位 同步到子类*/
   public function unitUnitSonUpdate(){
   	    if(IS_POST){
   	  	  $result = array('status'=>1,'msg'=>'操作成功');      
   	  	  $cat_id = I('id');        
   	  	  $unit   = M('Mall_category')->where(array('id'=>$cat_id))->getField('unit_unit');
          $r = M('Mall_category')->where(array('pid'=>$cat_id))->save(array('unit_unit'=>$unit));
          if($r === false){
              $result = array('status'=>0,'msg'=>'操作失败');      
          }          
          $this->ajaxReturn($result);
   	    }
    }   


    /*分类级数更新*/
    public function levelUpdate(){
   	    if(IS_POST){
   	  	    $result = array('status'=>1,'msg'=>'操作成功');      
   	  	    $cat_id = I('id');        
   	  	    $pid    = M('Mall_category')->where(array('id'=>$cat_id))->getField('pid');
            $level  = M('Mall_category')->where(array('id'=>$pid))->getField('level');
            $level++;
            $r = M('Mall_category')->where(array('id'=>$cat_id))->save(array('level'=>$level));
            if($r === false){
              $result = array('status'=>0,'msg'=>'操作失败');      
            }
      	    $redis = new \Com\Redis();
   	  	    /*商城商品分类缓存更新*/
	        Hook::add('updateCategory','Home\\Addons\\MallCategoryAddon');
	        Hook::listen('updateCategory');    
            $this->ajaxReturn($result);
   	    }
    } 

	/**
	 * 属性批量分配到下一级
	 */
    public function attrAllocationNext(){
    	if(IS_POST){
            $data    = I();
            $cat_id  = intval($data['id']);
	    	if($cat_id == 0){
	            $this->ajaxReturn(array('status'=>0,'msg'=>'请选择分类id'));
	    	}            
            $cat_ids = M('Mall_category')->where(array('pid'=>$cat_id))->field("id")->select();
            $cat_ids = array_column($cat_ids , 'id');
            $attr = M('Attrbute')->where(array('cat_id'=>$cat_id))->field('attr_id')->select();
	        if(empty($attr)){
	            $this->ajaxReturn(array('status'=>0,'msg'=>'无属性'));
	        }
	        $attr = array_column($attr , 'attr_id'); 
	        $result = D('Attr')->attrAllocation($cat_ids , $attr);
	        if($result['status'] == 1){
	            /*商城商品分类缓存更新*/
		        Hook::add('updateCategory','Home\\Addons\\MallCategoryAddon');
		        Hook::listen('updateCategory');
	        }
	        $this->ajaxReturn($result);
    	}
    }

	/**
	 * 属性分配
	 */
    public function attrAllocation(){
    	if(IS_POST){
            $data  = I();
            $cat_id  = intval($data['cat_id']);
            $type_id = intval($data['type_id']);
	    	if($cat_id == 0){
	            $this->ajaxReturn(array('status'=>0,'msg'=>'请选择分类id'));
	    	}
	        if(empty($data['attr'])){
	            $this->ajaxReturn(array('status'=>0,'msg'=>'请选择属性'));
	        }
	        $result = D('Attr')->attrAllocation($cat_id , $data['attr']);
	        if($result['status'] == 1){
	            /*商城商品分类缓存更新*/
		        Hook::add('updateCategory','Home\\Addons\\MallCategoryAddon');
		        Hook::listen('updateCategory');
	        }
	        $this->ajaxReturn($result);
    	}else{
    		$cat_id = I('id');
    		$data   = M('Mall_category')->where(array('id'=>$cat_id))->field('cat_name,filter_attr')->find();
    		if(empty($data)){
    			exit('分类不存在');
    		}
    		$attr = M('Attrbute')->where(array('attr_id'=>array('in' , $data['filter_attr'])))->select();
    		$redis     = new \Com\Redis();
            /*获取商城商品分类缓存*/
            $categorys = $redis->get('mall_category' , 'array');
            $temp_cat  = array_all_column($categorys , 'id');
            //面包屑 处理
            $crumb     = D('Mall_category')->getCrumb($cat_id , $temp_cat);
            //dump($crumb);
            $this->assign('crumb' , $crumb);
    		$this->assign('attr' , $attr);
    		$this->assign('data' , $data);
    		$this->assign('cat_id' , $cat_id);
    		$this->display();
    	}    	
    }   

	/**
	 * 属性删除
	 */
    public function attrAllocationDelete(){
    	if(IS_POST){
            $data    = I();
            $cat_id  = intval($data['cat_id']);
            $attr_id = intval($data['attr_id']);
	    	if($cat_id == 0){
	            $this->ajaxReturn(array('status'=>0,'msg'=>'请选择分类id'));
	    	}
	        if($attr_id == 0){
	            $this->ajaxReturn(array('status'=>0,'msg'=>'请选择需要删除的属性'));
	        }
	        $result = D('Attr')->attrAllocationDelete($cat_id , $attr_id );
	        if($result['status'] == 1){
	            /*商城商品分类缓存更新*/
		        Hook::add('updateCategory','Home\\Addons\\MallCategoryAddon');
		        Hook::listen('updateCategory');
	        }
	        $this->ajaxReturn($result);
    	} 	
    }          
}