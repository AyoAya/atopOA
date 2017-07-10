/**
 * Created by Fulwin on 2017/7/7.
 */
$(function(){

    layui.use(['form','layer'], function(){
        var form = layui.form(),
            layer = layui.layer;

        $('#content').scroll(function(){

            var top = $('#content').scrollTop();
            setTimeout(function(){
                $('.sf-box-l').css({top: (top+15)+'px',bottom: '0px'});
            },200);

        });

        form.on('submit(SaticfactionSubmit)', function(data){

            $.ajax({
                url: ThinkPHP['AJAX'] + '/RMA/addSatisfaction',
                type: 'POST',
                data: data.field,
                dataType: 'json',
                beforeSend: function(){
                    layer.load(2,{shade:[0.8,'#fff']});
                },
                success: function(response){
                    if( response.flag ){
                        layer.msg(response.msg,{icon : 1,time : 2000});
                        setTimeout(function(){
                            location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/RMA/satisfaction'
                        },2000);
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