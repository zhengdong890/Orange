<?php
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class ShopEditController extends Controller {
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

   /**
    * 修改店铺信息
    */
    public function shopSetting(){
    	if(IS_AJAX){
            $data            = I();			
            $data['member_id'] = $_SESSION['member_data']['id'];
             //上传图片
	        $upload           = new \Think\Upload();// 实例化上传类
	        $upload->maxSize  = 3145728;// 设置附件上传大小
	        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	        $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
	        // 上传文件
	        $info = $upload->upload();
	        if($info){
	            if($info['logo']){//获取缩略图片路径           
	                $data['thumb'] = $upload->rootPath.$info['logo']['savepath'].$info['logo']['savename'];
	            }              
	        }
			
            $result            = D('Shop')->shopSetting($data); 
			
            $this->ajaxReturn($result);
    	}else{
			$data['member_id'] = $_SESSION['member_data']['id'];
			
    		$data = M('shop_data')->where(array('member_id'=>$_SESSION['member_data']['id']))->find();
    		$area = M('Area')->where(array('area_level'=>1))->field('area_no,area_name')->select();
			$this->assign('member_id' , $data['member_id']);
    		$this->assign('data' , $data);
    		$this->assign('area' , $area);
    		$this->display();
    	}      
    }
   /**
    * 店铺装修
    */
    public function shopDecorate(){	
	    $member_id = $_SESSION['member_data']['id'];
		if($_GET){			
			$color = I('get.background_color');	         			        
						
        	$data = M('shop_nav_css')->where(array('member_id'=>$member_id))->setField('background_color',$color);			
			if( $data ){
				echo '<script>alert("修改成功！");</script>';				
			}       
    	}		
		$navs = M('shopping')->where(array('member_id'=>$member_id,'status'=>1))->order('rsort asc')->select();									
		$shopnavs = M('shop_category')->where(array('member_id'=>$member_id,'status'=>1))->order('sort asc')->select();		
		$treeDatas = getList($shopnavs);				
		$this->assign('treeData',$treeDatas); 
		$this->assign('navs',$navs);				 
		$this->display();  			
		 
    }
    
    /**
     * 服务与会员页编辑
     */
    public function service(){
        if(IS_POST){
            $data = I();          
            $member_id = $_SESSION['member_data']['id'];
            $shop_id =M('Shop_view')->where(array('seller_id'=>$member_id))->find();//查询是否存在有店铺
            if(empty($shop_id)){
                $data = array(
                        'seller_id' =>$member_id,
                        'content'   =>$data['editorValue'],
                        'type_id'   =>1
                    );
                $shop_des=M('Shop_view')->add($data);
            }else{

                $map = array(
                        'content'   =>$data['editorValue'],
                        'type_id'   =>1
                    );
                $shop_des=M('Shop_view')
                            ->where(array('seller_id'=>$member_id))
                            ->save($map);
            }
            if($shop_des){
                echo "<script>alert('提交成功')</script>";
             }else{
                header("Location:http://www.orangesha.com/index.php?m=Home&c=ShopEdit&a=service");
             }          
            //echo json_encode($result);
        }
        $member_id = $_SESSION['member_data']['id'];
        $shop_des =M('Shop_view')->where(array('seller_id'=>$member_id))->find();
        $this->assign('shop_des',$shop_des);
        $this->display();

    }
    
    public function getService(){
        //if(IS_POST){
            $data = M('Shop_view')
                  ->where(array('seller_id'=>$_SESSION['member_data']['id']))
                  ->field('id,content,type_id')
                  ->select();
            echo json_encode(array('data'=>$data,'status'=>1));
        //}
    }
    
   /**
    * 店铺信息修改
    */    
    public function saveShopData(){
    	if(IS_AJAX){
            $data              = I();
		
            $data['member_id'] = $_SESSION['member_data']['id'];
             //上传图片
	        $upload           = new \Think\Upload();// 实例化上传类
	        $upload->maxSize  = 3145728 ;// 设置附件上传大小
	        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	        $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
	        // 上传文件
	        $info = $upload->upload();
	        if($info){
	            if($info['thumb']){//获取缩略图片路径           
	                $data['thumb'] = $upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];
	            }              
	        }
            $result  = D('Shop')->shopDataUpdate($data); 
            $this->ajaxReturn($result);
    	}
    }

   /**
    * 获取店铺信息
    */    
    public function getShopData(){
    	if(IS_AJAX){
    		$member_id = $_SESSION['member_data']['id'];
            $shop_data = M('Shop_data')->where(array('member_id'=>$member_id))->find();
            $result    = array(
            	'status' => 1,
                'data'   => $shop_data
            ); 
            $this->ajaxReturn($result);
    	}
    }
	

   /**
    * 修改导航
    */    
public function shopNavUpdate(){
    if(I('post')){
		$navdata = I('post');
		$navdata = json_encode($navdata);
 			
		
		
		
		
	}
	
	
	
}
	
	
	
   /**
    * 获取导航
    */    

   /**
    * 获取店铺商品分类
    */    

   /**
    * 修改店铺banner
    */    
    public function shopBannerUpdate(){
        if(IS_POST){
        	$data = array();
        	//上传图片
	        $upload           = new \Think\Upload();// 实例化上传类
	        $upload->maxSize  = 3145728 ;// 设置附件上传大小
	        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	        $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
	        // 上传文件
	        $info = $upload->upload();
	        if($info){
	            if($info['thumb']){//获取缩略图片路径           
	                $data['thumb'] = $upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];
	            }              
	        }     
    		$member_id = $_SESSION['member_data']['id'];
            $r = M('Shop_banner')->where(array('member_id'=>$member_id))->save($data);
			if( $r!==false ){
				$thumb = M('shop_banner')->where(array('member_id'=>$member_id))->getField('thumb');
				$thumb = substr($thumb,1);
			}
            $result = array(
                'status' => 1,
                'msg'    => '更新成功',
				'pic'    => $thumb
            );
            if($r === false){
            	$result = array(
                    'status' => 0,
                    'msg'    => '更新失败'
            	);
            }
            //$this->ajaxReturn($result);
            
    	}
        $this->redirect('Seller_order/sellerIndex');


        
    }

   /**
    * 获取店铺banner
    */    
    public function getShopBanner(){
        if(IS_AJAX){
        	$member_id = $_SESSION['member_data']['id'];
        	$data = M('Shop_banner')->where(array('member_id'=>$member_id))->find();
            $this->ajaxReturn($data);
    	}
    }

}