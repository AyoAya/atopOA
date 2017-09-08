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

    // 自定义验证规则
    form.verify({
        name: [
            /^[a-zA-Z0-9]{1,20}$/,
            '规则名称自能是数字或字母 [不区分大小写]'
        ],
        length: [
            /^[1-9]([0-9]+)?$/,
            '长度格式不正确，只能输入数字'
        ],
        desc: function(value, item){
            if( value.length > 255 ){
                return '描述长度不能超过255个字符';
            }
        }
    });


    // 监听提交
    form.on('submit(addRuleSubmit)', function(data){
        console.log(data.field);
        $.post(ThinkPHP['AJAX'] + '/File/addRule', data.field, function(response){
            if( response.flag ){
                layer.msg(response.msg,{icon : 1,time : 1000});
                setTimeout(function(){
                    location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/File/fileRules';
                }, 1000);
            }else{
                layer.msg(response.msg,{icon : 2,time : 1000});
            }
        });
        return false;
    });

});