/**
 * Created by Fulwin on 2017/5/2.
 */
$(function(){


    //定义存放附件的数组
    var attachmentData = new Array(),
        attachmentList = new Array();


    //初始化layui组件
    layui.use(['form','layer','upload'], function(){
        var form = layui.form(),
            layer = layui.layer;

        var SUB_NAME,LOG_ID;


        /**
         * 客诉详情页文件上传
         * 1. 文件支持队列添加/删除
         * 2. 日志写入成功后返回日志id再上传文件
         * 3. 以返回的日志id为条件修改附件字段
         * 4. 解决webuploader在bootstrap框架modal里的兼容性问题
         * 		(1). 在modal窗口完全打开时初始化webuploader
         * 		(2). 当modal窗口关闭时，销毁webuoloader(样式冲突/问题)
         */

        $('#customer-rma-modal').on('shown.bs.modal', function(e){
            // uploader参数配置
            var logUploaderOption = {
                auto: false,
                server: ThinkPHP['AJAX'] + '/RMA/upload',
                pick: '#logPick',
                fileVal : 'Filedata',
                accept: {
                    title: 'file',
                    extensions: 'zip,rar,jpg,png,jpeg,doc,docx,xls,xlsx,pdf'
                },
                method: 'POST',
            };
            // 实例化uploader
            window.loguploader = WebUploader.create( logUploaderOption );
            // 添加到队列时
            window.loguploader.on('fileQueued', function( file ){
                var fileItem = '<div class="file-item" id="'+ file.id +'">' +
                    '<div class="pull-left"><i class="icon-file-alt"></i>&nbsp;&nbsp;'+ file.name +'</div>' +
                    '<div class="pull-right"><i class="icon-remove" title="移除该文件"></i></div>' +
                    '<div class="clearfix"></div>' +
                    '</div>';
                $(logUploaderOption.pick).next().append(fileItem);
            });
            // 上传错误时
            window.loguploader.on('error', function( code ){
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
            window.loguploader.on('uploadBeforeSend', function( block, data, headers ){
                data.SUB_NAME = SUB_NAME;
                data.LOG_ID = LOG_ID;
            });
            // 上传成功时
            window.loguploader.on('uploadSuccess', function( file,response ){
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
            window.loguploader.on('uploadFinished', function(){
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

        });

        // 销毁uploader
        $('#customer-rma-modal').on('hide.bs.modal', function(e){
            window.loguploader.destroy();
        });


        // 删除队列文件
        $('.uploader-attachment-queue').on('click', '.icon-remove', function(){
            var id = $(this).parent().parent().attr('id');
            // 删除队列中的文件
            window.loguploader.removeFile( window.loguploader.getFile(id,true) );
            // 删除dom节点
            $(this).parent().parent().remove();
        });



        //监听rma处理类型
        //如果当前步骤处于销售确认是否转RMA，当用户选择推送到下一步则显示QA部门人员选择框否则隐藏
        form.on('select(operation)', function( data ){
            var elem = data.elem;
            var value = data.value;
            var now_step = $(elem).parents('.customer-rma-form').find('input[name=step]').val();
            var operation_select_elem = $(elem).parents('.customer-rma-form').find('.operation-person-select');
            var step_select_elem = $(elem).parents('.customer-rma-form').find('.step-select');
            var fae_select_elem = $(elem).parents('.customer-rma-form').find('.fae-person-select');
            var close_reason_elem = $(elem).parents('.customer-rma-form').find('.close-reason-select');
            if( value == 'X' ){
                if( !$(operation_select_elem).hasClass('sr-only') ){
                    $(operation_select_elem).addClass('sr-only');
                }
                if( !$(step_select_elem).hasClass('sr-only') ){
                    $(step_select_elem).addClass('sr-only');
                }
                if( !$(fae_select_elem).hasClass('sr-only') ){
                    $(fae_select_elem).addClass('sr-only');
                }
                if( !$(close_reason_elem).hasClass('sr-only') ){
                    $(close_reason_elem).addClass('sr-only');
                }
            }
            if( now_step == 3 || now_step == 4 ){
                if( now_step == 3 && value == 'N' ){	//如果当前步骤等于3并且选择关闭客诉则显示关闭客诉原因下拉菜单
                    if( $(close_reason_elem).hasClass('sr-only') ){
                        $(close_reason_elem).removeClass('sr-only');
                    }
                }else{
                    if( !$(close_reason_elem).hasClass('sr-only') ){
                        $(close_reason_elem).addClass('sr-only');
                    }
                }
                if( now_step == 3 && value == 4 || now_step == 4 && value == 'Z' ){	//如果当前步骤等于3或者等于4，并且选择推送下一步或者转交则显示qa部门人员选择下拉菜单
                    if( $(operation_select_elem).hasClass('sr-only') ){
                        $(operation_select_elem).removeClass('sr-only');
                    }
                }else{
                    if( !$(operation_select_elem).hasClass('sr-only') ){
                        $(operation_select_elem).addClass('sr-only');
                    }
                }
            }
            if( now_step == 5 || now_step == 6 ){	//如果步骤可以回退则显示回退步骤下拉菜单
                if( now_step == 6 && value == 'N' ){	//如果当前步骤等于6并且选择关闭客诉则显示关闭客诉原因下拉菜单
                    if( $(close_reason_elem).hasClass('sr-only') ){
                        $(close_reason_elem).removeClass('sr-only');
                    }
                }else{
                    if( !$(close_reason_elem).hasClass('sr-only') ){
                        $(close_reason_elem).addClass('sr-only');
                    }
                }
                if( value == 'H' ){	//当用户选择回退的时候才显示否则隐藏
                    if( $(step_select_elem).hasClass('sr-only') ){
                        $(step_select_elem).removeClass('sr-only');
                    }
                    if( !$(fae_select_elem).hasClass('sr-only') ){
                        $(fae_select_elem).addClass('sr-only');
                    }
                }else{
                    if( !$(step_select_elem).hasClass('sr-only') ){
                        $(step_select_elem).addClass('sr-only');
                    }
                    if( value == 'Z' ){
                        if( $(fae_select_elem).hasClass('sr-only') ){
                            $(fae_select_elem).removeClass('sr-only');
                        }
                        if( !$(step_select_elem).hasClass('sr-only') ){
                            $(step_select_elem).addClass('sr-only');
                        }
                    }else{
                        if( !$(fae_select_elem).hasClass('sr-only') ){
                            $(fae_select_elem).addClass('sr-only');
                        }
                    }
                }
            }
        });


        //监听rma处理表单
        form.on('submit(rmaCustomer)', function( data ){
            var fields = data.field;
            if( attachmentList.length == 0 ){	//如果附件为空则提交空字符串，如果不为空则转换为json格式数据提交
                fields.attachments = '';
            }else{
                fields.attachments = JSON.stringify(attachmentList);
            }
            /*if( fields.operation_type == 'X' && $.trim(fields.log_content) == '' ){
             layer.msg('请输入点内容吧');
             $('textarea[name=log_content]').focus();
             return false;
             }*/
            //console.log(fields);
            $.ajax({
                url : ThinkPHP['AJAX'] + '/RMA/addRMA_OperationLog',
                type : 'POST',
                data : fields,
                dataType : 'json',
                beforeSend : function(){
                    layer.load(2,{shade:[0.8,'#fff']});
                },
                success : function(response){
                    if( response.flag > 0 ){
                        SUB_NAME = response.id;
                        LOG_ID = response.logid;
                        //如果队列里没有文件则不上传
                        if( window.loguploader.getFiles().length > 0 ){
                            window.loguploader.upload();
                        }else{
                            layer.msg(response.msg,{icon : 1,time : 2000});
                            setTimeout(function(){
                                location.reload();
                            },2000);
                        }
                    }else{
                        layer.msg(response.msg,{icon : 2,time : 2000});
                        setTimeout(function(){
                            layer.closeAll();
                        },2000);
                    }
                }
            });
            return false;
        });



    });
























});