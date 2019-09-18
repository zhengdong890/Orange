import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import cash from '../../js/GroupBuy/checkList';

var data = cash.getConfig();

function counter(state = data , action) {
  switch (action.type) {
    /*获取团购审核列表数据*/
    case 'get_check_data':
       cash.nowPage = action.nowPage;//当前页数
       if(action.data !== undefined){
    	   //ajax获取到的数据处理
    	   cash.setCheckData(action.data.data);
      	   cash.totalRows = action.data.total;
       };
       return cash.getData();
    

/***************************************团购审核*****************************************************/       
    /*显示审核框*/
    case 'fixed_check_show':
       var data = cash.tableData[cash.nowPage][action.id];
	   cash.fixedCheckData = data;
       cash.fixedCheckShow = true;
       return cash.getData();
    /*隐藏审核框*/
    case 'fixed_check_hide':
       cash.fixedCheckShow = false;
       return cash.getData();   
    /*审核框值改变*/
    case 'check_input_change':
       cash.fixedCheckData[action.name] = action.value;
       return cash.getData();       
    /*审核成功*/   
    case 'group_buy_check':
       delete cash.tableData[cash.nowPage][cash.fixedCheckData.id];
       cash.fixedCheckShow = false;
       return cash.getData();
    default:
       return state;
  }
}

// Store
const store = createStore(counter,applyMiddleware(thunk));
export default store;

