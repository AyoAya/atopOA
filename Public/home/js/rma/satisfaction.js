/**
 * Created by Fulwin on 2017/7/7.
 */
$(function(){

    layui.use(['form', 'layer'], function(){
        var form = layui.form(),
            layer = layui.layer;

        var attachmentList = new Array();
        var SUB_NAME;

        // uploader参数配置
        var sfUploaderOption = {
            auto: true,
            server: ThinkPHP['AJAX'] + '/RMA/SF_Upload',
            fileData: {
                SUB_NAME: SUB_NAME
            },
            pick: {
                id: '.filePick',
                multiple: false
            },
            fileVal : 'Filedata',
            accept: {
                title: 'file',
                extensions: 'zip,rar,jpg,png,jpeg,doc,docx,xls,xlsx,pdf'
            },
            method: 'POST',
        };


        /**
         * 编辑送样客诉
         * 点击后将所有的可编辑项变为input[text]，初始化时为hidden
         */
        $('.layui-table').on('click', '.edit-btn', function(){
            let _parent = $(this).parents('tr');
            let _next = $(this).parent().next();
            let _dynamics = _parent.find('.dynamic');
            let _stables = _parent.find('.stable');
            SUB_NAME = _parent.find('input[name=id]').val();

            // 实例化uploader
            sfUploader = WebUploader.create( sfUploaderOption );
            // 上传错误时
            sfUploader.on('error', function( code ){
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
            sfUploader.on('uploadBeforeSend', function( block, data, headers ){
                data.SUB_NAME = SUB_NAME;
            });
            // 上传成功时
            sfUploader.on('uploadSuccess', function( file,response ){
                if( response.flag > 0 ){
                    layer.msg('上传成功，请保存', { icon : 1,time : 1000 });
                    var attachmentObject = new Object();
                    attachmentObject.ext = response.ext;
                    attachmentObject.savename = response.savename;
                    attachmentObject.path = response.path;
                    attachmentList.push(attachmentObject);
                }else{
                    layer.closeAll();
                    layer.msg('上传失败', { icon : 2,time : 2000 });
                }
            });

            _dynamics.attr('type', 'text');
            _dynamics.css({display: 'block'});
            _stables.css({display: 'none'});
            $(this).parent().addClass('sr-only');
            _next.removeClass('sr-only');
        });

        /**
         * 取消编辑(回到初始状态)
         */
        $('.layui-table').on('click', '.cancel-btn', function(){
            let _parent = $(this).parents('tr');
            let _prev = $(this).parent().prev();
            let _dynamics = _parent.find('.dynamic');
            let _stables = _parent.find('.stable');

            sfUploader.destroy();

            _dynamics.attr('type', 'hidden');
            _dynamics.css({display: 'none'});
            _stables.css({display: 'block'});
            $(this).parent().addClass('sr-only');
            _prev.removeClass('sr-only');
        });

        /**
         * 保存按钮（保存修改后的配置并刷新当前页面）
         */
        $('.layui-table').on('click', '.save-btn', function(){
            let _tr = $(this).parents('tr');
            let tmpData = new Object();

            tmpData.id = _tr.find('input[name=id]').val();
            tmpData.customer_name = _tr.find('input[name=customer_name]').val();
            tmpData.issue_ids = _tr.find('input[name=issue_ids]').val();
            tmpData.years = _tr.find('input[name=years]').val();
            tmpData.feedback = _tr.find('input[name=feedback]').val();
            if( attachmentList.length > 0 ){
                tmpData.attachment = JSON.stringify(attachmentList);
            }

            $.ajax({
                url: ThinkPHP['AJAX'] + '/RMA/saveEditData',
                type: 'POST',
                data: tmpData,
                dataType: 'json',
                beforeSend: function(){
                    layer.load(2,{shade:[0.8,'#fff']});
                },
                success: function(response){
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

        /**
         * 添加送样客诉（新增表格栏位）
         */
        $('.add-column').click(function(){
            let tableStructure = "<tr>" +
                "<td>--</td>" +
                "<td><input type='text' class='layui-input' name='customer_name' placeholder='客诉名称'></td>" +
                "<td><input type='text' class='layui-input' name='issue_ids' placeholder='问题点'></td>" +
                "<td><input type='text' class='layui-input' name='years' placeholder='年份'></td>" +
                "<td><input type='text' class='layui-input' name='feedback' placeholder='反馈'></td>" +
                "<td>--</td>" +
                "<td>" +
                    "<div class='edit-active layui-btn-group'>" +
                    "<button type='button' class='layui-btn layui-btn-primary save-btn-a' title='保存'><i class='icon-ok'></i></button>" +
                    "<button type='button' class='layui-btn layui-btn-primary cancel-btn-a' title='取消'><i class='icon-remove'></i></button>" +
                    "</div>" +
                "</td>" +
            "</tr>";
            $('.layui-table tbody').append(tableStructure);
            form.render();
        });

        /**
         * 取消新增
         */
        $('.layui-table').on('click', '.cancel-btn-a', function(){
            $(this).parents('tr').remove();
        });

        /**
         * 新增送样客诉
         */
        $('.layui-table').on('click', '.save-btn-a', function(){
            let _tr = $(this).parents('tr');
            let tmpData = new Object();

            tmpData.customer_name = _tr.find('input[name=customer_name]').val();
            tmpData.issue_ids = _tr.find('input[name=issue_ids]').val();
            tmpData.years = _tr.find('input[name=years]').val();
            tmpData.feedback = _tr.find('input[name=feedback]').val();

            $.ajax({
                url: ThinkPHP['AJAX'] + '/RMA/addSatisfaction',
                type: 'POST',
                data: tmpData,
                dataType: 'json',
                beforeSend: function(){
                    layer.load(2,{shade:[0.8,'#fff']});
                },
                success: function(response){
                    if( response.flag > 0 ){
                        layer.msg(response.msg, { icon : 1,time : 2000 });
                        setTimeout(function(){
                            location.replace(location.href);
                        },2000);
                    }else{
                        layer.closeAll();
                        layer.msg(response.msg, { icon : 2,time : 2000 });
                    }
                }
            });
        });

    });

});