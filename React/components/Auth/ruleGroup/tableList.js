import React, { Component, PropTypes } from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Auth/ruleGroup';
var IMG_URL    = '/Public/admin_images/';
var IMG_UPLOAD = '/Public/Uploads/';

/* *
 * 表单
 * */
class TableList extends React.Component{
	render(){	
        const {value , dispatch } = this.props;
        value.tableData = value.tableData?value.tableData:[];
        if(value.table_data_state){
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
	            <td className='table_td'>分组描述</td>
	            <td className='table_td'>是否启用</td>
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
                   <td className = 'table_td'>{v.title}</td>
                   <td className = 'table_td'>
                       <a href='javascript:;' onClick={() => {dispatch(actions.groupStateChange(v.id , v.status))}}>
                           <img src={v.status==1?IMG_URL+'yes.gif':IMG_URL+'no.gif'} ref={'img'+v.id}></img>
                       </a>
                   </td>                   
                   <td className = 'table_handle'>
                       <a href = 'javascript:;' className = 'table_handle_a' onClick={() => {dispatch(actions.changeRules(v.id))}}>配置权限 </a>| 
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