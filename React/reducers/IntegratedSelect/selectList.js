import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import cash from '../../js/TenderSelect/selectList';

var data = cash.getConfig();

function counter(state = data , action) {
  switch (action.type) {
    /*获取中标融资租赁列表数据*/
    case 'get_select_data':
       if(action.data !== undefined){
    	   //ajax获取到的数据处理
    	   cash.setNewsData(action.data.data);
      	   cash.totalRows = action.data.total;
       };
       cash.nowPage = action.nowPage;//当前页数
       return cash.getData();  
    /*删除中标融资租赁列表数据*/
    case 'delete_select':
       delete cash.tableData[cash.nowPage][action.id]
       return cash.getData();       
    default:
       return state;
  }
}

// Store
const store = createStore(counter,applyMiddleware(thunk));
export default store;

