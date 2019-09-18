import thunk from 'redux-thunk';
import cash from '../../js/Integrated/integratedList';

let actions = {
		
/*************************************添加集成项目********************************************************/    
	
    /*显示添加分组框*/
	fixedAddShow : () => ({
        type : 'fixed_add_show',	    	
	}),
    /*隐藏添加管理员框*/
	fixedAddHide : () => ({
        type : 'fixed_add_hide',	    	
	}),
	/*添加集成项目 input框值记录*/
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
	/*ajax集成项目*/
	integratedAdd : () => (dispatch) => { 
		console.log(cash.fixedAddData);
        $.post('/index.php/Integrated/integratedAdd',cash.fixedAddData,function(res){
        	 if(res.status != 0){
            	 dispatch({
            		 type : 'integrated_add',
    	    		 data : res
            	 });        		 
        	 }else{
        		 alert(res.msg);
        	 }
        });
    },
    
/*************************************集成项目详情********************************************************/    
	
    /*显示添加集成项目*/
    fixedDetailsShow : (id) => { 
		return {
   		    type : 'fixed_details_show',
		    id   : id
	    }	
	},
    /*隐藏添加集成项目*/
	fixedDetailsHide : () => ({ 
   		type : 'fixed_details_hide',
	}),
	
/*************************************集成项目编辑********************************************************/ 
    
	/*显示集成项目编辑*/
    fixedEditShow : (id) => { 
		return {
   		    type : 'fixed_edit_show',
		    id   : id
	    }	
	},
    /*隐藏集成项目编辑*/
	fixedEditHide : () => ({ 
   		type : 'fixed_edit_hide',
	}),	
	/*编辑集成项目框 input框值记录*/
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
	/*ajax修改集成项目*/
	integratedEdit : () => (dispatch) => { 
        $.post('/index.php/Integrated/integratedUpdate',cash.fixedEditData,function(res){
        	 if(res.status != 0){
            	 dispatch({
            		 type : 'integrated_edit',
    	    		 data : res
            	 });        		 
        	 }else{
        		 alert(res.msg);
        	 }
        });
    },
    /*获取集成项目 缓存数据*/
    getData : (nowPage,parmers) => (dispatch, getState) => { 
    	cash.tableDataState = true;   
    	if(!cash.tableData[nowPage]){
	        $.post('/index.php/Integrated/integratedList',parmers,function(res){	 		        	 
	        	 dispatch({
	        		 type    : 'get_integrated_data',
	        		 nowPage : nowPage,
	        		 data    : res
	        	 });
	        });
        }else{
	       	 dispatch({
	    		 type    : 'get_integrated_data',
	    		 nowPage : nowPage
	    	 });
        }
    } 
};

export default actions;


