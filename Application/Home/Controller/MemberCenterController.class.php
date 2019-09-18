<?php
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
use Org\Msg\SendMsg;
header("content-type:text/html;charset=utf-8");
class MemberCenterController extends Controller {    
    public function _initialize(){       
        if(empty($_SESSION['member_data'])){
            header("Location:http://www.orangesha.com/login.html");
        }
        $id = $_SESSION['member_data']['id'];  
        $redis = new \Com\Redis();       
        unset($_SESSION['order_total']);
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
     * 个人中心页
     * */
    public function member(){
    	session_start();
    	$id = $_SESSION['member_data']['id'];
    	if(!empty($_SESSION['member_data'])){
            $member_data = $_SESSION['member_data'];
    	}else{
     	    $member_data             = D('Member_center')->getMemberData($_SESSION['member_id']); 
     	    $_SESSION['member_data'] = $member_data;		
    	}
        $member_id = $member_data['id'];
        $mem = M('Member')->where(array('id'=>$member_id))->find();
    	/*默认收货地址*/
    	$address = M('Member_address')->where(array('member_id'=>$id,'is_use'=>1))->find();
        //我喜欢的商品
        $count = M('Goods_collect')->where(array('member_id'=>$member_id))->count();
        $this->assign('num',$count);
        $this->assign('mem',$mem);
    	$this->assign('address',$address);
    	$this->assign('member_data',$member_data);
        $this->display();       
    }
    
    /*
     * 获取用户的信息
     * */
    public function getMemberData(){
        if(IS_AJAX){
            $id = $_SESSION['member_data']['id'];
            $r  = M('Member_security')->where(array('member_id'=>$id))->getField('id');
            $data = array(
                'email'       => $_SESSION['member_data']['email'],
                'telnum'      => $_SESSION['member_data']['username'],
                'is_identity' => $_SESSION['member_data']['is_identity'],
                'is_security' => $r?1:0
            );
            $this->ajaxReturn(array('status'=>1,'msg'=>$data));
        }
        
    }
    
    /* 
     * 会员信息编辑
     * */
    public function memberData(){
    	if(IS_AJAX){
		    $result = array(
                'status' => 1,
                'msg'    => '修改成功'
        	);
            $data = I();
            $member_data = array(
               'nickname' => $data['nickname'],
               'sex'      => $data['sex'],
               'email'    => $data['email'],
               'qq'       => $data['qq'],
               'zuoji'    => $data['zuoji']
	        );
		    //上传图片
	        $upload           = new \Think\Upload();// 实例化上传类
	        $upload->maxSize  = 10145728 ;// 设置附件上传大小
	        $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	        $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
	        // 上传文件
	        $info = $upload->upload();
	        if($info) {
	            if($info['headimg']){//获取缩略图片路径           
	                $member_data['headimg'] = $upload->rootPath.$info['headimg']['savepath'].$info['headimg']['savename'];
	            }	            
	        }
	        $id = $_SESSION['member_data']['id'];
            $r = M('Member_data')->where(array('member_id'=>$id))->save($member_data);    
            if($r === false){
            	$result = array(
                    'status' => 0,
                    'msg'    => '修改失败'
            	);
            }else{
            	$_SESSION['member_data']['nickname'] = $member_data['nickname'];
            	$_SESSION['member_data']['sex']      = $member_data['sex'];
            	$_SESSION['member_data']['email']    = $member_data['email'];
            	$_SESSION['member_data']['qq']       = $member_data['qq'];
            	$_SESSION['member_data']['zuoji']    = $member_data['zuoji'];
            	if($member_data['headimg']){
            		$_SESSION['member_data']['headimg']  = $member_data['headimg'];
            	}           	
            }
            $this->ajaxReturn($result);
    	}else{
	    	session_start();
	    	if(!empty($_SESSION['member_data'])){
	            $member_data = $_SESSION['member_data'];
	    	}else{
	     	    $member_data             = D('Member_center')->getMemberData($_SESSION['member_id']); 
	     	    $_SESSION['member_data'] = $member_data;		
	    	}
	    	$this->assign('member_data',$member_data);
    		$this->display();  
    	}  	 
    }

    /* 
     * ajax获取地区数据
     * */
    public function getArea(){
	   if(IS_AJAX){
	   		$data = $_POST;
	   		$area = M('Area')
	   		      ->where(array('parent_no'=>$data['area_no']))
	   		      ->field('area_no,area_name,id')
	   		      ->select();
	   		$result="<option value='0'>请选择...</option>";
	   		$this->ajaxReturn($area);
	   }
   }

    /* 
     * 账户安全
     * */
    public function accountSafety(){
        $member_id  = $_SESSION['member_data']['id'];
        $_SESSION['member_data']['is_identity'] = M('Member')->where(array('id'=>$member_id))->getField('is_identity');
        $_SESSION['member_data']['email'] = M('Member_data')->where(array('member_id'=>$member_id))->getField('email');
        $this->display(); 
    }

    /* 
     * 企业认证
     * */
    public function businessQualification(){
        if(IS_POST){
           $member_id  = $_SESSION['member_data']['id'];
	       $data   = I();
	       $r = M('Businesses_application')->where(array('member_id'=>$member_id))->find();
	       if(!$r['check_status'] && count($r)>0){
	           $this->ajaxReturn(array(
	               'status' => 0,
	               'msg'    => '您提交的申请正在审核中,请耐心等待'
	           ));
	           die;
	       }else 	       
	       if($r['status']){
	           $this->ajaxReturn(array(
	               'status' => 0,
	               'msg'    => '您已经认证过了'
	           ));
	           die;
	       }
   	       /*上传图片*/
		   $upload = new \Think\Upload();// 实例化上传类
		   $upload->maxSize = 3145728 ;// 设置附件上传大小
		   $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		   $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
		   // 上传文件
		   $info = $upload->upload();
		   if($info) {
		        if($info['bus_lice']){
		            $data['bus_lice'] = $upload->rootPath.$info['bus_lice']['savepath'].$info['bus_lice']['savename'];
		        }
		        if($info['permit']){
                    $data['permit'] = $upload->rootPath.$info['permit']['savepath'].$info['permit']['savename'];
		        }
		        if($info['code']){
		            $data['code'] = $upload->rootPath.$info['code']['savepath'].$info['code']['savename'];
		        }
		        if($info['registration']){
		            $data['registration'] = $upload->rootPath.$info['registration']['savepath'].$info['registration']['savename'];
		        }			        
		   }else{
		       $this->ajaxReturn(array(
		           'status' => 0,
		           'msg'=>$upload->getError()		           
		       ));die;
		   }
           $data['member_id'] = $member_id;
           if(count($r)>0){
               $result = D('Businesses')->Update($data);
           }else{
               $result = D('Businesses')->addData($data);
           } 
           if(!$result['status']){
               unlink($data['bus_lice'],$data['permit'],$data['code'],$data['registration']);    
           }else{
               if($data['bus_lice']){
                   unlink($r['bus_lice']);
               }
               if($data['permit']){
                   unlink($r['permit']);
               }
               if($data['code']){
                   unlink($r['code']);
               }
               if($data['registration']){
                   unlink($r['registration']);
               }
           }
           $this->ajaxReturn($result);
	    }else{
	    	$this->display(); 
	    }       
    }
    
    /* 
     * 获取企业认证信息
     * */
    public function getQualification(){
        if(IS_AJAX){
            $result = array(
                'status' => 1,
                'msg'    => 'success'
            );
            $member_id   = $_SESSION['member_data']['id'];
            $is_renzheng = M('Member')->where(array('id'=>$member_id))->getField('is_renzheng'); 
            $data = M('Businesses_application')->where(array('member_id'=>$member_id))->find();
            if($is_renzheng){                               
                $data['renzheng_status'] = 3;//审核通过
            }else{
                if(!$data['member_id']){
                    $data['renzheng_status'] = 0;//未申请
                }else
                if($data['status'] == 0 && $data['check_status'] == 1){
                    $data['renzheng_status'] = 2;//审核未通过
                }else   
                if($data['status'] == 0 && $data['check_status'] == 0){
                    $data['renzheng_status'] = 1;//正在审核
                }
            }
            $result['data'] = $data;
            $this->ajaxReturn($result);
        }
    }
    
    /*
     * 购物车
     * */
    public function cart(){
        $this->display();
    }
    
    /*
     * 共享商品评论
     * */    
    public function goodsComment(){
        if(IS_POST){
            $data       = I();
            $id         = $data['id'];unset($data['id']);
            $member_id  = $_SESSION['member_data']['id'];
            $order_data = M('Order_data')->where(array('id'=>$id,'member_id'=>$member_id))->find();
            if($order_data['send_status'] != 2){
                $result = array('status'=>0,'msg'=>'该订单状态不允许评论');
                $this->ajaxReturn($result);die;
            }
            if($order_data['is_comment'] == 1){
                $result = array('status'=>0,'msg'=>'该商品已经评论');
                $this->ajaxReturn($result);die;
            }
            $data['goods_id']  = $order_data['goods_id']; 
            $data['member_id'] = $member_id;
            /*上传图片*/
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728 ;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
            // 上传文件
            $info = $upload->upload();
            if($info) {
                $data['thumb'] = $upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];
            }                        
            $result = D('GoodsComment')->commentAdd($id , $data);
            $this->ajaxReturn($result);
        }else{
            $member_id = $_SESSION['member_data']['id'];
            $id = I('id');
            $data = M('Order_data')->where(array('id'=>$id,'member_id'=>$member_id))->find();
            if($data['send_status'] != 2){
                exit('该订单状态不允许评论');
            }
            $shop_name = M('Shop_data')->where(array('member_id'=>$data['seller_id']))->getField('shop_name');
            $this->assign('data',$data);
            $this->assign('shop_name',$shop_name);
            $this->display();            
        }
    }
    
    /*
     * 商城商品评论
     * */
    public function MallgoodsComment(){
        if(IS_POST){
            $data       = I();
            $id         = $data['id'];unset($data['id']);
            $member_id  = $_SESSION['member_data']['id'];
            $order_data = M('Mall_order_data')->where(array('id'=>$id,'member_id'=>$member_id))->find();
            if($order_data['send_status'] != 2){
                $result = array('status'=>0,'msg'=>'该订单状态不允许评论');
                $this->ajaxReturn($result);die;
            }
            if($order_data['is_comment'] == 1){
                $result = array('status'=>0,'msg'=>'该商品已经评论');
                $this->ajaxReturn($result);die;
            }
            $seller_id         = $order_data['seller_id']; 
            $data['goods_id']  = $order_data['goods_id']; 
            $data['member_id'] = $member_id;
            /*上传图片*/
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 3145728 ;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
            // 上传文件
            $info = $upload->upload();
            if($info) {
                $data['thumb'] = $upload->rootPath.$info['thumb']['savepath'].$info['thumb']['savename'];
            }                        
            $result = D('MallGoodsComment')->commentAdd($id , $data);
            if($result['status']){
            	$redis = new \Com\Redis();   
                $redis->redis->delete('shop_data'.$seller_id);
            }
            $this->ajaxReturn($result);
        }else{
            $member_id = $_SESSION['member_data']['id'];
            $id = I('id');
            $data = M('Mall_order_data')->where(array('id'=>$id,'member_id'=>$member_id))->find();
            if($data['send_status'] != 2){
                exit('该订单状态不允许评论');
            }
            $shop_name = M('Shop_data')->where(array('member_id'=>$data['seller_id']))->getField('shop_name');
            $this->assign('data',$data);
            $this->assign('shop_name',$shop_name);
            $this->display();
        }
    }  
    
    /*
     * 商城申请
     * */    
    public function mallApplication(){
        if(IS_POST){
            $member_id = $_SESSION['member_data']['id'];
            $r = M('Member')->where(array('id'=>$member_id))->Field('is_renzheng,is_mall')->find();
            if(!$r['is_renzheng']){
                $this->ajaxReturn(array('status'=>0,'msg'=>'请先进行企业认证','code'=>1));
            }
            if($r['is_mall']){
                $this->ajaxReturn(array('status'=>0,'msg'=>'您已经申请成功了','code'=>4));
            }
            $r = M('Mall_application')->where(array('seller_id'=>$member_id,'is_check'=>'0'))->find();
            if($r){
                $this->ajaxReturn(array('status'=>1,'msg'=>'您已经提交过申请了,请耐心等待','code'=>5));
                die;
            }
            $r = M('Mall_application')->add(array('seller_id'=>$member_id,'time'=>time()));
            if($r === false){
                $this->ajaxReturn(array('status'=>0,'msg'=>'提交申请失败','code'=>2));
            }
            $this->ajaxReturn(array('status'=>1,'msg'=>'提交申请成功,请等待审核结果','code'=>3));
        }        
    }

    /*
     * 获取卖家商城id
     * */
    public function getMallData(){
        if(IS_POST){
            $member_id = $_SESSION['member_data']['id'];
            $r = M('Member')->where(array('id'=>$member_id))->getField('is_mall');
            if(!$r){
                $time = M('Mall_application')->where(array('seller_id'=>$member_id))->getField('time');
                if($time){
                    $time = date('Y年m月d日  H:i:s',$time + 2 * 24 * 3600);
                    $this->ajaxReturn(array('status'=>1,'msg'=>'申请正在审核中,请耐心等待','code'=>2,'time'=>$time));
                }
                $this->ajaxReturn(array('status'=>1,'msg'=>'请先申请商城','code'=>1));
            }
            $this->ajaxReturn(array('status'=>1,'msg'=>'ok','code'=>3));
        }
    }
    
    public function sellerMall(){
        $this->display();
    }

    //收藏商品
    public function favorites(){
        $title=I('title');
        $member_id =$_SESSION['member_data']['id'];//获取member_id用户
        //dump($member_id);
        $where = "id>1";
        $count = M('goods_collect')->where(array('member_id'=>$member_id))->count();
        $p = getpage($count,10);
        $go= M('goods_collect')->where(array('member_id'=>$member_id))->field(true)->where($where)->order('id')->limit($p->firstRow, $p->listRows)->select();//查询收藏商品
        
      
        $this->assign('page', $p->show()); // 赋值分页输出
        $shop_data=M('Shop_Data');//查询店铺信息
        foreach ($go as $k => $v) {
            $go[$k]['child']=$shop_data->where(array('member_id'=>$v['seller_id']))->field('domain')->select();//设立店铺信息为子集
        }

        $this->assign('goods',$go);
        
        $goods= D('Shop_collect');//查询店铺

        //收藏店铺分页
        $where = "id>1";
        $count =$goods->where($where)->where(array('member_id'=>$member_id))->count();
        $p = getpage($count,4);
        
        $shop = $goods->where(array('member_id'=>$member_id))->field(true)->where($where)->order('id')->limit($p->firstRow, $p->listRows)->select();
        $m=M(Mall_goods);
        $g=M(Mall_goods)->where(array('member_id'=>$shop['seller_id']))->select();
        
        $this->assign('page1', $p->show()); // 赋值分页输出
        //收藏店铺
         foreach ($shop as $k=>$v){
                 $shop[$k]['child']=$m->where(array('member_id'=>$v['seller_id']))->where(array('status'=>1))->field("goods_thumb,goods_name,goods_price,id")->select();
         } 
        // dump($shop);
       $this->assign('shop',$shop);
       $this->assign('title',$title);


        $this->display();
    }
    //收藏商品删除
    public function goodsDel(){
        if(IS_AJAX){
            $member_id=$_SESSION['member_data']['id'];
            $data=I();
            $id=M('Goods_collect')
                    ->where(array('member_id'=>$member_id,'goods_id'=>$data['goods_id']))
                    ->delete();
            if($id){
                $this->ajaxReturn(array('status'=>1,'msg'=>'删除成功'));
            }else{
                $this->ajaxReturn(array('status'=>0,'msg'=>'删除失败'));
            }
        }

    }
        //收藏商品添加到购物车
       public function cartAdd(){
        if(IS_AJAX){
            $data = I();
            $member_id=$_SESSION['member_data']['id'];
            $seller_id = M('Mall_goods')->where(array('goods_id'=>$data['goods_id']))->getField('member_id');
            $renzheng  = M('Member')->where(array('id'=>$seller_id))->getField('is_renzheng');
            if(!$renzheng){
                $this->ajaxReturn(array(
                   'status' => 0,
                   'code'   => 11,
                   'msg'    => '该商家未认证'
                ));
            }
            if($_SESSION['member_data']['id']){
                $data['member_id'] = $_SESSION['member_data']['id'];
                $sku = M('Sku')->where(array('goods_id'=>$data['goods_id']))->find();
                $map['member_id']=$member_id;
                $map['goods_id']=$data['goods_id'];
                $map['number'] =$data['number'];
                $map['time']=date('Y-m-d H:i:s');
                $map['sku_id']=$sku['id'];
                $result = M('Mall_cart')->add($map);
                if($result){
                     $this->ajaxReturn(array('status'=>1,'msg'=>'加入购物车成功'));
                }else{
                     $this->ajaxReturn(array('status'=>0,'msg'=>'加入购物车失败'));

                }
               
            }else{
                $cart = is_array(unserialize($_COOKIE['mall_cart']))? unserialize($_COOKIE['mall_cart']) : array();
                $cart[] = $data;
                setcookie('mall_cart' , serialize($cart) , time() + 3600 * 24 , "/" , '.orangesha.com');
                $this->ajaxReturn(array('status'=>1,'msg'=>'ok'));
            }
        }
    }

    public function shop_del(){
        //收藏店铺删除
        $seller_id = I('seller_id');
        $del = M('Shop_collect')->where(array('seller_id'=>$seller_id))->delete();
        if ($del) {
           echo 1;
        }else{
            echo 0;
        }

    }
    



}