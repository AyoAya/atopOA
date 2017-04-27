$(function(){

	//存放附件数据
	//var attachmentData = new Array();
	var filter_content =$('#layer-open-content').html()
	var AllData = '{"sample_detail":[';	//定义空对象用于存放订单内容
	// 初始化layui组件
    layui.use(['layer','form'], function(){
        var layer = layui.layer;
        var form = layui.form();
		form.on('submit(sample)', function(data){
			var sample = $('#sampleForm');
			var order_num = sample.find('.sample_order_num').val();	//获取到订单号
			$('.traverse').each(function(index){
				var pn = $(this).find('input[name =product_select]').val(),
					count = $(this).find('input[name =count]').val(),
                    requirements_date = $(this).find('input[name =requirements_date]').val(),
					customer = $(this).find('input[name =customer]').val(),
					brand = $(this).find('input[name =brand]').val(),
					model = $(this).find('input[name =model]').val(),
                    manager = $(this).find('input[name =manager_id]').val(),
                    product_id = $(this).find('input[name =product_id]').val(),
					note = $(this).find('.sample_note').val();
				AllData += '{"pn":"'+ pn +'","count":"'+ count +'","requirements_date":"'+ requirements_date +'","customer":"'+ customer +'","brand":"'+ brand +'","product_id":"'+ product_id +'","model":"'+ model +'","note":"'+ note +'","manager":"'+ manager +'"},';
			});
			AllData = AllData.substring(0,AllData.length-1);
			AllData += ']}';
			$.ajax({
				url: ThinkPHP['AJAX'] + '/Sample/add',
				type: 'POST',
				data: {
					order_num : order_num,
                    sample_details : AllData,
					attachmentData : attachmentData
				},

				dataType: 'json',
				success: function(response){
					if( response.flag > 0 ){
						layer.msg(response.msg,{icon:1});

                        location.href = 'http://'+ThinkPHP['HTTP_HOST']+'/Sample/overview/id/'+response.id;
                    }else{
						AllData = '{"sample_detail":[';
						layer.msg(response.msg,{icon:2});

					}
				}
			});
			return false; //阻止表单跳转。
		});
		form.on('submit(productSearch)', function(data){
			$.ajax({
				url : ThinkPHP['AJAX'] + '/Sample/productSearch',
				type : 'POST',
				data : data.field,
				dataType : 'json',
				success : function( response ){
					if( response.flag > 0 ){
						var pns = '';
						for( var key in response.data ){	//拼装所有产品型号数据
							pns += '<li pro_id="'+ response.data[key].id +'">'+ response.data[key].pn +'</li>\r\n';
						}
						$('.product-list-box ul').html(pns);
					}else{
						layer.msg(response.msg);
					}
				}
			});
			return false;
		});
    });


	//点击选择产品展开筛选器
	var pro_element = null;
	$(document).on('click', '.sample_product_select', function(){
		pro_element = $(this);
		var filter_dialog = layer.open({
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

	//展开筛选
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
			url : ThinkPHP['AJAX'] + '/Sample/productFilter',
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

	//将产品信息添加到栏位
	$(document).on('click', '.product-list-box ul li' ,function(){
		pro_element.val($(this).text());
		pro_element.prev().val($(this).attr('pro_id'));
		pro_element.prev().prev().val($(this).attr('manager'));
		layer.closeAll();
	});

    //添加一栏
    var traverse = $('#wrap').html();
    $('#addBar').click(function(){
		$('#wrap').append(traverse);
		initDatetimePicker();	//每当添加一栏之后初始化全部时间选择器
		$('#layer-open-content').html(filter_content);
    });

    //删除栏位
    $('#wrap').on('click','.remove-bar',function(){
		var _parent = $(this).parent();
		var _traverse_length = $('#wrap').children('.traverse').length;
		if( _traverse_length == 1 ){
			layer.msg('至少保留一个');
		}else{
			_parent.slideUp('normal');
			setTimeout(function(){
				_parent.remove();
			},400);
		}
    });

	// 定义webuploader配置
	var attachmentData = new Array();	//定义存放附件的对象
	var webuploader_option = {
		auto: true,
		server: ThinkPHP['AJAX'] + '/Sample/upload',
		pick: '#filePicker',
		fileVal : 'Attachment',
		accept: {
			title: 'file',
			extensions: 'zip,rar,jpg,png,jpeg,doc,xls,xlsx,docx,pdf',
			mimeTypes: 'image/*,file/*',
		},
		method: 'POST',
	};
	// 实例化webuploader
	var uploader = WebUploader.create(webuploader_option);
	uploader.on('uploadSuccess', function(file, response){	//文件了上传成功后触发
		if( response.flag > 0 ){
			//当文件上传成功后添加到attachmentData
			var attachmentObject = new Object();
			attachmentObject.original = response.source;
			attachmentObject.newname = response.newname;
			attachmentObject.path = response.savepath;
			attachmentObject.ext = response.ext;
			attachmentObject.size = response.size;
			attachmentData.push(attachmentObject);
			var _html = '<li><span class="pull-left file-name layui-elip"><i class="layui-icon">&#xe621;</i>&nbsp;'+ response.source +'</span><button class="pull-right layui-btn layui-btn-danger layui-btn-mini remove-file-btn" file-path="'+ response.savepath +'" title="删除"><i class="layui-icon">&#x1006;</i></button><div class="clearfix"></div></li>';
			$('.file-list').append(_html);
		}else{
			layer.alert(response.msg, {
				title: '提示'
			});
		}
	});
	uploader.on('error', function( code ){	//文件上传出错时触发
		var msg = '';
		switch(code){
			case 'Q_EXCEED_NUM_LIMIT':
				msg = '只能上传一个文件';
			break;
			case 'Q_EXCEED_SIZE_LIMIT':
				msg = '文件大小超出限制';
			break;
			case 'Q_TYPE_DENIED':
				msg = '文件格式不允许';
			break;
		}
		layer.msg(msg, {icon:2});
	});

	//删除文件
	$(document).on('click', '.remove-file-btn', function(){
		var file_path = $(this).attr('file-path')
			elem = $(this).parent();
		$.ajax({
			url : ThinkPHP['AJAX'] + '/Sample/removeFile',
			type : 'POST',
			data : {
				filepath : file_path
			},
			dataType : 'json',
			success : function(response){
				if( response.flag > 0 ){
					elem.remove();
				}else{
					layer.msg(response.msg,{icon:2});
				}
			}
		});
	});

	//初始化时间选择器
	function initDatetimePicker(){
		$('#wrap input[name=requirements_date]').datetimepicker({
			language:  'zh-CN',
			format:'yyyy-mm-dd',
			weekStart: 1,
			todayBtn:  1,
			autoclose: 1,
			todayHighlight: 1,
			startDate : new Date(),
			pickerPosition: "bottom-left",
			startView: 2,
			minView: 2,
			forceParse: 0
		});
	}
	initDatetimePicker();


});