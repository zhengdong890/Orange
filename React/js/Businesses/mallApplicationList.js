class CashClass{
	constructor() {
        this.tableData   = {};
        this.table_data_state = false;
        this.shop_data = [];
        this.seller_id = '';
        this.check_data = [];
        this.check_show = false;
        
        this.nowPage     = 1;
        this.totalRows   = 0;                   
        this.listRows = 12;
        this.rollPage = 10;
        
        this.old_search = {
            'name'  : ''
        },
        this.search = {
            'name'  : ''
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
	 * 时间戳转日期
	 * */	
	formatDate(time){     
		var now    = new Date(time);  
        var year   = now.getFullYear();  
        var month  = now.getMonth()+1;     
        var date   = now.getDate();     
        var hour   = now.getHours();     
        var minute = now.getMinutes();
        var second = now.getSeconds(); 
        month  = month < 10 ? '0' + month : month;
        date   = date < 10 ? '0' + date : date;
        hour   = hour < 10 ? hour + '0' : hour;
        minute = minute < 10 ? '0' + minute : minute;
        second = second < 10 ? '0' + second : second;
        return   year + "-" + month + "-" + date + "   " + hour + ":" + minute + ":" + second;     
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
	 * 设置企业认证申请列表数据
	 * */		
	setData(data){
		data = this.arrayCloumn(data , 'id');
		var _this = this;
		data.map(function(k , index){
		    k['time']       = k['time'] ? _this.formatDate(parseInt(k['time']) * 1000) : '';
		    k['check_time'] = k['check_time'] ? _this.formatDate(parseInt(k['check_time']) * 1000) : '';
		});
		this.tableData[this.nowPage] = data;
	}
		
	/*
	 * 获取请求数据需要的参数
	 * */	
	getParamers(nowPage , parmers){
    	if(!nowPage){
    		parmers = {
    			name : this.search.name,	
    		    firstRows : 0,
    		    listRows  : 10
    		};
    		this.tableData = {};
    		this.old_search = {
            	'name' : this.search.name
            };
    		this.nowPage = 1;
    	}else{
    		this.nowPage = nowPage;
    		parmers.name  = this.old_search.name;
    	}
    	return parmers;
	}	
	
	getData(){
		return{
		    'table_data' : this.tableData[this.nowPage] ? this.tableData[this.nowPage] : {},
		    'table_data_state' : this.table_data_state,
		    'shop_data' : this.shop_data[this.seller_id],
		    'check_data' : this.check_data,	
		    'check_show' : this.check_show,
		    
	   		'data_page'  : {
	   		     totalRows : this.totalRows,
	   		     nowPage   : this.nowPage,
	   		     listRows  : this.listRows,
	   		     rollPage  : this.rollPage
	   		},
	   		'search' : this.search
	    } 
	}
}
var cash = new CashClass();
export default cash;