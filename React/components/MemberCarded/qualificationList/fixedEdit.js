import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/MemberCarded/qualificationList';
 /* *
   * 联动分类树
   * */
class fixedAdd extends React.Component{
	fieldValueChange(ref , dispatch){
		var dom  = this.refs[ref];
		var val  = dom.value;
		var name = dom.name;
		dispatch(actions.fieldValueChange(name , val));
	}
	
    render(){  
    	const {value , dispatch } = this.props;   
    	var data = value.fixed_edit_data;
    	var status = data.status == 1?true:false;
    	if(typeof(data.status) === 'undefined'){
    		return(
	            <div id = 'fixed_edit' className = {value.fixed_edit_hide?'':'fixed_edit_hide'}>    	
	        	</div> 
	        );
    	}
        return(
            <div id = 'fixed_edit' className = {value.fixed_edit_hide?'':'fixed_edit_hide'}>
        	    <div className = "fixed_edit_wraper">
        	         <h2 className = 'fixed_edit_title'>会员身份审核</h2>
        	         <ul className = 'fixed_edit_content'>
        	             <table cellspacing = "0">
        	                  <tr>
        	                      <td className = 'label'>会员名字:</td>
    	                          <td>{data.name}</td>
        	                      <td>身份证号码:</td>
        	                      <td>{data.carded_code}</td>
        	                  </tr>
        	                  <tr>
    	                          <td>身份证正面:</td>
    	                          <td><img className = 'img' src = {data.carded_thumb1?data.carded_thumb1.substring(1):''} /></td>
    	                          <td>身份证反面:</td>
    	                          <td><img className = 'img' src = {data.carded_thumb2?data.carded_thumb2.substring(1):''} /></td>
    	                      </tr>
        	                  <tr>
	                          <td>身份证反面面:</td>
	                          <td><img className = 'img' src = {data.carded_thumb1?data.carded_thumb3.substring(1):''} /></td>
	                          <td></td>
	                          <td></td>
	                      </tr>
        	                  <tr>
	                              <td>审核结果:</td>
	                              <td>
	                                  <input className = 'type_radio' type='radio' ref = 'is_check1' onClick = {this.fieldValueChange.bind(this , 'is_check1' , dispatch)} name='status' value='1' checked = {status}/>
	                                  <p className = 'p_text'>通过</p>
	                                  <input className = 'type_radio' type='radio' ref = 'is_check2' onClick = {this.fieldValueChange.bind(this , 'is_check2' , dispatch)} name='status' value='0' checked = {!status}/>
	                                  <p className = 'p_text'>不通过</p>
	                              </td>
	                              <td>审核意见:</td>
	                              <td>
	                                  <textarea name='content' ref = 'content' onBlur = {this.fieldValueChange.bind(this , 'content' , dispatch)} defaultValue = {data.content}></textarea>
	                              </td>
	                          </tr>    	                      
        	                  <tr>
	        	                  <td colSpan = '4'>
		        	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.fixedEditRequest())}}>确认</a>
		        	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.fixedEditHide())}}>取消</a>
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
export default connect(mapStateToProps)(fixedAdd);