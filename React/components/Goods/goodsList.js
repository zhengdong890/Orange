import React from 'react'
import ReactDOM from 'react-dom'
import { Provider, connect } from 'react-redux'
/*加载各组件模板*/
import AppPage from './goodsList/page';
import AppTableList from './goodsList/tableList';
import Crumbs from './goodsList/Crumbs';
import AppSearchForm from './goodsList/SearchForm';
import Handle from './goodsList/Handle';
import store from '../../reducers/goods/goodsList';
/*加载样式*/
require('../../css/public.css');
require('../../css/Search_form.css');
require('../../css/Table_list.css');
require('../../css/Handle.css');
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



