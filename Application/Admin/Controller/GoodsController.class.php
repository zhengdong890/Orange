<?php
namespace Admin\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class GoodsController extends CommonController{
	/*
     * 获取商品列表
     * 搜索
     * 分页 
     * */
	public function goodsList(){
	   if(IS_AJAX){
	       $goods_ids = '';
	       $where = array();
	       $where['is_check'] = 1;
	       $where['check_status'] = 1;
	       /*关键字查询*/
	       $keyword = I('keyword');
	       if($keyword){
	           $where_keyword = array(
	               'goods_name' => array('like',"%$keyword%"),
	               'goods_code '=> $keyword
	           );
	           $where_keyword['_logic'] = 'OR';
	       }
	       /*共享商品分类查询*/
	       $cat_id = intval(I('cat_id'));
	       if($cat_id){
	           $data = M('Category')->select();
	           $cats = getTree($data,$cat_id);
	           $cats[]['id'] = $cat_id;
	           foreach($cats as $v){
	               $cat_ids[] = intval($v['id']);//分类集组装 转换成int
	           }
	           $where['cat_id'] = array('in' , implode(',' , $cat_ids));//复合条件组装
	       }
	       /*加入推荐查询*/
	       $model_id = I('model_id');
	       if($model_id){
	           $goods_ids = M('Goods_model')->where(array('id'=>$model_id))->getField('goods_ids');
	       }
	       if(intval($keyword)){
	           $goods_ids .= ',' . intval($keyword);
	       }
	       if($goods_ids){
	           $where_keyword['id'] = array('in' , $goods_ids);
	       }
	       if($where_keyword){
	           $where['_complex'] = $where_keyword;
	       }
	       $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
	       $listRows = intval(I('listRows'))?intval(I('listRows')):10;
	       $list     = M('Goods')->order('id desc,sort')->limit($firstRow,$listRows)->where($where)->select();
	       $this->ajaxReturn(array('data'=>$list,'total'=>M('Goods')->where($where)->count()));	       
	   }
   	   $this->display();
	}
	
	/*商品批量排序*/
	public function sortChange(){
	    if(IS_AJAX){
	        $data   = I();
	        $result = D('Goods')->sortAllChange($data);
	        $this->ajaxReturn($result);
	    }
	}
	
	/*获取商品加入推荐*/
	public function getGoodsModel(){
	    if(IS_AJAX){
	        $goods_model = M('Goods_model')->select();
	        $this->ajaxReturn($goods_model);
	    }
	}
	
	/*ajax更改加入推荐状态*/
	public function goodsModelChange(){
	   	if(IS_POST){
	   		$model_id = I('id');
	   		$goods_id = I('goods_id');
	   		$model    = M('Goods_model')->where(array('id'=>$model_id))->getField('goods_ids');
	   		$model    = explode(',' , $model);
	   		$k        = array_search($goods_id, $model);
	   		if($k !== false){
                unset($model[$k]);
	   		}else{
	   			array_push($model , $goods_id);
	   		}
	   		$model = implode(',' , $model);
	   		$r = M('Goods_model')
	   		   ->where(array('id'=>$model_id))
	   		   ->save(array('goods_ids'=>$model));
	   		$result =array('status'=>'1','msg'=>'修改成功');
	   		if($r === false){
                $result =array('status'=>'0','msg'=>'修改失败');
	   		}else{
	   		    /*首页缓存处理*/
                Hook::add('IndexGoodsUpdate','Home\\Addons\\goodsAddon');
                Hook::listen('IndexGoodsUpdate');
	   		}
	   		$this->ajaxReturn($result);
   	    }
	}
	
	public function selectcategory(){
		/*获取商品分类树*/
		$categorys=M('Category')->select();
		$this->categorys = tree_1($categorys);
		$this->display();
	}
	
	/*
	 * 添加商品
	 * */
	public function goodsAdd(){
	    if(IS_POST){
	   		$data = I();
	   		/*检测共享商品数据合法性*/
	   		$r    = D('Goods')->checkGoodsData($data);
	   		if(!$r['status']){
	   		    $this->ajaxReturn($r);
	   		}
	   		/*取出租约属性*/
			foreach($data as $k=>$v){
				if(substr($k,0,6) == 'start_'){
					$k = substr($k,6);
					$rent[$k]['start'] = $v;
				}
				if(substr($k,0,4) == 'end_'){
					$k = substr($k,4);
					$rent[$k]['end'] = $v;
				}	
				if(substr($k,0,17) == 'goods_rent_price_'){
                    $k = substr($k,17);
					$rent[$k]['goods_rent_price'] = $v;
				}			
			}  		
			/*检测租期合法性*/
			$r = D('Goods')->checkRent($data['min_rent'] , $data['max_rent'] ,$rent);
			if(!$r['status']){
			    $this->ajaxReturn($r);
			}
		    //上传图片
	        $upload = new \Think\Upload();// 实例化上传类
	        $upload->maxSize = 3145728 ;// 设置附件上传大小
	        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	        $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
	        // 上传文件
	        $info = $upload->upload();
	        if($info) {
	        	/*商品表图片处理*/
	            if($info['goods_img']){//获取缩略图片路径         
	                $data['goods_thumb'] = $upload->rootPath.$info['goods_img']['savepath'].'thumb_'.$info['goods_img']['savename'];
	                $data['goods_img']   = $upload->rootPath.$info['goods_img']['savepath'].$info['goods_img']['savename'];
	                //生成缩略图
	                $image = new \Think\Image();
	                $image->open($data['goods_img']);
	                $image->thumb(200, 200)->save($data['goods_thumb']);
	            }     
	            /*商品相册处理*/
		        $length = $data['number'];//获取上传图片的个数
		        for($i = 1; $i <= $length; $i++){//图片组装
		            $xb    = 'gallery_img'.$i;//获取图片post过来数组的下标
		            $thumb = $upload->rootPath.$info["$xb"]['savepath'].$info["$xb"]['savename'];//获取上传图片路径
		            if($info["$xb"]){//如果图片有上传
		                $goods_gallery["$i"]['gallery_img'] = $thumb;
		            }
		        } 	            
	        }else{
		       $this->ajaxReturn(array(
		           'status' => 0,
		           'msg'    => $upload->getError()		           
		       ));die;
		    }
		    /*检测商品图片*/
		    if(!$data['goods_img']){
		        $this->ajaxReturn(array(
		            'status' => 0,
		            'msg'    => '请上传商品主图'
		        ));die;		        
		    }
	        $result = D('Goods')->goodsAdd($data , $goods_gallery , $rent);
	        $this->ajaxReturn($result);	
		}else{
			$cat_id=I('cat_id');
			/*自动获取最大的排序*/
			$sort=M('Goods')->max('sort');
			$this->sort=$sort?$sort++:'1';
			$this->category = M('Category')->where(array('id'=>$cat_id))->find();	
			//商品品牌
			$brands = M('Category_brand as a')
			        ->join("tp_goods_brand as b on a.brand_id=b.id")
			        ->field("b.*")
			        ->where(array('cat_id'=>$cat_id))
			        ->select();
			$this->assign('brands' , $brands);
			$this->display();
		}
	}

	/*
	 * 编辑商品
	 * */
	public function goodsUpdate(){
		if(IS_POST){
			$data = I();
			if(!$data['id']){
			    $this->ajaxReturn(array('status'=>0,'id错误'));
			}
			/*检测共享商品数据合法性*/
			$r    = D('Goods')->checkGoodsData($data);
			if(!$r['status']){
			    $this->ajaxReturn($r);
			}
			/*取出租约属性*/
			foreach($data as $k=>$v){
				if(substr($k,0,6) == 'start_'){
					$k = substr($k,6);
					$rent[$k]['start'] = $v;
				}
				if(substr($k,0,4) == 'end_'){
					$k = substr($k,4);
					$rent[$k]['end'] = $v;
				}	
				if(substr($k,0,17) == 'goods_rent_price_'){
                    $k = substr($k,17);
					$rent[$k]['goods_rent_price'] = $v;
				}			
			}
			/*检测租期合法性*/
			$r = D('Goods')->checkRent($data['min_rent'] , $data['max_rent'] ,$rent);
			if(!$r['status']){
			    $this->ajaxReturn($r);
			}			
			//上传图片
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 3145728 ;// 设置附件上传大小
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目
			// 上传文件
			$info = $upload->upload();
			if($info) {// 上传错误提示错误信息
				/*商品表图片处理*/
	            if($info['goods_img']){//获取缩略图片路径         
	                $data['goods_thumb'] = $upload->rootPath.$info['goods_img']['savepath'].'thumb_'.$info['goods_img']['savename'];
	                $data['goods_img']   = $upload->rootPath.$info['goods_img']['savepath'].$info['goods_img']['savename'];
	                //生成缩略图
	                $image = new \Think\Image();
	                $image->open($data['goods_img']);
	                $image->thumb(200, 200)->save($data['goods_thumb']);
	            }
				/*商品相册处理(上传新图与描述)*/
				$length = $data['number'];//获取上传图片的个数
				for($i=1;$i <= $length;$i++){//图片组装
					$xb    = 'gallery_img'.$i;//获取图片post过来数组的下标
					$thumb = $upload->rootPath.$info["$xb"]['savepath'].$info["$xb"]['savename'];//获取上传图片路径
					if($info["$xb"]){//如果图片有上传
						$data_goods_gallery["$i"]['gallery_img'] = $thumb;
					}
				}
				/*商品相册处理(更新图片与描述)*/
				foreach($info as $k=>$v){
					if(substr($k,0,14) == 'oldgallery_img'){
						$id = substr($k,14);
						$gallery_id[] = $id;
						$olddata_goods_gallery["$id"]['id']=$id;//获取图片的id
						$olddata_goods_gallery["$id"]['gallery_img']=$upload->rootPath.$v['savepath'].$v['savename'];//获取上传图片路径						
					}
				}
			}else
			if($upload->getError() != '没有文件被上传！'){
		       $this->ajaxReturn(array(
		           'status' => 0,
		           'msg'    => $upload->getError()		           
		       ));die;
		    }
			$goods_gallery = array(
                'old' => $olddata_goods_gallery,
                'new' => $data_goods_gallery
			);
			/*获取旧图*/
			if($data['goods_img']){
			    $old = M('Goods')
    			     ->where(array('id'=>$data['id']))
    			     ->field('goods_thumb,goods_img')
    			     ->find();
			}
			$gallery_id = implode(',' , $gallery_id);
			if($gallery_id){
			    $old_ = M('Goods_gallery')
			          ->where(array('id'=>array('in' , $gallery_id),'goods_id'=>$data['id']))
			          ->field('gallery_img')
			          ->select();
			}
            $result = D('Goods')->goodsUpdate($data , $goods_gallery , $rent);	
            /*删除旧图*/
            if($result['status']){
                if($data['goods_img']){
                    unlink($old['goods_thumb'],$old['goods_img']);
                }
                if($gallery_id){
                    foreach($old_ as $v){
                        unlink($v['gallery_img']);
                    }
                }  
                /*首页缓存处理*/
                Hook::add('IndexGoodsUpdate','Home\\Addons\\goodsAddon');
                Hook::listen('IndexGoodsUpdate' , $data['id']);
            }
            $this->ajaxReturn($result);	
		}else{
			$id            = I('id');
			$goods         = M('Goods')->where(array('id'=>$id))->find();//取出商品
			$goods_data    = M('Goods_data')->where(array('goods_id'=>$id))->find();
			$categorys     = M('Category')->select();//取出商品分类树
			//取出商品相册
			//商品品牌
			$brands = M('Category_brand as a')
			        ->join("tp_goods_brand as b on a.brand_id=b.id")
			        ->field("b.*")
			        ->where(array('cat_id'=>$goods['cat_id']))
			        ->select();
			$goods_gallery = M('Goods_gallery')->where(array('goods_id'=>$id))->select();
            //扩展属性		
			//$extendattr    = D('Goods')->get_extendattr($id,$goods['cat_filter_id']);	
			$goods_rent    = M("Goods_rent")->where(array('goods_id'=>$id))->select();
			$goods_rent_   = $goods_rent?$goods_rent:array('min_rent'=>1,'max_rent'=>99999);
			$this->assign('goods' , $goods);
			$this->assign('goods_json' , json_encode($goods));
			$this->assign('goods_data' , $goods_data);
			$this->assign('goods_gallery' , $goods_gallery);
			$this->assign('categorys' , tree_1($categorys));
			//$this->assign('extendattr' , $extendattr);
			$this->assign('goods_rent' , $goods_rent);
			$this->assign('brands' , $brands);
			$this->assign('goods_rent_json' , json_encode($goods_rent_,JSON_FORCE_OBJECT));
			$this->display();
		}
	}
	
	/*ajax删除商品*/
	public function goodsDelete(){
		if(IS_POST){
			$id     = I('id');
			$result = D('Goods')->goodsDelete($id);
			if($result['status']){
			    /*首页缓存处理*/
			    Hook::add('IndexGoodsUpdate','Home\\Addons\\goodsAddon');
			    Hook::listen('IndexGoodsUpdate' , $id);			    
			}
            $this->ajaxReturn($result);
		}
	}
	
	/*ajax删除商品相册*/
	public function goodsGalleryDelete(){
		if(IS_POST){
			$id     = I('gallery_id');
			$result = D('Goods')->goodsGalleryDelete($id);
			$this->ajaxReturn($result);
		}
	}

	/*ajax更改商品上架状态*/
	public function goodsStateChange(){
		if(IS_POST){
		    $result = array(
		        'status' => 0,
		        'msg'    => '操作成功'
		    );
			$id = I('id');
			$status = I('status') == 1 ? 1 : 0;
			$r = M('Goods')->where(array('id'=>$id))->save(array('status'=>$status));
			if($r === false){
				$result = array(
				    'status' => 0,
				    'msg'    => '操作失败'
				);
			}
			$this->ajaxReturn($result);
		}
	}
	
	/*ajax根据选择的分类动态输出销售属性的html*/
	public function attr_html(){
		if(IS_POST){
			$cat_id=I('cat_id');//获取分类id
			$attr=M("Category")->where(array('id'=>$cat_id))->getField("filter_attr");//获取分类下的扩展属性
			$attr=explode(',',$attr);//分割成数组
			$attr=M('Attrbute')->where(array('attr_id'=>array('in',$attr)))->select();//属性表里查出扩展属性的内容
			$html="";
			foreach($attr as $v){/*循环组装html*/
				if($v['attr_type']==3){
					$html.="<tr>
								<td class='label'>{$v['attr_name']}:</td>
					            <td>";
					$attr_value=M('Attrbute_value')->where(array('attr_id'=>$v['attr_id']))->order('sort')->select();
					foreach($attr_value as $v1){
						$html.="{$v1['attr_value']} <input type='checkbox' name='attr[]' value='".$v1['id']."' onclick='goods_baseattr_save(this)'>&nbsp;&nbsp;&nbsp;&nbsp;";
					}
					$html.="<td></tr>";
				}else{
					$html.="<tr>
								<td class='label'>{$v['attr_name']}:</td>
								<td><input type='text' name='attr".$v['attr_id']."[]'></input></td>
						    </tr>";
				}
			}
			echo $html;
		}
	}
		
	/*ajax根据选择的分类动态输出扩展属性的html*/
	public function extendattr_html(){
		if(IS_POST){
			$cat_id=I('cat_id');//获取分类id
			$attr_ids=M('Category')->where(array('id'=>$cat_id))->getField('filter_extendattr');
			$attr_ids=explode(',',$attr_ids);
			$extendattr=M('Attrbute')->where(array('attr_id'=>array('in',$attr_ids)))->select();//属性表里查出扩展属性的内容
			$attrs=M('Attrbute')->select();
			foreach($extendattr as $v){/*循环组装html*/
				$tree=tree_1($attrs,'attr_id',$v['attr_id']);
				if(empty($tree)){
					$html.="<tr>
					<td class='label'>{$v['attr_name']}:</td>
					<td>";
					if($v['attr_input_type']==1){
						$html.="<input type='text' name='extendattr".$v['attr_id']."'></input></td></tr>";
					}else{
						$attrbute_value=M('Attrbute_value')->where(array('attr_id'=>$v['attr_id']))->order('sort')->select();
						$html.="<select name='extendattr".$v['attr_id']."'><option value='0'>请选择...</option>";
						foreach($attrbute_value as $k=>$v){
							$html.="<option value='".$v['attr_value']."'>".$v['attr_value']."</option>";
						}
						$html.="</select></td></tr>";
					}
				}else{
					$html.="<tr>
					<td class='label' style='color:pink'>{$v['attr_name']}:</td>
					<td></td></tr>";
					 foreach($tree as $v){
					 	$html.="<tr><td class='label'>{$v['html']} {$v['attr_name']}:</td>";
					 	if($v['attr_input_type']==1){
					 		$html.="<td><input type='text' name='extendattr".$v['attr_id']."'></input></td></tr>";
					 	}else{
					 		$attrbute_value=M('Attrbute_value')->where(array('attr_id'=>$v['attr_id']))->order('sort')->select();
					 		$html.="<td><select name='extendattr".$v['attr_id']."'><option value='0'>请选择...</option>";
					 		foreach($attrbute_value as $k=>$v){
					 			$html.="<option value='".$v['attr_value']."'>".$v['attr_value']."</option>";
					 		}
					 		$html.="</select></td></tr>";
					 	}
					 }
				}
			}
			echo $html;	
		}
	}
	
	
	/*ajax删除特征值图片*/
	public function goods_attrgallery_del(){
		if(IS_POST){
			$id=I('id');
			$a=M('Goods_attrgallery')->where(array('id'=>$id))->delete();
			if($a!==false){
				echo '删除成功';
			}else{
				echo '删除失败';
			}
		}
	}
	
	/*ajax删除商品*/
	public function goods_delete(){
		if(IS_POST){
			$id=$_POST['id'];
			$img=M('Goods')->where(array('id'=>$id))->Field('goods_thumb,goods_img')->find();
			$a=M('Goods')->where(array('id'=>$id))->delete($id);
			if(!empty($a)){
				unlink($img['goods_thumb']);unlink($img['goods_img']);//删除图片
				/*删除商品相册*/
				$oldgallery_img=M('Goods_gallery')->where(array('id'=>$id))->getField('gallery_img');
				$a=M('Goods_gallery')->where(array('id'=>$id))->delete($id);
				if(!empty($a)){
					unlink($oldgallery_img);//删除图片
				}
				/*删除商品颜色*/
				$oldgallery_thumb=M('Goods_color')->where(array('id'=>$id))->getField('color_thumb');
				$a=M('Goods_color')->where(array('id'=>$id))->delete($id);
				if(!empty($a)){
					unlink($oldgallery_thumb);//删除图片
				}
				$result='删除成功';
			}else{
				$result='删除失败';
			}
			echo $result;
		}
	}
	 
	/*ajax删除商品相册*/
	public function delete_goodsgallery(){
		if(IS_POST){
			$id=$_POST['gallery_id'];
			$oldgallery_img=M('Goods_gallery')->where(array('id'=>$id))->getField('gallery_img');
			$a=M('Goods_gallery')->where(array('id'=>$id))->delete($id);
			if(!empty($a)){
				unlink($oldgallery_img);//删除图片
				$result='删除成功';
			}else{
				$result='删除失败';
			}
			echo $result;
		}
	}
	 
	/*ajax删除商品颜色*/
	public function delete_goodscolor(){
		if(IS_POST){
			$id=$_POST['color_id'];
			$oldgallery_thumb=M('Goods_color')->where(array('id'=>$id))->getField('color_thumb');
			$a=M('Goods_color')->where(array('id'=>$id))->delete($id);
			if(!empty($a)){
				unlink($oldgallery_thumb);//删除图片
				$result='删除成功';
			}else{
				$result='删除失败';
			}
			echo $result;
		}
	}
	 
	/*加入推荐*/
	public function goods_model(){
		$this->goods_model=M('Goods_model')->select();
		$this->display();
	}
	 
	/*添加加入推荐*/
	public function goods_model_add(){
		if(IS_POST){
			$data=I();
			/*验证数据*/
			$goods = D("Goods_model");
			$rules= array(
					array('goods_model_name','require','必须输入模块名'),
			);
			if(!$goods->validate($rules)->create($data)){
				$this->error($goods->getError(),'goods_model_add');
				die;
			}
			//上传图片
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 3145728 ;// 设置附件上传大小
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
			// 上传文件
			$info = $upload->upload();
			if(!$info) {// 上传错误提示错误信息
					
			}else{// 上传成功
				/*商品表图片处理*/
				if($info['goods_model_thumb']){//获取缩略图片路径
					$data['goods_model_thumb']=$upload->rootPath.$info['goods_model_thumb']['savepath'].$info['goods_model_thumb']['savename'];
				}
			}
			$id=M('Goods_model')->add($data);
			if($id){
				$this->success('添加成功','goods_model');
			}else{
				$this->error('添加失败','goods_model');
			}
		}else{
			$this->display();
		}
	}
	 
	/*修改加入推荐*/
	public function goods_model_update(){
		if(IS_POST){
			$data=I();
			/*验证数据*/
			$goods = D("Goods_model");
			$rules= array(
					array('goods_model_name','require','必须输入模块名'),
			);
			if(!$goods->validate($rules)->create($data)){
				$this->error($goods->getError(),U('goods_model_update',array('id'=>$data['id'])));
				die;
			}
			//上传图片
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 3145728 ;// 设置附件上传大小
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
			// 上传文件
			$info = $upload->upload();
			if(!$info) {// 上传错误提示错误信息
					
			}else{// 上传成功
				/*商品表图片处理*/
				if($info['goods_model_thumb']){//获取缩略图片路径
					$data['goods_model_thumb']=$upload->rootPath.$info['goods_model_thumb']['savepath'].$info['goods_model_thumb']['savename'];
					$oldgoods_model_thumb=M('Goods_model')->where(array('id'=>$data['id']))->getField('goods_model_thumb');//获取旧图
				}
			}
			$a=M('Goods_model')->save($data);
			if($a){
				unlink($oldgoods_model_thumb);
				$this->success('修改成功','goods_model');
			}else{
				$this->error('修改失败','goods_model');
			}
		}else{
			$id=I('id');
			$goods_model=M('Goods_model')->where(array('id'=>$id))->find();
			$goods_model['checked_y']=($goods_model['is_show']=='y')?'checked=checked':'';
			$goods_model['checked_n']=($goods_model['is_show']=='y')?'':'checked=checked';
			$this->goods_model=$goods_model;
			$this->display();
		}
	}
	 
	/*删除加入推荐模块*/
	public function goods_model_delete(){
		if(IS_POST){
			$id=$_POST['id'];
			$oldthumb=M('Goods_model')->where(array('id'=>$id))->getField('goods_model_thumb');
			$a=M('Goods_model')->where(array('id'=>$id))->delete();//删除
			if($a){
				unlink($oldthumb);//删除旧图
				$result='删除成功';
			}else{
				$result='删除失败';
			}
			echo $result;
		}
	}
	 
	/*ajax更改加入推荐开启状态*/
	public function goodsmodel_is_show_change(){
		if(IS_POST){
			$id=I('id');
			$is_show=M('Goods_model')->where(array('id'=>$id))->getField('is_show');
			$is_show=($is_show=='y')?'n':'y';
			$a=M('Goods_model')->where(array('id'=>$id))->save(array('is_show'=>$is_show));
			if($a){
				$result['tishi']="修改成功";
			}else{
				$result['tishi']="修改失败";
			}
			$result['is_show']=$is_show;
			$this->ajaxReturn($result);
		}
	}
}
