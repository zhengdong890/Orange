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
	    	tableData      : this.tableData, //规则数据
            
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
	
	changeNewsStatus(id , status){
		var data = [];
		for(var k in this.tableData[this.nowPage]){
			if(this.tableData[this.nowPage][k].id == id){
				this.tableData[this.nowPage][k].status = status;
				break;
			}
		}		
	}
	
	/*
	 * 设置规则数据
	 * */		
	setNewsData(data_){
		var data  = this.arrayCloumn(data_ , 'id');
 		var _this = this;
		data.map(function(k , index){
		    k['update_time']  = k['update_time'] ? _this.formatDate(parseInt(k['update_time']) * 1000) : '';
		});     	 
		this.tableData[this.nowPage] = data;
	}
	
	getData(){
		return{				
	    	tableData      : this.tableData[this.nowPage], //规则数据

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