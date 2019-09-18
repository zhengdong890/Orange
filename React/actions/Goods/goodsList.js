import thunk from 'redux-thunk';
import cash from '../../js/goods/goodsList';

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
	/*商品排序输入框变化*/
	sortChange : (id , value) => (dispatch, getState) => { 		
		cash.sort[id] = value;
	},
	/*商品排序改变*/
	sendSortChange : () => (dispatch, getState) => {
		if(!cash.objectIsEmpty(cash.sort)){
	        $.post('/index.php/Goods/sortChange',cash.sort,function(res){	   
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
	        $.post('/index.php/Category/getCategory',{},function(res){	   
	        	 cash.category = res;       	 
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
	        $.post('/index.php/Goods/getGoodsModel',{},function(res){	 
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
    getData : (nowPage,parmers) => (dispatch) => { 
    	parmers = cash.getParamers(nowPage , parmers);//获取参数
    	if(!cash.tableData[cash.nowPage]){
    		cash.goods_state = true;
	        $.post('/index.php/Goods/goodsList',parmers,function(res){	  
	        	 cash.setGoodsData(res.data);
	        	 cash.totalRows = res.total;		        	 
	        	 dispatch({
	        		 type : 'getdata',
	        		 data : cash.getData()
	        	 });
	        });
        }else{
	       	 dispatch({
	    		 type : 'getdata',
	    		 data : cash.getData()
	    	 });
        }
    },
    //删除商品
    deleteData : (id) => (dispatch) => { 
    	delete cash.tableData[cash.nowPage][id];
        $.post('/index.php/Goods/goodsDelete',{id:id},function(res){
        	dispatch({
        		type : 'detele_data',
        		data : cash.getData()
        	});
        }); 
    }, 
    /*更改商品上架状态*/
    goodsStateChange : (id, status) => (dispatch, getState) => { 
    	status = status == 1 ? 0 : 1;
        $.post('/index.php/Goods/goodsStateChange',{id:id,status:status},function(res){
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
        $.post('/index.php/Goods/goodsModelChange',{goods_id:goods_id,id:model_id},function(res){
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


