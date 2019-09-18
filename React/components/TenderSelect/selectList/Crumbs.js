import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/TenderSelect/selectList';
/* *
   * 面包屑
   * */
class crumbs extends React.Component{
	 render(){   
		 const {value , dispatch } = this.props; 
         return(
	         <div id='Crumbs'>
	              <a>后台管理中心</a><span>&nbsp;&nbsp;-</span><a>中标融资租赁列表</a>
	              <a className = 'btn' href = "/index.php/TenderSelect/selectAdd">添加中标融资租赁</a>
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