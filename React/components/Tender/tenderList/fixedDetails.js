import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Tender/tenderList';
 /* *
   * 集成项目详情
   * */
class fixedDetails extends React.Component{
    render(){  
    	const {value , dispatch } = this.props;   
    	var data = value.fixedDetailsData;
        if(data.title === undefined){
        	 return(
        	     <div id = 'fixed_edit' className = {value.fixedDetailsShow?'':'fixed_edit_hide'}>
        	     </div>
        	 )
        }
    	var type1 = data.type == 1?true:false; 
    	var type2 = data.type == 2?true:false; 
    	var type3 = data.type == 3?true:false; 
        return(
            <div id = 'fixed_edit' className = {value.fixedDetailsShow?'':'fixed_edit_hide'}>
        	    <div className = "fixed_edit_wraper">
        	         <h2 className = 'fixed_edit_title'>查看详情</h2>
        	         <ul className = 'fixed_edit_content'>
        	             <table cellspacing = "0">
			   	              <tr>
			                      <td className = 'label'>项目命名：</td>
			                      <td>{data.title}</td>
			                      <td>客户名称:</td>
			                      <td>{data.kh_name}</td>
			                  </tr>
			   	              <tr>
			                      <td className = 'label'>项目编号：</td>
			                      <td>{data.modelnum}</td>
			                      <td>创建时间:</td>
			                      <td>{data.update_time}</td>
		                      </tr>
			   	              <tr>
			                      <td className = 'label'>项目地址：</td>
			                      <td>{data.area}</td>
			                      <td>联系人:</td>
			                      <td>{data.contact_people}</td>
		                      </tr>	
			   	              <tr>
			                      <td className = 'label'>职位：</td>
			                      <td>{data.job}</td>
			                      <td>联系电话:</td>
			                      <td>{data.phone}</td>
	                          </tr>	
			   	              <tr>
			                      <td className = 'label'>企业产值：</td>
			                      <td>{data.chanzhi}</td>
			                      <td>需求金额:</td>
			                      <td>{data.case}</td>
                              </tr>
	                          <tr>
	    	                      <td>需求类型:</td>
		                          <td>		              
			                          <input className = 'type_radio' type='radio' checked = {type1}/>
			                          <p className = 'p_text'>一年分期付款</p>
			                          <input className = 'type_radio' type='radio' checked = {type2}/>
			                          <p className = 'p_text'>三年分期付款</p>
			                          <input className = 'type_radio' type='radio' checked = {type3}/>
			                          <p className = 'p_text'>五年分期付款</p>
			                      </td>    
		    	                  <td className = 'label'>需求描述：</td>
		                          <td>
		                          <div className = 'content_text_1'>
		                          {data.des}
		                          </div>
		                          </td>
                              </tr>
			                  <tr>
				                  <td colSpan = '4'>
			    	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.fixedDetailsHide())}}>关闭</a>
				                  </td>
			                  </tr>
        	             </table>           
        	         </ul>         
        	    </div>
        	</div>  
        )
    }
}

function mapStateToProps(state) {
   return {
      value: state
   }
}
export default connect(mapStateToProps)(fixedDetails);