import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/PurchaseSelect/selectList';
/* *
   * 面包屑
   * */
class crumbs extends React.Component{
	 render(){   
		 const {value , dispatch } = this.props; 
         return(
	         <div id='Crumbs'>
	              <a>后台管理中心</a><span>&nbsp;&nbsp;-</span><a>中标批量采购列表</a>
	              <a className = 'btn' href = "/index.php/PurchaseSelect/selectAdd">添加中标批量采购</a>
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