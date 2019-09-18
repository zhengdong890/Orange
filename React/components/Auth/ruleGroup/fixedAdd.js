import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Auth/ruleGroup';
 /* *
   * 添加数据
   * */
class fixedAdd extends React.Component{
	inputChange(ref , dispatch){
		var dom  = this.refs[ref];
		var val  = dom.value;
		var name = dom.name;
		dispatch(actions.addInputChange(name , val));
	}
	
    render(){  
    	const {value , dispatch } = this.props;   
    	var data = value.fixed_add_data;
    	if(data === undefined || data.title === undefined){
    		return(
    	       <div id = 'fixed_add' className = {value.fixed_add_show?'':'fixed_add_hide'}></div>
    	    );
    	}
    	var status = data.status == 1?true:false;
        return(
            <div id = 'fixed_add' className = {value.fixed_add_show?'':'fixed_add_hide'}>
        	    <div className = "fixed_add_wraper">
        	         <h2 className = 'fixed_add_title'>添加分組</h2>
        	         <ul className = 'fixed_add_content'>
        	             <table cellspacing = "0">
        	                  <tr>
        	                      <td className = 'label'>分组描述:</td>
    	                          <td>
    	                              <input type = 'text' name = 'title' ref = 'title' value = {data.title} onChange = {this.inputChange.bind(this , 'title' ,dispatch)}></input>
    	                          </td>
        	                      <td>是否开启:</td>
        	                      <td>
	                                  <input className = 'type_radio' type='radio' ref = 'is_check1' onClick = {this.inputChange.bind(this , 'is_check1' , dispatch)} name='status' value='1' checked = {status}/>
	                                  <p className = 'p_text'>通过</p>
	                                  <input className = 'type_radio' type='radio' ref = 'is_check2' onClick = {this.inputChange.bind(this , 'is_check2' , dispatch)} name='status' value='0' checked = {!status}/>
	                                  <p className = 'p_text'>不通过</p>
        	                      </td>
        	                  </tr>
        	                  <tr>
	        	                  <td colSpan = '4'>
		        	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.groupAdd())}}>确认</a>
		        	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.fixedAddHide())}}>取消</a>
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