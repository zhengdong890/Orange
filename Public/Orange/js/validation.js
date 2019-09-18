// JavaScript Document
var validation = {
	//手机验证
	checkMobile:function(val){
		if(!(/^1[3|4|5|7|8]\d{9}$/.test(val))){ 
			return false; 
		}else{
			return true; 
		}
	},	
	//用户名验证
	checkUsername:function(val){
		if(!(/^[\u4e00-\u9fa5a-zA-Z0-9\-]{4,15}$/.test(val))){ 
			return false; 
		}else{
			return true;
		}
	},
	//密码验证
	checkPwd:function(val){
		if(!(/^[0-9A-Za-z]{6,20}$/.test(val))){
			return false; 
		}else{
			return true;
		}
	},
	//密码确认验证
	checkPwd_confirm:function(valPwd,valsPwd){
		if(!(valPwd === valsPwd)){
			return false; 
		}else{
			return true;
		}
	},
	//验证是否全部是数字且不以0开头----合法：true---不合法：false---------
	f_NumericCheck:function (as_SourceString){
		return as_SourceString.match(/^[1-9]{1}\d*$/g);
	},
	//验证是否全部是数字可以0开头----合法：true---不合法：false---------
	f_NumericCheckAll:function (as_SourceString){
		return as_SourceString.match(/^[0-9]{1}\d*$/g);
	}
}







































