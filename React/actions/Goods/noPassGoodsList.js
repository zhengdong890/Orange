import thunk from 'redux-thunk';
import cash from '../../js/Goods/noPassGoodsList';

let actions = {
	/*搜索关键字输入框改变*/
	keywordsChange : (value) => (dispatch, getState) => { 
		cash.search.keyword = value;
	},
	/*搜索共享商品分类改变*/
	categorysChange : (value) => (dispatch, getState) => { 
		cash.search.cat_id = value;
	},
	/*分页栏下拉选择每页显示的数量改变*/
	pageListrowsChange : (value , dispatch) => (dispatch, getState) => { 
		cash.list_row = value;
		cash.tableData = {};		
		actions.getData(1,{'firstRows':0,'listRows':cash.list_row})(dispatch);
	},
	/*显示审核的意见*/
	checkDataShow : (id) => (dispatch, getState) => { 
		cash.check_data_show = true;
		cash.check_id        = id;
    	if(!cash.check_data[id]){
    		cash.category_state = true;
	        $.post('/index.php/GoodsCheck/getCheckData',{id : id},function(res){	   
	        	 cash.check_data[id] = res;   
	     	   	 dispatch({
	   			     type : 'check_data_show',
	   			     data : cash.getData()
	   		     });
	        });
        }else{
    	   	dispatch({
   			     type : 'check_data_show',
   			     data : cash.getData()
   		    });
        }  	
	},
	/*关闭审核的意见*/
	checkDataHide : () => (dispatch, getState) => { 
		cash.check_data_show = false;
	   	dispatch({
			 type : 'check_data_hide',
			 data : cash.getData()
		});
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
    /*获取数据 搜索 缓存数据*/
    getData : (nowPage,parmers) => (dispatch) => { 
    	parmers = cash.getParamers(nowPage , parmers);//获取参数
    	if(!cash.tableData[cash.nowPage]){
    		cash.goods_state = true;
	        $.post('/index.php/GoodsCheck/noPassGoodsList',parmers,function(res){	  
	        	 cash.setGoodsData(res.data);
	        	 cash.totalRows = res.total;		        	 
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
    }
};

export default actions;


