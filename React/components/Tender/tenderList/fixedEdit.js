import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Tender/tenderList';
 /* *
   * 添加数据
   * */
class fixedEdit extends React.Component{
	inputChange(ref , dispatch){
		var dom  = this.refs[ref];
		var val  = dom.value;
		var name = dom.name;		

		if(name.substring(0,4) == 'type'){
		    name = 'type';	
		}
	    dispatch(actions.editInputChange(name , val));
	}
	
    render(){  
    	const {value , dispatch } = this.props;   
    	var data     = value.fixedEditData;
        if(data.title === undefined){
	       	 return(
	       	     <div id = 'fixed_edit' className = {value.fixedEditShow?'':'fixed_edit_hide'}>
	       	     </div>
	       	 )
       }
    	var type1 = data.type == 1?true:false; 
    	var type2 = data.type == 2?true:false; 
    	var type3 = data.type == 3?true:false; 
        return(
            <div id = 'fixed_edit' className = {value.fixedEditShow?'':'fixed_edit_hide'}>
        	    <div className = "fixed_edit_wraper">
        	         <h2 className = 'fixed_edit_title'>编辑融资租凭</h2>
        	         <ul className = 'fixed_edit_content'>
        	             <table cellspacing = "0">
        	                  <tr>
        	                      <td className = 'label'>项目命名：</td>
    	                          <td>
    	                              <input type = 'text' name = 'title' ref = 'title' value = {data.title?data.title:''} onChange = {this.inputChange.bind(this , 'title' ,dispatch)}></input>
    	                          </td>
        	                      <td>客户名称:</td>
    	                          <td>
	                                  <input type = 'text' name = 'kh_name' ref = 'kh_name' value = {data.kh_name?data.kh_name:''} onChange = {this.inputChange.bind(this , 'kh_name' ,dispatch)}></input>
	                              </td>
        	                  </tr>
        	                  <tr>
	    	                      <td className = 'label'>项目编号：</td>
		                          <td>
		                              <input type = 'text' name = 'modelnum' ref = 'modelnum' value = {data.modelnum?data.modelnum:''} onChange = {this.inputChange.bind(this , 'modelnum' ,dispatch)}></input>
		                          </td>
	    	                      <td>创建时间:</td>
		                          <td>
	                                  {data.update_time} 
	                              </td>
	                          </tr>
        	                  <tr>
    	                          <td className = 'label'>项目地址：</td>
		                          <td>
		                              <input type = 'text' name = 'area' ref = 'area' value = {data.area?data.area:''} onChange = {this.inputChange.bind(this , 'area' ,dispatch)}></input>
		                          </td>
	    	                      <td>联系人:</td>
		                          <td>
	                                  <input type = 'text' name = 'contact_people' ref = 'contact_people' value = {data.contact_people?data.contact_people:''} onChange = {this.inputChange.bind(this , 'contact_people' ,dispatch)}></input>
	                              </td>
    	                      </tr>
    	                      <tr>
    	                          <td className = 'label'>职位：</td>
		                          <td>
		                              <input type = 'text' name = 'job' ref = 'job' value = {data.job} onChange = {this.inputChange.bind(this , 'job' ,dispatch)}></input>
		                          </td>
	    	                      <td>联系电话:</td>
		                          <td>
	                                  <input type = 'text' name = 'phone' ref = 'phone' value = {data.phone} onChange = {this.inputChange.bind(this , 'phone' ,dispatch)}></input>
	                              </td>
	                          </tr>
	                          <tr>
	    	                      <td className = 'label'>企业产值：</td>
		                          <td>
		                              <input type = 'text' name = 'chanzhi' ref = 'chanzhi' value = {data.chanzhi?data.chanzhi:''} onChange = {this.inputChange.bind(this , 'chanzhi' ,dispatch)}></input>
		                          </td>
	    	                      <td>需求金额:</td>
		                          <td>
	                                  <input type = 'text' name = 'case' ref = 'case' value = {data.case?data.case:''} onChange = {this.inputChange.bind(this , 'case' ,dispatch)}></input>
	                              </td>
                              </tr>
	                          <tr>
	    	                      <td>需求类型:</td>
		                          <td>		              
			                          <input className = 'type_radio' type='radio' ref = 'type_e1' onClick = {this.inputChange.bind(this , 'type_e1' , dispatch)} name='type_e1' value='1' checked = {type1}/>
			                          <p className = 'p_text'>一年分期付款</p>
			                          <input className = 'type_radio' type='radio' ref = 'type_e2' onClick = {this.inputChange.bind(this , 'type_e2' , dispatch)} name='type_e2' value='2' checked = {type2}/>
			                          <p className = 'p_text'>三年分期付款</p>
			                          <input className = 'type_radio' type='radio' ref = 'type_e3' onClick = {this.inputChange.bind(this , 'type_e3' , dispatch)} name='type_e3' value='3' checked = {type3}/>
			                          <p className = 'p_text'>五年分期付款</p>
			                      </td>    
		    	                  <td className = 'label'>需求描述：</td>
		                          <td>
		                              <textarea className = 'textarea_1' name = 'des' ref = 'des' onChange = {this.inputChange.bind(this , 'des' ,dispatch)} value = {data.des?data.des:''}></textarea>
		                          </td>
                              </tr>
        	                  <tr>
	        	                  <td colSpan = '4'>
		        	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.tenderEdit())}}>确认</a>
		        	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.fixedEditHide())}}>取消</a>
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
export default connect(mapStateToProps)(fixedEdit);