import thunk from 'redux-thunk';
import cash from '../../js/IntegratedSelect/selectAdd';

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
        $.post('/index.php/IntegratedSelect/selectAdd',cash.selectData,function(res){	        	 
        	 if(res.status){
        		 alert('添加成功');
            	 dispatch({
            		 type : 'select_add'
            	 });        		 
        	 }else{
        		 alert(res.msg);
        	 }
        });
    },
};

export default actions;


