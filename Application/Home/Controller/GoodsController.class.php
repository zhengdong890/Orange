<?php
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class GoodsController extends Controller {	
    public function _initialize(){
        $redis = new \Com\Redis();;
        $member_id = $_SESSION['member_data']['id'];
        /*获取购物车统计缓存*/
        Hook::add('totalCart','Home\\Addons\\TotalAddon');
        Hook::listen('totalCart',$member_id);
        /*底部帮助*/
        Hook::add('getFooterHelp','Home\\Addons\\HelpAddon');
        Hook::listen('getFooterHelp');
        $help = $redis->get('footer_help' , 'array');//获取redis的缓存        
        $this->assign('cart_total' , $_SESSION['cart_total']);
        $this->assign('help' , get_child($help));
    }
    
    /*
     * 商品详情页
     * */    
    public function goods(){
        $goods_id = I('goods_id'); //商品id
        $goods    = M('goods')->where(array('id'=>$goods_id))->find(); //商品信息
        $goods['deposit'] = $goods['deposit']?$goods['deposit']:0;
        if(!$goods['is_check'] || !$goods['check_status'] || $goods['status'] != 1){
            $this->redirect('Index/index');
        }
        $goods_data = M('goods_data')->where(array('goods_id'=>$goods_id))->find();
        /*商品相册*/
        $goods_gallery = M("goods_gallery")->where(array('goods_id'=>$goods_id))->select();
        if($goods_gallery){
            array_unshift($goods_gallery,array('gallery_img'=>$goods['goods_thumb']));
        }else{
            $goods_gallery[0] = array('gallery_img'=>$goods['goods_thumb']);
        }       
        $goods_hot  = M("Goods")->limit(0,4)->select();  //最热 
        $goods_rent = D('Goods')->getGoodsRent($goods_id);//获取租约金
        $area_str ="{$goods['province']},{$goods['city']},{$goods['area']}";
        $area = array();
        /*获取商品区域*/
        if($goods['province']){	        
	        $area = M('Area')
		          ->where(array('area_no'=>array('in',$area_str)))
		          ->Field('area_level,area_name')->select();        	
		}
		/*品牌*/
		$goods['brand_name'] = M('Goods_brand')->where(array('id'=>$goods['brand_id']))->getField('brand_name');
		$goods['safest']     = $goods['safest']?$goods['safest']:0;
		/*SEO*/
		$goods_seo = M('Goods_seo')->where(array('goods_id'=>$goods_id))->find();
		$this->assign('goods_seo',$goods_seo);
		$this->assign('area',$area);        
        $this->assign('goods',$goods);
        $this->assign('goods_data',$goods_data);
        $this->assign('goods_hot',$goods_hot);
        $this->assign('goods_gallery',$goods_gallery);
        $this->assign('goods_rent',$goods_rent);
        $this->assign('goods_rent_json',json_encode($goods_rent,JSON_FORCE_OBJECT));
        $this->display();
    }
    
    public function getGoodsCommentTotal(){
        if(IS_AJAX){
            $goods_id = intval(I('goods_id'));
            if(!$goods_id){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'code'   => 1,
                    'msg'    => '请输入商品id'
                ));
                die;
            }
            $arr[1] = M('Goods_comment')->where(array('goods_id'=>$goods_id,'level'=>1))->count();
            $arr[2] = M('Goods_comment')->where(array('goods_id'=>$goods_id,'level'=>1))->count();
            $arr[3] = M('Goods_comment')->where(array('goods_id'=>$goods_id,'level'=>3))->count();
            $this->ajaxReturn(array(
                'status' => 1,
                'code'   => 2,
                'msg'    => 'success',
                'data'   => $arr
            ));
        }
    }
    
    public function getGoodsComment(){
        if(IS_AJAX){
            $data     = I();
            $firstRow = $data['firstRow'];
            $listRows = $data['listRows'];
            $goods_id = $data['goods_id'];
            $comment  = M('Goods_comment')
                      ->where(array('goods_id'=>$goods_id))
                      ->limit($firstRow,$listRows)
                      ->select();
            $count = M('Goods_comment')->where(array('goods_id'=>$goods_id))->count();
            $ids = array();
            foreach($comment as $k => $v){
                $ids[] = $v['member_id'];
            }
            $ids = implode(',' , $ids);
            if(!$ids){
                $this->ajaxReturn(array(
                    'status' => 0,
                    'code'   => 1,
                    'msg'    => '暂无数据'
                ));
            }            
            $member_data = M('Member_data')->where(array('member_id'=>array('in',$ids)))->field("member_id,nickname,headimg")->select();
            $result = array(
                'status' => 1,
                'code'   => 2,
                'msg'    => 'ok',
	            'data'   => array(
	                'comment_data' => $comment,
	                'member_data'  => $member_data
	            ),
	            'totalRows' => $count
	        );  
            $this->ajaxReturn($result);
        }
    }
}