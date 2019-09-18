var formValidate = function(config){
    this.rules  = config.rules;
    this.msg    = config.msg;
    this.value  = {};
}

formValidate.prototype = {
     getValue : function(){
         return this.value;
     },
     checkField:function (field,value){
        this.value[field] = value;
        for(var key in this.rules[field]){
            var res = this.methods[key].call(this,field,value);
            if(!res.status){             
                this.msg[field] = res.error;
                break;
            }else{
                this.msg[field] = '';
            }
        }
        return this.msg[field];
    },
    checkSubmitStatus:function(fields){
        for(var k in this.msg){
            if(this.msg[k] && $.inArray(k, fields) == -1){
                return false;
            }
        }
        return true;
    },
    getFirstError:function(){
       for (var k in this.msg) {
           if(this.msg[k]){
               return this.msg[k];
           }           
       }
       return '';
    },
    getChineseLength:function(str){
        var realLength = 0, len = str.length, charCode = -1;
        for (var i = 0; i < len; i++) {
            charCode = str.charCodeAt(i);
            if(charCode >= 0 && charCode <= 128){
                realLength += 1;
            }else{
                realLength += 1;
            }       
        }
        return realLength;
    },
    methods:{
        require:function(field,value){
            if(!value){
                return {'status':false,'error':this.rules[field].require};
            }
            return {'status':true};
        },
        chinese:function(field,value){
            if(!(/^[\u4e00-\u9fa5]+$/).test(value)){
                return {'status':false,'error':this.rules[field].chinese};
            }
            return {'status':true};          
        },
        length_confines:function(field,value){
            var paramer =  this.rules[field].length[0];
            length = paramer[3]? this.getChineseLength(value) : value.length;
            if(length < paramer[0] || length > paramer[1]){
                return {'status':false,'error':this.rules[field].length[1]};
            }
            return {'status':true};
        }, 
        length:function(field,value){
            var paramer =  this.rules[field].length[0];
            length = paramer[3]? this.getChineseLength(value) : value.length;
            if(length != paramer){
                return {'status':false,'error':this.rules[field].length[1]};
            }
            return {'status':true};
        }, 
        regex:function(field,value){
            var regex =  this.rules[field].regex[0];
                regex = new RegExp(regex);
            if(!regex.test(value)){
                return {'status':false,'error':this.rules[field].regex[1]};
            }
            return {'status':true};
        },
        /*验证手机号码*/
        phone_number:function(field,value){
            if(!/^1[123456789]\d{9}$/.test(value)){
                return {'status':false,'error':this.rules[field].phone_number};
            }  
            return {'status':true}; 
        },
        /*验证邮箱*/
        email:function(field,value){
            if(/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/.test(value)){
                return {'status':false,'error':this.rules[field].email};
            }
            return {'status':true}; 
        },
        /*验证是否一致*/
        equalTo:function(field,value){
            var paramer =  this.rules[field].equalTo[0];          
            if(this.value[paramer] != value && typeof(this.value[paramer]) != 'undefined'){
                return {'status':false,'error':this.rules[field].equalTo[1]};
            }
            this.msg[paramer] = '';
            return {'status':true}; 
        }
    }
}