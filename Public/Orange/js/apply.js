$(function () {
   $('.apply-box').on('click',function () {
       $(this).parent().hide();
       $(this).parent().next().show();
    });
});
