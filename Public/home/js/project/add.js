/**
 * Created by Fulwin on 2017/2/16.
 */
$(function(){

    //检测浏览器中是否存在项目计划保存的localStorage数据，如果存在则替换为该数据，不存在则为新增
    if( window.localStorage ){  //检测浏览器是否支持localStorage
        // 检测localStorage的各项数据是否都存在并且不为空
        if( $.trim(window.localStorage.getItem('localStorageProjectInfoData')) != ''
            && $.trim(window.localStorage.getItem('localStoragePushEmailListData')) != ''
            && $.trim(window.localStorage.getItem('localStorageProjectPlanHTML')) != ''
            && $.trim(window.localStorage.getItem('localStorageProjectPlanData')) != '' ){
            //将所有localStorage的数据转换为对象[项目计划的html格式除外]
            var localStorageProjectInfoData = $.parseJSON(window.localStorage.getItem('localStorageProjectInfoData')),
                localStoragePushEmailListData = $.parseJSON(window.localStorage.getItem('localStoragePushEmailListData')),
                localStorageProjectPlanHTML = window.localStorage.getItem('localStorageProjectPlanHTML'),
                localStorageProjectPlanData = $.parseJSON(window.localStorage.getItem('localStorageProjectPlanData'));
            //将localStorage的数据添加至项目信息表单
            var projectForm = $('#projectForm');
            projectForm.find('input[name=pj_num]').val(localStorageProjectInfoData.pj_num);
            projectForm.find('input[name=pj_name]').val(localStorageProjectInfoData.pj_name);
            projectForm.find('input[name=pj_responsible]').val(localStorageProjectInfoData.pj_responsible);
            projectForm.find('input[name=pj_management]').val(localStorageProjectInfoData.pj_management);
            projectForm.find('input[name=pj_design]').val(localStorageProjectInfoData.pj_design);
            projectForm.find('input[name=pj_family]').val(localStorageProjectInfoData.pj_family);
            projectForm.find('input[name=pj_fw_development]').val(localStorageProjectInfoData.pj_fw_development);
            projectForm.find('input[name=pj_type]').val(localStorageProjectInfoData.pj_type);
            projectForm.find('input[name=pj_structure_design]').val(localStorageProjectInfoData.pj_structure_design);
            projectForm.find('input[name=pj_standard_name]').val(localStorageProjectInfoData.pj_standard_name);
            projectForm.find('input[name=pj_light_design]').val(localStorageProjectInfoData.pj_light_design);
            projectForm.find('input[name=pj_platform]').val(localStorageProjectInfoData.pj_platform);
            projectForm.find('input[name=pj_ate_design]').val(localStorageProjectInfoData.pj_ate_design);
            projectForm.find('input[name=pj_market_demand]').val(localStorageProjectInfoData.pj_market_demand);
            projectForm.find('input[name=pj_dvt_test]').val(localStorageProjectInfoData.pj_dvt_test);
            projectForm.find('input[name=pj_target_customer]').val(localStorageProjectInfoData.pj_target_customer);
            projectForm.find('input[name=pj_market]').val(localStorageProjectInfoData.pj_market);
            projectForm.find('input[name=pj_add_pro_time]').val(localStorageProjectInfoData.pj_add_pro_time);
            projectForm.find('input[name=pj_target_cost]').val(localStorageProjectInfoData.pj_target_cost);
            projectForm.find('textarea[name=pj_describe]').val(localStorageProjectInfoData.pj_describe);
            //将参与项目人员注入表单
            var pushEmailPersonList = $('#pushEmailPersonList');
            pushEmailPersonList.find('input[name=pj_responsible]').val(localStoragePushEmailListData.pj_responsible);
            pushEmailPersonList.find('input[name=pj_fw_development]').val(localStoragePushEmailListData.pj_fw_development);
            pushEmailPersonList.find('input[name=pj_design]').val(localStoragePushEmailListData.pj_design);
            pushEmailPersonList.find('input[name=pj_structure_design]').val(localStoragePushEmailListData.pj_structure_design);
            pushEmailPersonList.find('input[name=pj_dvt_test]').val(localStoragePushEmailListData.pj_dvt_test);
            pushEmailPersonList.find('input[name=pj_light_design]').val(localStoragePushEmailListData.pj_light_design);
            pushEmailPersonList.find('input[name=pj_ate_design]').val(localStoragePushEmailListData.pj_ate_design);
            pushEmailPersonList.find('input[name=pj_market]').val(localStoragePushEmailListData.pj_market);
            //将项目计划还原到localStorage的格式
            var tbody = $('#plan-table tbody');
            tbody.html(localStorageProjectPlanHTML);
            //将项目计划数据还原到添加前
            for(var key in localStorageProjectPlanData){
                for(var i=0;i<localStorageProjectPlanData[key].length;i++){
                    tbody.find('tr').eq(i).find('input[name=mile_stone]').val(localStorageProjectPlanData[key][i].mile_stone);
                    tbody.find('tr').eq(i).find('input[name=items]').val(localStorageProjectPlanData[key][i].items);
                    tbody.find('tr').eq(i).find('input[name=plan_start_time]').val(localStorageProjectPlanData[key][i].plan_start_time);
                    tbody.find('tr').eq(i).find('input[name=plan_stop_time]').val(localStorageProjectPlanData[key][i].plan_stop_time);
                }
            }
        }
    }

    var _index;
    // 点击打开选择人员模态框
    $('.table .open-modal').each(function(index){
        $(this).click(function(){
            //alert(index);
            // 获取到当前点击元素上一个节点的内容并打开moal弹出层，并修改弹出层title
            var _this = $(this);
            var _title = $(this).parent().prev().text();
            var _dpmt = $(this).attr('dpmt');
            //console.log(_dpmt);
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



    // 点击提交序列化表单并提交后台
    /*$('#submit').click(function(){
        var _serialize = $('#projectForm').serializeArray();
        // console.log(_serialize);
        $.ajax({
            url : ThinkPHP['AJAX'] + '/Project/add',
            type : 'POST',
            data : {
                data : _serialize
            },
            dataType : 'json',
            success : function(response){
                if( response.flag > 0 ){
                    $('#MessageText').html('<p><i class="icon-ok-sign text-success"></i>&nbsp;' + response.msg + '</p>' );
                    $('#MessageModal').modal('show');
                    setTimeout(function(){
                        location.href = '/Project/details/id/' + response.flag;
                    },1000);
                }else{
                    $('#MessageText').html('<p><i class="icon-ok-sign text-danger"></i>&nbsp;' + response.msg + '</p>' );
                    $('#MessageModal').modal('show');
                    setTimeout(function(){
                        location.reload();
                    },1000);
                }
            }
        });
    });*/

    $('#projectForm').validate({
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
            if(window.localStorage){    //在浏览器支持localStorage的情况下将用户录入的信息存储在本地
                storageData();
            }
            //如果有任何一个字段为空则不允许添加下一行
            var valid = true;
            $('#plan-table input[type=text]').each(function(index){
                if( $.trim($(this).val()) == '' ){
                    layer.msg('项目计划填写不完整',{time:2000});
                    valid = false;
                    return false;
                }
            });
            if( valid === false ) return false;
            var _serializePushEmailPersonList = $('#pushEmailPersonList').serializeArray();
            var plan_json = '{"planData":[';
            $('#plan-table tr').each(function(index){   //拼接所有项目计划数据 [ json ]
                var _this = $(this);
                var _gate = _this.find('input[name=gate]').val();
                var _mile_stone = _this.find('input[name=mile_stone]').val();
                var _items = _this.find('input[name=items]').val();
                var _plan_start_time = _this.find('input[name=plan_start_time]').val();
                var _plan_stop_time = _this.find('input[name=plan_stop_time]').val();
                plan_json += '{"gate":"' + _gate + '","mile_stone":"' + _mile_stone + '","items":"' + _items + '","plan_start_time":"' + _plan_start_time + '","plan_stop_time":"' + _plan_stop_time + '"},';
            });
            plan_json = plan_json.substring(0,plan_json.length-1);
            plan_json += ']}';
            $(form).ajaxSubmit({
                url : ThinkPHP['AJAX'] + '/Project/add',
                type : 'POST',
                data : {
                    /*pj_num : $('#projectForm input[name=pj_num]').val(),
                    pj_name : $('#projectForm input[name=pj_name]').val(),
                    pj_responsible : $('#projectForm input[name=pj_responsible]').val(),
                    pj_management : $('#projectForm input[name=pj_management]').val(),
                    pj_design : $('#projectForm input[name=pj_design]').val(),
                    pj_family : $('#projectForm input[name=pj_family]').val(),
                    pj_fw_development : $('#projectForm input[name=pj_fw_development]').val(),
                    pj_type : $('#projectForm input[name=pj_type]').val(),
                    pj_structure_design : $('#projectForm input[name=pj_structure_design]').val(),
                    pj_standard_name : $('#projectForm input[name=pj_standard_name]').val(),
                    pj_light_design : $('#projectForm input[name=pj_light_design]').val(),
                    pj_platform : $('#projectForm input[name=pj_platform]').val(),
                    pj_ate_design : $('#projectForm input[name=pj_ate_design]').val(),
                    pj_market_demand : $('#projectForm input[name=pj_market_demand]').val(),
                    pj_dvt_test : $('#projectForm input[name=pj_dvt_test]').val(),
                    pj_target_customer : $('#projectForm input[name=pj_target_customer]').val(),
                    pj_market : $('#projectForm input[name=pj_market]').val(),
                    pj_target_cost : $('#projectForm input[name=pj_target_cost]').val(),
                    pj_describe : $('#projectForm textarea[name=pj_describe]').val(),*/
                    idlist : _serializePushEmailPersonList,
                    planData : plan_json
                },
                dataType : 'json',
                beforeSend : function(){
                    $('#loading').removeClass('sr-only');
                },
                success : function(response){
                    if( response.flag > 0 ){
                        if( window.localStorage ){
                            window.localStorage.clear();    //如果添加成功则将所有localStorage数据清空
                        }
                        $('#loading').addClass('sr-only');
                        $('#MessageText').html('<p><i class="icon-ok-sign text-success"></i>&nbsp;' + response.msg + '</p>' );
                        $('#MessageModal').modal('show');
                        $('#loading').addClass('sr-only');
                        setTimeout(function(){
                            location.href = '/Project/details/id/' + response.flag;
                        },1000);
                    }else{
                        $('#MessageText').html('<p><i class="icon-remove-sign text-danger"></i>&nbsp;' + response.msg + '</p>' );
                        $('#MessageModal').modal('show');
                        $('#loading').addClass('sr-only');
                        setTimeout(function(){
                            $('#MessageModal').modal('hide');
                        },1000);
                    }
                }
            });
        },
    });

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

    function storageData(){
        window.localStorage.clear();
        //为避免提交数据失败，当点击提交后，将用户录入的数据存放在localStorage
        var localStorageData = '{"localStorageData":[';  //初始化存放项目计划数据的对象
        $('#plan-table tbody tr').each(function(index){
            var _this = $(this),
                mile_stone = _this.find('input[name=mile_stone]').val(),
                items = _this.find('input[name=items]').val(),
                plan_start_time = _this.find('input[name=plan_start_time]').val(),
                plan_stop_time = _this.find('input[name=plan_stop_time]').val();
            localStorageData += '{"mile_stone":"'+ mile_stone +'","items":"'+ items +'","plan_start_time":"'+ plan_start_time +'","plan_stop_time":"'+ plan_stop_time +'"},';
        });
        localStorageData = localStorageData.substring(0,localStorageData.length-1);
        localStorageData += ']}';
        var _projectForm = $('#projectForm'),
            pj_num = _projectForm.find('input[name=pj_num]').val(),
            pj_name = _projectForm.find('input[name=pj_name]').val(),
            pj_responsible = _projectForm.find('input[name=pj_responsible]').val(),
            pj_management = _projectForm.find('input[name=pj_management]').val(),
            pj_design = _projectForm.find('input[name=pj_design]').val(),
            pj_family = _projectForm.find('input[name=pj_family]').val(),
            pj_fw_development = _projectForm.find('input[name=pj_fw_development]').val(),
            pj_type = _projectForm.find('input[name=pj_type]').val(),
            pj_structure_design = _projectForm.find('input[name=pj_structure_design]').val(),
            pj_standard_name = _projectForm.find('input[name=pj_standard_name]').val(),
            pj_light_design = _projectForm.find('input[name=pj_light_design]').val(),
            pj_platform = _projectForm.find('input[name=pj_platform]').val(),
            pj_ate_design = _projectForm.find('input[name=pj_ate_design]').val(),
            pj_market_demand = _projectForm.find('input[name=pj_market_demand]').val(),
            pj_dvt_test = _projectForm.find('input[name=pj_dvt_test]').val(),
            pj_target_customer = _projectForm.find('input[name=pj_target_customer]').val(),
            pj_market = _projectForm.find('input[name=pj_market]').val(),
            pj_add_pro_time = _projectForm.find('input[name=pj_add_pro_time]').val(),
            pj_target_cost = _projectForm.find('input[name=pj_target_cost]').val(),
            pj_describe = _projectForm.find('textarea[name=pj_describe]').val();
        var projectInfoData = {
            pj_num : pj_num,
            pj_name : pj_name,
            pj_responsible : pj_responsible,
            pj_management : pj_management,
            pj_design : pj_design,
            pj_family : pj_family,
            pj_fw_development : pj_fw_development,
            pj_type : pj_type,
            pj_structure_design : pj_structure_design,
            pj_standard_name : pj_standard_name,
            pj_light_design : pj_light_design,
            pj_platform : pj_platform,
            pj_ate_design : pj_ate_design,
            pj_market_demand : pj_market_demand,
            pj_dvt_test : pj_dvt_test,
            pj_target_customer : pj_target_customer,
            pj_market : pj_market,
            pj_add_pro_time : pj_add_pro_time,
            pj_target_cost : pj_target_cost,
            pj_describe : pj_describe
        };
        projectInfoData = JSON.stringify(projectInfoData);
        var pushEmailList = $('#pushEmailPersonList'),
            _pj_responsible = pushEmailList.find('input[name=pj_responsible]').val(),
            _pj_fw_development = pushEmailList.find('input[name=pj_fw_development]').val(),
            _pj_design = pushEmailList.find('input[name=pj_design]').val(),
            _pj_structure_design = pushEmailList.find('input[name=pj_structure_design]').val(),
            _pj_dvt_test = pushEmailList.find('input[name=pj_dvt_test]').val(),
            _pj_light_design = pushEmailList.find('input[name=pj_light_design]').val(),
            _pj_ate_design = pushEmailList.find('input[name=pj_ate_design]').val(),
            _pj_market = pushEmailList.find('input[name=pj_market]').val();
        var pushEmailListData = {
            pj_responsible : _pj_responsible,
            pj_fw_development : _pj_fw_development,
            pj_design : _pj_design,
            pj_structure_design : _pj_structure_design,
            pj_dvt_test : _pj_dvt_test,
            pj_light_design : _pj_light_design,
            pj_ate_design : _pj_ate_design,
            pj_market : _pj_market
        };
        pushEmailListData = JSON.stringify(pushEmailListData);
        var storage = window.localStorage;
        storage.setItem('localStorageProjectInfoData',projectInfoData);
        storage.setItem('localStoragePushEmailListData',pushEmailListData);
        //console.log(storage.getItem('localStoragePushEmailListData'));
        storage.setItem('localStorageProjectPlanHTML',$('#plan-table tbody').html());   //将项目计划已经生成的格式存储在localStorage
        storage.setItem('localStorageProjectPlanData',localStorageData);    //将项目计划已经生成的数据存储在localStorage [json]
    }

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

    // 避免火狐浏览器刷新后数据依然保留，当页面刷新后也检查最后一行数据是否已经完全填写
    var num = 0;
    var _length = $('#plan-table tr:last-child input[type=text]').length;
    $('#plan-table tr:last-child input[type=text]').each(function(index){
        if( $.trim($(this).val()) != '' ){
            num++;
        }
    });
    if( num == _length ){
        $('.add-row-btn').removeClass('sr-only');
    }

    //检测localStorage是否存在动态生成的数据，如果存在则直接读取[避免刷新后动态生成的数据丢失]
    /*if(window.localStorage.getItem('localStorageNodeHtml') != ''){
        $('#plan-table tbody').html( window.localStorage.getItem('localStorageNodeHtml') )
    }else{
        console.log('localStorage不存在数据');
    }*/

    // 添加一行
    $('.add-row-btn').click(function(){
        //每点击一次添加一行将localSotrage的数据清空
        //window.localStorage.removeItem('localStorageNodeHtml');
        //如果有任何一个字段为空则不允许添加下一行
        var valid = true;
        var _length = $('#plan-table tr:last-child input[type=text]').length;
        $('#plan-table tr:last-child input[type=text]').each(function(index){
            if( $.trim($(this).val()) == '' ){
                layer.msg('请先填写完所有字段',{time:2000});
                valid = false;
                return false;
            }
        });
        if( valid === false ) return false;
        var dynamic_node = '';  //存放动态gate
        var _tr = $('#plan-table tbody tr:last-child');
        var mile_stone_text = _tr.find('input[name=mile_stone]').val();
        var _tbody = $('#plan-table tbody');
        var node_size = _tbody.find('tr').length;
        var _gate = _tr.find('input[name=gate]').val();
        for(var i=_gate;i<=(parseInt(_gate)+1);i++){
            dynamic_node += '<li gate="' + i + '"><a href="#">Gate' + i + '</a></li>';
        }
        var node_html = '<tr><td><div class="btn-group"><button type="button" class="btn btn-default gate-num">Gate' + (parseInt(_gate)+1) + '</button><button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span><span class="sr-only">Toggle Dropdown</span></button><ul class="dropdown-menu">' + dynamic_node + '</ul></div><input type="hidden" name="gate" value="' + (parseInt(_gate)+1) + '"></td><td  class="special"><input type="text" name="mile_stone" class="form-control" placeholder="里程碑"></td><td><input type="text" name="items" class="form-control" placeholder="节点"></td><td><input type="text" name="plan_start_time" class="form-control plan_start_time" readonly placeholder="计划开始时间"></td><td><input type="text" name="plan_stop_time" class="form-control plan_stop_time" readonly placeholder="计划结束时间"></td><td><button type="button" class="btn btn-danger remove-row"><i class="icon-remove"></i></button></td></tr>';
        _tbody.append(node_html);
        //window.localStorage.setItem('localStorageNodeHtml',$('#plan-table tbody').html());  //将动态生成的数据保存在localSorage
        $('#plan-table tbody tr:last-child').prev().find('.btn-group button').attr('disabled',true);    //每添加一行禁用上一行的gate选择
        initDatetimepicker();   // 给新增行的时间选择实例化
        var new_length = $('#plan-table tbody tr').length;
        for(var i=0;i<new_length-1;i++){
            $('#plan-table tbody tr').eq(i).find('td:last-child').html(''); //只保留最后一个元素有删除按钮，其余为空
        }
    });

    //删除一行
    $('#plan-table').on('click','.remove-row',function(){
        var _tr = $(this).parents('tr');
        var _prev = _tr.prev();
        _prev.find('.btn-group button').removeAttr('disabled');
        _tr.remove();
        if( $('#plan-table tr').length > 1 ){
            _prev.find('td:last-child').html('<button type="button" class="btn btn-danger remove-row"><i class="icon-remove"></i></button>');
        }
        var num = 0;
        var _length = $('#plan-table tr:last-child input[type=text]').length;
        $('#plan-table tr:last-child input[type=text]').each(function(index){
            if( $.trim($(this).val()) != '' ){
                num++;
            }
        });
        if( num == _length ){
            $('.add-row-btn').removeClass('sr-only');
        }else{
            $('.add-row-btn').addClass('sr-only');
        }
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

    // 切换gate
    $('#plan-table').on('click','.dropdown-menu li',function(){
        var _tbody = $(this).parents('tbody');
        var _tr = $(this).parents('tr');
        var _index = (_tr.index());
        var input_gate = _tr.find('input[name=gate]');
        var mile_stone = _tr.find('input[name=mile_stone]');
        var gate_num = $(this).parent().parent().find('.gate-num');
        var _gate = $(this).attr('gate');
        gate_num.text('Gate' + _gate);
        input_gate.val(_gate);
        if( _index > 0 ){   //如果当前选择元素大于0则说明点击的不是第一个
            var prev_gate = _tr.prev().find('input[name=gate]').val();
            var prev_mile_stone = _tr.prev().find('input[name=mile_stone]').val();
            if( $.trim(prev_gate) != '' ){
                if( input_gate.val() == prev_gate ){    //对比和上一个元素的gate是否相等，如果相等则和上一个元素的里程碑保持一致，不相等则为空
                    mile_stone.removeAttr('readonly');
                    mile_stone.attr('readonly','true');
                    mile_stone.val(prev_mile_stone);
                }else{
                    mile_stone.removeAttr('readonly');
                    mile_stone.val('');
                }
            }
        }
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