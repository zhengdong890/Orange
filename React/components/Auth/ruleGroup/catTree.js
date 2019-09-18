import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Auth/ruleGroup';
 /* *
   * 弹出无尽分类树
   * */
//<a className = 'toggel' href='javascript:;'>+</a>
class catTree extends React.Component{
	rulesSelectAll(id , dispatch){
	    dispatch(actions.rulesSelectAll(id));	
	}
	
    render(){  
    	const {value , dispatch } = this.props;
    	if(!value.rules_state){
    		dispatch(actions.getRules());
    	}else{
        	var rules       = value.rules,
        	    rules_check = value.rules_check;
        	var html  = [];
        	var _this = this;
        	rules.map(function(v , k){
        		var html_child = [];
        		if(v['child']){
            		v['child'].map(function(v1 , k1){
            			html_child.push(       			        
            			    <div className = 'cat_tree_level'>
    				            <p className = 'cat_tree_name'>
    				                <input type = 'checkbox' value = {v1.id} checked = {rules_check[v1.id]} onClick = {_this.rulesSelectAll.bind(_this , v1.id , dispatch)} />
               			            <a className = 'level_name'>{v1.title}</a>
    				            </p>
    			            </div>
    			        );
            		});        			
        		}
        		html.push(
        		    <div className = 'cat_tree_level'>
	        		    <p className ='cat_tree_name'>		       			    
		       			    <a className = 'level_name'>{v.title}</a>
		       			    <input type = 'checkbox' value = {v.id}  checked = {rules_check[v.id]} onClick = {_this.rulesSelectAll.bind(_this , v.id , dispatch)} />
	       			    </p>
	       			    <ul className = 'cat_tree_container'>
	       			        {html_child}
	       			    </ul>
        		    </div>
        		);     				
        	})    		
    	}    
        return(
            <div id = 'cat_tree'  className = {value.change_rule_state?'':'cat_tree_hide'} >
                <ul className = 'cat_tree_wraper'>
                    {html}
                </ul>
                <div className = 'tree_handle'>
                    <a className = 'submit_btn' href = 'javascript:;' onClick = {() => {dispatch(actions.ruleGroupAccess())}}>确认修改</a>
                    <a className = 'submit_btn' href = 'javascript:;' onClick = {() => {dispatch(actions.changeRulesHide())}}>取消</a>
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
export default connect(mapStateToProps)(catTree);