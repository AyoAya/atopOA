/**
 * Created by Fulwin on 2017/3/30.
 */
$(function(){

    layui.use('form', function(){
        var form = layui.form(),
            layer = layui.layer;
        form.on('submit(productSubmit)', function( data ){
            console.log(data.field);
            $.ajax({
                url : ThinkPHP['AJAX'] + '/Product/add',
                type : 'POST',
                data : data.field,
                dataType : 'json',
                success : function(response){
                    if( response.flag > 0 ){
                        layer.msg(response.msg,{icon:1});
                        $('.layui-form')[0].reset();    //提交成功后重置表单
                    }else{
                        layer.msg(response.msg,{icon:2});
                    }
                }
            });
            return false;
        });
    });

    // 为防止mCustomScrollbar插件优先级问题，档下拉框被展开时禁用该插件，页面任何区域被点击时再激活
    /*$(document).on('click',function(){
        if( $('.layui-form-select').hasClass('layui-form-selected') ){
            $('#content-box').mCustomScrollbar('disable');
        }else{
            $('#content-box').mCustomScrollbar('update');
        }
    });*/

});
