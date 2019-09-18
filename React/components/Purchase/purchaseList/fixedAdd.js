import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Purchase/purchaseList';
 /* *
   * 添加数据
   * */
class fixedAdd extends React.Component{
	inputChange(ref , dispatch){
		var dom  = this.refs[ref];
		var val  = dom.value;
		var name = dom.name;		
		if(name.substring(0,4) == 'type'){
		    name = 'type';	
		}
	    dispatch(actions.addInputChange(name , val));		
	}
	
    render(){  
    	const {value , dispatch } = this.props;   
    	var data     = value.fixedAddData;
    	var type1 = data.type == 1?true:false; 
    	var type2 = data.type == 2?true:false; 
    	var type3 = data.type == 3?true:false; 
        return(
            <div id = 'fixed_add' className = {value.fixedAddShow?'':'fixed_add_hide'}>
        	    <div className = "fixed_add_wraper">
        	         <h2 className = 'fixed_add_title'>添加设备采购</h2>
        	         <ul className = 'fixed_add_content'>
        	             <table cellspacing = "0">
        	                  <tr>
        	                      <td className = 'label'>项目命名：</td>
    	                          <td>
    	                              <input type = 'text' name = 'title' ref = 'title' value = {data.title} onChange = {this.inputChange.bind(this , 'title' ,dispatch)}></input>
    	                          </td>
        	                      <td>客户名称:</td>
    	                          <td>
	                                  <input type = 'text' name = 'kh_name' ref = 'kh_name' value = {data.kh_name} onChange = {this.inputChange.bind(this , 'kh_name' ,dispatch)}></input>
	                              </td>
        	                  </tr>
        	                  <tr>
    	                          <td className = 'label'>项目地址：</td>
		                          <td>
		                              <input type = 'text' name = 'area' ref = 'area' value = {data.area} onChange = {this.inputChange.bind(this , 'area' ,dispatch)}></input>
		                          </td>
	    	                      <td>联系人:</td>
		                          <td>
	                                  <input type = 'text' name = 'contact_people' ref = 'contact_people' value = {data.contact_people} onChange = {this.inputChange.bind(this , 'contact_people' ,dispatch)}></input>
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
		                              <input type = 'text' name = 'chanzhi' ref = 'chanzhi' value = {data.chanzhi} onChange = {this.inputChange.bind(this , 'chanzhi' ,dispatch)}></input>
		                          </td>
	    	                      <td>需求金额:</td>
		                          <td>
	                                  <input type = 'text' name = 'case' ref = 'case' value = {data.case} onChange = {this.inputChange.bind(this , 'case' ,dispatch)}></input>
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
		        	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.purchaseAdd())}}>确认</a>
		        	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.fixedAddHide())}}>取消</a>
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
export default connect(mapStateToProps)(fixedAdd);