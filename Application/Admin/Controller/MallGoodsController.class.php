<?php
namespace Admin\Controller;
use Com\Auth;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class MallGoodsController extends Controller{
    /*
     * 审核
     * */     
    public function goodsCheck(){
        if(IS_POST){
            $data = array(
                'content'  => I('content'),
                'goods_id' => I('id'),
                'time'     => time()
            );        	
        	$seller_id = M('Mall_goods')->where(array('id'=>$data['goods_id']))->getField('member_id');
            if(!$seller_id){
                $this->ajaxReturn(array('status'=>0,'msg'=>'商品不存在'));
            }             
            if(!$data['content']){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请输入内容'));
            }
            $data['seller_id'] = $seller_id;
            $r = M('Mall_goods_check')->add($data);
            if($r === false){
                $this->ajaxReturn(array('status'=>0,'msg'=>'失败'));
            }else{
            	$this->ajaxReturn(array('status'=>1,'msg'=>'成功'));
            }
        }else{
        	$id = I('id');
        	$goods_name = M('Mall_goods')->where(array('id'=>$id))->getField('goods_name');
        	$this->assign('goods_name' , $goods_name);
        	$this->assign('id' , $id);
        	$this->display();
        }
    } 

    /*
     * 获取商品列表
     * 搜索
     * 分页
     * */
	public function goodsList(){
	   if(IS_AJAX){
	        $goods_ids = '';
	        $where = array();
	        if(intval(I('seller_id'))){
	            $where['member_id'] = intval(I('seller_id'));
	        }
	        if(intval(I('status')) == 1 || intval(I('status')) == 0){
	            $where['status'] = intval(I('status'));
	        }
	        /*关键字查询*/
	        $keyword = I('keyword');
	        if($keyword){
	            $where_keyword = array(
	                'goods_name' => array('like',"%$keyword%"),
	                //'id'         => $keyword
	            );
	            $where_keyword['_logic'] = 'OR';
	        }
	        /*商城商品分类查询*/
	        $cat_id = intval(I('cat_id'));	       
	        if($cat_id){
	        	$redis   = new \Com\Redis();
		        /*商城商品分类缓存   更新*/
		        Hook::add('getCategory','Home\\Addons\\MallCategoryAddon');
		        Hook::listen('getCategory');
		        $categorys = $redis->get('mall_category' , 'array');
		        //当前分类的下级分类
	            $next_cat = D('Home/MallCategory')->getLastLevelCategory($cat_id , $categorys);
		        $temp_cat  = array_all_column($categorys , 'id');
		        if(count($next_cat) <= 0){
		            $where['cat_id'] = $cat_id;
		        }else{
		            //所有的子级分类
		            $cat_ids  = D('Home/MallCategory')->getSonCategory($cat_id , $temp_cat);
		            $where['cat_id'] = array('in' , implode($cat_ids , ','));
		        } 
	        }
	           
	        /*加入推荐查询*/
	        $model_id = I('model_id');
	        if($model_id){
	            $goods_ids = M('Mall_goods_model')
			               ->where(array('id'=>$model_id))
			               ->getField('goods_ids');
	        }
	        if(intval($keyword)){
	        	if(!$model_id || strpos($goods_ids , $keyword) !== false){
                    $goods_ids = $keyword;
	        	}else{
	        		$goods_ids = '';
        	    }	        		
	        }
	        if($goods_ids){
	            $where_keyword['id'] = array('in' , $goods_ids);
	        }
	        if($where_keyword){
	            $where['_complex'] = $where_keyword;
	        }
	        $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
	        $listRows = intval(I('listRows'))?intval(I('listRows')):10;
	        $list     = M('Mall_goods')
			          ->order('id desc')
			          ->limit($firstRow,$listRows)
			          ->where($where)
			          ->select();
	        $this->ajaxReturn(array(
	        	'data'  => $list,
	        	'total' => M('Mall_goods')->where($where)->count(),
	        	$where
	        ));
	    }else{
	       $seller_id = I('seller_id') ? intval(I('seller_id')) : '0';
	       $this->assign('seller_id' , $seller_id);
	       $this->display();	       
	    }
	}
	
	/*
	 * 添加商品
	 * */
	public function goodsAdd(){
	    if(IS_POST){
	   		$data = I();
	   		/*检测商城商品数据合法性*/
	   		$r    = D('MallGoods')->checkGoodsData($data);
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
	        $result = D('MallGoods')->goodsAdd($data , $goods_gallery);
	        $this->ajaxReturn($result);
		}else{
			$cat_id=I('cat_id');
			/*自动获取最大的排序*/
			$sort=M('Mall_goods')->max('sort');
			$this->sort=$sort?$sort++:'1';
			$this->category=M('Mall_category')->where(array('id'=>$cat_id))->find();	
			//商品品牌
			$brands = M('Mall_category_brand as a')
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
			$data = $_POST;
			if(!$data['id']){
			    $this->ajaxReturn(array('status'=>0,'id错误'));
			}
			/*检测商城商品数据合法性*/
			$r    = D('MallGoods')->checkGoodsData($data);
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
			    $old = M('Mall_goods')
			         ->where(array('id'=>$data['id']))
			         ->field('goods_thumb,goods_img')
			         ->find();
			}
			$gallery_id = implode(',' , $gallery_id);
			if($gallery_id){
			    $old_ = M('Mall_goods_gallery')
			    ->where(array('id'=>array('in' , $gallery_id),'goods_id'=>$data['id']))
			    ->field('gallery_img')
			    ->select();
			}
            $result = D('MallGoods')->goodsUpdate($data , $goods_gallery);
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
                Hook::add('IndexGoodsUpdate','Home\\Addons\\MallGoodsAddon');
                Hook::listen('IndexGoodsUpdate' , $data['id']);
            }
			$this->ajaxReturn($result);
		}else{
			$id        = I('id');
			$goods     = M('Mall_goods')->where(array('id'=>$id))->find();//取出商品
			$goods_data= M('Mall_goods_data')->where(array('goods_id'=>$id))->find();			
			$goods_gallery = M('Mall_goods_gallery')->where(array('goods_id'=>$id))->select();//取出商品相册

	        $pcategory = M('Mall_category')->where(array('id'=>$goods['cat_id']))->find();	
			//商品品牌
			$brands = M('Mall_category_brand as a')
			        ->join("tp_goods_brand as b on a.brand_id=b.id")
			        ->field("b.*")
			        ->where(array('cat_id'=>$goods['cat_id']))
			        ->select();
			/*扩展属性*/		
			//$this->extendattr = D('MallGoods')->get_extendattr($id,$goods['cat_filter_id']);
			$this->assign('goods_data',$goods_data);
            $this->assign('pcategory',$pcategory);
			$this->assign('goods_gallery' , $goods_gallery);
			$this->assign('goods' , $goods);
			$this->assign('goods_json' , json_encode($goods));
			$this->assign('brands' , $brands);
			$this->display();
		}
	}

	/*ajax删除商品*/
	public function goodsDelete(){
		if(IS_POST){
			$id     = I('id');
			$result = D('MallGoods')->goodsDelete($id);
            $this->ajaxReturn($result);
		}
	}

	/*ajax删除商品相册*/
	public function goodsGalleryDelete(){
		if(IS_POST){
			$id     = I('gallery_id');
			$result = D('MallGoods')->goodsGalleryDelete($id);
			$this->ajaxReturn($result);
		}
	}
	
	/*商品批量排序*/
	public function sortChange(){
	    if(IS_AJAX){
	        $data   = I();
	        $result = D('MallGoods')->sortAllChange($data);
	        $this->ajaxReturn($result);
	    }
	}
	
	/*ajax更改商品上架状态*/
	public function goodsStateChange(){
	    if(IS_POST){
	        $result = array(
	            'status' => 1,
	            'msg'    => '操作成功'
	        );
	        $id = I('id');
	        if(is_array($id)){
                $ids = implode(',' , $id);
	        }else{
	        	$ids = $id;
	        }
	        $status = I('status') == 1 ? 1 : 0;
	        $r = M('Mall_goods')
		       ->where(array('id'=>array('in' , $ids)))
		       ->save(array('status'=>$status));
		    $result['ids'] = $ids;
	        if($r === false){
	            $result = array(
	                'status' => 0,
	                'msg'    => '操作失败',
	            );
	        }
	        $this->ajaxReturn($result);
	    }
	}

	/*ajax更改加入推荐状态*/
	public function goodsModelChange(){
	    if(IS_POST){
	        $model_id = I('id');
	        $goods_id = I('goods_id');
	        $model    = M('Mall_goods_model')->where(array('id'=>$model_id))->getField('goods_ids');
	        $model    = explode(',' , $model);
	        $k        = array_search($goods_id, $model);
	        if($k !== false){
	            unset($model[$k]);
	        }else{
	            array_push($model , $goods_id);
	        }
	        $model = implode(',' , $model);;
	        $r = M('Mall_goods_model')->where(array('id'=>$model_id))->save(array('goods_ids'=>$model));
	        $result =array('status'=>'1','msg'=>'修改成功');
	        if($r === false){
	            $result =array('status'=>'0','msg'=>'修改失败');
	        }else{
	            $redis = new \Com\Redis();
	            $redis->redis->delete('index_mall_goods');	            
	        }
	        $this->ajaxReturn($result);
	    }
	}

	/*ajax批量设置加入推荐状态为下架*/
	public function allGoodsModelFalse(){
	    if(IS_POST){
	    	$result   = array('status'=>'1','msg'=>'修改成功');
	        $model_id = I('model_id');
	        $goods_id = I('goods_id');
	        if(!is_array($goods_id) || count($goods_id) < 0){
                $this->ajaxReturn(array('status'=>'0','msg'=>'id错误'));
	        }
	        $model    = M('Mall_goods_model')->where(array('id'=>$model_id))->getField('goods_ids');
	        $model    = explode(',' , $model);
	        $goods_id = array_diff($model , $goods_id);
	        $goods_id = implode(',' , $goods_id);
	        if($goods_id == $model_id){
                $this->ajaxReturn($result);
	        }
	        $r = M('Mall_goods_model')->where(array('id'=>$model_id))->save(array('goods_ids'=>$goods_id));	
            if($r === false){
	            $result =array('status'=>'0','msg'=>'修改失败');
	        }else{
	            $redis = new \Com\Redis();
	            $redis->redis->delete('index_mall_goods');	            
	        }
	        $this->ajaxReturn($result);
	    }
	}

	public function selectcategory(){
	    $this->display();
	}	
}
