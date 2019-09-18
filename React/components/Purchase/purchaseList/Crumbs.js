import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Purchase/purchaseList';
/* *
   * 面包屑
   * */
class crumbs extends React.Component{
	 render(){   
		 const {value , dispatch } = this.props; 
         return(
	         <div id='Crumbs'>
	              <a>后台管理中心</a><span>&nbsp;&nbsp;-</span><a>设备采购列表</a>
	              <a className = 'btn' href = "javascript:;" onClick = {() => {dispatch(actions.fixedAddShow())}}>添加设备采购</a>
	         </div>   
         )
     }     
}
function mapStateToProps(state) {
   return {
      value: state
   }
}
export default connect(mapStateToProps)(crumbs);