/**
 * Created by GCX on 2017/6/12.
 */
$(function(){

    $('.firm-li').click(function () {
        $(this).addClass('active');
        $('.ate-li').removeClass('active');
    });

    $('.ate-li').click(function () {
        $(this).addClass('active');
        $('.firm-li').removeClass('active');
    });

    return false;

})