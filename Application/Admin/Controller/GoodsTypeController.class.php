<?php
namespace Admin\Controller;
use Think\Controller;
header("content-type:text/html;charset=utf-8");
class GoodsTypeController extends Controller {
	/**
	 * 商品类型列表
	 */
	public function goodsTypeList(){
		if(IS_AJAX){
	   	    $id   = I('id');
	   	    $list = M('Goods_type')->where(array('pid'=>$id))->select();
            $this->ajaxReturn($list);
        }else{
        	$this->display();
        }       	
	}


	/*属性列表*/
	public function attrList(){
		$type_id=I('type_id');
		$attr['0']['type_id']=$type_id;
		foreach($attr as $k=>$v){
			$attr_1=M('Attrbute')->where(array('type_id'=>$v['type_id']))->select();
			foreach($attr_1 as $k1=>$v1){
				$a=M('Attrbute_value')->where(array('attr_id'=>$v1['attr_id']))->Field('attr_value')->order('sort')->select();
				$attr_value='';
				foreach($a as $v2){
			        $attr_value.=$v2['attr_value'];
			        $attr_value.=",";
		        }			       
				$attr_1["$k1"]['attr_value']=substr($attr_value,0,92);//str_replace(PHP_EOL,',',$v1['attr_value']);
			}
			$attr["$k"]['type_name']=M('Goods_type')->where(array('id'=>$v['type_id']))->getField('type_name');
			$attr_1=tree_1($attr_1,'attr_id');
			$attr["$k"]['attr']=$attr_1;
		}		
		$this->type_id=$type_id;
		$this->attr=$attr;
		$this->display();
	}

	/*新建商品类型*/
	public function goodsTypeAdd(){
		if(IS_POST){
			$data=I();
			/*验证数据*/
			$model = D("Goods_type");
			$rules= array(
					array('type_name','require','商品类型名称不能为空'),
			);
			if(!$model->validate($rules)->create($data)){
				$this->error($model->getError(),'goods_type_add');
				die;
			}
			$id=$model->add($data);
			if($id){			    
				$this->success('添加成功','goods_type');
			}else{
				$this->success('添加失败','goods_type_add');
			}
		}else{
			$goods_type=M('Goods_type')->select();
			$this->goods_type = tree_1($goods_type);
			$this->display();
		}
	}
	
	/*添加属性*/
	public function attrAdd(){
		if(IS_POST){
			$data=I();
			/*验证数据*/
			$model = D("Attrbute");
			$rules= array(
					array('attr_name','require','属性名称不能为空'),
					array('type_id','require','商品类型不能为空'),
			);
			if(!$model->validate($rules)->create($data)){
				$this->error($model->getError(),U('attr_add',array('type_id'=>$data['type_id'])));
				die;
			};
			$attr_value=str_replace(PHP_EOL,',',$data['attr_value']);//增加的值
			$attr_value=explode(',',$attr_value);//组成数组
			$id=$model->add($data);
			$sort=1;
			if($id){
				foreach($attr_value as $v){
					if($v){
						M('Attrbute_value')->add(array('attr_id'=>$id,'attr_value'=>$v,'sort'=>$sort));
						$sort++;
					}					
				}
				$attr_ids_ = M('Attrbute')->where(array('type_id'=>$data['type_id']))->field('attr_id')->select();
				foreach($attr_ids_ as $k => $v){
				    $attr_ids[] = $v['attr_id'];
				}
				$attr_ids = implode(',' , $attr_ids);
				M('Category')->where(array('type_id'=>$data['type_id']))->save(array('filter_extendattr'=>$attr_ids));
				M('Mall_category')->where(array('type_id'=>$data['type_id']))->save(array('filter_extendattr'=>$attr_ids));				 
				$this->success('添加成功',U('attr',array('type_id'=>$data['type_id'])));
			}else{
				$this->success('添加失败','attr_add');
			}
		}else{
			$this->type_id=I('type_id');
			$this->type_name=M('Goods_type')->where(array('id'=>$this->type_id))->getField('type_name');
		    $attr=M('Attrbute')->where(array('type_id'=>$this->type_id))->select();
			$this->attr = tree_1($attr,'attr_id'); 
			$this->display();
		}
		
	}
	
	/*修改属性*/
	public function attrUpdate(){
		if(IS_POST){
			$data=I();
			/*验证数据*/
			$model = D("Attrbute");
			$rules= array(
					array('attr_name','require','属性名称不能为空'),
					array('type_id','require','商品类型不能为空'),
			);
			if(!$model->validate($rules)->create($data)){
				$this->error($model->getError(),U('attr_update',array('attr_id'=>$data['attr_id'])));
				die;
			};
			$attr_value=str_replace(PHP_EOL,',',$data['attr_value']);//增加的值
			$attr_value=explode(',',$attr_value);//组成数组
			/*取出已经存在的可选值*/
			$i=0;
			foreach($data as $k=>$v){
				if(substr($k,0,13)=='oldattr_value'){
					$id=substr($k,13);
					$oldattr_value["$id"]['id']=$id;
					$oldattr_value["$id"]['attr_value']=$v;
					$oldattr_value["$id"]['sort']=$data["oldattr_sort{$id}"];
					M('Attrbute_value')->save($oldattr_value["$id"]);//更新
				}
			}
			$a=$model->save($data);//保存
			$sort=M('Attrbute_value')->where(array('attr_id'=>$data['attr_id']))->max('sort');
			if($a!==false){
				foreach($attr_value as $v){
					if($v){
						$sort++;
						M('Attrbute_value')->add(array('attr_id'=>$data['attr_id'],'attr_value'=>$v,'sort'=>$sort));
					}					
				}
				$this->success('修改成功',U('attr',array('type_id'=>$data['type_id'])));
			}else{
				$this->success('修改失败',U('attr_update',array('attr_id'=>$data['attr_id'])));
			}
		}else{
			$attr_id=I('attr_id');
			$attr=M('Attrbute')->where(array('attr_id'=>$attr_id))->find();
			$attr['attr_value']='';
			$attr['attr_value']=M('Attrbute_value')->where(array('attr_id'=>$attr_id))->order('sort')->select();
			$attr['attr_type_1']=$attr['attr_type']==1?'checked=checked':'';
			$attr['attr_type_2']=$attr['attr_type']==2?'checked=checked':'';
			$attr['attr_type_3']=$attr['attr_type']==3?'checked=checked':'';
			$attr['type_1']=$attr['type']=='no'?'checked=checked':'';
			$attr['type_2']=$attr['type']=='picture'?'checked=checked':'';
			$attr['type_3']=$attr['type']=='gallery'?'checked=checked':'';
			$attr['attr_input_type_1']=$attr['attr_input_type']==1?'checked=checked':'';
			$attr['attr_input_type_2']=$attr['attr_input_type']==2?'checked=checked':'';
			$this->attr=$attr;//该特征量
			/*所有商品类型*/
			$goods_type=M('Goods_type')->select();
			$this->goods_type = tree_1($goods_type);
			/*所有该类型下的特征量*/
			$attrs=M('Attrbute')->where(array('type_id'=>$attr['type_id']))->select();
			$this->attrs = tree_1($attrs,'attr_id');
			//dump($this->attrs);
			$this->display();
		}	
	}
	
	public function attrDelete(){
		if(IS_POST){
			$attr_id=I('attr_id');
			$r = M('Attrbute')->where(array('attr_id'=>$attr_id))->delete();
			if($r !== false){
			    $type_id   = M('Attrbute')->where(array('attr_id'=>$attr_id))->getField('type_id');
			    $attr_ids_ = M('Attrbute')->where(array('type_id'=>$type_id))->field('attr_id')->select();
			    foreach($attr_ids_ as $k => $v){
			       $attr_ids[] = $v['attr_id'];
			    }
			    $attr_ids = implode(',' , $attr_ids);
			    M('Category')->where(array('type_id'=>$type_id))->save(array('filter_extendattr'=>$attr_ids));
			    M('Mall_category')->where(array('type_id'=>$type_id))->save(array('filter_extendattr'=>$attr_ids));
				echo "删除成功";
			}
		}
	}

  /**
   * 获取商品类型属性
   */ 
   public function getFilterAttr(){
	   if(IS_POST){
		    $attr = D('Goods_type')->getFilterAttr(I('type_id'));
			$this->ajaxReturn($attr['data']);
	   }
   }
}