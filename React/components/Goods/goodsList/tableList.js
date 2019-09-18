import React, { Component, PropTypes } from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/goods/goodsList';
var IMG_URL    = '/Public/admin_images/';
var IMG_UPLOAD = '/Public/Uploads/';
var UPDATE_URL = '/index.php/goods/goodsUpdate.html';
/* *
 * 表单
 * */
class TableList extends React.Component{
	render(){	
        const {value , dispatch } = this.props;
        if(value.goods_state && !value.goods_model_state){
        	dispatch(actions.getGoodsModel());
        	return (
                <form action="" method="post"></form>
            );
        }else
        if(value.goods_state && value.goods_model_state){
        	var goods_model = [];
    		for(var k in value.goods_model){
    			goods_model.push(value.goods_model[k]);
    		}
            return (
                <form action="" method="post">   
                    <TableBody data = {value.goods_data} goods_model = {goods_model} dispatch = {dispatch} />
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
		var dispatch    = this.props.dispatch,
		    goods_model = this.props.goods_model;
		var model = [];
		goods_model.map(function(v , key){
			model.push(<td className='table_td'>{v.name}</td>);
		});
        return(
            <tr>
	            <td className='td'>                                   
                    <div className='select_checkbox'>
                         <input type='checkbox' className='checkbox' /><a>id</a>
                    </div> 
	            </td>	            
	            <td className='table_td'>商品名称</td>
	            <td className='table_td'>添加时间</td>
	            <td className='table_td'>图片</td>
	            <td className='table_td'>价格</td>
	            {model}
	            <td className='table_td'>上架</td>
	            <td className='table_td'>
	                <a className = 'table_btn' href = 'javascript:;' onClick = {() => {dispatch(actions.sendSortChange())}}>排序</a>
	            </td>
	            <td className='table_td'>库存</td>
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
	        goods_model   = this.props.goods_model,
	        dispatch      = this.props.dispatch;
	    var table_list = [],
	        state      = '';
		var data = [];
		for(var k in data_){
			data.push(data_[k]);
		}
		var _this = this;
		data.map(function(v , index){  
        	var model = [];	
        	goods_model.map(function(v1 , index1){
                model.push(	
                    <td className='table_td'>
                        <a href='javascript:;' onClick={() => {dispatch(actions.modelsStateChange(v.id , v1.id))}}>
                            <img src={$.inArray(v.id,v1.goods_ids)!=-1?IMG_URL+'yes.gif':IMG_URL+'no.gif' }></img>
                        </a>
                    </td>
                );
	        });
            table_list.push( 
                <tr key = {index}>
                   <td className='td'>                                   
                      <div className='select_checkbox'>
                            <input type='checkbox' className='checkbox' /><a>{v.id}</a>
                      </div> 
                   </td>
                   <td className='table_td'>
                       <div className = 'table_td_text'>{v.goods_name}</div>
                   </td>
                   <td className='table_td'>{v.create_time}</td>
                   <td className='table_td'>
                       <a href="" target="_Blank">
                       <img src={v.goods_thumb.substring(1)}></img>
                       </a>
                   </td>
                   <td className='table_td'>{v.goods_price}</td>
                   {model}
                   <td className='table_td'>
                       <a href='javascript:;' onClick={() => {dispatch(actions.goodsStateChange(v.id , v.status))}}>
                           <img src={v.status==1?IMG_URL+'yes.gif':IMG_URL+'no.gif'} ref={'img'+v.id}></img>
                       </a>
                   </td>                   
                   <td className='table_td'>
                       <input type = 'text' ref = {'sort' + v.id} name = {'sort' + v.id} defaultValue = {v.sort} onBlur = {_this.sortChange.bind(_this , v.id , dispatch)}></input>
                   </td>
                   <td className='table_td'>{v.goods_number}</td>
                   <td className='table_handle'>
                       <div>
                           <a href={UPDATE_URL+'?id='+v.id}>
                               <img src={IMG_URL+'icon_edit.gif'}></img>
                           </a>
                           <a href='javascript:;' onClick={() => {if(confirm('确认删除?')){dispatch(actions.deleteData(v.id))}}}>
                               <img src={IMG_URL+'icon_trash.gif'}></img>
                           </a>
                       </div>
                   </td>
                </tr> 
            )
		});
        return(
            <table cellSpacing='0' className="tableList"> 
                <tbody>
                <TableHeader goods_model = {goods_model} dispatch = {dispatch} />
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