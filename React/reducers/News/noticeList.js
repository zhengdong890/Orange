import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import cash from '../../js/News/noticeList';

var data = cash.getConfig();

function counter(state = data , action) {
  switch (action.type) {
    /*获取新闻资讯列表数据*/
    case 'get_news_data':
       cash.nowPage = action.nowPage;//当前页数
       if(action.data !== undefined){
    	   //ajax获取到的数据处理
    	   cash.setNewsData(action.data.data);
      	   cash.totalRows = action.data.total;
       };    
       return cash.getData();  
    /*删除新闻资讯列表数据*/
    case 'delete_news':
       delete cash.tableData[cash.nowPage][action.id]
       return cash.getData();  
       /*新闻资讯状态改变*/
    case 'news_status_change':
   	   cash.changeNewsStatus(action.id , action.status);
       return cash.getData();         
    default:
       return state;
  }
}

// Store
const store = createStore(counter,applyMiddleware(thunk));
export default store;

