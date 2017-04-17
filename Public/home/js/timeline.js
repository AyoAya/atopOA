/**
 * Created by Fulwin on 2017/4/6.
 */
$(function(){



    // 点击收缩/展开列表
    $('.timeline').on('click','.timeline-heading .timeline-table .timeline-title-text span',function(){
        var _parent = $(this).parents('.timeline-table');
        if( _parent.hasClass('timeline-hide') ){
            if( $(this).find('i').attr('class') == 'icon-caret-down' ){
                $(this).find('i').attr('class','icon-caret-up');
            }else{
                $(this).find('i').attr('class','icon-caret-down');
            }
            _parent.removeClass('timeline-hide');
        }else{
            if( $(this).find('i').attr('class') == 'icon-caret-down' ){
                $(this).find('i').attr('class','icon-caret-up');
            }else{
                $(this).find('i').attr('class','icon-caret-down');
            }
            _parent.addClass('timeline-hide');
        }
    });





});
