class CashClass{
	constructor() {
        this.tableData      = [];
        this.fixedAddData   = {
            username : '',
            password : '',
            repeatpassword : '',
            name : '',
            group_id : 0,
            lock : 1
        };
        this.fixedEditData  = {
 		   id             : '',
           username       : '',
           name           : '',
           lock           : 1,
           group_id       : 0,
           password       : '',
           repeatpassword : '',
           is_user        : 0,
           is_pwd         : 0
        };     
        this.group          = [];
        
        this.tableDataState = false;
        this.fixedAddShow   = false;	           
        this.fixedEditShow  = false;
        this.groupState     = false;
               
        this.nowPage   = 1;
        this.totalRows = '';                   
        this.listRows  = 12;
        this.rollPage  = 10;        
    }	
	
	getConfig(){
	    return {
	    	tableData      : this.tableData, //管理员数据
	    	fixedAddData   : this.fixedAddData,//添加管理员数据
	    	fixedEditData  : this.fixedEditData,//修改管理员数据
	    	group          : this.group,//权限分组
	    	tableDataState : this.tableDataState,
	    	fixedAddShow   : this.fixedAddShow,
	    	fixedEditShow  : this.fixedEditShow,
	    	groupState     : this.groupState,
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
	setAdminData(data_){
      	 var data = [];
    	 for(var k in data_){
    		 data_[k].rules = data_[k].rules?data_[k].rules.split(','):[];
    		 data[data_[k].id] = data_[k]; 
    	 }
		this.tableData[this.nowPage] = data;
	}
	
	getData(){
		return{				
	    	tableData      : this.tableData[this.nowPage], //管理员数据
	    	fixedAddData   : this.fixedAddData,//添加管理员数据
	    	fixedEditData  : this.fixedEditData,//修改管理员数据
            group          : this.group,
	    	tableDataState : this.tableDataState,
	    	fixedAddShow   : this.fixedAddShow,
	    	fixedEditShow  : this.fixedEditShow,
	    	groupState     : this.groupState,
		    
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