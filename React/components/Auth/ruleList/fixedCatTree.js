import React from 'react';
import {connect} from 'react-redux';
import actions from '../../../actions/Auth/ruleList';
 /* *
  * 弹出分类树
  * */
class catTree extends React.Component{
	changeParent(dispatch , id , pid){
	    dispatch(actions.changeParent(id , pid));	
	}
	
    render(){  
    	const {value , dispatch } = this.props;
    	if(!value.rules_state){
    		dispatch(actions.getRules());
    	}else{
        	var rules = value.data;
        	var html  = [];
        	var _this = this;
        	rules.map(function(v , k){
        		var html_child = [];
        		v['child'].map(function(v1 , k1){
        			html_child.push(       			        
        			    <div className = 'cat_tree_level'>
				            <p className = 'cat_tree_name'>
           			            <a className = 'level_name' onClick = {_this.changeParent.bind(_this , dispatch , v1.id , v1.pid)} href = 'javascript:;'>{v1.title}</a>
				            </p>
			            </div>
			        );
        		});
        		html.push(
        		    <div className = 'cat_tree_level'>
	        		    <p className ='cat_tree_name'>		       			    
		       			    <a className = 'level_name' onClick = {_this.changeParent.bind(_this , dispatch , v.id ,v.pid)} href = 'javascript:;'>{v.title}</a>
	       			    </p>
	       			    <ul className = 'cat_tree_container'>
	       			        {html_child}
	       			    </ul>
        		    </div>
        		);     				
        	})    		
    	}    
        return(
            <div id = 'cat_tree'  className = {value.cat_tree_state?'':'cat_tree_hide'} >
                <ul className = 'cat_tree_wraper'>
                    {html}
                </ul>
                <div className = 'tree_handle'>
                    <a className = 'submit_btn' href = 'javascript:;' onClick = {() => {dispatch(actions.catTreeHide())}}>取消</a>
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