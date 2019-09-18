import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/News/newsAdd';

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
		if(name.substring(0,3) == 'seo'){
		    name = 'seo_news';	
		}
	    dispatch(actions.editInputChange(name , val));
	}
	
	editorInit(dispatch){
	    var ue = UE.getEditor('myEditor', {
	        theme:"default", //皮肤
	        lang:"zh-cn", //语言
	        initialFrameWidth:1000,  //初始化编辑器宽度,默认800
	        initialFrameHeight:600,
	        allHtmlEnabled:false,
	    });
	 	this.editorChange(ue , dispatch);
	}
	
	editorChange(ue , dispatch){
	    ue.addListener("blur",function(){
		    var content = ue.getContent();
		    dispatch(actions.editInputChange('content' , content));
		})
	}
	
    render(){  
    	const {value , dispatch } = this.props;
    	var data = value.newsData;
    	var status = data.status == 1?true:false;
    	var seo_news = data.seo_news == 1?true:false; 
    	this.editorInit(dispatch);
        return(		
	        <div className = 'block_edit'>		                    	   
	            <table>
	                <tr>
		                <td className = 'td_label'>文章标题:</td>
		                <td>
		                    <input type = 'text' name = 'title' ref = 'title' value = {data.title?data.title:''} onChange = {this.inputChange.bind(this , 'title' ,dispatch)}></input>
		                </td>
	                </tr> 
	                <tr>
			            <td className = 'td_label'>关键字:</td>
			            <td>
			                <input type = 'text' name = 'keyword' ref = 'keyword' value = {data.keyword?data.keyword:''} onChange = {this.inputChange.bind(this , 'keyword' ,dispatch)}></input>
			            </td>
		            </tr> 
	                <tr>
			            <td className = 'td_label'>描述:</td>
			            <td>
			                <input type = 'text' name = 'description' ref = 'description' value = {data.description?data.description:''} onChange = {this.inputChange.bind(this , 'description' ,dispatch)}></input>
			            </td>
		            </tr> 
	                <tr>
		                <td className = 'td_label'>内容:</td>
		                <td>
		                    <textarea id = 'myEditor' className = 'textarea_2' name = 'content' ref = 'content' value = {data.content?data.content:''}></textarea>
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
                        <td className = 'td_label'>是否seo的新闻：</td>
                        <td>
                            <input className = 'type_radio' type='radio' ref = 'seo_news_a1' onClick = {this.inputChange.bind(this , 'seo_news_a1' , dispatch)} name='seo_news_a1' value='1' checked = {seo_news}/>
	                        <p className = 'p_text'>是</p>
	                        <input className = 'type_radio' type='radio' ref = 'seo_news_a2' onClick = {this.inputChange.bind(this , 'seo_news_a2' , dispatch)} name='seo_news_a2' value='0' checked = {!seo_news}/>
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