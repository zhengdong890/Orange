import thunk from 'redux-thunk';
import cash from '../../js/GroupBuy/noPassGroupBuyList';

let actions = {

/*************************************团购商品详情********************************************************/    
	
    /*显示详情框*/
	fixedDetailsShow : (id) => { 
		return {
   		    type : 'fixed_details_show',
		    id   : id
	    }	
	},
    /*隐藏详情框*/
	fixedDetailsHide : () => ({ 
   		type : 'fixed_details_hide',
	}),
    /*获取团购申请数据  缓存数据*/
    getData : (nowPage,parmers) => (dispatch, getState) => { 
    	cash.tableDataState = true;    	
    	if(!cash.tableData[nowPage]){
	        $.post('/index.php/GroupBuy/noPassGroupByList',parmers,function(res){	 		        	 
	        	 dispatch({
	        		 type    : 'get_group_data',
	        		 nowPage : nowPage,
	        		 data    : res
	        	 });
	        });
        }else{
	       	 dispatch({
	    		 type    : 'get_group_data',
	    		 nowPage : nowPage
	    	 });
        }
    },

};

export default actions;


