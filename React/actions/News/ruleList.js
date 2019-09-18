import thunk from 'redux-thunk';
import cash from '../../js/News/ruleList';

let actions = {
    /*获取规则数据  缓存数据*/
    getData : (nowPage,parmers) => (dispatch, getState) => { 
    	cash.tableDataState = true;    	
    	if(!cash.tableData[nowPage]){
	        $.post('/index.php/News/ruleList',parmers,function(res){	 
	        	 dispatch({
	        		 type    : 'get_news_data',
	        		 nowPage : nowPage,
	        		 data    : res
	        	 });
	        });
        }else{
	       	 dispatch({
	    		 type    : 'get_news_data',
	    		 nowPage : nowPage
	    	 });
        }
    },
    /*删除规则*/
    newsDelete : (id) => (dispatch) => {
    	if(confirm('是否删除')){
            $.post('/index.php/News/newsDelete',{id : id},function(res){
           	 if(res.status){
               	 dispatch({
               		 type : 'delete_news',
               		 id   : id
               	 });        		 
           	 }else{
           		 alert(res.msg);
           	 }
           });
    	}
    },
    /*更改规则状态*/
    newsStateChange : (id, status) => (dispatch) => { 
    	status = status == 1 ? 0 : 1;
        $.post('/index.php/News/newsStateChange',{id:id,status:status},function(res){
        	 if(res.status != 0){
            	 dispatch({
            		 type   : 'news_status_change',
                     id     : id,
                     status : status
            	 });        		 
        	 }else{
        		 alert(res.msg);
        	 }

        });
    },
};

export default actions;


