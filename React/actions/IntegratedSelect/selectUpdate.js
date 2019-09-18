import thunk from 'redux-thunk';
import cash from '../../js/IntegratedSelect/selectUpdate';

let actions = {
    editInputChange : (name , value) => { 
		return {
		     type  : 'input_select_change',
		     name  : name,
		     value : value
 	    }
	},	
    /*添加新闻资讯*/
    newsEdit : () => (dispatch) => { 
        $.post('/index.php/IntegratedSelect/selectUpdate',cash.selectData,function(res){	        	 
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


