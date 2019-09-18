import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/MallGoods/goodsList';

 /* *
   * 搜索部分
   * */
class searchForm extends React.Component{
	keywordsChange(dispatch){
		var dom = this.refs['keyword'];
		var val = dom.value;
		dispatch(actions.keywordsChange(val));
	}

	categorysChange(dispatch){
		var dom = this.refs['cat_id'];
		var val = dom.value;
		dispatch(actions.categorysChange(val));
	}

	goodsModelChange(dispatch){
		var dom = this.refs['model_id'];
		var val = dom.value;
		dispatch(actions.goodsModelChange(val));
	}

	goodsStatusChange(dispatch){
		var dom = this.refs['search_status'];
		var val = dom.value;
		dispatch(actions.goodsSearchStatusChange(val));
	}

	render(){
    	  const {value , dispatch } = this.props;
    	  if(!value.category_state){
    		  dispatch(actions.getCategory());
    	  }else{
    		  var category = value.category;
    		  //console.log(category);
    		  var options = [];
    		  category.map(function(v , key){   
    			  options.push(<option value = {v.id} >{v.cat_name}</option>);
    			  if(v['child']){
        			  v['child'].map(function(v , key){
        			  	options.push(<option value = {v.id} >&nbsp;&nbsp;&nbsp;&nbsp;----&nbsp;{v.cat_name}</option>);
        			  	if(v['child']){
	        			  v['child'].map(function(v , key){
	        			  	  options.push(<option value = {v.id} style = {{color:'red'}}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;----&nbsp;{v.cat_name}</option>);
		        			  if(v['child']){
		  	        			  v['child'].map(function(v , key){
		  	        			  	  options.push(<option value = {v.id} style = {{color:'blue'}}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;----&nbsp;{v.cat_name}</option>);
		  	        			  })     				  
		  	    			  } 
	        			  })     				  
	    			    }          				  
        			  })     				  
    			  } 			 
    		  });
    	  }
      	  var goods_model = value.goods_model;
		  var model_options = [];
		  goods_model.map(function(v , key){
			  model_options.push(<option value = {v.id} >{v.name}</option>);
		  });

          return(
              <div id='searchForm'>         
                  <p> 
	                  <select defaultValue = '0' name='cat_id' ref = 'cat_id' onChange = {this.categorysChange.bind(this , dispatch)}>
	                      <option value="0">所有分类</option>
	                      {options}>           
	                  </select>
	               </p>
	               <p> 加入推荐:
	                    <select  defaultValue = '0' name = "model_id" ref = 'model_id' onChange = {this.goodsModelChange.bind(this , dispatch)}>
	                        <option value="0">全部</option>
	                        {model_options}          
	                    </select>
	                </p>
	                <p> 上下架:
	                    <select  defaultValue = '-1' name = "search_status" ref = 'search_status' onChange = {this.goodsStatusChange.bind(this , dispatch)}>
	                        <option value="-1">全部</option>
	                        <option value="1">上架</option>
	                        <option value="0">下架</option>         
	                    </select>
	                </p>
	                <p>关键字: <input type='text' ref = 'keyword' name='keyword'  defaultValue = {value.search.keyword} onBlur = {this.keywordsChange.bind(this , dispatch)}></input></p>
	                <button onClick={() => {dispatch(actions.getData())}}>搜索</button>
	          </div>
          )
      }
}
function mapStateToProps(state) {
   return {
      value: state
   }
}
export default connect(mapStateToProps)(searchForm);