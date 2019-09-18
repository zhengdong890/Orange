import React, { Component, PropTypes } from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/GroupBuy/groupBuyList';
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
	            <td className='table_td'>商品名称</td>
	            <td className='table_td'>团购价格</td>
	            <td className='table_td'>申请位置</td>
	            <td className='table_td'>申请时间</td>
	            <td className='table_td'>团购开始时间</td>
	            <td className='table_td'>团购持续时间</td>
	            <td className='table_td'>状态</td>
	            <td className='table_td'>操作</td>
            </tr> 
        )
	}	
}

/*首页*/
class TableBody extends React.Component{
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
                   <td className = 'table_td'><div className='table_td_text'>{v.goods_name}</div></td>
                   <td className = 'table_td'>{v.group_price}/万元</td>
                   <td className = 'table_td'>{v.ad_1 == 1 ? '首页' : v.ad_1 == 2 ?'推荐位':''}</td>
                   <td className = 'table_td'>{v.create_time}</td>
                   <td className = 'table_td'>{v.start_time}</td>  
                   <td className = 'table_td'>{v.time}</td>
                   <td className = 'table_td'>{v.is_guoqi == 1 ? '已过期' : '进行中'}</td>
                   <td className = 'table_handle'>
                       <a href = 'javascript:;' className = 'table_handle_a' onClick={() => {dispatch(actions.fixedEditShow(v.id))}}>编辑 </a>|
                       <a href = 'javascript:;' className = 'table_handle_a' onClick={() => {dispatch(actions.fixedDetailsShow(v.id))}}> 查看详情</a>
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