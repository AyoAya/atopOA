/**
 * Created by Fulwin on 2017/3/30.
 */
$(function(){

    layui.use(['form','layer'], function(){
        var form = layui.form(),
            layer = layui.layer;
        form.on('submit(ProductEditSubmit)', function( data ){
            $.ajax({
                url : ThinkPHP['AJAX'] + '/Product/edit',
                type : 'POST',
                data : data.field,
                dataType : 'json',
                beforeSend: function(){
                    layer.load(2,{shade:[0.8,'#fff']});
                },
                success : function(response){
                    if( response.flag > 0 ){
                        layer.msg(response.msg,{icon:1,time:2000});
                        setTimeout(function(){
                            location.replace(location.href);
                        },2000);
                    }else{
                        layer.msg(response.msg,{icon:2});
                    }
                }
            });
            return false;
        });

    });

});
