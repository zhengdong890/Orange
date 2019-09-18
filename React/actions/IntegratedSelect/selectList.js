import thunk from 'redux-thunk';
import cash from '../../js/IntegratedSelect/selectList';

let actions = {
    /*获取中标集成项目数据  缓存数据*/
    getData : (nowPage,parmers) => (dispatch, getState) => { 
    	cash.tableDataState = true;    	
    	if(!cash.tableData[nowPage]){
	        $.post('/index.php/IntegratedSelect/selectList',parmers,function(res){	 
	        	 dispatch({
	        		 type    : 'get_select_data',
	        		 nowPage : nowPage,
	        		 data    : res
	        	 });
	        });
        }else{
	       	 dispatch({
	    		 type    : 'get_select_data',
	    		 nowPage : nowPage
	    	 });
        }
    },
    /*删除中标集成项目*/
    selectDelete : (id) => (dispatch) => {
    	if(confirm('是否删除')){
            $.post('/index.php/IntegratedSelect/selectDelete',{id : id},function(res){
           	 if(res.status){
               	 dispatch({
               		 type : 'delete_select',
               		 id   : id
               	 });        		 
           	 }else{
           		 alert(res.msg);
           	 }
           });
    	}
    },
};

export default actions;


