import React from 'react'
import ReactDOM from 'react-dom'
import { Provider, connect } from 'react-redux'
/*加载各组件模板*/
import AppFixedTabEdit from './mallApplicationList/fixedTabEdit';
import AppPage from './mallApplicationList/page';
import AppTableList from './mallApplicationList/tableList';
import Crumbs from './mallApplicationList/Crumbs';
import AppSearchForm from './mallApplicationList/SearchForm';
import store from '../../reducers/Businesses/mallApplicationList';

/*加载样式*/
require('../../css/fixedTabEdit.css');
require('../../css/public.css');
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
                   <AppFixedTabEdit />
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



