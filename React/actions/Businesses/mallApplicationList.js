import thunk from 'redux-thunk';
import cash from '../../js/Businesses/mallApplicationList';

let actions = {
	/*搜索关键字输入框改变*/
	filedInputChange : (value) => (dispatch, getState) => { 
		cash.search.name = value;
	},	
	
	fieldValueChange : (name , value) => { 
		cash.check_data[name] = value;
		return {
		     type : 'check_show',
   		     data : cash.getData()
 	    }
	},	
	/*审核框字段值变化*/
	setCheckData :(name , value) => { 
		cash.check_data[name] = value;
		return {
		     type : 'check_show',
   		     data : cash.getData()
 	    }
	},	 
	/*分页栏下拉选择每页显示的数量改变*/
	pageListrowsChange : (value , dispatch) => (dispatch, getState) => { 
		cash.list_row = value;
		cash.tableData = {};		
		actions.getData(1,{'firstRows':0,'listRows':cash.list_row})(dispatch);
	},	
	/*显示审核数据*/
	checkDataShow : (id , seller_id) => (dispatch) => {
		cash.check_data = cash.tableData[cash.nowPage][id];
		cash.check_show = true;
		cash.seller_id  = seller_id;
		actions.getShopData(seller_id , dispatch);
			
	},
	/*隐藏审核数据*/
	checkDataHide : () => {
		cash.check_show = false;
		return {
	  		type : 'check_show',
	        data : cash.getData()
	  	} 	
	},
    /*审核数据*/
    checkData : () => (dispatch) => { 
    	var parmers = {
    	    id      : cash.check_data.id , 
    	    status  : cash.check_data.status, 
    	    content : cash.check_data.content 
    	}
        $.post('/index.php/Businesses/qualification',parmers,function(res){	
        	 alert(res.msg);
        	 if(res.status){
            	 cash.check_show = false;
            	 cash.tableData[cash.nowPage][cash.check_data.id].status = cash.check_data.status;
            	 cash.tableData[cash.nowPage][cash.check_data.id].content = cash.check_data.content;
            	 cash.tableData[cash.nowPage][cash.check_data.id].check_status = 1;
            	 dispatch({
            		 type : 'get_data',
            		 data : cash.getData()
            	 });        		 
        	 }
        });
    },
    /*获取店铺数据 缓存*/
    getShopData : (seller_id , dispatch) => { 
    	if(!cash.shop_data[seller_id]){
	        $.post('/index.php/Businesses/getShopData',{seller_id : seller_id},function(res){	
	        	 cash.shop_data[seller_id] = res;
	     		 dispatch({
	    	  		type : 'check_show',
	    	        data : cash.getData()
	    	  	 }); 
	        });
        }else{
    		dispatch({
    	  		type : 'check_show',
    	        data : cash.getData()
    	  	}); 
        }
    },
    /*获取数据 搜索 缓存数据*/
    getData : (nowPage,parmers) => (dispatch) => { 
    	parmers = cash.getParamers(nowPage , parmers);
    	if(!cash.tableData[nowPage]){
	        $.post('/index.php/Businesses/getmallApplications',parmers,function(res){	
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


