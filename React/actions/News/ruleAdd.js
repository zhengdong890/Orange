import thunk from 'redux-thunk';
import cash from '../../js/News/ruleAdd';

let actions = {
    editInputChange : (name , value) => { 
		return {
		     type  : 'input_news_change',
		     name  : name,
		     value : value
 	    }
	},	
    /*添加规则*/
    newsEdit : () => (dispatch) => { 
        $.post('/index.php/News/ruleAdd',cash.newsData,function(res){	        	 
        	 if(res.status){
        		 alert('添加成功');
            	 dispatch({
            		 type : 'news_add'
            	 });        		 
        	 }else{
        		 alert(res.msg);
        	 }
        });
    },
};

export default actions;


