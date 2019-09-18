import thunk from 'redux-thunk';
import cash from '../../js/Admin/adminList';

let actions = {
	/*ajax获取权限分组*/
	getGroup : () => (dispatch) => {
		if(!cash.groupState){
		    cash.groupState = true;
	        $.post('/index.php/AuthGroup/getGroup',{},function(res){
	        	 if(res.status != 0){
	            	 dispatch({
	            		 type : 'get_group_data',
	    	    		 data : res
	            	 });        		 
	        	 }else{
	        		 alert(res.msg);
	        	 }
	        });			
		}
    },
        
/*************************************添加管理员********************************************************/    
	
    /*显示添加分组框*/
	fixedAddShow : () => ({
        type : 'fixed_add_show',	    	
	}),
    /*隐藏添加管理员框*/
	fixedAddHide : () => ({
        type : 'fixed_add_hide',	    	
	}),
	/*添加管理员框 input框值记录*/
	addInputChange : (name , value) => { 		
		return {
		     type  : 'add_input_change',
   		     name  : name,
   		     value : value
 	    }
	},	
	/*ajax添加管理员*/
	adminAdd : () => (dispatch) => { 
        $.post('/index.php/Admin/adminAdd',cash.fixedAddData,function(res){
        	 if(res.status != 0){
            	 dispatch({
            		 type : 'admin_add',
    	    		 data : res
            	 });        		 
        	 }else{
        		 alert(res.msg);
        	 }
        });
    },
    
/*************************************编辑管理员账号********************************************************/    
	
    /*显示添加分组框*/
	fixedEditShow : (id) => { 
		return {
   		    type : 'fixed_edit_show',
		    id   : id
	    }	
	},
    /*隐藏添加分组框*/
	fixedEditHide : () => ({ 
   		type : 'fixed_edit_hide',
	}),
	/*编辑管理员框 input框值记录*/
	editInputChange : (name , value) => { 
		return {
		     type  : 'edit_input_change',
   		     name  : name,
   		     value : value
 	    }
	},	
	/*ajax修改管理员账号信息*/
	adminEdit : () => (dispatch) => { 
        $.post('/index.php/Admin/adminUpdate',cash.fixedEditData,function(res){
        	 if(res.status != 0){
            	 dispatch({
            		 type : 'admin_edit',
    	    		 data : res
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
	        	 for(var k in res){
	        		 cash.rules[res[k].id] = res[k]; 
	        	 }
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
    /*获取管理员数据 搜索 缓存数据*/
    getData : (nowPage,parmers) => (dispatch, getState) => { 
    	cash.tableDataState = true;    	
    	if(!cash.tableData[nowPage]){
	        $.post('/index.php/Admin/adminList',parmers,function(res){	 		        	 
	        	 dispatch({
	        		 type    : 'get_admin_data',
	        		 nowPage : nowPage,
	        		 data    : res
	        	 });
	        });
        }else{
	       	 dispatch({
	    		 type    : 'get_admin_data',
	    		 nowPage : nowPage
	    	 });
        }
    },
    /*锁定管理员状态 改变*/
    lockChange : (id , lock) => (dispatch) => {  	
    	lock = lock == 1 ? 0 : 1;
        $.post('/index.php/Admin/lockChange',{id : id , lock : lock},function(res){	 
        	 if(res.status != 0){
            	 dispatch({
            		 type  : 'lock_change',
            		 id    : id,
            		 lock  : lock
            	 });        		 
        	 }else{
        		 alert(res.msg);
        	 }
        });
    }, 

};

export default actions;


