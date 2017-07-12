/**
 * Created by Fulwin on 2017/6/21.
 */
$(function(){

    //实例化email推送
    $('.email-block-wrapper').emailBlock();


    layui.use(['form','layer','upload'], function(){
        var form = layui.form(),
            layer = layui.layer;

        var SUB_NAME, attachmentList = new Array();

        // uploader参数配置
        var logUploaderOption = {
            auto: false,
            server: ThinkPHP['AJAX'] + '/File/uploadAttachment',
            pick: '#filePick',
            fileVal : 'Filedata',
            accept: {
                title: 'file',
                extensions: 'zip,rar,jpg,png,jpeg,gif,doc,docx,xls,xlsx,pdf'
            },
            method: 'POST',
        };
        // 实例化uploader
        APRuploader = WebUploader.create( logUploaderOption );
        // 添加到队列时
        APRuploader.on('fileQueued', function( file ){
            var fileItem = '<div class="file-item" id="'+ file.id +'">' +
                '<div class="pull-left"><i class="file-icon file-icon-ext-'+ file.ext +'"></i> '+ file.name +'</div>' +
                '<div class="pull-right"><i class="icon-remove" title="移除该文件"></i></div>' +
                '<div class="clearfix"></div>' +
                '</div>';
            $(logUploaderOption.pick).next().append(fileItem);
        });
        // 上传错误时
        APRuploader.on('error', function( code ){
            var msg = '';
            switch(code){
                case 'Q_EXCEED_NUM_LIMIT':
                    msg = '只能上传一个文件';
                    break;
                case 'Q_EXCEED_SIZE_LIMIT':
                    msg = '文件大小超出限制';
                    break;
                case 'Q_TYPE_DENIED':
                    msg = '文件格式不允许';
                    break;
                case 'F_DUPLICATE':
                    msg = '文件已存在';
                    break;
                default:
                    msg = code;
            }
            layer.msg(msg, {icon: 2, time: 2000});
        });
        // 上传成功时
        APRuploader.on('uploadSuccess', function( file,response ){

            if( response.flag > 0 ){
                var attachmentObject = new Object();
                attachmentObject.ext = response.ext;
                attachmentObject.savename = response.savename;
                attachmentObject.path = response.path;
                attachmentList.push(attachmentObject);
                // console.log(attachmentList);
            }else{
                layer.closeAll();
                layer.msg(response.msg, { icon : 2,time : 2000 });
            }

        });
        // 所有文件上传结束时
        APRuploader.on('uploadFinished', function( data ){

            var sub_name = SUB_NAME,
                attachments = JSON.stringify(attachmentList);

            $.ajax({
                url: ThinkPHP['AJAX'] + '/File/saveDetailAttachment',
                dataType: 'json',
                type: 'POST',
                data: {
                    attachments : attachments,
                    id : SUB_NAME
                },
                beforeSend: function(){
                    layer.load(1, {
                        shade: [0.5,'#fff'] //0.1透明度的白色背景
                    });
                },
                success : function( response ){
                    if(response.flag > 0){
                        layer.msg(response.msg, {icon: 1, time: 2000});
                        setTimeout(function () {
                            location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/File/review';
                        })
                    }else{
                        layer.msg(response.msg, {icon: 2, time: 2000});
                        setTimeout(function () {
                            location.replace(location.href);
                        })
                    }

                }
            })


        });


        // 删除队列文件
        $('.uploader-attachment-queue').on('click', '.icon-remove', function(){
            var id = $(this).parent().parent().attr('id');
            // 删除队列中的文件
            APRuploader.removeFile( id, true );
            // 删除dom节点
            $(this).parent().parent().remove();
        });


        form.on('submit(submit)',function( data ){

            var ccs = [];

            if( $('.email-block-cc').children().length ){
                $('.email-block-cc .email-block-user-item-span').each(function (index) {
                    let tmpObj = new Object();
                    tmpObj.name = $(this).text();
                    tmpObj.id = $(this).attr('user-id');
                    tmpObj.email = $(this).attr('user-email');
                    ccs.push(tmpObj);
                })
            }

            var emailObj = JSON.stringify(ccs);



            $.ajax({
                url : ThinkPHP['AJAX'] + '/File/review',
                dataType : 'json',
                type : 'POST',
                data : {
                    data : data.field,
                    cc : emailObj
                },
                beforeSend: function(){
                    layer.load(1, {
                        shade: [0.5,'#fff'] //0.1透明度的白色背景
                    });
                },
                success: function( response ){
                    // 根据返回的结果检查队列里是否包含文件，如果有则上传，没有则直接跳转到详情页
                    if( response.flag > 0 ){
                        if( APRuploader.getFiles().length > 0 ){ //检查队列里是否包含文件
                            SUB_NAME = response.flag;
                            APRuploader.option('formData', {SUB_NAME: SUB_NAME});
                            APRuploader.upload();
                        }else{
                            layer.msg(response.msg, {icon: 1, time: 1500});
                            setTimeout(function () {
                                location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/File/review';
                            })
                        }
                    }else{
                        location.replace(location.href);
                        layer.msg('ERROR', {icon: 2, time: 2000});
                    }
                }
            });

            return false;

        });


    });




});