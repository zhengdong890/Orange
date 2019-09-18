import React, { Component, PropTypes } from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Order/checkList';
var IMG_URL    = '/Public/admin_images/';
var IMG_UPLOAD = '/Public/Uploads/';

/* *
 * 表单
 * */
class TableList extends React.Component{
	render(){	
        const {value , dispatch } = this.props;
        if(value.table_data_state){
            return (
                <form action="" method="post">   
                    <TableBody data = {value.table_data} dispatch = {dispatch} />
                </form>
            );
        }else{
            return (
                <form action="" method="post"></form>
            );
        }
	}	
}

/* *
 * 
 * */
class TableHeader extends React.Component{
	render(){	
		var dispatch    = this.props.dispatch;
        return(
            <tr>
	            <td className='td'>                                   
                    <div className='select_checkbox'>
                         <input type='checkbox' className='checkbox' /><a>id</a>
                    </div> 
	            </td>	            
	            <td className='table_td'>订单号</td>
	            <td className='table_td'>买家电话</td>
	            <td className='table_td'>买家姓名</td>
	            <td className='table_td'>买家地址</td>
	            <td className='table_td'>卖家姓名</td>
	            <td className='table_td'>卖家电话</td>
	            <td className='table_td'>订单价格</td>
	            <td className='table_td'>订单状态</td>
	            <td className='table_td'>下单时间</td>	            
	            <td className='table_td'>操作</td>           
            </tr> 
        )
	}	
}

/*首页*/
class TableBody extends React.Component{
	render(){
	    var data         = this.props.data,
	        dispatch      = this.props.dispatch;
	    var table_list = [],
	        state      = '';		
		var _this = this;
		data.map(function(v , index){  
            table_list.push( 
                <tr key = {index}>
                   <td className='td'>                                   
                      <div className='select_checkbox'>
                            <input type='checkbox' className='checkbox' /><a>{v.id}</a>
                      </div> 
                   </td>
                   <td className='table_td'>{v.order_sn}</td>
   	               <td className='table_td'>{v.tel_num}</td>
   	               <td className='table_td'>{v.name}</td>
   	               <td className='table_td'>{v.address}</td>
   	               <td className='table_td'>{v.seller_data.name}</td>
   	               <td className='table_td'>{v.seller_data.telnum}</td>
                   <td className='table_td'>{v.total_price}</td>
                   <td className='table_td'>
                       {v.status == 0 ? '未确认' : (v.status  == 1 ? '已确认':(v.status  == 2 ? '已完成':''))}
	                   &nbsp;
	                   {v.pay_status == 0 ? '未付款' : (v.pay_status == 1 ? '已付款':'')}
	                   &nbsp;
	                   {v.send_status == 0 ? '未发货' : (v.send_status == 1 ? '已发货':(v.send_status == 2 ? '已收货':''))}
	                   &nbsp;
	                   {v.is_comment == 0 ? '未评论' : (v.is_comment == 1 ? '已评论':'')}
                   </td>
                   <td className='table_td'>{v.time}</td>                   
                   <td className = 'table_handle'>
                       <a href = 'javascript:;' onClick={() => dispatch(actions.orderCheckShow(v.id))}>审核</a> 
                       <a> | </a>                     
                       <a href = 'javascript:;' onClick={() => dispatch(actions.orderDataShow(v.id))}>详情</a>                                     
                   </td>
                </tr> 
            )
		});
        return(
            <table cellSpacing='0' className="tableList"> 
                <tbody>
                <TableHeader dispatch = {dispatch} />
                {table_list}
                </tbody>
            </table>
        );
	}	
}

function mapStateToProps(state) {
   return {
      value: state
   }
}
export default connect(mapStateToProps)(TableList);