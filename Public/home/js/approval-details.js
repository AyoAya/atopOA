/**
 * Created by Fulwin on 2017/1/11.
 */
$(function(){

    //初始化审批模态框
    $('#approval-modal').modal({
        backdrop : false,
        show : false
    });

    //点击切换状态
    $('.state-select li').click(function(){
        var state = $(this).attr('state');
        var text = $(this).find('a').text();
        $('.state').val(state);
        $('.state-btn-text').text(text);
    });

    $('#done').click(function(){
        $('#loading').css('display','block');
        $.ajax({
            url : ThinkPHP['AJAX'] + '/Approval/addApprovalLog',
            type : 'POST',
            data : {
                state : $('.state').val(),
                belongs_to : $('.joint').val(),
                comment : $('.textarea-edit').val(),
                type : $('.type').val()
            },
            dataType : 'json',
            success : function(response){
                if( response > 0 ){
                    location.replace(location.href);
                }
            }
        });
    });

});