/**
 * Created by Fulwin on 2017/5/2.
 */
$(function(){


    //定义存放附件的数组
    var attachmentData = new Array(),
        attachmentList = new Array();


    //初始化layui组件
    layui.use(['form','layer','upload','element'], function(){
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
                    '<div class="pull-left"><i class="file-icon file-icon-ext-'+ file.ext +'"></i> '+ file.name +'</div>' +
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
            if( $(data.elem).is(':checked') ){
                var _tmpDom = '';
                $(data.elem).attr('checked',true);
                //如果选中当前人员则将该人员添加到展示列表
                _tmpDom += '<div class="cc-list-item" value="'+$(this).attr('value')+'">'+$(this).attr('title')+' <i class="icon-remove"></i></div>\r\n';
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
            var push_person_select = $(elem).parents('.customer-rma-form').find('.push-person-select');
            var push_person_x_select = $(elem).parents('.customer-rma-form').find('.push-person-x-select');
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
            if( now_step == 2 || now_step == 4 || now_step == 5 ){
                if( value == 6 || value == 3 ){
                    if( $(push_person_select).hasClass('sr-only') ){
                        $(push_person_select).removeClass('sr-only')
                    }
                }else{
                    if( !$(push_person_select).hasClass('sr-only') ){
                        $(push_person_select).addClass('sr-only')
                    }
                }
                if( value == 5 ){
                    if( $(push_person_x_select).hasClass('sr-only') ){
                        $(push_person_x_select).removeClass('sr-only')
                    }
                }else{
                    if( !$(push_person_x_select).hasClass('sr-only') ){
                        $(push_person_x_select).addClass('sr-only')
                    }
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
            if( now_step == 2 ){
                if( value == 'Z' ){
                    if( $(fae_select_elem).hasClass('sr-only') ){
                        $(fae_select_elem).removeClass('sr-only');
                    }
                }else{
                    if( !$(fae_select_elem).hasClass('sr-only') ){
                        $(fae_select_elem).addClass('sr-only');
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

            //console.log(data.field);
            if( data.field.step == 4 && data.field.operation_type == 6 || data.field.operation_type == 5 && window.loguploader.getFiles().length <= 0 ){
                layer.msg('请先上传分析报告',{ icon:2, time:2000 });
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
                            layer.msg(response.msg,{icon : 1,time : 1000});
                            setTimeout(function(){
                                location.reload();
                            },1000);
                        }
                    }else{
                        layer.msg(response.msg,{icon : 2,time : 1000});
                        setTimeout(function(){
                            layer.closeAll();
                        },1000);
                    }
                }
            });
            return false;
        });



    });
























});