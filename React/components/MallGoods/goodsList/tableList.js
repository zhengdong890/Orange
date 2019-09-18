import React, { Component, PropTypes } from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/MallGoods/goodsList';
var IMG_URL    = '/Public/admin_images/';
var IMG_UPLOAD = '/Public/Uploads/';
var UPDATE_URL = '/index.php/MallGoods/goodsUpdate.html';
var CHECK_URL  = '/index.php/MallGoods/goodsCheck.html';
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
    		var goods_model = value.goods_model;
            return (
                <form action="" method="post">   
                    <TableBody data = {value.goods_data} check_all = {value.check_all} goods_model = {goods_model} dispatch = {dispatch} />
                    <Handdle goods_model = {goods_model} dispatch = {dispatch} />
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
 * 批量处理
 * */
class Handdle extends React.Component{
	/*获取选中的加入推荐*/
	goodsModelSelect(dispatch){
		var dom  = this.refs['handdle_goods_model'];
		var val  = dom.value;
		dispatch(actions.goodsModelSelect(val));		
	}	
	
	goodsStatusSelect(dispatch){
		var dom  = this.refs['handdle_goods_status'];
		var val  = dom.value;
		dispatch(actions.goodsStatusSelect(val));		
	}	
	
	render(){	
		var dispatch    = this.props.dispatch,
		    goods_model = this.props.goods_model,
		    select_model = [];
		goods_model.map(function(v , key){
			select_model.push(<option value={v.id}>{v.name}</option>);
		});
		var check = typeof(check_all) == 'undefined' || check_all == false ? false : true;
        return(
            <div className='handdle'>
                <p className='handdle_p'>选中项</p>
                <div className='handdle_select'>
                    <select defaultValue = '0' ref = 'handdle_goods_model' onChange = {this.goodsModelSelect.bind(this , dispatch)}>
                        <option value='0'>加入推荐批量下架...</option>
                        {select_model}
                    </select>
                </div>
                <a className='btn_a' href = 'javascript:;' onClick = {() => {dispatch(actions.allGoodsModelFalse())}}>确认</a>
                <div className='handdle_select'>
	                <select defaultValue = '0' ref = 'handdle_goods_status' onChange = {this.goodsStatusSelect.bind(this , dispatch)}>
	                    <option value='0'>批量下架</option>
	                    <option value='1'>批量上架</option>
	                </select>
                </div>
                <a className='btn_a' href = 'javascript:;' onClick = {() => {dispatch(actions.allGoodsStateChange())}}>确认</a>
            </div>
        )
	}	
}

/* *
 * 头部
 * */
class TableHeader extends React.Component{
	checkStatusChange(dispatch){
		var dom  = this.refs['check_all'];
		var val  = dom.checked;
		dispatch(actions.setCheckAllStatus(val));
	}
	
	render(){	
		var dispatch    = this.props.dispatch,
		    goods_model = this.props.goods_model,
		    check_all   = this.props.check_all;
		var model = [];
		goods_model.map(function(v , key){
			model.push(<td className='table_td'>{v.name}</td>);
		});
		var check = typeof(check_all) == 'undefined' || check_all == false ? false : true;
        return(
            <tr>
	            <td className='td'>                                   
                    <div className='select_checkbox'>
                         <input type='checkbox' className='checkbox' ref='check_all' onClick = {this.checkStatusChange.bind(this , dispatch)} checked = {check} /><a>id</a>
                    </div> 
	            </td>	            
	            <td className='table_td'>商品名称</td>
	            <td className='table_td'>发布时间</td>
	            <td className='table_td'>图片</td>
	            <td className='table_td'>价格</td>
	            {model}
	            <td className='table_td'>上架</td>
	            <td className='table_td'>
	                <a className = 'table_btn' href = 'javascript:;' onClick = {() => {dispatch(actions.sendSortChange())}}>排序</a>
	            </td>
	            <td className='table_td'>库存</td>
	            <td className='table_td'>审核</td>
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
	
	checkStatusChange(ref , id , dispatch){
		var dom  = this.refs[ref + id];
		var val  = dom.checked;
		dispatch(actions.setCheckStatus(id , val));
	}
	
	render(){
	    var data          = this.props.data,
	        check_all     = this.props.check_all,
	        goods_model   = this.props.goods_model,
	        dispatch      = this.props.dispatch;
	    var table_list = [],
	        state      = '';
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
        	var check = typeof(v.check) == 'undefined' || v.check == false ? false : true;
            table_list.push( 
                <tr key = {index}>
                   <td className='td'>                                   
                      <div className='select_checkbox'>
                            <input type='checkbox' className='checkbox' ref = {'check' + v.id} onClick = {_this.checkStatusChange.bind(_this , 'check' , v.id , dispatch)} checked = {check}/><a>{v.id}</a>
                      </div> 
                   </td>
                   <td className='table_td'>
                       <div className='table_td_text'>{v.goods_name}</div>
                   </td>
                   <td className='table_td'>{v.create_time}</td>
                   <td className='table_td'>
                       <a href="" target="_Blank">
                       <img src={v.goods_thumb.substring(0,1)=='.'?v.goods_thumb.substring(1):v.goods_thumb}></img>
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
                   <td className='table_td'>
                        <a href={CHECK_URL+'?id='+v.id}>审核</a>
                    </td>
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
                <TableHeader goods_model = {goods_model} check_all = {check_all} dispatch = {dispatch} />
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