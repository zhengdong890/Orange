import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Goods/checkList';

 /* *
   * 联动分类树
   * */
class blockCatTree extends React.Component{
    render(){  
    	const {value , dispatch } = this.props;
    	if(!value.cat_tree_data_state){
    		dispatch(actions.getCategorys());
    	}else{
        	var data  = value.cat_tree_data,
        	    html  = [],
        	    html_child = [],
        	    child = value.child;
        	var _this = this;
        	data.map(function(v , k){
        		var paramer = {cat_id : v.id};
        		html.push(
        		    <div className = 'cat_tree_level'>
	        		    <p className ='cat_tree_name'>		       			    
		       			    <a className = 'level_name' href = 'javascript:;' onClick = {() => {dispatch(actions.getData(1,'',v.id))}}>{v.cat_name}</a> 
		       			</p>
        		    </div>
        		);     				
        	});
        	child.map(function(v , k){
        		html_child.push(
        		    <div className = 'cat_tree_level'>
	        		    <p className ='cat_tree_name'>		       			    
		       			    <a className = 'level_name' href = 'javascript:;' onClick = {() => {dispatch(actions.getData(1,'',v.id))}}>{v.cat_name}</a>
		       			</p>
        		    </div>
        		);     				
        	}); 
    	}    
        return(
            <div id = 'cat_tree_block' >
                <ul className = 'cat_tree_wraper'>
                    {html}
                </ul>
                <ul className = 'cat_tree_wraper'>
                    {html_child}
                </ul>
           	</div>     
        )
    }
}

function mapStateToProps(state) {
   return {
      value: state
   }
}
export default connect(mapStateToProps)(blockCatTree);