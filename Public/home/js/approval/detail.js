/**
 * Created by Fulwin on 2017/6/21.
 */
$(function(){

    layui.use(['form','layer','upload','element','layedit'], function(){
        var form = layui.form(),
            layedit = layui.layedit,
            element = layui.element(),
            layer = layui.layer;

        layedit.build('editor', {
            tool: [
                'strong' //加粗
                ,'italic' //斜体
                ,'underline' //下划线
                ,'del' //删除线

                ,'|' //分割线

                ,'left' //左对齐
                ,'center' //居中对齐
                ,'right' //右对齐

                ,'|' //分割线

                ,'link' //超链接
                ,'unlink' //清除链接
                ,'face' //表情
                ,'image' //插入图片
            ]
        });

    });




});