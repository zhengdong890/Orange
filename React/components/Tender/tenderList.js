import React from 'react'
import ReactDOM from 'react-dom'
import { Provider, connect } from 'react-redux'
/*加载各组件模板*/
import AppPage from './tenderList/page';
import AppTableList from './tenderList/tableList';
import AppCrumbs from './tenderList/Crumbs';
import AppFixedAdd from './tenderList/fixedAdd';
import AppFixedDetails from './tenderList/fixedDetails';
import AppFixedEdit from './tenderList/fixedEdit';
import store from '../../reducers/Tender/tenderList';
/*加载样式*/
require('../../css/public.css');
require('../../css/Table_list.css');
require('../../css/fixedAdd.css');
require('../../css/fixedEdit.css');
require('../../css/page.css');
require('../../css/Crumbs.css');

class Container extends React.Component{	
	render(){
        return(
           <div id = 'container'>
               <Provider store = {store}> 
               <AppFixedAdd />
               </Provider>
               <Provider store = {store}> 
               <AppFixedEdit />
               </Provider>               
               <Provider store = {store}> 
               <AppFixedDetails />
               </Provider>
               <Provider store = {store}> 
               <AppCrumbs />
               </Provider>
	           <div id = 'main'>
	               <Provider store = {store}> 
	               <AppTableList />
	               </Provider>
	           </div>
               <Provider store = {store}>
               <AppPage />
               </Provider>
           </div>
        )
	}
}
ReactDOM.render(<Container />, document.getElementById('body'));



