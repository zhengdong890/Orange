import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions//MallOrder/checkList';
 /* *
   * 
   * */
class orderCheckShow extends React.Component{
	fieldValueChange(ref , dispatch){
		var dom  = this.refs[ref];
		var val  = dom.value;
		var name = dom.name;
		dispatch(actions.setCheckData(name , val));
	}
	
    render(){  
    	const {value , dispatch } = this.props;  
    	var data = value.order_check_data;
    	var status = data.status == 1?true:false;
    	if(typeof(data.id) === 'undefined'){
    		return(
	            <div id = 'fixed_edit' className = {value.order_check_show?'':'fixed_edit_hide'}>    	
	        	</div> 
	        );
    	}
        return(
            <div id = 'fixed_edit' className = {value.order_check_show?'':'fixed_edit_hide'}>
        	    <div className = "fixed_edit_wraper">
        	         <h2 className = 'fixed_edit_title'>订单审核</h2>
        	         <ul className = 'fixed_edit_content'>
        	             <table cellspacing = "0">
    	                  <tr>
                              <td>审核结果:</td>
                              <td>
                                  <input className = 'type_radio' type='radio' ref = 'status1' onClick = {this.fieldValueChange.bind(this , 'status1' , dispatch)} name='status' value='1' checked = {status}/>
                                  <p className = 'p_text'>通过</p>
                                  <input className = 'type_radio' type='radio' ref = 'status2' onClick = {this.fieldValueChange.bind(this , 'status2' , dispatch)} name='status' value='0' checked = {!status}/>
                                  <p className = 'p_text'>不通过</p>
                              </td>
                              <td>审核意见:</td>
                              <td>
                                  <textarea name = 'content' ref = 'content' onChange = {this.fieldValueChange.bind(this , 'content' ,dispatch)} value = {data.content}></textarea>
                              </td>
                          </tr>    	                      
    	                  <tr>
        	                  <td colSpan = '4'>
	        	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.orderCheckRequest())}}>确认</a>
	        	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.orderCheckHide())}}>取消</a>
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
export default connect(mapStateToProps)(orderCheckShow);