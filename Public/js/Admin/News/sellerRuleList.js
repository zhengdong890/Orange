// +----------------------------------------------------------------------
// | time:2016-7-10
// +----------------------------------------------------------------------
// | email:597089187@qq.com
// +----------------------------------------------------------------------
// | Author: yanjin
// +----------------------------------------------------------------------
(function(e){
var Page = function(config){
    this.config(config);//配置    
    this.ajax();//第一次ajax请求
}

Page.prototype = {
    config:function(config){   
    	//请求地址
        this.url        = config.url;
        // 分页栏每页显示的页数
        this.rollPage   = /^[1-9]+[0-9]*]*$/.test(config.rollPage) ? config.rollPage : 3;
        // 附加请求参数
        this.paramer    = config.paramer ? config.paramer : '';
        //总记录数 
        this.totalRows;
        //第多少条开始取  
        this.firstRow;
        //设置每页显示条数 默认20条
        this.listRows   = /^[1-9]+[0-9]*]*$/.test(config.listRows) ? config.listRows : 20; 
        //当前页数默认为1
        this.nowPage    = /^[1-9]+[0-9]*]*$/.test(config.nowPage) ? config.nowPage : 1;
        //每页显示的多少条数据 
        this.callback   = config.callback||'';
        //分页数据
        this.page       = {};
    },
    /***初始运行***/
    init:function(){
    	//获得分页内容
        this.get_page();
        //回调函数
        this.callback(this.data , this.page); 
    },
    /***ajax请求***/
    ajax:function(){
        var _this     = this;
        this.firstRow = this.listRows * (this.nowPage - 1);      
        var d = {firstRow : this.firstRow , listRows : this.listRows};
        for(i in this.parameter){
            if(typeof(this.parameter[i]) != 'undefined'){
         	    d[i] = this.parameter[i];
            }       
        }
        $.post(this.url,d,function(res){
	        if(res){       
			      //res= eval('(' +res + ')');
			    _this.data      = res['data'];
			    _this.totalRows = res['total'];
			    _this.init();
	        }
        })   
    }, 
     /***分页***/
    get_page:function(){
    	var __PAGE = {
    		prev      : false,
    		first     : false,
    		page      : [],
    		next      : false,
    		end       : false,
    		now_page  : ''
    	};
        /* 计算分页信息 */
        this.totalPages = Math.ceil(this.totalRows/this.listRows); //总页数
        //当前页面大于总页面
        if(!this.totalPages && this.nowPage > this.totalPages){
           this.nowPage = this.totalPages;//当前页面设置为总页面
        } 
        /* 计算分页临时变量 */
        now_cool_page      = this.rollPage / 2;
        now_cool_page_ceil = Math.ceil(now_cool_page);
        //上一页
        up_row  = this.nowPage - 1;  
        __PAGE.prev = up_row > 0 ? up_row : false;
        //下一页
        down_row  = this.nowPage+1;  
        __PAGE.next = (down_row <= this.totalPages) > 0 ? down_row : false;    
        //数字连接
        for(i = 1; i <= this.rollPage;i++){
            /*计算出页数*/
            if((this.nowPage - now_cool_page) <= 0 ){
                page = i;
            }else
            if((this.nowPage + now_cool_page - 1) >= this.totalPages){
                page = this.totalPages - this.rollPage + i;
            }else{
                page = this.nowPage - now_cool_page_ceil + i;
            }  
            //页数大于0并且页数不等于当前页面         
            if(page > 0 && page != this.nowPage){
                //当前页数小于或者等于总页数才让其显示
                if(page <= this.totalPages){
                 	__PAGE.page.push(page);                           
                }else{
                    break;
                }
            }else         
            if(this.nowPage==page){
             	__PAGE.page.push(page);
             	__PAGE.now_page = page;
            } 
        }
        __PAGE.totalPages = this.totalPages;
        __PAGE.totalRows  = this.totalRows; 
        this.page = __PAGE;         
    }, 
}

e.ajaxPage = function(config){
    return new Page(config);
}
})(window);


(function(e){
	var update_url = 'http://houtai.orangesha.com/News/sellerRuleUpdate?id=';
	var status_url = 'http://houtai.orangesha.com/News/newsStateChange';
	var delete_url = 'http://houtai.orangesha.com/News/newsDelete';
	var listRows_option = createListRowsOption([10 , 20 , 40]);
	var config   = {
	    listRows  : 10, 
	    rollPage  : 3,
	    url       : "http://houtai.orangesha.com/News/sellerRuleList",
	    callback:function(data,page){            
	        showHtml(data);
	        showPage(page);    
		}
	};
	var PageObj = ajaxPage(config);

	$(".page ul").on('click' , '.page_a' , function(){
	    var p = $(this).attr('data-p');
	    PageObj.nowPage = parseInt(p);
	    PageObj.ajax();    
	})     

	$(".page ul").on('click' , '.jump' , function(){
	    var p = $("input[name='jump_page']").val();
	    PageObj.nowPage = parseInt(p);
	    PageObj.ajax();    
	}) 

	$(".page ul").on('change' , '.set_listRows' , function(){
	    var listRows = $(this).val();
	    PageObj.nowPage  = 1;
	    PageObj.listRows = listRows;
	    PageObj.ajax();    
	}) 

	$("#main table").on('click' , '.change_status' , function(){
	    var status = parseInt($(this).attr('data-status'));
	    var id     = $(this).parents('tr').attr('data-id');
	    var _this  = this;
	    status = status == 1 ? 0 : 1;
	    $.post(status_url , {id : id , status : status} , function(res){
            if(res.status == '1'){
                $(_this).parents('tr').remove();
            }
	    })    
	}) 

	$("#main table").on('click' , '.delete' , function(){
	    var id     = $(this).parents('tr').attr('data-id');
	    var _this  = this;
	    $.post(delete_url , {id : id} , function(res){
            if(res.status == '1'){
                $(_this).attr('data-status' , status);                
            }
	    })    
	}) 

	/*
	 * 时间戳转日期
	 * */	
	function formatDate(time){     
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

	function showHtml(data){
		var html = '';
	    for(var k in data){
	        html = html 
	             + '<tr data-id="' + data[k].id + '">'
	             + '    <td class="td">'
	             + '        <div class="select_checkbox">'
	             + '            <input type="checkbox" class="checkbox" value="on">'
	             + '            <a>' + data[k].id + '</a>'
	             + '        </div>'
	             + '    </td>'
	             + '    <td class="table_td">' + data[k].title + '</td>'
	             + '    <td class="table_td">' + data[k].keyword + '</td>'
	             + '    <td class="table_td">' + formatDate(data[k].update_time * 1000) + '</td>'
	             + '    <td class="table_td">'
	             + '        <a href="javascript:;" class="change_status" data-status="' + data[k].status +'"">'
	             +  (
	             	        data[k].status == 1 ?  
	                            '<img src="/Public/admin_images/yes.gif">':
	                            '<img src="/Public/admin_images/no.gif">'
	                )          
	             + '        </a>'
	             + '    </td>'
	             + '    <td class="table_handle">'
	             + '        <a href="' + update_url + data[k].id + '" class="table_handle_a">编辑 </a>'
	             + '        <a href="javascript:;" class="table_handle_a delete"> 删除</a>'
	             + '    </td>'
	             + '</tr>';
	    }
		$("#main tbody").html(html);
	}
    
    function createListRowsOption(data){
    	var str = '<select class="set_listRows">';
    	data.map(function(v){
            str +='<option value="' + v + '">' + v +'</option>';
    	});
    	str +='</select>';
    	return str;
    }

	function showPage(data){
	    var page = listRows_option;
	    page += data.first !== false ? ('<a class="first" href="javascript:;">首页</a>') :
	        ('<a class="first">首页</a>');
	    page += data.prev !== false ? ('<a class="prev" href="javascript:;">上一页</a>') :
	        ('<a class="first">上一页</a>');     
	    for(var k in data.page){
	    	page += data.page[k] == data.now_page ? 
	    	    ('<a class="page_a now" data-p=' + data.page[k] + '>' + data.page[k] +'</a>') :
	    	    ('<a class="page_a" href="javascript:;" data-p=' + data.page[k] + '>' + data.page[k] +'</a>');   	
	    } 
	    page += data.next !== false ? ('<a class="first" href="javascript:;">下一页</a>') :
	        ('<a class="first">下一页</a>');
	    page += data.end !== false ? ('<a class="first" href="javascript:;">末页</a>') :
	        ('<a class="first">末页</a>');             
	    page += '<p class="total">共' + data.totalPages + '页' + data.totalRows + '条数据</p>'; 
	    page += '<input name="jump_page" value="1">';   
	    page += '<a href="javascript:;" class="jump">GO</a>';
	    $(".page ul").html(page);          
	}
})();