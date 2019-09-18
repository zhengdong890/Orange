import React, { Component, PropTypes } from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Integrated/checkList';
var IMG_URL    = '/Public/admin_images/';
var IMG_UPLOAD = '/Public/Uploads/';

/* *
 * 表单
 * */
class TableList extends React.Component{
	render(){	
        const {value , dispatch } = this.props; 
        if(value.tableDataState){
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
	            <td className='table_td'>项目命名</td>
	            <td className='table_td'>机器使用经验</td>
	            <td className='table_td'>项目类型</td>     
	            <td className='table_td'>金融服务</td>
	            <td className='table_td'>申请时间</td>
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
		var type    = {'1':'弧焊','2':'切割分离','3':'上下料','4':'搬运'};
		var jinrong = {'1':'分期付款','2':'融资租赁','3':'直接购买'};
		data.map(function(v , index){ 
			var type_str = '';
			v.type.map(function(v , index){
				type_str += type[v] + ',';
			})
            table_list.push( 
                <tr key = {index}>
                   <td className='td'>                                   
                      <div className='select_checkbox'>
                            <input type='checkbox' className='checkbox' /><a>{v.id}</a>
                      </div> 
                   </td>
                   <td className = 'table_td'>{v.title}</td>
                   <td className = 'table_td'>{v.is_use == 1 ? '有' : '无'}</td>
                   <td className = 'table_td'>{type_str}</td>
                   <td className = 'table_td'>{jinrong[v.jinrong]}</td> 
                   <td className = 'table_td'>{v.update_time}</td>
                   <td className = 'table_handle'>
                       <a href = 'javascript:;' className = 'table_handle_a' onClick={() => {dispatch(actions.fixedCheckShow(v.id))}}>审核</a>
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