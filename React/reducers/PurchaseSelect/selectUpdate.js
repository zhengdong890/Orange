import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import cash from '../../js/PurchaseSelect/selectUpdate';

var data = cash.getConfig();

function counter(state = data , action) {
  switch (action.type) { 
  /**/
  case 'input_select_change':
	 cash.selectData[action.name] = action.value;
     return cash.getData(); 
  /**/
  case 'select_update':
     window.location.href = '/index.php/PurchaseSelect/selectList';
     return cash.getData();      
    default:
       return state;
  }
}

// Store
const store = createStore(counter,applyMiddleware(thunk));
export default store;


