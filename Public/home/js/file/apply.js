/**
 * Created by Fulwin on 2017/9/1.
 */
layui.use(['form', 'jquery', 'layer'], function(){
    // init layui components.
    var form = layui.form(),
        $ = layui.jquery,
        layer = layui.layer;

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