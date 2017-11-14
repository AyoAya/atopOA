/**
 * Created by GCX on 2017/10/30.
 */
$(function(){

    layui.use(['form','layer'],function(){
        var layer = layui.layer,
            form = layui.form();

        // 副总审批加载信息
        $('.rw-text').click(function(event){
            if($('.rb').css('display') == 'none'){
                $('.rb').show();
            }else{
                $('.rb').hide();
            }
        });
        // 部门加载信息
        $('.department-text').click(function(event){
            if($('.department-xz').css('display') == 'none'){
                $('.department-xz').show();
            }else{
                $('.department-xz').hide();
            }
        });


        $(document).bind('click',function(e){

            // 过滤掉初次输入框点击失效
            if( e.target.className == 'department-text' ) return;
            if( e.target.className == 'icon-remove close-depart' ) return;
            var _targerDom = $('.department-xz');
            var currentDisplay = $('.department-xz').css('display');
            // 当选择框在显示的情况才执行
            if( currentDisplay != 'none' ){
                if( !_targerDom.is(e.target) && _targerDom.has(e.target).length === 0 ){
                    if( currentDisplay == 'block' ){
                        $('.department-xz').css('display', 'none');
                    }
                }
            }

            if( e.target.className == 'rw-text' ) return;
            if( e.target.className == 'icon-remove close-user' ) return;
            var _dome = $('.rb');
            var _Display = $('.rb').css('display');
            // 当选择框在显示的情况才执行
            if( _Display != 'none' ){
                if( !_dome.is(e.target) && _dome.has(e.target).length === 0 ){
                    if( _Display == 'block' ){
                        $('.rb').css('display', 'none');
                    }
                }
            }



        });

        var SUB_NAME,LOG_ID;
        var attachmentData = new Array();	//定义存放附件的对象

        // 定义webuploader配置
            var webuploader_option = {
                auto: true,
                server: ThinkPHP['AJAX'] + '/Bulletin/upload',
                pick: {
                    id: '#picker',
                    multiple: false
                },
                fileVal: 'Filedata',
                accept: {
                    title: 'file',
                    extensions: 'zip,rar,jpg,png,jpeg,doc,xls,xlsx,docx,gif,pdf'
                },
                method: 'POST'
            };
            // 实例化webuploader
            window.uploader = WebUploader.create(webuploader_option);

            // 添加到队列时
            window.uploader.on('fileQueued', function (file) {

            });

            // 上传之前
            window.uploader.on('uploadBeforeSend', function (block, data, headers) {
                data.PATH = '/Bulletin/';
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

                console.log(response);

                if (response.flag > 0) {

                    var fileItem = '<div class="file-item" style="width: 100%" id="' + file.id + '" ext = "'+file.ext+'" name="'+file.name+'" path="'+response.path+'">' +
                        '<div class="pull-left"><i class="file-icon file-icon-ext-' + file.ext + '"></i> ' + file.name + '</div>' +
                        '<div class="pull-right"><i class="icon-remove" title="移除该文件"></i></div>' +
                        '<div class="clearfix"></div>' +
                        '</div>';
                    $(webuploader_option.pick.id).next().append(fileItem);
                } else {
                    layer.closeAll();
                    layer.msg(response.msg, {icon: 2, time: 2000});
                }

            });

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

            // 选中人员添加进队列
            $('.fz-ps').on('click','.person',function(){
                var text_ = $(this).text();
                var email_ = $(this).attr('email');
                var id_ = $(this).attr('uid');

                var _person = '<span class="cc_person cc_person'+id_+'" email="'+email_+'" uid="'+id_+'">'+text_+' <i class="icon-remove close-user"></i></span>';

                var num = 0;

                $('.rw-text .cc_person').each(function(){
                    if($(this).hasClass('cc_person'+id_)){
                        num += 1;
                        return;
                    }
                });

                if(num > 0){
                    layer.msg(''+text_+'已存在!',{icon:5,time:1000});
                }else{
                    $('.rw-text').append(_person);
                }

            })
            // 选中部门添加进队列
            $('.department').on('click','.depart_person',function(){
                var text_ = $(this).text();
                var id_ = $(this).attr('pid');

                var _person = '<span class="department_person department_person'+id_+'" pid="'+id_+'">'+text_+' <i class="icon-remove close-depart"></i></span>';

                var num = 0;

                $('.department-text .department_person').each(function(){
                    if($(this).hasClass('department_person'+id_)){
                        num += 1;
                        return;
                    }
                });

                if(num > 0){
                    layer.msg(''+text_+'已存在!',{icon:5,time:2000});
                }else{
                    if(id_ == 'all'){
                        $('.department-text').html(_person);
                    }else{

                        $('.department-text .department_person').each(function(){
                            if($(this).hasClass('department_personall')){
                                num += 1;
                                return;
                            }
                        });
                        if(num > 0){
                            $('.department-text').html('');
                            $('.department-text').append(_person);
                        }else{
                            $('.department-text').append(_person);
                        }

                    }
                }

            })

            // close 删除列队中用户
            $('.fz-ps').on('click','.close-user',function(){

                $(this).parent().remove();
                if( $(this).parents('.fz-rw').prev().css('display') == 'none'){
                    $('.rb').hide();
                }else{
                    $('.rb').show();
                }

            });

            // close 删除列队中部门
            $('.department').on('click','.close-depart',function(){

                $(this).parent().remove();
                if( $(this).parents('.department-rw').prev().css('display') == 'none'){
                    $('.department-xz').hide();
                }else{
                    $('.department-xz').show();
                }

            });
            // 点击添加评审人
            $('.add-user').click(function(){
                $('.fz-ps').show();
                $('.remove-fz').show();
            });

            $('.remove-fz').click(function(){
                $('.fz-ps').hide();
                $('.rb').hide();
                $('.rw-text').html('');
                $(this).hide();
            });

            // 提交数据
            form.on('submit(submit)',function( data ){

                if(data.field.context == ''){
                    layer.msg('正文不能为空！',{icon:5,time:2000});
                    return false;
                }

                var tmpAtta = new Array();

                if( $('#picker').next().children().length > 0 ){
                    $('#picker').next().find('.file-item').each(function(index){
                        let tmpObj = {
                            ext: $(this).attr('ext'),
                            savename: $(this).attr('name'),
                            path: $(this).attr('path')
                        };
                        tmpAtta.push(tmpObj);
                    });
                }

                if( tmpAtta.length <= 0 ){
                    data.field.attachment = '';
                }else{
                    data.field.attachment = JSON.stringify(tmpAtta);
                }

                // 收集审批人
                var tmpArr = [];
                $('.rw-text .cc_person').each(function( index ){
                    tmpArr.push($(this).attr('uid'));
                })

                if($('.fz-ps').css('display') != 'none'){
                    if(tmpArr == ''){
                        layer.msg('请选择副总，如不选请移除此级审批!',{icon:5,time:2000})
                        return false;
                    }else{
                        data.field.vice = tmpArr;
                    }

                }

                // 收集通知部门
                var tmpDepart = [];
                $('.department-text .department_person').each(function( index ){
                    tmpDepart.push($(this).attr('pid'));
                })

                //console.log(tmpDepart);

                if(tmpDepart == ''){
                    layer.msg('请选择部门!',{icon:5,time:2000})
                    return false;
                }else{
                    data.field.department = tmpDepart;
                }



                $.ajax({
                    url : ThinkPHP['AJAX'] + '/Bulletin/add',
                    type : 'POST',
                    data : data.field,
                    dataType : 'json',
                    beforeSend : function(){
                        layer.load(2, { shade : [0.5,'#fff'] });
                    },
                    success : function ( response ) {
                        if( response.flag > 0){
                            layer.msg(response.msg,{icon:1,time:2000});
                            setTimeout(function(){
                                location.href = 'http://'+ThinkPHP['HTTP_HOST']+'/Bulletin/detail/id/'+response.flag;
                            },2000);
                        }else{
                            layer.msg(response.msg,{icon:2,time:2000});
                            setTimeout(function(){
                                location.reload();
                            },2000)
                        }
                    }

                });


                return false;

            })






        })

})