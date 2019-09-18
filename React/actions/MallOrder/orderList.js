import thunk from 'redux-thunk';
import cash from '../../js/MallOrder/orderList';

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
	/*更改付款状态*/
	changePayState : (id , pay_status) => (dispatch) => {
		pay_status = pay_status == 1 ? 0 : 1;
        $.post('/index.php/MallOrder/changePayState',{id:id,pay_status:pay_status},function(res){	
        	 cash.tableData[cash.nowPage][id].pay_status = pay_status;
        	 dispatch({
        		 type : 'change_pay_state',
        		 data : cash.getData()
        	 });
        });
    },
	/*显示订单详细*/
	showFixedTable : (id) => (dispatch, getState) => {
    	cash.fixed_table_show = true;
    	if(!cash.fixed_table_data[id]){
	        $.post('/index.php/MallOrder/getOrderData',{order_id:id},function(res){	
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
	        $.post('/index.php/MallOrder/getOrder',parmers,function(res){	        	 
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


