import React, { Component, PropTypes } from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Businesses/sellerList';
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
	            <td className='table_td'>店铺名称</td>
	            <td className='table_td'>商品数量</td>
	            <td className='table_td'>创建时间</td>
              <td className='table_td'>是否签约</td>
	            <td className='table_td'>店铺当前状态</td>
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
	    var table_list = [],
	        state      = '';
		var _this = this;
		data.map(function(v , index){  
            table_list.push( 
                <tr key = {index}>
                   <td className='td'>                                   
                      <div className='select_checkbox'>
                           <a>{v.id}</a>
                      </div> 
                   </td>
                   <td className='table_td'>{v.shop_name}</td>                          
                   <td className='table_td'>{v.goods_number}</td>
                   <td className='table_td'>{v.time}</td>
                   <td className='table_td'>{v.status=='1'?'营业中':'已关闭'}</td>
                   <td className='table_td'>{v.is_sign=='1'?'已签约':'暂未签约'}</td>
                   <td className='table_handle'>
                        <a className = 'table_handle_a' href={'/index.php/MallGoods/goodsList.html'+'?seller_id='+v.member_id}>查看商家商品</a> |
                        <a className = 'table_handle_a' href='javascript:;' onClick={() => {dispatch(actions.changeShopStatus(v.id , v.status=='1'?'0':'1'))}}> {v.status=='1'?'关闭':'开启'}</a> |
                        <a className = 'table_handle_a' href={'/index.php/ShopData/shopUpdate.html'+'?seller_id='+v.member_id}>店铺编辑</a> 
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