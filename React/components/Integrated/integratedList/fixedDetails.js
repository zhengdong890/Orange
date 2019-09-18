import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Integrated/integratedList';
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
    	var is_use   = data.is_use == 1?true:false; 
    	var jinrong1 = data.jinrong == 1?true:false; 
    	var jinrong2 = data.jinrong == 2?true:false; 
    	var jinrong3 = data.jinrong == 3?true:false; 
    	var type0 = data.type[0]?1:0; 
    	var type1 = data.type[1]?1:0; 
    	var type2 = data.type[2]?1:0; 
    	var type3 = data.type[3]?1:0;  
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
		                          <td>{data.update_time} </td>
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
			                      <td className = 'label'>项目时间：</td>
			                      <td>{data.area}</td>
			                      <td>机器使用经验:</td>
			                      <td>
			                          <input className = 'type_radio' type='radio' checked = {is_use}/>
			                          <p className = 'p_text'>是</p>
			                          <input className = 'type_radio' type='radio' checked = {!is_use}/>
			                          <p className = 'p_text'>否</p>
			                      </td>
	                          </tr>	
			   	              <tr>
			                      <td className = 'label'>企业产值：</td>
			                      <td>{data.chanzhi}</td>
			                      <td>计划投入预算:</td>
			                      <td>{data.yusuan}</td>
                              </tr>
	                          <tr>
	    	                      <td className = 'label'>项目类型：</td>
		                          <td>
		                              <input className = 'type_radio' type='checkbox' value='1' checked = {type0}/>
		                              <p className = 'p_text'>弧焊</p>
		                              <input className = 'type_radio' type='checkbox' value='1' checked = {type1}/>
		                              <p className = 'p_text'>切割分离</p>
		                              <input className = 'type_radio' type='checkbox' value='1' checked = {type2}/>
		                              <p className = 'p_text'>上下料</p>
		                              <input className = 'type_radio' type='checkbox' value='1' checked = {type3}/>
		                              <p className = 'p_text'>搬运</p>
		                          </td>
			                      <td className = 'label'>指定品牌：</td>
			                      <td>{data.brand_name}</td>
                              </tr>
	                          <tr>
    	                      <td className = 'label'>产品描述：</td>
		                          <td>
		                              <div dangerouslySetInnerHTML={{__html: data.desc}}></div>
		                          </td>
	    	                      <td>金融服务:</td>
		                          <td>
			                          <input className = 'type_radio' type='radio' checked = {jinrong1}/>
			                          <p className = 'p_text'>分期付款</p>
			                          <input className = 'type_radio' type='radio' checked = {jinrong2}/>
			                          <p className = 'p_text'>融资租赁</p>
			                          <input className = 'type_radio' type='radio' checked = {jinrong3}/>
			                          <p className = 'p_text'>直接购买</p>
	                              </td>
                              </tr>                          			   	    
 			   	             <tr>
			                      <td>项目描述:</td>
			                      <td colSpan = '3'>
			                          <div className = 'content_text' dangerouslySetInnerHTML={{__html: data.content}}></div>
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