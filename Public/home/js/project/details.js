/**
 * Created by Fulwin on 2017/2/16.
 */

$(function(){

    // 归档文件：删除文档
    $('.document-unlink-btn').click(function(){
        var _path = $(this).prev().val();
        var _id = $(this).next().val();
        $.ajax({
            url : ThinkPHP['AJAX'] + '/Project/removeDocument',
            type : 'POST',
            data : {
                id : _id,
                path : _path
            },
            dataType : 'json',
            success : function(response){
                if( response.flag == 1 ){
                    $('#MessageText').html('<p><i class="icon-ok-sign text-success"></i>&nbsp;' + response.msg + '</p>' );
                    $('#MessageModal').modal('show');
                    setTimeout(function(){
                        location.reload();
                    },1000);
                }else{
                    $('#MessageText').html('<p><i class="icon-remove-sign text-danger"></i>&nbsp;' + response.msg + '</p>' );
                    $('#MessageModal').modal('show');
                    setTimeout(function(){
                        $('#MessageModal').modal('hide');
                    },1000);
                }
            }
        });
    });

    // 评论区：跳转楼层
    var floorTotal = $('.message-board').children('.media').length; //获取到总楼层数
    if( floorTotal > 0 ){   //当楼层数量大于指定值时才显示跳转楼层dom
        $('#jumpFloor').removeClass('sr-only');
    }
    $('.jump-info b').text(floorTotal);

    // 讨论区：发表提交
    $('#submit-discuss-content').click(function(){
        var _id = $(this).attr('discuss_id');
        var context = ue.getContent();//获取到编辑器的内容
        var cc_list = '';
        if( $('#ccPersonList').children().length > 0 ){
            $('#ccPersonList li').each(function(i){
                cc_list += $(this).attr('user-id') + ',';
            });
            cc_list = cc_list.substring(0,cc_list.length-1);
        }
        if( $.trim(context) != '' ){//如果内容为空不允许提交
            $.ajax({
                url : ThinkPHP['AJAX'] + '/Project/addDiscussContext',
                type : 'POST',
                data : {
                    discuss_id : _id,
                    discuss_context : context,
                    cc_list : cc_list,
                    floor : (floorTotal+1)
                },
                dataType : 'json',
                beforeSend : function(){
                    $('#loading').removeClass('sr-only');
                },
                success : function(response){
                    if( response.flag == 1 ){
                        $('#loading').addClass('sr-only');
                        $('#MessageText').html('<p><i class="icon-ok-sign text-success"></i>&nbsp;' + response.msg + '</p>' );
                        $('#MessageModal').modal('show');
                        setTimeout(function(){
                            location.reload();
                        },1000);
                    }else{
                        $('#loading').addClass('sr-only');
                        $('#MessageText').html('<p><i class="icon-remove-sign text-success"></i>&nbsp;' + response.msg + '</p>' );
                        $('#MessageModal').modal('show');
                        setTimeout(function(){
                            $('#MessageModal').modal('hide');
                        },1000);
                    }
                }
            });
        }else{
            layer.msg('请输入点内容吧');
        }
    });

    $('#logModal').modal({
        backdrop: true,
        show: false
    });

    // 点击激活modal
    $('.modal-btn-focus').click(function(){
        $('#logModal').modal('show');
    });

    // 项目计划：点击编辑激活修改
    $('.plan-table').on('click','.editor-btn',function(){
        // 获取到当前点击元素的父节点tr/td
        var _td = $(this).parent();
        var _tr = $(this).parent().parent();
        var plan_project = $(this).attr('plan_project');
        var plan_node = $(this).attr('plan_node');
        // 根据当前点击按钮的步骤值的大小判断picker插件显示的位置
        var _step = $(this).attr('step');
        var picker_position = '';
        //当步骤数大于3时显示在上方
        if( _step > 3 ){
            picker_position = 'top-left';
        }else{
            picker_position = 'bottom-left';
        }
        _td.html('<div class="btn-group">' +
            '<button type="button" class="btn btn-default btn-sm td-cancel-btn">' +
                '<i class="icon-remove"></i></button>' +
                '<button type="button" class="btn btn-default btn-sm td-save-btn" plan_project="' + plan_project + '" plan_node="' + plan_node + '">' +
                '<i class="icon-ok"></i></button></div>');
        // 获取到对应的栏位数据
        var start_time_text = _tr.find('.start_time').text();
        var complete_time_text = _tr.find('.complete_time').text();
        var comments_text = _tr.find('.comments').html();
        comments_text = comments_text.replace(/<br>/g,'\r\n');//替换<br>标签为\r\n
        var prev_td = _td.prev().css('height');

        //console.log(comments_text);
        // 将对应的栏位数据填充到input
        _tr.find('.start_time').html('<input type="text" name="start_time" readonly class="form-control input-sm start_time_picker" value="' + start_time_text + '">');
        _tr.find('.complete_time').html('<input type="text" name="complete_time" readonly class="form-control input-sm complete_time_picker" value="' + complete_time_text + '">');
        _tr.find('.comments').html('<textarea style="height: '+ prev_td +';" type="text" name="comments" class="form-control input-sm comments_textarea">' + comments_text + '</textarea>');
        // 初始化时间组件
        $('.start_time_picker,.complete_time_picker').datetimepicker({
            language:  'zh-CN',
            format:'yyyy-mm-dd',
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            pickerPosition: picker_position,
            startView: 2,
            minView: 2,
            forceParse: 0
        });
    });

    // 项目计划：点击取消回退到正常状态
    $('.plan-table').on('click','.td-cancel-btn',function(){
        // 获取到当前点击元素的父节点tr/td
        var _td = $(this).parent().parent();
        var _tr = $(this).parent().parent().parent();
        _td.html('<button class="editor-btn btn btn-default btn-sm"><i class="icon-edit"></i></button>');
        // 获取到原始数据
        var start_time_text = _tr.find('.start_time').attr('originally');
        var complete_time_text = _tr.find('.complete_time').attr('originally');
        var comments_text = _tr.find('.comments').attr('originally');
        //console.log(comments_text);
        // 恢复到原始数据
        _tr.find('.start_time').html(start_time_text);
        _tr.find('.complete_time').html(complete_time_text);
        _tr.find('.comments').html(comments_text);
    });


    // 项目计划：点击保存提交当前修改数据
    $('.plan-table').on('click','.td-save-btn',function(){
        var plan_project = $(this).attr('plan_project');
        var plan_node = $(this).attr('plan_node');
        var _parent_tr = $(this).parents('tr');
        //var plan_start_time = _parent_tr.find('input[name=plan_start_time]').val();
        //var plan_complete_time = _parent_tr.find('input[name=plan_complete_time]').val();
        var start_time = _parent_tr.find('input[name=start_time]').val();
        var complete_time = _parent_tr.find('input[name=complete_time]').val();
        var comments = _parent_tr.find('textarea[name=comments]').val();
        $.ajax({
            url : ThinkPHP['AJAX'] + '/Project/plan',
            type : 'POST',
            data : {
                plan_project : plan_project,
                plan_node : plan_node,
                start_time : start_time,
                complete_time : complete_time,
                comments : comments
            },
            dataType : 'json',
            beforeSend : function(){
                $('#loading').removeClass('sr-only');
            },
            success : function(response){
                if( response.flag > 0 ){
                    $('#loading').addClass('sr-only');
                    $('#MessageText').html('<p><i class="icon-ok-sign text-success"></i>&nbsp;' + response.msg + '</p>' );
                    $('#MessageModal').modal('show');
                    setTimeout(function(){
                        location.reload();
                    },1000);
                }else{
                    $('#loading').addClass('sr-only');
                    $('#MessageText').html('<p><i class="icon-remove-sign text-danger"></i>&nbsp;错误</p>' );
                    $('#MessageModal').modal('show');
                    setTimeout(function(){
                        $('#MessageModal').modal('hide');
                    },1000);
                }
            }
        });
    });

    var upOption = {    // Huploadify默认配置
        auto : true,
        //提交到服务器
        uploader : ThinkPHP['AJAX'] + '/Project/document',
        formData : {
            gate_num : 1,   // 动态值，每当切换gate，重新配置该参数
            project_id : $('#documentUploadProjectId').val()
        },
        //定义显示在默认按钮上的文本
        buttonText : '上传文件',
        //设置上传按钮的宽高
        fileObjName : 'Filedata',
        //设置上传按钮的宽高
        width : 120,
        height : 40,
        //设置值为false时，一次只能选中一个文件
        multi : false,
        //图片类型
        fileTypeDesc : '图片类型',
        //过滤文件格式
        fileTypeExts : '*.rar;*.zip;*.txt;*.pdf;*.doc;*.docx;*.xls;*.xlsx;*.ppt;*.jpg;*.jpeg;*.gif;*.png',
        //限制文件大小5m
        fileSizeLimit : 5242880,
        onUploadStart : function(){
            $('#loading').removeClass('sr-only');
        },
        //上传成功之后执行回调函数(file:返回文件信息，data：返回服务器回调信息，response：返回真或假)
        onUploadSuccess : function(file,data){
            var obj = $.parseJSON(data);
            if(obj){
                if( obj.flag == 1 ){
                    $('#loading').addClass('sr-only');
                    $('#MessageText').html('<p><i class="icon-ok-sign text-success"></i>&nbsp;' + obj.msg + '</p>' );
                    $('#MessageModal').modal('show');
                    setTimeout(function(){
                        location.reload();
                    },1000);
                }else{
                    $('#MessageText').html('<p><i class="icon-remove-sign text-success"></i>&nbsp;' + obj.msg + '</p>' );
                    $('#MessageModal').modal('show');
                    setTimeout(function(){
                        $('#MessageModal').modal('hide');
                    },1000);
                }
            }
        }
    };

    // 获取Huoloadify实例
    var up = $('#fileUpload').Huploadify(upOption);

    // 归档文件：切换Gate
    $('#gateList li').click(function(){
        up.destroy();   // 每当切换gate时将当前Huploadify实例销毁
        var gate = $(this).attr('gate');
        var gateMilestone = $(this).text();
        var gateNumNode = $('#gateNum');
        var documentProjectGate = $('#documentProjectGate');
        gateNumNode.text(gateMilestone);
        upOption.formData.gate_num = gate;  // 重写Huploadify配置
        up = $('#fileUpload').Huploadify(upOption); // 选择好gate之后重新生成Huploadify实例
    });

    // 当选择抄送人模态框被打开后执行
    /*$('#CCpeopleModal').on('show.bs.modal', function(e){

    });*/

    // 初始化layui组件
    layui.use(['layer','form'], function(){
        var layer = layui.layer,
            form = layui.form();
        //监听部门改变
        form.on('select(change)', function( data ){
            $.ajax({
                url : ThinkPHP['AJAX'] + '/Project/ajaxChangeDepartment',
                type : 'POST',
                data : {
                    dpmt : data.value
                },
                dataType : 'json',
                success : function(response){
                    var _html = '';
                    if( response.flag > 0 ){
                        for(key in response.data){
                            _html += '<li email="'+response.data[key].email+'" user-id="'+response.data[key].id+'" title="'+response.data[key].nickname+'">'+response.data[key].nickname+'</li>';
                        }
                        $('#cc-person-list').html(_html);
                    }else{
                        $('#cc-person-list').html('<center><span class="empty-cc"><i class="icon-info-sign"></i>&nbsp;'+response.msg+'</span></center>');
                    }
                }
            });
        });
    });

    // 评论区：跳转楼层
    $('#jump').click(function(){
        var floor = $(this).prev().val();
        if( $.trim(floor) == '' ){
            layer.msg('请输入跳转的楼层');
            return false;
        }else{
            var regular = /^[1-9]([0-9]{1,})?$/;
            if( !regular.test(floor) ){
                layer.msg('请输入正确的楼层');
                return false;
            }else{
                if( floor > floorTotal ){
                    layer.msg('超过总楼层数');
                    return false;
                }else{
                    var top = $('div[name=floor'+floor+']').offset().top - 90;   //获取到跳转楼层到页面顶部的距离
                    $('#content-box').mCustomScrollbar('scrollTo',top);
                }
            }
        }
    });


    // 讨论区：打开添加抄送人模态框
    $('#add-cc-people').click(function(){
        $('#CCpeopleModal').modal('show');
        $('#content-box').mCustomScrollbar('disable');
    });

    $('#CCpeopleModal').on('hidden.bs.modal', function (e) {
        $('#content-box').mCustomScrollbar('update');
    })

    // 讨论区：点击抄送人添加到抄送人员列表
    $('#cc-person-list').on('click','li',function(){
        $('#has-been-box').removeClass('sr-only');
        var flag = true;
        var email = $(this).attr('email');
        var nickname = $(this).text();
        var user_id = $(this).attr('user-id');
        var _html = '<li email="'+email+'" user-id="'+user_id+'">'+nickname+'<div class="remove-cc"><i class="icon-remove"></i></div></li>';
        var child_length = $('#addList').children('li').length;
        if( child_length > 0 ){
            $('#addList li').each(function(index){
                if( user_id == $(this).attr('user-id') ){
                    flag = false;
                    return false;
                }
            });
        }
        if(!flag){
            layer.msg(nickname+'已存在',{offset:'260px'});
        }else{
            $('#addList').append(_html);
        }
    });

    // 讨论区：点击删除抄送人
    $('#addList').on('click','li',function() {
        var user_id = $(this).attr('user-id');
        if( $('#ccPersonList i').children('li').length > 0 ){
            $('#ccPersonList i li').each(function(){
                if( user_id == $(this).attr('user-id') ){
                    $(this).remove();
                }
            });
        }
        $(this).remove();
        if( $('#addList').children('li').length == 0 ){
            if( !$('#has-been-box').hasClass('sr-only') ){
                $('#has-been-box').addClass('sr-only');
            }
        }
    });

    // 讨论区：确定添加抄送人
    $('#done-cc-list').click(function(){
        var _length = $('#addList').children('li').length;
        if( _length > 0 ){  // 如果抄送人不为空，则将所有的抄送人的id拼接为字符串保存
            var idList = '';
            var _html = '';
            $('#addList li').each(function(){
                _html += '<li email="'+$(this).attr('email')+'" user-id="'+$(this).attr('user-id')+'">'+$(this).text()+'<i class="icon-remove-sign"></i></li>\r\n'
                idList += $(this).attr('user-id') + ',';
            });
            $('#ccPersonList i').html(_html);
            $('#cc_list').val(idList);
        }
        $('#CCpeopleModal').modal('hide');
    });

    // 讨论区：删除抄送人
    $('#ccPersonList i').on('click','li i',function(){
        var _parent = $(this).parent();
        var user_id = _parent.attr('user-id');
        if( $('#addList').children('li').length > 0 ){
            $('#addList li').each(function(){
                if( user_id == $(this).attr('user-id') ){
                    $(this).remove();
                }
            });
        }
        $(this).remove();
        if( $('#addList').children('li').length == 0 ){
            if( !$('#has-been-box').hasClass('sr-only') ){
                $('#has-been-box').addClass('sr-only');
            }
        }
        _parent.remove();
    })

    // 讨论区：重置
    $('#reset-cc-people').click(function(){
        $(this).parent().find('.not-default').html('');
        $('#addList').html('');
        if( $('#addList').children('li').length == 0 ){
            if( !$('#has-been-box').hasClass('sr-only') ){
                $('#has-been-box').addClass('sr-only');
            }
        }
    });



});

// 当页面加载完成后，检查地址栏是否存在楼层，如果存在则直接锁定到该楼层
window.onload = function(){
    var floorSize = $('.message-board').children('.media').length; //获取到总楼层数
    if( $('#floorNum') ){
        var floorNum = $('#floorNum').val();
        if( $.trim(floorNum) != '' ){
            var regular = /^[1-9]([0-9]{1,})?$/;
            if( regular.test(floorNum) ){
                if( floorNum <= floorSize ){
                    var top = $('div[name=floor'+floorNum+']').offset().top - 90;   //获取到跳转楼层到页面顶部的距离
                    $('#content-box').scroll(top)
                }
            }
        }
    }
};

