/**
 * Created by Fulwin on 2017/8/31.
 */
$(function(){

    layui.use(['layer'], function(){
        var layer = layui.layer;


        $('.gz-xq').click(function(){
            console.log('open modal');
            layer.open({
                type: 1,
                title: '规则详情 & 抄送列表',
                area: ['700px'],
                shadeClose: true,
                shade: ['0.5','#000'],
                content: $(this).next().html(),
            });
        });


    })

});