import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Auth/ruleGroup';
 /* *
   * 联动分类树
   * */
class fixedAdd extends React.Component{
	inputChange(ref , dispatch){
		var dom  = this.refs[ref];
		var val  = dom.value;
		var name = dom.name;
		if(ref == 'status_1' || ref == 'status_2'){
			name = 'status';
		}
		dispatch(actions.editInputChange(name , val));
	}
	
    render(){  
    	const {value , dispatch } = this.props;   
    	var data = value.fixed_edit_data;
    	if(data === undefined || data.title === undefined){
    		return(
    	       <div id = 'fixed_edit' className = {value.fixed_edit_show?'':'fixed_edit_hide'}></div>
    	    );
    	}
    	var status = data.status == 1?true:false;
        return(
            <div id = 'fixed_edit' className = {value.fixed_edit_show?'':'fixed_edit_hide'}>
        	    <div className = "fixed_edit_wraper">
        	         <h2 className = 'fixed_edit_title'>权限分组修改</h2>
        	         <ul className = 'fixed_edit_content'>
        	             <table cellspacing = "0">
			   	              <tr>
			                      <td className = 'label'>分组描述:</td>
			                      <td>
			                          <input type = 'text' name = 'title' ref = 'title' value = {data.title} onChange = {this.inputChange.bind(this , 'title' ,dispatch)}></input>
			                      </td>
			                      <td>是否开启:</td>
			                      <td>
			                          <input className = 'type_radio' type='radio' ref = 'status_1' onClick = {this.inputChange.bind(this , 'status_1' , dispatch)} name='status_1' value='1' checked = {status}/>
			                          <p className = 'p_text'>通过</p>
			                          <input className = 'type_radio' type='radio' ref = 'status_2' onClick = {this.inputChange.bind(this , 'status_2' , dispatch)} name='status_2' value='0' checked = {!status}/>
			                          <p className = 'p_text'>不通过</p>
			                      </td>
			                  </tr>
			                  <tr>
				                  <td colSpan = '4'>
			    	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.groupEdit())}}>确认</a>
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