import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import cash from '../../js/Admin/adminList';

var data = cash.getConfig();

function counter(state = data , action) {
  switch (action.type) {
    /*获取管理员列表数据*/
    case 'get_admin_data':
       if(action.data !== undefined){
    	   //ajax获取到的数据处理
    	   cash.setAdminData(action.data.data);
      	   cash.totalRows = action.data.total;
       };
       cash.nowPage = action.nowPage;//当前页数
       return cash.getData();
    /*管理员锁定状态改变*/
    case 'lock_change':
       cash.tableData[cash.nowPage][action.id].lock = action.lock;
       return cash.getData();       
    /*获取权限分组数据*/
    case 'get_group_data':     
       cash.group = cash.arrayCloumn(action.data , 'id');
       return cash.getData();      
       
/***************************************添加管理员*****************************************************/       
    /*显示添加管理员框*/
    case 'fixed_add_show':
       cash.fixedAddShow = true;
       return cash.getData(); 
    /*隐藏添加管理员框*/
    case 'fixed_add_hide':
       cash.fixedAddShow = false;
       return cash.getData();
    /*管理员框值改变*/
    case 'add_input_change':
       cash.fixedAddData[action.name] = action.value;
       return cash.getData();
    /*管理员添加成功*/   
    case 'admin_add':
       var data    = cash.fixedAddData;
           data.id = action.data.id;
           data.group_name = cash.group[data.group_id]['title'];
       cash.tableData[cash.nowPage].push(data);
       cash.fixedAddShow = false;
       return cash.getData();

/***************************************管理员账号编辑*****************************************************/       
    /*显示编辑管理员框*/
    case 'fixed_edit_show':
       var data = cash.tableData[cash.nowPage][action.id];
	   cash.fixedEditData = {
		   id             : data.id,
           username       : data.username,
           name           : data.name,
           lock           : data.lock,
           group_id       : data.group_id,
           password       : '',
           repeatpassword : '',
           is_user        : 0,
           is_pwd         : 0//默认不修改密码
       };
       cash.fixedEditShow = true;
       return cash.getData();
    /*隐藏编辑管理员框*/
    case 'fixed_edit_hide':
       cash.fixedEditShow = false;
       return cash.getData();   
    /*管理员框值改变*/
    case 'edit_input_change':
       cash.fixedEditData[action.name] = action.value;
       return cash.getData();       
       /*管理员信息修改成功*/   
    case 'admin_edit':
       cash.tableData[cash.nowPage][cash.fixedEditData.id] = cash.fixedEditData;
       cash.fixedEditShow = false;
       return cash.getData();
    default:
       return state;
  }
}

// Store
const store = createStore(counter,applyMiddleware(thunk));
export default store;

