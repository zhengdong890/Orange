import React from 'react'
import ReactDOM from 'react-dom'
import { Provider, connect } from 'react-redux'
/*加载各组件模板*/
import AppPage from './goodsList/page';
import AppTableList from './goodsList/tableList';
import AppFixedEdit from './goodsList/fixedEdit';
import AppCrumbs from './goodsList/Crumbs';
import store from '../../reducers/MallGoodsSeo/goodsList';
/*加载样式*/
require('../../css/public.css');
require('../../css/fixedEdit.css');
require('../../css/Table_list.css');
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



