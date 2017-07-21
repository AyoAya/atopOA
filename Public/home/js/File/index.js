/**
 * Created by Fulwin on 2017/6/21.
 */
$(function(){


    layui.use(['form','layer','upload','element'], function(){
        var form = layui.form(),
            element = layui.element(),
            layer = layui.layer;

        var SUB_NAME, attachmentList = new Array();


        //添加一栏评审人
        $('#addPerson').on('click',function(){

        })

      /*  //清除筛选
        $('#submitSearch').on('click',function(){
            if($(this).prev().val()){
                alert(1)
                $('.cle-box').css('display','block')
            }else{
                alert(2)
            }
        });
        $('.cle-box').on('click',function(){
            $(this).css('display','none');
            location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/Review/index';
        })*/

        // uploader参数配置
        var logUploaderOption = {
            auto: false,
            server: ThinkPHP['AJAX'] + '/Review/uploadAttachment',
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
                attachmentObject.Ext = response.ext;
                attachmentObject.SaveName = response.savename;
                attachmentObject.SavePath = response.path;
                attachmentList.push(attachmentObject);
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
                url: ThinkPHP['AJAX'] + '/Review/saveDetailAttachment',
                dataType: 'json',
                type: 'POST',
                data: {
                    attachments : attachments,
                    id : SUB_NAME
                },
                success : function( response ){
                    if(response.flag > 0){
                        layer.msg('操作成功', {icon: 1, time: 0});
                        setTimeout(function () {
                            location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/Review/index';
                        })
                    }else{
                        layer.msg(layer.msg, {icon: 2, time: 0});
                        setTimeout(function () {
                            location.reload();
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


        //alert($('.apr-type select').val())
        var CurrentTab = $('#CurrentTab').val();    //获取到当前的tab
        // 如果当前访问的是 "initiated" 这个tab的才加载以下请求
        if( CurrentTab == 'initiated' ){

            $.ajax({
                url: ThinkPHP['AJAX'] + '/Review/category',
                type: 'POST',
                dataType: 'json',
                data: {
                    id : $('.apr-type select').val()

                },
                success : function( response ){
                    let tem ='';

                    var jsonObj=eval('('+response+')');
                    var arr = jsonObj[0].step;
                    $('.apr-box').find('small').html(jsonObj[0].aname);

                    //遍历step的数据注入模板
                    for(let i=0;i<arr.length;i++){
                        tem += "<li aid='"+arr[i].a_id+"' sid='"+arr[i].id+"'><i class='icon-double-angle-right'></i>　Step - "+arr[i].step+"　　"+arr[i].name+"</li>\n\r";
                    }

                    $('.apr-box').find('.apr-preview').find('ul').html(tem);
                    form.render('select');

                }

            });


            $.ajax({
                url: ThinkPHP['AJAX'] + '/Review/person',
                type: 'POST',
                dataType: 'json',
                data: {
                    id : $('.apr-type select').val()
                },
                success: function (response) {
                    let tmpPerson = '';

                    for (let i = 0; i < response.length; i++) {
                        tmpPerson += '<option value="' + response[i].id + '" email="'+ response[i].email +'">' + response[i].nickname + '</option>\n\r'
                    }
                    $('.sel-person select').html(tmpPerson);
                    form.render('select');
                }
            });


        }


        //监听选择的某个集
        form.on('select(category)',function(data){

            $.ajax({
                url: ThinkPHP['AJAX'] + '/Review/category',
                type: 'POST',
                dataType: 'json',
                data: {
                    id : data.value,

                },
                success : function( response ){
                    let tem ='';

                    var jsonObj=eval('('+response+')');
                    var arr = jsonObj[0].step;
                    $('.apr-box').find('small').html(jsonObj[0].aname);

                    //遍历step的数据注入模板
                    for(let i=0;i<arr.length;i++){
                         tem += "<li aid='"+arr[i].a_id+"' sid='"+arr[i].id+"'><i class='icon-double-angle-right'></i>　Step - "+arr[i].step+"　　"+arr[i].name+"</li>\n\r";
                    }

                    $('.apr-box').find('.apr-preview').find('ul').html(tem);
                    form.render('select');

                }

            });

            $.ajax({
                url: ThinkPHP['AJAX'] + '/Review/person',
                type: 'POST',
                dataType: 'json',
                data: {
                    id : data.value,
                },
                success : function( response ){
                    let tmpPerson = '';

                    for (let i=0;i<response.length;i++){

                        tmpPerson += '<option value="'+ response[i].id +'" email="'+ response[i].email +'">'+ response[i].nickname +'</option>\n\r'
                    }
                    $('.sel-person select').html(tmpPerson);
                    form.render('select');

                }

            });

        });

        form.on('submit(submit)',function( data ){

            $.ajax({
                url : ThinkPHP['AJAX'] + '/Review/SaveInitiatedApproval',
                dataType : 'json',
                type : 'POST',
                data : data.field,
                beforeSend: function(){
                    layer.load(1, {
                        shade: [0.5,'#fff'] //0.1透明度的白色背景
                    });
                },
                success: function( response ){

                    // 根据返回的结果检查队列里是否包含文件，如果有则上传，没有则直接跳转到详情页
                    if( response.flag ){

                        if( APRuploader.getFiles().length > 0 ){ //检查队列里是否包含文件
                            SUB_NAME = response.flag;
                            APRuploader.option('formData', {SUB_NAME: SUB_NAME});
                            APRuploader.upload();
                        }else{
                            layer.msg(response.msg, {icon: 1, time: 1500});
                            setTimeout(function () {
                                location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/Review/index';
                            })
                        }
                    }else{
                        layer.msg('ERROR', {icon: 2, time: 2000});
                    }
                }
            });

            return false;

        });


    });




});