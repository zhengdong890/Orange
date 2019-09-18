$(document).ready(function(){
       var w=($(document.body).width());
       w=(w-1400)/2;
            if(w<0){
            	w=0;
       }
       $("#wraper").css('margin-left',w+'px');
})