import React, { Component, PropTypes } from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Member/memberList';
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
	            <td className='table_td'>会员账号</td>
	            <td className='table_td'>上次登录时间</td>
	            <td className='table_td'>身份认证状态</td>
	            <td className='table_td'>商城开通状态</td>
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
	    var data_         = this.props.data,
	        dispatch      = this.props.dispatch;
	    var table_list = [],
	        state      = '';
		var data = [];
		for(var k in data_){
			data.push(data_[k]);
		}
		var _this = this;
		data.map(function(v , index){  
            table_list.push( 
                <tr key = {index}>
                   <td className='td'>                                   
                      <div className='select_checkbox'>
                           <a>{v.id}</a>
                      </div> 
                   </td>
	   	           <td className='table_td'>{v.username}</td>
	   	           <td className='table_td'>{v.login_time}</td>
		           <td className='table_td'>{v.is_renzheng == '1' ? '已认证'  : '未认证'}</td>
		           <td className='table_td'>{v.is_mall == '1' ? '已开通'  : '未开通'}</td>
		           <td className='table_td'>{v.lock == '1' ? '未锁定':'已锁定'}</td>
                   <td className='table_handle'>
                        <a className = 'table_handle_a' href = 'javascript:;' onClick = {() => {dispatch(actions.changeLock(v.id , v.lock))}}>{v.lock == '1' ? '锁定':'解除锁定'}</a>    
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