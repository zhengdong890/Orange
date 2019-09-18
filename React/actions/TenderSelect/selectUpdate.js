import thunk from 'redux-thunk';
import cash from '../../js/PurchaseSelect/selectUpdate';

let actions = {
    editInputChange : (name , value) => { 
		return {
		     type  : 'input_select_change',
		     name  : name,
		     value : value
 	    }
	},	
    /*添加批量采购*/
    newsEdit : () => (dispatch) => { 
        $.post('/index.php/PurchaseSelect/selectUpdate',cash.selectData,function(res){	        	 
        	 if(res.status){
        		 alert('修改成功');
            	 dispatch({
            		 type : 'select_update'
            	 });        		 
        	 }else{
        		 alert(res.msg);
        	 }
        });
    },
};

export default actions;


