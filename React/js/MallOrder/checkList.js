class CashClass{
	constructor() {
        this.tableData        = [];
        this.order_data       = [];
        this.order_check_data = {status:0,'content':''};

        this.table_data_state = false; 
        
        this.order_data_show  = false;       
        this.order_check_show = false;
        
        this.nowPage     = 1;
        this.totalRows   = 0;                   
        this.list_row = 12;
        this.rollPage = 10;       
        this.old_search = {
            'order_sn'  : '',
            'status'    : '',
            'send_status' : '',
            'start_time' : '',
            'end_time' : ''
        },
        this.search = {
            'order_sn'  : '',
            'status'    : '',
            'send_status' : '',
            'start_time' : '',
            'end_time' : ''
        };
        
        this.order_id = '';
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
	
	/*
	 * 设置订单数据
	 * */		
	setData(data){
		data = this.arrayCloumn(data , 'id');
		this.tableData[this.nowPage] = data;		
	}
	
	/*
	 * 设置订单详细数据
	 * */		
	setOrderData(id , data){
		this.order_data[id] = data;
		this.order_data_id = id;
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
    		this.nowPage         = nowPage;
    		parmers.order_sn     = this.old_search.order_sn;
    		parmers.status       = this.old_search.status;
    		parmers.send_status  = this.send_status;
    		parmers.start_time = this.old_search.start_status;
    		parmers.end_time   = this.old_search.end_status;
    	}
    	return parmers;
	}
	
	getData(){
		return{
		    'table_data' : this.tableData[this.nowPage] ? this.tableData[this.nowPage] : {},
		    'order_data' : this.order_data[this.order_data_id]?this.order_data[this.order_data_id]:[],
		    'order_check_data' : this.order_check_data?this.order_check_data:[],
		    
		    'table_data_state' : this.table_data_state,		
		    		
		    'order_data_show'  : this.order_data_show,	    
		    'order_check_show' : this.order_check_show,
		    
	   		'data_page'  : {
	   		     totalRows : this.totalRows,
	   		     nowPage   : this.nowPage,
	   		     listRows  : this.list_row,
	   		     rollPage  : this.rollPage
	   		},	   		
	   		'search' : this.search, 
	   		
	    } 
	}
}
var cash = new CashClass();
export default cash;