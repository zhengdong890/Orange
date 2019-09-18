import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import cash from '../../js/News/ruleAdd';

var data = cash.getConfig();

function counter(state = data , action) {
  switch (action.type) { 
  /**/
  case 'input_news_change':
	 cash.newsData[action.name] = action.value;
     return cash.getData(); 
  /**/
  case 'news_add':
	 window.location.href = '/index.php/News/ruleList';
     return cash.getData();      
    default:
       return state;
  }
}

// Store
const store = createStore(counter,applyMiddleware(thunk));
export default store;


