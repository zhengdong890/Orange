import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import cash from '../../js/Purchase/purchaseList';

var data = cash.getConfig();

function counter(state = data , action) {
  switch (action.type) {
    /*获取设备采购数据*/
    case 'get_purchase_data':
       cash.nowPage = action.nowPage;//当前页数
       if(action.data !== undefined){
    	   //ajax获取到的数据处理
    	   cash.setPurchaseData(action.data.data);
      	   cash.totalRows = action.data.total;
       };      
       return cash.getData();    
       
/***************************************添加设备采购*****************************************************/       
    /*显示添加设备采购框*/
    case 'fixed_add_show':
       cash.fixedAddShow = true;
       return cash.getData(); 
    /*隐藏添加设备采购框*/
    case 'fixed_add_hide':
       cash.fixedAddShow = false;
       return cash.getData();
    /*设备采购框值改变*/
    case 'add_input_change':
       cash.fixedAddData[action.name] = action.value;
       return cash.getData();
    /*设备采购框值改变*/
    case 'add_type_change':
       cash.fixedAddData['type'][action.num] = cash.fixedAddData['type'][action.num] == 0 ? action.num+1 : 0;
       return cash.getData();   
    /*设备采购添加成功*/   
    case 'purchase_add':
       var data    = cash.fixedAddData;
           data.id = action.data.id;
       cash.tableData[cash.nowPage].push(data);
       cash.fixedAddShow = false;
       return cash.getData();

/***************************************设备采购详情*****************************************************/       
    /*显示设备采购详情*/
    case 'fixed_details_show':
	   cash.fixedDetailsData = cash.tableData[cash.nowPage][action.id];
       cash.fixedDetailsShow = true;
       return cash.getData();
    /*隐藏编设备采购详情*/
    case 'fixed_details_hide':
       cash.fixedDetailsShow = false;
       return cash.getData(); 
       
/***************************************设备采购编辑*****************************************************/       
    /*显示设备采购编辑*/
    case 'fixed_edit_show':
	   cash.fixedEditData = cash.tableData[cash.nowPage][action.id];	   	   
       cash.fixedEditShow = true;
       return cash.getData();
    /*隐藏编设备采购编辑*/
    case 'fixed_edit_hide':
       cash.fixedEditShow = false;
       return cash.getData();     
    /*设备采购框值改变*/
    case 'edit_input_change':
       cash.fixedEditData[action.name] = action.value;
       return cash.getData(); 
    /*设备采购框类型值改变*/
    case 'edit_type_change':
       cash.fixedEditData['type'][action.num] = cash.fixedEditData['type'][action.num] == 0 ? action.num+1 : 0;
       console.log(cash.fixedEditData['type']);
       return cash.getData();         
       /*管理员信息修改成功*/   
    case 'purchase_edit':
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

