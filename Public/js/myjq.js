;(function($,window,document,undefined){   
  /*获取浏览器属性*/	
  $.fn.getOs = function(options){
	   var OsObject = ""; 
	   var browser=new Array();
	   if(isIE = navigator.userAgent.indexOf("MSIE")!=-1){	
		   browser['width']=$(document.body).width();
		   browser['height']=$(document.body).height();
		   browser['type']="MSIE";
		   return browser; 
	   } 
	   if(isFirefox=navigator.userAgent.indexOf("Firefox")!=-1){ 
		   browser['width']=screen.width;
		   browser['height']=screen.height;
		   browser['type']="Firefox";
		   return browser; 			   
	   } 
	   if(isChrome=navigator.userAgent.indexOf("Chrome")!=-1){ 
		   browser['width']=$(document.body).width();
		   browser['height']=$(document.body).height();
		   browser['type']="Chrome";
		   return browser; 
	   } 
	   if(isSafari=navigator.userAgent.indexOf("Safari")!=-1){ 
		   browser['width']=$(document.body).width();
		   browser['height']=$(document.body).height();
		   browser['type']="Safari";
		   return browser; 
	   }  
	   if(isOpera=navigator.userAgent.indexOf("Opera")!=-1){ 
		   browser['width']=$(document.body).width();
		   browser['height']=$(document.body).height();
		   browser['type']="Opera";
		   return browser; 
	   } 
  }
  
  /*滚动加载图片*/
  $.fn.scrollLoading = function(options){
       //创建defaults对象
	     var flag=1;
         var defaults = {
                 attr: "data-url" 
         };  
         var params = $.extend({}, defaults, options || {}); //合并
         params.cache = [];//在params对象里创建cache数组
         $(this).each(function() {
                 var node = this.nodeName.toLowerCase(), url = $(this).attr(params["attr"]); //把节点名转换成小写,获取this的data-url值
                 if (!url) { return; }
                //把获取的obj,节点名,url(data-url值)存入data对象
                 var data = {
                            obj: $(this),
                            tag: node,
                            url: url
                        };
                 params.cache.push(data);//将data对象压入cache数组里           
          });
         //动态显示数据
         var loading = function() {	 
        	 var st = $(window).scrollTop();  
             var sth = st + $(window).height();//获取当前屏幕位置
             //遍历params.cache数组
             $.each(params.cache, function(i, data){
                      var o = data.obj, tag = data.tag, url = data.url;
                      //判断obj是否存在
                      if (o){
                    	      post = o.offset().top; posb = post + o.height();//获取当前img块在网页中距离网页顶部的位置,加上自身块高度    
	                          if ((post > st && posb< sth) || (posb > st && posb < sth)) {
	                                     //在浏览器窗口内
	                                     if (tag === "img") {
	                                           //图片，改变src
	                                           o.attr("src", url); 
	                                     } else {
	                                         o.load(url);
	                                     }  
	                                     data.obj = null;    
	                          }
                      }
             });   
             return false;
       };
	   //事件触发
	   //加载完毕即执行
	   loading();
	   //滚动执行
	   $(window).bind("scroll", loading);
  };

 /* 触屏代码类start
  * options(触发滑动所需的滑动像素,结构{'x_l':'50','x_r':'50','y_t':'50','y_b':'50'}),func1(向左滑动执行的函数),func2(向右滑动执行的函数),func3(手指离开屏幕执行的事件)
  * bind(绑定监听触屏函数),move_x(x轴滑屏函数),move_y(y轴滑屏函数),click_one(单击),click_two(双击),drafting(滑屏拖拽)
  * startX(触摸时的X坐标),startY(触摸时的Y坐标),endX(手离开时X坐标),endY(手离开时的Y坐标),x(滑动的X距离),y(滑动的Y距离)
  * */
  var Touchscreen = function(This,options,func1,func2,func3){
	      this.element=This,this.startX,this.startY,this.endX,this.endY,this.move_x=0,this.move_y=0,this.flag=0;
	   	  this.func1=func1;
	   	  this.func2=func2;
	   	  this.func3=func3;
	   	  this.drafting_startX, this.drafting_startY,
	   	  defaults={'x_l':'50','x_r':'50','y_t':'50','y_b':'50'}; 
	   	  this.settings = $.extend({},defaults,options);//合并
      }
     
  Touchscreen.prototype = {
	 bind:function(type){
	         var This= this.element;
	         var This_1=this;
	         this.element.each(function(){
	               This[0].addEventListener('touchstart',touchSatrt,false); 
	               This[0].addEventListener('touchmove',touchMove,false);
	               This[0].addEventListener('touchend', touchEnd,false); 
	         });   
	         //触摸
	         function touchSatrt(e){  
	               var touch=e.touches[0];               
	               this.startY = touch.pageY;//刚触摸时的坐标  
	               this.startX = touch.pageX;//刚触摸时的坐标     
	               if(type=='drafting'){
                        This_1.drafting_startX=This.element.css('left');
                        This_1.drafting_startY=This.element.css('top');
	               } 
	         }
	         //滑动    
	         function touchMove(e){  
	    	      e.preventDefault();
	              var touch=e.touches[0];	        
	    	      this.endX = touch.pageX;
	    	      this.endY = touch.pageY;  
	    	      if(type=='drafting'){
	                  This_1.drafting();  
	              }    	    	     
	         } 
	         //手指离开屏幕      
	         function touchEnd(e){ 
			      This_1.move_x=this.endX-this.startX;
			      This_1.move_y=this.endY-this.startY;   
			      if(type=='move_x'){
			    	   This_1.Move_x(); 
			      }else 
			      if(type=='move_y'){
			    	   This_1.Move_y();  
			      }else 
			      if(type=='click_one'){
			    	   This_1.click_one();  
			      }else 
			      if(type=='click_two'){
			    	   This_1.click_two();  
			      }                   
		     }  
      },
      //水平滑动触发
      Move_x:function(){
             if(this.move_x<-this.settings.x_l){	   	
        	     this.func1();           	   
             }
	         if(this.move_x>this.settings.x_r){
		         this.func2(); 
	         }
	         this.func3();
	  },
	  //垂直滑动触发
      Move_y:function(){
             if(this.move_y<-this.settings.y_t){	   	
        	      this.func1();           	   
             }
	         if(this.move_y>this.settings.y_b){
		         this.func2(); 
	         }
             this.func3();
       },
       //单击
       click_one:function(){
             This=this;
             if(this.flag==0){
                   if((Math.abs(this.move_x)<5||isNaN(this.move_x))&&(Math.abs(this.move_y)||isNaN(this.move_y))){	   	      
	        	        setTimeout(function(){
	                          This.func1();
	        	        },400); 	   
                   }
             }
       },
       //双击
       click_two:function(){
             if(this.flag==0){
                   if((Math.abs(this.move_x)<5||isNaN(this.move_x))&&(Math.abs(this.move_y)||isNaN(this.move_y))){	   	
	        	        this.flag =1;      
	        	        setTimeout(function(){
	                         this.flag =0; 
	        	        },400); 	   
                    }
              }else{
                   this.func1();
              } 
       },
       //滑动拖拽
       drafting:function(){
             var left=this.endX-this.startX+ this.drafting_startY;
             var top=this.endY-this.startY+ this.drafting_startY;
             this.element.css({'left':left+'px','top':top+'px'});
       }
   }
   /*调用触屏方法*/ 
   $.fn.mytouchscreen= function(options,type,func1,func2,func3){ 
    	 var touchscreen = new  Touchscreen($(this),options,func1,func2,func3);
    	 touchscreen.bind(type);   	
   }   	
   /*触屏代码end*/

   /* 图片轮播类start
    * $.fn.mylunbo(调用该类的插件方法)
    * This(传过来的jquery对象),w(外部大盒子宽度),jianju(图片之间的间距),time(自动轮播的时间间隔),func1(每滑动一次执行的函数),func2(滑动到原点时执行的函数)
    * move(包住所有图片的盒子的left移动值),total_w(包住所有图片的盒子的总宽度)
    * move_left(类内函数,向左移动距离w),move_left(类内函数,向右移动距离w),autoplay_left(类内函数,自动向左轮播函数),autoplay_right(类内函数,自动向右轮播函数)
    * play_left(执行move_left函数),play_right(执行move_right函数)
    * */
    var Lunbo = function(This,w,jianju,time,func1,func2){
    	  this.element=This;
    	  this.total_w=parseInt(This.css('width'));
	   	  this.move=parseInt(This.css('left')); 
	   	  this.w=w;  	 
	   	  this.time=time;
	   	  this.jianju=jianju;
	   	  this.func1=func1;
	   	  this.func2=func2;	   	   	  
    }
   
   Lunbo.prototype = {
     //左轮播
	 move_left: function(){
	         if(Math.abs(this.move-this.w)>=this.total_w-this.jianju){
	                  this.move=0    
	                  this.element.css('left',this.move); 		                                   
	                  this.func2();
	            }else{ 
	                  this.move=  this.move-this.w;  
	                  this.element.stop().animate({'left':this.move+this.jianju},300);
	                  this.func1();   	                 
	            }   
     },
     //右轮播
     move_right: function(){
	         if(this.move>=0){
	       	         this.move=-this.total_w+this.w;    
	                 this.element.css('left',this.move+this.jianju); 	            
	                 this.func2();
	          }else{
	                 this.move= this.move+this.w;   
	                 this.element.stop().animate({'left':this.move-this.jianju},300);
	                 this.func1();   
	          }    
     },
     //自动左轮播
     autoplay_left:function(){  
             var This=this;
             var time=this.time;   
             t=setInterval(function(){
                This.move_left();
             },time);       
             return t;
     },
     //自动右轮播
     autoplay_right:function(){   
            var This=this;
            var time=this.time;     
            t=setInterval(function(){                   	       
                    This.move_right();
            },time);  
            return t;
     },
     //手动左轮播
     play_left:function(){  
            var This=this; 
            t=setTimeout(function(){  
                    This.move=parseInt(This.element.css('left'));  	 
              This.move_left();
            },350);
            return t;                       
     },
     //手动右轮播
     play_right:function(){  
            var This=this; 
            t=setTimeout(function(){    
                   This.move=parseInt(This.element.css('left'));  	 
                   This.move_right();
            },350);
            return t;     
     }
   }

  /*该类插件调用方法
     * w(外部大盒子宽度),jianju(图片之间的间距),time(自动轮播的时间间隔)
     * type轮播类型3种,1:自动播放(参数autoplay),2:手动向左滑动(参数play_left),3:手动向右滑动(参数play_right)
     * func1(每滑动一次执行的函数),func2(滑动到原点时执行的函数)
   * */
   $.fn.mylunbo=function(w,jianju,time,type,func1,func2){	
         var lunbo = new  Lunbo($(this),w,jianju,time,func1,func2);
         if(type=='autoplay_left'){
         	     return lunbo.autoplay_left();//自动向左播放
         }else
         if(type=='autoplay_right'){
         	     return lunbo.autoplay_right();//自动向右播放
         }else
         if(type=='play_left'){
         	     return lunbo.play_left();//向左滑动一次
         }else
         if(type=='play_right'){
               return lunbo.play_right();//向右滑动一次
         }
   }
  /*图片轮播类end*/

  /*瀑布流
     * n(每行排列的个数),position_w(盒子的宽度px),position_x(盒子之间的水平间距px),top(盒子之间的垂直间距px)
     * arr(存放每个盒子的高度),arr1(存放下一行每个盒子的位置,下行盒子的位置由它所在列的位置上面所有盒子高度的累加决定)
     * index(盒子的下标),h(找出盒子垂直位置上最大值,用作设置外部大盒子的高度)
     * return 返回盒子垂直位置上最大值
   * */
   $.fn.mypubu=function(n,position_w,position_x,top){	   	
        var arr=Array(),This=$(this);
        var arr1=Array(0,0,0,0,0,0,0,0,0,0);
        var h,index;      
    	/****设置第一行位置****/
        for(var i=0;i<n;i++){           
        	This.eq(i).css({'top':0,'left':position_w*i+ position_x*(i+1)+'px'});
        }      
        /****设置其他行的位置****/
        This.each(function(){
               index=$(this).index();//获取当前下标
               arr[index]=This.eq(index).css('height');//获取当前块的高度
               //如果到了行的末尾则设置下行所有块的位置
               if((index+1)%n==0){
                     //循环下一行的列
                     for(var i=0,j=n;i<n;i++,j--){           
                            arr1[i]+=parseInt(arr[index-(j-1)])+top;//盒子在垂直方向上的位置(top为累加的垂直间距)
                            This.eq(index+i+1).css({'top':arr1[i],'left':position_w*i+ position_x*(i+1)+'px'});//设置位置(垂直位置,水平位置)     
                     }                           
               }      
         }) 
         index++;
        /****获取最大高度的列的值****/         
        for(var i=0,k=index-index%n;i<=index%n-1;i++,k++){
                arr1[i]+=parseInt(arr[k])+top;       
        }
        h=Math.max.apply(null,arr1);//获取盒子垂直位置上最大值
        return h;
    }
})(jQuery,window,document);