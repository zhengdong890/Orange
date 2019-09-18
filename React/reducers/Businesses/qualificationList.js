import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
var data = {
	  'table_data' : {},
	  'table_data_state' : false,
	  
      check_data : {is_check:0,'content':''},
      check_data_id : '',
      check_show : false,
      
	  'data_page' :{
		  totalRows : '',
		  nowPage   : 1,
		  listRows  : 12,
		  rollPage  : 7
	  },
	  'search' : {
		  'name' : ''
	  }
} 

function counter(state = data , action) {
  switch (action.type) {
    case 'get_data':
       return action.data; 
    case 'check_show':
       return action.data;
    case 'changestatus':
       return action.data;
    case 'get_goods_model':
       return action.data;
    case 'get_category':
       return action.data;
    case 'change_model_status':
       return action.data;
    default:
       return state;
  }
}

// Store
const store = createStore(counter,applyMiddleware(thunk));
export default store;

