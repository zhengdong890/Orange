import React, { Component, PropTypes } from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Businesses/mallApplicationList';
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
                         <a>编号</a>
                    </div> 
	            </td>	            
	            <td className='table_td'>企业名称</td>
	            <td className='table_td'>申请时间</td>
	            <td className='table_td'>审核时间</td>
	            <td className='table_td'>审核状态</td>
	            <td className='table_td'>是否审核</td>  
	            <td className='table_td'>操作</td> 
            </tr> 
        )
	}	
}

/*首页*/
class TableBody extends React.Component{
	render(){
	    var data          = this.props.data,
	        dispatch      = this.props.dispatch;
	    var table_list = [];
		var _this = this;
		data.map(function(v , index){  
            table_list.push( 
                <tr key = {index}>
                   <td className='td'>                                   
                      <div className='select_checkbox'>
                           <a>{v.id}</a>
                      </div> 
                   </td>
                   <td className='table_td'>{v.name}</td>                          
                   <td className='table_td'>{v.time}</td>
                   <td className='table_td'>{v.check_time}</td>
                   <td className='table_td'>{v.status=='1'?'审核通过':'审核未通过'}</td>
                   <td className='table_td'>{v.check_status=='1'?'已审核':'暂未审核'}</td>
                   <td className='table_handle'>
                        <a className = 'table_handle_a' href = 'javascript:;' onClick={() => dispatch(actions.checkDataShow(v.id , v.seller_id))}>审核</a> 
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