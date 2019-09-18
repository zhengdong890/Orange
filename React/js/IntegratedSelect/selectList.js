class CashClass{
	constructor() {
        this.tableData      = [];   
        
        this.tableDataState = false;   
        
        this.nowPage   = 1;
        this.totalRows = '';                   
        this.listRows  = 12;
        this.rollPage  = 10;        
    }	
	
	getConfig(){
	    return {
	    	tableData      : this.tableData, //中标集成项目数据
            
	    	tableDataState : this.tableDataState,

	   		Page : {
	   		     totalRows : this.totalRows,
	   		     nowPage   : this.nowPage,
	   		     listRows  : this.listRows,
	   		     rollPage  : this.rollPage
	   		}
	    }    	
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
	 * 设置融资租赁数据
	 * */		
	setNewsData(data_){
		var data  = this.arrayCloumn(data_ , 'id');   	 
		this.tableData[this.nowPage] = data;
	}
	
	getData(){
		return{				
	    	tableData      : this.tableData[this.nowPage], //新闻资讯数据

	    	tableDataState : this.tableDataState,
		    
	   		Page : {
	   		     totalRows : this.totalRows,
	   		     nowPage   : this.nowPage,
	   		     listRows  : this.listRows,
	   		     rollPage  : this.rollPage
	   		},   	    
	    } 
	}
}
var cash = new CashClass();
export default cash;