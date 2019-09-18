import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
var data = {
    'table_data' : [],
	'order_data' : [],
	'order_check_data' : {is_check:0,'content':''},
			    
	'table_data_state' : false,		
			    		
	'order_data_show'  : false,	 
	'order_check_show' : false,	
			    
	'data_page'  : {
	     totalRows : '',
	     nowPage   : 1,
	     listRows  : 12,
	     rollPage  : 10
	},		
	'search' : {
        'order_sn'  : '',
        'status'    : '',
        'send_status' : '',
        'start_time' : '',
        'end_time' : ''
	}
		   		
} 

function counter(state = data , action) {
  switch (action.type) {
    case 'get_data':
       return action.data; 
    case 'order_data_show':
       return action.data;
    case 'order_check_show':
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

