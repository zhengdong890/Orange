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
      this.set_event();//设置事件
      this.ajax();//第一次ajax请求
}

Page.prototype = {
   config:function(config){
         this.wraper=config.wraper[0]&&typeof(config.wraper)=='object'?config.wraper:$(config.wraper);//外部容器
         this.flag=config.flag;
         this.url=config.url;//ajax地址
         var reg=/^[1-9]+[0-9]*]*$/;//是否为数字的正则
         //分页设置
         this.rollPage   = reg.test(config.rollPage)?config.rollPage:3;// 分页栏每页显示的页数
         this.parameter=config.parameter?config.parameter:'';//附加参数
         this.totalRows //设置总记录数  
         this.listRows   = reg.test(config.listRows)?config.listRows:20; //设置每页显示行数
         this.nowPage    = reg.test(config.nowPage)?config.nowPage:1;//当前页数默认为1
         //每页显示的多少条数据 
         this.listrow_config=typeof(config.listrow_config)=="undefined"?['1','2','3']:config.listrow_config;
         this.firstRow   = this.listRows * (this.nowPage - 1);//根据当前页码计算是第几条数据
         this.callback=config.callback||'';//回调函数    
         this.data_cache = [];//得到的数据根据分页缓存
         this.cache = typeof(config.cache)!=='undefined'?config.cache:false; //是否开启缓存   
     },
     /***初始运行***/
     init:function(){
        this.getPage['two'].call(this);//获得分页内容
        this.callback_function();//回调函数
     },
     /***ajax请求***/
     ajax:function(){
       if(this.cache && typeof(this.data_cache[this.nowPage])!=='undefined'){
       	   this.data = this.data_cache[this.nowPage];
           this.init();
       }else{
	       var _this=this;
	       var parameter='';
	       var firstRow=this.firstRow;
	       var listRows=this.listRows;
	       parameter="{";
	       for(i in this.parameter){
	         if(typeof(this.parameter[i])!='undefined'){
	           parameter+=i+":'"+this.parameter[i]+"',";
	         }       
	       }
	       parameter = parameter+'firstRow'+":firstRow,"+'listRows'+":listRows";
	       parameter+="}";
	       var parameter = eval('(' + parameter + ')');
	       $.post(this.url,parameter,function(res){
			    if(res){        
			      res             = eval('(' +res + ')');
			      _this.data_cache[_this.nowPage] = res['data'];
			      _this.data      = res['data'];
			      _this.totalRows = res['totalRows'];
			      _this.init();
			    }
	       })        	
       }  
     }, 
     setParameter:function(parameter){
         this.parameter = parameter;
     },
     /***数组替换***/
     str_replace:function(find,replace,string){
       for(i=0;i<find.length;i++){
         string=string.replace(find[i],replace[i]);
       }
       return string;
     },
     /***分页的选择下拉框***/
     get_page_select:function(){
         //每页显示条数
         the_listRows="<select class='set_listRows'>";
         for(var i=0;i<this.listrow_config.length;i++){
             if(this.listrow_config[i]==this.listRows){
                 the_listRows+="<option value='"+this.listrow_config[i]+"' selected>"+this.listrow_config[i]+"</option>";
             }else{
                 the_listRows+="<option value='"+this.listrow_config[i]+"'>"+this.listrow_config[i]+"</option>";
             }                    
         }
         the_listRows+="</select>";
         return the_listRows; 
     },
     getPage:{
        one:function(){
            var page_data = {'prev':'','next':'','page':[],'nowPage':''};
            /* 计算分页信息 */
            this.totalPages = Math.ceil(this.totalRows/this.listRows); //总页数
            //当前页面大于总页面
            if(!this.totalPages&&this.nowPage>this.totalPages){
               this.nowPage = this.totalPages;//当前页面设置为总页面
            }         
            page_data.nowPage = this.nowPage;
            /* 计算分页临时变量 */
            now_cool_page      = this.rollPage/2;
            now_cool_page_ceil = Math.ceil(now_cool_page);
            //上一页
            up_row  = this.nowPage - 1;     
            page_data.prev = up_row > 0 ? up_row : '';
            //下一页
            down_row  = this.nowPage+1;            
            page_data.next = (down_row <= this.totalPages) ? down_row : '';
            var page; 
            for(i = 1; i <= now_cool_page_ceil; i++){
                if((this.nowPage - now_cool_page) <= 0 ){
                    page = i;
                }else
                if((this.nowPage + now_cool_page - 1) >= this.totalPages){
                    page = this.totalPages - this.rollPage + i;
                }else{
                    page = this.nowPage - now_cool_page_ceil + i;
                } 
                page_data.page.push(page);          
            }
            var l = page_data.page.length;
            for(var i = 1; i < now_cool_page_ceil; i++){
                if(page_data.page[l-1] < this.totalPages + l + i - this.rollPage){
                    page_data.page.push(this.totalPages - (this.rollPage - l) + i);
                }               
            }    
            if(page_data.page[l-1] < page_data.page[l]){
                page_data.page.splice(l, 0, ".");  
            } 
            this.page_data = page_data;           
        },
        two:function(){
            var page_data = {'prev':'','next':'','page':[],'nowPage':''};
            /* 计算分页信息 */
            this.totalPages = Math.ceil(this.totalRows/this.listRows); //总页数
            //当前页面大于总页面
            if(!this.totalPages&&this.nowPage>this.totalPages){
               this.nowPage = this.totalPages;//当前页面设置为总页面
            }         
            page_data.nowPage = this.nowPage;
            //上一页
            up_row  = this.nowPage - 1;     
            page_data.prev = up_row > 0 ? up_row : '';
            //下一页
            down_row  = this.nowPage+1;            
            page_data.next = (down_row <= this.totalPages) ? down_row : '';            
            var nowPage    = this.nowPage,
                totalPages = this.totalPages,
                m,
                n;
            if(totalPages <=1 ){
                page_data.page = [1];
                this.page_data = page_data;
                return;
            }    
            m = (nowPage * 2) / totalPages;
            if(Math.ceil(m) < 2 && Math.ceil(m) > 0){
                m = Math.ceil(m);
            }else
            if(Math.ceil(m) >= 2){
                m = Math.ceil(m) - 1;
            }else
            if(Math.ceil(m) <= 0){
                m = 1;
            }
            n = 2 - m;

            p_1 = Math.floor(nowPage / m);
            p_2 = Math.floor((totalPages - nowPage) / n);

            var arr = [1,2];
            for(var k = 1; k < m-1; k++){
                if(p_1 == 0){
                    arr.push(k * p_1 + 1);
                }else{
                    arr.push(k * p_1 , k * p_1 + 1);
                }   
            }
            if(arr[1] + 1 < nowPage - 1){
                arr.push('.');
            }
            if(nowPage < totalPages){
                arr.push(nowPage - 1 > 0 ? nowPage - 1 : 1 , nowPage , nowPage + 1);
            }
            for(var k = 1; k < n-1; k++){
                if(p_2 == 0){
                    arr.push(nowPage + k * p_2 + 1);
                }else{
                    arr.push(nowPage + k * p_2 , nowPage + k * p_2 + 1);
                }       
            }                             
            if(arr[arr.length - 1] + 1 < totalPages - 1){
                arr.push('.');
            }            
            arr.push(totalPages - 1 > 0 ? totalPages - 1 : 1 , totalPages);
            arr = array_unique(arr , '.');
            page_data.page = arr;
            this.page_data = page_data;
            function array_unique(arr , str) {
                var result = [], hash = {};
                for (var i = 0, elem; (elem = arr[i]) != null; i++) {
                    if (!hash[elem] || elem == '.') {
                        result.push(elem);
                        hash[elem] = true;
                    }
                }
                return result;
            }
        }
     },
     /***分页***/
     get_page:function(){
     	 var page_data = {'prev':'','next':'','page':[],'nowPage':''};
         /* 计算分页信息 */
         this.totalPages = Math.ceil(this.totalRows/this.listRows); //总页数
         //当前页面大于总页面
         if(!this.totalPages&&this.nowPage>this.totalPages){
           this.nowPage = this.totalPages;//当前页面设置为总页面
         }         
         page_data.nowPage = this.nowPage;
         /* 计算分页临时变量 */
         now_cool_page      = this.rollPage/2;
         now_cool_page_ceil = Math.ceil(now_cool_page);
         //上一页
         up_row  = this.nowPage - 1;
         up_page = up_row > 0 ? "<div class='left-btn change' data-p=" + up_row + "></div>" : '';        
         page_data.prev = up_row > 0 ? up_row : '';
         //下一页
         down_row  = this.nowPage+1;
         down_page = (down_row <= this.totalPages) ? "<div class='right-btn change' data-p=" + down_row + "></div>" :'';        
         page_data.next = (down_row <= this.totalPages) ? down_row : '';
         //第一页
         the_first = '';
         if(this.totalPages > this.rollPage && (this.nowPage -now_cool_page) >= 1){
             the_first = "<div class='page-btn change' data-p='1'>首页</div>";
         }
         //最后一页
         the_end = '';
         if(this.totalPages > this.rollPage && (this.nowPage + now_cool_page) < this.totalPages){
             the_end = "<div class='page-btn change' data-p=" + this.totalPages + ">末页</div>";
         }
         //统计
         the_count="";
         the_go="";
         //数字连接
         link_page = "";
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
                     link_page+= "<div class='page-btn change' data-p=" + page + ">" + page + "</div>"; 
                 }else{
                     break;
                 }
             }else         
             if(this.nowPage == page){
                 link_page+= "<div class='page-btn current' data-p=" + page + ">" + page + "</div>"; 
             }  
         }
        var page; 
        for(i = 1; i <= now_cool_page_ceil; i++){
        	if((this.nowPage - now_cool_page) <= 0 ){
                page = i;
            }else
            if((this.nowPage + now_cool_page - 1) >= this.totalPages){
                page = this.totalPages - this.rollPage + i;
            }else{
                page = this.nowPage - now_cool_page_ceil + i;
            } 
            page_data.page.push(page);         	
        }

        var l = page_data.page.length;
    	for(var i = 1; i < now_cool_page_ceil; i++){
    		if(page_data.page[l-1] < this.totalPages + l + i - this.rollPage){
    			page_data.page.push(this.totalPages - (this.rollPage - l) + i);
    		}       		
    	}    
    	if(page_data.page[l-1] < page_data.page[l]){
            page_data.page.splice(l, 0, ".");  
    	} 

        //console.log(page_data.page);
        this.page_data = page_data;
        //获取选择每页行数html         
        the_listRows='';//this.get_page_select(); 
        //替换分页内容
        this.html_page = this.str_replace(
            ['%SELECT%','%FIRST%','%UP_PAGE%','%LINK_PAGE%','%DOWN_PAGE%', '%END%','%COUNT%','%GO%'],
            [the_listRows,the_first, up_page, link_page,down_page, the_end,the_count,the_go],
            '%SELECT% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %COUNT% %GO%'
        ); 
     }, 

     /****表格设置数据****/
     callback_function:function(){  
       this.callback(this.data,this.html_page,this.page_data);    
     },
     /***各类事件响应***/
     set_event:function(){
        var wraper     = this.wraper;
        var listRows_p = this.wraper.find('.set_listRows').val();
        _this=this;
        wraper.on('click','.change',function(){
            _this.nowPage  = parseInt($(this).attr('data-p'));//当前页数    
            _this.firstRow = _this.listRows * (_this.nowPage - 1); 
            _this.ajax(); 
        })
        wraper.on('click','.go',function(){   
            _this.nowPage=parseInt(wraper.find('.go_number').val());//当前页数
            _this.firstRow=_this.listRows * (_this.nowPage - 1);               
            _this.ajax();
        })
        wraper.find('.set_listRows').change(function(){
            _this.listRows=parseInt($(this).val());           
            _this.ajax();
        })           
     }
}

e.ajaxPage=function(config){
    return new Page(config);
}
})(window);