/**
 * Created by GCX on 2017/6/12.
 */
$(function(){

    $('.firm-li').click(function () {
        $(this).addClass('active');
        $('.ate-li').removeClass('active');
        $('.firm-tab').css('display','inline-block');
        $('.ate-tab').css('display','none');
    })

    $('.ate-li').click(function () {
        $(this).addClass('active');
        $('.firm-li').removeClass('active');
        $('.firm-tab').css('display', 'none');
        $('.ate-tab').css('display', 'inline-block');
    })


})