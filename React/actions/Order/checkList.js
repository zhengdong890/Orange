import thunk from 'redux-thunk';
import cash from '../../js/Order/checkList';

let actions = {
	/*搜索关键字输入框改变*/
	fieldValueChange : (field , value) => (dispatch, getState) => { 
		cash.search[field] = value;
	},	
	/*审核信息*/
	setCheckData : (field , value) => (dispatch, getState) => { 
		cash.order_check_data[field] = value;
	    dispatch({
  		     type : 'order_check_show',
    		 data : cash.getData()
  	    });	
	},	
	/*分页栏下拉选择每页显示的数量改变*/
	pageListrowsChange : (value , dispatch) => (dispatch, getState) => { 
		cash.list_row = value;
		cash.tableData = {};		
		actions.getData(1,{'firstRows':0,'listRows':cash.list_row})(dispatch);
	},	
	/*弹出审核框*/
	orderCheckShow : (id) => (dispatch) => { 
	   cash.order_check_data.id      = id;
	   cash.order_check_data.status  = -1;
	   cash.order_check_data.content = '';
	   cash.order_check_show = true;
	   dispatch({
  		     type : 'order_check_show',
    		 data : cash.getData()
  	   });	       
	},
	/*取消审核框*/
	orderCheckHide : (id) => { 
	   cash.order_check_show = false;
	   return {
  		   type : 'order_check_show',
    	   data : cash.getData()
  	   }	       
	},
	/*ajax审核*/
	orderCheckRequest : () => (dispatch) => {
        $.post('/index.php/Order/orderCheck',cash.order_check_data,function(res){	
        	 alert(res.msg);
        	 if(res.status){
        	     delete cash.tableData[cash.nowPage][cash.order_check_data.id];       		 
        		 cash.order_check_show = false;
        		 dispatch({
          		     type : 'order_check_show',
            		 data : cash.getData()
          	     });        		 
        	 }
       });	      
	},
	/*显示订单详细*/
	orderDataShow : (id) => (dispatch, getState) => {
    	cash.order_data_show = true;
    	if(!cash.order_data[id]){
	        $.post('/index.php/Order/getOrderData',{order_id:id},function(res){	
	        	 cash.setOrderData(id , res.data);
	        	 dispatch({
	        		 type : 'order_data_show',
	        		 data : cash.getData()
	        	 });
	        });
        }else{
	       	dispatch({
	    		 type : 'order_data_show',
	    		 data : cash.getData()
	    	});
        }
    },
	/*隐藏订单详细*/
	orderDataHide : () => (dispatch) => {
		cash.order_data_show = false;
       	dispatch({
   		    type : 'order_data_show',
   		    data : cash.getData()
   	    });
	},
    /*获取数据 搜索 缓存数据*/
    getData : (nowPage,parmers) => (dispatch) => {
    	if(!cash.tableData[nowPage]){
    		cash.table_data_state = true;
        	parmers = cash.getParamers(nowPage , parmers);
	        $.post('/index.php/Order/getCheckList',parmers,function(res){	        	 
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


