$(function(){

    //定义存放附件的数组
    var attachmentList = new Array();

    //如果有留言就显示
    $('.myTooltip').hover(function(){
        $(this).find('.popover-box').removeClass('sr-only');
    },function(){
        $(this).find('.popover-box').addClass('sr-only');
    });


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

    // 删除队列文件
    $('.uploader-attachment-queue').on('click', '.icon-remove', function(){
        var id = $(this).parent().parent().attr('id');
        // 删除队列中的文件
        window.uploader.removeFile( window.uploader.getFile(id,true) );
        // 删除dom节点
        $(this).parent().parent().remove();
    });



    //初始化layui组件
    layui.use(['form','layer'], function() {
        var layer = layui.layer,
            form = layui.form();

        //监听操作类型，如果是push则显示推送人列表
        form.on('select(operation)', function( data ){

            var _value = data.value;    //获取到当前选中value

            //根据当前操作类型显示对应的处理人列表
            if( _value == 'push' || _value == 'transfer' || _value == 'rollback' ){
                if( _value == 'push' ){
                    $('#operating-person').css({display:'inline-block'});
                    $('.logistics').css({display:'inline-block'});
                    $('#transfer-person').css({display:'none'});
                    $('#rollback-person').css({display:'none'});
                    $('#TestReport').css({display:'block'});
                    //当用户选择推送时，检查上传附件dom元素是否存在，如果存在则实例化webuploader
                    //为避免重复生成实例，当window.uploader类型为object时不创建实例
                    if( $('#testReportPick').length > 0 ){

                        if( typeof(window.uploader) != "object"  ){
                            // uploader参数配置
                            var logUploaderOption = {
                                auto: false,
                                server: ThinkPHP['AJAX'] + '/Sample/upload',
                                pick: '#testReportPick',
                                fileVal : 'Filedata',
                                accept: {
                                    title: 'file',
                                    extensions: 'zip,rar,jpg,gif,png,jpeg,doc,docx,xls,xlsx,pdf'
                                },
                                method: 'POST',
                            };
                            // 实例化uploader
                            window.uploader = WebUploader.create( logUploaderOption );
                            // 添加到队列时
                            window.uploader.on('fileQueued', function( file ){
                                var fileItem = '<div class="file-item" id="'+ file.id +'" style="width: 508px !important;">' +
                                    '<div class="pull-left"><i class="file-icon file-icon-ext-'+ file.ext +'"></i> '+ file.name +'</div>' +
                                    '<div class="pull-right"><i class="icon-remove" title="移除该文件"></i></div>' +
                                    '<div class="clearfix"></div>' +
                                    '</div>';
                                $(logUploaderOption.pick).next().append(fileItem);
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
                            // 上传之前
                            window.uploader.on('uploadBeforeSend', function( block, data, headers ){
                                data.SUB_NAME = $('#detailID').val();
                                data.DIR = '/Sample/TestReport/';
                            });
                            // 上传成功时
                            window.uploader.on('uploadSuccess', function( file,response ){
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
                            window.uploader.on('uploadFinished', function(){
                                $.ajax({
                                    url : ThinkPHP['AJAX'] + '/Sample/insertTestReport',
                                    type : 'POST',
                                    data : {
                                        SUB_NAME : $('#detailID').val(),
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
                        }
                    }
                }
                if( _value == 'transfer' ){
                    $('#operating-person').css({display:'none'});
                    $('#rollback-person').css({display:'none'});
                    $('.logistics').css({display:'none'});
                    $('#transfer-person').css({display:'inline-block'});
                    $('#TestReport').css({display:'none'});
                    //兼容bootstrap和webuploader样式，当用户选择非推送的步骤时销毁webuploader实例
                    if( window.uploader ){
                        //销毁webuploader
                        window.uploader.destroy();
                        //webuploader被销毁后指定window.uploader值为undefined
                        window.uploader = undefined;
                        //销毁webuploader实例后清空队列里存在的附件dom元素
                        $('.uploader-attachment-queue').html('');
                    }
                }
                if( _value == 'rollback' ){
                    $('#operating-person').css({display:'none'});
                    $('#rollback-person').css({display:'inline-block'});
                    $('.logistics').css({display:'none'});
                    $('#transfer-person').css({display:'none'});
                    $('#TestReport').css({display:'none'});
                    //兼容bootstrap和webuploader样式，当用户选择非推送的步骤时销毁webuploader实例
                    if( window.uploader ){
                        //销毁webuploader
                        window.uploader.destroy();
                        //webuploader被销毁后指定window.uploader值为undefined
                        window.uploader = undefined;
                        //销毁webuploader实例后清空队列里存在的附件dom元素
                        $('.uploader-attachment-queue').html('');
                    }
                }
            }else{
                $('#operating-person').css({display:'none'});
                $('.logistics').css({display:'none'});
                $('#transfer-person').css({display:'none'});
                $('#rollback-person').css({display:'none'});
                $('#TestReport').css({display:'none'});
                //兼容bootstrap和webuploader样式，当用户选择非推送的步骤时销毁webuploader实例
                if( window.uploader ){
                    //销毁webuploader
                    window.uploader.destroy();
                    //webuploader被销毁后指定window.uploader值为undefined
                    window.uploader = undefined;
                    //销毁webuploader实例后清空队列里存在的附件dom元素
                    $('.uploader-attachment-queue').html('');
                }
            }

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
        $('.cc-box-list .layui-form-checkbox').css({display:'none'});

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


        //添加处理记录
        form.on('submit(processingRecord)',function( data ){

            if( data.field.waybill && $.trim(data.field.waybill) == '' ){
                layer.msg('请输入运单号');
                return false;
            }

            //检查用户是否开启抄送
            if( data.field.cc_on && data.field.cc_on == 'Y' ){
                var emails = new Array();
                //如果开启则收集选中的人员
                $('#ccBox .cc-box-list input:checked').each(function(){
                    emails.push($(this).attr('email'));
                });
                if( emails.length <= 0 ){
                    layer.msg('你没有选择抄送人喔');
                    return false;
                }
                data.field.cc_email_list = emails;
            }

            if( window.uploader ){
                //如果uoloader存在并且队列里存在文件则上传
                if( window.uploader.getFiles().length <= 0 ){
                    layer.msg('请上传测试报告');
                    return false;
                }
            }

            //如果当前推送步骤没有物流数据则删除相应数据
            if( $('.logistics').css('display') == 'none' ){
                delete data.field.logistics;
                delete data.field.waybill;
            }



            $.ajax({
                url : ThinkPHP['AJAX'] + '/Sample/addSampleLog',
                type : 'post',
                dataType : 'JSON',
                data : data.field,
                beforeSend : function(){
                    layer.load(2, { shade : [0.5,'#fff'] });
                },
                success : function( response ){

                    if (response.flag > 0){

                        //数据插入成功后检查uploader是否存在
                        if( typeof (window.uploader) == 'object' ){
                            //如果uoloader存在并且队列里存在文件则上传
                            if( window.uploader.getFiles().length > 0 ){
                                window.uploader.upload();
                            }
                        }else{
                            layer.msg( response.msg, { icon : 1, time : 2000 } );
                            setTimeout(function(){
                                location.reload();
                            },2000);
                        }

                    }else{
                        layer.msg( response.msg, { icon : 2, time : 2000 } );
                    }
                }
            });


            return false; //阻止表单跳转。
        });


    });


});