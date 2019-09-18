import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Order/orderList';

 /* *
   * 搜索部分
   * */
class searchForm extends React.Component{
	filedInputChange(ref , dispatch){
		var dom = this.refs[ref];
		var val = dom.value;
		dispatch(actions.filedInputChange(ref , val));
	}
	
	timeChange(ref){
		WdatePicker({
			el: ref ,
			dateFmt:'yyyy-MM-dd HH:mm:ss'
		})
	}
	
	render(){
    	  const {value , dispatch } = this.props;
          return(
              <div id='searchForm'>         
	                <p>订单号: <input type='text' ref = 'order_sn' name='order_sn'  defaultValue = {value.search.order_sn} onBlur = {this.filedInputChange.bind(this , 'order_sn' , dispatch)}></input></p>
	                <p>订单状态: 
	                	<select ref = 'status' name = 'status'  defaultValue = {value.search.status} onChange = {this.filedInputChange.bind(this , 'status' , dispatch)}>
	                        <option value = ''>全部</option>
	                        <option value = '0'>未付款</option>
	                        <option value = '1'>已付款</option>
	                    </select>
	                </p>
	                <p>发货状态: 
	                	<select ref = 'send_status' name = 'send_status'  defaultValue = {value.search.send_status} onChange = {this.filedInputChange.bind(this , 'send_status' , dispatch)}>
	                        <option value = ''>全部</option>
	                        <option value = '0'>未发货</option>
	                        <option value = '1'>已发货</option>
	                        <option value = '2'>已收货</option>
	                    </select>
	                </p>
	                <p>下单时间 :&nbsp; 
	                	<input id ='start_time' type = 'text' ref = 'start_time' name = 'start_time' className = 'input_text' onBlur = {this.filedInputChange.bind(this , 'start_time' , dispatch)}  onFocus = {this.timeChange.bind(this,'start_time')} />
	                    &nbsp;-&nbsp;
	                    <input id ='end_time' type = 'text' ref = 'end_time' name = 'end_time' className = 'input_text' onBlur = {this.filedInputChange.bind(this , 'end_time' , dispatch)} onFocus = {this.timeChange.bind(this,'end_time')}  />
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