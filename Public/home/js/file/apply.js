/**
 * Created by Fulwin on 2017/9/1.
 */
layui.use(['form', 'jquery', 'layer'], function(){
    // init layui components.
    var form = layui.form(),
        $ = layui.jquery,
        layer = layui.layer;

    // 获取页面初始化时选中文件类型的描述信息
    var fileTypeId = $('.layui-form-select dl dd.layui-this').attr('lay-value');
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

    // 监听提交
    form.on('submit(applySubmit)', function(data){
        console.log(data.field);
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