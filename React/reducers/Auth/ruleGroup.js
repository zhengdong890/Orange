import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
var data = {
	'tableData'   : [],
	'data_page'   : {
	    totalRows : '',
		nowPage   : 1,
		listRows  : 10
	},	
	'rules'       : [],
	'rules_check' : [],
	'change_rule_state' : false,
   	'rules_state' : false,

} 

function counter(state = data , action) {
  switch (action.type) {
    case 'get_data':
       return action.data; 
    case 'get_rules':
       return action.data; 
    case 'change_select':
       return action.data; 
    case 'change_rules':
       return action.data;
    case 'fixed_add_data':
       return action.data;
    case 'fixed_edit_data':
        return action.data;
    default:
       return state;
  }
}

// Store
const store = createStore(counter,applyMiddleware(thunk));
export default store;

