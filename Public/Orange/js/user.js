// JavaScript Document
var user = {
	user_validation:{
		v_cont:["请输入正确的手机号码",
				"号码已重复",
				"验证码错误",
				"用户名输入有误",
				"不能为空",
				"密码长度为6-20位数字和英文组成",
				"两次输入的密码不一致"],
		//提交验证
		submit_v:function(url){
			$.ajax({
				type: 'POST',
				url: url,
				data: {
					mobile:$("#mobile").val(),
					mobile_code:$("#mobile_code").val(),
					username:$("#username").val(),
					password:$("#password").val(),
					repassword:$("#password_confirm").val(),
					verify_code:$("#verify_code").val()
				},
				dataType: "JSON",
				success:function(data){
					switch(data){
						case 1:
							$(".register").css('display','none');
							$(".success").css('display','block');
							break;
						case -4:
							alert('密码错误');break;
						case -13:
							alert('图形验证码不正确');break;
						case -14:
							alert('短信验证码不正确');break;
						default:
							alert('未知错误');
					}
				}
			});
		},		
		//手机初始判断
		first_v_mobile:function(id,url){
			var $box = $("#"+id);
			var $v_box = $box.parent().siblings(".prompt");			
			$box.blur(function(){
				var val = $box.val();
				if(val==""||val==null){  //为空判断	
					$v_box.html('<div class="fn-sign fn-errer marL12"><i class="errer-i marR10 fLe"></i><span class="fLe">'+user.user_validation.v_cont[4]+'</span></div>')
					return;
				}
				if(validation.checkMobile(val)){					
					$.ajax({
						type: 'POST',
						url: url,
						data: {
							mobile:val,
						},
						dataType: "JSON",
						success:function(data){
							switch(data){
								case 0:
									$v_box.html('<div class="fn-sign fn-errer marL12"><i class="errer-i marR10 fLe"></i><span class="fLe">'+user.user_validation.v_cont[1]+'</span></div>')
									break;
								case 1: //验证通过
									$box.attr("data-validation","1")
									$v_box.html('<div class="fn-sign marL12"><i class="correct-i"></i></div>')
									break;
								case 2:
									$v_box.html('<div class="fn-sign fn-errer marL12"><i class="errer-i marR10 fLe"></i><span class="fLe">'+user.user_validation.v_cont[1]+'</span></div>')
									break;
								default :
									break;
							}
						}
					});
				}else{
					$v_box.html('<div class="fn-sign fn-errer marL12"><i class="errer-i marR10 fLe"></i><span class="fLe">'+user.user_validation.v_cont[0]+'</span></div>')
				};
			})			
		},
		//手机账号
		mobile_v:function(id){
			var $box = $("#"+id);
			var val = $box.val();
			var $v_box = $box.parent().siblings(".prompt");		
			if(validation.checkMobile(val)){ //规则判断
				$box.attr("data-validation","1");
			}else{
				$v_box.html('<div class="fn-sign fn-errer marL12"><i class="errer-i marR10 fLe"></i><span class="fLe">'+user.user_validation.v_cont[0]+'</span></div>')
			}
		},
		//手机验证码
		mobile_code_v:function(id){
			
		},
		//用户名
		username_v:function(id){
			var $box = $("#"+id);
			var val = $box.val();
			var $v_box = $box.parent().siblings(".prompt");		
			if(validation.checkUsername(val)){ //规则判断
				$box.attr("data-validation","1");
			}else{
				$v_box.html('<div class="fn-sign fn-errer marL12"><i class="errer-i marR10 fLe"></i><span class="fLe">'+user.user_validation.v_cont[3]+'</span></div>')
			}
		},
		//密码
		password_v:function(id){
			var $box = $("#"+id);
			var val = $box.val();
			var $v_box = $box.parent().siblings(".prompt");		
			if(validation.checkPwd(val)){  //规则判断
				$box.attr("data-validation","1");
			}else{
				$v_box.html('<div class="fn-sign fn-errer marL12"><i class="errer-i marR10 fLe"></i><span class="fLe">'+user.user_validation.v_cont[5]+'</span></div>')
			}
		},
		//密码确认
		password_confirm:function(_PwdId,sPwdId){
			var $box = $("#"+sPwdId);			
			var valsPwd = $box.val();
			var $v_box = $box.parent().siblings(".prompt");
			if(validation.checkPwd_confirm($("#"+_PwdId).val(),valsPwd)){  //规则判断
				$box.attr("data-validation","1");
			}else{
				$v_box.html('<div class="fn-sign fn-errer marL12"><i class="errer-i marR10 fLe"></i><span class="fLe">'+user.user_validation.v_cont[6]+'</span></div>')
			}			
		},
		//图片验证码
		verify_code_v:function(id){
			
		},
		//为空判断
		empty_detection:function(id){  //全部判断
			var $box  = $(id);
			$box.find(".prompt").each(function(i){
				var val = $(this).prev().find("input[type='text'], input[type='password']").val();

				if(val==""||val==null){ 
					$(this).html('<div class="fn-sign fn-errer marL12"><i class="errer-i marR10 fLe"></i><span class="fLe">'+user.user_validation.v_cont[4]+'</span></div>')
					return;
				}
			})
		},
		//图片幻灯片效果
		focusImg:function(id,sub_id,prev,next,num,spend,showTime,auto){
			var curr = 0,
				_sub = $(sub_id);
			_sub.each(function(i){
				$(this).click(function(){
					curr = i;
					$(id).eq(i).fadeIn(showTime).siblings().fadeOut(showTime);
					$(this).siblings(".trigger").stop(true,true).removeClass("imgSelected").end().addClass("imgSelected");
					return false;
				});
			});
			var pg = function(flag){
				if (flag) {
					if (curr == 0) {
						todo = num-1;
					} else {
						todo = (curr - 1) % num;
					}
				} else {
					todo = (curr + 1) % num;
				}
				_sub.eq(todo).click();
			};
			$(prev).click(function(){
				pg(true);
				return false;
			});
			$(next).click(function(){
				pg(false);
				return false;
			});
			if(auto){
				var timer,
				_autoPlay = function() {
					timer = setInterval(function(){
						todo = (curr + 1) % num;
						_sub.eq(todo).click();
					},spend);
				}
				$(id).hover(function(){
						clearInterval(timer);
					},
					function(){
						_autoPlay();
					}
				);
				_autoPlay();
			}
		}
	}
}














