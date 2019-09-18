import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
var data = {
	'data'  : [],
	'child' : [],
   	'rules_state' : false,
   	'fixed_edit_show' : false,
   	'fixed_add_show' : false,
   	'fixed_edit_data' : [],
   	'cat_tree_state' : [],
   	'fixed_add_data' : [],
} 

function counter(state = data , action) {
  switch (action.type) {
    case 'rules_add':
	    return action.data;
    case 'rules_update':
  	    return action.data;
    case 'input_add_change':
    	return action.data;  	    
    case 'input_edit_change':
    	return action.data;
    case 'cat_tree_state':
        return action.data;
    case 'fixed_add_data':
        return action.data;
    case 'fixed_add_hide':
        return action.data;
    case 'fixed_edit_data':
        return action.data; 
    case 'fixed_edit_hide':
        return action.data; 
    case 'get_rules':
        return action.data; 
    case 'show_child':
        return action.data; 
    case 'change_rules':
        return action.data;
    default:
        return state;
  }
}

// Store
const store = createStore(counter,applyMiddleware(thunk));
export default store;

