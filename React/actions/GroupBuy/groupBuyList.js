import thunk from 'redux-thunk';
import cash from '../../js/GroupBuy/groupBuyList';

let actions = {
		
/*************************************团购商品编辑********************************************************/ 		
   
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
	imgChange : (name , value) => {
		
		return {
			name  : name,
			value : value,
   		    type  : 'img_change'
		}
	},
	groupBuyEdit : () => (dispatch) => { 
		var parmers = {};
		var data    = cash.fixedEditData;
		var formData = new FormData(); 
		formData.append('id' , data.id);
		if(data.img_1 && data.img_1.substring(0,4) == 'data'){
			var blob     = cash.dataURItoBlob(data.img_1);
			var filename = blob.type.substring(6);
			formData.append('img_1' , blob,'blob.'+filename);
    	}
		if(data.img_2 && data.img_2.substring(0,4) == 'data'){
			var blob     = cash.dataURItoBlob(data.img_2);
			var filename = blob.type.substring(6);
			formData.append('img_2' , blob,'blob.'+filename);
    	}
		if(data.img && data.img.substring(0,4) == 'data'){
			var blob     = cash.dataURItoBlob(data.img);
			var filename = blob.type.substring(6);
			formData.append('img' , blob,'blob.'+filename);
    	}
	    $.ajax({  
	          url  : '/index.php/GroupBuy/groupBuyUpdate' ,  
	          type : 'POST',  
	          data : formData,  
	          async: true,  
	　　　　　          cache: false,  
	          contentType: false,  
	          processData: false,  
	          success: function (res) { 
	        	  if(res.status){
		        	  dispatch({
			         		 type    : 'group_buy_update'
			          });	        		  
	        	  }else{
	        		  alert(res.msg);
	        	  }
	          },  
	          error: function (returndata) {  
	              console.log(returndata); 
	          }  
	     });
	},
/*************************************团购商品详情********************************************************/	
 
	/*显示详情框*/
	fixedDetailsShow : (id) => { 
		return {
   		    type : 'fixed_details_show',
		    id   : id
	    }	
	},
    /*隐藏详情框*/
	fixedDetailsHide : () => ({ 
   		type : 'fixed_details_hide',
	}),
    /*获取团购申请数据  缓存数据*/
    getData : (nowPage,parmers) => (dispatch, getState) => { 
    	cash.tableDataState = true;    	
    	if(!cash.tableData[nowPage]){
	        $.post('/index.php/GroupBuy/groupByList',parmers,function(res){	 
	        	 dispatch({
	        		 type    : 'get_group_data',
	        		 nowPage : nowPage,
	        		 data    : res
	        	 });
	        });
        }else{
	       	 dispatch({
	    		 type    : 'get_group_data',
	    		 nowPage : nowPage
	    	 });
        }
    },

};

export default actions;


