/**
 * Created by Fulwin on 2017/6/21.
 */
$(function(){

    layui.use(['form','layer','upload'], function(){
        var form = layui.form(),
            layer = layui.layer;
        var SUB_NAME,VERSION,NO,attachmentList = new Array();

        // 定义webuploader配置
        var webuploader_option = {
            auto: false,
            server: ThinkPHP['AJAX'] + '/File/uploadAttachment',
            pick: {
                id: '#picker',
                multiple: false
            },
            fileVal: 'Filedata',
            accept: {
                title: 'file',
                extensions: 'zip,rar,doc,xls,xlsx,docx,pdf'
            },
            method: 'POST'
        };

        $('.edit-file').click(function(){
            $('.info-input').css('display','block');
            $('.info-div').css('display','none');
            $(this).css('display','none');
            // 实例化webuploader
            window.uploader = WebUploader.create(webuploader_option);

            // 添加到队列时
            window.uploader.on('fileQueued', function (file) {

                var fileItem = '<div class="file-item" style="width: 100%" id="' + file.id + '" ext = "'+file.ext+'" name="'+file.name+'" path="'+file.path+'">' +
                    '<div class="pull-left"><i class="file-icon file-icon-ext-' + file.ext + '"></i> ' + file.name + '</div>' +
                    '<div class="pull-right"><i class="icon-remove" title="移除该文件"></i></div>' +
                    '<div class="clearfix"></div>' +
                    '</div>';
                $(webuploader_option.pick.id).next().append(fileItem);

            });

            // 上传之前
            window.uploader.on('uploadBeforeSend', function (block, data, headers) {
                console.log(data);
                data.PATH = '/File/';
            });

            // 上传错误时
            window.uploader.on('error', function (code) {
                var msg = '';
                switch (code) {
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

            window.uploader.on('fileDequeued', function(file){
                console.log(window.uploader.getFiles());
            });

            // 上传成功时
            window.uploader.on('uploadSuccess', function (file, response) {

                if( response.flag > 0 ){
                    var attachmentObject = new Object();
                    attachmentObject.ext = response.ext;
                    attachmentObject.savename = response.savename;
                    attachmentObject.path = response.path;
                    attachmentList.push(attachmentObject);
                }else{
                    layer.closeAll();
                    layer.msg(response.msg, {icon: 2, time: 2000});
                }

            });

            window.uploader.on('uploadFinished', function( data ) {

                var sub_name = SUB_NAME,
                    no = NO,
                    version = VERSION,
                    attachments = JSON.stringify(attachmentList);

                //console.log(SUB_NAME);

                $.ajax({
                    url: ThinkPHP['AJAX'] + '/File/saveDetailAttachment',
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        attachments: attachments,
                        id: SUB_NAME,
                        no: NO,
                        version: VERSION
                    },
                    beforeSend: function () {
                        layer.load(1, {
                            shade: [0.5, '#fff'] //0.1透明度的白色背景
                        });
                    },
                    success: function (response) {
                        if (response.flag > 0) {
                            layer.msg(response.msg, {icon: 1, time: 2000});
                            setTimeout(function () {
                                location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/File/fileDetail/no/' + NO + '/version/' + VERSION;
                            })
                        } else {
                            layer.msg(response.msg, {icon: 2, time: 2000});
                            setTimeout(function () {
                                location.replace(location.href);
                            })
                        }
                    }
                });
            });

            $('.close-info-input').click(function(){
                $('.info-input').css('display','none');
                $('.info-div').css('display','block');
                $('.edit-file').css('display','inline-block')
                //销毁
                window.uploader.destroy();
            });

        });


        // 删除队列文件
        $('.uploader-file-queue').on('click', '.icon-remove', function () {

            var id = $(this).parent().parent().attr('id');

        });
        $('.close-info-input').click(function(){
            $('.info-input').css('display','none');
            $('.info-div').css('display','block');
            $('.edit-file').css('display','inline-block')
            //销毁
            window.uploader.destroy();
        });

        var attachmentData = new Array();	//定义存放附件的对象

        // 删除队列文件
        $('.uploader-file-queue').on('click', '.icon-remove', function () {

            var id = $(this).parent().parent().attr('id');

            $('.file-item').each(function (index) {
                console.log($(this).parent().parent()[index]);
            })

            // 删除队列中的文件
            window.uploader.removeFile( id, true );
            // 删除dom节点
            $(this).parent().parent().remove();

        })


        $('#Modal-box').on('hidden.bs.modal', function (e) {
            window.uploader.destroy();
        })

        // 删除队列文件
        $('.uploader-attachment-queue').on('click', '.icon-remove', function(){
            var id = $(this).parent().parent().attr('id');
            // 删除队列中的文件
            window.uploader.removeFile( id, true );
            // 删除dom节点
            $(this).parent().parent().remove();
        });


        // 判断此评审是否有操作按钮权限
        $('.layui-form-item .btn-lay-orr a').click(function(){
            if($(this).find('button').hasClass('lay-orr')){
                layer.alert('该文件正在评审中，不能进行升级！', {
                    title:'评审未完成',
                    icon: 5,
                    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
                })
            }
            if($(this).find('button').hasClass('lay-err')){
                layer.alert('该文件正在评审中，不能重复发起评审！', {
                    title:'评审已发起',
                    icon: 5,
                    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
                })
            }
            if($(this).find('button').hasClass('lay-edit')){
                layer.alert('该文件未编辑，不能发起评审！', {
                    title:'文件未编辑',
                    icon: 5,
                    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
                })
            }
            if($(this).find('button').hasClass('lay-nou')){
                layer.alert('抱歉，只有编号申请人才能发起评审！', {
                    title:'没有权限',
                    icon: 5,
                    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
                })
            }
            if($(this).find('button').hasClass('lay-suss')){
                layer.alert('该文件已经评审完成，不能重复发起评审！', {
                    title:'评审已完成',
                    icon: 5,
                    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
                })
            }
            if($(this).find('button').hasClass('lay-old')){
                layer.alert('该文件版本过老，不能重复发起评审！', {
                    title:'版本过旧',
                    icon: 5,
                    skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
                })
            }
        })

        $('.layui-form-item .btn-lay-orr .edit-yfq').click(function(){

            layer.alert('该文件正在评审中，不能进行编辑！', {
                title:'评审已发起',
                icon: 5,
                skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
            })

        })

        $('.layui-form-item .btn-lay-orr .edit-ygd').click(function(){

            layer.alert('该文件已归档，不能进行编辑！', {
                title:'评审已归档',
                icon: 5,
                skin: 'layer-ext-moon' //该皮肤由layer.seaning.com友情扩展。关于皮肤的扩展规则，去这里查阅
            })

        })


        form.on('submit(submit)',function( data ){

            if($('#detail_form input[name="getVersion"]').val() == ''){

                if($('.info-input input[name="version"]').val() == ''){
                    layer.msg('请输入版本号！',{time:2000})
                    return false;
                }
                if(!$('.uploader-file-queue').html()){
                    layer.msg('请上传附件！',{time:2000})
                    return false;
                }

            }

            var getNo = $('#detail_form input[name="getNo"]').val();

            data.field.getNo = getNo;

            var version = VERSION;

            $.ajax({
                url : ThinkPHP['AJAX'] + '/File/fileDetail',
                dataType : 'json',
                type : 'POST',
                data :  data.field,

                beforeSend: function(){
                    layer.load(1,{
                        shade: [0.5,'#fff'] //0.1透明度的白色背景
                    });
                },
                success: function( response ){

                    console.log(response);
                    // 根据返回的结果检查队列里是否包含文件，如果有则上传，没有则直接跳转到详情页
                    if( response.flag){
                        if( window.uploader.getFiles().length > 0 ){ //检查队列里是否包含文件
                            SUB_NAME = response.flag;
                            VERSION = response.version;
                            NO = response.no;
                            window.uploader.option('formData', {SUB_NAME: SUB_NAME,VERSION:VERSION,NO:NO});
                            window.uploader.upload();
                        }else{
                            layer.msg(response.msg, {icon: 1, time: 1500});
                            setTimeout(function () {
                                location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/File/fileDetail/no/'+response.no+'/version/'+response.version+'';
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
