class CashClass{
	constructor() {
        this.tableData   = {};
        this.table_data_state = false;
        
        this.fixed_edit_data = {};        
        this.fixed_edit_hide = false;
        
        this.nowPage     = 1;
        this.listRows = 12;
        this.rollPage = 10;
        this.totalRows   = 0;
        
        this.old_search = {
            'username'  : ''
        },
        this.search = {
            'username'  : ''
        };
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
	}	
	
	/*
	 * 获取请求数据需要的参数
	 * */	
	getParamers(nowPage , parmers){
    	if(!nowPage){
    		parmers = this.search;
    		parmers.firstRows = 0;
    		parmers.listRows  = this.listRows;
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
		    'table_data' : this.tableData[this.nowPage] ? this.tableData[this.nowPage] : [],
	   		'data_page'  : {
	   		     totalRows : this.totalRows,
	   		     nowPage   : this.nowPage,
	   		     listRows  : this.listRows,
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