// JavaScript Document

/* 手机获取验证码效果 */
var wb = {
	/* 手机短信计时 */
	verifyMobile:function(id){
		var $box = $(id);
		var num = 0;
		var spend = 3;
		var text_cont = [" s 后重新获取",
						"重新获取验证码",
						"请输入手机号码"
						];
		$box.bind("click",function(){
			var val_mobile=$("#mobile").val();
			if(val_mobile==""||val_mobile==null){
				alert(text_cont[2])
				return;
			}
			get_mobile_code();
			$box.removeClass("reset-butt-verify").attr("disabled","true").addClass("wait-butt-verify");
			$box.val( 0 + text_cont[0]);
			clearInterval(_time);
			wait_time();			
		});
		function wait_time(){
			_time = setInterval(function(){
				if( num >= spend ){
					$box.on("click");	
					clearInterval(_time);
					num = 0;
					$box.removeClass("wait-butt-verify").removeAttr("disabled").addClass("reset-butt-verify").val( text_cont[1]);
				}else{
					num++;
					$box.val( num + text_cont[0]);					
				}
			},1000);
		}
	},
	/* 选项卡 */
	optionsCard:function(id, card, tabId, listener,cssStyle){
		var $card = $(card);
		var $tab = $(id).find(tabId).find(".tab");
		var inx = 0;
		$card.on(listener,function(){
			if(!parseInt($(this).attr('data-val'))){
				$(this).addClass(cssStyle).attr("data-val",1).siblings().removeClass(cssStyle).attr("data-val",0);
				inx = $(this).index();
				$tab.eq(inx).show().siblings().hide();
			}

		})

	},
	//增1 减1按钮效果
	num_control:function(id){
		var $numb_bt = $(id);
		var num = 0;
		$numb_bt.each(function(){
			var $reduce_b = $(this).find(".reduce_b");
			var $cont = $(this).find(".numb-val");
			var $add_b = $(this).find(".add_b");
			$reduce_b.on("click",function(){
				var val = $(this).next().find("input").val();
				if(val <= 0 || val == "" ){ //判断为空 或者为0时候让其等于0
					num = 0;
					$(this).next().find("input").val(num);
				}else{
					num = val;
					$(this).next().find("input").val(num-1);
				}
			})
			$add_b.on("click",function(){
				var n = $(this).prev().find("input").val();
				num = n++;
				$(this).prev().find("input").val(n)
			})
		})
	},
	//键盘输入控制字数
	keyb_control:function(id,maxlength){
		var textarea = $(id);
		textarea.on("propertychange",function(){
			checktext();
		})
		textarea.on("input",function(){
			checktext();
		})
		textarea.on("change",function(){
			checktext();
		})
		textarea.on("keyup",function(){
			checktext();
		})
		$(window).on("mousedown",function(){
			checktext();
		})
		function checktext(){
			if(!textarea.length > 0) return;
			var strval = textarea.val();
			var strlen = strval.length;
			textarea.next().html(strlen+"/60");
			if(strlen > maxlength){
				//msgbox.innerHTML = "最多只能输入"+maxlength+"个字符";
				textarea.val(strval.substr(0,maxlength));
			}
		}
	},
	/*
	* 简易下拉框
	* -------------------------------
	* 参数：ID 下拉框宽度 延迟 上偏移
	* -------------------------------
	*/
	selectSimple:function(id,w,speend,ofs){
		var $select = $(id);
		$select.on("click",function(){
			var h = $(this).outerHeight(true);
			if(!parseInt($select.attr("data-type"))){
				$(this).attr("data-type",1).find(".select-op-box").css({
					"top":h-ofs,
					"width":w
				}).slideDown(speend);
			}else{
				$(this).attr("data-type",0).find(".select-op-box").slideUp(speend-100);
			}
		})
		$select.find(".select-op-box a").click(function(){
			var val = $(this).attr("data-val");
			$select.find("span").html(val);
		})
	}

}
//时间倒计时
var Countdown ={
	d_time : $("#z_timer").attr("data-time"),
	show_time:function(id,_time){
		var $_time = $("#"+id);
		var time_start = new Date().getTime(); //设定当前时间
		var time_end =  new Date(_time).getTime(); //设定目标时间
		// 计算时间差
		var time_distance = time_end - time_start;
		// 天
		var int_day = Math.floor(time_distance/86400000)
		time_distance -= int_day * 86400000;
		// 时
		var int_hour = Math.floor(time_distance/3600000)
		time_distance -= int_hour * 3600000;
		// 分
		var int_minute = Math.floor(time_distance/60000)
		time_distance -= int_minute * 60000;
		// 秒
		var int_second = Math.floor(time_distance/1000)
		// 时分秒为单数时、前面加零
		if(int_day < 10){
			int_day = "0" + int_day;
		}
		if(int_hour < 10){
			int_hour = "0" + int_hour;
		}
		if(int_minute < 10){
			int_minute = "0" + int_minute;
		}
		if(int_second < 10){
			int_second = "0" + int_second;
		};
		// 显示时间
		$_time.find(".n_days").html(int_day);
		$_time.find(".n_hours").html(int_hour);
		$_time.find(".n_minutes").html(int_minute);
		$_time.find(".n_seconds").html(int_second);
	}
}








