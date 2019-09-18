class CashClass{
	constructor() {
        this.tableData      = [];
        this.fixedEditData  = {};  
        this.fixedDetailsData  = {};     
        
        this.tableDataState = false;   
        this.fixedEditShow  = false;
        this.fixedDetailsShow  = false;
               
        this.nowPage   = 1;
        this.totalRows = '';                   
        this.listRows  = 12;
        this.rollPage  = 10;        
    }	
	
	getConfig(){
	    return {
	    	tableData      : this.tableData, //团购数据
	    	fixedDetailsData : this.fixedDetailsData,//修改团购数据
	    	fixedEditData : this.fixedEditData,//团购详情
            
	    	tableDataState : this.tableDataState,
	    	fixedEditShow : this.fixedEditShow,
	    	fixedDetailsShow : this.fixedDetailsShow,

	   		Page : {
	   		     totalRows : this.totalRows,
	   		     nowPage   : this.nowPage,
	   		     listRows  : this.listRows,
	   		     rollPage  : this.rollPage
	   		}
	    }    	
	}
	
	dataURItoBlob(base64Data) {
		var byteString;
		if(base64Data.split(',')[0].indexOf('base64') >= 0){
		    byteString = atob(base64Data.split(',')[1]);
		}else{
			byteString = unescape(base64Data.split(',')[1]);
		}		
		var mimeString = base64Data.split(',')[0].split(':')[1].split(';')[0];
		var ia = new Uint8Array(byteString.length);
		for (var i = 0; i < byteString.length; i++) {
		    ia[i] = byteString.charCodeAt(i);
		}
		return new Blob([ia], {type:mimeString});
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
	 * 设置团购商品数据
	 * */		
	setCheckData(data_){
		var data  = this.arrayCloumn(data_ , 'id');
 		var _this = this;
		data.map(function(k , index){
		    k['start_time']  = k['start_time'] ? _this.formatDate(parseInt(k['start_time']) * 1000) : '';
		    k['create_time'] = k['create_time'] ? _this.formatDate(parseInt(k['create_time']) * 1000) : '';
		    k['check_time']  = k['check_time'] ? _this.formatDate(parseInt(k['check_time']) * 1000) : '';
		});     	 
		this.tableData[this.nowPage] = data;
	}
	
	getData(){
		return{				
	    	tableData      : this.tableData[this.nowPage], //管理员数据
	    	fixedEditData : this.fixedEditData,//团购详情
	    	fixedDetailsData : this.fixedDetailsData,//修改管理员数据

	    	tableDataState : this.tableDataState,
	    	fixedEditShow : this.fixedEditShow,
	    	fixedDetailsShow : this.fixedDetailsShow,
		    
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