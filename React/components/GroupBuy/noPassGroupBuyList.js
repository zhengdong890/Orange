import React from 'react'
import ReactDOM from 'react-dom'
import { Provider, connect } from 'react-redux'
/*加载各组件模板*/
import AppPage from './noPassGroupBuyList/page';
import AppTableList from './noPassGroupBuyList/tableList';
import AppCrumbs from './noPassGroupBuyList/Crumbs';
import AppFixedDetails from './noPassGroupBuyList/fixedDetails';
import store from '../../reducers/GroupBuy/noPassGroupBuyList';
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



