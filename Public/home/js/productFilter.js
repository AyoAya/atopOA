/**
 * Created by Fulwin on 2017/4/11.
 */
$(function(){

    //重置筛选
    $(document).on('click', '.reset-filter-btn', function(){
        $('#filter-map input').val('');
        layer.closeAll();
        layer.open({
            type : 1,
            title : '选择产品',
            area: ['1200px'],
            shade: ['0.5','#000'],
            content: filter_content,
            cancel : function(index, layero){
                $('#filter-map input').val('');
                layer.close(index);
            }
        });
    });

    //展开
    $(document).on('click', '.an-more-btn', function(){
        var _this = $(this),
            _flag = _this.attr('flag'),
            _sr_only_items = $(this).parent().prev().find('.overflow');	//获取到当前类别所有隐藏的item
        if( _flag == 'off' ){
            _this.removeClass('icon-chevron-down');
            _this.addClass('icon-chevron-up');
            _sr_only_items.css('display','inline-block');
            _this.attr('flag','on');
        }else{
            _this.removeClass('icon-chevron-up');
            _this.addClass('icon-chevron-down');
            _sr_only_items.css('display','none');
            _this.attr('flag','off');
        }
    });


    //筛选产品
    $(document).on('click', '.filter-item', function(){
        var _category = $(this).attr('category'),
            _value = $(this).text()
        _this = $(this);
        if( _category == 'casetemp' || _category == 'wavelength' ){
            var _as_name = $(this).attr('as_name');
            $('#filter-map input[name='+ _category +']').val(_as_name);	//将选中的赋予到筛选表单
        }else{
            $('#filter-map input[name='+ _category +']').val(_value);	//将选中的赋予到筛选表单
        }
        $.ajax({
            url : ThinkPHP['AJAX'] + '/Auth/productFilter',
            type : 'POST',
            data : {
                filter : {
                    type : $('#filter-map input[name=type]').val(),
                    wavelength : $('#filter-map input[name=wavelength]').val(),
                    reach : $('#filter-map input[name=reach]').val(),
                    connector : $('#filter-map input[name=connector]').val(),
                    casetemp : $('#filter-map input[name=casetemp]').val()
                },
                category : _category
            },
            dataType : 'json',
            success : function(response){
                if( response.flag > 0 ){
                    var pns = '';
                    for( var key in response.data ){	//拼装所有产品型号数据
                        pns += '<li pro_id="'+ response.data[key].id +'" manager="'+ response.data[key].manager +'">'+ response.data[key].pn +'</li>\r\n';
                    }
                    for( var k in response.category ){
                        var _selector = '.filter-items-' + k.substring(0, k.length-1),
                            _category_list = '';
                        for( var i in response.category[k] ){
                            if( (parseInt(i) + 1) > 6 ){	//如果记录大于6条则隐藏，反之则显示
                                if( k == 'types' ){
                                    _category_list += '<li category="type" class="filter-item overflow" title="'+ response.category[k][i].type +'">'+ response.category[k][i].type +'</li>\r\n';
                                }else if( k == 'wavelengths' ){
                                    _category_list += '<li category="wavelength" class="filter-item overflow" as_name="'+ response.category[k][i].wavelength +'" title="'+ response.category[k][i].wavelength_as_name +'">'+ response.category[k][i].wavelength_as_name +'</li>\r\n';
                                }else if( k == 'reachs' ){
                                    _category_list += '<li category="reach" class="filter-item overflow" title="'+ response.category[k][i].reach +'">'+ response.category[k][i].reach +'</li>\r\n';
                                }else if( k == 'connectors' ){
                                    _category_list += '<li category="connector" class="filter-item overflow" title="'+ response.category[k][i].connector +'">'+ response.category[k][i].connector +'</li>\r\n';
                                }else if( k == 'casetemps' ){
                                    _category_list += '<li category="casetemp" class="filter-item overflow" as_name="'+ response.category[k][i].casetemp +'" title="'+ response.category[k][i].casetemp_as_name +'">'+ response.category[k][i].casetemp_as_name +'</li>\r\n';
                                }
                            }else{
                                if( k == 'types' ){
                                    _category_list += '<li category="type" class="filter-item" title="'+ response.category[k][i].type +'">'+ response.category[k][i].type +'</li>\r\n';
                                }else if( k == 'wavelengths' ){
                                    _category_list += '<li category="wavelength" class="filter-item" as_name="'+ response.category[k][i].wavelength +'" title="'+ response.category[k][i].wavelength_as_name +'">'+ response.category[k][i].wavelength_as_name +'</li>\r\n';
                                }else if( k == 'reachs' ){
                                    _category_list += '<li category="reach" class="filter-item" title="'+ response.category[k][i].reach +'">'+ response.category[k][i].reach +'</li>\r\n';
                                }else if( k == 'connectors' ){
                                    _category_list += '<li category="connector" class="filter-item" title="'+ response.category[k][i].connector +'">'+ response.category[k][i].connector +'</li>\r\n';
                                }else if( k == 'casetemps' ){
                                    _category_list += '<li category="casetemp" class="filter-item" as_name="'+ response.category[k][i].casetemp +'" title="'+ response.category[k][i].casetemp_as_name +'">'+ response.category[k][i].casetemp_as_name +'</li>\r\n';
                                }
                            }
                        }
                        $(_selector).html(_category_list);
                        var children_length = parseInt($(_selector).children('li').length) / 2,	//获取到对应类别的item数量
                            more_btn = $(_selector).parent().next().find('.an-more-btn');
                        //console.log(more_btn);
                        if( children_length <= 6 ){	//如果记录大于6条则显示展开按钮，反之则隐藏
                            if( !more_btn.hasClass('sr-only') ){
                                more_btn.addClass('sr-only');
                            }
                        }else{
                            if( more_btn.hasClass('sr-only') ){
                                more_btn.removeClass('sr-only');
                            }
                        }
                    }
                    $('.product-list-box ul').html(pns);
                    _this.parent().find('li').removeClass('active');
                    _this.addClass('active');
                }else{
                    layer.msg(response.msg);
                }
            }
        });
    });


});