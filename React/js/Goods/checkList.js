class CashClass{
	constructor() {
        this.table_data          = []; //分页的商品数据
        this.table_data_state    = false; //获取商品的状态
        
        this.category            = ''; //商品分类                    
        this.category_state      = false; //获取商品分类的初始状态
        
        this.cat_tree_data       = []; //商品分类树
        this.cat_tree_data_state = false; //获取的商品分类树状态
        
        this.goods_edit_data     = {};//审核的商品信息
        this.goods_edit_state    = false;//弹出的审核框显示状态 默认不显示
        
        this.child  = []; //子集
        this.now_tab             = 1;//当前显示的tab
        this.edit_data           = {};
        this.goods_data          = {};//当前选中商品其他信息
        this.nowPage             = 1; //当前页
        this.totalRows           = 0; //总页数
        this.listRows            = 12;//每页显示的页数            
        this.rollPage            = 7;
        this.now_cat_ids         = '';
        this.old_search = {
            'keyword'  : '',
            'cat_id'   : 0,
        },
        this.search = {
            'keyword'  : '',
            'cat_id'   : 0
        };
        this.goods_check_data = {'check_status':'0','content':''};//审核的结果信息
    }	
	
	goodsDataInit(_data){
		var data = [];
	    for(var k in _data){
	        data[_data[k].id] = _data[k]; 	
	    }
	    return data;
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
	 * 获取用,号隔开的商品分类
	 * @param  int cat_id 商品分类id
	 * */
	getCatIds(cat_id){
		if(this.cat_tree_data[cat_id]){
			var cat_ids = this.cat_tree_data[cat_id].child_id.join(',');
			parmers = {cat_ids : cat_ids};
    		cash.child = cash.cat_tree_data[cat_id]['child'];
    		cash.now_cat_ids = cat_ids;//记录当前选择的商品分类
		}else{
			parmers = {cat_ids : cat_id};
			cash.now_cat_ids = cat_id;//记录当前选择的商品分类
		}    		
	}
	
	/*
	 * 获取商品数据附加参数
	 * */
	getParamer(){
		
	}
	
	getData(){
		return{
		    'table_data' : this.table_data[this.nowPage] ? this.table_data[this.nowPage] : {},
		    'table_data_state' : this.table_data_state,
		    'category'   : this.category,
		    'category_state' : this.category_state,
		    'cat_tree_data' : this.cat_tree_data,
		    'cat_tree_data_state' : this.cat_tree_data_state,
		    'child'       : this.child,
		    'goods_edit_data' : this.goods_edit_data,
		    'goods_edit_state' : this.goods_edit_state,
		    'edit_data'  : this.edit_data,
		    'goods_data' : this.goods_data,
	   		'data_page'  : {
	   		     totalRows : this.totalRows,
	   		     nowPage   : this.nowPage,
	   		     listRows  : this.listRows,
	   		     rollPage  : this.rollPage
	   		},
	   		'now_tab' : this.now_tab,
	   		'search' : this.search,
	   		'goods_check_data' : this.goods_check_data
	    } 
	}
}
var cash = new CashClass();
export default cash;