import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import cash from '../../js/GroupBuy/groupBuyList';

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
       
/***************************************团购编辑*****************************************************/       
       /*显示编辑框*/
       case 'fixed_edit_show':
          var data = cash.tableData[cash.nowPage][action.id];
   	      cash.fixedEditData = data;
          cash.fixedEditShow = true;
          return cash.getData();
       /*隐藏审核框*/
       case 'fixed_edit_hide':
          cash.fixedEditShow = false;
          return cash.getData();    
          /*隐藏审核框*/
       case 'img_change':
          cash.fixedEditData[action.name] = action.value;
          return cash.getData();
          /*隐藏审核框*/
       case 'group_buy_update':
          cash.fixedEditShow = false;
          return cash.getData();    
          
/***************************************团购详情*****************************************************/       
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

