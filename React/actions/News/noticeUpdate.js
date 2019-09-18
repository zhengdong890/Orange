import thunk from 'redux-thunk';
import cash from '../../js/News/noticeUpdate';

let actions = {
    editInputChange : (name , value) => { 
		return {
		     type  : 'input_news_change',
		     name  : name,
		     value : value
 	    }
	},	
    /*修改新闻资讯*/
    newsEdit : () => (dispatch) => { 
        $.post('/index.php/News/noticeUpdate',cash.newsData,function(res){	        	 
        	 if(res.status){
        		 alert('修改成功');
            	 dispatch({
            		 type : 'news_edit'
            	 });        		 
        	 }else{
        		 alert(res.msg);
        	 }
        });
    },
};

export default actions;


