/**
 * Created by Fulwin on 2017/3/30.
 */
$(function(){

    layui.use(['form','layer'], function(){
        var form = layui.form(),
            layer = layui.layer;
        form.on('submit(productSubmit)', function( data ){
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


        $('.del-btn').click(function(){
            let del_id = $(this).attr('del-id');
            layer.confirm('您确定要删除该条产品吗？', {
                btn: ['确定', '取消']
            }, function(index, layero){
                $.ajax({
                    url: ThinkPHP['AJAX'] + '/Product/delete',
                    type: 'POST',
                    data: {
                        id: del_id
                    },
                    dataType: 'json',
                    success: function(response){
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
            }, function(index){

            })
        });


    });

});
