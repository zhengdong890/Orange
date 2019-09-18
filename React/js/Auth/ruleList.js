class CashClass{
	constructor() {
		this.data   = [];
		this.rules_state = false;  		
		this.child  = [];
		
		this.fixed_edit_data = [];
		this.fixed_edit_show = false;
		
		this.fixed_add_data = {};
		this.fixed_add_show = false ;
		
		this.type = '';               
        this.cat_tree_state = false;
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
	 * 初始化添加规则数据
	 * */	
	fixedAddDataInit(){
	    this.fixed_add_data = {
	    	p_name    : '',
	        pid       : '',
	        name      : '',
	        title     : '',
	        condition : ''
	    }	
	}
	
	/*
	 * 设置修改框数据
	 * */		
	setFixedEditData(pid , id){
		if(pid != 0){
		    this.fixed_edit_data = {
		        id        : this.data[pid]['child'][id].id,
				pid       : this.data[pid]['child'][id].pid,
				name      : this.data[pid]['child'][id].name,
				title     : this.data[pid]['child'][id].title,
				condition : this.data[pid]['child'][id].condition,
				p_name    : this.data[pid]['title'],
				pid       : this.data[pid]['id']
		    };
		}else{
			this.fixed_edit_data = {
			    id        : this.data[id].id,
			    pid       : this.data[id].pid,
			    name      : this.data[id].name,
			    title     : this.data[id].title,
			    condition : this.data[id].condition,
			    p_name    : '顶级分类',
			    pid       : 0
			};
		}
	}    

	/*
	 * 设置商品数据
	 * */		
	setRulesData(data){
	   	for(var k in data){
			 this.data[data[k].id] = data[k];
			 var child = [];
			 for(var k1 in data[k]['child']){
				 child[data[k]['child'][k1].id] = data[k]['child'][k1];
			 }
			 this.data[data[k].id]['child'] = child;
		}
	}
	
	getData(){
		return{
			'data'        : this.data,
			'rules_state' : this.rules_state,
			'child'       : this.child,
			
			'fixed_edit_data' : this.fixed_edit_data,
			'fixed_edit_show' : cash.fixed_edit_show,
			
			'fixed_add_data' : this.fixed_add_data,
			'fixed_add_show' : cash.fixed_add_show,	   	  	   	    
	   	    
	   	    'cat_tree_state' : cash.cat_tree_state
	    } 
	}
}
var cash = new CashClass();
export default cash;