import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Integrated/integratedList';
 /* *
   * 添加数据
   * */
class fixedAdd extends React.Component{
	inputChange(ref , dispatch){
		var dom  = this.refs[ref];
		var val  = dom.value;
		var name = dom.name;		
		if(name.substring(0,6) == 'is_use'){
		    name = 'is_use';	
		}
		if(name.substring(0,7) == 'jinrong'){
		    name = 'jinrong';	
		}
		if(name.substring(0,4) == 'type'){
		    var num = name.substring(6,7);
		    dispatch(actions.addTypeChange(num));
		}else{
			dispatch(actions.addInputChange(name , val));
		}
	}
	
    render(){  
    	const {value , dispatch } = this.props;   
    	var data     = value.fixedAddData;
    	var is_use   = data.is_use == 1?true:false; 
    	var jinrong1 = data.jinrong == 1?true:false; 
    	var jinrong2 = data.jinrong == 2?true:false; 
    	var jinrong3 = data.jinrong == 3?true:false; 
        return(
            <div id = 'fixed_add' className = {value.fixedAddShow?'':'fixed_add_hide'}>
        	    <div className = "fixed_add_wraper">
        	         <h2 className = 'fixed_add_title'>添加集成项目</h2>
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
	    	                      <td className = 'label'>项目时间：</td>
		                          <td>
		                              <input type = 'text' name = 'project_time' ref = 'project_time' value = {data.project_time} onChange = {this.inputChange.bind(this , 'project_time' ,dispatch)}></input>
		                          </td>
	    	                      <td>机器使用经验:</td>
		                          <td>
			                          <input className = 'type_radio' type='radio' ref = 'is_use_a1' onClick = {this.inputChange.bind(this , 'is_use_a1' , dispatch)} name='is_use_a1' value='1' checked = {is_use}/>
			                          <p className = 'p_text'>是</p>
			                          <input className = 'type_radio' type='radio' ref = 'is_use_a2' onClick = {this.inputChange.bind(this , 'is_use_a2' , dispatch)} name='is_use_a2' value='0' checked = {!is_use}/>
			                          <p className = 'p_text'>否</p>
	                              </td>
                              </tr>
	                          <tr>
	    	                      <td className = 'label'>企业产值：</td>
		                          <td>
		                              <input type = 'text' name = 'chanzhi' ref = 'chanzhi' value = {data.chanzhi} onChange = {this.inputChange.bind(this , 'chanzhi' ,dispatch)}></input>
		                          </td>
	    	                      <td>计划投入预算:</td>
		                          <td>
	                                  <input type = 'text' name = 'yusuan' ref = 'yusuan' value = {data.yusuan} onChange = {this.inputChange.bind(this , 'yusuan' ,dispatch)}></input>
	                              </td>
                              </tr>
	                          <tr>
	    	                      <td className = 'label'>项目类型：</td>
		                          <td>
		                              <input className = 'type_radio' type='checkbox' ref = 'type_a0' onClick = {this.inputChange.bind(this , 'type_a0' , dispatch)} name='type_a0' value='1'/>
		                              <p className = 'p_text'>弧焊</p>
		                              <input className = 'type_radio' type='checkbox' ref = 'type_a1' onClick = {this.inputChange.bind(this , 'type_a1' , dispatch)} name='type_a1' value='1'/>
		                              <p className = 'p_text'>切割分离</p>
		                              <input className = 'type_radio' type='checkbox' ref = 'type_a2' onClick = {this.inputChange.bind(this , 'type_a2' , dispatch)} name='type_a2' value='1'/>
		                              <p className = 'p_text'>上下料</p>
		                              <input className = 'type_radio' type='checkbox' ref = 'type_a3' onClick = {this.inputChange.bind(this , 'type_a3' , dispatch)} name='type_a3' value='1'/>
		                              <p className = 'p_text'>搬运</p>
		                          </td>
	    	                      <td>指定品牌:</td>
		                          <td>
	                                  <input type = 'text' name = 'brand_name' ref = 'brand_name' value = {data.brand_name} onChange = {this.inputChange.bind(this , 'brand_name' ,dispatch)}></input>
	                              </td>
                              </tr>
	                          <tr>
	    	                      <td className = 'label'>产品描述：</td>
		                          <td>
		                              <textarea className = 'textarea_1' name = 'desc' ref = 'desc' onChange = {this.inputChange.bind(this , 'desc' ,dispatch)} value = {data.desc}></textarea>
		                          </td>
	    	                      <td>金融服务:</td>
		                          <td>
			                          <input className = 'type_radio' type='radio' ref = 'jinrong_a1' onClick = {this.inputChange.bind(this , 'jinrong_a1' , dispatch)} name='jinrong_a1' value='1' checked = {jinrong1}/>
			                          <p className = 'p_text'>分期付款</p>
			                          <input className = 'type_radio' type='radio' ref = 'jinrong_a2' onClick = {this.inputChange.bind(this , 'jinrong_a2' , dispatch)} name='jinrong_a2' value='2' checked = {jinrong2}/>
			                          <p className = 'p_text'>融资租赁</p>
			                          <input className = 'type_radio' type='radio' ref = 'jinrong_a3' onClick = {this.inputChange.bind(this , 'jinrong_a3' , dispatch)} name='jinrong_a3' value='3' checked = {jinrong3}/>
			                          <p className = 'p_text'>直接购买</p>
	                              </td>
                              </tr>
	                          <tr>
	    	                      <td className = 'label'>项目描述：</td>
		                          <td colSpan = '3'>
		                              <textarea className = 'textarea_2' name = 'content' ref = 'content' onChange = {this.inputChange.bind(this , 'content' ,dispatch)} value = {data.content}></textarea>
		                          </td>
                              </tr>
        	                  <tr>
	        	                  <td colSpan = '4'>
		        	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.integratedAdd())}}>确认</a>
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