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

        var SUB_NAME,LOG_ID;

        var attachmentData = new Array();	//定义存放附件的对象

        //var filter_content =$('#Modal-box').html();
        $('#Modal-box').on('shown.bs.modal', function(e) {
            // 定义webuploader配置
            var webuploader_option = {
                auto: true,
                server: ThinkPHP['AJAX'] + '/Software/upload',
                pick: '#picker',
                fileVal: 'Filedata',
                accept: {
                    title: 'file',
                    extensions: 'zip,rar,jpg,png,jpeg,doc,xls,xlsx,docx,gif,pdf'
                },
                method: 'POST',
            };
            // 实例化webuploader
            window.uploader = WebUploader.create(webuploader_option);

            // 添加到队列时
            uploader.on('fileQueued', function( file ){
                var fileItem = '<div class="file-item" id="'+ file.id +'">' +
                    '<div class="pull-left"><i class="file-icon file-icon-ext-'+ file.ext +'"></i> '+ file.name +'</div>' +
                    '<div class="pull-right"><i class="icon-remove" title="移除该文件"></i></div>' +
                    '<div class="clearfix"></div>' +
                    '</div>';
                $(webuploader_option.pick).next().append(fileItem);
            });
            // 上传之前
            window.uploader.on('uploadBeforeSend', function( block, data, headers ){
                data.SUB_NAME = $('#subName').val();
                data.PATH = '/Software/';
            });

            // 上传错误时
            window.uploader.on('error', function( code ){
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
            window.uploader.on('uploadSuccess', function( file,response ){
                if( response.flag > 0 ){
                    var attachmentObject = new Object();
                    attachmentObject.savename = response.savename;
                    attachmentObject.ext = response.ext;
                    attachmentObject.path = response.path;
                    attachmentData.push(attachmentObject);
                }else{
                    layer.closeAll();
                    layer.msg(response.msg, { icon : 2,time : 2000 });
                }
            });

            // 删除队列文件
            $('.uploader-file-queue').on('click', '.icon-remove', function(){
                var id = $(this).parent().parent().attr('id');
                // 删除队列中的文件
                uploader.removeFile( uploader.getFile(id,true) );
                // 删除dom节点
                $(this).parent().parent().remove();
            });

            //文件上传结束时
            form.on('submit(software)',function( data ){

                if(window.uploader.getFiles().length >0){
                     data.field.attachment = JSON.stringify(attachmentData);
                   console.log(data.field);
                }else {
                     console.log(data.field);
                }

                $.ajax({
                    url : ThinkPHP['AJAX'] + '/Software/addLog',
                    type : 'POST',
                    dataType : 'json',
                    data : {
                       addRel : data.field,
                    },
                    success : function( response ){
                        if( response.flag > 0 ){
                            layer.closeAll();
                            layer.msg(response.msg, { icon : 1,time : 2000 });
                            setTimeout(function(){
                                location.reload();
                            },2000);
                        }else{
                            layer.closeAll();
                            layer.msg(response.msg, { icon : 2,time : 2000 });
                        }
                    }
                });

                return false;
            })


        })

    });


    // 销毁uploader
    $('#Modal-box').on('hide.bs.modal', function(e){
        window.uploader.destroy();
    });














})