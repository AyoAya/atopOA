/**
 * Created by GCX on 2017/9/18.
 */
$(function(){

    $('#banner').on('click','li',function(){
        // 移除所有样式
        $('#banner li').removeClass('has-active');
        // 添加当前选中状态
        $(this).addClass('has-active');
    })



})