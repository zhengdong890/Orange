import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Auth/ruleList';
 /* *
   * 联动分类树
   * */
class fixedEdit extends React.Component{
	inputChange(ref , dispatch){
		var dom = this.refs[ref];
		var val = dom.value;
	    dispatch(actions.inputEditChange(ref , val));	
	}
	
    render(){  
    	const {value , dispatch } = this.props;   
    	var data = value.fixed_edit_data;
    	if(data.name === undefined){
    		return(
	            <div id = 'fixed_edit' className = {value.fixed_edit_show?'':'fixed_edit_hide'}>
	        	    <div className = "fixed_edit_wraper">
	        	         <h2 className = 'fixed_edit_title'>编辑规则</h2>
	        	         <ul className = 'fixed_edit_content'>
	        	         </ul>    
	        	    </div>
	        	</div>  
    	    );    		
    	}
        return(
            <div id = 'fixed_edit' className = {value.fixed_edit_show?'':'fixed_edit_hide'}>
        	    <div className = "fixed_edit_wraper">
        	         <h2 className = 'fixed_edit_title'>编辑规则</h2>
        	         <ul className = 'fixed_edit_content'>
        	             <table cellspacing="0">
        	                  <tr>
        	                      <td>所属上级:</td>
    	                          <td>
    	                              <input type = 'text' value = {data.p_name} disabled></input>
    	                              <a className = 'fixed_edit_a' href = 'javascript:;' onClick = {() => {dispatch(actions.catTreeShow())}}>切换</a>
    	                          </td>
        	                      <td>规则名称:</td>
        	                      <td><input type='text' name ='name' value = {data.name} ref = 'name' onChange = {this.inputChange.bind(this , 'name' ,dispatch)}></input></td>
        	                  </tr>
        	                  <tr>
    	                          <td>规则描述:</td>
    	                          <td><input type='text' name = 'title' value = {data.title} ref = 'title' onChange = {this.inputChange.bind(this , 'title' ,dispatch)}></input></td>
    	                          <td>condition:</td>
    	                          <td><input type='text' name = 'condition' value = {data.condition} ref = 'condition' onChange = {this.inputChange.bind(this , 'condition' ,dispatch)}></input></td>
    	                      </tr>
        	                  <tr>
	        	                  <td colSpan = '4'>
		        	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.ruleEdit())}}>确认</a>
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
export default connect(mapStateToProps)(fixedEdit);