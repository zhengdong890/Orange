import thunk from 'redux-thunk';
import cash from '../../js/Goods/checkList';

let actions = {
	/*审核框字段值变化*/
	fieldValueChange : (name , value) => { 
		cash.goods_check_data[name] = value;
		return {
		     type : 'goods_edit',
   		     data : cash.getData()
 	    }
	},
	/*搜索共享商品分类改变*/
	categorysChange : (value) => (dispatch, getState) => { 
		cash.search.cat_id = value;
	},
	/*分页栏下拉选择每页显示的数量改变*/
	pageListrowsChange : (value , dispatch) => (dispatch, getState) => { 
		cash.list_row = value;
		cash.table_data = {};		
		actions.getData(1,{'firstRows':0,'listRows':cash.list_row})(dispatch);
	},
	changeTab : (number) => { 
		cash.now_tab = number;
		return {
 		     type : 'change_tab',
    		 data : cash.getData()
  	    }		
	},
	/*隐藏审核框*/
	goodsEditHide : () => {
		cash.goods_edit_state = false;
		return {
			type : 'goods_edit',
   		    data : cash.getData()
		}
	},
	/*弹出审核框*/
	goodsEditShow : (id) => (dispatch) => { 
	   cash.goods_check_data.id = id;
	   cash.goods_check_data.check_status = '0';
	   cash.goods_check_data.content      = '';
       $.post('/index.php/GoodsCheck/getGoodsData',{id : id},function(res){
    	   cash.goods_data = res; 
    	   cash.edit_data  = cash.table_data[cash.nowPage][id];
    	   cash.goods_edit_state = true;
    	   dispatch({
      		     type : 'goods_edit',
	    		 data : cash.getData()
      	   });
       });	       
	},
	/*审核结果*/
	goodsChcek : () => (dispatch) => { 
        $.post('/index.php/GoodsCheck/goodsCheck',cash.goods_check_data,function(res){
        	if(res.status){
        		cash.goods_edit_state = false;
        		delete cash.table_data[cash.nowPage][cash.goods_check_data.id];
         	    dispatch({
        		    type : 'goods_edit',
          		    data : cash.getData()
        	    });        		
        	}else{
        		alert(res.msg);
        	}
        });	       
	},
    /*获取商品分类*/
    getCategorys : () => (dispatch) => { 
    	if(!cash.cat_tree_data_state){
    		cash.cat_tree_data_state = true;
	        $.post('/index.php/Category/getCategory',{},function(res){
	        	 for(var k in res){
	        		 cash.cat_tree_data[res[k].id] = res[k];
	        		 var child = [];
	        		 var child_id = [];
	        		 for(var k1 in res[k]['child']){
	        			 child[res[k]['child'][k1].id] = res[k]['child'][k1];
	        			 child_id.push(res[k]['child'][k1].id);
	        		 }
	        		 cash.cat_tree_data[res[k].id]['child_id'] = child_id;
	        		 cash.cat_tree_data[res[k].id]['child'] = child;
	        	 }
	        	 dispatch({
	        		 type : 'get_cat_tree',
		    		 data : cash.getData()
	        	 });
	        });
        }else{
	       	 dispatch({
	    		 type : 'get_cat_tree',
	    		 data : cash.getData()
	    	 });
        }
    },    
    /*获取数据 搜索 缓存数据*/
    getData : (nowPage,parmers,cat_id) => (dispatch, getState) => {
    	cash.nowPage = nowPage;
    	if(cat_id){
    		cash.table_data = [];
    		if(cash.cat_tree_data[cat_id]){
    			var cat_ids = cash.cat_tree_data[cat_id].child_id.join(',');
    			parmers = {cat_ids : cat_ids};
        		cash.child = cash.cat_tree_data[cat_id]['child'];
        		cash.now_cat_ids = cat_ids;//记录当前选择的商品分类
    		}else{
    			parmers = {cat_ids : cat_id};
    			cash.now_cat_ids = cat_id;//记录当前选择的商品分类
    		}    		
    		parmers.firstRow = 0;
    	}else{
    		if(cash.now_cat_ids){
    			parmers.cat_ids = cash.now_cat_ids;
    		}
    	}
    	parmers.listRows = cash.listRows;       
    	if(!cash.table_data[nowPage]){
       	    cash.table_data_state = true;
	        $.post('/index.php/GoodsCheck/checkList',parmers,function(res){	        	 
	        	 cash.totalRows = res.total;
	        	 cash.table_data[nowPage] = cash.goodsDataInit(res.data);
	        	 dispatch({
	        		 type : 'get_goods_data',
	        		 data : cash.getData()
	        	 });
	        });
        }else{
	       	 dispatch({
	    		 type : 'get_goods_data',
	    		 data : cash.getData()
	    	 });
        }
    },
    beginPickApple: () => ({
        type: 'apple/BEGIN_PICK_APPLE'
    }),
};

export default actions;


