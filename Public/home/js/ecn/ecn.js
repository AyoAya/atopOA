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

    // 编辑
    $('.edit-ecn-btn').click(function(){
        $('.preview-condition').addClass('sr-only');
        $('.edit-condition').removeClass('sr-only');
    });

    // 取消编辑
    $('.cancel-edit-btn').click(function(){
        $('.edit-condition').addClass('sr-only');
        $('.preview-condition').removeClass('sr-only');
        $('#content').scrollTop(0);
    });

});