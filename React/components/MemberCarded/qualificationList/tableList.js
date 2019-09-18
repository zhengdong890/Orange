import React, { Component, PropTypes } from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/MemberCarded/qualificationList';
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
                         <a>id</a>
                    </div> 
	            </td>	            
	            <td className='table_td'>会员账号</td>
	            <td className='table_td'>真实姓名</td>
	            <td className='table_td'>身份证号码</td>
	            <td className='table_td'>是否审核</td>
	            <td className='table_td'>审核状态</td>
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
		           <td className='table_td'>{v.name}</td>
		           <td className='table_td'>{v.carded_code}</td>
		           <td className='table_td'>{v.is_check=='1'?'审核':'未审核'}</td>
		           <td className='table_td'>{v.status=='1'?'通过':'未通过'}</td>
                   <td className='table_handle'>
                        <a className = 'table_handle_a' href='javascript:;' onClick = {() => {dispatch(actions.fixedEdit(v.id))}}>审核</a>    
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