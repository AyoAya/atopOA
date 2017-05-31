/**
 * Created by Fulwin on 2017/1/24.
 */
$(function(){

    $('#prompt').modal({
        backdrop : false,
        show : false
    });
    $('#filter').modal({
        backdrop : false,
        show : false
    });
    $('#product-modal').modal({
        backdrop : false,
        show : false
    });

    //ajax添加兼容记录
    $('.btn-submit').click(function(){
        var _form = $('.compatible-form');
        if( $.trim( _form.find('input[name=pn]').val() ) == '' ){
            _form.find('input[name=pn]').focus();
            alertMsg('请选择产品型号');
            return false;
        }
        if( $.trim( _form.find('input[name=vendor]').val() ) == '' ){
            _form.find('input[name=vendor]').focus();
            alertMsg('请选择设备品牌');
            return false;
        }
        if( $.trim( _form.find('input[name=model]').val() ) == '' ){
            _form.find('input[name=model]').focus();
            alertMsg('请选择设备型号');
            return false;
        }
        $.ajax({
            url : ThinkPHP['AJAX'] + '/Compatibility/addCompatibleMatrix',
            type : 'POST',
            data : {
                pn : $('#pn').val(),
                pn_name : $('#pn_value').val(),
                vendor : $('#vendor').val(),
                vendor_name : $('#vendor_value').val(),
                model : $('#model').val(),
                state : $('#state_flag').val(),
                version : $('#version').val(),
                original : $('#original').val(),
                comment : $('#comment').val(),
            },
            dataType : 'json',
            success : function(response){
                if( response ){
                    $('#prompt').modal('show');
                    setTimeout(function(){
                        location.href = ThinkPHP['ROOT'] + '/Compatibility';
                    },1000);
                }
            }
        });
    });

    //选择状态
    $('#state li').click(function(){
        var _text = $(this).text();
        var _state = $(this).attr('state');
        $('#stateText').text(_text);
        $('#state_flag').val(_state);
    });

    //封装消息提示插件
    function alertMsg(msg){
        $().toastmessage('showToast', {
            text: msg,
            sticky: false,
            position: 'top-right',
            type: 'warning',
            inEffectDuration: 600,
            stayTime: 3000,
        });
    }


    //显示更多
    var nowDom = null;
    $('.filter-body').on('click','.filter-col .label-more',function(){
        var _this = $(this);
        //console.log(_this.attr('flag'));
        var _parents = _this.parents('.filter-body');
        var i = _this.find('i');
        var _prev = _this.prev();
        var _hidden_li = _prev.find('li.default-hide-li');
        var flag = _this.attr('flag');
        if(flag=='true'){
            _this.attr('flag','flase');
            //alert('true');
            $('.filter-body .filter-col .label-more').find('span').text('更多');
            $('.filter-body .label-more').each(function(index){
                $(this).attr('flag','true');
            });
            $('.filter-body').find('i').attr('class','icon-angle-down');
            _parents.find('li.default-hide-li').css('display','none');
            _prev.find('li').css('display','inline-block');
            _this.find('span').text('收起');
            i.attr('class','icon-angle-up');
        }else{
            //alert('false');
            //_this.attr('flag','true');
            _hidden_li.css('display','none');
            _this.find('span').text('更多');
            i.attr('class','icon-angle-down');
        }

    });

    $('#brandList li').click(function(){
        var _text = $(this).text();
        var _id = $(this).attr('belong');
        $('#vendor_value').val(_text);
        $('#vendor').val(_id);
        $('#vendorModal').modal('hide');
    });


    //获取到筛选初始数据
    var initFilterBody = $('.filter-body').html();

    //初始化选择产品模态框
    $('#product-modal').modal({
        backdrop : false,
        show : false,
    });
    //初始化选择设备厂商模态框
    $('#vendorModal').modal({
        backdrop : false,
        show : false,
    });

    //显示更多
    var nowDom = null;
    $('.filter-body').on('click','.filter-col .label-more',function(){
        var dropdown = true;
        var _this = $(this);
        var _parents = _this.parents('.filter-body');
        var i = _this.find('i');
        var _prev = _this.prev();
        var _hidden_li = _prev.find('li.default-hide-li');
        var flag = _this.attr('flag');
        if(flag=='true'){
            //alert('true');
            $('.filter-body .filter-col .label-more').find('span').text('更多');
            $('.filter-body .label-more').each(function(index){
                $(this).attr('flag','true');
            });
            $('.filter-body').find('i').attr('class','icon-angle-down');
            _parents.find('li.default-hide-li').css('display','none');
            _prev.find('li').css('display','inline-block');
            _this.find('span').text('收起');
            i.attr('class','icon-angle-up');
            dropdown = false;
            _this.attr('flag','flase');
        }else{
            //alert('false');
            _hidden_li.css('display','none');
            _this.find('span').text('更多');
            i.attr('class','icon-angle-down');
            dropdown = true;
            _this.attr('flag','true');
        }

    });


    //条件筛选
    $(document).on('click','#conditon-list li span',function(){
        var _condition = $(this).parent().attr('condition');
        var _this_input = '';
        if(_condition=='clearAll'){
            $('#filterForm input').val('');
            $('.filter-body').html(initFilterBody);
        }else{
            $('#filterForm input').each(function(){
                if($(this).attr('name')==_condition){
                    _this_input = $(this);
                    $(this).val('');
                }
            });
            _this_input.nextAll().val('');
            $(this).parent().nextAll().remove();
        }
        $.post(ThinkPHP['AJAX']+'/Sample/filter',{
            data : $('#filterForm').serializeArray(),
        },function(data){
            if(data){
                if(!data.condition.type){
                    if($('#conditon-list li.filter-type').length > 0){
                        $('#conditon-list li.filter-type').remove();
                    }
                    $('.condition-type li').css({'color':'#555','background':'#fff','border':'solid 1px #fff'});
                }
                if(!data.condition.wavelength){
                    if($('#conditon-list li.filter-wavelength').length > 0){
                        $('#conditon-list li.filter-wavelength').remove();
                    }
                    $('.condition-wavelength li').css({'color':'#555','background':'#fff','border':'solid 1px #fff'});
                }
                if(!data.condition.reach){
                    if($('#conditon-list li.filter-reach').length > 0){
                        $('#conditon-list li.filter-reach').remove();
                    }
                    $('.condition-reach li').css({'color':'#555','background':'#fff','border':'solid 1px #fff'});
                }
                if(!data.condition.connector){
                    if($('#conditon-list li.filter-connector').length > 0){
                        $('#conditon-list li.filter-connector').remove();
                    }
                    $('.condition-connector li').css({'color':'#555','background':'#fff','border':'solid 1px #fff'});
                }
                if(!data.condition.casetemp){
                    if($('#conditon-list li.filter-casetemp').length > 0){
                        $('#conditon-list li.filter-casetemp').remove();
                    }
                    $('.condition-casetemp li').css({'color':'#555','background':'#fff','border':'solid 1px #fff'});
                }
                if(data.data){
                    var listr = '';
                    var wavelength_li = '';
                    var reach_li = '';
                    var connector_li = '';
                    var casetemp_li = '';
                    var obj = eval(data.data);
                    var wavelength = new Array();
                    var reach = new Array();
                    var connector = new Array();
                    var casetemp = new Array();
                    for(var i=0;i<obj.length;i++){
                        if(i<=16){
                            listr += '<li p_id="'+ obj[i].pid +'" nickname="'+ obj[i].nickname +'" manager="'+ obj[i].manager +'" uid="'+ obj[i].uid +'" pn="'+ obj[i].pn +'">'+ obj[i].pn +'</li>';
                        }
                        wavelength.push(obj[i].wavelength);
                        reach.push(obj[i].reach);
                        connector.push(obj[i].connector);
                        casetemp.push(obj[i].casetemp);
                    }
                    if(_condition=='type'){
                        var unique_wavelength = $.unique(wavelength.sort());
                        var unique_reach = $.unique(reach.sort());
                        var unique_connector = $.unique(connector.sort());
                        var unique_casetemp = $.unique(casetemp.sort());
                        for(var i=0;i<unique_wavelength.length;i++){
                            if(unique_wavelength[i].length < 12 && unique_wavelength[i].indexOf('.') <= 0){
                                if(i<=8){
                                    wavelength_li += "<li onclick=filter('wavelength','"+ unique_wavelength[i] +"')>"+ unique_wavelength[i] +"</li>";
                                }else{
                                    wavelength_li += "<li onclick=filter('wavelength','"+ unique_wavelength[i] +"') style='display:none;' class='default-hide-li'>"+ unique_wavelength[i] +"</li>";
                                }
                            }
                        }
                        for(var i=0;i<unique_reach.length;i++){
                            if(i<=6){
                                reach_li += "<li onclick=filter('reach','"+ unique_reach[i] +"')>"+ unique_reach[i] +"</li>";
                            }else{
                                reach_li += "<li onclick=filter('reach','"+ unique_reach[i] +"') style='display:none;' class='default-hide-li'>"+ unique_reach[i] +"</li>";
                            }
                        }
                        for(var i=0;i<unique_connector.length;i++){
                            connector_li += "<li onclick=filter('connector','"+ unique_connector[i] +"')>"+ unique_connector[i] +"</li>";
                        }
                        for(var i=0;i<unique_casetemp.length;i++){
                            if(unique_casetemp[i]=='C'){
                                casetemp_li += "<li onclick=filter('casetemp','"+ unique_casetemp[i] +"')>C档（0-70°）</li>";
                            }else{
                                casetemp_li += "<li onclick=filter('casetemp','"+ unique_casetemp[i] +"')>I档（-40-85°）</li>";
                            }
                        }
                        $('.condition-wavelength').html(wavelength_li);
                        $('.condition-reach').html(reach_li);
                        $('.condition-connector').html(connector_li);
                        $('.condition-casetemp').html(casetemp_li);
                    }else if(_condition=='wavelength'){
                        var unique_reach = $.unique(reach.sort());
                        var unique_connector = $.unique(connector.sort());
                        var unique_casetemp = $.unique(casetemp.sort());
                        for(var i=0;i<unique_reach.length;i++){
                            reach_li += "<li onclick=filter('reach','"+ unique_reach[i] +"')>"+ unique_reach[i] +"</li>";
                        }
                        for(var i=0;i<unique_connector.length;i++){
                            connector_li += "<li onclick=filter('connector','"+ unique_connector[i] +"')>"+ unique_connector[i] +"</li>";
                        }
                        for(var i=0;i<unique_casetemp.length;i++){
                            if(unique_casetemp[i]=='C'){
                                casetemp_li += "<li onclick=filter('casetemp','"+ unique_casetemp[i] +"')>C档（0-70°）</li>";
                            }else{
                                casetemp_li += "<li onclick=filter('casetemp','"+ unique_casetemp[i] +"')>I档（-40-85°）</li>";
                            }
                        }
                        $('.condition-reach').html(reach_li);
                        $('.condition-connector').html(connector_li);
                        $('.condition-casetemp').html(casetemp_li);
                    }else if(_condition=='reach'){
                        var unique_connector = $.unique(connector.sort());
                        var unique_casetemp = $.unique(casetemp.sort());
                        for(var i=0;i<unique_connector.length;i++){
                            connector_li += "<li onclick=filter('connector','"+ unique_connector[i] +"')>"+ unique_connector[i] +"</li>";
                        }
                        for(var i=0;i<unique_casetemp.length;i++){
                            if(unique_casetemp[i]=='C'){
                                casetemp_li += "<li onclick=filter('casetemp','"+ unique_casetemp[i] +"')>C档（0-70°）</li>";
                            }else{
                                casetemp_li += "<li onclick=filter('casetemp','"+ unique_casetemp[i] +"')>I档（-40-85°）</li>";
                            }
                        }
                        $('.condition-connector').html(connector_li);
                        $('.condition-casetemp').html(casetemp_li);
                    }else if(_condition=='connector'){
                        var unique_casetemp = $.unique(casetemp.sort());
                        for(var i=0;i<unique_casetemp.length;i++){
                            if(unique_casetemp[i]=='C'){
                                casetemp_li += "<li onclick=filter('casetemp','"+ unique_casetemp[i] +"')>C档（0-70°）</li>";
                            }else{
                                casetemp_li += "<li onclick=filter('casetemp','"+ unique_casetemp[i] +"')>I档（-40-85°）</li>";
                            }
                        }
                        $('.condition-casetemp').html(casetemp_li);
                    }
                    $('#product-list').html(listr);
                }
            }
        },'json');
    });


    //点击产品型号锁定产品经理
    $('#product-modal').on('click','#product-list li',function(){
        var _this = $(this);
        var pn = _this.attr('pn');
        var nickname = _this.attr('nickname');
        var manager = _this.attr('manager');
        var pid = _this.attr('p_id');
        $('#pn_value').val(pn);
        $('#pn').val(pid);
        $('#product-modal').modal('hide');
    });

});


//数据筛选
function filter(key,value,th){
    var _key = key;
    var _value = value;
    var _th = th;
    if($('#conditon-list li.filter-type').length > 0){
        if(_key=='type'){
            $('#filterForm input[name=type]').val(value);
            $('#filterForm input[name!=type]').val('');
            var conditionList = $('#conditon-list li');
            for(var i=0;i<conditionList.length;i++){
                if(!$(conditionList[i]).hasClass('filter-type')){
                    conditionList[i].remove();
                }
            }
        }else{
            $('#filterForm input').each(function(){
                if($(this).attr('name')==key){
                    $(this).val(value);
                }
            });
        }
    }else{
        $('#filterForm input').each(function(){
            if($(this).attr('name')==key){
                $(this).val(value);
            }
        });
    }
    $.post(ThinkPHP['AJAX']+'/Sample/filter',{
        data : $('#filterForm').serializeArray(),
    },function(data){
        if(data){
            if(data.condition.type){
                if($('#conditon-list li.filter-type').length > 0){
                    $('#conditon-list li.filter-type').html('类型：' + data.condition.type + '&nbsp;<span class="icon-remove"></span>');
                }else {
                    $('#conditon-list').append('<li class="filter-type" condition="type" value="'+ data.condition.type +'">类型：' + data.condition.type + '&nbsp;<span class="icon-remove"></span></li>');
                }
                $('.condition-type li').css({'color':'#555','background':'#fff','border':'solid 1px #fff'});
                $('.condition-type li').each(function(){
                    if($(this).text()==data.condition.type){
                        $(this).css({'color':'#fff','background':'#428bca','border':'solid 1px #428bca'});
                    }
                });
                if($('#conditon-list').children().length > 0){
                    if($('#conditon-list li.clear-condition').length <= 0){
                        $('#conditon-list').append('<li class="pull-right clear-condition" condition="clearAll"><span class="clear-condition-text">清空条件</span></li>');
                    }
                }else{
                    if($('#conditon-list li.clear-condition')){
                        $('#conditon-list .clear-condition').remove();
                    }
                }
            }
            if(data.condition.type && data.condition.wavelength && !data.condition.reach && !data.condition.connector && !data.condition.casetemp ){
                if(data.condition.wavelength.indexOf('.') > 0){
                    if($('#conditon-list li.filter-wavelength').length > 0){
                        $('#conditon-list li.filter-wavelength').html('波长：' + $(_th).text() + '&nbsp;<span class="icon-remove"></span>');
                    }else {
                        $('#conditon-list').append('<li class="filter-wavelength" condition="wavelength" value="'+ data.condition.wavelength +'">波长：' + $(_th).text() + '&nbsp;<span class="icon-remove"></span></li>');
                    }
                    $('.condition-wavelength li').css({'color':'#555','background':'#fff','border':'solid 1px #fff'});
                    $('.condition-wavelength li').each(function(){
                        if($(this).attr('value')==data.condition.wavelength){
                            $(this).css({'color':'#fff','background':'#428bca','border':'solid 1px #428bca'});
                        }
                    });
                    if($('#conditon-list').children().length > 0){
                        if($('#conditon-list li.clear-condition').length <= 0){
                            $('#conditon-list').append('<li class="pull-right clear-condition" condition="clearAll"><span class="clear-condition-text">清空条件</span></li>');
                        }
                    }else{
                        if($('#conditon-list li.clear-condition')){
                            $('#conditon-list .clear-condition').remove();
                        }
                    }
                }else{
                    if($('#conditon-list li.filter-wavelength').length > 0){
                        $('#conditon-list li.filter-wavelength').html('波长：' + data.condition.wavelength + '&nbsp;<span class="icon-remove"></span>');
                    }else {
                        $('#conditon-list').append('<li class="filter-wavelength" condition="wavelength">波长：' + data.condition.wavelength + '&nbsp;<span class="icon-remove"></span></li>');
                    }
                    $('.condition-wavelength li').css({'color':'#555','background':'#fff','border':'solid 1px #fff'});
                    $('.condition-wavelength li').each(function(){
                        if($(this).text()==data.condition.wavelength){
                            $(this).css({'color':'#fff','background':'#428bca','border':'solid 1px #428bca'});
                        }
                    });
                    if($('#conditon-list').children().length > 0){
                        if($('#conditon-list li.clear-condition').length <= 0){
                            $('#conditon-list').append('<li class="pull-right clear-condition" condition="clearAll"><span class="clear-condition-text">清空条件</span></li>');
                        }
                    }else{
                        if($('#conditon-list li.clear-condition')){
                            $('#conditon-list .clear-condition').remove();
                        }
                    }
                }
            }
            if(data.condition.reach){
                if($('#conditon-list li.filter-reach').length > 0){
                    $('#conditon-list li.filter-reach').html('距离：' + data.condition.reach + '&nbsp;<span class="icon-remove"></span>');
                }else {
                    $('#conditon-list').append('<li class="filter-reach" condition="reach" value="'+ data.condition.reach +'">距离：' + data.condition.reach + '&nbsp;<span class="icon-remove"></span></li>');
                }
                $('.condition-reach li').css({'color':'#555','background':'#fff','border':'solid 1px #fff'});
                $('.condition-reach li').each(function(){
                    if($(this).text()==data.condition.reach){
                        $(this).css({'color':'#fff','background':'#428bca','border':'solid 1px #428bca'});
                    }
                });
                if($('#conditon-list').children().length > 0){
                    if($('#conditon-list li.clear-condition').length <= 0){
                        $('#conditon-list').append('<li class="pull-right clear-condition" condition="clearAll"><span class="clear-condition-text">清空条件</span></li>');
                    }
                }else{
                    if($('#conditon-list li.clear-condition')){
                        $('#conditon-list .clear-condition').remove();
                    }
                }
                if($('.condition-reach').children().length <= 7){
                    $('.condition-reach').next().hide();
                }
            }
            if(data.condition.connector){
                if($('#conditon-list li.filter-connector').length > 0){
                    $('#conditon-list li.filter-connector').html('接口：' + data.condition.connector + '&nbsp;<span class="icon-remove"></span>');
                }else {
                    $('#conditon-list').append('<li class="filter-connector" condition="connector" value="'+ data.condition.connector +'">接口：' + data.condition.connector + '&nbsp;<span class="icon-remove"></span></li>');
                }
                $('.condition-connector li').css({'color':'#555','background':'#fff','border':'solid 1px #fff'});
                $('.condition-connector li').each(function(){
                    if($(this).text()==data.condition.connector){
                        $(this).css({'color':'#fff','background':'#428bca','border':'solid 1px #428bca'});
                    }
                });
                if($('#conditon-list').children().length > 0){
                    if($('#conditon-list li.clear-condition').length <= 0){
                        $('#conditon-list').append('<li class="pull-right clear-condition" condition="clearAll"><span class="clear-condition-text">清空条件</span></li>');
                    }
                }else{
                    if($('#conditon-list li.clear-condition')){
                        $('#conditon-list .clear-condition').remove();
                    }
                }
            }
            if(data.condition.casetemp){
                if($('#conditon-list li.filter-casetemp').length > 0){
                    if(data.condition.casetemp=='C'){
                        $('#conditon-list li.filter-casetemp').html('环境：C档（0-70°）&nbsp;<span class="icon-remove"></span>');
                    }else{
                        $('#conditon-list li.filter-casetemp').html('环境：I档（-40-85°）&nbsp;<span class="icon-remove"></span>');
                    }
                }else {
                    if(data.condition.casetemp=='C'){
                        $('#conditon-list').append('<li class="filter-casetemp" condition="casetemp" value="'+ data.condition.casetemp +'">环境：C档（0-70°）&nbsp;<span class="icon-remove"></span></li>');
                    }else{
                        $('#conditon-list').append('<li class="filter-casetemp" condition="casetemp" value="'+ data.condition.casetemp +'">环境：I档（-40-85°）&nbsp;<span class="icon-remove"></span></li>');
                    }
                }
                $('.condition-casetemp li').css({'color':'#555','background':'#fff','border':'solid 1px #fff'});
                $('.condition-casetemp li').each(function(){
                    if($(this).attr('en')==data.condition.casetemp){
                        $(this).css({'color':'#fff','background':'#428bca','border':'solid 1px #428bca'});
                    }
                });
                if($('#conditon-list').children().length > 0){
                    if($('#conditon-list li.clear-condition').length <= 0){
                        $('#conditon-list').append('<li class="pull-right clear-condition" condition="clearAll"><span class="clear-condition-text">清空条件</span></li>');
                    }
                }else{
                    if($('#conditon-list li.clear-condition')){
                        $('#conditon-list .clear-condition').remove();
                    }
                }
            }
            if(data.data){
                var listr = '';
                var wavelength_li = '';
                var wavelength_li_length = '';
                var reach_li = '';
                var connector_li = '';
                var casetemp_li = '';
                var obj = eval(data.data);
                var wavelength = new Array();
                var reach = new Array();
                var connector = new Array();
                var casetemp = new Array();
                var wave_length = new Array();
                for(var i=0;i<obj.length;i++){
                    listr += '<li p_id="'+ obj[i].pid +'" nickname="'+ obj[i].nickname +'" manager="'+ obj[i].manager +'" uid="'+ obj[i].uid +'" pn="'+ obj[i].pn +'" type="'+ obj[i].type +'">'+ obj[i].pn +'</li>';
                    wavelength.push(obj[i].wavelength);
                    reach.push(obj[i].reach);
                    connector.push(obj[i].connector);
                    casetemp.push(obj[i].casetemp);
                    if(obj[i].wave_length){
                        wave_length.push(obj[i].wave_length);
                    }
                }
                if(_key=='type'){
                    var unique_wavelength = $.unique(wavelength.sort());
                    var unique_reach = $.unique(reach.sort());
                    var unique_connector = $.unique(connector.sort());
                    var unique_casetemp = $.unique(casetemp.sort());
                    var unique_wave_length = $.unique(wave_length.sort()).reverse();
                    if(_value.indexOf('DWDM') > 0){
                        //alert('包含DWDM');
                        for(var i=0;i<unique_wavelength.length;i++){
                            if(unique_wavelength[i].length <= 11){
                                if(i<=6){
                                    wavelength_li += "<li onclick=filter('wavelength','"+ unique_wave_length[i] +"',this) value='"+ unique_wave_length[i] +"'>"+ unique_wavelength[i] +"</li>";
                                }else{
                                    wavelength_li += "<li onclick=filter('wavelength','"+ unique_wave_length[i] +"',this) style='display:none;' class='default-hide-li' value='"+ unique_wave_length[i] +"'>"+ unique_wavelength[i] +"</li>";
                                }
                            }else{
                                wavelength_li_length += "<li onclick=filter('wavelength','"+ unique_wave_length[i] +"',this) class='width-length' value='"+ unique_wavelength[i] +"'>"+ unique_wave_length[i] +"</li>";
                            }
                        }
                    }else{
                        //alert('不包含DWDM');
                        for(var i=0;i<unique_wavelength.length;i++){
                            if(unique_wavelength[i].length <= 11){
                                if(i<=6){
                                    wavelength_li += "<li onclick=filter('wavelength','"+ unique_wavelength[i] +"') value='"+ unique_wavelength[i] +"'>"+ unique_wavelength[i] +"</li>";
                                }else{
                                    wavelength_li += "<li onclick=filter('wavelength','"+ unique_wavelength[i] +"') style='display:none;' class='default-hide-li' value='"+ unique_wavelength[i] +"'>"+ unique_wavelength[i] +"</li>";
                                }
                            }else{
                                wavelength_li_length += "<li onclick=filter('wavelength','"+ unique_wavelength[i] +"') class='width-length' value='"+ unique_wavelength[i] +"'>"+ unique_wavelength[i] +"</li>";
                            }
                        }
                    }
                    wavelength_li += wavelength_li_length;
                    for(var i=0;i<unique_reach.length;i++){
                        if(i<=6){
                            reach_li += "<li onclick=filter('reach','"+ unique_reach[i] +"') value='"+ unique_reach[i] +"'>"+ unique_reach[i] +"</li>";
                        }else{
                            reach_li += "<li onclick=filter('reach','"+ unique_reach[i] +"') style='display:none;' class='default-hide-li' value='"+ unique_reach[i] +"'>"+ unique_reach[i] +"</li>";
                        }
                    }
                    for(var i=0;i<unique_connector.length;i++){
                        connector_li += "<li onclick=filter('connector','"+ unique_connector[i] +"') value='"+ unique_connector[i] +"'>"+ unique_connector[i] +"</li>";
                    }
                    for(var i=0;i<unique_casetemp.length;i++){
                        if(unique_casetemp[i]=='C'){
                            casetemp_li += "<li onclick=filter('casetemp','"+ unique_casetemp[i] +"') en='"+ unique_casetemp[i] +"'>C档（0-70°）</li>";
                        }else{
                            casetemp_li += "<li onclick=filter('casetemp','"+ unique_casetemp[i] +"') en='"+ unique_casetemp[i] +"'>I档（-40-85°）</li>";
                        }
                    }
                    $('.condition-wavelength').html(wavelength_li);
                    $('.condition-reach').html(reach_li);
                    $('.condition-connector').html(connector_li);
                    $('.condition-casetemp').html(casetemp_li);
                    if($('.condition-wavelength').children().length <= 7){
                        $('.condition-wavelength').next().hide();
                    }else{
                        $('.condition-wavelength').next().show();
                    }
                    if($('.condition-reach').children().length <= 7){
                        $('.condition-reach').next().hide();
                    }else{
                        $('.condition-reach').next().show();
                    }
                }else if(_key=='wavelength'){
                    var unique_reach = $.unique(reach.sort());
                    var unique_connector = $.unique(connector.sort());
                    var unique_casetemp = $.unique(casetemp.sort());
                    for(var i=0;i<unique_reach.length;i++){
                        reach_li += "<li onclick=filter('reach','"+ unique_reach[i] +"') value='"+ unique_reach[i] +"'>"+ unique_reach[i] +"</li>";
                    }
                    for(var i=0;i<unique_connector.length;i++){
                        connector_li += "<li onclick=filter('connector','"+ unique_connector[i] +"') value='"+ unique_reach[i] +"'>"+ unique_connector[i] +"</li>";
                    }
                    for(var i=0;i<unique_casetemp.length;i++){
                        if(unique_casetemp[i]=='C'){
                            casetemp_li += "<li onclick=filter('casetemp','"+ unique_casetemp[i] +"') en='"+ unique_casetemp[i] +"'>C档（0-70°）</li>";
                        }else{
                            casetemp_li += "<li onclick=filter('casetemp','"+ unique_casetemp[i] +"') en='"+ unique_casetemp[i] +"'>I档（-40-85°）</li>";
                        }
                    }
                    $('.condition-reach').html(reach_li);
                    $('.condition-connector').html(connector_li);
                    $('.condition-casetemp').html(casetemp_li);
                    if($('.condition-reach').children().length <= 7){
                        $('.condition-reach').next().hide();
                    }else{
                        $('.condition-reach').next().show();
                    }
                }else if(_key=='reach'){
                    var unique_connector = $.unique(connector.sort());
                    var unique_casetemp = $.unique(casetemp.sort());
                    for(var i=0;i<unique_connector.length;i++){
                        connector_li += "<li onclick=filter('connector','"+ unique_connector[i] +"') value='"+ unique_connector[i] +"'>"+ unique_connector[i] +"</li>";
                    }
                    for(var i=0;i<unique_casetemp.length;i++){
                        if(unique_casetemp[i]=='C'){
                            casetemp_li += "<li onclick=filter('casetemp','"+ unique_casetemp[i] +"') en='"+ unique_casetemp[i] +"'>C档（0-70°）</li>";
                        }else{
                            casetemp_li += "<li onclick=filter('casetemp','"+ unique_casetemp[i] +"') en='"+ unique_casetemp[i] +"'>I档（-40-85°）</li>";
                        }
                    }
                    $('.condition-connector').html(connector_li);
                    $('.condition-casetemp').html(casetemp_li);
                }else if(_key=='connector'){
                    var unique_casetemp = $.unique(casetemp.sort());
                    for(var i=0;i<unique_casetemp.length;i++){
                        if(unique_casetemp[i]=='C'){
                            casetemp_li += "<li onclick=filter('casetemp','"+ unique_casetemp[i] +"') en='"+ unique_casetemp[i] +"'>C档（0-70°）</li>";
                        }else{
                            casetemp_li += "<li onclick=filter('casetemp','"+ unique_casetemp[i] +"') en='"+ unique_casetemp[i] +"'>I档（-40-85°）</li>";
                        }
                    }
                    $('.condition-casetemp').html(casetemp_li);
                }
                $('#product-list').html(listr);
            }
        }
    },'json');
}
