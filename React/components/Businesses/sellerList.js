import React from 'react'
import ReactDOM from 'react-dom'
import { Provider, connect } from 'react-redux'
/*加载各组件模板*/
import AppPage from './sellerList/page';
import AppTableList from './sellerList/tableList';
import Crumbs from './sellerList/Crumbs';
import AppSearchForm from './sellerList/SearchForm';
import store from '../../reducers/Businesses/sellerList';
/*加载样式*/
require('../../css/public.css');
require('../../css/Search_form.css');
require('../../css/Table_list.css');
require('../../css/page.css');
require('../../css/Crumbs.css');
class Container extends React.Component{	
	render(){
        return(
           <div id='container'>
               <Crumbs />
               <Provider store={store}> 
                   <AppSearchForm />
               </Provider>
	           <div id='main'>
	           <Provider store={store}> 
	               <AppTableList />
	           </Provider>
	           </div>
               <Provider store={store}>
               <AppPage />
               </Provider>
           </div>
        )
	}
}
ReactDOM.render(<Container />, document.getElementById('body'));



