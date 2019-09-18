import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Admin/adminList';
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
    	var data    = value.fixedAddData;
    	var status  = data.lock == 1?true:false; 
    	var options = [];  
	  	if(value.groupState){
	  		var group   = value.group;			  		  
			group.map(function(v , key){
				options.push(<option value = {v.id} >{v.title}</option>);
			});
		}
        return(
            <div id = 'fixed_add' className = {value.fixedAddShow?'':'fixed_add_hide'}>
        	    <div className = "fixed_add_wraper">
        	         <h2 className = 'fixed_add_title'>添加管理员</h2>
        	         <ul className = 'fixed_add_content'>
        	             <table cellspacing = "0">
        	                  <tr>
        	                      <td className = 'label'>管理员账户：</td>
    	                          <td>
    	                              <input type = 'text' name = 'username' ref = 'username' value = {data.username} onChange = {this.inputChange.bind(this , 'username' ,dispatch)}></input>
    	                          </td>
        	                      <td>管理员密码:</td>
    	                          <td>
	                                  <input type = 'text' name = 'password' ref = 'password' value = {data.password} onChange = {this.inputChange.bind(this , 'password' ,dispatch)}></input>
	                              </td>
        	                  </tr>
        	                  <tr>
	    	                      <td className = 'label'>再次确认密码：</td>
		                          <td>
		                              <input type = 'text' name = 'repeatpassword' ref = 'repeatpassword' value = {data.repeatpassword} onChange = {this.inputChange.bind(this , 'repeatpassword' ,dispatch)}></input>
		                          </td>
	    	                      <td>管理员身份:</td>
		                          <td>
	                                  <select defaultValue = '0' name='group_id' ref = 'group_id' onChange = {this.inputChange.bind(this , 'group_id' ,dispatch)}>
	                                  <option value='0'>请选择</option>
	                                  {options}
	                                  </select>
	                              </td>
    	                      </tr>
        	                  <tr>
	    	                      <td className = 'label'>管理员姓名：</td>
		                          <td>
		                              <input type = 'text' name = 'name' ref = 'name' value = {data.name} onChange = {this.inputChange.bind(this , 'name' ,dispatch)}></input>
		                          </td>
		                          <td className = 'label'>是否锁定：</td>
	    	                      <td>
	                                  <input className = 'type_radio' type='radio' ref = 'lock_a1' onClick = {this.inputChange.bind(this , 'lock_a1' , dispatch)} name='lock' value='0' checked = {!status}/>
	                                  <p className = 'p_text'>锁定</p>
	                                  <input className = 'type_radio' type='radio' ref = 'lock_a2' onClick = {this.inputChange.bind(this , 'lock_a2' , dispatch)} name='lock' value='1' checked = {status}/>
	                                  <p className = 'p_text'>不锁定</p>
	    	                      </td>
    	                      </tr>
        	                  <tr>
	        	                  <td colSpan = '4'>
		        	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.adminAdd())}}>确认</a>
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