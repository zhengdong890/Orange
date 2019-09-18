import React from 'react'
import ReactDOM from 'react-dom'
import { Provider, connect } from 'react-redux'
/*加载各组件模板*/
import AppfixedTableList from './orderList/fixedTableList';
import AppPage from './orderList/page';
import AppTableList from './orderList/tableList';
import Crumbs from './orderList/Crumbs';
import AppSearchForm from './orderList/SearchForm';
import store from '../../reducers/Order/orderList';
/*加载样式*/
require('../../css/fixedTableList.css');
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
                  <AppfixedTableList />
               </Provider>
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



