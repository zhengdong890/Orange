import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import cash from '../../js/Integrated/checkList';

var data = cash.getConfig();

function counter(state = data , action) {
  switch (action.type) {
    /*获取集成项目数据*/
    case 'get_integrated_data':
       cash.nowPage = action.nowPage;//当前页数
       if(action.data !== undefined){
    	   //ajax获取到的数据处理
    	   cash.setIntegratedData(action.data.data);
      	   cash.totalRows = action.data.total;
       };      
       return cash.getData();          

/***************************************集成项目审核*****************************************************/       
    /*显示集成项目详情*/
    case 'fixed_check_show':
	   cash.fixedCheckData = cash.tableData[cash.nowPage][action.id];
       cash.fixedCheckShow = true;
       return cash.getData();
    /*隐藏编集成项目详情*/
    case 'fixed_check_hide':
       cash.fixedCheckShow = false;
       return cash.getData();
    /*审核集成项目框值改变*/
    case 'check_input_change':
       cash.fixedCheckData[action.name] = action.value;
       return cash.getData();
    /*集成项目框审核成功*/
    case 'check_success':
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

