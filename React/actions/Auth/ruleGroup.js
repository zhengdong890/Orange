import thunk from 'redux-thunk';
import cash from '../../js/Auth/ruleGroup';

let actions = {

	changeRulesHide : () => {
		cash.change_rule_state = false;
		return {
            type: 'change_rules',
            data : cash.getData()
		}
    },
    
/*************************************添加分组********************************************************/    
	
    /*显示添加分组框*/
	fixedAddShow : () => (dispatch) => { 
		cash.fixed_add_show = true;
		dispatch({
   		    type : 'fixed_add_data',
		    data : cash.getData()
	    })	
	},
    /*隐藏添加分组框*/
	fixedAddHide : () => (dispatch) => { 
		cash.fixed_add_show = false;
		dispatch({
   		    type : 'fixed_add_data',
		    data : cash.getData()
	    })	
	},
	/*分组框 input框值记录*/
	addInputChange : (name , value) => { 
		cash.fixed_add_data[name] = value;
		return {
		     type : 'fixed_add_data',
   		     data : cash.getData()
 	    }
	},	
	/*ajax添加分组*/
	groupAdd : () => (dispatch) => { 
        $.post('/index.php/AuthGroup/ruleGroupAdd',cash.fixed_add_data,function(res){
        	 if(res.status != 0){
        		 cash.fixed_add_show = false;
        		 var data = cash.fixed_add_data;
        		 data.id  = res.id;
        		 cash.tableData[cash.nowPage].push(data);
            	 dispatch({
            		 type : 'fixed_add_data',
    	    		 data : cash.getData()
            	 });        		 
        	 }else{
        		 alert(res.msg);
        	 }
        });
    },
    
/*************************************编辑分组********************************************************/    
	
    /*显示添加分组框*/
	fixedEditShow : (id) => { 
		cash.fixed_edit_data = cash.tableData[cash.nowPage][id];
		cash.fixed_edit_show = true;
		return {
   		    type : 'fixed_edit_data',
		    data : cash.getData()
	    }	
	},
    /*隐藏添加分组框*/
	fixedEditHide : () => (dispatch) => { 
		cash.fixed_edit_show = false;
		dispatch({
   		    type : 'fixed_edit_data',
		    data : cash.getData()
	    })	
	},
	/*分组框 input框值记录*/
	editInputChange : (name , value) => { 
		cash.fixed_edit_data[name] = value;
		return {
		     type : 'fixed_edit_data',
   		     data : cash.getData()
 	    }
	},	
	/*ajax添加分组*/
	groupEdit : () => (dispatch) => { 
        $.post('/index.php/AuthGroup/ruleGroupUpdate',cash.fixed_edit_data,function(res){
        	 if(res.status != 0){
        		 var id = cash.fixed_edit_data.id;
        		 cash.fixed_edit_show = false;
        		 cash.tableData[cash.nowPage][id] = cash.fixed_edit_data;
            	 dispatch({
            		 type : 'fixed_edit_data',
    	    		 data : cash.getData()
            	 });        		 
        	 }else{
        		 alert(res.msg);
        	 }
        });
    },
    
	/*规则分配选择*/
	rulesSelectAll : (id) => (dispatch, getState) => { 
		if(cash.rules_check[id]){
		    delete cash.rules_check[id];
		    if(typeof(cash.rules[id]) != 'undefined' && typeof(cash.rules[id]['child']) != 'undefined'){
		        for(var k in cash.rules[id]['child']){
		        	var child_id = cash.rules[id]['child'][k].id;
		        	delete cash.rules_check[child_id];
		        }	
		    }
		}else{
			cash.rules_check[id] = id;
		    if(typeof(cash.rules[id]) != 'undefined' && typeof(cash.rules[id]['child']) != 'undefined'){
		        for(var k in cash.rules[id]['child']){
		        	var child_id = cash.rules[id]['child'][k].id;
		        	cash.rules_check[child_id] = child_id;
		        }	
		    }
		}
		dispatch({
   		    type : 'change_select',
   		    data : cash.getData()
   	    });
	},
    /*获取规则*/
    getRules : () => (dispatch, getState) => { 
    	if(!cash.rules_state){
    		cash.rules_state = true;
	        $.post('/index.php/Auth/getRules',{},function(res){
	        	 cash.setRulesData(res);
	        	 dispatch({
	        		 type : 'get_rules',
		    		 data : cash.getData()
	        	 });
	        });
        }else{
	       	 dispatch({
	    		 type : 'get_rules',
	    		 data : cash.getData()
	    	 });
        }
    },
    /*获取分组数据 搜索 缓存数据*/
    getData : (nowPage,parmers) => (dispatch, getState) => { 
    	cash.nowPage = nowPage;
    	cash.table_data_state = true;
    	if(!cash.tableData[nowPage]){
	        $.post('/index.php/AuthGroup/ruleGroup',parmers,function(res){	 
	        	 cash.setGroupData(res.data);
	        	 cash.totalRows = res.total;		        	 
	        	 dispatch({
	        		 type : 'get_data',
	        		 data : cash.getData()
	        	 });
	        });
        }else{
	       	 dispatch({
	    		 type : 'get_data',
	    		 data : cash.getData()
	    	 });
        }
    },
    /*配置权限 弹出*/
    changeRules: (id) => (dispatch, getState) => { 
    	var rules = cash.rules;
    	var group = cash.tableData[cash.nowPage][id];
    	cash.rules_check = [];
    	cash.nowGroupId  = id;
    	for(var k in rules){
    		for(var k1 in rules[k]['child']){
        		if($.inArray(rules[k]['child'][k1].id,group.rules) != -1){
        			cash.rules_check[rules[k]['child'][k1].id] = rules[k]['child'][k1].id;	
        		} 
    		}
    	}
    	cash.change_rule_state = true;
       	dispatch({
    		type : 'change_rules',
    		data : cash.getData()
    	});
    },
    /*分组ajax修改权限*/
    ruleGroupAccess : () => (dispatch) => { 
    	var id = cash.nowGroupId;
        $.post('/index.php/AuthGroup/ruleGroupAccess',{id:id,rules:cash.rules_check},function(res){	  
        	alert(res.msg);
        	if(res.status){
        	    cash.tableData[cash.nowPage][id]['rules'] = cash.rules_check;
        	}        	
        	cash.change_rule_state = false;
        	dispatch({
                type: 'change_rules',
                data : cash.getData()
    		});
        });
    }
};

export default actions;


