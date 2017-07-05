/**
 * Created by Fulwin on 2017/6/21.
 */
$(function(){

    if($('.sel-type').val() == 'success' || $('.sel-type').val() == 'refuse'){
        $('#pass').addClass('sr-only');
    }

    layui.use(['form','layer','upload','element','layedit'], function(){
        var form = layui.form(),
            layedit = layui.layedit,
            element = layui.element(),
            layer = layui.layer;

        var layeditBuild = layedit.build('editor', {
            tool: [
                'strong' //加粗
                ,'italic' //斜体
                ,'underline' //下划线
                ,'del' //删除线

                ,'|' //分割线

                ,'left' //左对齐
                ,'center' //居中对齐
                ,'right' //右对齐

                ,'|' //分割线

                ,'link' //超链接
                ,'unlink' //清除链接
                ,'face' //表情
            ]
        });

        // 监听用户选择操作
        form.on('select(action)', function(data){

            if( data.value == 'transfer' ){
                $('#transfer').removeClass('sr-only');
                $('#pass').addClass('sr-only');
                $('#rollback').addClass('sr-only');
            }else if(data.value == 'pass'){
                $('#transfer').addClass('sr-only');
                $('#pass').removeClass('sr-only');
                $('#rollback').addClass('sr-only');
            }else if(data.value == 'rollback'){
                $('#transfer').addClass('sr-only');
                $('#pass').addClass('sr-only');
                $('#rollback').removeClass('sr-only');
            }else{
                $('#transfer').addClass('sr-only');
                $('#pass').addClass('sr-only');
                $('#rollback').addClass('sr-only');
            }

        });

        form.on('submit(submit)',function( data ){

            $.ajax({
                url : ThinkPHP['AJAX'] + '/Approval/step',
                dataType : 'json',
                type : 'POST',
                data : {
                    data : data.field,
                    id : $(this).parent().prev().val(),
                    dit : layedit.getContent(layeditBuild)
                },
                success : function( response ) {
                    if (response.flag > 0) {
                        layer.msg(response.msg, {icon: 1, time: 2000});
                        setTimeout(function () {
                            location.reload();
                        }, 2000)
                    } else {
                        layer.msg(response.msg, {icon: 2, time: 2000});
                    }

                    if (response.flag == 3) {
                        $('.editor-wrapper').css('display', 'none')
                    }
                }

            });

            return false;

        });


    });


});