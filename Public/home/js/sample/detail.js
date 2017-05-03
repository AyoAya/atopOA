$(function(){

    //如果有留言就显示
    $('.myTooltip').hover(function(){
        $(this).find('.popover-box').removeClass('sr-only');
    },function(){
        $(this).find('.popover-box').addClass('sr-only');
    });

    //
    layui.use(['layer','form'], function() {
        var layer = layui.layer;
        var form = layui.form();
        form.on('select(operation)', function( data ){
            var _value = data.value;    //获取到当前选中value

            if( _value == 'push' ){
                if( $('#operating-person').hasClass('sr-only') ){
                    $('#operating-person').removeClass('sr-only');
                }
            }else{
                if( !$('#operating-person').hasClass('sr-only') ){
                    $('#operating-person').addClass('sr-only');
                }
            }

        });







        form.on('submit(formDemo)',function( data ){

            $.ajax({
                url : ThinkPHP['AJAX'] + '/Sample/addSampleLog',
                type : 'post',
                dataType : 'JSON',
                data : data.field,
                beforeSend : function(){
                    layer.load(2, { shade : [0.5,'#fff'] });
                },
                success : function( response ){
                    if (response.flag > 0){
                        layer.msg( response.msg, { icon : 1, time : 2000 } );
                        setTimeout(function(){
                            location.reload();
                        },2000);
                    }else{

                    }
                }
            })


            return false; //阻止表单跳转。
        })


    })


});