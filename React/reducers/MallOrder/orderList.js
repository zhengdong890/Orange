import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
var data = {
	  'table_data' : [],
	  'order_data' : [],
	  'fixed_table_data':[],
	  
	  'fixed_table_show' : false,
	  'table_data_state' : false,
	  
	  'data_page' :{
		  totalRows : '',
		  nowPage   : 1,
		  listRows  : 10
	  },
	  'search' : {
		  'order_sn' : ''
	  }, 
} 

function counter(state = data , action) {
  switch (action.type) {
    case 'get_data':
       return action.data; 
    case 'fixed_table_show':
       return action.data;
    case 'changestatus':
       return action.data;
    case 'get_goods_model':
       return action.data;
    case 'get_category':
       return action.data;
    case 'change_model_status':
       return action.data;
    case 'change_pay_state':
        return action.data;
    default:
       return state;
  }
}

// Store
const store = createStore(counter,applyMiddleware(thunk));
export default store;

