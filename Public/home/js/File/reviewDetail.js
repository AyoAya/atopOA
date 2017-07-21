/**
 * Created by GCX on 2017/7/13.
 */
$(function(){


    // 监听滚动，如果添加处理日志存在并且出现返回顶部按钮则将添加日志按钮向上调整
    $('#content').scroll(function(){

        if( $('.solo_order') ){
            if( $('#scrollBackTop').hasClass('sr-only') ){
                $('.solo_order').css({bottom : '60px'});
            }else{
                $('.solo_order').css({bottom : '122px'});

            }
        }

    });

    layui.use(['form','layer'],function(){

        var form = layui.form(),
            layer = layui.layer;

        form.on('submit(submit)',function( data ){


            $.ajax({
                url : ThinkPHP['AJAX'] + '/File/saveDetail',
                dataType : 'json',
                type : 'POST',
                data : data.field,
                beforeSend : function(){
                    layer.load(2, { shade : [0.5,'#fff'] });
                },
                success : function( response ){
                    if( response.flag > 0 ){
                        $('.solo_order').css('display','none');
                        layer.msg(response.msg,{icon:1,time:2000});
                        setTimeout(function(){
                            location.reload();
                        },2000)
                    }else{
                        layer.msg(response.msg,{icon:2,time:2000});
                        layer.close();
                    }
                }


            });

            return false;

        });



        // 撤回
        form.on('submit(rollback)',function( data ){

            layer.confirm('确定撤回？', {
                btn: ['撤回','取消'] //按钮
            }, function(){
                $.ajax({
                    url : ThinkPHP['AJAX'] + '/File/rollbackDetail',
                    dataType : 'json',
                    type : 'POST',
                    data : data.field,
                    beforeSend : (function(){
                        layer.load(2, { shade : [0.5,'#fff'] });
                    }),
                    success : function( response ){
                        if( response.flag > 0 ){

                            layer.msg(response.msg,{icon:1,time:2000})
                            location.reload();

                        }else{
                            layer.msg(response.msg,{icon:2,time:2000})
                            location.reload();
                        }
                    }


                });
            });

            return false;



        });

    });





})