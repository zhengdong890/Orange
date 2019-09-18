/**
 * 
 */
import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions//MallOrder/checkList';
class orderDataShow extends React.Component{
    render(){	
        const {value , dispatch } = this.props;
        var order_data = value.order_data;
        return (
            <div id = 'fixed_table' className = {value.order_data_show?'':'fixed_table_hide'}>   
                <ul>
                    <TableBody data = {order_data} dispatch = {dispatch} />
                </ul>
            </div>
        );
	}
}


class TableBody extends React.Component{
	render(){
		const {data , dispatch } = this.props;
		var _this = this;
		var table_list = [];
		data.map(function(v , index){ 
            table_list.push( 
                <tr key = {index}>
                   <td className='table_td'>{v.id}</td>
                   <td className='table_td max_width'>
                       {v.goods_name}
                   </td>
                   <td className='table_td'>{v.goods_price/10000}万</td>
                   <td className='table_td'>{v.rent_number}</td>
                   <td className='table_td'>{v.total_price/10000}万</td>
                   <td className='table_handle'>
                   </td>
                </tr> 
            )
		});
        return(
            <table cellSpacing='0' className="fixedtableList"> 
                <tbody>
                <tr>
		            <td colSpan = '6' className = 'td_colspan'>
		                <a className = 'td_btn' href = 'javascript:;' onClick={() => {dispatch(actions.orderDataHide())}}>确认</a>
		            </td>	          
                </tr> 
                <TableHeader dispatch = {dispatch} />
                {table_list}
                </tbody>
            </table>
        );
	}	
}

/* *
 * 表格头部 
 * */
class TableHeader extends React.Component{
	render(){	
		var dispatch    = this.props.dispatch;
        return(
            <tr>
	            <td className='table_td'>编号</td>	
	            <td className='table_td'>商品名称</td>
	            <td className='table_td'>单价</td>
	            <td className='table_td'>数量</td>
	            <td className='table_td'>总价</td>
	            <td className='table_td'>操作</td>           
            </tr> 
        )
	}	
}

function mapStateToProps(state) {
   return {
      value: state
   }
}
export default connect(mapStateToProps)(orderDataShow);