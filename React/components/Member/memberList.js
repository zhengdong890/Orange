import React from 'react'
import ReactDOM from 'react-dom'
import { Provider, connect } from 'react-redux'
/*加载各组件模板*/
import AppPage from './memberList/page';
import AppFixedEdit from './memberList/fixedEdit';
import AppTableList from './memberList/tableList';
import Crumbs from './memberList/Crumbs';
import AppSearchForm from './memberList/SearchForm';
import store from '../../reducers/Member/memberList';
/*加载样式*/
require('../../css/public.css');
require('../../css/Crumbs.css');
require('../../css/fixedEdit.css');
require('../../css/Search_form.css');
require('../../css/Table_list.css');
require('../../css/page.css');

class Container extends React.Component{	
	render(){
        return(
           <div id='container'>
               <Crumbs />
               <Provider store = {store}> 
               <AppFixedEdit />
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



