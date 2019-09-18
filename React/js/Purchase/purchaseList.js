class CashClass{
	constructor() {
        this.tableData      = [];
        this.fixedAddData   = {
            type : 0
        };
        this.fixedEditData  = {};     
        this.fixedDetailsData  = {};     
        
        this.tableDataState   = false;
        this.fixedAddShow     = false;	           
        this.fixedEditShow    = false;
        this.fixedDetailsShow = false;
               
        this.nowPage   = 1;
        this.totalRows = '';                   
        this.listRows  = 12;
        this.rollPage  = 10;        
    }	
	
	getConfig(){
	    return {
	    	tableData        : this.tableData, //设备采购数据
	    	fixedAddData     : this.fixedAddData,//添加设备采购数据
	    	fixedEditData    : this.fixedEditData,//修改设备采购数据
	    	fixedDetailsData : this.fixedDetailsData,//设备采购详情
	    	
	    	tableDataState    : this.tableDataState,
	    	fixedAddShow      : this.fixedAddShow,
	    	fixedEditShow     : this.fixedEditShow,
	    	fixedDetailsShow  : this.fixedDetailsShow,
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
	 * 设置设备采购数据
	 * */		
	setPurchaseData(data_){
		this.tableData[this.nowPage] = this.arrayCloumn(data_ , 'id');
	}
	
	getData(){
		return{				
	    	tableData        : this.tableData[this.nowPage], //设备采购数据
	    	fixedAddData     : this.fixedAddData,//添加设备采购数据
	    	fixedEditData    : this.fixedEditData,//修改设备采购数据
	    	fixedDetailsData : this.fixedDetailsData,//设备采购详情
	    	
	    	tableDataState : this.tableDataState,
	    	fixedAddShow   : this.fixedAddShow,
	    	fixedEditShow  : this.fixedEditShow,
	    	fixedDetailsShow  : this.fixedDetailsShow,
	    	
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