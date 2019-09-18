import thunk from 'redux-thunk';
import cash from '../../js/MemberCarded/qualificationList';

let actions = {
	/*搜索关键字输入框改变*/
	filedInputChange : (field , value) => (dispatch) => { 
		cash.search[field] = value;
	},		
    /*弹出审核框*/
    fixedEdit: (id) => {
       cash.fixed_edit_hide = true;
       cash.fixed_edit_data = cash.tableData[cash.nowPage][id];
	   return {
	       type : 'show_edit',
		   data : cash.getData()
	   };
    },	
    /*隐藏审核框*/
	fixedEditHide : () => {
	    cash.fixed_edit_hide = false;
  	    return {
		    type : 'hide_edit',
		    data : cash.getData()
	    }
	},
	/*审核框字段值变化*/
	fieldValueChange : (name , value) => { 
		cash.fixed_edit_data[name] = value;
		return {
		     type : 'show_edit',
   		     data : cash.getData()
 	    }
	},
	/*ajax审核*/
	fixedEditRequest : () => (dispatch) => { 
		var data = {
		    id      : cash.fixed_edit_data.id,
		    status  : cash.fixed_edit_data.status,
		    content : cash.fixed_edit_data.content
		};
		cash.fixed_edit_hide = false;		
        $.post('/index.php/MemberCarded/qualificationCheck',data,function(res){
        	if(res.status){
        		delete cash.tableData[cash.nowPage][data.id];
        	}else{
        		alert(res.msg);
        	}
           	dispatch({
         		 type : 'hide_edit',
         		 data : cash.getData()
         	});
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
    	if(!cash.tableData[nowPage]){
    		cash.table_data_state = true;
        	parmers = cash.getParamers(nowPage , parmers);
	        $.post('/index.php/MemberCarded/getQualifications',parmers,function(res){	
	        	 cash.setData(res.data);
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
    }
};

export default actions;


