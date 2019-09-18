import thunk from 'redux-thunk';
import cash from '../../js/Integrated/checkList';

let actions = {   
	
   
    
/*************************************集成项目审核********************************************************/    
	
    /*显示添加集成项目*/
	fixedCheckShow : (id) => { 
		return {
   		    type : 'fixed_check_show',
		    id   : id
	    }	
	},
    /*隐藏添加集成项目*/
	fixedCheckHide : () => ({ 
   		type : 'fixed_check_hide',
	}),
	/*审核集成项目框 input框值记录*/
	checkInputChange : (name , value) => { 
		return {
		     type  : 'check_input_change',
   		     name  : name,
   		     value : value
 	    }
	},
	/*ajax修改集成项目*/
	integratedCheck : () => (dispatch) => { 
		var parmer = {
		    id            : cash.fixedCheckData.id,
		    check_status  : cash.fixedCheckData.check_status,
		    check_content : cash.fixedCheckData.check_content
		};
        $.post('/index.php/Integrated/integratedCheck',parmer,function(res){
        	 if(res.status != 0){
            	 dispatch({
            		 type : 'check_success',
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
	        $.post('/index.php/Integrated/checkList',parmers,function(res){	 		        	 
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


