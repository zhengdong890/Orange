/*工具js 2016.8.29 李军*/
var tool = {
        minus:function(value){
            //点击减少数量，最少为1个
            $(".tool-minus").click(function(){
                var num = parseInt($(this).next().text());
                if(num>value){
                    num--;
                    $(this).next().text(num);
                }else{
                    alert("数量不能小于"+value);
                }
            });
        },
        plus:function(value){
            //点击增加数量，最多可以限制
            $(".tool-plus").click(function(){
                var num = parseInt($(this).prev().text());
                if(num<value){
                    num++;
                    $(this).prev().text(num); 
                }else{
                    alert("数量不能大于"+value);
                }
            });
        },
        showTime:function(value){
            setInterval(function(){
                var endtime = new Date(value);/*结束时间2016/8/31,23:59:59*/
                var starttime = new Date();/*当前时间*/
                var progress = parseInt((endtime.getTime()-starttime.getTime())/1000);
                var day = parseInt(progress/60/60/24);
                var h = parseInt(progress/60/60%24);
                var m = parseInt(progress/60%60);
                var s = parseInt(progress%60);
                if(h<10){
                    h = "0"+h;
                }
                if(m<10){
                    m = "0"+m;
                }
                if(s<10){
                    s = "0"+s;
                }
                //$(".datetime").html("剩余"+day+"天"+h+":"+m+":"+s);
                $(".last-time").html("剩余"+day+"天"+h+":"+m+":"+s);
            },1000);
        }
    };