class CashClass{
	constructor() {
        this.tableData   = {};
        this.nowPage     = 1;
        this.totalRows   = 0;
        this.goods_model = '';
        this.goods_model_state = false;
        this.category_state = false;
        this.goods_state = false;
        this.category = '';
        this.list_row = 10,
        this.rollPage = 10,
        this.old_search = {
            'keyword'  : '',
            'cat_id'   : 0, 
            'model_id' : 0
        },
        this.sort = {},//排序
        this.search = {
            'keyword'  : '',
            'cat_id'   : 0,
            'model_id' : 0
        };
    }	
	
	changeStatus(id , status){
		var data = [];
		for(var k in this.tableData[this.nowPage]){
			if(this.tableData[this.nowPage][k].id == id){
				this.tableData[this.nowPage][k].status = status;
				break;
			}
		}		
	}
	
	changeModelStatus(goods_id , model_id){		
   		var model = this.goods_model[model_id]['goods_ids'],
   		    l     = '',
   		    k     = this.array_search(goods_id, model);
   		if(k !== false){
   			model.splice(k, 1);
   		}else{
   			model.push(goods_id);
   		}
   		this.goods_model[model_id]['goods_ids'] = model;
	}
	
	array_search(goods_id , model){
   		for(var k in model){
   			if(model[k] == goods_id){
   				return k;
   			}
   		}
   		return false;
	}
	
	/*
	 * 判断object是否有数据
	 * */	
	objectIsEmpty(obj){
		for(var k in obj){
			return false;
		}
		return true;
	}	
	
	/*
	 * 数组值变为下标
	 * */		
	arrayCloumn(data_ , field){
		var data = [];
		for(var k in data_){
			data[data_[k][field]] = data_[k];
		}
		return data;
	}
	
	/*
	 * 设置加入推荐数据
	 * */		
	setGoodsModel(data){
		 var goods_model = {};
    	 for(var k in data){
    		 if(data[k].goods_ids){
    			 data[k].goods_ids = data[k].goods_ids.split(',');  
    		 }else{
    			 data[k].goods_ids = [];
    		 }    		 
    		 goods_model[data[k].id] = data[k];	        		 
    	 }
    	 this.goods_model = goods_model;
	}

	/*
	 * 设置商品数据
	 * */		
	setGoodsData(data){
		data = this.arrayCloumn(data , 'id');
		this.tableData[this.nowPage] = data;
		this.sort = {};
	}
	
	/*
	 * 获取请求数据需要的参数
	 * */	
	getParamers(nowPage , parmers){
    	if(!nowPage){
    		parmers = {
    		    keyword   : this.search.keyword,	
    		    cat_id    : this.search.cat_id,
    		    model_id  : this.search.model_id,
    		    firstRows : 0,
    		    listRows  : 10
    		};
    		this.tableData = {};
    		this.old_search = {
            	'keyword' : this.search.keyword,
                'cat_id'  : this.search.cat_id,
            	'model_id': this.search.model_id
            };
    		this.nowPage = 1;
    	}else{
    		this.nowPage = nowPage;
    		parmers.keyword  = this.old_search.keyword;
    		parmers.cat_id   = this.old_search.cat_id;
    		parmers.model_id = this.old_search.model_id;
    	}
    	return parmers;
	}
	
	/*
	 * 获取页面需要的所有数据
	 * */
	getData(){
		return{
		    'goods_data' : this.tableData[this.nowPage] ? this.tableData[this.nowPage] : {},
		    'sort'       : this.sort,
	   		'data_page'  : {
	   		     totalRows : this.totalRows,
	   		     nowPage   : this.nowPage,
	   		     listRows  : this.list_row,
	   		     rollPage  : this.rollPage
	   		},
	   		'search' : this.search, 
	   		'goods_model' : this.goods_model,
	   		'category'    : this.category,
	   		'category_state' : this.category_state,
	   		'goods_state' : this.goods_state,
	   	    'goods_model_state' : this.goods_model_state
	    } 
	}
}
var cash = new CashClass();
export default cash;