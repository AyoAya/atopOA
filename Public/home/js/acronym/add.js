/**
 * Created by Fulwin on 2017/2/7.
 */
$(function(){

    layui.use(['form','layer'], function(){
        var form = layui.form(),
            layer = layui.layer;

        form.on('submit(acronym)', function( data ){
            //console.log(data);

            $.ajax({
                url : ThinkPHP['AJAX'] + '/Acronym/add',
                type : 'POST',
                data : data.field,
                dataType : 'json',
                success : function(response){
                    if( response.flag > 0 ){
                        layer.msg(response.msg,{icon:1,time:2000});
                        setTimeout(function(){
                            location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/Acronym/details/id/' + response.id;
                        },2000);
                    }else{
                        layer.msg(response.msg,{icon:2,time:2000});
                    }
                }
            });

            return false;
        });

    });

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