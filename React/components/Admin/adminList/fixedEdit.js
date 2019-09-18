import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Admin/adminList';
 /* *
   * 联动分类树
   * */
class fixedAdd extends React.Component{
	inputChange(ref , dispatch){
		var dom  = this.refs[ref];
		var val  = dom.value;
		var name = dom.name;
		if(ref == 'lock_e1' || ref == 'lock_e2'){
			name = 'lock';
		}
		dispatch(actions.editInputChange(name , val));
	}
	
    render(){  
    	const {value , dispatch } = this.props;   
    	var data = value.fixedEditData;    	
    	var status  = data.lock   == 1?true:false; 
    	var is_pwd  = data.is_pwd == 1?true:false; 
    	var is_user  = data.is_user == 1?true:false; 
    	var options = []; 
	  	if(value.groupState){
	  		var group   = value.group;			  		  
			group.map(function(v , key){
				options.push(<option value = {v.id} >{v.title}</option>);
			});
		}
        return(
            <div id = 'fixed_edit' className = {value.fixedEditShow?'':'fixed_edit_hide'}>
        	    <div className = "fixed_edit_wraper">
        	         <h2 className = 'fixed_edit_title'>管理员信息修改</h2>
        	         <ul className = 'fixed_edit_content'>
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
			                          <select value = {data.group_id} name='group_id' ref = 'group_id' onChange = {this.inputChange.bind(this , 'group_id' ,dispatch)}>
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
			                          <input className = 'type_radio' type='radio' ref = 'lock_e1' onClick = {this.inputChange.bind(this , 'lock_e1' , dispatch)} name='lock_e1' value='0' checked = {!status}/>
			                          <p className = 'p_text'>锁定</p>
			                          <input className = 'type_radio' type='radio' ref = 'lock_e2' onClick = {this.inputChange.bind(this , 'lock_e2' , dispatch)} name='lock_e2' value='1' checked = {status}/>
			                          <p className = 'p_text'>不锁定</p>
			                      </td>
			                  </tr>
			                  <tr>
			                      <td className = 'label'>是否修改账号：</td>
			                      <td>
			                          <input className = 'type_radio' type='radio' ref = 'is_user1' onClick = {this.inputChange.bind(this , 'is_user1' , dispatch)} name='is_user' value='1' checked = {is_user}/>
			                          <p className = 'p_text'>是</p>
			                          <input className = 'type_radio' type='radio' ref = 'is_user2' onClick = {this.inputChange.bind(this , 'is_user2' , dispatch)} name='is_user' value='0' checked = {!is_user}/>
			                          <p className = 'p_text'>否</p>
			                      </td>
			                      <td className = 'label'>是否修改密码：</td>
			                      <td>
			                          <input className = 'type_radio' type='radio' ref = 'is_pwd1' onClick = {this.inputChange.bind(this , 'is_pwd1' , dispatch)} name='is_pwd' value='1' checked = {is_pwd}/>
			                          <p className = 'p_text'>是</p>
			                          <input className = 'type_radio' type='radio' ref = 'is_pwd2' onClick = {this.inputChange.bind(this , 'is_pwd2' , dispatch)} name='is_pwd' value='0' checked = {!is_pwd}/>
			                          <p className = 'p_text'>否</p>
			                      </td>
		                      </tr>
			                  <tr>
				                  <td colSpan = '4'>
			    	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.adminEdit())}}>确认</a>
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