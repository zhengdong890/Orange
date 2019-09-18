import React from 'react'
import ReactDOM from 'react-dom'
import { Provider, connect } from 'react-redux'
/*加载各组件模板*/
import AppBlockCatTree from './checkList/blockCatTree';
import AppPage from './checkList/page';
import AppTableList from './checkList/tableList';
import Crumbs from './checkList/Crumbs';
import Handle from './checkList/Handle';
import AppFixedTabEdit from './checkList/fixedTabEdit';
import store from '../../reducers/Goods/checkList';
/*加载样式*/
require('../../css/checkList/checkList.css');
require('../../css/Crumbs.css');
require('../../css/fixedTabEdit.css');
require('../../css/checkList/blockCatTree.css');
require('../../css/checkList/tableList.css');
require('../../css/Handle.css');
require('../../css/page.css');
class Container extends React.Component{	
	render(){
        return(
           <div id = 'container'>
               <Provider store = {store}><AppFixedTabEdit /></Provider>
               <Crumbs />
	           <div id = 'main'>
                   <Provider store = {store}><AppBlockCatTree /></Provider>
                   <ul className = 'table_wraper'>
		               <Provider store={store}><AppTableList /></Provider>
		               <Provider store={store}><AppPage /></Provider>
	               </ul>
	           </div>
           </div>
        )
	}
}
ReactDOM.render(<Container />, document.getElementById('body'));



