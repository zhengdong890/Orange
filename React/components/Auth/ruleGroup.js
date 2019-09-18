import React from 'react'
import ReactDOM from 'react-dom'
import { Provider, connect } from 'react-redux'
/*加载各组件模板*/
import AppPage from './ruleGroup/page';
import AppTableList from './ruleGroup/tableList';
import AppCrumbs from './ruleGroup/Crumbs';
import AppFixedAdd from './ruleGroup/fixedAdd';
import AppFixedEdit from './ruleGroup/fixedEdit';
import AppCatTree from './ruleGroup/catTree';
import store from '../../reducers/Auth/ruleGroup';
/*加载样式*/
require('../../css/public.css');
require('../../css/Table_list.css');
require('../../css/fixedAdd.css');
require('../../css/fixedEdit.css');
require('../../css/page.css');
require('../../css/Crumbs.css');
require('../../css/fixedCatTree.css');

class Container extends React.Component{	
	render(){
        return(
           <div id = 'container'>
               <Provider store = {store}> 
               <AppCatTree />
               </Provider>
               <Provider store = {store}> 
               <AppFixedAdd />
               </Provider>
               <Provider store = {store}> 
               <AppFixedEdit />
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



