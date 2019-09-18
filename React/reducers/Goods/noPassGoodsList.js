import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
var data = {
	  'goods_data' : {},
	  'check_data' : [],
	  'category'   : '',
	  
	  'check_data_show' : false,
	  'goods_state' : false,
	  'category_state' : false,
	  
	  'data_page' :{
		  totalRows : '',
		  nowPage   : 1,
		  listRows  : 10,
		  rollPage  : 10
	  },
	  'search' : {
		  'keyword' : '',
		  'cat_id'  : 0 
	  }		  
} 

function counter(state = data , action) {
  switch (action.type) {
    case 'get_data':
       return action.data; 
    case 'check_data_show':
       return action.data;
    case 'check_data_hide':
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

