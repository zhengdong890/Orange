import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Goods/noPassGoodsList';
 /* *
   * 
   * */
class fixedCheckData extends React.Component{
	fieldValueChange(ref , dispatch){
		var dom  = this.refs[ref];
		var val  = dom.value;
		var name = dom.name;
		dispatch(actions.fieldValueChange(name , val));
	}
	
    render(){  
    	const {value , dispatch } = this.props;   
    	var data = value.check_data;
    	if(typeof(data) === 'undefined'){
    		return(
	            <div id = 'fixed_edit' className = {value.check_data_show?'':'fixed_edit_hide'}>    	
	        	</div> 
	        );
    	}
        return(
            <div id = 'fixed_edit' className = {value.check_data_show?'':'fixed_edit_hide'}>
        	    <div className = "fixed_edit_wraper">
        	         <h2 className = 'fixed_edit_title'>商品审核意见</h2>
        	         <ul className = 'fixed_edit_content'>
        	             <table cellspacing = "0">
        	                  <tr>
	                              <td>审核时间:</td>
	                              <td>{data.time}</td>
	                              <td>审核意见:</td>
	                              <td>{data.content}</td>
	                          </tr>    	                      
        	                  <tr>
	        	                  <td colSpan = '4'>
		        	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.checkDataHide())}}>确认</a>
	        	                  </td>
        	                  </tr>
        	             </table>           
        	         </ul>         
        	    </div>
        	</div>  
        )
    }
}

function mapStateToProps(state) {
   return {
      value: state
   }
}
export default connect(mapStateToProps)(fixedCheckData);