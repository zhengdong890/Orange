import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/GroupBuy/noPassGroupBuyList';
/* *
   * 面包屑
   * */
class crumbs extends React.Component{
	 render(){   
		 const {value , dispatch } = this.props; 
         return(
	         <div id='Crumbs'>
	              <a>后台管理中心</a><span>&nbsp;&nbsp;-</span><a>团购管理</a><span>&nbsp;&nbsp;-</span><a>未通过的商品</a>	             
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