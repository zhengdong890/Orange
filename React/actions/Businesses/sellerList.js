import thunk from 'redux-thunk';
import cash from '../../js/Businesses/sellerList';

let actions = {
	/*搜索关键字输入框改变*/
	filedInputChange : (value) => (dispatch, getState) => { 
		cash.search.shop_name = value;
	},	
	/*更改店铺状态*/
	changeShopStatus: (id , status) => (dispatch) => { 
        $.post('/index.php/ShopData/changeShopStatus',{id , status},function(res){	   
        	 cash.tableData[cash.nowPage][id].status = status;
        	 if(res.status){
            	 dispatch({
            		 type : 'get_category',
    	    		 data : cash.getData()
            	 });        		 
        	 }
        });
    },	
	/*分页栏下拉选择每页显示的数量改变*/
	pageListrowsChange : (value , dispatch) => (dispatch, getState) => { 
		cash.list_row = value;
		cash.tableData = {};		
		actions.getData(1,{'firstRows':0,'listRows':cash.list_row})(dispatch);
	},	
    /*获取数据 搜索 缓存数据*/
    getData : (nowPage,parmers) => (dispatch, getState) => { 
    	parmers = cash.getParamers(nowPage , parmers);
    	if(!cash.tableData[nowPage]){
	        $.post('/index.php/ShopData/getShopListData',parmers,function(res){	
	        	 cash.setData(res.data);
	        	 cash.totalRows   = res.total;	
	        	 cash.table_data_state = true;
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


