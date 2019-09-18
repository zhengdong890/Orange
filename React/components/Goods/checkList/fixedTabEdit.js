import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Goods/checkList';

 /* *
   * 
   * */
class fixedTabEdit extends React.Component{
    render(){  
    	const {value , dispatch } = this.props; 
        return(
            <div id = 'fixed_tab_wraper' className = {value.goods_edit_state?'':'fixed_tab_hide'}>
                <ul className = 'fixed_tab_ul'>
                    <TabHeader dispatch = {dispatch} now_tab = {value.now_tab} />                    
                    <div className = 'tab_content'>
                        <TabContent_1 brand_name = {value.brand_name} data = {value.edit_data} now_tab = {value.now_tab} goods_data = {value.goods_data}/>
                        <TabContent_2 rent = {value.goods_rent} data = {value.edit_data} now_tab = {value.now_tab}/>
                        <TabContent_3 goods_gallery = {value.goods_gallery} now_tab = {value.now_tab}/>
                        <TabContent_4 goods_data = {value.goods_data} now_tab = {value.now_tab}/>
                        <TabContent_5 goods_check_data = {value.goods_check_data} data = {value.edit_data} now_tab = {value.now_tab} dispatch = {dispatch}/>
                    </div>
                </ul>
           	</div>     
        )
    }
}

class TabHeader extends React.Component{
	render(){
		const {dispatch , now_tab} = this.props; 
	    return (
	        <div className = "tab_header">
		        <a href = "javascript:;" className = {now_tab == 1 ? 'tab_active' : ''} onClick = {() => {dispatch(actions.changeTab(1))}}>商品信息</a>		       
	        </div>
        )
	}
}


class TabContent_1 extends React.Component{
	render(){
		const {brand_name , data , now_tab , goods_data} = this.props;
	    return (
            <div className = {now_tab == 1 ? '' : 'tab_content_hide'}>		                    	   
                <table>                            
                    <tr>
                        <td className = 'tab_content_label'>商品名称:</td>
                        <td>{data.goods_name}</td>
                    </tr> 
                    <tr>
                        <td className = 'tab_content_label'>商品分类:</td>
                        <td>{goods_data.cat_name}</td>
                    </tr> 
                    <tr>
                        <td className = 'tab_content_label'>商品品牌:</td>
                        <td>{brand_name}</td>
                    </tr>                     		            
	                <tr>
			            <td className = 'tab_content_label'>设备型号:</td>
			            <td>{data.goods_model}</td>
		            </tr>
                    <tr>
		                <td className = 'tab_content_label'>购置时间:</td>
		                <td>{data.buy_time}</td>
	                </tr> 
                    <tr>
			            <td className = 'tab_content_label'>设备厂家:</td>
			            <td>{data.sbcj}</td>
		            </tr>
		            <tr>
			            <td className = 'tab_content_label'>运费:</td>
			            <td>{data.yuifei}</td>
	                </tr>
                    <tr>
		                <td className = 'tab_content_label'>设备成色:</td>
		                <td>{data.sbcs}</td>
		            </tr> 
                    <tr>
	                    <td className = 'tab_content_label'>联系方式:</td>
	                    <td>{data.phone}</td>
	                </tr> 
                    <tr>
                        <td className = 'tab_content_label'>商品价格:</td>
                        <td>{data.goods_price}</td>
                    </tr> 		            
		            <tr>
	                    <td className = 'tab_content_label'>押金:</td>
	                    <td>{data.deposit == 1?'购买':'不购买'}</td>
                    </tr> 	                	                
		            <tr>
			            <td className = 'tab_content_label'>保险金额:</td>
			            <td>{data.safest == 1?'月租金X3':(data.safest == 2?'月租金X6':'无')}</td>
                    </tr>
		            <tr>
		                <td className = 'tab_content_label'>商品数量:</td>
		                <td>{data.goods_number}</td>
                    </tr>
                    <tr>
                        <td className = 'tab_content_label'>商品封面图片:</td>
	                    <td>
	                    	 <img className = 'img' src = {data.goods_thumb?data.goods_thumb.substring(1):''}/>     
	                    </td>
                    </tr>
                </table>
            </div>
        )
	}
}

class TabContent_2 extends React.Component{	
	render(){
		const {rent , data , now_tab} = this.props;
		var html = [];
		rent.map(function(v , k){
			html.push(
			    <div>                                               
                   {v.start} - {v.end}
                   价格:{v.goods_rent_price}
                </div>
            );
		})
	    return (
            <div>		                    	   
                <table>                            
                    <tr>
                        <td className = 'tab_content_label'>最小出租月数:</td>
                        <td>{data.min_rent}</td>
                    </tr>  
                    <tr>
                        <td className = 'tab_content_label'>最大出租月数:</td>
                        <td>{data.max_rent}</td>
                    </tr> 
                    <tr >
                        <td className = 'tab_content_label'>区间优惠设置:</td>
                        <td>
                          <div>                                               
                              {html}
                          </div>                   
                        </td>
                     </tr>
                </table>
            </div>
        )
	}
}
class TabContent_3 extends React.Component{
	render(){
		const {goods_gallery , now_tab} = this.props;
		var html = [];
		goods_gallery.map(function(v , k){
		    html.push(		        
		        <li className = 'img_container'><img src = {v.gallery_img.substring(1)}></img></li> 		        
		    );	
		})
	    return (
            <div>
                <p>商品相册：</p>
                <table>                            
	                <tr>
		                <td></td>
		                <td>
			                <div><ul>{html}</ul></div> 
		                </td> 
		            </tr>                                                   
                </table>
            </div>
        )
	}
}

class TabContent_4 extends React.Component{
	render(){
		const {goods_data , now_tab} = this.props;
	    return (
            <div>	
                <p>商品详情：</p>
                <div className = 'content_text' dangerouslySetInnerHTML={{__html: goods_data.goods_content}}>
                </div>
            </div>
        )
	}
}

class TabContent_5 extends React.Component{
	fieldValueChange(ref , dispatch){
		var dom  = this.refs[ref];
		var val  = dom.value;
		var name = dom.name;
		dispatch(actions.fieldValueChange(name , val));
	}
	
	render(){
		const {goods_check_data , data , now_tab , dispatch} = this.props;
		var check_status = goods_check_data.check_status == '0' ? true : false;
		console.log(goods_check_data.content);
	    return (
            <div>		                    	   
                <table>                            
                    <tr>
		                <td className = 'tab_content_label'>审核结果:</td>
		                <td>
		                   <input type='radio' name='check_status' ref = 'is_check1' value='1' className = 'check' onClick = {this.fieldValueChange.bind(this , 'is_check1' , dispatch)} checked = {!check_status}></input>
		                   <p className = 'check_text'>通过</p>
		                   <input type='radio' name='check_status' ref = 'is_check2' value='0' className = 'check' onClick = {this.fieldValueChange.bind(this , 'is_check2' , dispatch)} checked = {check_status}></input>
		                   <p className = 'check_text'>不通过</p>
		                </td>
		            </tr>  
                    <tr>
	                    <td className = 'tab_content_label' >审核意见:</td>
	                    <td>
	                        <textarea name = 'content' ref = 'content' onChange = {this.fieldValueChange.bind(this , 'content' ,dispatch)} value = {goods_check_data.content}></textarea>
	                    </td>
                    </tr>
                    <tr>
                        <td className = 'tab_content_label' >操作</td>
                        <td>
                            <a className = 'btn' href='javascript:;' onClick={() => dispatch(actions.goodsChcek())}>确认审核</a>
                            <a className = 'btn' href='javascript:;' onClick={() => dispatch(actions.goodsEditHide())}>取消</a>
                        </td>
                    </tr> 
                </table>
            </div>
        )
	}
}

function mapStateToProps(state) {
   return {
      value: {
    	  edit_data:state.edit_data,
    	  goods_rent : state.goods_data.goods_rent?state.goods_data.goods_rent:[],
    	  goods_gallery : state.goods_data.goods_gallery?state.goods_data.goods_gallery:[],
    	  goods_data : state.goods_data.goods_data?state.goods_data.goods_data:[],	  
    	  goods_edit_state:state.goods_edit_state,
    	  goods_check_data:state.goods_check_data,
    	  brand_name : state.goods_data.brand_name,
    	  now_tab : state.now_tab
      }
   }
}
export default connect(mapStateToProps)(fixedTabEdit);