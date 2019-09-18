<?php
//积分商城
namespace Admin\Controller;
use Think\Controller;
class IntegrationGoodsController extends Controller{
	public function goodsList(){
	   if(IS_AJAX){
	        $goods_ids = '';
	        $where = array();
	        if(intval(I('seller_id'))){
	            $where['member_id'] = intval(I('seller_id'));
	        }
	        if(intval(I('status'))){
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
	        $list     = M('Integration_goods')
			          ->order('id desc')
			          ->limit($firstRow,$listRows)
			          ->where($where)
			          ->select();
	        $this->ajaxReturn(array(
	        	'data'  => $list,
	        	'total' => M('Integration_goods')->where($where)->count()
	        ));
	    }else{
	       $seller_id = I('seller_id') ? intval(I('seller_id')) : '0';
	       $this->assign('seller_id' , $seller_id);
	       $this->display();	       
	    }
	}

    public function goodsAdd(){
	    if(IS_POST){
	   		$data = I();
	   		/*检测商城商品数据合法性*/
	   		$r    = D('IntegrationGoods')->checkGoodsData($data);
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
	        $result = D('IntegrationGoods')->goodsAdd($data , $goods_gallery);
	        $this->ajaxReturn($result);
	    }else{
	    	/*自动获取最大的排序*/
			$sort = M('Integration_goods')->max('sort');
			$this->sort=$sort?$sort++:'1';
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
			$r    = D('IntegrationGoods')->checkGoodsData($data , 2);
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
			}else
			if($upload->getError() != '没有文件被上传！'){
		       $this->ajaxReturn(array(
		           'status' => 0,
		           'msg'    => $upload->getError()		           
		       ));die;
		    }
			/*获取旧图*/
			if($data['goods_img']){
			    $old = M('Integration_goods')
			         ->where(array('id'=>$data['id']))
			         ->field('goods_thumb,goods_img')
			         ->find();
			}
            $result = D('IntegrationGoods')->goodsUpdate($data);
            /*删除旧图*/
            if($result['status']){
                if($data['goods_img']){
                    unlink($old['goods_thumb'],$old['goods_img']);
                }
            }
			$this->ajaxReturn($result);
		}else{
			$id        = I('id');
			$goods     = M('Integration_goods')->where(array('id'=>$id))->find();//取出商品
			$this->assign('goods' , $goods);
			$this->display();
		}
	}

	/*ajax删除商品*/
	public function goodsDelete(){
		if(IS_POST){
			$id     = I('id');
			$result = D('IntegrationGoods')->goodsDelete($id);
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
	        $r = M('Integration_goods')
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

	/*商品批量排序*/
	public function sortChange(){
	    if(IS_AJAX){
	        $data   = I();
	        $result = D('IntegrationGoods')->sortAllChange($data);
	        $this->ajaxReturn($result);
	    }
	}
}