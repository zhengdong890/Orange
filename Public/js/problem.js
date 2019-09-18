$(document).ready(function($) {
    $(".problem .problem-nav ul .nav-title").click(function(){
        if($(this).hasClass("nav-active")){
            $(this).removeClass("nav-active");
            $(this).siblings().slideUp();
        }else{
            $(this).addClass("nav-active");
            $(this).siblings().slideToggle();
            $(this).parent().siblings().find('li').not('.nav-title').slideUp();
            $(this).parent().siblings().find('.nav-title').removeClass("nav-active");
        };
        //默认显示第一个自导航内容
        var _this=$(this).next();
        tabs(_this);
    });
    $('.nav-title').siblings().click(function () {
        $(this).addClass('active');
        $(this).siblings().removeClass('active');
        var _this=$(this);
        tabs(_this);
    });

});
function tabs(obj) {
    var _thisName='.'+obj.attr('id');
    $(_thisName).siblings().hide();
    $(_thisName).parent().siblings().children().hide();
    $(_thisName).parent().siblings().hide();
    $(_thisName).fadeIn();
    $(_thisName).parent().fadeIn();};