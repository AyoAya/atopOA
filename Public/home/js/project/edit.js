/**
 * Created by Fulwin on 2017/2/16.
 */
$(function(){

    var _index;
    // 点击打开选择人员模态框
    $('.table .open-modal').each(function(index){
        $(this).click(function(){
            //alert(index);
            // 获取到当前点击元素上一个节点的内容并打开moal弹出层，并修改弹出层title
            var _this = $(this);
            var _title = $(this).parent().prev().text();
            var _dpmt = $(this).attr('dpmt');
            console.log(_dpmt);
            $('#choosePersonBody ul li').each(function(index){
                // 隐藏非改部门人员
                if( $(this).attr('dpmtid') != _dpmt ){
                    $(this).addClass('sr-only');
                }
            });
            $('#choosePersonTitle').text(_title);
            $('#choosePerson').modal('show');
            // 将选中的人员放入输入框
            _index = index;
            choosePerson();
        });
    });

    //当模态框被隐藏时，将所有人员的class清除
    $('#choosePerson').on('hidden.bs.modal', function (e) {
        $('#choosePersonBody ul li').removeClass('sr-only');
    })

    // 将选中的人放进对应的栏位
    function choosePerson(){
        $('#choosePersonBody ul li').click(function(){
            var _name = $('.table .open-modal').eq(_index).attr('name');
            var _userid = $(this).attr('userid');
            if( $('#pushEmailPersonList input[name="'+ _name +'"]') ){
                $('#pushEmailPersonList input[name="'+ _name +'"]').val(_userid);
            }
            $('.table .open-modal').eq(_index).val($(this).text());
            $('.table .open-modal').eq(_index).next().html('&nbsp;');
            $('#choosePerson').modal('hide');
        });
    }

    $('.pj_add_pro_time').datetimepicker({
        language:  'zh-CN',
        format:'yyyy-mm-dd',
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        /*startDate : new Date(),*/
        pickerPosition : 'bottom-right',
        startView: 2,
        minView: 2,
        forceParse: 0
    });

    //修改提交
    $('#projectEditForm').validate({
        focusInvalid : true,
        rules : {
            pj_num : {
                required : true,
            },
            pj_name : {
                required : true,
            },
            pj_responsible : {
                required : true,
            },
            pj_management : {
                required : true,
            },
            pj_fw_development : {
                required : true,
            },
            pj_design : {
                required : true,
            },
            pj_structure_design : {
                required : true,
            },
            pj_dvt_test : {
                required : true,
            },
            pj_light_design : {
                required : true,
            },
            pj_ate_design : {
                required : true,
            },
            pj_market : {
                required : true,
            },
            pj_family : {
                required : true,
            },
            pj_type : {
                required : true,
            },
            pj_standard_name : {
                required : true,
            },
            pj_platform : {
                required : true,
            },
            pj_market_demand : {
                required : true,
            },
            pj_target_customer : {
                required : true,
            },
            pj_target_cost : {
                required : true,
                digits : true
            },
            pj_describe : {
                required : true,
            }
        },
        messages : {
            pj_num : {
                required : '项目编号不能为空',
            },
            pj_name : {
                required : '项目名称不能为空',
            },
            pj_responsible : {
                required : '项目负责人不能为空',
            },
            pj_management : {
                required : '项目管理人不能为空',
            },
            pj_fw_development : {
                required : 'FW开发不能为空',
            },
            pj_design : {
                required : '项目设计不能为空',
            },
            pj_structure_design : {
                required : '结构件设计不能为空',
            },
            pj_dvt_test : {
                required : 'DVT测试不能为空',
            },
            pj_light_design : {
                required : '光器件设计不能为空',
            },
            pj_ate_design : {
                required : 'ATE设计不能为空',
            },
            pj_market : {
                required : '市场部不能为空',
            },
            pj_family : {
                required : '产品族不能为空',
            },
            pj_type : {
                required : '产品类型不能为空',
            },
            pj_standard_name : {
                required : '标准品名不能为空',
            },
            pj_platform : {
                required : '平台不能为空',
            },
            pj_market_demand : {
                required : '潜在市场需求不能为空',
            },
            pj_target_customer : {
                required : '目标客户不能为空',
            },
            pj_target_cost : {
                required : '目标成本不能为空',
                digits : '只能输入整数'
            },
            pj_describe : {
                required : '项目描述不能为空',
            }
        },
        ignore : "",
        errorElement : 'span',
        errorPlacement : function(error, element) {
            error.appendTo(element.next());
        },
        submitHandler : function(form){
            var _serializePushEmailPersonList = $('#pushEmailPersonList').serializeArray();
            var data = {
                id : $('#projectEditForm input[name=info_id]').val(),
                pj_num : $('#projectEditForm input[name=pj_num]').val(),
                pj_name : $('#projectEditForm input[name=pj_name]').val(),
                pj_responsible : $('#projectEditForm input[name=pj_responsible]').val(),
                pj_management : $('#projectEditForm input[name=pj_management]').val(),
                pj_design : $('#projectEditForm input[name=pj_design]').val(),
                pj_family : $('#projectEditForm input[name=pj_family]').val(),
                pj_fw_development : $('#projectEditForm input[name=pj_fw_development]').val(),
                pj_type : $('#projectEditForm input[name=pj_type]').val(),
                pj_structure_design : $('#projectEditForm input[name=pj_structure_design]').val(),
                pj_standard_name : $('#projectEditForm input[name=pj_standard_name]').val(),
                pj_light_design : $('#projectEditForm input[name=pj_light_design]').val(),
                pj_platform : $('#projectEditForm input[name=pj_platform]').val(),
                pj_ate_design : $('#projectEditForm input[name=pj_ate_design]').val(),
                pj_market_demand : $('#projectEditForm input[name=pj_market_demand]').val(),
                pj_dvt_test : $('#projectEditForm input[name=pj_dvt_test]').val(),
                pj_target_customer : $('#projectEditForm input[name=pj_target_customer]').val(),
                pj_market : $('#projectEditForm input[name=pj_market]').val(),
                pj_add_pro_time : $('#projectEditForm input[name=pj_add_pro_time]').val(),
                pj_target_cost : $('#projectEditForm input[name=pj_target_cost]').val(),
                pj_describe : $('#projectEditForm textarea[name=pj_describe]').val(),
                idlist : _serializePushEmailPersonList
            };
            $(form).ajaxSubmit({
                url : ThinkPHP['AJAX'] + '/Project/edit',
                type : 'POST',
                data : data,
                dataType : 'json',
                success : function(response){
                    if( response.flag > 0 ){
                        layer.msg(response.msg, {time: 3000, icon: 1});
                    }else{
                        layer.msg(response.msg, {time: 3000, icon: 2});
                    }
                }
            });
        },
    });

    //修改数据
    $('.save-data-btn').click(function(){
        var _parent = $(this).parent().parent(),
            _id = _parent.find('input[name=plan_id]').val(),
            _gate = _parent.find('input[name=gate]').val(),
            _mile_stone = _parent.find('input[name=mile_stone]').val(),
            _items = _parent.find('input[name=items]').val(),
            _plan_start_time = _parent.find('input[name=plan_start_time]').val(),
            _plan_stop_time = _parent.find('input[name=plan_stop_time]').val();
        $.ajax({
            url : ThinkPHP['AJAX'] + '/Project/edit',
            type : 'POST',
            data : {
                _type : 'plan',
                id : _id,
                gate : _gate,
                mile_stone : _mile_stone,
                items : _items,
                plan_start_time : _plan_start_time,
                plan_stop_time : _plan_stop_time
            },
            dataType : 'json',
            success : function(response){
                if( response.flag > 0 ){
                    layer.msg(response.msg, {time: 3000, icon: 1});
                }else{
                    layer.msg(response.msg, {time: 3000, icon: 2});
                }
            }
        });
    });

    // 初始化时间选择插件
    function initDatetimepicker(){
        $('.plan_start_time').datetimepicker({
            language:  'zh-CN',
            format:'yyyy-mm-dd',
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            /*startDate : '2014-3-21',
            endDate : new Date(),*/
            pickerPosition : 'top-right',
            startView: 2,
            minView: 2,
            forceParse: 0
        });
        $('.plan_stop_time').datetimepicker({
            language:  'zh-CN',
            format:'yyyy-mm-dd',
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            /*startDate : new Date(),*/
            pickerPosition : 'top-right',
            startView: 2,
            minView: 2,
            forceParse: 0
        });
    }

    initDatetimepicker();

});