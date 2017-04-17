/**
 * Created by Fulwin on 2017/1/12.
 */
$(function(){

    //ajax分页(我发起的)
    $('#initiate_page_list').on('click','a',function(){
        var pagenumber = $(this).text();
        if(!isNaN(pagenumber)){
            initiate_page(pagenumber);
        }
    });

    //ajax分页(我审批的)
    $('#approval_page_list').on('click','a',function(){
        var pagenumber = $(this).text();
        if(!isNaN(pagenumber)){
            approval_page(pagenumber);
        }
    });

});


//ajax分页(我发起的)
function initiate_page(pagenumber){
    $.ajax({
        url : ThinkPHP['AJAX'] + '/Approval/initiate_page',
        type : 'POST',
        data : {
            pagenumber : pagenumber
        },
        dataType : 'json',
        success : function(response){
            if(typeof(response)=='object'){
                var str = '';
                var page = '';
                for(var key in response){
                    if(key=='page'){
                        page += '<li><a>当前'+ pagenumber +'/'+ response[key] +'</a></li>';
                        for( var i=1; i<=response[key]; i++ ){
                            page += '<li><a>'+ i +'</a></li>';
                        }
                        $('#initiate_page_list').html(page);
                    }else{
                        str += '<div class="expense-row">' +
                            '<div class="expense-face pull-left">' +
                            '<img class="pull-left" src="'+ response[key].face +'" alt=".." width="50">' +
                            '</div>' +
                            '<div class="expense-context pull-left">' +
                            '<p class="expense-context-title"><a href="/Approval/details/id/'+ response[key].id +'">'+ response[key].expense_name +'的'+ response[key].type_name +'</a></p>' +
                            '<p><i class="'+ response[key].icon_class +'"></i>&nbsp;<span>'+ response[key].step_state +'</span>&nbsp;<span class="'+ response[key].label_class +'">'+ response[key].label_text +'</span>' +
                            '<small class="expense-context-time"><i class="icon-time"></i>&nbsp;'+ response[key].create_time +'</small>' +
                            '</p>' +
                            '</div>' +
                            '</div>';
                    }
                }
                $('#initiate').html(str);
                $('#initiate_page_list li').removeClass('active');
                $('#initiate_page_list li').eq(pagenumber).addClass('active');
            }

        }
    });
}

//ajax分页(我审批的)
function approval_page(pagenumber){
    $.ajax({
        url : ThinkPHP['AJAX'] + '/Approval/approval_page',
        type : 'POST',
        data : {
            pagenumber : pagenumber
        },
        dataType : 'json',
        success : function(response){
            if(typeof(response)=='object'){
                var str = '';
                var page = '';
                for(var key in response){
                    str += '<div class="expense-row">' +
                                '<div class="expense-face pull-left">' +
                                    '<img class="pull-left" src="'+ response[key].face +'" alt=".." width="50">' +
                                '</div>' +
                                '<div class="expense-context pull-left">' +
                                    '<p class="expense-context-title">' +
                                        '<a href="/Approval/details/id/'+ response[key].assoc +'">'+ response[key].nickname +'的'+ response[key].type_name +'</a>' +
                                    '</p>' +
                                    '<p>' +
                                        '<small class="expense-context-time"><i class="icon-time"></i>&nbsp;'+ response[key].time +'</small>' +
                                    '</p>' +
                                '</div>' +
                            '</div>'
                }
                $('#approval').html(str);
                $('#approval_page_list li').removeClass('active');
                $('#approval_page_list li').eq(pagenumber).addClass('active');
            }

        }
    });
}