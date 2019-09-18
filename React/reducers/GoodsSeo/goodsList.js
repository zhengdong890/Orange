import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import cash from '../../js/GoodsSeo/goodsList';

var data = cash.getConfig();

function counter(state = data , action) {
  switch (action.type) {
    /*显示编辑框*/
    case 'fixed_edit_show':
       var data = cash.tableData[cash.nowPage][action.id];
	       cash.fixedEditData = cash.goodsSeo[action.id]?cash.goodsSeo[action.id]:{};
	       cash.fixedEditData.goods_name = data.goods_name;
	       cash.fixedEditData.goods_id   = action.id;
           cash.fixedEditShow = true;
     return cash.getData();
    /*共享商品框值改变*/
    case 'edit_input_change':
       cash.fixedEditData[action.name] = action.value;
       return cash.getData();
    /*隐藏编辑框*/
    case 'fixed_edit_hide':
        cash.fixedEditShow = false;
        return cash.getData();     
    /*获取共享商品列表数据*/
    case 'get_goods_data':
       if(action.data !== undefined){
    	   //ajax获取到的数据处理
    	   cash.setGoodsData(action.data.data);
    	   cash.setGoodsSeoData(action.data.seo);
      	   cash.totalRows = action.data.total;
       };
       cash.nowPage = action.nowPage;//当前页数
       return cash.getData(); 
    /**/
    case 'goods_seo_add':
        cash.goodsSeo[cash.fixedEditData.goods_id] = cash.fixedEditData;
        cash.goodsSeo[cash.fixedEditData.goods_id].id = action.id;
        cash.fixedEditShow = false;
        return cash.getData();    
    /**/
    case 'goods_seo_edit':
        cash.fixedEditShow = false;
        return cash.getData();        
    default:
       return state;
  }
}

// Store
const store = createStore(counter,applyMiddleware(thunk));
export default store;

