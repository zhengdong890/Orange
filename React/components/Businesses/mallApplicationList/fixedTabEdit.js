import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Businesses/mallApplicationList';

 /* *
   * 
   * */
class fixedTabEdit extends React.Component{
    render(){  
    	const {value , dispatch } = this.props;
        return(
            <div id = 'fixed_tab_wraper' className = {value.check_show?'':'fixed_tab_hide'}>
                <ul className = 'fixed_tab_ul'>                   
                    <div className = 'tab_content'>
                        <TabContent_1 data = {value.check_data} shop_data = {value.shop_data} dispatch = {dispatch}/>
                    </div>
                </ul>
           	</div>     
        )
    }
}

class TabContent_1 extends React.Component{
	fieldValueChange(ref , dispatch){
		var dom  = this.refs[ref];
		var val  = dom.value;
		var name = dom.name;
		dispatch(actions.setCheckData(name , val));
	}
	
	render(){
		const {data , shop_data , dispatch} = this.props;
		if(data === undefined || shop_data === undefined || shop_data['1'] === undefined){
		   return (			
			  <div></div>
		   )			
		}else{
		   var seller_data       = shop_data['1'],
	           shop_data_        = shop_data['0'];
		   var status = data.status == 1 ? true : false;
		   return (			
		            <div>		                    	   
		            <table>
		                <tr>
			                <td className = 'tab_content_label'>企业名称:</td>
			                <td>{seller_data.name}</td>
		                </tr> 
		                <tr>
				            <td className = 'tab_content_label'>企业地址:</td>
				            <td>{seller_data.address}</td>
			            </tr>
			            <tr>
				            <td className = 'tab_content_label'>联系方式:</td>
				            <td>{seller_data.contact}</td>
		                </tr>
		                <tr>
			                <td className = 'tab_content_label'>公司指定联系人:</td>
			                <td>{seller_data.place_contact}</td>
			            </tr> 
		                <tr>
		                    <td className = 'tab_content_label'>指定联系人号码:</td>
		                    <td>{seller_data.contact_phone}</td>
		                </tr> 
	                    <tr>
	                        <td className = 'tab_content_label'>店铺名称:</td>
	                        <td>
	                            <input type='text' name='shop_name' value = {shop_data_.shop_name}>
	                            </input>           
	                        </td>
                        </tr>
                        <tr>
	                        <td className = 'tab_content_label'>上传店铺Logo:</td>
	                        <td>
	                        	<div className = "img_container">
	                        	    <img src={shop_data_.thumb ? shop_data_.thumb.substring(1) : ''}/>
	                                <a className="btn_addimg">
	                                    点击添加
	                                    <input type="file" name="thumb"></input>
	                                </a>                                             
	                            </div> 
	                        </td>
                        </tr>
                        <tr>
	                        <td className = 'tab_content_label'>域名设置:</td>
	                        <td>
		                        <input type='text' name='domain' value = {shop_data_.domain}>
	                            </input>            
	                        </td>
	                    </tr>
                        <tr>
	                        <td className = 'tab_content_label'>域名设置:</td>
	                        <td>
		                        <input type='text' name='desc' value = {shop_data_.desc}>
	                            </input>            
	                        </td>
                        </tr> 
		                <tr>
			                <td className = 'tab_content_label'>审核结果:</td>
			                <td>
			                   <input type='radio' name='status' ref = 'is_check1' value='1' className = 'check' onClick = {this.fieldValueChange.bind(this , 'is_check1' , dispatch)} checked = {status}></input>
			                   <p className = 'check_text'>通过</p>
			                   <input type='radio' name='status' ref = 'is_check2' value='0' className = 'check' onClick = {this.fieldValueChange.bind(this , 'is_check2' , dispatch)} checked = {!status}></input>
			                   <p className = 'check_text'>不通过</p>
			                </td>
			            </tr>  
		                <tr>
		                    <td className = 'tab_content_label' >审核意见:</td>
		                    <td>
		                        <textarea name = 'content' ref = 'content' onChange = {this.fieldValueChange.bind(this , 'content' ,dispatch)} value = {data.content}></textarea>
		                    </td>
		                </tr>
		                <tr>
		                    <td className = 'tab_content_label' >操作</td>
		                    <td>
		                        <a className = 'btn' href='javascript:;' onClick={() => dispatch(actions.checkData())}>确认审核</a>
		                        <a className = 'btn' href='javascript:;' onClick={() => dispatch(actions.checkDataHide())}>取消</a>
		                    </td>
		                </tr>                     
		            </table>
		        </div>
		    )			
		}
	    

	}
}

function mapStateToProps(state) {
   return {
      value: state    
   }
}
export default connect(mapStateToProps)(fixedTabEdit);