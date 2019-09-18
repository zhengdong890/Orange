import thunk from 'redux-thunk';
import cash from '../../js/MallGoods/goodsList';

let actions = {
	/*搜索关键字输入框改变*/
	keywordsChange : (value) => (dispatch, getState) => { 
		cash.search.keyword = value;
	},
	/*搜索共享商品分类改变*/
	categorysChange : (value) => (dispatch, getState) => { 
		cash.search.cat_id = value;
	},
	/*搜索共享商品加入推荐改变*/
	goodsModelChange : (value) => (dispatch, getState) => { 
		cash.search.model_id = value;
	},
	/*搜索商品上下架状态改变*/
	goodsSearchStatusChange : (value) => (dispatch, getState) => { 
		cash.search.status = value;
	},
	/*商品排序输入框变化*/
	sortChange : (id , value) => (dispatch, getState) => { 		
		cash.sort[id] = value;
	},
	/*选中状态*/
	setCheckStatus : (id , status) => (dispatch, getState) => { 		
		cash.tableData[cash.nowPage][id].check = status;
		dispatch({
   		   type : 'set_check_status',
   		   data : cash.getData()
   	    });
	},
	/*选中状态  全选*/
	setCheckAllStatus : (status) => (dispatch) => { 	
		cash.check_all = status;
		cash.setCheckAllStatus();
		dispatch({
   		   type : 'set_check_status',
   		   data : cash.getData()
   	    });
	},
	
/********************************************批量操作***************************************************/
	/*批量操作 加入推荐选择*/
	goodsModelSelect : (value) => { 
		cash.select_model = parseInt(value);
		return {
			type : 'set_check_status',
	   		data : cash.getData()
		}
	},
	/*加入推荐批量下架*/
	allGoodsModelFalse : () => (dispatch) => { 	
		var id       = cash.getGoodsModelFalse(),
		    model_id = cash.select_model;
		if(model_id == 0){
			alert('请选择加入推荐');
    		return;
		}
		var flag = true;
        for(var k in id){
        	flag = false;
        	break;
        }
        if(flag){
        	alert('请选择id');return;
        }
        $.post('/index.php/MallGoods/allGoodsModelFalse',{goods_id : id , model_id : model_id},function(res){	
        	alert(res.msg);
            if(res.status == '1'){
            	window.location.reload();
            }
        });	        
	},
	/*批量操作 上下架选择*/
	goodsStatusSelect : (value) => { 
		cash.goods_status = parseInt(value);
		return {
			type : 'set_check_status',
	   		data : cash.getData()
		}
	},
	/*批量上下架*/
	allGoodsStateChange : () => (dispatch) => { 	
		var id       = cash.getGoodsModelFalse(),
		    status   = cash.goods_status;
		var flag = true;
        for(var k in id){
        	flag = false;
        	break;
        }
        if(flag){
        	alert('请选择id');return;
        }
        $.post('/index.php/MallGoods/goodsStateChange',{id : id , status : status},function(res){	
        	alert(res.msg);
            if(res.status == '1'){
            	cash.changeGoodsStatus(status);
            	dispatch({
        			type : 'get_data',
        	   		data : cash.getData()
            	});
            }
        });	        
	},
	
	/*商品排序改变*/
	sendSortChange : () => (dispatch, getState) => {
		var flag = false;
		for(var k in cash.sort){
			flag = true;break;
		}
		if(flag){
	        $.post('/index.php/MallGoods/sortChange',cash.sort,function(res){	   
		       	 window.location.reload();
	        });			
		}
	},
	/*分页栏下拉选择每页显示的数量改变*/
	pageListrowsChange : (value , dispatch) => (dispatch, getState) => { 
		cash.list_row = value;
		cash.tableData = {};		
		actions.getData(1,{'firstRows':0,'listRows':cash.list_row})(dispatch);
	},
    /*获取共享商品分类*/
    getCategory : () => (dispatch, getState) => { 
    	if(!cash.category_state){
    		cash.category_state = true;
	        $.post('/index.php/Mall_category/getCategory',{},function(res){	   
	        	 cash.setCategoryData(res);  
	        	 dispatch({
	        		 type : 'get_category',
		    		 data : cash.getData()
	        	 });
	        });
        }else{
	       	 dispatch({
	    		 type : 'get_category',
	    		 data : cash.getData()
	    	 });
        }
    },		
    /*获取商品加入推荐*/
    getGoodsModel : () => (dispatch, getState) => { 
    	if(!cash.goods_model_state){
        	cash.goods_model_state = true;
	        $.post('/index.php/Mall_goods_model/getGoodsModel',{},function(res){	
	        	 cash.setGoodsModel(res);
	        	 dispatch({
	        		 type : 'get_goods_model',
		    		 data : cash.getData() 
	        	 });
	        });
        }else{
	       	 dispatch({
	    		 type : 'get_goods_model',
	    		 data : cash.getData()
	    	 });
        }
    },
    /*获取数据 搜索 缓存数据*/
    getData : (nowPage,parmers) => (dispatch, getState) => { 
    	parmers = cash.getParamers(nowPage,parmers);
   	    if(__SELLER_ID){
   	    	parmers.seller_id = __SELLER_ID;
   	    }   	
    	if(!cash.tableData[nowPage]){
	        $.post('/index.php/MallGoods/goodsList',parmers,function(res){	        	 
	        	 cash.totalRows = res.total;
	        	 cash.setGoodsData(res.data);	
	        	 cash.goods_state = true;
	        	 dispatch({
	        		 type : 'get_data',
	        		 data : cash.getData()
	        	 });
	        });
        }else{
	       	 dispatch({
	    		 type : 'get_data',
	    		 data : cash.getData()
	    	 });
        }
    },
    //删除商品
    deleteData : (id) => (dispatch) => { 
        $.post('/index.php/MallGoods/goodsDelete',{id:id},function(res){
        	 alert(res.msg);
        	 if(res.status){
    	    	 delete cash.tableData[cash.nowPage][id];
    			 dispatch({
    	    		 type : 'delete_data',
    	    		 data : cash.getData()
    	    	 });
        		 dispatch({
            		 type : 'detele_data',
            		 data : cash.getData()
            	 });
        	 }
        });
    }, 
    /*更改商品上架状态*/
    goodsStateChange : (id, status) => (dispatch, getState) => { 
    	status = status == 1 ? 0 : 1;
        $.post('/index.php/MallGoods/goodsStateChange',{id:id,status:status},function(res){
        	 cash.changeStatus(id , status);
        	 dispatch({
        		 type : 'changestatus',
        		 data : cash.getData()
        	 });
        });
    }, 
    /*更改加入推荐状态*/
    modelsStateChange : (goods_id , model_id) => (dispatch, getState) => { 
    	status = status == 1 ? 0 : 1;
        $.post('/index.php/MallGoods/goodsModelChange',{goods_id:goods_id,id:model_id},function(res){
        	 cash.changeModelStatus(goods_id,model_id);
        	 dispatch({
        		 type : 'change_model_status',
        		 data : cash.getData()
        	 });
        });
    },
    beginPickApple: () => ({
        type: 'apple/BEGIN_PICK_APPLE'
    }),
};

export default actions;


