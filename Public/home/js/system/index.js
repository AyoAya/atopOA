/**
 * Created by Fulwin on 2016/11/21.
 */

$(function(){

    //alert(ThinkPHP['UPLOADIFY_CONFIG_FILETYPEEXTS']);

    //是否开启后台登陆验证码
    $('.config-verify-box .verify-switch,.config-page-box .verify-switch').click(function(){
        var _this = $(this);
        var value = $(this).attr('value');
        var key = $(this).attr('key');
        var flag = $(this).attr('flag');
        if(value==1){
            value = 0;
        }else{
            value = 1;
        }
        $.post(ThinkPHP['AJAX'] + '/System/rewriteConfiguration',{key:key,value:value},function(data){
            if(data){
                _this.addClass('active');
                _this.attr('value','1');
                if(_this.attr('flag') && _this.attr('flag')=='verify'){
                    $('.config-verify-info').removeClass('sr-only');
                }
            }else{
                _this.removeClass('active');
                _this.attr('value','0');
                if(_this.attr('flag') && _this.attr('flag')=='verify'){
                    $('.config-verify-info').addClass('sr-only');
                }
            }
        },'json');

    });

    //自定义验证规则
    $.validator.addMethod("fileExts", function(value, element) {
        var tel = /^(\*\.[a-z]+;)+$/;
        return this.optional(element) || (tel.test(value));
    }, "格式不正确");

    //修改文件上传配置
    $('#uploadConfigForm').validate({
        focusInvalid : true,
        rules : {
            FileSizeLimit : {
                required : true,
                min : 1,
                max : 1000,
            },
            UploadLimit : {
                required : true,
                min : 1,
                max : 8,
            },
            FileTypeExts : {
                required : true,
                fileExts : true,
            },
        },
        messages : {
            FileSizeLimit : {
                required : '文件大小限制不能为空',
                min : '最小不能低于1M',
                max : '最大不能高于1000M',
            },
            UploadLimit : {
                required : '文件上传最大数量为空',
                min : '最小数量不能低于1个',
                max : '最大数量不能高于8个',
            },
            FileTypeExts : {
                required : '文件上传格式限制不能为空',
                fileExts : '格式错误',
            },
        },
        ignore : "",
        errorElement : 'b',
        errorPlacement : function(error, element) {
            if(element.attr('name')!='FileTypeExts'){
                error.appendTo(element.parent().next());
            }else{
                error.appendTo(element.next());
            }
        },
        submitHandler : function(form){
            $(form).ajaxSubmit({
                url : ThinkPHP['AJAX'] + '/System/rewriteConfiguration',
                type : 'POST',
                data : {
                    FileSizeLimit : $('#uploadConfigForm input[name=FileSizeLimit]').val(),
                    UploadLimit : $('#uploadConfigForm input[name=UploadLimit]').val(),
                    FileTypeExts : $('#uploadConfigForm input[name=FileTypeExts]').val(),
                },
                dataType : 'json',
                success : function(response){
                    if(response){
                        $('.modal-message-success .message-text').text('修改成功');
                        $('.modal-message-success').show();
                        $('#message-modal').modal('show');
                        setTimeout(function(){
                            $('#message-modal').modal('hide');
                            $('.modal-message-success').hide();
                        },800);
                    }else{
                        $('.modal-message-error .message-text').text('修改失败');
                        $('.modal-message-error').show();
                        $('#message-modal').modal('show');
                        setTimeout(function(){
                            $('#message-modal').modal('hide');
                            $('.modal-message-error').hide();
                        },800);
                    }
                }
            });
        },
    });

    //分页配置信息
    $('#pageConfigForm').validate({
        focusInvalid : true,
        rules : {
            pageLimitSize : {
                required : true,
                min : 10,
                max : 50,
            },
        },
        messages : {
            pageLimitSize : {
                required : '每页显示数量不能为空',
                min : '最小不能低于10条',
                max : '最大不能高于50条',
            },
        },
        ignore : "",
        errorElement : 'b',
        errorPlacement : function(error, element) {
            error.appendTo(element.parent().next());
        },
        submitHandler : function(form){
            $(form).ajaxSubmit({
                url : ThinkPHP['AJAX'] + '/System/rewriteConfiguration',
                type : 'POST',
                data : {
                    value : $('#pageConfigForm input[name=pageLimitSize]').val(),
                },
                dataType : 'json',
                success : function(response){
                    if(response){
                        $('.modal-message-success .message-text').text('修改成功');
                        $('.modal-message-success').show();
                        $('#message-modal').modal('show');
                        setTimeout(function(){
                            $('#message-modal').modal('hide');
                            $('.modal-message-success').hide();
                        },800);
                    }else{
                        $('.modal-message-error .message-text').text('修改失败');
                        $('.modal-message-error').show();
                        $('#message-modal').modal('show');
                        setTimeout(function(){
                            $('#message-modal').modal('hide');
                            $('.modal-message-error').hide();
                        },800);
                    }
                }
            });
        },
    });

    //自定义验证规则
    $.validator.addMethod("cookieRule", function(value, element) {
        var tel = /^[a-zA-Z0-9\.\^\+\-\*\/]+$/;
        return this.optional(element) || (tel.test(value));
    }, "格式不正确");

    //Cookie密钥
    $('#cookieConfigForm').validate({
        focusInvalid : true,
        rules : {
            cookieKey : {
                required : true,
                minlength : 6,
                maxlength : 40,
                cookieRule : true,
            },
        },
        messages : {
            cookieKey : {
                required : 'cookie密钥不能为空',
                minlength : 'cookie密钥不能小于6位',
                maxlength : 'cookie密钥不能大于40位',
                cookieRule : '格式错误',
            },
        },
        ignore : "",
        errorElement : 'b',
        errorPlacement : function(error, element) {
            element.parent().next().html('');
            error.appendTo(element.parent().next());
        },
        submitHandler : function(form){
            $(form).ajaxSubmit({
                url : ThinkPHP['AJAX'] + '/System/rewriteConfiguration',
                type : 'POST',
                data : {
                    value : $('#cookieConfigForm input[name=cookieKey]').val(),
                },
                dataType : 'json',
                success : function(response){
                    if(response){
                        $('.modal-message-success .message-text').text('修改成功');
                        $('.modal-message-success').show();
                        $('#message-modal').modal('show');
                        setTimeout(function(){
                            $('#message-modal').modal('hide');
                            $('.modal-message-success').hide();
                        },800);
                    }else{
                        $('.modal-message-error .message-text').text('修改失败');
                        $('.modal-message-error').show();
                        $('#message-modal').modal('show');
                        setTimeout(function(){
                            $('#message-modal').modal('hide');
                            $('.modal-message-error').hide();
                        },800);
                    }
                }
            });
        },
    });

});













