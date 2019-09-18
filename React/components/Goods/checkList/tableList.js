import React, { Component, PropTypes } from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Goods/checkList';
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
	            <td className='table_td'>商品名称</td>
	            <td className='table_td'>发布时间</td>
	            <td className='table_td'>价格</td>
	            <td className='table_td'>库存</td>
	            <td className='table_td'>审核</td>
	            <td className='table_td'>操作</td>           
            </tr> 
        )
	}	
}

/*首页*/
class TableBody extends React.Component{
	render(){
	    var data_      = this.props.data,
	        dispatch   = this.props.dispatch;
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
                   <td className = 'table_td'>
                       <div className = 'table_td_text'>{v.goods_name}</div>
                   </td>
                   <td className = 'table_td'>{v.create_time}</td> 
                   <td className = 'table_td'>{v.goods_price}</td> 
                   <td className = 'table_td'>{v.goods_number}</td>
                   <td className = 'table_td'> 
	                   <img src = {v.is_check==1?IMG_URL+'yes.gif':IMG_URL+'no.gif'} ref={'img'+v.id}></img>
                   </td>
                   <td className = 'table_handle'>
                       <a href = 'javascript:;' onClick={() => dispatch(actions.goodsEditShow(v.id))}>审核</a>
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