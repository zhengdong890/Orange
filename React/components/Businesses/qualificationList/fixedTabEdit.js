import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Businesses/qualificationList';

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
                        <TabContent_1 data = {value.check_data} dispatch = {dispatch}/>
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
		const {data , dispatch} = this.props;
		if(data === undefined){
		   return (			
			  <div></div>
		   )			
		}else{
		   var code_html         = '',
		       registration_html = '';
		   if(data.bus_lice_type == 2){
			   code_html = 
				   <tr>
	                  <td className = 'tab_content_label'>组织机构代码证:</td>
	                  <td><img className = 'td_img' src= {data.code?data.code.substring(1):''} /></td>
	               </tr>			   
		   }
		   if(data.bus_lice_type == 2){
			   registration_html = 
				   <tr>
	                  <td className = 'tab_content_label'>税务登记证:</td>
	                  <td><img className = 'td_img' src= {data.registration?data.registration.substring(1):''} /></td>
	               </tr>			   
		   }
		   var status = data.status == 1 ? true : false;
		   return (			
		            <div>		                    	   
		            <table>
		                <tr>
			                <td className = 'tab_content_label'>企业名称:</td>
			                <td>{data.name}</td>
		                </tr> 
		                <tr>
				            <td className = 'tab_content_label'>企业地址:</td>
				            <td>{data.address}</td>
			            </tr>
			            <tr>
				            <td className = 'tab_content_label'>联系方式:</td>
				            <td>{data.contact}</td>
		                </tr>
		                <tr>
			                <td className = 'tab_content_label'>公司指定联系人:</td>
			                <td>{data.place_contact}</td>
			            </tr> 
		                <tr>
		                    <td className = 'tab_content_label'>指定联系人号码:</td>
		                    <td>{data.contact_phone}</td>
		                </tr> 
		                <tr>
		                    <td className = 'tab_content_label'>营业执照:</td>
		                    <td>
		                        <img className = 'td_img' src= {data.bus_lice?data.bus_lice.substring(1):''}/>
		                    </td>
		                </tr> 
			            <tr>
		                    <td className = 'tab_content_label'>开户许可证:</td>
		                    <td>
		                        <img className = 'td_img' src= {data.permit?data.permit.substring(1):''}/>
		                    </td>
		                </tr> 	                	                
		                {code_html}
		                {registration_html}
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