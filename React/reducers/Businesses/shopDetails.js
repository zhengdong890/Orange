import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import cash from '../../js/Businesses/shopDetails';

var data = cash.getConfig();

function counter(state = data , action) {
  switch (action.type) { 
    default:
       return state;
  }
}

// Store
const store = createStore(counter,applyMiddleware(thunk));
export default store;

