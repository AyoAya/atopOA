layui.use(['form', 'jquery', 'layer', 'layedit'], function(){
    // init layui components.
    var form = layui.form(),
        $ = layui.jquery,
        layer = layui.layer,
        layedit = layui.layedit;

    var _fileID = $('#fileID').val(),
        attachmentsObject;

    // 定义layedit编辑器配置
    var layeditOptions = {
        height: 240
    }

    // 实例化layedit编辑器
    var layeditDescription = layedit.build('layedit-description', layeditOptions);

    // 编辑
    $('.xq-edit-btn').click(function(){
        $('.preview-condition').addClass('sr-only');
        $('.edit-condition').removeClass('sr-only');
    });

    // 取消编辑
    $('.xq-cancel-btn').click(function(){
        $('.preview-condition').removeClass('sr-only');
        $('.edit-condition').addClass('sr-only');
        $('#content').scrollTop(0);
    });

    // 回收
    $('.recycle-btn').click(function(){
        layer.msg('暂未开放回收功能');
    });

    // uploader参数配置
    var FileUploaderOptions = {
        auto: false,
        server: ThinkPHP['AJAX'] + '/File/upload',
        pick: {
            id: '#FilePick',
            multiple: false
        },
        fileVal : 'Filedata',
        accept: {
            title: 'file',
            multi: false,
            extensions: 'zip,rar,jpg,png,jpeg,doc,docx,xls,xlsx,pdf'
        },
        method: 'POST',
    };

    // 实例化uploader
    FileUploader = WebUploader.create( FileUploaderOptions );

    // 添加到队列时
    FileUploader.on('fileQueued', function( file ){
        var fileItem = '<div class="file-item" id="'+ file.id +'">' +
            '<div class="pull-left"><i class="file-icon file-icon-ext-'+ file.ext +'"></i> '+ file.name +'</div>' +
            '<div class="pull-right"><i class="icon-remove" title="移除该文件"></i></div>' +
            '<div class="clearfix"></div>' +
            '</div>';
        $(FileUploaderOptions.pick.id).next().append(fileItem);
    });

    // 上传错误时
    FileUploader.on('error', function( code ){
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
    FileUploader.on('uploadBeforeSend', function( block, data, headers ){
        data.PATH = '/File/' + _fileID + '/';
    });

    // 上传成功时
    FileUploader.on('uploadSuccess', function( file,response ){
        if( response.flag > 0 ){
            attachmentsObject = {
                name: response.savename,
                ext: response.ext,
                path: response.path
            }
            submitEditData(attachmentsObject);  // 文件上传成功后提交编辑数据
        }else{
            layer.msg(response.msg, { icon : 2,time : 1000 });
        }
    });

    // 删除队列文件
    $('.uploader-attachment-queue').on('click', '.icon-remove', function(){
        var id = $(this).parent().parent().attr('id');
        // 删除队列中的文件
        FileUploader.removeFile( id, true );
        // 删除dom节点
        $(this).parent().parent().remove();
    });

    // 回收文件号
    $('.recycle-btn').click(function(){
        layer.confirm('点击确定后该编号将会被系统回收，继续吗？', {
            btn: ['确定','取消'] //按钮
        }, function(){
            $.post(ThinkPHP['AJAX'] + '/File/recyleFileNumber', {
                fileid: fileid
            }, function(response){
                if( response.flag ){
                    layer.msg(response.msg, {icon: 1, time: 1500});
                    setTimeout(function(){
                        location.href = 'http://'+ ThinkPHP['HTTP_HOST'] +'/File';
                    }, 1500);
                }else{
                    layer.msg(response.msg, {icon: 1, time: 2000});
                    setTimeout(function(){
                        layer.closeAll();
                    }, 2000);
                }
            });
        }, function(){
            layer.closeAll();
        });
    });

    // 编辑提交
    $('#saveFileData').click(function(){
        var data = {};
        if( !checkEditData() ) return false;
        if( FileUploader.getFiles().length ) {  // 如果不存在附件
            FileUploader.upload();
        }else{
            submitEditData(null);
        }
    });

    // 验证编辑
    function checkEditData(){
        var version = $('#version').val();
        if( version.length <= 0 ){
            layer.msg('请输入版本号', {icon : 2,time : 1000});
            return false;
        }
        if( !/^[v|V][0-9]+(\.[0-9]+)+$/.test(version) ){
            layer.msg('版本格式错误', {icon : 2,time : 1000});
            return false;
        }
        if( $('#file-attachment-a').length < 1 ){
            if( FileUploader.getFiles().length <= 0 ){
                layer.msg('请上传附件', {icon : 2,time : 1000});
                return false;
            }
        }
        return true;
    }

    $('#review-btn').click(function(){
        let rid = $(this).attr('rid');
        //询问框
        layer.open({
            type: 1,
            title: '提示',
            skin: 'layui-layer-demo', //样式类名
            closeBtn: 1, //不显示关闭按钮
            area: ['440px'],
            btn: ['确定','取消'],
            shadeClose: true, //开启遮罩关闭
            content: '<p style="padding: 20px;"><i class=""></i>该文件已存在对应的ECN，点击确定将会跳转到ECN详情页面。<br><br>1.如果ECN状态为待评审，可以直接发起评审。<br>2.如果ECN状态为已拒绝，则需重新编辑后再发起评审。</p>',
            yes: function(index, layero){
                location.href = 'http://'+ ThinkPHP['HTTP_HOST'] +'/ECN/add/type/file/id/'+ rid;
            },
            btn2: function(index, layero){
                layer.closeAll();
            }
        });
    });

    // 提交编辑数据
    function submitEditData(filedata){
        let _version = $('#version').val();
        let postData = {};
        postData.id = _fileID;
        postData.version = _version;
        if( filedata ) postData.attachment = JSON.stringify(filedata);
        postData.description = layedit.getContent(layeditDescription);
        $.post(ThinkPHP['AJAX'] + '/File/detail', postData, function(response){
            if( response.flag ){
                layer.msg(response.msg, {icon : 1,time : 1000});
                setTimeout(function(){
                    location.replace(location.href);
                }, 1000);
            }else{
                layer.msg(response.msg, {icon : 2,time : 3000});
                setTimeout(function(){
                    location.replace(location.href);
                }, 3000);
            }
        })
    }

});