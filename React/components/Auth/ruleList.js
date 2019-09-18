import React from 'react'
import ReactDOM from 'react-dom'
import { Provider, connect } from 'react-redux'
/*加载各组件模板*/
import Crumbs from './ruleList/Crumbs';
import AppFixedEdit from './ruleList/fixedEdit';
import AppFixedAdd from './ruleList/fixedAdd';
import AppBlockCatTree from './ruleList/blockCatTree';
import AppFixedCatTree from './ruleList/fixedCatTree';
import store from '../../reducers/Auth/ruleList';
/*加载样式*/
require('../../css/public.css');
require('../../css/fixedEdit.css');
require('../../css/fixedAdd.css');
require('../../css/blockCatTree.css');
require('../../css/fixedCatTree.css');
require('../../css/Crumbs.css');
class Container extends React.Component{	
	render(){
        return(
           <div id = 'container'>
               <Crumbs />
               <Provider store = {store}> 
                   <AppFixedCatTree />
               </Provider>
                   <Provider store = {store}> 
               <AppFixedAdd />
               </Provider>
               <Provider store = {store}> 
                   <AppFixedEdit />
               </Provider>
	           <div id = 'main'>
	               <Provider store = {store}> 
	                   <AppBlockCatTree />
	               </Provider>
	           </div>
           </div>
        )
	}
}
ReactDOM.render(<Container />, document.getElementById('body'));



