<?php
/*
 * 商城商品
 * */
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class Member_goodsController extends Controller {
    public function _initialize(){       
        if(empty($_SESSION['member_data'])){
            $this->redirect('Member/login');
        }
        $id = $_SESSION['member_data']['id'];  
        $redis = new \Com\Redis();       
        /*订单数量统计处理*/
        Hook::add('totalOrder','Home\\Addons\\TotalAddon');
        Hook::listen('totalOrder',$id);
        /*购物车统计处理*/
        Hook::add('totalCart','Home\\Addons\\TotalAddon');
        Hook::listen('totalCart',$id);    
        /*底部帮助*/
        Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
        Hook::listen('getFooterHelp');
        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
        $this->assign('help' , $help);
        $this->assign('order_total' , $_SESSION['order_total']);
        $this->assign('cart_total' , $_SESSION['cart_total']);
    }

    /* 
     * 共享商品列表
     * */
    public function goodsList(){
       if(IS_POST){
           $data     = I();
       	   $firstRow = $data['firstRow'];
       	   $listRows = $data['listRows'];
	       $goods = M('Goods')
	              ->order('id desc')
	              ->where(array('member_id'=>$_SESSION['member_data']['id']))
	              ->limit($firstRow,$listRows)
	              ->select();
	       $count  = M('Goods')->where(array('member_id'=>$_SESSION['member_data']['id']))->count();
	       $result = array(
	            'data'      => $goods,
	            'totalRows' => $count
	       );  
	       echo json_encode($result);     
       }else{
	       $goods = M('Goods')
	              ->order('id desc')
	              ->where(array('member_id'=>$_SESSION['member_data']['id']))
	              ->select(); 
	   	   $this->assign('goods' , $goods);
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
			if(intval($data['rent_dw']) != 4){
			    $r = D('Goods')->checkRent($data['min_rent'] , $data['max_rent'] ,$rent);
			    if(!$r['status']){
			        $this->ajaxReturn($r);
			    }			    
			}			
		    //上传图片
	        $upload           = new \Think\Upload();// 实例化上传类
	        $upload->maxSize  = 745728 ;// 设置附件上传大小
	        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
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
	    	$cat_id = I('cat_id');
	    	if($cat_id){
				$category   = M('Category')->where(array('id'=>$cat_id))->find();
				$p_name     = M('Category')->where(array('id'=>$category['pid']))->getField('cat_name');
				//商品品牌
			    $brands = M('Category_brand as a')
			        ->join("tp_goods_brand as b on a.brand_id=b.id")
			        ->field("b.*")
			        ->where(array('cat_id'=>$cat_id))
			        ->select();
			    $this->assign('brands' , $brands);
				$this->assign('category' , $category);
				$this->assign('p_name' , $p_name);
				$this->display();
	    	}     		
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
			if(intval($data['rent_dw']) != 4){
			    $r = D('Goods')->checkRent($data['min_rent'] , $data['max_rent'] ,$rent);
			    if(!$r['status']){
			        $this->ajaxReturn($r);
			    }
			}
			//上传图片
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 800000 ;// 设置附件上传大小
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
			$id         = I('id');
			$goods      = M('Goods')->where(array('id'=>$id))->find();//取出商品
			$goods_data = M('Goods_data')->where(array('goods_id'=>$id))->find();
			$categorys  = M('Category')->select();
			$goods_gallery = M('Goods_gallery')->where(array('goods_id'=>$id))->select();//取出商品相册	
			/*扩展属性*/		
			//$extendattr  = D('Goods')->get_extendattr($id,$goods['cat_filter_id']);	
			$goods_rent  = M("Goods_rent")->where(array('goods_id'=>$id))->select();		
			$goods_rent_ = $goods_rent?$goods_rent:array('min_rent'=>1,'max_rent'=>99999);
			//商品品牌
			$brands = M('Category_brand as a')
			        ->join("tp_goods_brand as b on a.brand_id=b.id")
			        ->field("b.*")
			        ->where(array('cat_id'=>$goods['cat_id']))
			        ->select();
			//商品未通过审核
			if($goods['is_check'] == '1' && $goods['check_status'] == '0'){
			    $goods['check_content'] = M('Goods_check')->where(array('goods_id'=>$id))->getField('content');    
			}
			$this->assign('goods' , $goods);
			$this->assign('goods_json' , json_encode($goods));
			$this->assign('brands' , $brands);
			$this->assign('goods_data' , $goods_data);
			$this->assign('goods_gallery' , $goods_gallery);
			$this->assign('gallery_number' , 4-count($goods_gallery));
			$this->assign('categorys' , tree_1($categorys));
			$this->assign('goods_rent' , $goods_rent);
			//$this->assign('extendattr' , $extendattr);
			$this->assign('goods_rent_json' , json_encode($goods_rent_,JSON_FORCE_OBJECT));
			$this->display();
		}
	}
	
	/*ajax删除商品*/
	public function goodsDelete(){
		if(IS_POST){
			$id     = I('id');
			$result = D('Goods')->goodsDelete($id);
            $this->ajaxReturn($result);
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
	        $result = D('Goods')->statusChange($status_arr);
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

    public function selectCategory(){
    	$categorys = M('Category')->where(array('pid'=>0))->select();
    	$this->assign('categorys' , $categorys);
        $this->display();       
    }   

    public function getCategory(){
    	if(IS_POST){
    		$cat_id    = I('cat_id');
    		$categorys = M('Category')
    		           ->where(array('pid'=>$cat_id))
    		           ->field("id,cat_name")
    		           ->select();
    		$this->ajaxReturn(array('status'=>1,'data'=>$categorys));
    	}
    }    
}