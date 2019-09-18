<?php
/*
 * 新闻资讯
 * */
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class NewsController extends Controller {
  /**
   * 新闻资讯列表页
   * @access public  
   */ 
   public function newsList(){ 
       if(IS_AJAX){
           $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
           $listRows = intval(I('listRows'))?intval(I('listRows')):10;  
           $list     = M('News')
                     ->limit($firstRow,$listRows)
                     ->where(array('type'=>1))
                     ->order('create_time desc')
                     ->select();
           $this->ajaxReturn(array('data'=>$list,'total'=>M('News')->where(array('type'=>1))->count()));
       }else{
           $this->display();
       }       
   }

   /**
    * 新闻资讯添加页
    * @access public
    */
   public function newsAdd(){
       if(IS_AJAX){
           $data = I();
           $r    = D('News')->newsAdd($data);
           $this->ajaxReturn($r);            
       }else{
           $this->display();
       }
   } 

   /**
    * 新闻资讯修改页
    * @access public
    */
   public function newsUpdate(){
       if(IS_AJAX){
           $data = I();
           $r    = D('News')->newsUpdate($data);
           $this->ajaxReturn($r);
       }else{
           $id   = I('id');
           $data = M('News')->where(array('id'=>$id))->find();
           $data['content'] = html_entity_decode($data['content']);
           $this->assign('data' , json_encode($data));
           $this->display();
       }
   } 

   /**
    * 删除新闻资讯
    * @access public
    */
   public function newsDelete(){
       if(IS_AJAX){
           $id = I('id');
           $r  = D('News')->newsDelete($id);
           $this->ajaxReturn($r);
       }
   }

   /**
    * ajax更改新闻资讯状态
    * @access public
    */
    public function newsStateChange(){
        if(IS_POST){
            $result = array(
               'status' => 1,
               'msg'    => '操作成功'
            );
            $id = I('id');
            $status = I('status') == 1 ? 1 : 0;
            $r = M('News')->where(array('id'=>$id))->save(array('status'=>$status));
            if($r === false){
                $result = array(
                   'status' => 0,
                   'msg'    => '操作失败'
                );
            }
           $this->ajaxReturn($result);
        }
    }
/*********************************************公告**************************************************/

  /**
   * 公告列表页
   * @access public  
   */ 
   public function noticeList(){ 
       if(IS_AJAX){
           $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
           $listRows = intval(I('listRows'))?intval(I('listRows')):10;  
           $list     = M('News')
                     ->limit($firstRow,$listRows)
                     ->order('create_time desc')
                     ->where(array('type'=>2))
                     ->select();
           $this->ajaxReturn(array('data'=>$list,'total'=>M('News')->where(array('type'=>2))->count()));
       }else{
           $this->display();
       }       
   }

   /**
    * 公告添加页
    * @access public
    */
    public function noticeAdd(){
        if(IS_AJAX){
            $data = I();
            $data['type'] = 2;
            $r    = D('News')->newsAdd($data);
            $this->ajaxReturn($r);            
        }else{
            $this->display();
        }
    } 

   /**
    * 公告修改页
    * @access public
    */
   public function noticeUpdate(){
   	    if(IS_AJAX){
            $this->newsUpdate();
        }else{
	        $id   = I('id');
	        $data = M('News')->where(array('id'=>$id))->find();
	        $data['content'] = html_entity_decode($data['content']);
	        $this->assign('data' , json_encode($data));
	        $this->display();
	    }
   }

   /**
    * 删除公告
    * @access public
    */
   public function noticeDelete(){
       $this->newsDelete();
   }

/*********************************************规则**************************************************/

   /**
    * 规则列表页
    * @access public
    */
   public function ruleList(){
       if(IS_AJAX){
           $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
           $listRows = intval(I('listRows'))?intval(I('listRows')):10;
           $list     = M('News')
                     ->limit($firstRow,$listRows)
                     ->order('create_time desc')
                     ->where(array('type'=>3))
                     ->select();
           $this->ajaxReturn(array('data'=>$list,'total'=>M('News')->where(array('type'=>3))->count()));
       }else{
           $this->display();
       }
   }  
    
    /**
     * 规则添加页
     * @access public
     */
    public function ruleAdd(){
        if(IS_AJAX){
            $data = I();
            $data['type'] = 3;
            $r    = D('News')->newsAdd($data);
            $this->ajaxReturn($r);
        }else{
            $this->display();
        }
    } 
  
   /**
    * 规则修改页
    * @access public
    */
    public function ruleUpdate(){
   	    if(IS_AJAX){
            $this->newsUpdate();
        }else{
	        $id   = I('id');
	        $data = M('News')->where(array('id'=>$id))->find();
	        $data['content'] = html_entity_decode($data['content']);
	        $this->assign('data' , json_encode($data));
	        $this->display();
       }
    }
  
   /**
    * 删除公告
    * @access public
    */
   public function ruleDelete(){
       $this->newsDelete();
   }

/*********************************************卖家规则**************************************************/

   /**
    * 卖家规则列表页
    * @access public
    */
   public function sellerRuleList(){
       if(IS_AJAX){
           $firstRow = intval(I('firstRow'))?intval(I('firstRow')):0;
           $listRows = intval(I('listRows'))?intval(I('listRows')):10;
           $list     = M('News')
                     ->limit($firstRow,$listRows)
                     ->where(array('type'=>4))
                     ->select();
           $this->ajaxReturn(array('data'=>$list,'total'=>M('News')->where(array('type'=>3))->count()));
       }else{
           $this->display();
       }
   }

    /**
     * 卖家规则添加页
     * @access public
     */
    public function sellerRuleAdd(){
        if(IS_AJAX){
        	$_POST['type'] = 4;
        	$this->newsAdd();
        }else{
            $this->display();
        }
    }

   /**
    * 卖家规则修改页
    * @access public
    */
    public function sellerRuleUpdate(){
   	    if(IS_AJAX){
   	        $this->newsUpdate();
        }else{
	        $id   = I('id');
	        $data = M('News')->where(array('id'=>$id))->find();
	        $data['content'] = html_entity_decode($data['content']);
	        $this->assign('data' , $data);
	        $this->display();
	    }
    } 

   /**
    * 删除卖家规则
    * @access public
    */
    public function sellerRuleDelete(){
       $this->newsDelete();
    }

        
}