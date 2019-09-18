<?php
/*
 * 批量采购
 * */  
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class BulkPurchaseController extends Controller {	  
    public function _initialize(){
        $redis = new \Com\Redis();
        /*底部帮助*/
        Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
        Hook::listen('getFooterHelp');
        $help = $redis->get('footer_help' , 'array');//获取redis的缓存
        $this->assign('help' , $help);
    }
    
    public function index(){

    	if($cat_id=I('cat_id')){
    		//采购刷选、
    		/*分页*/
    		$where=array('status'=>1,'catid'=>$cat_id);
    		$list=$this->page_($where);
        
    	}elseif($time=I('time')){
    		if($time==3){
    			$where=array('status'=>1);
    			$time = (strtotime("-3 days"));
    			$time  =date('Y-m-d H:i:s', $time);  			
    			$map['create_time']  = array('gt',$time);
				$list=$this->page_($where,$map);				
    			$name='三天内新采购';
    			$this->assign('name',$name);
    		}
    		if($time==7){
    			$where=array('status'=>1);
    			$time = (strtotime("-7 days"));
    			$time  =date('Y-m-d H:i:s', $time);  			
    			$map['create_time']  = array('gt',$time);
				$list=$this->page_($where,$map);				
    			$name='一周内新采购';
    			$this->assign('name',$name);
    		}
    		if($time==30){
    			$where=array('status'=>1);
    			$time = (strtotime("-30 days"));
    			$time  =date('Y-m-d H:i:s', $time);  			
    			$map['create_time']  = array('gt',$time);
				$list=$this->page_($where,$map);				
    			$name='一个月内新采购';
    			$this->assign('name',$name);
    		}
    		if($time==31){
    			$where=array('status'=>1);
    			$time = (strtotime("-30 days"));
    			$time  =date('Y-m-d H:i:s', $time);  			
    			$map['create_time']  = array('lt',$time);
				$list=$this->page_($where,$map);				
    			$name='一个月以上新采购';
    			$this->assign('name',$name);
    		}
    	}else{
    		$where=array('status'=>1);
    		$list=$this->page_($where);
    		
    	}

    	$paramer  = I();//获取查询参数
        foreach($paramer as $k => $v){
            $paramer[$k] = "$k=$v";
        }
        unset($paramer['p']);
        unset($paramer['m']);
        $html  = pageHtml('http://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['PATH_INFO'] , $list['show'] , implode('&' , $paramer));
		/*获取banner*/
		$thumb = M('Purchase_banner')->where(array('id'=>3))->getField('thumb');
		/*中标批量采购*/
		$select = M('Purchase_select')
        		->where(array('status'=>1))
        		->select();
        //批量采购图片
        $img=D('Purchase_company')->where(array('is_tj'=>1))->select();
       // dump($img);
        $this->assign('img',$img);
		/*获取Seo缓存*/
		$redis = new \Com\Redis();
		Hook::add('navSeo','Home\\Addons\\SeoAddon');
		$param = array($redis , 3);
        Hook::listen('navSeo', $param);
        $member_id = $_SESSION['member_data']['id'];
        
        //获取cat_id

        if(empty($map)){
        	$where=array('status'=>1);
 			$da = M('Purchase')
		        ->where($where)
		        ->select();
        }else{
        	 $da = M('Purchase')
			     ->where($map)
			     ->where($where)
			     ->select();
        }
       
        $cat_ids = array_column($da,'catid');
        $cat_ids = array_unique($cat_ids);//取出数组里面重复的值
        $cat_ids = implode(',', $cat_ids);
        
        $map=array();
        $map['id']  = array('in',$cat_ids);

        //获取商品分类
        $categorys  = M("Mall_category as m")
        			->where($map)
                    ->where(array('m.status'=>'1','m.level'=>1))
                    ->field('m.id,m.cat_name')
                    ->order('sort')
                    ->select();
       	$cate_data=array();
        foreach($categorys as $k=>$v){
        		$id=$v['id'];
        		$com = M('Purchase')->where("catid=$id")->count();
        		$v['num']=$com;
        		$categorys[$k]=$v; 
        		
        }
        //求num数之和
        $total_num=array_sum(array_map(create_function('$val', 'return $val["num"];'), $categorys));
        //地点查询
       // $area = M('Area')->where(array('parent_no'=>0))->field('area_name')->select();
        foreach($list['data'] as $k=>$v){
        		$a = $v['deadline'];
				$b = date("Y-m-d H:i:s");
				$aa = strtotime($a)-strtotime($b);
				$time = $aa/86400;//天
				$time1 = floor($time);
				
				$bb = $time1*86400;
				$bb1 = $aa-$bb;
				$time2 = $bb1/3600;//小时
				$time2 = floor($time2);
				
				$time3 = $time2*3600+$bb;
				//dump($time3);
				$time3 = $aa-$time3;
				//dump($time3);
				$time3 = $time3/60;//分钟
				$time3 = floor($time3);
				
				$list['data'][$k]['day']=$time1;
				$list['data'][$k]['time']=$time2;
				$list['data'][$k]['minute']=$time3;

        }
        $this->assign('categorys',$categorys);
        $this->assign('total_num',$total_num);
		$seo = $redis->get('nav_seo3' , 'array');
		$this->assign('cate_name',$mall_categorys);

		$this->assign("seo",$seo);
		$this->assign("list",$list['data']);
		$this->assign("select",$select);
		$this->assign('html',$html);
		$this->assign("thumb",$thumb);
		$this->assign('list_json',json_encode($list,JSON_FORCE_OBJECT));
		$this->display();
	}
	public function page_($where,$map=array()){
		$count = M('Purchase')->where($where)->where($map)->count();
        $Page  = new \Think\Page($count,8);// 实例化分页类 
        $show  = $Page->getPage();
        $fild  ='p.id,p.cat_name,p.title,p.des,p.create_time,p.catid,p.num,p.unit,p.area,p.deadline';
        $list  = M('Purchase as p')
		        ->field($fild)
		        ->where($map)
		        ->where($where)
		        ->order('p.id desc')
		        ->limit($Page->firstRow.','.$Page->listRows)
		        ->select();
		return array(
				'data'=>$list,
				'show'=>$show

			);
	}



	//批量采购详情
	public function detail(){
		if(IS_AJAX){
			$data = I();

			$id= $data['id'];
			if($id==''){
				$this->ajaxReturn(array('status'=>0,'msg'=>'id错误'));
				exit;
			}
			$r=M('Purchase as p')
					->where(array('p.id'=>$id))
					->join('left join tp_purchase_img as k on k.purchase_id=p.id')
					->find();
			if($r===false){
				$this->ajaxReturn(array('status'=>0,'msg'=>'暂无详情'));
			}else{
				$this->ajaxReturn(array(
						'status'=>1,
						'data'  =>$r
					));
			}

		}
	}

	public function tenancy(){
	    $paramer = I();//获取查询参数
	    $where   = array();
	    if($paramer['type_id']){//行业
	        $where['type_id'] = $paramer['type_id'];
	    }
	    if($paramer['area_id']){//区域
	        $where['area_id'] = $paramer['area_id'];
	    }
	    if($paramer['brand_id']){//品牌
	        $where['brand_id'] = $paramer['brand_id'];
	    }
	    /*分页*/
	    $count = M('Purchase_company')->where($where)->count();
	    $Page  = new \Think\Page($count,10);// 实例化分页类
	    $show  = $Page->getPage();
	    $fild  ='*';
	    $list  = M('Purchase_company')->where($where)->limit($Page->firstRow.','.$Page->listRows)->field($fild)->select();
	    /*获取区域*/
	    $area_ = M('Area')->where(array('area_level'=>1))->select();
	    foreach($area_ as $v){
	        $area[$v['id']] = $v;
	    }
	    foreach($list as $k=>$v){
	        $list[$k]['area_name'] = $area[$v['area_id']]['area_name'];
	    }
	    /*获取类型*/
	    $type = M('Company_type')->select();
	    /*获取品牌*/
	    $brand = M('Company_brand')->select();
	    /*url参数格式组装*/
	    foreach($paramer as $k => $v){
	        $paramer[$k] = "$k=$v";
	    }
	    $area = array_chunk($area, 8);
	    unset($paramer['p']);
	    $page  = pageHtml(U('Tender/tenancy') , $show, implode('&' , $paramer));
	    // /*获取Seo缓存*/
	     $redis = new \Com\Redis();
	     // Hook::add('tenderSeo','Home\\Addons\\SeoAddon');
	     // Hook::listen('tenderSeo',$redis);
	    $seo = $redis->get('seo_tender' , 'array');
	    $this->assign("seo",$seo);
	    $this->assign('paramers',json_encode(I()));
	    $this->assign("list",$list);
	    $this->assign("type",$type);
	    $this->assign("brand",$brand);
	    $this->assign("area",$area[0]);
	    $this->assign("page",$page);
	    $this->display();
	}  

   /**
    * 批量采购信息申请添加
    * @access public
    */ 
    public function purchaseAdd(){
   	   if(IS_POST){
   	   	   $data   = I();
           
            /*上传图片*/
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 3145728 ;// 设置附件上传大小
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
			// 上传文件
			$info = $upload->upload();

			if($info) {
					if($info['goods_img']){
						$data['thumb'] = $upload->rootPath.$info['goods_img']['savepath'].$info['goods_img']['savename'];//获取图片路径
					}
					if($info['goods_img1']){
						$data['thumb1'] = $upload->rootPath.$info['goods_img1']['savepath'].$info['goods_img1']['savename'];//获取图片路径
					}
					if($info['goods_img2']){
						$data['thumb2'] = $upload->rootPath.$info['goods_img2']['savepath'].$info['goods_img2']['savename'];//获取图片路径
					}				
			}

			$result = D('Purchase')->purchaseAdd($data,$data['thumb'],$data['thumb1'],$data['thumb2']);
           $this->ajaxReturn($result);
   	   }	   
    }

   	public function writeOffer(){
   		//立即报价
   		if(IS_POST){
			$data = I();
			/*上传图片*/
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 3145728 ;// 设置附件上传大小
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
			// 上传文件
			$info = $upload->upload();
			if($info) {
					if($info['img1']){
						$data['img1'] = $upload->rootPath.$info['img1']['savepath'].$info['img1']['savename'];//获取图片路径
					}
					if($info['img2']){
						$data['img2'] = $upload->rootPath.$info['img2']['savepath'].$info['img2']['savename'];//获取图片路径
					}
					if($info['img3']){
						$data['img3'] = $upload->rootPath.$info['img3']['savepath'].$info['img3']['savename'];//获取图片路径
					}				
			}
   				$member_id = $_SESSION['member_data']['id'];
   				if(empty($member_id)){
   					$this->ajaxReturn(array(
   						'status'=>0,
   						'msg'   =>'请先登入'
   						));
   					die;
   				}

   				$result=D('Purchase')->offeradd($data,$member_id,$data['img1'],$data['img2'],$data['img3']);
   				
   				$this->ajaxReturn($result);
   		}else{
   			if(empty($_SESSION['member_data'])){
            header("Location:http://www.orangesha.com/login.html");
        }
        $id = $_SESSION['member_data']['id']; 
   		$data = I();
   		$id = $data['id'];
   		$pur = M('Purchase as p')
   				->join('left join tp_purchase_img as i ON i.purchase_id=p.id')
   				->where(array('p.id'=>$id))
   				->field('p.id,p.cat_name,p.num,p.unit,p.area,p.create_time,p.price_range,p.des,p.kh_name,p.phone,i.thumb,i.thumb1,i.thumb2')
   				->find();
   		//检查数据是否属于合法信息

   		$this->assign('data',$pur);
   		$this->display();
   		}
   		
   	}	
}