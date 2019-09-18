import React from 'react'
import ReactDOM from 'react-dom'
import { Provider, connect } from 'react-redux'
/*加载各组件模板*/
import AppOrderDataShow from './checkList/orderDataShow';
import AppOrderCheckShow from './checkList/orderCheckShow';
import AppPage from './checkList/page';
import AppTableList from './checkList/tableList';
import Crumbs from './checkList/Crumbs';
import AppSearchForm from './checkList/SearchForm';
import store from '../../reducers/Order/checkList';
/*加载样式*/
require('../../css/fixedTableList.css');
require('../../css/public.css');
require('../../css/fixedEdit.css');
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
                  <AppOrderDataShow />
               </Provider>
               <Provider store={store}> 
                  <AppOrderCheckShow />
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



