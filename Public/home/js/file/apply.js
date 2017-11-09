/**
 * Created by Fulwin on 2017/9/1.
 */
layui.use(['form', 'jquery', 'layer'], function(){
    // init layui components.
    var form = layui.form(),
        $ = layui.jquery,
        layer = layui.layer;

    var manuals = ['PCB', 'PCBA', 'ES', 'ISO'];  // 需要手动录入编号的文件类型

    // 获取页面初始化时选中文件类型的描述信息
    var fileTypeId = $('#FileType dl dd.layui-this').attr('lay-value');
    $.post(ThinkPHP['AJAX'] + '/File/getFileTypeDescription', {
        fileTypeId: fileTypeId
    }, function(response){
        $('p.tswb span').text(response.msg);
    });

    // 监听文件类型改变
    form.on('select(fileType)', function(data){
        var currentOptionText = $(data.elem).find('option[value="'+ data.value +'"]').text();
        console.log(currentOptionText);
        // 如果选择的文件类型在需要手动录入编号的条件下则显示文件号输入框，反之则隐藏
        if( $.inArray(currentOptionText, manuals) >= 0 ){
            $('#FileNumberWrapper').removeClass('sr-only');
            if( currentOptionText == 'ES' ){
                $('#FileNumberWrapper input[name="filenumber"]').attr('placeholder', '例：ES-EL10-D38-01').val('');
            }else if( currentOptionText == 'ISO' ){
                $('#FileNumberWrapper input[name="filenumber"]').attr('placeholder', '例：AP-QI-0610F02').val('');
            }else if( currentOptionText == 'PCB' ){
                $('#FileNumberWrapper input[name="filenumber"]').attr('placeholder', '例：1.12.EL10-D38').val('');
            }else if( currentOptionText == 'PCBA' ){
                $('#FileNumberWrapper input[name="filenumber"]').attr('placeholder', '例：1.13.EL10-D38-A01').val('');
            }
        }else{
            $('#FileNumberWrapper').addClass('sr-only');
            $('#FileNumberWrapper input[name="filenumber"]').attr('placeholder', '文件编号').val('');
        }
        $.post(ThinkPHP['AJAX'] + '/File/getFileTypeDescription', {
            fileTypeId: data.value
        }, function(response){
            $('p.tswb span').text(response.msg);
        });
    });

    /*form.on('select(HowToApply)', function(data){
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
    });*/

    // 监听提交
    form.on('submit(applySubmit)', function(data){
        var dom = $('#FileType');
        var fileTypeText = dom.find('option[value="'+ data.field.id +'"]').text();
        if( $.inArray(fileTypeText, manuals) >= 0 ){
            data.field.apply = 'manual';
            data.field.type = fileTypeText;
            if( data.field.filenumber == '' ){
                layer.msg('请输入文件编号');
                return false;
            }
            // 如果是ISO或ES类型文件则检查文件标号格式的合法性
            if( fileTypeText == 'ISO' && !/^AP-[A-Z\-?0-9]+$/.test(data.field.filenumber) || fileTypeText == 'ES' && !/^ES-[A-Z\-?0-9]+$/.test(data.field.filenumber) ){
                layer.msg('文件编号格式错误');
                return false;
            }
            // 如果是PCB或PCBA类型文件则检查文件标号格式的合法性
            if( fileTypeText == 'PCB' && !/^1\.12[\.A-Z\-?0-9]+$/.test(data.field.filenumber) || fileTypeText == 'PCBA' && !/^1\.13[\.A-Z\-?0-9]+$/.test(data.field.filenumber) ){
                layer.msg('文件编号格式错误');
                return false;
            }
        }else{
            data.field.apply = 'system';
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