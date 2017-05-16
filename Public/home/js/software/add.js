/**
 * Created by Fulwin on 2017/5/16.
 */
$(function(){

    //初始化layui组件
    layui.use(['form','layer'], function(){
        var form = layui.form(),
            layer = layui.layer;

        //监听用户选择类型
        form.on('select(type)', function( data ){

            var mcu = $('.layui-form .mcu-box');

            if( data.value == 'firmware' ){
                mcu.css({display:'block'});
                mcu.find('.layui-input').attr('lay-verify','required');
            }else{
                mcu.css({display:'none'});
                mcu.find('.layui-input').removeAttr('lay-verify');
            }

        });

        //监听提交
        form.on('submit(add)', function( data ){



            return false;

        });



    });




});