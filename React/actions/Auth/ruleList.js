import thunk from 'redux-thunk';
import cash from '../../js/Auth/ruleList';

let actions = {
	/*修改输入编辑框的值*/
	inputAddChange : (field , val) => {
		cash.fixed_add_data[field] = val;
		return {
   		    type : 'input_add_change',
   		    data : cash.getData()
   	    };
	},
	/*修改输入编辑框的值*/
	inputEditChange : (field , val) => {
		cash.fixed_edit_data[field] = val;
		return {
   		    type : 'input_edit_change',
   		    data : cash.getData()
   	    };
	},
	catTreeShow : () => { 
    	cash.cat_tree_state = true;
      	return {
    		 type : 'cat_tree_state',
    		 data : cash.getData()
    	};
    },	
    catTreeHide : () => { 
    	cash.cat_tree_state = false;
      	return {
    		 type : 'cat_tree_state',
    		 data : cash.getData()
    	};
    },	
    /*切换父级*/
    changeParent : (id , pid) => { 
    	cash.cat_tree_state = false;
    	if(cash.type == 'add'){
        	if(pid != 0){
        		cash.fixed_add_data.p_name = cash.data[pid]['child'][id]['title'];
        		cash.fixed_add_data.pid = cash.data[pid]['child'][id]['pid'];
        	}else{
        		cash.fixed_add_data.p_name = cash.data[id]['title'];
        		cash.fixed_add_data.pid    = cash.data[id]['id'];
        	}      		
    	}else{
        	if(pid != 0){
        		cash.fixed_edit_data.p_name = cash.data[pid]['child'][id]['title'];
        		cash.fixed_edit_data.pid = cash.data[pid]['child'][id]['pid'];
        	}else{
        		cash.fixed_edit_data.p_name =  cash.data[id]['title'];
        		cash.fixed_edit_data.pid =  cash.data[id]['id'];
        	}      		
    	}
      	return {
   		   type : 'cat_tree_state',
   		   data : cash.getData()
   	    };
    },	
	/*显示编辑框*/
	fixedEdit : (pid , id) => (dispatch) => {
		cash.type = 'edit'; 
		cash.setFixedEditData(pid , id);
		cash.fixed_edit_show = true;
		dispatch({
   		    type : 'fixed_edit_data',
		    data : cash.getData()
	    })	
	},
	/*隐藏编辑框*/
    fixedEditHide : () => { 
    	cash.fixed_edit_show = false;
      	return {
    		 type : 'fixed_edit_hide',
    		 data : cash.getData()
    	};
    },
	/*显示添加框*/
	fixedAdd : () => (dispatch) => { 
		cash.type = 'add'; 
		cash.fixedAddDataInit();
		cash.fixed_add_show = true;
		dispatch({
   		    type : 'fixed_add_data',
		    data : cash.getData()
	    })	
	},
	/*隐藏添加框*/
    fixedAddHide : () => { 
    	cash.fixed_add_show = false;
      	return {
    		 type : 'fixed_add_hide',
    		 data : cash.getData()
    	};
    },
	/*显示子菜单*/
    showChild : (id) => (dispatch) => { 
    	cash.child = cash.data[id]['child'];
      	dispatch({
    		 type : 'show_child',
    		 data : cash.getData()
    	});
    },
    /*ajax添加规则*/
    ruleAdd : () => (dispatch) => { 
    	$.post('/index.php/Auth/ruleAdd',cash.fixed_add_data,function(res){	
	        if(res.status != 0){
	    	    var pid = cash.fixed_add_data.pid;
	    	    if(pid != 0){
	    	    	cash.data[pid]['child'].push({
	    	    		id        : res.id,
        				pid       : pid,
        				name      : cash.fixed_add_data.name,
        				title     : cash.fixed_add_data.title,
        				condition : cash.fixed_add_data.condition	
	    	    	});
	    	    }else{
	    	    	cash.data.push({
	    	    		id        : res.id,
        				pid       : pid,
        				name      : cash.fixed_add_data.name,
        				title     : cash.fixed_add_data.title,
        				condition : cash.fixed_add_data.condition		
	    	    	});
	    	    }
	    	    cash.fixed_add_show = false;
	    	}else{
	    		alert(res.msg);
	    	}
	       	dispatch({
	       		type : 'rules_add',
		        data : cash.getData()
	       	});
       });     
    },
    /*ajax修改规则*/
    ruleEdit : () => (dispatch) => { 
    	$.post('/index.php/Auth/ruleUpdate',cash.fixed_edit_data,function(res){	
	        if(res.status){
	    	    var pid = cash.fixed_edit_data.pid;
	    	    var id  = cash.fixed_edit_data.id;
	    	    if(pid != 0){
	    	    	cash.data[pid]['child'][id] = {
	    	    		id        : id,
        				pid       : pid,
        				name      : cash.fixed_edit_data.name,
        				title     : cash.fixed_edit_data.title,
        				condition : cash.fixed_edit_data.condition	
	    	    	};
	    	    }else{
	    	    	cash.data[id].pid       = cash.fixed_edit_data.pid;
	    	    	cash.data[id].name      = cash.fixed_edit_data.name;
	    	    	cash.data[id].title     = cash.fixed_edit_data.title;
	    	    	cash.data[id].condition = cash.fixed_edit_data.condition;
	    	    }
	    	    cash.fixed_edit_show = false;
	    	}
	       	dispatch({
	       		type : 'rules_update',
		        data : cash.getData()
	       	});
       });     
    },
    /*获取规则*/
    getRules : () => (dispatch) => { 
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
    }
};

export default actions;


