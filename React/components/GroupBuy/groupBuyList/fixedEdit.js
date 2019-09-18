import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/GroupBuy/groupBuyList';
 /* *
   * 团购编辑框
   * */
class fixedEdit extends React.Component{
	inputChange(ref , dispatch){
		var dom  = this.refs[ref];
		var val  = dom.value;
		var name = dom.name;
		if(ref == 'check_status1' || ref == 'check_status2'){
			name = 'check_status';
		}
		dispatch(actions.checkInputChange(name , val));
	}
	
	imgChange(ref , dispatch){
		var dom  = this.refs[ref];
        var file   = dom.files[0];   
		var reader = new FileReader();  
		var _this  = this 
		//将文件以Data URL形式读入页面  
		reader.readAsDataURL(file);  
		reader.onload=function(e){ 
		   	//显示文件  
			dispatch(actions.imgChange(ref , this.result));
		}  
	}
	
    render(){  
    	const {value , dispatch } = this.props;   
    	var data = value.fixedEditData;    	
    	var check_status  = data.check_status   == 1?true:false; 
    	if(data.id === undefined){
    		return(
    	        <div id = 'fixed_edit' className = {value.fixedEditShow?'':'fixed_edit_hide'}></div>
    	    );
    	}
    	var ad_1_html = '';//首页
    	var ad_2_html = '';//推荐位
    	var ad_3_html = '';
    	if(data.ad_1 == 1){
    		var img_1 = [];
        	if(!data.img_1){
        		img_1.push(<img></img>);    		
        	}else
        	if(data.img_1.substring(0,4) == 'data'){
        	    img_1.push(<img src = {data.img_1}></img>);
        	}else{
        		img_1.push(<img src = {data.img_1.substring(1)}></img>);
        	}    
        	ad_1_html = 
        	<span>
		        <td className = 'label'>首页图片：</td>
	            <td>
		            <div className = 'addimg'>
		                {img_1}
		                <a className = "btn_addimg">
		                点击修改
		                    <input type="file" name = "img_1" ref = 'img_1' onChange = {this.imgChange.bind(this , 'img_1' ,dispatch)}></input>
		                </a>
		            </div>			                          
	            </td>
			</span>;
    	}
    	
    	if(data.ad_1 == 1 || data.ad_1 == 2){
    		var img_2 = [];
        	if(!data.img_2){
        		img_2.push(<img></img>);    		
        	}else
        	if(data.img_2.substring(0,4) == 'data'){
        	    img_2.push(<img src = {data.img_2}></img>);
        	}else{
        		img_2.push(<img src = {data.img_2.substring(1)}></img>);
        	}    
        	ad_2_html = 
        	<span>
		        <td className = 'label'>推荐位图片：</td>
	            <td>
		            <div className = 'addimg'>
		                {img_2}
		                <a className = "btn_addimg">
		                点击修改
		                    <input type="file" name = "img_2" ref = 'img_2' onChange = {this.imgChange.bind(this , 'img_2' ,dispatch)}></input>
		                </a>
		            </div>			                          
	            </td>
			</span>;
    	}else{
    		var img = [];
        	if(!data.img){
        		img.push(<img></img>);    		
        	}else
        	if(data.img.substring(0,4) == 'data'){
        	    img.push(<img src = {data.img}></img>);
        	}else{
        		img.push(<img src = {data.img.substring(1)}></img>);
        	}    
        	ad_3_html = 
        	<span>
		        <td className = 'label'>推荐位图片：</td>
	            <td>
		            <div className = 'addimg'>
		                {img}
		                <a className = "btn_addimg">
		                点击修改
		                    <input type="file" name = "img" ref = 'img' onChange = {this.imgChange.bind(this , 'img' ,dispatch)}></input>
		                </a>
		            </div>			                          
	            </td>
			</span>;    		
    	}
    	
        return(
            <div id = 'fixed_edit' className = {value.fixedEditShow?'':'fixed_edit_hide'}>
        	    <div className = "fixed_edit_wraper">
        	         <h2 className = 'fixed_edit_title'>团购商品编辑</h2>
        	         <ul className = 'fixed_edit_content'>
        	             <table cellspacing = "0">
			   	              <tr>
			                      {ad_1_html}
			                      {ad_2_html}
			                      {ad_3_html}
		                      </tr>	
			                  <tr>
				                  <td colSpan = '4'>
			    	                  <a className = 'btn' href = 'javascript:;' onClick = {() => {dispatch(actions.groupBuyEdit())}}>确认</a>
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