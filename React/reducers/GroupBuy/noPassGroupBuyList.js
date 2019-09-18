import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import cash from '../../js/GroupBuy/noPassGroupBuyList';

var data = cash.getConfig();

function counter(state = data , action) {
  switch (action.type) {
    /*获取团购审核列表数据*/
    case 'get_group_data':
       cash.nowPage = action.nowPage;//当前页数
       if(action.data !== undefined){
    	   //ajax获取到的数据处理
    	   cash.setCheckData(action.data.data);
      	   cash.totalRows = action.data.total;
       };
       return cash.getData();
    

/***************************************团购审核*****************************************************/       
    /*显示审核框*/
    case 'fixed_details_show':
       var data = cash.tableData[cash.nowPage][action.id];
	   cash.fixedDetailsData = data;
       cash.fixedDetailsShow = true;
       return cash.getData();
    /*隐藏审核框*/
    case 'fixed_details_hide':
       cash.fixedDetailsShow = false;
       return cash.getData();   
    default:
       return state;
  }
}

// Store
const store = createStore(counter,applyMiddleware(thunk));
export default store;

