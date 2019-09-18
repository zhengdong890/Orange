class CashClass{
	constructor() {
        this.tableData   = {};
        this.check_data  = [];
        this.check_id    = '';
        this.category    = '';
        
        this.check_data_show = false;
        this.category_state = false;
        this.goods_state = false;   
        
        this.nowPage     = 1;
        this.list_row = 10,
        this.rollPage = 10,
        this.totalRows   = 0;
        this.old_search = {
            'keyword'  : '',
            'cat_id'   : 0
        },
        this.sort = {},//排序
        this.search = {
            'keyword'  : '',
            'cat_id'   : 0
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
	 * 设置商品数据
	 * */		
	setGoodsData(data){
		data = this.arrayCloumn(data , 'id');
		this.tableData[this.nowPage] = data;
		this.sort = {};
	}
	
	/*
	 * 获取请求数据需要的参数
	 * */	
	getParamers(nowPage , parmers){
    	if(!nowPage){
    		parmers = {
    		    keyword   : this.search.keyword,	
    		    cat_id    : this.search.cat_id,
    		    firstRows : 0,
    		    listRows  : 10
    		};
    		this.tableData = {};
    		this.old_search = {
            	'keyword' : this.search.keyword,
                'cat_id'  : this.search.cat_id
            };
    		this.nowPage = 1;
    	}else{
    		this.nowPage = nowPage;
    		parmers.keyword  = this.old_search.keyword;
    		parmers.cat_id   = this.old_search.cat_id;
    	}
    	return parmers;
	}
	
	/*
	 * 获取页面需要的所有数据
	 * */
	getData(){
		return{
		    'goods_data' : this.tableData[this.nowPage] ? this.tableData[this.nowPage] : {},
		    'check_data' : this.check_data[this.check_id],
		    'category'    : this.category,
		    
	 		'category_state' : this.category_state,
	   		'goods_state' : this.goods_state,
		    'check_data_show' : this.check_data_show,
		    	
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