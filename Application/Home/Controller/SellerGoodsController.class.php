<?php
/*
 * 卖家中心商品处理
 * */  
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class SellerGoodsController extends Controller {	
	public function _initialize(){
        if(empty($_SESSION['member_data'])){
            header("Location:http://www.orangesha.com/login.html");
        }
        $redis = new \Com\Redis();
        /*底部帮助*/
        Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
        Hook::listen('getFooterHelp');
        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
        /*获取所有店铺的二级域名  更新*/
        $all_shop_domain = $redis->get('all_shop_data' , 'array');//获取redis的缓存
        $id = $_SESSION['member_data']['id'];
        $this->assign('domain' , $all_shop_domain[$id]['domain']?$all_shop_domain[$id]['domain']:$id);
        $this->assign('help' , $help);
    }

    /* 
     * 获取商品
     * */
    public function getGoodsAndSku(){
        if(IS_AJAX && IS_POST){
            $data      = I();
       	    $firstRow  = $data['firstRow'];
       	    $listRows  = $data['listRows'];
       	    $seller_id = $_SESSION['member_data']['id'];
       	    if($data['cat_id']){
                $search['cat_id'] = $data['cat_id'];
            }  
	        if($data['goods_name']){
	            $search['goods_name'] = $data['goods_name'];
	        }  
	        if($data['sku_code']){
	            $search['sku_code'] = $data['sku_code'];
	        }  	        
       	    if(!isset($data['search'])){
       	        $result = D('Mall_goods')->getGoodsAndSku($seller_id , array($firstRow,$listRows));
       	    }else{
                $result = D('Mall_goods')
                    ->getGoodsBySearch($seller_id , $data['search'] , array($firstRow,$listRows));
       	    }
            $this->ajaxReturn($result);         	
        }
    }
    
    /* 
     * 商城商品列表
     * */
    public function goodsList(){
       if(IS_POST){
           $data     = I();
       	   $firstRow = $data['firstRow'];
       	   $listRows = $data['listRows'];
		  // $parameter =  $data[1];
		  // dump($parameter);exit;
	       $goods = M('Mall_goods')
	              ->order('id desc')
	              ->where(array('member_id'=>$_SESSION['member_data']['id']))
	              ->limit($firstRow,$listRows)
	              ->select();
	       $count  = M('Mall_goods')->where(array('member_id'=>$_SESSION['member_data']['id']))->count();
	       $result = array(
	            'data'      => $goods,
	            'totalRows' => $count
	       );  
	       echo json_encode($result);     
       }else{
		   $member_id = $_SESSION['member_data']['id'];
		   $this->assign('member_id' , $member_id);
	   	   $this->display();          	
       }
    }

	/*
	 * 添加商品
	 * */
    public function goodsAdd(){
    	if(IS_POST){
	   		$data              = I();
	   		$data['member_id'] = $_SESSION['member_data']['id'];
	   		/*检测商城商品数据合法性*/
	   		$r    = D('Mall_goods')->checkGoodsData($data);
	   		if(!$r['status']){
	   		    $this->ajaxReturn($r);
	   		}
            $price = array();
            $num   = array();
	   		/*筛选属性处理*/
	   		if(!empty($data['attr'])){
		   		$data['attr'] = htmlspecialchars_decode($data['attr']);
		   		$attr = json_decode($data['attr'] , true);
		   		$r = D('Mall_goods')->checkAttr($data['cat_id'] , $attr);//检测筛选属性合法性 	
		   		if(!$r['status']){
		   		    $this->ajaxReturn($r);
		   		}
	   		}
		   	//获取商品分类sku
		   	$sku_code = M('Mall_category')->where(array('id'=>$data['cat_id']))->getField('code');
	   		/*属性sku处理*/
	   		if(!empty($data['sku'])){
		   		$sku = htmlspecialchars_decode($data['sku']);
		   		$sku = json_decode($sku , true);
		   		$r   = D('Mall_goods')->checkSku($sku , $data['attr'] , $type);//检测筛选属性合法性
		   		if(!$r['status']){
		   		    $this->ajaxReturn($r);
		   		}
		   		foreach ($sku as $k => $v) {
		   			$sku[$k]['sku_code'] = $sku_code.$data['member_id'].setnum(4,'n','0123456789');
		   		} 
		   		$price = array_merge($price , array_column($sku , 'price'));
		   		$num   = array_merge($num , array_column($sku , 'number'));		 
            }else{
            	//$this->ajaxReturn(array('status'=>0,'msg'=>'请至少输入一个商品信息'));
            }
            $data['sku_code']     = $sku_code.$data['member_id'].setnum(4,'n','0123456789');
            $data['goods_number'] = array_sum($num);
	   		$data['goods_price']  = min($price);
		    //上传图片
	        $upload           = new \Think\Upload();// 实例化上传类
	        $upload->maxSize  = 3145728 ;// 设置附件上传大小
	        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	        $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
	        // 上传文件
	        $info = $upload->upload();
	        if($info) {
	            /*商品表图片处理*/
	            if($info['goods_img']){//获取缩略图片路径         
	                $data['goods_thumb'] = $upload->rootPath.$info['goods_img']['savepath'].'thumb_'.$info['goods_img']['savename'];
	                $data['goods_img'] = $upload->rootPath.$info['goods_img']['savepath'].$info['goods_img']['savename'];
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
	        $result = D('Mall_goods')->goodsAdd($data , $goods_gallery , $data['member_id']);
	        if($result['status']){//商品筛选属性入库
	        	if(isset($attr)){
                    $r = D('Mall_goods')->goodsAttrAdd($attr , $result['goods_id'] , $data['member_id']);
	        	}
	            $r = D('Mall_goods')->goodsSkuAdd($sku , $result['goods_id'], $data['member_id']);
	        }
	        $this->ajaxReturn($result);
    	}else{
    		/*$data = M('Attrbute')->field('attr_id,attr_value')->select();
            foreach($data as $v){
            	if($v['attr_value'] == ''){
            		continue;
            	}
                $r = M('Attrbute_value')->where(array('attr_id'=>$v['attr_id']))->field('attr_id')->find();
                if(empty($r)){
                	$a = explode('\\r\\n' , $v['attr_value']);
                    dump($a);die;
                }
            }*/
			$member_id = $_SESSION['member_data']['id'];
	    	$cat_id = I('cat_id');
	    	!$cat_id && exit('请先选择商品分类');
	    	$redis   = new \Com\Redis();
	        /*商城商品分类缓存   更新*/
	        Hook::add('getCategory','Home\\Addons\\MallCategoryAddon');
	        Hook::listen('getCategory');
	        $categorys = $redis->get('mall_category' , 'array');
	        $temp_cat  = array_all_column($categorys , 'id');
	        //面包屑 处理
	        $crumb    = D('MallCategory')->getCrumb($cat_id , $temp_cat);
	    	$category = M('Mall_category')->where(array('id'=>$cat_id))->find();
	    	//获取商品所属分类的属性和属性值
	    	$filter_attr = $category['filter_attr'];
            $attr     = D('Attrbute')->getCategoryBaseAttr($filter_attr);
	    	/*获取品牌*/
	    	//商品分类 品牌更新
	    	Hook::add('updateMallCategoryBrand','Home\\Addons\\BrandAddon');
	    	Hook::listen('updateMallCategoryBrand');
	    	$category_brands = $redis->get('mall_category_brands', 'array');//获取redis的缓存
	    	//商品 品牌更新
	    	Hook::add('updateBrand','Home\\Addons\\BrandAddon');
	    	Hook::listen('updateBrand');
	    	$brands_ = $redis->get('brands', 'array');//获取redis的缓存
	    	//关联
	    	$brands = array();
	    	foreach($category_brands as $v){
	    	    if($v['cat_id'] == $cat_id){
	    	        $brands[$v['brand_id']] = $brands_[$v['brand_id']];
	    	    }
	    	}
		    //商品品牌
			$brands = M('Mall_category_brand as a')
			        ->join("tp_goods_brand as b on a.brand_id=b.id")
			        ->field("b.*")
			        ->where(array('cat_id'=>$cat_id))
			        ->select();
			$shop_nav=M('shopping')->where(array('member_id'=>$member_id,'status'=>1))->order('rsort asc')->select();				
			$shopcat = M('shop_category')->where(array('member_id'=>$member_id,'status'=>1))->order('sort asc')->select();					
			$shopcat = getList($shopcat);
			$this->assign('attr_json',$attr?json_encode($attr):'[]');
			$this->assign('shopcat',$shopcat);
            $this->assign('shop_nav' , $shop_nav);			
			$this->assign('brands' , $brands);
			$this->assign('attr' , $attr);
			$this->assign('category' , $category);
			$this->assign('crumb' , $crumb);
			$this->display();     		
    	}   
    }   

	/*
	 * 编辑商品
	 * */
	public function goodsUpdate(){
		if(IS_POST){
			$data = $_POST;
			$data['member_id'] = $_SESSION['member_data']['id'];			
			if(!$data['id']){
			    $this->ajaxReturn(array('status'=>0,'msg'=>'id错误'));
			}
			/*检测商城商品数据合法性*/
			$r    = D('Mall_goods')->checkGoodsData($data , 2);
			if(!$r['status']){
			    $this->ajaxReturn($r);
			}	

			/*新添加的 筛选属性处理*/
			//商品已经拥有的属性
			$temp_attr = M('Mall_goods_baseattr')
					   ->where(array('goods_id'=>$data['id']))
					   ->field('attr_id,attr_value_id')
					   ->select();
			foreach($temp_attr as $v){
                $goods_attr[$v['attr_id']][] = $v['attr_value_id'];
			}   		

	   		if(!empty($data['new_attr'])){
	   			$data['new_attr'] = htmlspecialchars_decode($data['new_attr']);
	   		    $new_attr = json_decode($data['new_attr'] , true);
		   		$r = D('Mall_goods')->checkAttr($data['cat_id'] , $new_attr);//检测筛选属性合法性	
		   		if(!$r['status']){
		   		    $this->ajaxReturn($r);
		   		}
	   		}

	   		/*新添加的 sku属性处理*/
	   		if(!empty($data['new_sku'])){
	   		    $new_sku = htmlspecialchars_decode($data['new_sku']);
	   		    $new_sku = json_decode($new_sku , true);
		   	    //检测新添加的 sku合法性
		   	    $r   = D('Mall_goods')->checkSku($new_sku , $new_attr , $type);
		   		if(!$r['status']){
		   		    $this->ajaxReturn($r);
		   		} 	   			
	   		}
            
	   		/*旧的 sku属性处理*/
	   		if(!empty($data['old_sku'])){
		   		$old_sku = htmlspecialchars_decode($data['old_sku']);
		   		$old_sku = json_decode($old_sku , true);
	            $r   = D('Mall_goods')->checkUpdateSku($old_sku , $goods_attr , $type);
		   		if(!$r['status']){
		   		    $this->ajaxReturn($r);
		   		} 
	   		}
            
            /*需要删除的sku*/
            $delete_sku = $data['delete_sku'];

	   		/*获取商品分类sku编码*/
	   		$sku_code = M('Mall_category')->where(array('id'=>$data['cat_id']))->getField('code');
	   		foreach ($new_sku as $k => $v) {
	   			$new_sku[$k]['sku_code'] = $sku_code.$data['member_id'].setnum(4,'n','0123456789');
	   			//$new_sku[$k]['price'] = floatval($v['price']);
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
	                $data['goods_img'] = $upload->rootPath.$info['goods_img']['savepath'].$info['goods_img']['savename'];
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
						$thumb = $upload->rootPath.$v['savepath'].$v['savename'];//获取上传图片路径
						$olddata_goods_gallery["$id"]['gallery_img']=$thumb;

					}
				}
				/*团购图片*/
				if($info['group_img']){//获取图片路径
					$group['group_img']=$upload->rootPath.$info['group_img']['savepath'].$info['group_img']['savename'];
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
            $result = D('Mall_goods')->goodsUpdate($data , $goods_gallery);           
            if($result['status']){
            	/*删除旧图*/
                if($data['goods_img']){
                    unlink($old['goods_thumb'],$old['goods_img']);
                }
                if($gallery_id){
                    foreach($old_ as $v){
                        unlink($v['gallery_img']);
                    }
                }            
                //分拣出商品属性的 增加数据 删除数据
                $attr = D('Mall_goods')->getAttrHandleType($goods_attr , $new_attr);
                //商品属性增加
                if(isset($attr['add'])){
                    $r = D('Mall_goods')->goodsAttrAdd($attr['add'] , $data['id'] , $data['member_id']);
                }                     	           
                //删除商品的属性
                D('Mall_goods')->attrDeleteByAttrValueId($attr['delete'] , $data['id'] , $data['member_id']);
                //新增sku属性
                if(isset($new_sku)){
	                $r = D('Mall_goods')->goodsSkuAdd($new_sku , $data['id'] , $data['member_id']);
	            }
	            //修改sku属性
	            if(isset($old_sku)){
	                $r = D('Mall_goods')->goodsSkuUpdate($old_sku ,  $data['id'] , $data['member_id']);
	            }
	            //根据商品目前拥有的属性 动态删除商品的sku 	            
                D('Mall_goods')->deleteSkuByGoodsAttr($data['member_id'] , $data['id']);
                //用户主动删除的sku
                if(count($delete_sku) > 0){
                    D('Mall_goods')->deleteSku($data['member_id'] , $delete_sku , $data['id']); 
                }                 	            
	            //设置库存
	            if(isset($new_sku) || isset($old_sku)){
                    $price = M('Sku')->where(array('goods_id'=>$data['id']))->min('price');
                    M('Mall_goods')->where(array('id'=>$data['id']))->setField('goods_price' , $price);
	            }                            
                /*首页缓存处理*/
                Hook::add('IndexGoodsUpdate','Home\\Addons\\MallGoodsAddon');
                Hook::listen('IndexGoodsUpdate' , $data['id']);
            }
            /*团购处理*/
			if($data['is_group']){
				$group['goods_group_price'] = $data['goods_group_price'];
				D('Mall_goods')->goodsGroup($data['id'] , $group);
			}	
			$this->ajaxReturn($result);
		}else{
			$member_id = $_SESSION['member_data']['id'];
			$id        = intval(I('id'));
			$goods     = M('Mall_goods')
				       ->where(array('id'=>$id,'member_id'=>$member_id))
				       ->find();//取出商品
		    if(!$goods['id']){
                exit('无该商品');
		    }
		    $category = M('Mall_category')->where(array('id'=>$goods['cat_id']))->find();
		    
		    //获取商品附加属性
			$goods_data = M('Mall_goods_data')->where(array('goods_id'=>$id))->find();
	    	//获取商品所属分类的属性和属性值
	    	$filter_attr= $category['filter_attr'];
            $attr       = D('Attrbute')->getCategoryBaseAttr($filter_attr);
            $attr       = array_all_column($attr , 'attr_id');
            $redis   = new \Com\Redis();
	        /*商城商品分类缓存   更新*/
	        Hook::add('getCategory','Home\\Addons\\MallCategoryAddon');
	        Hook::listen('getCategory');
	        $cat_id=$category['id'];
            $categorys = $redis->get('mall_category' , 'array');
	        $temp_cat  = array_all_column($categorys , 'id');
	        //面包屑 处理
	        $crumb    = D('MallCategory')->getCrumb($cat_id , $temp_cat);
	
			//获取商品拥有的属性		
            $goods_attr = M('Mall_goods_baseattr')->where(array('goods_id'=>$id))->select();
            foreach($goods_attr as $v){
                if($v['attr_value_id'] < 0){
                    $attr[$v['attr_id']]['attr_value'][] = array(
                    	'attr_value_id' => $v['attr_value_id'],
                        'attr_id'       => $v['attr_id'],
                        'attr_value'    => $v['attr_value']
                    );
                }
            }
            $attr = array_values($attr);
            //获取商品sku
            $goods_sku  = D('Mall_goods')->getGoodsSku($id);
			//商品相册
			$goods_gallery = M('Mall_goods_gallery')->where(array('goods_id'=>$id))->select();	
			//商品品牌
			$brands = M('Mall_category_brand as a')
			        ->join("tp_goods_brand as b on a.brand_id=b.id")
			        ->field("b.*")
			        ->where(array('cat_id'=>$goods['cat_id']))
			        ->select();
            /*团购*/			
            $group = M('Group_buy')->where(array('id'=>1))->find();
            $group_list = M('Group_list')->where(array('goods_id'=>$id))->find();

			$shopcat = M('shop_category')->where(array('member_id'=>$member_id,'status'=>1))->order('sort asc')->select();					
			$shopcat = getList($shopcat);
			$getcat_id = M('Mall_goods')->field('shop_cat')->where(array('member_id'=>$member_id,'id'=>$id))->getField('shop_cat');
			$this->assign('attr',$attr);
			$this->assign('crumb',$crumb);
			$this->assign('attr_json',$attr?json_encode($attr):'[]');
            $this->assign('goods_attr_json',$goods_attr?json_encode($goods_attr):'{}');
            $this->assign('goods_sku_json',$goods_sku?json_encode($goods_sku):'{}');       
			$this->assign('shopcat',$shopcat);
			$this->assign('getcat_id',$getcat_id);
			$this->assign('category',$category);
            $this->assign('group' , $group);
            $this->assign('group_list' , $group_list);		
			$this->assign('brands' , $brands);		
			$this->assign('goods' , $goods);
			$this->assign('goods_json' , json_encode($goods));
			$this->assign('goods_data' , $goods_data);
			$this->assign('goods_gallery' , $goods_gallery);
			$this->assign('gallery_number' , 4-count($goods_gallery));
			//$this->assign('extendattr' , $extendattr);
			$this->display();
		}
	}
	
	/*删除sku*/
	public function deleteSku(){
	    if(IS_AJAX){
	    	$id        = intval(I('id'));
	    	$member_id = $_SESSION['member_data']['id'];
	    	if(!$id){
	    	    $this->ajaxReturn(array('status'=>0,'msg'=>'id错误'));
	    	}
	    	$r  = M('Sku')->where(array('sku_id'=>$id))->delete();
	    	if($r === false || $r = 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'删除失败'));
	    	}
	    	$sku_value = M('Sku_value')
			    	   ->where(array('sku_id'=>$id))
			    	   ->field('attr_id,attr_value,attr_value_id,goods_id')
			    	   ->select();       
			$r  = M('Sku_value')->where(array('sku_id'=>$id))->delete(); 

			$goods_id  = $sku_value[0]['goods_id']; 
		    $attr_value_id = implode(',' , array_column($sku_value , 'attr_value_id')); 
		    $all_value = M('Sku_value')
		    	   ->where(array('goods_id'=>$goods_id ,'attr_value_id'=>array('in',$attr_value_id),'sku_id'=>array('neq',$id)))
		    	   ->field('attr_value_id')
		    	   ->select();
		    if(count($all_value) > 0){
	    		//删除商品属性
	    		$all_value = array_column($all_value, 'attr_value_id');	
			    $all_value = array_flip($all_value);
			    foreach($sku_value as $k => $v){
	                if(isset($all_value[$v['attr_value_id']])){
	                    unset($sku_value[$k]);
	                }
                }
		    }	   	    	    	
	    	if($r === false || $r = 0){
                $this->ajaxReturn(array('status'=>0,'msg'=>'删除失败'));
	    	}else{
                $r = D('Mall_goods')->goodsAttrDelete($sku_value , $member_id);
	    		$this->ajaxReturn(array('status'=>1,'msg'=>'删除成功'));
	    	}
	    }
	}

	/*ajax更改显示状态*/
	public function statusChange(){
	    if(IS_POST){
	        $data       = I();
	        $status_arr = array();
	        foreach($data as $k=>$v){
	            $goods_id = $k;
	            $status_arr[] = array(
	                'id'     => intval($goods_id),
	                'status' => intval($v) == 1 ? 1 : 0
	            );
	        }
	        if(count($data) <= 0){
	            $this->ajaxReturn(array(
	                'status' => 0,
	                'msg'    => '请输入需要下架的商品'
	            ));die;
	        }
	        $result = D('Mall_goods')->statusChange($status_arr);
	        $this->ajaxReturn($result);
	    }
	}
	
	/*ajax删除商品*/
	public function goodsDelete(){
		if(IS_POST){
			$id     = I('id');
			$result = D('Mall_goods')->goodsDelete($id);
            $this->ajaxReturn($result);
		}
	}

	/*ajax删除商品相册*/
	public function goodsGalleryDelete(){
		if(IS_POST){
			$id     = I('gallery_id');
			$result = D('Mall_goods')->goodsGalleryDelete($id);
			$this->ajaxReturn($result);
		}
	}

    public function selectCategory(){
        $redis = new \Com\Redis(); 
        $categorys = $redis->get('mall_category' , 'array');
        foreach($categorys as $k =>$v){
            if($v['level'] > 2){
            	unset($categorys[$k]);
            }
        }
                /*M('Mall_category')->where(array('pid'=>9512))->save(array('level'=>3));
        	        Hook::add('updateCategory','Home\\Addons\\MallCategoryAddon');
	        Hook::listen('updateCategory'); */
        $categorys = get_child($categorys);

        $this->assign('categorys' , $categorys);
        $this->display();       
    }   

    public function getCategory(){
    	if(IS_POST){
    		$cat_id    = I('cat_id');
    		$categorys = M('Mall_category')
    		           ->where(array('pid'=>$cat_id))
    		           ->field("id,cat_name")
    		           ->select();
    		$this->ajaxReturn(array('status'=>1,'data'=>$categorys));
    	}
    }
    
    
	/*添加分类*/
	   public function show(){	
		   if(IS_AJAX){		     
			   $json = array('msg'=>'ok');			 
			   $catid = trim(I('post.cat'));
			   $data['name'] = trim(I('post.catname'));
			   $data['sort'] = trim(I('post.sort'));
			   $data['status'] = 1;
			   $data['time'] = time();
			   $data['member_id'] = $_SESSION['member_data']['id']; 
			   $fild = ('id,name');
			   $nameData = M('shop_category')->field($fild)->where(array('member_id'=>$data['member_id']))->select();
			   $arr = array();
			   foreach( $nameData as $k=>$v ){
				   $arr[$k] = $v['name'];				   
			   }
			   if( in_array($data['name'],$arr) ){				   
				    $this->ajaxReturn(array('msg'=>'onemore'));				   				   
			   }
			   
			   if( $catid==0 ){
				 $data['pid']=0;  				   
			   }else{
				  $pid = M('shop_category')->where(array('member_id'=>$data['member_id'],'id'=>$catid))->getField('pid'); 
				  $pid = intval($pid);				 
				  if( $pid !==0 ){
					  	 $this->ajaxReturn(array('msg'=>'notallow'));				  
					  
				  }else{
					 $data['pid'] = $catid; 					  
				  }				  
			   }
			   $addCat = M('shop_category')->add($data);
			   if( $addCat ){
				   $this->ajaxReturn(array('msg'=>'ok'));				   				   
			   }else{
				    $this->ajaxReturn(array('msg'=>'error'));				   
			   }			 
		    
		   }else{			   
				$member_id = $_SESSION['member_data']['id']; 			
				$shopnav = M('shop_category')->where(array('member_id'=>$member_id,'status'=>1))->order('sort asc')->select();		
			    $treeData = getLayer($shopnav);
				$this->assign('member_id',$member_id);
				$this->assign('treeData',$treeData);	  		   
				$this->display();  			   
		   }
	   }         
        public function selectcat(){		  
		   if(IS_AJAX){	   
               $member_id = $_SESSION['member_data']['id']; 
			   $fild = ('id,name,member_id');
			   $getData = M('shop_nav')->where(array('member_id'=>$member_id,'status'=>1))->order('time asc')->select();
			  
			   if( $getData  ){
				   $getData = json_encode($getData);	 
				   echo $getData;
						   
			   }
			  
		   }   
		}	
	/*修改分类*/	
		 public function showupdate(){		  
		  if(IS_AJAX){
			  $catname =  I('post.catname'); 
			  $catid = I('post.catid');
			  $rootid = I('post.rootid');
			  $rootsort = I('post.rootsort');
              $childid = I('post.childid');
			  $childname = I('post.childname');	
              $aid = I('post.aid');
              $childsort = I('post.childsort');
              $addid = I('post.addid');
              $addchild = I('post.addchild');
			  $asortid = I('post.asortid');
              $asortchild = I('post.asortchild');
			  $member_id = $_SESSION['member_data']['id']; 
			  if( $catname && $catid ){
				   $result = M('shop_category')->where(array('member_id'=>$member_id,'id'=>$catid))->setField('name',$catname);
				   if( $result ){
					   $this->ajaxReturn(array('msg'=>'ok'));					   
				   }else{
					   $this->ajaxReturn(array('msg'=>'error'));
				   }				  
			  }elseif( $rootid && $rootsort ){
				   $result1 = M('shop_category')->where(array('member_id'=>$member_id,'id'=>$rootid))->setField('sort',$rootsort);
				  	if( $result1 ){
					   $this->ajaxReturn(array('msg'=>'ok'));					   
				   }else{
					   $this->ajaxReturn(array('msg'=>'error'));
				   }	  
			  }elseif( $childid && $childname ){
				   $result2 = M('shop_category')->where(array('member_id'=>$member_id,'id'=>$childid))->setField('name',$childname);
				  	if( $result2 ){
					   $this->ajaxReturn(array('msg'=>'ok'));					   
				   }else{
					   $this->ajaxReturn(array('msg'=>'error'));
				   }	  
			  }elseif( $aid && $childsort ){
				   $result3 = M('shop_category')->where(array('member_id'=>$member_id,'id'=>$aid))->setField('sort',$childsort);
				  	if( $result3 ){
					   $this->ajaxReturn(array('msg'=>'ok'));					   
				   }else{
					   $this->ajaxReturn(array('msg'=>'error'));
				   }	  
			  }elseif( $addid && $addchild ){
				  $data['name'] = $addchild;
				  $data['pid'] = $addid;
				  $data['member_id'] = $member_id;
				  $data['time'] =time();
				  $data['status'] = 1;
				  $result4 = M('shop_category')->where(array('member_id'=>$member_id))->add($data);
				  	if( $result4 ){
					   $this->ajaxReturn(array('msg'=>'ok'));					   
				   }else{
					   $this->ajaxReturn(array('msg'=>'error'));
				   }	  
			  }
			  elseif( $asortid && $asortchild ){
				   $result5 = M('shop_category')->where(array('member_id'=>$member_id,'name'=>$asortid))->setField('sort',$asortchild);
				  	if( $result5 ){
					   $this->ajaxReturn(array('msg'=>'ok'));					   
				   }else{
					   $this->ajaxReturn(array('msg'=>'error'));
				   }	  
			  }else{
				   echo '<script>alert("非法提交数据!");history.back();</script>';
				  
			  }	
			  
		   }else{
			   echo '<script>alert("非法访问!");history.back();</script>';			   
		   }              		   
		}
/* 删除分类 */
        public function showdel(){		  
		   if(IS_AJAX){	   
               $catid = I('post.catid');
			   if( $catid ){
				    $member_id = $_SESSION['member_data']['id']; 
                 //   M('mall_goods')->where(array('member_id'=>$member_id,'cat_id'=>))					
				    $delData = M('shop_nav')->where(array('cat_id'=>$catid,'member_id'=>$member_id))->setField('status',0);
				    if( $delData ){
						$this->ajaxReturn(array('msg'=>'ok'));						
					}
			   }			  			  
		   }   
		}

/*添加商品导航*/
		 public function shownav(){	
          $member_id = $_SESSION['member_data']['id']; 	 
		   if(IS_AJAX){	               		   			  	
               $data['cat_id'] = I('post.catname');
			   if($data['cat_id']==0){
				   $this->ajaxReturn(array('msg'=>'none')); 
				   
			   }
			   $data['nav_name'] = M('shop_category')->where(array('member_id'=>$member_id,'id'=>$data['cat_id']))->getField('name');
               $rel = M('shopping')->where(array('member_id'=>$member_id))->select();			  			   
			   $arr = array();
			   if( $rel ){
				   foreach( $rel as $k=>$v ){
					   if( intval($v['status']) == 0){
						   continue;						   
					   }
					   $arr[$k] = $v['nav_name'];					   					   
				   }
				   if( in_array($data['nav_name'],$arr)){
					   $this->ajaxReturn(array('msg'=>'onemore'));
				   }				   
			   }
			   $data['sort']  = trim(I('post.sort'));
			   $data['member_id'] = $_SESSION['member_data']['id']; 
			   $data['status'] = 1;
			   $data['time'] = time();
              			   
			   $addCat = M('shopping')->add($data);
			   if( $addCat ){
				   $this->ajaxReturn(array('msg'=>'ok','catid'=>$addCat,'catname'=>$data['nav_name'],'sort'=>$data['sort'] ));				   				   
			   }else{
				    $this->ajaxReturn(array('msg'=>'error'));				   
			   }			 
		   }else{		    
			    $navs = M('shopping')->where(array('member_id'=>$member_id,'status'=>1))->order('rsort asc')->select();									
				$shopnavs = M('shop_category')->where(array('member_id'=>$member_id,'status'=>1))->order('sort asc')->select();		
			    $treeDatas = getList($shopnavs);				
				$this->assign('treeData',$treeDatas); 
                $this->assign('navs',$navs);				
				$this->display();  			   
		   }
	   }   
/*添加顶级分类*/
        public function navupdate(){
		  if(IS_AJAX){
			  $member_id = $_SESSION['member_data']['id'];
			  $catdata['name'] =  I('post.catname'); 			  
              $catdata['sort']= I('post.sort');
			  $catdata['pid'] = 0;
			  $catdata['member_id'] = $member_id;
			  $catdata['time'] =time();
			  $catdata['status'] = 1;
			  $results = M('shop_category')->add($catdata);	  
			  if( $results){
				  $this->ajaxReturn(array('msg'=>'ok'));					   
			  }				  						   
		   }else{
			   
			   $this->display();
		   }
          
		}
        
	/*删除导航*/	
		public function navdel(){		  
		   if(IS_AJAX){	   
               $catid = I('post.catid');
			   if( $catid ){
				    $member_id = $_SESSION['member_data']['id']; 
					$delData = M('shopping')->where(array('id'=>$catid,'member_id'=>$member_id))->setField('status',0);
					if( $delData ){
						$this->ajaxReturn(array('msg'=>'ok'));						
					}															   
			   }			  
			  
		   }   
		}
		
	/*导航设置*/	
		public function navManage(){		  
		   if(IS_AJAX){	
				 $member_id = $_SESSION['member_data']['id']; 
				 $rootid = I('post.rootid');		   
				 $rootsort = I('post.rootsort');
                 $arrstr = I('post.arrstr');
				 $sortids = I('post.sortids');
			 if( $rootid && $rootsort ){
				
				$delData = M('shopping')->where(array('cat_id'=>$rootid,'member_id'=>$member_id))->setField('rsort',$rootsort); 				 
				
				if( $delData ){
					$this->ajaxReturn(array('msg'=>'ok'));									
				} 
			 }
			 if( $arrstr ){				
				 $arr = explode(',',$arrstr);
				 if( $sortids ){
						$arrsort = explode(',',$sortids);
						if(in_array('-1',$arrsort)){
							M('shopping')->where(array('member_id'=>$member_id,'rsort'=>-1))->setField('status',1);												
						}else{
							M('shopping')->where(array('member_id'=>$member_id,'rsort'=>-1))->setField('status',0);						
						}
						
						     											 
				 }else{
						 M('shopping')->where(array('member_id'=>$member_id,'rsort'=>-1))->setField('status',0);						
						 M('shopping')->where(array('member_id'=>$member_id,'rsort'=>-2))->setField('status',0);											 
				 }
				 				 
				 $fild=('id,member_id,cat_id');
				 $catids = M('shopping')->field($fild)->where(array('member_id'=>$member_id,'status'=>1))->select();
				 $catarr = array();
                // $addarr = array();				 
				 foreach( $catids as $k=>$v ){										 
						 $catarr[$k] = $v['cat_id'];													 
				 }
				 foreach( $arr as $k3=>$v3 ){
				     if( !in_array( $v3,$catarr ) ){						 
						// $addarr[] = $v3 ;
						 $catdata =  M('shop_category')->where(array('member_id'=>$member_id,'id'=>$v3,'status'=>1))->find();
						 $navdata['cat_id'] = $v3;
						 $navdata['nav_name'] = $catdata['name'];
						 $navdata['member_id'] = $member_id;
						 $navdata['time'] = time();
						 $navdata['status'] = 1;
						 $navdata['rsort'] = 0;
						 $isset = M('shopping')->where(array('member_id'=>$member_id,'cat_id'=>$v3,'status'=>0))->find();
						 if( $isset ){
							 $resu = M('shopping')->where(array('member_id'=>$member_id,'cat_id'=>$v3,'status'=>0))->setField('status',1);
							 
						 }else{
							  $rel = M('shopping')->add($navdata);
						 }
					     
						 if( !$rel && $resu ==false){
							 $this->ajaxReturn(array('msg'=>'error'));  							  
						 }
						 
					 }							 	 
				 }
				 
				 foreach( $catarr as $k4=>$v4 ){
				     if( !in_array( $v4,$arr ) ){						 
						// $addarr[] = $v3 ;
						 $result =  M('shopping')->where(array('member_id'=>$member_id,'cat_id'=>$v4,'status'=>1))->setField('status',0);						 
					    						 
					 }							 	 
				 }
				 
				  $this->ajaxReturn(array('msg'=>'ok')); 
			 }
			 
		   }else{			   			   
			    $member_id = $_SESSION['member_data']['id'];   
                $shopnav = M('shop_category as a')
				->field('a.id,a.pid,a.member_id,a.name,a.sort,b.id as bid,b.member_id,b.rsort')
				->join('left join tp_shopping as b on a.id=b.cat_id')
				->where(array('a.member_id'=>$member_id))
				->select(); 
			    $treeData = getLayer($shopnav);	
               			
				$getcss = M('shop_nav_css')->where(array('member_id'=>$member_id))->getField('background_color');
				$this->assign('member_id',$member_id);
				$this->assign('getcss',$getcss);
				$this->assign('treeData',$treeData);
			   $this->display();
			   
		   }   
		}

         public function checkstatus(){		  
		   if(IS_AJAX){	   
               $check = I('post.check');
			   if( $check=='check' ){
				    $member_id = $_SESSION['member_data']['id']; 
					$Data = M('shopping')->where(array('member_id'=>$member_id, 'status'=>1))->select();
					$checkarr = array();
					if( $Data ){
						$sortarr = array();
						foreach( $Data as $k=>$v ){
							if( $v['cat_id'] ){
								$checkarr[]=$v['cat_id'];								
							}
							if( $v['rsort']<0 ){
								$sortarr[]=$v['rsort'];								
							}
							
						}
                        if( $sortarr !==array() ){
						    $sortstr = implode(',',$sortarr);								
						}						
                        $resstr = implode(',',$checkarr);	
                        $this->ajaxReturn(array('msg'=>'ok','resstr'=>$resstr,'sortstr'=>$sortstr));						
												
					}															   
			   }			  
			  
		   }   
		}
		
		 public function navcss(){		  
		   if(IS_AJAX){	
		      $color = I('post.color');
			  $member_id = $_SESSION['member_data']['id']; 
			  $rel = M('shop_nav_css')->where(array('member_id'=>$member_id))->setField('background_color',$color);
			  if( $rel ){
				  $this->ajaxReturn(array('msg'=>'ok'));
			  }
		   
           }   
		}

/*删除分类*/
		public function delcat(){		  
		   if( I('get.delid') ){
               $delid = I('get.delid');		   
		       $member_id = $_SESSION['member_data']['id'];
               $isset =  M('mall_goods')->where(array('shop_cat'=>$delid,'status'=>1))->find();
			   if( $isset ){
				   echo "<script>alert('该分类已经添加商品,请先将商品下架或更改分类后删除！');location.href='".U('Seller_goods/show')."' </script>"; 
				   header("location:".U('Seller_goods/show'));	
			   }else{
				   $rel =  M('shop_category')->where(array('member_id'=>$member_id))->delete($delid);
				   if( $rel ){
					 $aisset =  M('shopping')->where(array('member_id'=>$member_id,'cat_id'=>$delid ))->find();
					 if( $aisset ){
						 M('shopping')->where(array('member_id'=>$member_id,'cat_id'=>$delid ))->delete(); 
					 } 
					 echo "<script>alert('删除成功！');location.href='".U('Seller_goods/show')."' </script>";			   
				   }
			   
				  		   
			   }
		   
           }   
		}
		
        public function newgoods(){		  
		   if(IS_AJAX){	
		              
		          $json = array('msg'=>'ok');
				  $newarr = I('post.newarr');
				  $member_id = $_SESSION['member_data']['id']; 
				
				  $where['id']=array('in',$newarr);
				  $where['member_id']=$member_id;
				  $fild =('id,member_id,is_new');
				  $rel =  M('mall_goods')->field($fild)->where($where)->select();
				  foreach(  $rel as $k=>$v ){				  
					  $update = M('mall_goods')->where(array('member_id'=>$member_id,'id'=>$v['id']))->setField('is_new',1);
					  if( $update === false ){					 
						 $this->ajaxReturn(array('msg'=>'error'));
					  }
				  }			  			 			 
				$this->ajaxReturn($json );
			 }
		   
             
		}
		
    public function hotgoods(){		  
		   if(IS_AJAX){	
		              
		          $json = array('msg'=>'ok');
				  $hotarr = I('post.hotarr');
				  $member_id = $_SESSION['member_data']['id']; 
				
				  $where['id']=array('in',$hotarr);
				  $where['member_id']=$member_id;
				  $fild =('id,member_id,is_new');
				  $rel =  M('mall_goods')->field($fild)->where($where)->select();
				  foreach(  $rel as $k=>$v ){				  
					  $update = M('mall_goods')->where(array('member_id'=>$member_id,'id'=>$v['id']))->setField('is_new',2);
					  if( $update === false ){					 
						 $this->ajaxReturn(array('msg'=>'error'));
					  }
				  }			  			 			 
				$this->ajaxReturn($json );
			 }
		   
             
		}		
			  		
	  public function anewgoods(){		  
		   if(IS_AJAX){	               		   
				  $isnew = I('post.isnew');
				  if( $isnew ){
					  $member_id = $_SESSION['member_data']['id']; 								 
					  $where['member_id']=$member_id;
					  $where['is_new']=1;
					  $fild =('id,member_id,is_new');
					  $rel =  M('mall_goods')->field($fild)->where($where)->select();
					  $idarr = array();
					  foreach(  $rel as $v ){				  
						 $idarr['is_new'][]=$v['id'];
					  }                    													 
					  $map['member_id']=$member_id;
					  $map['is_new']=2;
					  $afild =('id,member_id,is_new');
					  $rels =  M('mall_goods')->field($afild)->where($map)->select();
					  $idarrs = array();
					  foreach(  $rels as $vs ){				  
						 $idarr['is_hot'][]=$vs['id'];
					  }		
					  
					  echo json_encode($idarr);	
                 }				  
			 }		               
		}

	   public function quxiaogoods(){		  
		   if(IS_AJAX){			              		         
				  $datas = I('post.quxiao');
				  if( $datas ){
					  $member_id = $_SESSION['member_data']['id']; 								 
					  $fild =('id,member_id,is_new');
					  $dataarr = explode(',',$datas);
					 
					  foreach( $dataarr as $va ){
						 $rerult =  M('mall_goods')->field($fild)->where(array('member_id'=>$member_id,'id'=>$va))->setField('is_new',0);
						 if( $rerult==false ){
							 $this->ajaxReturn(array('msg'=>'error'));							 
						 } 
					  }
					  $this->ajaxReturn(array('msg'=>'ok')); 
					 				  								  
				  }
			 }		               
	   }
	   //申请添加品牌类别
	  public function apply_brand(){
        $redis = new \Com\Redis(); 
        /*商城商品分类缓存   更新*/
        Hook::add('getCategory','Home\\Addons\\MallCategoryAddon');
        Hook::listen('getCategory');
        $categorys = $redis->get('mall_category' , 'array');
        foreach($categorys as $k =>$v){
            if($v['level'] > 2){
            	unset($categorys[$k]);
            }
        }
        $categorys = get_child($categorys);
        $this->assign('categorys' , $categorys);
        $this->display();       
    }
    public function reviseActivity(){
    	//营销活动产品修改显示
    	$data = I();
    	$id = $data['id'];
    	$member_id = $_SESSION['member_data']['id'];
    	if(empty($id)){
    		exit('该营销活动id不存在');
    	}
    	$rele = M('Release_activity')
    				->where(array('id'=>$id,'seller_id'=>$member_id))
    				->find();
    	$goods_ids = $rele['goods_id'];
    	$arr = explode(',', $goods_ids);
    	$number = count($arr);
    	$this->assign('number',$number);
    	$this->assign('goods',$arr);
    	$this->assign('data',$rele);
    	$this->display();
    } 
} 