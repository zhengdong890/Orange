class CashClass{
	constructor() {
        this.tableData   = {};   
        this.goods_model = [];
        this.category = [];
        this.sort = {};//排序
        
        this.check_all = false;
        
        this.select_model = 0;
        
        this.goods_status = 0;
        
        this.goods_model_state = false;
        this.category_state = false;
        this.goods_state = false;
                          
        this.nowPage     = 1;
        this.totalRows   = 0;                   
        this.list_row = 10;
        this.rollPage = 10; 
        
        this.old_search = {
            'keyword'  : '',
            'cat_id'   : 0, 
            'model_id' : 0,
            'status'   : '-1'
        },
        
        this.search = {
            'keyword'  : '',
            'cat_id'   : 0,
            'model_id' : 0,
            'status'   : '-1'
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
	
	/*全选 or 全不选*/
	setCheckAllStatus(){
		var data = this.tableData[this.nowPage];
		var status = this.check_all;
		data.map(function(v){
			v.check = status;
		});
	}
	
	/*获取加入推荐批量下架的数据*/
	getGoodsModelFalse(){
		var data = this.tableData[this.nowPage];
		var id   = {};
		data.map(function(v){
			if(v.check){
				id[v.id] = v.id;
			}
		});
		return id;
	}
	
	/*批量修改商品上下级值*/
	changeGoodsStatus(status){		
		var data = this.tableData[this.nowPage];
		data.map(function(v){
			v.status = status;
		});
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
	 * 数组值变为下标
	 * */		
	arrayCloumn(data_ , field){
		var data = [];
		for(var k in data_){
			data[data_[k][field]] = data_[k];
		}
		return data;
	}	
	
	setCategoryData(data){
	    this.category = this.arrayCloumn(data , 'id');	    
	}
	
	/*
	 * 设置加入推荐数据
	 * */		
	setGoodsModel(data){
		var goods_model = [];
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
		this.tableData[this.nowPage] = this.arrayCloumn(data , 'id');
	}	
	
	/*
	 * 获取请求数据需要的参数
	 * */	
	getParamers(nowPage , parmers){
    	if(!nowPage){
    		parmers = this.search;
    		parmers.firstRows = 0;
    		parmers.listRows  = 10;
    		this.tableData = {};
    		this.old_search = this.search;
    		this.nowPage = 1;
    	}else{
    		this.nowPage     = nowPage;
    		parmers.keyword  = this.old_search.keyword;
    		parmers.cat_id   = this.old_search.cat_id;
    		parmers.model_id = this.old_search.model_id;
    		parmers.status   = this.old_search.status;
    	}
    	this.sort = {};
    	return parmers;
	}
	
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
	   		check_all    : this.check_all,
	   		select_model : this.select_model,
	   		goods_status : this.goods_status,
	   		
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