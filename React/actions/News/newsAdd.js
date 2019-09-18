import thunk from 'redux-thunk';
import cash from '../../js/News/newsAdd';

let actions = {
    editInputChange : (name , value) => { 
		return {
		     type  : 'input_news_change',
		     name  : name,
		     value : value
 	    }
	},	
    /*添加新闻资讯*/
    newsEdit : () => (dispatch) => { 
        $.post('/index.php/News/newsAdd',cash.newsData,function(res){	        	 
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


