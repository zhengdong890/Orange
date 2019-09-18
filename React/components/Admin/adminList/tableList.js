import React, { Component, PropTypes } from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Admin/adminList';
var IMG_URL    = '/Public/admin_images/';
var IMG_UPLOAD = '/Public/Uploads/';

/* *
 * 表单
 * */
class TableList extends React.Component{
	render(){	
        const {value , dispatch } = this.props;        
        if(value.tableDataState){
        	dispatch(actions.getGroup());
            return (
                <form action="" method="post">   
                    <TableBody data = {value.tableData} dispatch = {dispatch} />
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
        return(
            <tr>
	            <td className='td'>                                   
                    <div className='select_checkbox'>
                         <input type='checkbox' className='checkbox' /><a>id</a>
                    </div> 
	            </td>	            
	            <td className='table_td'>账号</td>
	            <td className='table_td'>所属分组</td>
	            <td className='table_td'>上次登录时间</td>               
	            <td className='table_td'>上次登录ip</td>     
	            <td className='table_td'>锁定状态</td>
	            <td className='table_td'>操作</td>
            </tr> 
        )
	}	
}

/*首页*/
class TableBody extends React.Component{
	sortChange(id , dispatch){
		var dom = this.refs['sort'+id];
		var val = dom.value;
		dispatch(actions.sortChange(id , val));
	}
	
	render(){
	    var data       = this.props.data,
	        dispatch   = this.props.dispatch;
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
                   <td className = 'table_td'>{v.username}</td>
                   <td className = 'table_td'>{v.group_name}</td>
                   <td className = 'table_td'>{v.login_time}</td>
                   <td className = 'table_td'>{v.login_ip}</td>  
                   <td className = 'table_td'>{v.lock == 1 ? '未锁定' : '已锁定'}</td>
                   <td className = 'table_handle'>
                       <a href = 'javascript:;' className = 'table_handle_a' onClick={() => {dispatch(actions.lockChange(v.id , v.lock))}}>{v.lock == 1 ? '锁定' : '解除锁定'} </a>| 
                       <a href = 'javascript:;' className = 'table_handle_a' onClick={() => {dispatch(actions.fixedEditShow(v.id))}}> 编辑</a>
                   </td>
                </tr> 
            )
		});
        return(
            <table cellSpacing='0' className="tableList"> 
                <tbody>
                <TableHeader />
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