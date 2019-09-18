import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/MemberCarded/qualificationList';

 /* *
   * 搜索部分
   * */
class searchForm extends React.Component{
	filedInputChange(ref , dispatch){
		var dom = this.refs[ref];
		var val = dom.value;
		dispatch(actions.filedInputChange(ref , val));
	}
	
	render(){
    	  const {value , dispatch } = this.props;
          return(
              <div id='searchForm'>         
	               <p>会员账号: <input type='text' ref = 'username' name='username'  defaultValue = {value.search.username} onBlur = {this.filedInputChange.bind(this , 'username' , dispatch)}></input></p>
	               <p>是否审核: 
	                	<select ref = 'is_check' name = 'is_check'  defaultValue = {value.search.is_check} onChange = {this.filedInputChange.bind(this , 'is_check' , dispatch)}>
	                        <option value = '0'>未审核</option>
	                        <option value = '1'>已审核</option>
	                    </select>
	               </p>
	               <p>审核状态: 
	                	<select ref = 'status' name = 'status'  defaultValue = {value.search.status} onChange = {this.filedInputChange.bind(this , 'status' , dispatch)}>
	                        <option value = ''>全部</option>
	                        <option value = '0'>未通过</option>
	                        <option value = '1'>已通过</option>
	                    </select>
	               </p>
	               <button onClick={() => {dispatch(actions.getData())}}>搜索</button>
	          </div>
          )
      }
}
function mapStateToProps(state) {
   return {
      value: state
   }
}
export default connect(mapStateToProps)(searchForm);