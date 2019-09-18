import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
var data = {
	  'goods_data' : {},
	  'table_data_state' : false,
	  'fixed_edit_data':{},
	  'fixed_edit_hide':false,
	  'data_page' :{
		  totalRows : '',
   		  nowPage   : 1,
   		  listRows  : 12,
   		  rollPage  : 10
	  },
	  'search' : {
		  'username' : ''
	  }
} 

function counter(state = data , action) {
  switch (action.type) {
    case 'get_data':
       return action.data; 
    case 'show_edit':
       return action.data;
    case 'hide_edit':
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

