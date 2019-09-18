import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/TenderSelect/selectUpdate';

 /* *
   * 
   * */
class blockEdit extends React.Component{
	inputChange(ref , dispatch){
		var dom  = this.refs[ref];
		var val  = dom.value;
		var name = dom.name;		
		if(name.substring(0,6) == 'status'){
		    name = 'status';	
		}
	    dispatch(actions.editInputChange(name , val));
	}
	
    render(){  
    	const {value , dispatch } = this.props;
    	var data = value.selectData;
    	var status = data.status == 1?true:false; 
    	
        return(		
	        <div className = 'block_edit'>		                    	   
	            <table>
	                <tr>
		                <td className = 'td_label'>中标标题:</td>
		                <td>
		                    <input type = 'text' name = 'title' ref = 'title' value = {data.title?data.title:''} onChange = {this.inputChange.bind(this , 'title' ,dispatch)}></input>
		                </td>
	                </tr> 
	                <tr>
			            <td className = 'td_label'>中标描述:</td>
			            <td>
			                <input type = 'text' name = 'desc' ref = 'desc' value = {data.desc?data.desc:''} onChange = {this.inputChange.bind(this , 'desc' ,dispatch)}></input>
			            </td>
		            </tr> 
	                <tr>
			            <td className = 'td_label'>中标区域:</td>
			            <td>
			                <input type = 'text' name = 'area' ref = 'area' value = {data.area?data.area:''} onChange = {this.inputChange.bind(this , 'area' ,dispatch)}></input>
			            </td>
		            </tr> 
                    <tr>
                        <td className = 'td_label'>是否显示：</td>
                        <td>
                            <input className = 'type_radio' type='radio' ref = 'status_a1' onClick = {this.inputChange.bind(this , 'status_a1' , dispatch)} name='status_a1' value='1' checked = {status}/>
	                        <p className = 'p_text'>是</p>
	                        <input className = 'type_radio' type='radio' ref = 'status_a2' onClick = {this.inputChange.bind(this , 'status_a2' , dispatch)} name='status_a2' value='0' checked = {!status}/>
	                        <p className = 'p_text'>否</p>
                        </td>
                    </tr>
	                <tr>
	                    <td></td>
	                    <td>
    	                   <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.newsEdit())}}>确认</a>   	                  
	                    </td>
                  </tr>
	            </table>
	        </div>	   
        )
    }
}

function mapStateToProps(state) {
   return {
      value: state    
   }
}
export default connect(mapStateToProps)(blockEdit);