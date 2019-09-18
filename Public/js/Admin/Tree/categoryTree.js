/**
 * 多级分类树
 */
var class_tree = (function(){
	var old_cat_id    = '';
    var __tree_next   = {};//存储某个分类的下一级分类数据
    var __level       = {};
    var __now_select  = {};//用户当前选择的分类路径
    var __now_cat_id  = 0;
    var __now_cat_name= '顶级分类';
    var __url         = '';
    var __confirm_fun = '';//点击确认按钮的回调
    var __init_status = false;

    /**
     * html处理
     */
    var __html = {
    	createTreeContent : function(){
            var html = ''
            + '<div class="tree"></div>'
            + '<div class="handle">'
            + '    <a href="javascript:;" class="btn btn_confirm">确认</a>'
            + '    <a href="javascript:;" class="btn btn_resetting">重置</a>'
            + '    <a href="javascript:;" class="btn btn_close">取消</a>'
            + '</div>';
            return html;       
    	},
        createTreeList : function(data , level){
            var html = '<ul class="tree_list" level="' + level + '">';
            data.forEach(function(v){
                html = html
                + '<li id="' + v.id +'">'
                + '    <a href="javascript:;" class="tree_a">' + v.cat_name + '</a>'
                + '</li>';    
            });
            html = html + '</ul>';
            return html;
        }
    }
    
    /**
     * 事件处理
     */
    var event = function(){
        $(".tree").on('click' , '.tree_a' , function(){
            var id    = $(this).parent('li').attr('id'); 
            var level = $(this).parents('.tree_list').attr('level'); 
            __now_cat_id = id;
            __now_cat_name =  $(this).text();
            level = parseInt(level) + 1;
            $(this).parents('.tree_list').find('.color').removeClass('color');
            $(this).addClass('color');
            if(level > 4){
            	return;
            }
            addTreeList(id , level);
        })

        //点击确认
        $(".handle .btn_confirm").click(function(){
    		if(typeof(__confirm_fun) == 'function'){
                __confirm_fun(__now_cat_id , __now_cat_name);
    		}       		         
        })

        //点击重置
        $(".handle .btn_resetting").click(function(){
            __now_cat_id   = 0;
            __now_cat_name = '顶级分类';    
            __now_select   = {};
            __level        = {};	
            $(".tree_list:gt(0)").html('');
            $(".tree_list:eq(0)").find('.color').removeClass('color');
        })               

        //点击关闭
        $(".handle .btn_close").click(function(){
            $(".fixed_wraper").css('display','none');
        })        
    }

    /**
     * 根据level级数清除所有比他大的二级的html
     */
    function clearHtmlByLevel(level){
        $(".tree_list").eq(level+1).html('');
        $(".tree_list").eq(level+2).html('');
        delete __now_select[level+1];
        delete __now_select[level+2];
    }
 
    /**
     * 添加html
     */
    function addTreeList(id , level){
        if(__now_select[level] == id){
            return;
        }
        if(level > 1){
           __now_select[level-1] = id;
        }        
        if(typeof(__tree_next[id]) == 'object' && __tree_next[id] != 'null'){
            var html = __html.createTreeList(__tree_next[id] , level);
            if(__level[level]){
                $(".tree_list").eq(level-1).replaceWith(html);
            }else{
                $(".tree").append(html);
            }    
            clearHtmlByLevel(level-1);            
        }else{
            requestData(id , function(res){
                if(res.length == 0) return;
                __tree_next[id] = res;
                var html = __html.createTreeList(res , level);
                if(__level[level]){
                    $(".tree_list").eq(level-1).replaceWith(html);
                }else{
                    __level[level] = level;
                    $(".tree").append(html);
                }
                clearHtmlByLevel(level-1);
            });              
        }
    }

    /**
     * 请求获取数据
     */
    function requestData(id , func){
        $.post(__url , {id : id} , func);    
    }
   
    return {
    	init : function(config){
            if(__init_status === true){
                return;   
            }
            if(typeof(config.setConfirmFun) == 'function'){
                this.setConfirmFun(config.setConfirmFun);
            }
            __url = config.url ? config.url : __url;
            $(".fixed_wraper").html(__html.createTreeContent()); 
            event();
            addTreeList(0 , 1);
            __init_status = true;
    	},
    	setConfirmFun : function(func){
            __confirm_fun = func;
    	}, 
    	setUrl : function(url){
            __url = url;
    	},
    	showTree : function(){
            $(".fixed_wraper").css('display','block');   
    	},
    	hideTree : function(){
            $(".fixed_wraper").css('display','none');   
    	},
        addTreeList : addTreeList
    }
})();