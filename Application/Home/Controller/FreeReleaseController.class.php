<?php
namespace Home\Controller;
use Think\Controller;
use Think\Hook;
header("content-type:text/html;charset=utf-8");
class FreeReleaseController extends Controller {
    public function selectCategory(){
        if(!empty($_SESSION['member_data'])){
            $this->redirect('Member_goods/selectCategory');
        }
        $this->display();
    }
    
    /*
     * 免费发布商品
     * */
    public function goodsAdd(){
        if(IS_POST){
            $data   = I();
            $telnum = $data['telnum'];
            unset($data['telnum']);
            if(!preg_match('/^1[3458]\d{9}$/', $telnum)){
                $this->ajaxReturn(array('status'=>'0','msg'=>'电话号码格式不正确'));
            }            
            /*先自动注册*/
            $password = setnum(10);
            $register_data = array(
                'username'        => $telnum, //账号
                'password'        => $password, //密码
                'repeat_password' => $password //确认密码
            );
            $result = D('Member')->register($register_data);//注册
            if($result['status']){//注册成功
                
            }else{
                $this->ajaxReturn($result);
            }
            $data['member_id'] = $result['member_id'];
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
            /*取出销售属性*/
            $baseattr = array();
            /*取出扩展属性*/
            foreach($data as $k => $v){
                if(substr($k,0,10) == 'extendattr'){
                    $attr_id = substr($k,10);
                    $extendattr["$attr_id"]['attr_id']    = $attr_id;
                    $extendattr["$attr_id"]['attr_value'] = $v;
                }
            }
            /*取出商品相册图片的描述*/
            foreach($data as $k => $v){
                if(substr($k,0,14) == 'gallery_remark'){
                    $i = substr($k,14);
                    $goods_gallery["$i"]['gallery_remark']=$v;
                }
            }
            //上传图片
            $upload           = new \Think\Upload();// 实例化上传类
            $upload->maxSize  = 3145728 ;// 设置附件上传大小
            $upload->exts     = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->savePath = date('Ym').'/'; // 设置附件上传（子）目录
            // 上传文件
            $info = $upload->upload();
            if($info) {
                /*商品表图片处理*/
                if($info['goods_thumb']){//获取缩略图片路径
                    $data['goods_thumb'] = $upload->rootPath.$info['goods_thumb']['savepath'].$info['goods_thumb']['savename'];
                }
                if($info['goods_img']){//获取图片路径
                    $data['goods_img'] = $upload->rootPath.$info['goods_img']['savepath'].$info['goods_img']['savename'];
                }
                /*商品相册处理*/
                $length = $data['number'];//获取上传图片的个数
                for($i = 1; $i <= $length; $i++){//图片组装
                    $xb    = 'gallery_img'.$i;//获取图片post过来数组的下标
                    $thumb = $upload->rootPath.$info["$xb"]['savepath'].$info["$xb"]['savename'];//获取上传图片路径
                    if(!empty($thumb)){//如果图片有上传
                        $goods_gallery["$i"]['gallery_img'] = $thumb;
                    }
                }
            }
            $result = D('Goods')->goodsAdd($data , $baseattr  , $extendattr  , $goods_gallery , $rent);
            $this->ajaxReturn($result);
        }else{
            $cat_id = I('cat_id');
            if($cat_id){
                /*自动获取最大的排序*/
                $sort       = M('Goods')->max('sort');
                $sort       = $sort? $sort++ : '1';
                $category   = M('Category')->where(array('id'=>$cat_id))->find();
                $extendattr = D('Goods')->getCatExtendAttr($cat_id); //扩展属性
                //商品品牌
                $brands = M('Category_brand as a')
                ->join("tp_goods_brand as b on a.brand_id=b.id")
                ->field("b.*")
                ->where(array('cat_id'=>$cat_id))
                ->select();
                $this->assign('brands' , $brands);
                $this->assign('sort' , $sort);
                $this->assign('category' , $category);
                $this->assign('extendattr' , $extendattr);
                $this->display();
            }
        }
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