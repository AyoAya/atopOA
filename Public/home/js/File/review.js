/**
 * Created by Fulwin on 2017/6/21.
 */
$(function(){

    //实例化email推送
    $('.email-block-wrapper').emailBlock();

    $('.add-approval').click(function(){
        var approval_dom = '';
        $('.approval-wrapper').append(approval_dom);
    });

    if( $('.approval-wrapper') ){
        var ApprovalSlideDownBox = $('.approval-wrapper').html();
    }




    layui.use(['form','layer','upload'], function(){
        var form = layui.form(),
            layer = layui.layer;

        var step_num = 1;

        // 添加评审人
        $('.layui-form').on('click', '.choice-approval-user', function(){
            layer.open({
                type: 1,
                title: '选择评审人',
                area: ['1100px'], //宽高
                content: layerApprovalContext
            });
        });

       $(document).on('click', '.user-item', function(){
           if( $(this).hasClass('active') ){
                $(this).removeClass('active');
           }else{
               $(this).addClass('active');
           }
       });

       // 移除一列
        $('.approval-wrapper').on('click','.close-review',function(){
            let _parent = $(this).parents('.email-push-selector');
            let children_length = $('.approval-wrapper').children().length;
            //如果页面只有一栏则不允许用户再删除并提示
            if( children_length > 1 ){
                step_num--;
                _parent.remove();
                //为防止用户删除时导致的序号错误，当用户删除任何一栏时重新进行排序
                $('.approval-wrapper .email-push-selector').each(function(index){
                    $(this).find('.num').text( index + 1 );
                });
            }else{
                layer.msg('至少保留一栏', {icon: 2, time: 2000});
            }
        });


        // 添加一列
       $('.add-approval').click(function(){
           $('.approval-wrapper').append(ApprovalSlideDownBox);
           $('.approval-wrapper .layui-form-item').each(function(index){
                $(this).find('.num').text(index+1);
           });
           $('.email-block-wrapper').emailBlock();
            form.render();
       });

        /*layer.open({
            type: 1,
            skin: 'layui-layer-rim', //加上边框
            area: ['420px', '240px'], //宽高
            content: 'html内容'
        });*/

        var SUB_NAME, NUM_ID,FILE_NO,VERSION,attachmentList = new Array();

        // uploader参数配置
        var logUploaderOption = {
            auto: false,
            server: ThinkPHP['AJAX'] + '/File/uploadAttachment',
            pick: '#filePick',
            fileVal : 'Filedata',
            accept: {
                title: 'file',
                extensions: 'zip,rar,doc,docx,xls,xlsx,pdf'
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
            }else{
                layer.closeAll();
                layer.msg(response.msg, { icon : 2,time : 2000 });
            }

        });
        // 所有文件上传结束时
        APRuploader.on('uploadFinished', function( data ){

            var sub_name = SUB_NAME,
                numId = NUM_ID,
                version = VERSION,
                attachments = JSON.stringify(attachmentList);

            //console.log(SUB_NAME);

            $.ajax({
                url: ThinkPHP['AJAX'] + '/File/saveDetailAttachment',
                dataType: 'json',
                type: 'POST',
                data: {
                    attachments : attachments,
                    id : SUB_NAME,
                    num : NUM_ID,
                    version : VERSION
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
                            location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/File/fileDetail/no/'+FILE_NO+'/version/'+VERSION;
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

            if($('.file-no-box input').val() == null){
                layer.msg('请选择编号，没有请申请！',{time:2000});
                return false;
            }

            if(!$('.uploader-attachment-queue').html()){
                layer.msg('请上传附件！',{time:2000});
                return false;
            }

            var AllUserItem = new Array();

            //获取多级评审评审人数据
            $('.layui-form .email-push-selector').each(function(index){
                var userItemsArr = new Array();
                var emailBlockRecipient = $(this).find('.email-block-recipient');
                emailBlockRecipient.find('.email-block-user-item-span').each(function(){
                    userItemsArr.push({
                        userId: $(this).attr('user-id'),
                        userEmail: $(this).attr('user-email')
                    });
                });
                AllUserItem.push(JSON.stringify(userItemsArr));
            });

            var num = $('.file_num').find("option:selected").attr('num');

            var file_no = $('.file-no-box').find('input').val();

            data.field.allUserItem = AllUserItem;
            data.field.num = num;
            data.field.file_text = file_no;

            $.ajax({
                url : ThinkPHP['AJAX'] + '/File/review',
                dataType : 'json',
                type : 'POST',
                data : {
                    data : data.field,
                    num : num,
                    file_no : file_no
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
                            NUM_ID = response.num;
                            FILE_NO = response.file_no;
                            VERSION = response.version;
                            APRuploader.option('formData', {SUB_NAME: SUB_NAME,NUM_ID: NUM_ID,FILE_NO:FILE_NO,VERSION:VERSION});
                            APRuploader.upload();
                        }else{
                            layer.msg(response.msg, {icon: 1, time: 1500});
                            setTimeout(function () {
                                location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/File/fileDetail/no/'+response.file_no+'/version/'+response.version;
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