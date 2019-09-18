<?php
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class CategoryController extends Controller {
  //更新缓存
  public function updateRedis(){
      if(IS_POST){
          $router_text = array();
          $c = M('Category')->select();              
          foreach($c as $k => $v){
              $regex = "/^{$v['router']}(.*)$/";
              if($v['pid']){
                  $router_text[$regex] = "Home/Categorys/goodsList?pid={$v['pid']}&cat_id={$v['id']}";
              }else{
                  $router_text[$regex] = "Home/Categorys/goodsList?pid={$v['id']}";
              }          
          }
          $c = M('Mall_category')->select();
          $in_array  = array(43 , 50);
          foreach($c as $k => $v){
              $regex = "/^{$v['router']}(.*)$/";
              if($v['pid']){
                  if(in_array($v['id'] , $in_array) || in_array($v['pid'] , $in_array)){
                      $router_text[$regex] = "Home/Tool/goodsList?pid={$v['pid']}&cat_id={$v['id']}";
                  }else{
                      $router_text[$regex] = "Home/MallCategorys/goodsList?pid={$v['pid']}&cat_id={$v['id']}";
                  }               
              }else{
                  if(in_array($v['id'] , $in_array) || in_array($v['pid'] , $in_array)){
                      $router_text[$regex] = "Home/Tool/goodsList?pid={$v['id']}";
                  }else{
                      $router_text[$regex] = "Home/MallCategorys/goodsList?pid={$v['id']}";
                  }
                  
              }
          }
          $router_text = json_encode($router_text);
          file_put_contents('./router.txt', $router_text);   
           $this->ajaxReturn(array('status'=>1,'msg'=>'ok'));     
      }
  }

  /**
   * 商品分类列表页
   * @access public  
   */ 
   public function categoryList(){ 
    /*
      $data = M('Mall_category')->where(array('pid'=>0))->field('id,pid')->select();
      foreach($data as $k => $v){
            $data_1 = M('Mall_category')->where(array('pid'=>$v['id']))->field('id,pid')->select();
            foreach($data_1 as $k1 => $v1){
              $data_2 = M('Mall_category')->where(array('pid'=>$v1['id']))->field('id,pid')->select();
              foreach($data_2 as $k2 => $v2){
                    $data_3 = M('Mall_category')->where(array('pid'=>$v2['id']))->field('id,pid')->select();
                foreach($data_3 as $k3 => $v3){
                   $arr[$k.$k1][] = $v3['id'];
                }
              }       
            }            
      }
      file_put_contents('four_cat.txt',serialize($arr));*/
      //$data = file_get_contents('four_cat.txt');
     // dump(unserialize($data));
	   	$data = D('Category')->getCategory();
	   	$list = tree_1($data);
	   	$this->assign('list' , $list);
	   	$this->display();
   }
   
   public function getCategory(){
       if(IS_AJAX){
           $data =  D('Category')->getCategory();
           $list = get_child($data);
           $this->ajaxReturn($list);
       }     
   }
   
   /*添加商品分类*/
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
				$data['index_thumb'] = $upload->rootPath.$info['index_thumb']['savepath'].$info['index_thumb']['savename'];//获取图片路径
			}	
			$data['filter_extendattr'] = implode(',',$data['filter_extendattr']);//扩展属性封成字符串
	   		$result                    = D('Category')->categoryAdd($data);
	   		$redis = new \Com\Redis();
	   		$redis->redis->delete('index_cattegory');
	   		$this->ajaxReturn($result);
	   	}else{
	   		$cat_id     = I('cat_id');
	   		$this->assign('cat_id' , $cat_id);
	   		$categorys  = D('Category')->getCategory();
	   		$this->assign('categorys' , tree_1($categorys));
	   	    /*商品类型*/
		   	$goods_type = D('Goods_type')->getGoodsType();
		   	$goods_type = tree_1($goods_type);
		   	$this->assign('goods_type' , $goods_type);
		   	$sort       = M('Category')->max('sort');
		   	$this->assign('sort' , $sort +1 );
		   	/*商品品牌*/
	   		$brands = M('Goods_brand')->field('id,brand_name')->select();
	   		$this->assign('brands' , $brands);
	   		$this->display();
	   	}
   }
   
   /*修改商品分类*/
   public function categoryUpdate(){
	   if(IS_POST){
	   		$data   = $_POST;	   		   			   		
	   	    //上传图片
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 220000;// 设置附件上传大小
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
			// 上传文件
			$info = $upload->upload();
			if($info) {// 上传错误提示错误信息
				if($info['cat_thumb']){
					$data['cat_thumb'] = $upload->rootPath.$info['cat_thumb']['savepath'].$info['cat_thumb']['savename'];//获取图片路径
				}
				if($info['index_thumb']){
				    $data['index_thumb'] = $upload->rootPath.$info['index_thumb']['savepath'].$info['index_thumb']['savename'];//获取图片路径
				}
			}	
			if($data['filter_extendattr']){
				$data['filter_extendattr'] = implode(',',$data['filter_extendattr']);//扩展属性封成字符串
			}
			$result = D('Category')->categoryUpdate($data);	
			$redis = new \Com\Redis();
			$redis->redis->delete('index_cattegory');
			$this->ajaxReturn($result);
	   	}else{
	   		$id       = I('id');
	   		$category = M('Category')->where(array('id'=>$id))->find(); 
	   		$this->assign('category' , $category);   		
	   		$extends  = M('Attrbute')
			          ->where(array('attr_id'=>array('in',$category['filter_extendattr'])))
			          ->select();
			$this->assign('filter_extendattr' , $extends);
	   		$categorys = M('Category')->select();
	   		$this->assign('categorys' , tree_1($categorys));	   		
	   		/*商品类型*/
	   		$goods_type = M('Goods_type')->select();
	   		$this->assign('goods_type' , tree_1($goods_type));
	   		/*商品品牌*/
	   		$brands = M('Goods_brand')->field('id,brand_name')->select();
	   		$this->assign('brands' , $brands);
	   		/*商品分类品牌*/
	   		$cat_brand_ = M('Category_brand')->where(array('cat_id'=>$id))->select();
	   		foreach ($cat_brand_ as $k => $v) {
	   			$cat_brand[] = $v['brand_id'];
	   		}
	   		$this->assign('cat_brand' , $cat_brand);
	   		$this->display();
	   	}
   }
   
   /*ajax删除商品分类*/
   public function categoryDelete(){
   	  if(IS_POST){
   	  	  $id     = I('id');
   	  	  $result = D('Category')->categoryDelete($id);
   	  	  $this->ajaxReturn($result);
   	  }
   }
   
   /*
	* 共享商品分类品牌同步到子类
	* */
   public function brandSonUpdate(){
   	  if(IS_POST){
   	  	  $cat_id = I('id');
   	  	  /*获取分类下的品牌*/
   	  	  $brand_ids = M('Category_brand')
   	  	             ->where(array('cat_id'=>$cat_id))
   	  	             ->field("brand_id")
   	  	             ->select();
   	  	  $values = array();
   	  	  //获取子集分类           
   	  	  $ids_   = M('Category')->where(array('pid'=>$cat_id))->field("id")->select();
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
          M('Category_brand')->where(array('cat_id'=>array('in',$ids)))->delete();
          if(count($brand_ids) <= 0){
          	  $result = array('status'=>1,'msg'=>'操作成功');      
              $this->ajaxReturn($result);
          }
          /*增加*/
          $sql = "INSERT INTO `tp_category_brand` ".'('.(implode(',',$fields)).') VALUES '.implode(',', $values);
          $r  = M()->execute($sql); 
          $result = array('status'=>1,'msg'=>'操作成功');  
          $redis  = new \Com\Redis(); 
          $redis->redis->delete('category_brands');    
          $this->ajaxReturn($result);
   	  }
   }  
   
   /*ajax更改显示状态*/
   public function statusChange(){
       if(IS_POST){
           $result=array(
               'status'=>'1',
               'msg' => 'ok'
           );
           $id = I('id');
           $status = I('status');
           $r = M('Category')->where(array('id'=>$id))->save(array('status'=>$status));
           if($r === false){
               $result=array(
                   'status'=>'0',
                   'msg' => '失败'
               );
           }
           $redis = new \Com\Redis();
           $redis->delete('index_mall_cattegory');
           $this->ajaxReturn($result);
       }
   }   
}
  	  /*
      $data = M('mall_category')->where(array('pid'=>151))->select();
      foreach($data as $v){
          $a = M('mall_category')->where(array('pid'=>$v['id']))->select();

          foreach($a as $v1){

          }
      }
      die;*/
          /* 
        $z = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T');
      $data = M('Mall_category')->where(array('pid'=>0))->field('id,pid')->select();
      foreach($data as $k => $v){
            $data_1 = M('Mall_category')->where(array('pid'=>$v['id']))->field('id,pid')->select();
            foreach($data_1 as $k1 => $v1){
              $data_2 = M('Mall_category')->where(array('pid'=>$v1['id']))->field('id,pid')->select();
              foreach($data_2 as $k2 => $v2){
                $n = $k2+1;
                    $n = $n < 10 ? '0'.$n : $n;
                    $data_3 = M('Mall_category')->where(array('pid'=>$v2['id']))->field('id,pid')->select();
                foreach($data_3 as $k3 => $v3){
                $n1 = $k3+1;
                    $n1 = $n1 < 10 ? '0'.$n1 : $n1; 
                    M('Mall_category')->where(array('id'=>$v3['id']))->save(array('code'=>$z[$k].$z[$k1].$n.$n1));
                }
                }       
            }            
      }
*/ 