import thunk from 'redux-thunk';
import cash from '../../js/Order/orderList';

let actions = {
	/*搜索关键字输入框改变*/
	filedInputChange : (field , value) => (dispatch, getState) => { 
		cash.search[field] = value;
	},	
	/*隐藏订单详细*/
	hideFixedTable : () => (dispatch) => {
		cash.fixed_table_show = false;
       	dispatch({
   		    type : 'fixed_table_show',
   		    data : cash.getData()
   	    });
	},
	/*分页栏下拉选择每页显示的数量改变*/
	pageListrowsChange : (value , dispatch) => (dispatch, getState) => { 
		cash.list_row = value;
		cash.tableData = {};		
		actions.getData(1,{'firstRows':0,'listRows':cash.list_row})(dispatch);
	},	
	/*删除订单*/
	orderDelete : (id) => (dispatch) => {    	
        $.post('/index.php/Order/orderDelete',{order_id:id},function(res){	
        	 alert(res.msg);
             if(res.status){
            	 delete cash.tableData[cash.nowPage][id];
            	 cash.fixed_table_show = false;	
            	 dispatch({
            		 type : 'fixed_table_show',
            		 data : cash.getData()
            	 });
             }
        });
    },
	/*显示订单详细*/
	showFixedTable : (id) => (dispatch, getState) => {
    	cash.fixed_table_show = true;
    	if(!cash.fixed_table_data[id]){
	        $.post('/index.php/Order/getOrderData',{order_id:id},function(res){	
	        	 cash.setOrderData(id , res.data);
	        	 dispatch({
	        		 type : 'fixed_table_show',
	        		 data : cash.getData()
	        	 });
	        });
        }else{
	       	dispatch({
	    		 type : 'fixed_table_show',
	    		 data : cash.getData()
	    	});
        }
    },
    /*获取数据 搜索 缓存数据*/
    getData : (nowPage,parmers) => (dispatch) => {
    	if(!cash.tableData[nowPage]){
    		cash.table_data_state = true;
        	parmers = cash.getParamers(nowPage , parmers);
	        $.post('/index.php/Order/getOrder',parmers,function(res){	        	 
	        	 cash.setData(res.data);
	        	 cash.totalRows = res.total;
	        	 dispatch({
	        		 type : 'get_data',
	        		 data : cash.getData()
	        	 });
	        });
        }else{
        	cash.nowPage = nowPage;
	       	dispatch({
	    		 type : 'get_data',
	    		 data : cash.getData()
	    	});
        }
    }
};

export default actions;


