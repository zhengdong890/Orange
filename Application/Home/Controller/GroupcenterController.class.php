<?php
namespace Home\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class GroupcenterController extends Controller {

   public function goodsAdd(){
   	 
       $this->display();
   }

       //商品修改页
    public function edit(){
        //$id = I('id');
		$id = 1;
        $goodsData = M('group_img') -> find($id);
       
        $this -> assign('goods',$goodsData);
        $this -> display();
    }
   
   
   
   
    public function insert(){
	 
	 $msg = '商品添加成功<br/>';//定义提示信息
        //定义主图上传参数
        $upload = new \Think\Upload();
        $upload -> maxSize = 1024000000;
        $upload -> autoSub = false;
        $upload -> exts = array('jpg','gif','png','jpeg');
        $upload -> rootPath = "Public/";
        $upload -> savePath = 'Uploads/'.date('Ym').'/';//主图保存地址
        $info = $upload -> uploadOne($_FILES['pic']);//上传图片
        if(!$info){
            $this -> error($upload -> getError());//如果图片上传失败获取失败信息
        }
        //图片缩放
        $file = "Public/".$info['savepath'].$info['savename'];
	
        $image = new \Think\Image();
        $image ->open($file);
        $thumbName = "Public/".$info['savepath']."thumb_".$info['savename'];
		
        $image -> thumb(220, 282)->save($thumbName ); 
       
        
		
		//添加商品表 
        $goods = M('group_img');
        //自定义组装添加数据,防止create()转义html标签
        $good['title'] = $_POST['theme']; 
		$good['state'] = 0;   
        $good['goodsname'] = $_POST['name'];
        $good['goodsprice'] = $_POST['price'];
        $good['pic'] = '/'.$file;
		$good['pic_thumb'] = '/'.$thumbName;
        $good['descr'] = $_POST['editorValue'];
        $good['addtime'] = time();
        //添加到数据库并判断结果，错误返回错误信息
        $goods_id = $goods -> add($good);//添加成功返回商品ID
        if(!$goods_id){
            $this -> error('商品添加失败！');
        }
      
	 if(!$_POST['imagesName']){
            $this -> success($msg);//判断是否有描述图
        }
        $goodspic = M('group_spics');
        $imgs['goods_id'] = $goods_id;//组合添加数据
        $imgsName = explode(',',$_POST['imagesName']);//拆分描述图地址为数组
        //循环遍历添加到数据库
        foreach($imgsName as $k => $v){
            $imgs['pic'] = $v;
            $insertId = $goodspic -> add($imgs);
            //判断是否添加成功，返回错误信息
            if(!$insertId){
                $msg .= '第'.($k + 1).'张图片上传失败<br/>';
            }
        }
        //$this -> redirect('add', '', 2, $msg);//跳转到商品属性添加页
        $this -> success($msg);//跳转到商品属性添加页     
   }
   
   
     public function update(){
       $id = I('id'); 	
$id = 7;	   
        $goods = M('group_img');//实例化商品表
        $descr = $goods -> field('descr,pic') -> find($id);//查询商品描述、主图
        //判断是否有修改主图
	
			if(!$_FILES['pic']['name']){
					$good['pic'] = $descr['pic'];
						
			} else {
				
					$upload = new \Think\Upload();
					$upload -> maxSize = 1024000000;
					$upload -> autoSub = false;
					$upload -> exts = array('jpg','gif','png','jpeg');
					$upload -> rootPath = "Public/";
					$upload -> savePath = 'Uploads/'.date('Ym').'/';//主图保存地址
					$info = $upload -> uploadOne($_FILES['pic']);//上传图片
					
					if(!$info){
						$this -> error($upload -> getError());//如果图片上传失败获取失败信息
					}
			
					//图片缩放
					$filename = "Public/".$info['savepath'].$info['savename'];
				
					$image = new \Think\Image();
					$image ->open($filename);
					$thumbNames = "Public/".$info['savepath']."thumb_".$info['savename'];					
					$image -> thumb(220, 282)->save("Public/".$info['savepath']."thumb_".$info['savename']); 
					
					
					@unlink($descr['pic']);
					@unlink($descr['pic_thumb']); 
								
			}
			$good['id'] = $id;			
			$good['title'] = $_POST['theme']; 
			$good['state'] = 0;	
			$good['goodsname'] = $_POST['name'];
			$good['goodsprice'] = $_POST['price'];
			$good['pic'] = '/'.$filename;
			$good['pic_thumb'] = '/'.$thumbNames;
			$good['descr'] = $_POST['editorValue'];
			$good['addtime'] = time();		
			if(!$goods->save($good)){
            $this -> error('修改失败！');
             }

			if($good['descr'] == $descr['descr']){
            $this -> success('修改成功！');
            exit;
            }
		
            $msg = '商品修改成功<br/>';//定义提示信息
        //判断是否有描述图
          if(!$_POST['imagesName']){
            $this -> success($msg);
            exit;
           }
		   
		$goodspic = M('group_spics');
        $data = $goodspic -> where("goods_id = {$id}") -> select();
        //遍历得到商品图库
        foreach($data as $v){
            $pics[] = $v['pic'];
        }
        $imgs['goods_id'] = $id;//组合添加数据
        $imgsName = explode(',',$_POST['imagesName']);//拆分描述图名称为数组
        //循环遍历添加到数据库
        foreach($imgsName as $k => $v){
            if(!in_array($v ,$pics)){
                $imgs['pic'] = $v;
                $insertId = $goodspic -> add($imgs);
                //判断是否添加成功，返回错误信息
                if(!$insertId){
                    $msg .= '第'.($k + 1).'张图片上传失败<br/>';
                }
            }
        }
        $this -> success($msg);
			
		}		
   
   
   
   
   
   
   
   
}