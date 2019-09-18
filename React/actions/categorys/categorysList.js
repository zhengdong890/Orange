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
		var flag = false;
		for(var k in cash.sort){
			flag = true;break;
		}
		if(flag){
	        $.post('/index.php/Admin/Goods/sortChange',cash.sort,function(res){	   
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
	        $.post('/index.php/Admin/Category/getCategory',{},function(res){	   
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
	        $.post('/index.php/Admin/Goods/getGoodsModel',{},function(res){	 
	        	 var goods_model = {};
	        	 for(var k in res){
	        		 res[k].goods_ids = res[k].goods_ids.split(',');
	        		 goods_model[res[k].id] = res[k];	        		 
	        	 }
	        	 cash.goods_model = goods_model;	        	 
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
    	if(!nowPage){
    		parmers = {
    		    keyword   : cash.search.keyword,	
    		    cat_id    : cash.search.cat_id,
    		    model_id  : cash.search.model_id,
    		    firstRows : 0,
    		    listRows  : 10
    		};
    		cash.tableData = {};
        	cash.old_search = {
            	'keyword' : cash.search.keyword,
                'cat_id'  : cash.search.cat_id,
            	'model_id': cash.search.model_id
            };
    		nowPage = 1;
    	}else{
    		parmers.keyword  = cash.old_search.keyword;
    		parmers.cat_id   = cash.old_search.cat_id;
    		parmers.model_id = cash.old_search.model_id;
    	}
    	cash.nowPage = nowPage;
   	    cash.sort = {};
    	if(!cash.tableData[nowPage]){
	        $.post('/index.php/Admin/Goods/getGoodsList',parmers,function(res){	        	 
	        	 cash.totalRows = res.total;
	        	 cash.tableData[nowPage] = res.data;	
	        	 cash.goods_state = true;
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
    deleteData : (id) => (dispatch, getState) => { 
        $.post(pageUrl,{id:id},function(res){
        	 window.location.reload();
        	 /*dispatch({
        		 type : 'deteledata',
        		 data : {} 
        	 });*/
        }); 
    }, 
    /*更改商品上架状态*/
    goodsStateChange : (id, status) => (dispatch, getState) => { 
    	status = status == 1 ? 0 : 1;
        $.post('/index.php/Admin/Goods/goodsStateChange',{id:id,status:status},function(res){
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
        $.post('/index.php/Admin/Goods/goodsModelChange',{goods_id:goods_id,id:model_id},function(res){
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


