/**
 * Created by Fulwin on 2017/9/1.
 */
layui.use(['form', 'jquery', 'layer', 'layedit'], function(){
    // init layui components.
    var form = layui.form(),
        $ = layui.jquery,
        layer = layui.layer,
        layedit = layui.layedit;

    // 定义layedit编辑器配置
    var layeditOptions = {
        height: 150
    }

    // 实例化layedit编辑器
    var layeditDescription = layedit.build('layedit-description', layeditOptions);
    var layeditReason = layedit.build('layedit-reason', layeditOptions);

    // 添加文件
    $('.choose-btn').click(function(){
        layer.open({
            type: 1,
            id: 'LAYER_CONTAINER',
            title: '选择文件',
            area: ['1000px', '500px'],
            shade: [0.5, '#000'],
            shadeClose: true,
            content: $('.filelist-wrapper').html()
        })
    });

    // 编辑
    $('.edit-ecn-btn').click(function(){
        console.log('hide current node and show next node.');
        $('.preview-condition').addClass('sr-only');
        $('.edit-condition').removeClass('sr-only');
    });

    // 取消编辑
    $('.cancel-edit-btn').click(function(){
        console.log('hide current node and show prev node.');
        $('.edit-condition').addClass('sr-only');
        $('.preview-condition').removeClass('sr-only');
        $('#content').scrollTop(0);
    });

});