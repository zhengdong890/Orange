import thunk from 'redux-thunk';
import cash from '../../js/Purchase/purchaseList';

let actions = {
		
/*************************************添加设备采购********************************************************/    
	
    /*显示添加设备采购框*/
	fixedAddShow : () => ({
        type : 'fixed_add_show',	    	
	}),
    /*隐藏添加设备采购框*/
	fixedAddHide : () => ({
        type : 'fixed_add_hide',	    	
	}),
	/*添加设备采购 input框值记录*/
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
	/*ajax设备采购*/
	purchaseAdd : () => (dispatch) => { 
		console.log(cash.fixedAddData);
        $.post('/index.php/Purchase/purchaseAdd',cash.fixedAddData,function(res){
        	 if(res.status != 0){
            	 dispatch({
            		 type : 'purchase_add',
    	    		 data : res
            	 });        		 
        	 }else{
        		 alert(res.msg);
        	 }
        });
    },
    
/*************************************设备采购详情********************************************************/    
	
    /*显示添加设备采购*/
    fixedDetailsShow : (id) => { 
		return {
   		    type : 'fixed_details_show',
		    id   : id
	    }	
	},
    /*隐藏添加设备采购*/
	fixedDetailsHide : () => ({ 
   		type : 'fixed_details_hide',
	}),
	
/*************************************设备采购编辑********************************************************/ 
    
	/*显示设备采购编辑*/
    fixedEditShow : (id) => { 
		return {
   		    type : 'fixed_edit_show',
		    id   : id
	    }	
	},
    /*隐藏设备采购编辑*/
	fixedEditHide : () => ({ 
   		type : 'fixed_edit_hide',
	}),	
	/*编辑设备采购框 input框值记录*/
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
	/*ajax修改设备采购*/
	purchaseEdit : () => (dispatch) => { 
        $.post('/index.php/Purchase/purchaseUpdate',cash.fixedEditData,function(res){
        	 if(res.status != 0){
            	 dispatch({
            		 type : 'purchase_edit',
    	    		 data : res
            	 });        		 
        	 }else{
        		 alert(res.msg);
        	 }
        });
    },
    /*获取设备采购 缓存数据*/
    getData : (nowPage,parmers) => (dispatch, getState) => { 
    	cash.tableDataState = true;   
    	if(!cash.tableData[nowPage]){
	        $.post('/index.php/Purchase/purchaseList',parmers,function(res){	 		        	 
	        	 dispatch({
	        		 type    : 'get_purchase_data',
	        		 nowPage : nowPage,
	        		 data    : res
	        	 });
	        });
        }else{
	       	 dispatch({
	    		 type    : 'get_purchase_data',
	    		 nowPage : nowPage
	    	 });
        }
    } 
};

export default actions;


