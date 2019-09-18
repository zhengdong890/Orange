import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Member/memberList';

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