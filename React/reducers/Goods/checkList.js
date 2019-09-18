import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
var data = {
	  'table_data' : [],
  	  'table_data_state' : false,
      'category' : '',  
	  'category_state' : false,
	  'cat_tree_data' : [],
      'cat_tree_data_state' : false,
      'child' : [],
      'goods_edit_data' :{},
	  'goods_edit_state' : false,
	  'goods_data' : {},
	  'edit_data'  : {},
	  'now_tab' : 1,
	  'data_page' :{
		  totalRows : '',
		  nowPage   : 1,
		  listRows  : 12,
		  rollPage  : 7
	  },
	  'search' : {
		  'keyword' : '',
		  'cat_id'  : 0 ,
	  }, 
	  'goods_check_data' : {'check_status':'0','content':''}
	  
} 

function counter(state = data , action) {
  switch (action.type) {
    case 'get_cat_tree':
        return action.data;
    case 'show_child':
        return action.data;        
    case 'get_goods_data':
       return action.data; 
    case 'get_category':
       return action.data;
    case 'goods_edit':
        return action.data; 
    case 'change_tab':
        return action.data;
    case 'goods_check':
        return action.data;    
    default:
       return state;
  }
}

// Store
const store = createStore(counter,applyMiddleware(thunk));
export default store;

