import thunk from 'redux-thunk';
import cash from '../../js/GoodsSeo/goodsList';

let actions = {
	/*显示编辑框*/
	fixedEditShow : (id) => { 		
		return {
   		    type : 'fixed_edit_show',
		    id   : id
	    }	
	},
    /*隐藏编辑框*/
	fixedEditHide : () => ({ 
   		type : 'fixed_edit_hide',
	}),
	/*共享商品SEO input框值*/
	editInputChange : (name , value) => { 
		return {
		     type  : 'edit_input_change',
   		     name  : name,
   		     value : value
 	    }
	},
	/*分页栏下拉选择每页显示的数量改变*/
	pageListrowsChange : (value , dispatch) => (dispatch) => { 
		cash.listRows  = value;
		cash.tableData = {};		
		actions.getData(1,{'firstRows':0,'listRows':cash.listRows})(dispatch);
	},
    /*获取共享商品数据  缓存数据*/
    getData : (nowPage,parmers) => (dispatch) => {    	    	
    	parmers = cash.getParamers(nowPage , parmers);//获取参数
    	if(!cash.tableData[nowPage]){
    		cash.tableDataState = true;
	        $.post('/index.php/GoodsSeo/goodsList',parmers,function(res){	 
	        	 dispatch({
	        		 type    : 'get_goods_data',
	        		 nowPage : nowPage,
	        		 data    : res
	        	 });
	        });
        }else{
	       	 dispatch({
	    		 type    : 'get_goods_data',
	    		 nowPage : nowPage
	    	 });
        }
    },
    /*共享商品SEO编辑*/
    goodsSeoEdit : () => (dispatch) => {
       $.post('/index.php/GoodsSeo/goodsSeoUpdate',cash.fixedEditData,function(res){
    	   if(res.status){  
    		   if(res.id){
      	       	   dispatch({
    	       		   type  : 'goods_seo_add',
    	       		   id    : res.id
    	       	   });     			   
    		   }else{
      	       	   dispatch({
    	       		   type  : 'goods_seo_edit'
    	       	   });     			   
    		   }   	  
  	       }else{
  	    	   alert(res.msg);
  	       }
       });
    }
};

export default actions;


