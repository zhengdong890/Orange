import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Businesses/mallApplicationList';

 /* *
   * 搜索部分
   * */
class searchForm extends React.Component{
	filedInputChange(dispatch){
		var dom = this.refs['name'];
		var val = dom.value;
		dispatch(actions.filedInputChange(val));
	}
	
	render(){
    	  const {value , dispatch } = this.props;
          return(
              <div id='searchForm'>         
	                <p>店铺名称: <input type='text' ref = 'name' name='name'  defaultValue = {value.search.name} onBlur = {this.filedInputChange.bind(this , dispatch)}></input></p>
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