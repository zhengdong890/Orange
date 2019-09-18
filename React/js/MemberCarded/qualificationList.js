class CashClass{
	constructor() {
        this.tableData   = {};
        this.nowPage     = 1;
        this.totalRows   = 0;
        this.fixed_edit_data = {};
        this.table_data_state = false;
        this.list_row = 10;
        this.rollPage = 10;
        this.fixed_edit_hide = false;
        this.old_search = {
            'username'  : '',
            'is_check'  : 0,
        },
        this.sort = {},//排序
        this.search = {
            'username'  : '',
            'is_check'  : 0
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
	 * 设置审核申请数据
	 * */		
	setData(data){
		data = this.arrayCloumn(data , 'id');
		this.tableData[this.nowPage] = data;
		this.sort = {};
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
    		this.old_search =  this.search;
    		this.nowPage = 1;
    	}else{
    		this.nowPage      = nowPage;
    		parmers.username  = this.old_search.username;
    		parmers.is_check  = this.old_search.is_check;
    		parmers.status    = this.old_search.status;
    	}
    	return parmers;
	}
	
	getData(){
		return{
		    'table_data' : this.tableData[this.nowPage] ? this.tableData[this.nowPage] : {},
		    'sort'       : this.sort,
	   		'data_page'  : {
	   		     totalRows : this.totalRows,
	   		     nowPage   : this.nowPage,
	   		     listRows  : this.list_row,
	   		     rollPage  : this.rollPage
	   		},
	   		'fixed_edit_data' : this.fixed_edit_data,
	   		'fixed_edit_hide' : this.fixed_edit_hide,
	   		'search' : this.search, 
	   		'table_data_state' : this.table_data_state
	    } 
	}
}
var cash = new CashClass();
export default cash;