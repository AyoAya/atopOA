/**
 * Created by Fulwin on 2017/9/1.
 */
$(function(){

    // 筛选按钮
    $('.filter-btn').click(function(){
        let dom = $('#file-filter');
        if( $(this).find('i').attr('class') == 'icon-caret-up' ){
            $(this).find('i').attr('class', 'icon-caret-down');
        }else{
            $(this).find('i').attr('class', 'icon-caret-up');
        }
        if( dom.css('display') == 'none' ){
            dom.slideDown('fast');
        }else{
            dom.slideUp('fast');
        }
    });

});