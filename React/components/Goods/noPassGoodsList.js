import React from 'react'
import ReactDOM from 'react-dom'
import { Provider, connect } from 'react-redux'
/*加载各组件模板*/
import AppFixedCheckData from './noPassGoodsList/fixedCheckData';
import AppPage from './noPassGoodsList/page';
import AppTableList from './noPassGoodsList/tableList';
import Crumbs from './noPassGoodsList/Crumbs';
import AppSearchForm from './noPassGoodsList/SearchForm';
import store from '../../reducers/goods/noPassGoodsList';
/*加载样式*/
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
                   <AppFixedCheckData />
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



