import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
var data = {
	  'goods_data' : {},
	  'goods_model' : [],
	  'category'    : '',
	  
	  'goods_state' : false,
	  'goods_model_state' : false,
	  'category_state' : false,
	  
	  'data_page'  : {
		   totalRows : '',
		   nowPage   : 1,
		   listRows  : 10,
		   rollPage  : 10
	  },
	  
	  'search' : {
		  'keyword' : '',
		  'cat_id'  : 0 ,
		  'model_id': 0
	  }, 

} 

function counter(state = data , action) {
  switch (action.type) {
    case 'set_check_status':
       return action.data; 
    case 'get_data':
        return action.data; 
    case 'delete_data':
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

