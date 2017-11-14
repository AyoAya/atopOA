/**
 * Created by GCX on 2017/10/30.
 */
$(function(){

    layui.use(['form','layer'],function(){
        var layer = layui.layer,
            form = layui.form();

        // 监听滚动，如果添加处理日志存在并且出现返回顶部按钮则将添加日志按钮向上调整
        $('#content').scroll(function(){

            if( $('#ReviewBtn') ){
                if( $('#scrollBackTop').hasClass('sr-only') ){
                    $('#ReviewBtn').css({bottom : '60px'});
                }else{
                    $('#ReviewBtn').css({bottom : '122px'});

                }
            }

        });

        form.on('submit(submit)',function( data ){


            $.ajax({
                url : ThinkPHP['AJAX'] + '/Bulletin/detail',
                type : 'POST',
                data : data.field,
                dataType : 'json',
                beforeSend : function(){
                    layer.load(2, { shade : [0.5,'#fff'] });
                },
                success : function ( response ) {
                    if( response.flag > 0){
                        layer.msg(response.msg,{icon:1,time:2000});
                        setTimeout(function(){
                            location.reload();
                        },2000);
                    }else{
                        layer.msg(response.msg,{icon:2,time:2000});
                        setTimeout(function(){
                            location.reload();
                        },2000)
                    }
                }

            });


            return false;





        })

    })

})