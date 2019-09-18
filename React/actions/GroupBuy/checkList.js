import thunk from 'redux-thunk';
import cash from '../../js/GroupBuy/checkList';

let actions = {

/*************************************团购申请审核********************************************************/    
	
    /*显示审核框*/
	fixedCheckShow : (id) => { 
		return {
   		    type : 'fixed_check_show',
		    id   : id
	    }	
	},
    /*隐藏审核框*/
	fixedCheckHide : () => ({ 
   		type : 'fixed_check_hide',
	}),
	/*审核框 input框值记录*/
	checkInputChange : (name , value) => { 
		return {
		     type  : 'check_input_change',
   		     name  : name,
   		     value : value
 	    }
	},	
	/*ajax提交审核结果*/
	groupBuyCheck : () => (dispatch) => { 
		var data = {
	        id            : cash.fixedCheckData.id,
	        check_status  : cash.fixedCheckData.check_status,
	        check_content : cash.fixedCheckData.check_content
		};
        $.post('/index.php/GroupBuy/groupBuyCheck',data,function(res){
        	 if(res.status != 0){
            	 dispatch({
            		 type : 'group_buy_check',
    	    		 data : res
            	 });        		 
        	 }else{
        		 alert(res.msg);
        	 }
        });
    },
    /*获取团购申请数据  缓存数据*/
    getData : (nowPage,parmers) => (dispatch, getState) => { 
    	cash.tableDataState = true;    	
    	if(!cash.tableData[nowPage]){
	        $.post('/index.php/GroupBuy/checkList',parmers,function(res){	 		        	 
	        	 dispatch({
	        		 type    : 'get_check_data',
	        		 nowPage : nowPage,
	        		 data    : res
	        	 });
	        });
        }else{
	       	 dispatch({
	    		 type    : 'get_check_data',
	    		 nowPage : nowPage
	    	 });
        }
    },

};

export default actions;


