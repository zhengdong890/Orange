import thunk from 'redux-thunk';
import cash from '../../js/Tender/tenderList';

let actions = {
		
/*************************************添加融资招标********************************************************/    
	
    /*显示添加融资招标框*/
	fixedAddShow : () => ({
        type : 'fixed_add_show',	    	
	}),
    /*隐藏添加融资招标框*/
	fixedAddHide : () => ({
        type : 'fixed_add_hide',	    	
	}),
	/*添加融资招标 input框值记录*/
	addInputChange : (name , value) => { 		
		return {
		     type  : 'add_input_change',
   		     name  : name,
   		     value : value
 	    }
	},	
	addTypeChange : (num) => { 		
		return {
		     type : 'add_type_change',
  		     num  : parseInt(num)
	    }
	},	
	/*ajax融资招标*/
	integratedAdd : () => (dispatch) => { 
		console.log(cash.fixedAddData);
        $.post('/index.php/Tender/tenderAdd',cash.fixedAddData,function(res){
        	 if(res.status != 0){
            	 dispatch({
            		 type : 'tender_add',
    	    		 data : res
            	 });        		 
        	 }else{
        		 alert(res.msg);
        	 }
        });
    },
    
/*************************************融资招标详情********************************************************/    
	
    /*显示添加融资招标*/
    fixedDetailsShow : (id) => { 
		return {
   		    type : 'fixed_details_show',
		    id   : id
	    }	
	},
    /*隐藏添加融资招标*/
	fixedDetailsHide : () => ({ 
   		type : 'fixed_details_hide',
	}),
	
/*************************************融资招标编辑********************************************************/ 
    
	/*显示融资招标编辑*/
    fixedEditShow : (id) => { 
		return {
   		    type : 'fixed_edit_show',
		    id   : id
	    }	
	},
    /*隐藏融资招标编辑*/
	fixedEditHide : () => ({ 
   		type : 'fixed_edit_hide',
	}),	
	/*编辑融资招标框 input框值记录*/
	editInputChange : (name , value) => { 
		return {
		     type  : 'edit_input_change',
   		     name  : name,
   		     value : value
 	    }
	},	
	editTypeChange : (num) => { 		
		return {
		     type : 'edit_type_change',
  		     num  : parseInt(num)
	    }
	},
	/*ajax修改融资招标*/
	tenderEdit : () => (dispatch) => { 
        $.post('/index.php/Tender/tenderUpdate',cash.fixedEditData,function(res){
        	 if(res.status != 0){
            	 dispatch({
            		 type : 'tender_edit',
    	    		 data : res
            	 });        		 
        	 }else{
        		 alert(res.msg);
        	 }
        });
    },
    /*获取融资招标 缓存数据*/
    getData : (nowPage,parmers) => (dispatch, getState) => { 
    	cash.tableDataState = true;   
    	if(!cash.tableData[nowPage]){
	        $.post('/index.php/Tender/tenderList',parmers,function(res){	 		        	 
	        	 dispatch({
	        		 type    : 'get_tender_data',
	        		 nowPage : nowPage,
	        		 data    : res
	        	 });
	        });
        }else{
	       	 dispatch({
	    		 type    : 'get_tender_data',
	    		 nowPage : nowPage
	    	 });
        }
    } 
};

export default actions;


