import React from 'react'
import ReactDOM from 'react-dom'
import { Provider, connect } from 'react-redux'
/*加载各组件模板*/
import AppPage from './groupBuyList/page';
import AppTableList from './groupBuyList/tableList';
import AppCrumbs from './groupBuyList/Crumbs';
import AppFixedEdit from './groupBuyList/fixedEdit';
import AppFixedDetails from './groupBuyList/fixedDetails';
import store from '../../reducers/GroupBuy/groupBuyList';
/*加载样式*/
require('../../css/public.css');
require('../../css/Table_list.css');
require('../../css/fixedEdit.css');
require('../../css/page.css');
require('../../css/Crumbs.css');

class Container extends React.Component{	
	render(){
        return(
           <div id = 'container'>
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



