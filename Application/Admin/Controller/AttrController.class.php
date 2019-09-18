<?php
/*
 * 商品分类属性管理
 * */
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class AttrController extends Controller {
    
    /**
     * 根据分类id获取属性值
     */
	public function getAttrByCatId(){
        if(IS_POST){
        	$id   = I('id');
        	$data = M('Attrbute')->where(array('cat_id'=>$id))->select();
        	$this->ajaxReturn($data);
        }
	}

   /**
    * 属性列表页
    * @access public  
    */ 
    public function attrList(){
    	$cat_id   = intval(I('id'));
    	!$cat_id && exit('id错误');
    	$cat_name = M('Mall_category')->where(array('id' => $cat_id))->getField('cat_name');
    	$list   = M('Attrbute')
	    	  ->where(array('cat_id'=>$cat_id))
	    	  ->Field('attr_id,attr_name,attr_value,status')
	    	  ->select();
    	array_walk($list, function(&$v){
             $v['attr_value'] = str_replace('\r\n', ',',$v['attr_value']);
    	});
        //dump($list);
    	$this->assign('list' , $list);
    	$this->assign('cat_id' , $cat_id);
		$this->assign('cat_name' , $cat_name);
	   	$this->display();
    }
    /*ajax更改商品品牌状态*/
    public function changeStatus(){
      if(IS_POST){
            $attr_id     = I('attr_id');
            $status = I('status') == 1 ? 1 : 0;
            $r = M('Attrbute')
               ->where(array('attr_id'=>$attr_id))
               ->save(array('status'=>$status));
            if($r === false){
                $result = array(
                    'status' => 0,
                    'msg'    => 'error'
                );
            }else{
                $result = array(
                    'status' => 1,
                    'msg'    => '确认修改？'
                );
            }
            $this->ajaxReturn($result);
        }
    }

   /**
    * 添加属性
    * @access public  
    */ 
    public function attrAdd(){ 
    	if(IS_POST){
            $data   = I();
            $result = D('Attr')->attrAdd($data);
            $this->ajaxReturn($result);
    	}else{
    		$cat_id = intval(I('cat_id'));
    		!$cat_id && exit('id错误');
    		$cat_name = M('Mall_category')->where(array('id' => $cat_id))->getField('cat_name');
    		$this->assign('cat_id' , $cat_id);
    		$this->assign('cat_name' , $cat_name);
    	    $this->display();	
    	}	   	
    }    

   /**
    * 修改属性
    * @access public  
    */ 
    public function attrUpdate(){ 
    	if(IS_POST){
            $data   = I();   
            $result = D('Attr')->attrUpdate($data);                
            $this->ajaxReturn($result);
    	}else{
    		$attr_id  = intval(I('attr_id'));
    		!$attr_id && exit('id错误');
    		$data     = M('Attrbute')
			    	  ->where(array('attr_id'=>$attr_id))
			    	  ->field('attr_id,cat_id,attr_name,attr_value')
			    	  ->find();   
			$attr_ids   = implode(',' ,array_column($data, 'attr_id'));
			$attr_value = M('Attrbute_value')
			            ->field('attr_id,attr_value_id,attr_value')
					    ->where(array('attr_id'=>$data['attr_id']))
					    ->select(); 	  
    		$cat_name = M('Mall_category')->where(array('id' => $data['cat_id']))->getField('cat_name');
    		$data['attr_value'] = str_replace('\r\n', '&#13;&#10;',$data['attr_value']);
    		$this->assign('data' , $data);
    		$this->assign('attr_value' , $attr_value);
    		$this->assign('cat_name' , $cat_name);
    	    $this->display();	
    	}	   	
    } 

   /**
    * 删除属性
    * @access public  
    */     
    public function attrDelete(){
       if(IS_AJAX){
           $id = I('id');
           $r  = D('Attr')->attrDelete($id);
           $this->ajaxReturn($r);
       }
    } 

   /**
    * 删除属性值
    * @access public  
    */     
    public function attrValueDelete(){
       if(IS_AJAX){
           $id = I('id');
           $r  = D('Attr')->attrValueDelete($id);
           $this->ajaxReturn($r);
       }
    } 

   /**
    * 同步属性到子类
    * @access public  
    */     
    public function synchroAttr(){
        if(!IS_POST) die;
        $cat_id = intval(I('id'));
        $result = D('Attr')->updateAttrToNext($cat_id);
        $this->ajaxReturn($result);
    }  
}