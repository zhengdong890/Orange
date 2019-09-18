import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/MallGoodsSeo/goodsList';
 /* *
   * 共享商品seo编辑框
   * */
class fixedEdit extends React.Component{
	inputChange(ref , dispatch){
		var dom  = this.refs[ref];
		var val  = dom.value;
		var name = dom.name;
		dispatch(actions.editInputChange(name , val));
	}
	
    render(){  
    	const {value , dispatch } = this.props;   
    	var data = value.fixedEditData;    	
    	if(data.goods_id === undefined){
    		return(
    	        <div id = 'fixed_edit' className = {value.fixedEditShow?'':'fixed_edit_hide'}></div>
    	    );
    	}
    	
        return(
            <div id = 'fixed_edit' className = {value.fixedEditShow?'':'fixed_edit_hide'}>
        	    <div className = "fixed_edit_wraper">
        	         <h2 className = 'fixed_edit_title'>商城商品SEO编辑</h2>
        	         <ul className = 'fixed_edit_content'>
        	             <table cellspacing = "0">
			   	              <tr>
			   	                  <td className = 'label'>商品名称：</td>
						          <td>{data.goods_name}</td>
			   	                  <td className = 'label'>标题：</td>
						          <td>
						              <input type = 'text' name = 'title' ref = 'title' value = {data.title?data.title:''} onChange = {this.inputChange.bind(this , 'title' ,dispatch)}></input>		                          
						          </td>
		                      </tr>
			   	              <tr>
			   	                  <td className = 'label'>关键字：</td>
						          <td>
						              <input type = 'text' name = 'keyword' ref = 'keyword' value = {data.keyword?data.keyword:''} onChange = {this.inputChange.bind(this , 'keyword' ,dispatch)}></input>		                         
						          </td>
			   	                  <td className = 'label'>描述：</td>
						          <td>
						              <textarea className = 'textarea_1' name = 'desc' ref = 'desc' onChange = {this.inputChange.bind(this , 'desc' ,dispatch)} value = {data.desc?data.desc:''}></textarea>    
						          </td>
	                          </tr>	
			                  <tr>
				                  <td colSpan = '4'>
			    	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.goodsSeoEdit())}}>确认</a>
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