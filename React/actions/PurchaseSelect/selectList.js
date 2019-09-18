import thunk from 'redux-thunk';
import cash from '../../js/PurchaseSelect/selectList';

let actions = {
    /*获取中标融资租赁数据  缓存数据*/
    getData : (nowPage,parmers) => (dispatch, getState) => { 
    	cash.tableDataState = true;    	
    	if(!cash.tableData[nowPage]){
	        $.post('/index.php/PurchaseSelect/selectList',parmers,function(res){	 
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
    /*删除中标融资租赁*/
    selectDelete : (id) => (dispatch) => {
    	if(confirm('是否删除')){
            $.post('/index.php/PurchaseSelect/selectDelete',{id : id},function(res){
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


