import React from 'react'
import ReactDOM from 'react-dom'
import { Provider, connect } from 'react-redux'
/*加载各组件模板*/
import AppPage from './newsList/page';
import AppTableList from './newsList/tableList';
import AppCrumbs from './newsList/Crumbs';
import store from '../../reducers/News/newsList';
/*加载样式*/
require('../../css/public.css');
require('../../css/Table_list.css');
require('../../css/page.css');
require('../../css/Crumbs.css');

class Container extends React.Component{	
	render(){
        return(
           <div id = 'container'>
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



