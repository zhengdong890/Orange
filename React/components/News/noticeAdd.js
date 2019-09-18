import React from 'react'
import ReactDOM from 'react-dom'
import { Provider, connect } from 'react-redux'
/*加载各组件模板*/
import AppCrumbs from './noticeAdd/Crumbs';
import AppBlockEdit from './noticeAdd/blockEdit';
import store from '../../reducers/News/noticeAdd';
/*加载样式*/
require('../../css/public.css');
require('../../css/Crumbs.css');
require('../../css/blockEdit.css');
class Container extends React.Component{	
	render(){
        return(
           <div id='container'>               
               <Provider store={store}> 
               <AppCrumbs />
               </Provider>
	           <div id='main'>
	           <Provider store={store}> 
                   <AppBlockEdit />
               </Provider>
	           </div>
           </div>
        )
	}
}
ReactDOM.render(<Container />, document.getElementById('body'));



