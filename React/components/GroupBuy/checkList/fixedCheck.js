import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/GroupBuy/checkList';
 /* *
   * 联动分类树
   * */
class fixedCheck extends React.Component{
	inputChange(ref , dispatch){
		var dom  = this.refs[ref];
		var val  = dom.value;
		var name = dom.name;
		if(ref == 'check_status1' || ref == 'check_status2'){
			name = 'check_status';
		}
		dispatch(actions.checkInputChange(name , val));
	}
	
    render(){  
    	const {value , dispatch } = this.props;   
    	var data = value.fixedCheckData;    	
    	var check_status  = data.check_status   == 1?true:false; 
    	if(data.id === undefined){
    		return(
    	        <div id = 'fixed_edit' className = {value.fixedCheckShow?'':'fixed_edit_hide'}></div>
    	    );
    	}
    	var img   = [];
    	var img_1 = [];
    	var img_2 = [];
    	if(data.ad_1 == 1){
            img_1.push(<img src = {data.img_1.substring(1)}></img>); 
            img_2.push(<img src = {data.img_2.substring(1)}></img>); 
    	}else
    	if(data.ad_1 == 2){
            img_2.push(<img src = {data.img_2.substring(1)}></img>);   
    	}else{
    		img.push(<img src = {data.img.substring(1)}></img>);
    	}  

        return(
            <div id = 'fixed_edit' className = {value.fixedCheckShow?'':'fixed_edit_hide'}>
        	    <div className = "fixed_edit_wraper">
        	         <h2 className = 'fixed_edit_title'>团购商品审核</h2>
        	         <ul className = 'fixed_edit_content'>
        	             <table cellspacing = "0">
			   	              <tr>
			                      <td className = 'label'>商品名称：</td>
			                      <td>{data.goods_name}</td>
			                      <td className = 'label'>商品价格:</td>
			                      <td>{data.goods_price}</td>
			                  </tr>
			   	              <tr>
		                          <td className = 'label'>商品标题：</td>
		                          <td>{data.title}</td>
			                      <td className = 'label'>申请位置:</td>
			                      <td>{data.ad_1 == 1 ? '首页' : data.ad_1 == 2 ? '推荐位' : ''}</td>
		                      </tr>
			   	              <tr>
			                      <td className = 'label'>团购价格：</td>
			                      <td>{data.group_price}</td>
			                      <td className = 'label'>申请时间:</td>
			                      <td>{data.create_time}</td>
		                      </tr>
			   	              <tr>
			                      <td className = 'label'>团购开始时间：</td>
			                      <td>{data.start_time}</td>
			                      <td className = 'label'>持续时间:</td>
			                      <td>{data.time}</td>
	                          </tr>
			   	              <tr>
		                          <td className = 'label'>首页图片：</td>
		                          <td colSpan = '3'>{img_1}</td>
	                          </tr>
			   	              <tr>
		                          <td className = 'label'>推荐位图片：</td>
		                          <td colSpan = '3'>{img_2}</td>
	                          </tr>
			   	              <tr>
			                      <td className = 'label'>团购图片：</td>
			                      <td colSpan = '3'>{img}</td>
                              </tr>
			                  <tr>
			                      <td className = 'label'>审核结果：</td>
			                      <td>
			                          <input className = 'type_radio' type='radio' ref = 'check_status1' onClick = {this.inputChange.bind(this , 'check_status1' , dispatch)} name='check_status1' value='1' checked = {check_status}/>
			                          <p className = 'p_text'>通过</p>
			                          <input className = 'type_radio' type='radio' ref = 'check_status2' onClick = {this.inputChange.bind(this , 'check_status2' , dispatch)} name='check_status2' value='0' checked = {!check_status}/>
			                          <p className = 'p_text'>不通过</p>
			                      </td>
			                      <td className = 'label'>审核意见：</td>
			                      <td>
			                          <input type = 'text' name = 'check_content' ref = 'check_content' value = {data.check_content} onChange = {this.inputChange.bind(this , 'check_content' ,dispatch)}></input>
			                      </td>
			                  </tr>
			                  <tr>
				                  <td colSpan = '4'>
			    	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.groupBuyCheck())}}>确认</a>
			    	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.fixedCheckHide())}}>取消</a>
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
export default connect(mapStateToProps)(fixedCheck);