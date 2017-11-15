/**
 * Created by Fulwin on 2017/9/1.
 */
layui.use(['form', 'jquery', 'layer'], function(){
    // init layui components.
    var form = layui.form(),
        $ = layui.jquery,
        layer = layui.layer;

    // 获取页面初始化时选中文件类型的描述信息
    var fileTypeId = $('#FileType dl dd.layui-this').attr('lay-value');
    $.post(ThinkPHP['AJAX'] + '/File/getFileTypeDescription', {
        fileTypeId: fileTypeId
    }, function(response){
        $('p.tswb span').text(response.msg);
    });

    // 监听文件类型改变
    form.on('select(fileType)', function(data){
        $.post(ThinkPHP['AJAX'] + '/File/getFileTypeDescription', {
            fileTypeId: data.value
        }, function(response){
            $('p.tswb span').text(response.msg);
        });
    });

    form.on('select(HowToApply)', function(data){
        if( data.value == 'manual' ){
            $('#FileNumberWrapper').removeClass('sr-only');
            $('#FileTypeWrapper').addClass('sr-only');
            $('p.tswb').addClass('sr-only');
            $('p.ts-wb').removeClass('sr-only');
        }else{
            $('#FileNumberWrapper').addClass('sr-only');
            $('#FileTypeWrapper').removeClass('sr-only');
            $('p.tswb').removeClass('sr-only');
            $('p.ts-wb').addClass('sr-only');
        }
    });

    // 监听提交
    form.on('submit(applySubmit)', function(data){
        console.log(data.field.filenumber.length);
        if( data.field.apply == 'manual' ){
            data.field.filenumber = $.trim(data.field.filenumber);
            if( data.field.filenumber == '' ){
                layer.msg('请输入文件编号');
                return false;
            }
            if( !/^AP-[A-Z\-?0-9]+$/.test(data.field.filenumber) ){
                layer.msg('文件编号格式错误');
                return false;
            }
            if( data.field.filenumber.length > 13 ){
                layer.msg('文件号过长，请控制在12位或以下');
                return false;
            }
        }
        $.post(ThinkPHP['AJAX'] + '/File/apply', data.field, function(response){
            console.log(response);
            if( response.flag ){
                layer.msg(response.msg,{icon : 1,time : 1000});
                setTimeout(function(){
                    location.replace(location.href);
                }, 1000);
            }else{
                layer.msg(response.msg,{icon : 2,time : 1000});
            }
        })
        return false;
    });


});