import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Businesses/shopDetails';

 /* *
   * 
   * */
class blockEdit extends React.Component{
    render(){  
    	const {value , dispatch } = this.props;
    	var data = value.detailsData;
        return(		
	        <div className = 'block_edit'>		                    	   
	            <table>
	                <tr>
		                <td className = 'td_label'>企业名称:</td>
		                <td>{data.shop_data.shop_name}</td>
	                </tr> 
	                <tr>
			            <td className = 'td_label'>会员电话:</td>
			            <td>{data.member_data.telnum}</td>
		            </tr>                   
	            </table>
	        </div>	   
        )
    }
}

function mapStateToProps(state) {
   return {
      value: state    
   }
}
export default connect(mapStateToProps)(blockEdit);