import React, { Component, PropTypes } from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/MallGoodsSeo/goodsList';

/* *
 * 表单
 * */
class TableList extends React.Component{
	render(){	
        const {value , dispatch } = this.props; 
        if(value.tableDataState){
            return (
                <form action="" method="post">   
                    <TableBody data = {value.tableData} goodsSeo = {value.goodsSeo} dispatch = {dispatch} />
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
                        <a>id</a>
                    </div> 
	            </td>	
	            <td className='table_td'>商品名称</td>
	            <td className='table_td'>标题</td>
	            <td className='table_td'>关键字</td>
	            <td className='table_td'>描述</td>
	            <td className='table_td'>操作</td>
            </tr> 
        )
	}	
}

/*首页*/
class TableBody extends React.Component{
	render(){
	    var data       = this.props.data,
	        goodsSeo   = this.props.goodsSeo,
	        dispatch   = this.props.dispatch;
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
                   <td className = 'table_td'>
                       <div className = 'table_td_text'>{v.goods_name}</div>
                   </td> 
                   <td className = 'table_td'>{goodsSeo[index]?goodsSeo[index].title:''}</td>
                   <td className = 'table_td'>
                       <div className = 'table_td_text'>{goodsSeo[index]?goodsSeo[index].keyword:''}</div>
                   </td> 
                   <td className = 'table_td'>
                       <div className = 'table_td_text'>{goodsSeo[index]?goodsSeo[index].desc:''}</div>
                   </td> 
                   <td className = 'table_handle'>
                       <a href = 'javascript:;' className = 'table_handle_a' onClick={() => {dispatch(actions.fixedEditShow(v.id))}}>编辑</a>
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