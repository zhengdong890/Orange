import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Goods/checkList';

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
	
	render(){
    	  const {value , dispatch } = this.props;
    	  if(!value.category_state){
    		  dispatch(actions.getCategory());
    	  }else{
    		  var category = [];
    		  for(var k in value.category){
    			  category.push(value.category[k]);
    		  }
    		  var options = [];    		  
    		  category.map(function(v , key){
    			  options.push(<option value = {v.id} >{v.html}{v.cat_name}</option>);
    		  });
    	  }
          return(
              <div id='searchForm'>         
                  <p> 
	                  <select defaultValue = '0' name='cat_id' ref = 'cat_id' onChange = {this.categorysChange.bind(this , dispatch)}>
	                      <option value="0">所有分类</option>
	                      {options}>           
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