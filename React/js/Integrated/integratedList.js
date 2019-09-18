class CashClass{
	constructor() {
        this.tableData      = [];
        this.fixedAddData   = {
            is_use : 1,
            type : [0,0,0,0],
            jinrong : 1
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
	    	tableData        : this.tableData, //集成项目数据
	    	fixedAddData     : this.fixedAddData,//添加集成项目数据
	    	fixedEditData    : this.fixedEditData,//修改集成项目数据
	    	fixedDetailsData : this.fixedDetailsData,//集成项目详情
	    	
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
	 * 设置分组数据
	 * */		
	setIntegratedData(data_){
		var data = this.arrayCloumn(data_ , 'id');
		data.map(function(v , index){
			v.type = v.type.split(',');	
		})
		this.tableData[this.nowPage] = data;
	}
	
	getData(){
		return{				
	    	tableData        : this.tableData[this.nowPage], //集成项目数据
	    	fixedAddData     : this.fixedAddData,//添加集成项目数据
	    	fixedEditData    : this.fixedEditData,//修改集成项目数据
	    	fixedDetailsData : this.fixedDetailsData,//集成项目详情
	    	
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