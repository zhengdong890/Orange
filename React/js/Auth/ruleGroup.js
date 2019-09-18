class CashClass{
	constructor() {
        this.tableData   = [];
        this.table_data_state = false;

        this.rules       = [];
        this.rules_state = false; 
        
        this.fixed_add_data = {
            title  : '',
            status : 1
        };
        this.fixed_add_show = false;
	    
        this.fixed_edit_data = {};
        this.fixed_edit_show = false;
        
        this.rules_check = [];
        
        this.nowPage   = 1;
        this.totalRows = 0;                   
        this.listRows  = 12;
        this.rollPage  = 10;
        
        this.change_rule_state = false,        
        this.nowGroupId = '';
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
	 * 设置权限数据
	 * */		
	setRulesData(data_){
      	var data = [];
    	for(var k in data_){
    		data[data_[k].id] = data_[k]; 
    	}    	 
		this.rules = data;
	}
	
	/*
	 * 设置分组数据
	 * */		
	setGroupData(data_){
      	 var data = [];
    	 for(var k in data_){
    		 data_[k].rules = data_[k].rules?data_[k].rules.split(','):[];
    		 data[data_[k].id] = data_[k]; 
    	 }
		this.tableData[this.nowPage] = data;
	}
	
	getData(){
		return{
			tableData   : this.tableData[this.nowPage],
			table_data_state : this.table_data_state,			
		    
		    fixed_add_data : this.fixed_add_data,
		    fixed_add_show : this.fixed_add_show,
		    
		    fixed_edit_data : this.fixed_edit_data,
		    fixed_edit_show : this.fixed_edit_show,
		    
		    rules       : this.rules,
		    rules_state : this.rules_state,
		    
	   		data_page   : {
	   		     totalRows : this.totalRows,
	   		     nowPage   : this.nowPage,
	   		     listRows  : this.listRows,
	   		     rollPage  : this.rollPage
	   		},
	   		
		    rules_check : this.rules_check,		    
		    change_rule_state : this.change_rule_state,	   	    
	    } 
	}
}
var cash = new CashClass();
export default cash;