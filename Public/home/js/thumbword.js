/**
 * Created by Fulwin on 2017/2/7.
 */
$(function(){

    //提交缩略词添加
    $('#submit-btn').click(function(){
        if( $.trim($('#acronym').val())=='' ){
            $('#acronym').focus();
            alertMsg('请输入简称');
            return false;
        }
        if( $.trim($('#full_name').val())=='' ){
            $('#full_name').focus();
            alertMsg('请输入全称');
            return false;
        }
        if( $.trim($('#meaning').val())=='' ){
            $('#meaning').focus();
            alertMsg('请输入意义');
            return false;
        }
        $.ajax({
            url : ThinkPHP['AJAX'] + '/Acronym/add',
            type : 'POST',
            data : {
                acronym : $('#acronym').val(),
                full_name : $('#full_name').val(),
                meaning : $('#meaning').val(),
                ref_url : $('#ref_url').val(),
                introduction : $('#introduction').val()
            },
            dataType : 'json',
            success : function(response){
                alert(response);
                location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/Acronym/details/id/' + response;
            }
        });
    });

    //封装消息提示插件
    function alertMsg(msg){
        $().toastmessage('showToast', {
            text: msg,
            sticky: false,
            position: 'top-right',
            type: 'warning',
            inEffectDuration: 600,
            stayTime: 3000,
        });
    }

});