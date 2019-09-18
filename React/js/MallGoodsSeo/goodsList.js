class CashClass{
	constructor() {
        this.tableData      = [];  
        this.fixedEditData  = {};
        this.goodsSeo       = [];
        
        this.tableDataState = false; 
        this.fixedEditShow  = false;
        
        this.oldSearch = {
             'keyword'  : '',
             'cat_id'   : 0, 
             'model_id' : 0
        },
        this.search = {
            'keyword'  : '',
            'cat_id'   : 0,
            'model_id' : 0
        };
        
        this.nowPage   = 1;
        this.totalRows = '';                   
        this.listRows  = 12;
        this.rollPage  = 10;  
        this.listRowsConfig =[5,10,12,20,40,100];
    }	
	
	getConfig(){
	    return {
	    	tableData      : this.tableData, //中标集成项目数据
	    	fixedEditData  : this.fixedEditData,
	    	goodsSeo       : this.goodsSeo,
            
	    	tableDataState : this.tableDataState,
	    	fixedEditShow  : this.fixedEditShow,

	   		Page : {
	   		     totalRows : this.totalRows,
	   		     nowPage   : this.nowPage,
	   		     listRows  : this.listRows,
	   		     rollPage  : this.rollPage,
	   		     listRowsConfig : this.listRowsConfig
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
	 * 获取请求数据需要的参数
	 * */	
	getParamers(nowPage , parmers){
    	if(!nowPage){
    		parmers = {
    		    keyword   : this.search.keyword,	
    		    cat_id    : this.search.cat_id,
    		    model_id  : this.search.model_id,
    		    firstRows : 0,
    		    listRows  : 10
    		};
    		this.tableData = {};
    		this.oldSearch = {
            	'keyword' : this.search.keyword,
                'cat_id'  : this.search.cat_id,
            	'model_id': this.search.model_id
            };
    		this.nowPage = 1;
    	}else{
    		this.nowPage = nowPage;
    		parmers.keyword  = this.oldSearch.keyword;
    		parmers.cat_id   = this.oldSearch.cat_id;
    		parmers.model_id = this.oldSearch.model_id;
    	}
    	return parmers;
	}	
	
	/*
	 * 设置共享商品数据
	 * */		
	setGoodsData(data_){
		var data  = this.arrayCloumn(data_ , 'id');   	 
		this.tableData[this.nowPage] = data;
	}
	
	/*
	 * 设置共享商品seo数据
	 * */		
	setGoodsSeoData(data_){
		var data      = this.arrayCloumn(data_ , 'goods_id');   	 
		this.goodsSeo = data.reduce(function(arr , item){
			arr[item.goods_id] = item;
			return arr;
		}, this.goodsSeo)
	}	
	
	getData(){
		return{				
	    	tableData      : this.tableData[this.nowPage], //新闻资讯数据
	    	fixedEditData  : this.fixedEditData,
	    	goodsSeo       : this.goodsSeo,
	    	
	    	tableDataState : this.tableDataState,
	    	fixedEditShow  : this.fixedEditShow,
	    	
	    	search : this.search, 
	    	
	   		Page : {
	   		     totalRows : this.totalRows,
	   		     nowPage   : this.nowPage,
	   		     listRows  : this.listRows,
	   		     rollPage  : this.rollPage,
	   		     listRowsConfig : this.listRowsConfig
	   		},   	    
	    } 
	}
}
var cash = new CashClass();
export default cash;