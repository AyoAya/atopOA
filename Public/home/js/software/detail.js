/**
 * Created by GCX on 2017/5/18.
 */
$(function(){

    // 监听滚动，如果添加处理日志存在并且出现返回顶部按钮则将添加日志按钮向上调整
    $('#content').scroll(function(){

        if( $('.solo_order') ){
            if( $('#scrollBackTop').hasClass('sr-only') ){
                $('.solo_order').css({bottom : '60px'});
            }else{
                $('.solo_order').css({bottom : '122px'});
            }
        }

    });

    layui.use(['form','layer'], function() {
        var layer = layui.layer,
            form = layui.form();

        var filter_content =$('#myModal').html();

        // 定义webuploader配置
        var webuploader_option = {
            auto: false,
            server: ThinkPHP['AJAX'] + '/Sample/upload',
            pick: '#picker',
            fileVal : 'Filedata',
            accept: {
                title: 'file',
                extensions: 'zip,rar,jpg,png,jpeg,doc,xls,xlsx,docx,pdf'
            },
            method: 'POST',
        };

        // 实例化webuploader
        var uploader = WebUploader.create(webuploader_option);


    });















})