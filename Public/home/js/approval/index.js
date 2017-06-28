/**
 * Created by Fulwin on 2017/6/21.
 */
$(function(){

    layui.use(['form','layer','upload','element'], function(){
        var form = layui.form(),
            element = layui.element(),
            layer = layui.layer;

        // uploader参数配置
        var logUploaderOption = {
            auto: false,
            server: ThinkPHP['AJAX'] + '/RMA/upload',
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
        // 上传之前
        APRuploader.on('uploadBeforeSend', function( block, data, headers ){
            data.SUB_NAME = SUB_NAME;
            data.LOG_ID = LOG_ID;
        });
        // 上传成功时
        APRuploader.on('uploadSuccess', function( file,response ){
            if( response.flag > 0 ){
                var attachmentObject = new Object();
                attachmentObject.SourceName = response.name;
                attachmentObject.SaveName = response.savename;
                attachmentObject.SavePath = response.path;
                attachmentList.push(attachmentObject);
            }else{
                layer.closeAll();
                layer.msg(response.msg, { icon : 2,time : 2000 });
            }
        });
        // 所有文件上传结束时
        APRuploader.on('uploadFinished', function(){
            $.ajax({
                url : ThinkPHP['AJAX'] + '/RMA/insertAttachment',
                type : 'POST',
                data : {
                    logid : LOG_ID,
                    attachments : JSON.stringify(attachmentList)
                },
                dataType : 'json',
                success : function( response ){
                    if( response.flag > 0 ){
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
        });

        // 删除队列文件
        $('.uploader-attachment-queue').on('click', '.icon-remove', function(){
            var id = $(this).parent().parent().attr('id');
            // 删除队列中的文件
            APRuploader.removeFile( APRuploader.getFile(id,true) );
            // 删除dom节点
            $(this).parent().parent().remove();
        });


        //监听选择的某个集
        form.on('select(category)',function(data){

            $('.apr-box').find('.apr-preview').find('ul').find('li').each(function(){
                $(this).remove();
            });

            $.ajax({
                url: ThinkPHP['AJAX'] + '/Approval/category',
                type: 'POST',
                dataType: 'json',
                data: {
                   id : data.value
                },
                success : function( response ){
                    var jsonObj=eval('('+response+')');
                    //console.log(jsonObj);
                    var arr = jsonObj[0].step;

                    $('.apr-box').find('small').html(jsonObj[0].aname);
                    //遍历step的数据注入模板
                    for(var i=0;i<arr.length;i++){

                        var tem = "<li aid='"+arr[i].a_id+"' sid='"+arr[i].id+"'><i class='icon-double-angle-right'></i>　Step - "+arr[i].step+"　　"+arr[i].name+"</li>";

                        $('.apr-box').find('.apr-preview').find('ul').append(tem);
                    }

                    //console.log(jsonObj[0]);
                }

            });

        });

       /* if(!Cat){

        }*/

    });




});