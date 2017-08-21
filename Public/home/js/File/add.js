/**
 * Created by GCX on 2017/7/23.
 */
$(function(){

    layui.use(['form','layer'],function(){

        var layer = layui.layer,
            form = layui.form();


        form.on('submit(submit)',function ( data ) {

            $.ajax({
                url : ThinkPHP['AJAX'] + '/File/add',
                type : 'POST',
                dataType : 'json',
                data : data.field,

                success : function( response ){

                    if(response.flag > 0){
                        layer.msg(response.msg,{icon:1,time:2000})
                        setTimeout(function(){
                            location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/File';
                        },2000)
                    }else{
                        layer.msg(response.msg,{icon:2,time:2000})
                    }
                }

            })

            return false;

        })

    })

})