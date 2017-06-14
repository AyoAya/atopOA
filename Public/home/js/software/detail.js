/**
 * Created by GCX on 2017/5/18.
 */
$(function(){

    //实例化email推送
    $('.email-block-wrapper').emailBlock();

    // 默认只显示最新的一条版本信息
    $('.timeline-tr').each(function () {
        if($(this).index() == 0){
            $(this).find('.timeline-content').find('.box-info').css('display','block');
        }else{
            $(this).find('.timeline-content').find('.box-info').css('display','none');
            $(this).find('.timeline-content').find('.box-off-on').find('i').removeClass('icon-caret-down');
            $(this).find('.timeline-content').find('.box-off-on').find('i').addClass('icon-caret-right');
        }
    });

    //移除i标签的默认样式
    $('.box-off-on').find('.file-name').find('i').removeClass('icon-caret-right');


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

        var num = 0;
        var SUB_NAME,LOG_ID;

        var attachmentData = new Array();	//定义存放附件的对象

        //var filter_content =$('#Modal-box').html();
        $('#Modal-box').on('shown.bs.modal', function(e) {
            // 定义webuploader配置
            var webuploader_option = {
                auto: true,
                server: ThinkPHP['AJAX'] + '/Software/upload',
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

                var arr = ['zip', 'rar', 'doc', 'xls', 'xlsx', 'docx', 'pdf'];

                // 判断非图片文件是否大于1个
                if ($.inArray(file.ext, arr) >= 0) {

                    num++;
                }

                if (num > 1) {

                    num--;
                    window.uploader.removeFile( file.id, true );
                    layer.msg('非图像文件只能上传一个!');
                }

            });

            // 上传之前
            window.uploader.on('uploadBeforeSend', function (block, data, headers) {
                data.SUB_NAME = $('#subName').val();
                data.PATH = '/Software/';
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

        })



        //文件上传结束时
        form.on('submit(software)',function( data ){

            var tmpArr = new Array();

            if( $('#picker').next().children().length > 0 ){
                $('#picker').next().find('.file-item').each(function(index){
                    let tmpObj = {
                        ext: $(this).attr('ext'),
                        savename: $(this).attr('name'),
                        path: $(this).attr('path')
                    };
                    tmpArr.push(tmpObj);
                });
            }

            if( tmpArr.length <= 0 ){
                data.field.attachment = '';
            }else{
                data.field.attachment = JSON.stringify(tmpArr);
            }

            var receiveList = {
                recipient: [],
                cc: []
            };

            if( $('.email-block-push').children().length ){
                $('.email-block-push .email-block-user-item-span').each(function (index) {
                    let tmpObj = new Object();
                    tmpObj.name = $(this).text();
                    tmpObj.id = $(this).attr('user-id');
                    tmpObj.email = $(this).attr('user-email');

                    receiveList.recipient.push(tmpObj);
                })
            }

            if( $('.email-block-cc').children().length ){
                $('.email-block-cc .email-block-user-item-span').each(function (index) {
                    let tmpObj = new Object();
                    tmpObj.name = $(this).text();
                    tmpObj.id = $(this).attr('user-id');
                    tmpObj.email = $(this).attr('user-email');
                    receiveList.cc.push(tmpObj);
                })
            }

            emailObj = JSON.stringify(receiveList);

            $.ajax({
                url : ThinkPHP['AJAX'] + '/Software/addLog',
                type : 'POST',
                dataType : 'json',
                data : {
                    addRel : data.field,
                    email : emailObj,
                },

                beforeSend : function(){
                    layer.load(2, { shade : [0.5,'#fff'] });
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




    });


    // 销毁uploader
    $('#Modal-box').on('hide.bs.modal', function(e){
        window.uploader.destroy();
    });


    //里程碑

   $('.box-off-on a').click(function () {
       $(this).each(function () {
          if($(this).parent().next().css('display') == 'block'){
              $(this).find('.i-sty').removeClass('icon-caret-down');
              $(this).next().find('.file-name').find('i').removeClass('icon-caret-right');
              $(this).parent().next().css('display','none');
              $(this).find('.i-sty').addClass('icon-caret-right');
          }else {
              $(this).find('.i-sty').removeClass('icon-caret-right');
              $(this).next().find('.file-name').find('i').removeClass('icon-caret-right');
              $(this).parent().next().css('display','block');
              $(this).find('.i-sty').addClass('icon-caret-down');
          }
      })
    });










})




/*
 //监听用户是否开启邮件发送功能
 form.on('checkbox(ccs)', function( data ){
 if( $(data.elem).is(':checked') == true ){
 $('#all-fs-list').removeClass('sr-only');
 $('#fsBox').removeClass('sr-only');
 }else{
 $('#all-fs-list').addClass('sr-only');
 $('#fsBox').addClass('sr-only');
 }
 });

 //默认隐藏所有的发送人列表，当用户选择部门的时候显示对应的部门人员
 $('#fsBox .fs-box-list .layui-form-checkbox').css({display:'none'});

 //监听用户选择所属部门显示对应对应部门人员
 form.on('select(departments)', function( data ){
 var _value = data.value;
 $('#fsBox .fs-box-list .layui-form-checkbox').css({display:'none'});
 $('#fsBox .fs-box-list input[department='+_value+']').next().css({display:'inline-block'});

 });

 //监听用户选择有邮件发送人
 form.on('checkbox(users)', function( data ){
 console.log();
 if( $(data.elem).is(':checked') ){
 var _tmpDom = '';
 $(data.elem).attr('checked',true);
 //如果选中当前人员则将该人员添加到展示列表
 _tmpDom += '<div class="fs-list-item" value="'+$(this).attr('value')+'" email="'+$(this).attr('email')+'">'+$(this).attr('title')+' <i class="icon-remove"></i></div>\r\n';
 $('#all-fs-list').append(_tmpDom);
 }else{
 $(data.elem).attr('checked',false);
 //如果选中之后取消则将对应展示列表里的人员删除
 $('#all-fs-list div[value='+$(this).attr('value')+']').remove();
 }
 });

 //删除展示列表里面的发送人
 $('#all-fs-list').on('click', '.fs-list-item', function(){
 var value = $(this).attr('value');
 $('#fsBox .fs-box-list input[value='+ value +']').attr('checked',false);
 $('#fsBox .fs-box-list input[value='+ value +']').next().removeClass('layui-form-checked');
 $(this).remove();
 });


 //监听用户是否开启抄送功能
 form.on('checkbox(cc)', function( data ){
 if( $(data.elem).is(':checked') == true ){
 $('#all-cc-list').removeClass('sr-only');
 $('#ccBox').removeClass('sr-only');
 }else{
 $('#all-cc-list').addClass('sr-only');
 $('#ccBox').addClass('sr-only');
 }
 });

 //默认隐藏所有的抄送人列表，当用户选择部门的时候显示对应的部门人员
 $('#ccBox .cc-box-list .layui-form-checkbox').css({display:'none'});

 //监听用户选择所属部门显示对应对应部门人员
 form.on('select(department)', function( data ){
 var _value = data.value;
 $('#ccBox .cc-box-list .layui-form-checkbox').css({display:'none'});
 $('#ccBox .cc-box-list input[department='+_value+']').next().css({display:'inline-block'});

 });

 //监听用户选择抄送人
 form.on('checkbox(user)', function( data ){
 console.log();
 if( $(data.elem).is(':checked') ){
 var _tmpDom = '';
 $(data.elem).attr('checked',true);
 //如果选中当前人员则将该人员添加到展示列表
 _tmpDom += '<div class="cc-list-item" value="'+$(this).attr('value')+'" email="'+$(this).attr('email')+'">'+$(this).attr('title')+' <i class="icon-remove"></i></div>\r\n';
 $('#all-cc-list').append(_tmpDom);
 }else{
 $(data.elem).attr('checked',false);
 //如果选中之后取消则将对应展示列表里的人员删除
 $('#all-cc-list div[value='+$(this).attr('value')+']').remove();
 }
 });

 //删除展示列表里面的抄送人
 $('#all-cc-list').on('click', '.cc-list-item', function(){
 var value = $(this).attr('value');
 $('#ccBox .cc-box-list input[value='+ value +']').attr('checked',false);
 $('#ccBox .cc-box-list input[value='+ value +']').next().removeClass('layui-form-checked');
 $(this).remove();
 });
 console.log(fileData);
 */




