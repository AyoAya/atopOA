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


});