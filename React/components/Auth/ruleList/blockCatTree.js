import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Auth/ruleList';
 /* *
   * 联动分类树
   * */
//<a className = 'toggel' href='javascript:;'>+</a>
class blockCatTree extends React.Component{
    render(){  
    	const {value , dispatch } = this.props;
    	if(!value.rules_state){
    		dispatch(actions.getRules());
    	}else{
        	var data  = value.data,
        	    html  = [],
        	    html_child = [],
        	    child = value.child;
        	var _this = this;
        	data.map(function(v , k){
        		html.push(
        		    <div className = 'cat_tree_level'>
	        		    <p className ='cat_tree_name'>		       			    
		       			    <a className = 'level_name' href = 'javascript:;' onClick = {() => {dispatch(actions.showChild(v.id))}}>{v.title}</a>
		       			    <a className = 'handle_name' href = 'javascript:;' onClick = {() => {dispatch(actions.fixedEdit(v.pid , v.id))}}>编辑</a>
		       			    <a className = 'handle_name' href = 'javascript:;' onClick = {() => {dispatch(actions.showChild(v.id))}}>删除</a>
		       			</p>
        		    </div>
        		);     				
        	});
        	child.map(function(v , k){
        		html_child.push(
        		    <div className = 'cat_tree_level'>
	        		    <p className ='cat_tree_name'>		       			    
		       			    <a className = 'level_name' href = 'javascript:;' >{v.title}</a>
		       			    <a className = 'handle_name' href = 'javascript:;' onClick = {() => {dispatch(actions.fixedEdit(v.pid , v.id))}}>编辑</a>
		       			    <a className = 'handle_name' href = 'javascript:;' onClick = {() => {dispatch(actions.showChild(v.id))}}>删除</a>
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
                <div className = 'tree_handle'>
                    <a className = 'submit_btn' href = 'javascript:;' onClick = {() => {dispatch(actions.fixedAdd())}}>添加规则</a>
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
export default connect(mapStateToProps)(blockCatTree);