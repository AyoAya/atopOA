/**
 * customer客诉处理页面js
 */


$(function(){


	var attachmentData = new Array();	//定义存放附件的数组
	var attachmentList = new Array();	//定义存放附件的数组
		//初始化layui组件
	layui.use(['form','layer','upload'], function(){
		var form = layui.form(),
			layer = layui.layer;

		form.on('submit(customer)', function(data){
			var _data = data.field;
			if( attachmentList.length > 0 ){
				_data.attachment = JSON.stringify(attachmentList);
			}
			$.ajax({
				url : ThinkPHP['AJAX'] + '/RMA/add',
				type : 'POST',
				data : _data,
				dataType : 'json',
				beforeSend : function(){
					var loading = layer.load(2, {shade : [0.5,'#fff']});
				},
				success : function(response){
					if( response.flag == 1 ){
						layer.msg(response.msg, { icon : 1,time : 2000 });
						setTimeout(function(){
							location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/RMA/details/id/' + response.id;
						},2000);
					}else{
						layer.closeAll();
						layer.msg(response.msg, { icon : 2,time : 2000 });
					}
				}
			});
			return false;
		});

		form.on('radio(is_rma)', function( data ){
			var _element = data.elem,
				_next = $(_element).parent().parent().next();
			if( data.value == 0 ){
				if( !$(_next).hasClass('sr-only') ){
					$(_next).addClass('sr-only');
				}
			}else{
				if( $(_next).hasClass('sr-only') ){
					$(_next).removeClass('sr-only');
				}
			}
		});

		//监听rma处理类型
		//如果当前步骤处于销售确认是否转RMA，当用户选择推送到下一步则显示QA部门人员选择框否则隐藏
		form.on('select(operation)', function( data ){
			var elem = data.elem;
			var value = data.value;
			var now_step = $(elem).parents('.customer-rma-form').find('input[name=step]').val();
			var operation_select_elem = $(elem).parents('.customer-rma-form').find('.operation-person-select');
			var step_select_elem = $(elem).parents('.customer-rma-form').find('.step-select');
			var fae_select_elem = $(elem).parents('.customer-rma-form').find('.fae-person-select');
			var close_reason_elem = $(elem).parents('.customer-rma-form').find('.close-reason-select');
			if( value == 'X' ){
				if( !$(operation_select_elem).hasClass('sr-only') ){
					$(operation_select_elem).addClass('sr-only');
				}
				if( !$(step_select_elem).hasClass('sr-only') ){
					$(step_select_elem).addClass('sr-only');
				}
				if( !$(fae_select_elem).hasClass('sr-only') ){
					$(fae_select_elem).addClass('sr-only');
				}
				if( !$(close_reason_elem).hasClass('sr-only') ){
					$(close_reason_elem).addClass('sr-only');
				}
			}
			if( now_step == 3 || now_step == 4 ){
				if( now_step == 3 && value == 'N' ){	//如果当前步骤等于3并且选择关闭客诉则显示关闭客诉原因下拉菜单
					if( $(close_reason_elem).hasClass('sr-only') ){
						$(close_reason_elem).removeClass('sr-only');
					}
				}else{
					if( !$(close_reason_elem).hasClass('sr-only') ){
						$(close_reason_elem).addClass('sr-only');
					}
				}
				if( now_step == 3 && value == 4 || now_step == 4 && value == 'Z' ){	//如果当前步骤等于3或者等于4，并且选择推送下一步或者转交则显示qa部门人员选择下拉菜单
					if( $(operation_select_elem).hasClass('sr-only') ){
						$(operation_select_elem).removeClass('sr-only');
					}
				}else{
					if( !$(operation_select_elem).hasClass('sr-only') ){
						$(operation_select_elem).addClass('sr-only');
					}
				}
			}
			if( now_step == 5 || now_step == 6 ){	//如果步骤可以回退则显示回退步骤下拉菜单
				if( now_step == 6 && value == 'N' ){	//如果当前步骤等于6并且选择关闭客诉则显示关闭客诉原因下拉菜单
					if( $(close_reason_elem).hasClass('sr-only') ){
						$(close_reason_elem).removeClass('sr-only');
					}
				}else{
					if( !$(close_reason_elem).hasClass('sr-only') ){
						$(close_reason_elem).addClass('sr-only');
					}
				}
				if( value == 'H' ){	//当用户选择回退的时候才显示否则隐藏
					if( $(step_select_elem).hasClass('sr-only') ){
						$(step_select_elem).removeClass('sr-only');
					}
					if( !$(fae_select_elem).hasClass('sr-only') ){
						$(fae_select_elem).addClass('sr-only');
					}
				}else{
					if( !$(step_select_elem).hasClass('sr-only') ){
						$(step_select_elem).addClass('sr-only');
					}
					if( value == 'Z' ){
						if( $(fae_select_elem).hasClass('sr-only') ){
							$(fae_select_elem).removeClass('sr-only');
						}
						if( !$(step_select_elem).hasClass('sr-only') ){
							$(step_select_elem).addClass('sr-only');
						}
					}else{
						if( !$(fae_select_elem).hasClass('sr-only') ){
							$(fae_select_elem).addClass('sr-only');
						}
					}
				}
			}
		});

		form.on('submit(rma)', function( data ){
			var _data = data.field;
			if( attachmentData.length > 0 ){
				_data.attachment = JSON.stringify(attachmentData);
			}
			$.ajax({
				url : ThinkPHP['AJAX'] + '/RMA/toRMA',
				type : 'POST',
				data : _data,
				dataType : 'json',
				success : function(response){
					console.log(response);
				}
			});
			return false;
		});


		//监听rma处理表单文件上传
		layui.upload({
			url : ThinkPHP['AJAX'] + '/Customer/uploadFile',
			title : '上传文件',
			method : 'post',
			before : function(input){
				console.log('文件上传中...');
			},
			success : function(response, input){
				var attachmentObject = new Object();
				attachmentObject.SourceName = response.OldFileName;
				attachmentObject.SaveName = response.NewFileName;
				attachmentObject.SavePath = response.FileSavePath;
				attachmentList.push(attachmentObject);
				var _html = '<li><i class="layui-icon">&#xe621;</i>&nbsp;'+ response.OldFileName +'&nbsp;<i class="icon-remove-sign remove-file" title="删除" filepath="'+ response.FileSavePath +'"></i></li>';
				$('#attachment-list').append(_html);
			},
			unwrap : false
		});
		layui.upload({
			elem : '.add-customer-upload-file',
			url : ThinkPHP['AJAX'] + '/RMA/uploadFile',
			title : '上传文件',
			method : 'post',
			before : function(input){
				console.log('文件上传中...');
			},
			success : function(response, input){
				var attachmentObject = new Object();
				attachmentObject.SourceName = response.OldFileName;
				attachmentObject.SaveName = response.NewFileName;
				attachmentObject.SavePath = response.FileSavePath;
				attachmentList.push(attachmentObject);
				var _html = '<li><i class="layui-icon">&#xe621;</i>&nbsp;'+ response.OldFileName +'&nbsp;<i class="icon-remove-sign remove-file" title="删除" filepath="'+ response.FileSavePath +'"></i></li>';
				$('#attachment-list').append(_html);
			},
			unwrap : false
		});

		//监听rma处理表单
		form.on('submit(rmaCustomer)', function( data ){
			var fields = data.field;
			if( attachmentList.length == 0 ){	//如果附件为空则提交空字符串，如果不为空则转换为json格式数据提交
				fields.attachments = '';
			}else{
				fields.attachments = JSON.stringify(attachmentList);
			}
			console.log(fields);
			$.ajax({
				url : ThinkPHP['AJAX'] + '/RMA/addRMA_OperationLog',
				type : 'POST',
				data : fields,
				dataType : 'json',
				beforeSend : function(){
					layer.load(2,{shade:[0.8,'#fff']});
				},
				success : function(response){
					if( response.flag > 0 ){
						layer.msg(response.msg,{icon : 1,time : 2000});
						setTimeout(function(){
							location.reload();
						},2000);
					}else{
						layer.msg(response.msg,{icon : 2,time : 2000});
						setTimeout(function(){
							layer.closeAll();
						},2000);
					}
				}
			});
			return false;
		});

	});


	//文件下载
	$('a.download').click(function(){
		$(this).parent().submit();
	});













































	$('.filter-btn').click(function(){
		var filter_dom = $('#rma-filter'),
			caret_dom = $(this).find('i'),
			icon_type = $(this).find('i').attr('class');
		if( icon_type == 'icon-caret-down' ){
			caret_dom.attr('class','icon-caret-up');
		}else{
			caret_dom.attr('class','icon-caret-down');
		}
		filter_dom.slideToggle('fast');
	});


	// 监听滚动，如果添加处理日志存在并且出现返回顶部按钮则将添加日志按钮向上调整
	$('#content').scroll(function(){

		if( $('.addOperationLog') ){
			if( $('#scrollBackTop').hasClass('sr-only') ){
				$('.addOperationLog').css({bottom : '60px'});
			}else{
				$('.addOperationLog').css({bottom : '122px'});
			}
		}

	});






	//点击弹出rma处理窗口
	/*$(document).on('click','.is_rma_box',function(){
		/!*$('.sale-audit-box').removeClass('sr-only');
		layer.open({
			type : 1,
			skin: 'layui-layer-rim',
			area : '500px',
			content : $('.sale-audit-box'),
			zIndex : 0,
			close : function(){
				$('.sale-audit-box').addClass('sr-only');
			}
		});*!/
	});*/


	var webuploader_option = {
		auto: true,
		server: ThinkPHP['AJAX'] + '/RMA/uploadFile',
		pick: '#uploadAttachment',
		fileVal : 'Filedata',
		accept: {
			title: 'file',
			extensions: 'jpg,png,jpeg,doc,xls,xlsx,docx,pdf'
		},
		method: 'POST',
	};
	// 实例化webuploader
	var uploader = WebUploader.create(webuploader_option);
	uploader.on('uploadSuccess', function(file, response){	//文件了上传成功后触发
		if( response.flag > 0 ){
			//当文件上传成功后添加到attachmentData
			var attachmentObject = new Object();
			attachmentObject.SourceName = response.OldFileName;
			attachmentObject.SaveName = response.NewFileName;
			attachmentObject.SavePath = response.FileSavePath;
			attachmentData.push(attachmentObject);
			var _html = '<li><i class="layui-icon">&#xe621;</i>&nbsp;'+ response.OldFileName +'&nbsp;<i class="icon-remove-sign remove-file" title="删除" filepath="'+ response.FileSavePath +'"></i></li>';
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

	$('.file-list,#attachment-list').on('click','.remove-file',function(){
		var _filepath = $(this).attr('filepath'),
			_parent = $(this).parent();
		$.ajax({
			url : ThinkPHP['AJAX'] + '/RMA/removeFile',
			type : 'POST',
			data : {
				filepath : _filepath
			},
			dataType : 'json',
			success : function(response){
				if(response.flag){
					_parent.remove();
				}
			}
		});
	});









	//alert($('body').width());

	//多余的文本转换成省略号
	//$('.media-heading-edit').css({'overflow':'hidden','white-space':'nowrap','text-overflow':'ellipsis','width':$(window).width()-385});

	//初始化添加处理记录模态框
	$('#customer-modal').modal({
		backdrop:false,
		show:false
	});
	
	//初始化选择销售人员模态框
	$('#sales-modal').modal({
		backdrop:false,
		show:false
	});
	
	//点击销售人员添加active类
	$('#user-list li').click(function(){
		$(this).addClass('active').siblings().removeClass('active');
		$('#salesperson_error').html('');
		$('#salesperson').val($(this).text());
		$('#userAccount').val($(this).attr('account'));
	});


	//选择产品型号
	$('#pn').click(function(){
		//alert(1);
	});
	
	//新增客诉验证ajax处理
	$('#form').validate({
		focusInvalid : true,
		rules : {
			salesperson : {
				required : true,
			},
			customer : {
				required : true,
			},
			pn : {
				required : true,
			},
			vendor : {
				required : true,
			},
			model : {
				required : true,
			},
			error_message : {
				required : true,
			},
			reason : {
				required : true,
			},
		},
		messages : {
			salesperson : {
				required : '请选择销售人员',
			},
			customer : {
				required : '客户不能为空',
			},
			pn : {
				required : '请输入产品信息',
			},
			vendor : {
				required : '请输入设备厂商',
			},
			model : {
				required : '请输入设备型号',
			},
			error_message : {
				required : '请输入错误信息',
			},
			reason : {
				required : '请输入原因分析',
			},
		},
		ignore : "",
		errorElement : 'b',
		errorPlacement : function(error, element) {
			if(element.attr('name')=='salesperson'){
				error.appendTo(element.parents('#form').find('#salesperson_error'));
			}else{
			    error.appendTo(element.next()); 
			}
		},
		submitHandler : function(form){
			$('#loading').css('display','block');
			$(form).ajaxSubmit({
				url : ThinkPHP['AJAX'] + '/RMA/addCustomer',
				type : 'POST',
				data : {
					cc_time : $('#cc_time').val(),
					salesperson : $('#userAccount').val(),
					salesperson_name : $('#salesperson').val(),
					customer : $('#customer').val(),
					sale_order : $('#sale_order').val(),
					pn : $('#pn').val(),
					vendor : $('#vendor').val(),
					model : $('#model').val(),
					error_message : $('#error_message').val(),
					reason : $('#reason').val(),
					comments : $('#comments').val(),
					managers : $('#manager-id').val()
				},
				dataType : 'json',
				success : function(response){
					if(response.flag){
						$('#loading').css('display','none');
						$('.modal-message-success .message-text').text(response.msg);
						$('.modal-message-success').show();
						$('#message-modal').modal('show');
						setTimeout(function(){
							$('.modal-message-success').hide();
							location.href = ThinkPHP['ROOT'] + '/customerDetails/'+response.id;
						},1200);
					}else{
						$('.modal-message-error .message-text').text(response.msg);
						$('.modal-message-error').show();
						$('#message-modal').modal('show');
						setTimeout(function(){
							$('.modal-message-error').hide();
						},1200);
					}
				}
			});
		},
	});

	$('#message-modal').modal({
		show : false,
		backdrop : false,
	});

	//点击图像放大
	var documentWidth = $(document).width();
	var documentHeight = $(window).innerHeight();
	//alert(documentHeight);
	$('.customer-complaint-img').click(function(){
		var _this = $(this);
		var source = $(this).attr('src');
		$('.pictureContainer').html('<img class="appendImg" src="'+ source +'" alt="...">');
		var size = $('.pictureContainer img').width();
		var img = new Image();
		img.src = source;
		var w = img.width;
		var h = img.height;
		if(w>=documentWidth && h>=documentHeight){
			//当图像展开后禁用滚动条
			//$('#content-box').mCustomScrollbar('disable');
			$('.pictureContainer img').css({'max-width':(documentWidth-360)+'px'});
			setTimeout(function(){
				var imgHeight = $('.pictureContainer img').height();
				$('.pictureContainer img').css({'position':'absolute','left':'50%','marginLeft':-(documentWidth-360)/2+'px','top':'50%','marginTop':-(imgHeight/2)+'px'});
			},50);
		}else if(w>=documentWidth && h<documentHeight){
			//当图像展开后禁用滚动条
			$('#content-box').mCustomScrollbar('disable');
			$('.pictureContainer img').css({'max-width':(documentWidth-360)+'px'});
			setTimeout(function(){
				var imgHeight = $('.pictureContainer img').height();
				$('.pictureContainer img').css({'position':'absolute','left':'50%','marginLeft':-(documentWidth-360)/2+'px','top':'50%','marginTop':-(imgHeight/2)+'px'});
			},50);
		}else if(w<documentWidth && h>=documentHeight){
			//当图像展开后禁用滚动条
			$('#content-box').mCustomScrollbar('disable');
			$('.pictureContainer img').css({'max-height':(documentHeight-120)+'px'});
			setTimeout(function(){
				var imgWidth = $('.pictureContainer img').width();
				$('.pictureContainer img').css({'position':'absolute','left':'50%','marginLeft':-(imgWidth/2)+'px','top':'50%','marginTop':-(documentHeight-120)/2+'px'});
			},50);
		}else if(w<documentWidth && h<documentHeight){
			//当图像展开后禁用滚动条
			$('#content-box').mCustomScrollbar('disable');
			setTimeout(function(){
				var imgWidth = $('.pictureContainer img').width();
				var imgHeight = $('.pictureContainer img').height();
				$('.pictureContainer img').css({'position':'absolute','left':'50%','marginLeft':-(imgWidth/2)+'px','top':'50%','marginTop':-(imgHeight/2)+'px'});
			},50);
		}
		$('#picturePreview').fadeIn('normal');
	});

	//点击关闭图像预览
	$('.closePreview').click(function(){
		//当图像关闭后开启滚动条
		//$('#content-box').mCustomScrollbar('update');
		$('#picturePreview').fadeOut('normal');
	});

	/**
	 * uploader : ThinkPHP['AJAX'] + '/RMA/uploadFile',
	 * uploadLimit : ThinkPHP['UPLOADIFY_CONFIG_UPLOADLIMIT'],
	 * fileTypeExts : ThinkPHP['UPLOADIFY_CONFIG_FILETYPEEXTS'],
	 * fileSizeLimit : ThinkPHP['UPLOADIFY_CONFIG_FILESIZELIMIT']*1024*1024,
	 */
	//console.log(ThinkPHP['UPLOADIFY_CONFIG_UPLOADLIMIT']+' | '+ThinkPHP['UPLOADIFY_CONFIG_FILETYPEEXTS']+' | '+ThinkPHP['UPLOADIFY_CONFIG_FILESIZELIMIT']*1024*1024);


	//引入uploadify插件并配置相关参数
	var fileArr = new Array();
	$('#file').Huploadify({
		//提交到服务器
		method : 'post',
		auto : true,
		uploader : ThinkPHP['AJAX'] + '/RMA/uploadFile',
		//为上传按钮添加类名
		//buttonClass : 'btn btn-warning upload-btn',
		//定义显示在默认按钮上的文本
		buttonText : '上传文件',
		//设置上传按钮的宽高
		fileObjName : 'Filedata',
		width : 140,
		height : 80,
		//是否显示上传进度百分比和文件大小
		/*showUploadedPercent : true,
		 showUploadedSize : true,*/
		//定义允许的最大上传数量
		//uploadLimit : 1,
		//设置值为false时，一次只能选中一个文件
		multi : true,
		uploadLimit : ThinkPHP['UPLOADIFY_CONFIG_UPLOADLIMIT'],
		//设置上传完成后从上传队列中移除的时间（单位：秒）
		//removeTimeout : 1,
		//图片类型
		fileTypeDesc : '图片类型',
		//过滤文件格式
		fileTypeExts : ThinkPHP['UPLOADIFY_CONFIG_FILETYPEEXTS'],
		//限制文件大小5m
		fileSizeLimit : ThinkPHP['UPLOADIFY_CONFIG_FILESIZELIMIT']*1024*1024,
		onUploadSuccess : function(file,data,response){
			var file_exists = true;
			if(data){
				var obj = $.parseJSON(data);
				if(obj.flag){
					//禁止重复文件dom元素存在
					if($('#file-list').children().length > 0){
						$('#file-list li').each(function(index){
							if($(this).find('small').text()==obj.OldFileName){
								$().toastmessage('showToast', {
									text : '文件已存在!',
									sticky : false,
									position : 'top-right',
									type : 'error',
									inEffectDuration : 600,
									stayTime : 3000,
								});
								file_exists = false;
							}
						});
					}
					if( file_exists ){
						fileArr.push(obj.FileSavePath);
						var fileTotal = fileArr.length;
						$('#fileTotal').text(fileTotal);
						var html = '<li><div class="file-pic"><img src="' + ThinkPHP['IMG'] + '/file_preview.png"></div><small class="file-name">' + obj.OldFileName + '</small><span source="' + obj.FileSavePath + '" class="remove-file" title="删除"></span></li>';
						$('#file-list').append(html);
					}
				}
			}else{

			}
		},
		onSelectError : function (file, errorCode, errorMsg) {
			//配置上传错误信息
			var msgText = '';
			switch (errorCode) {
				case -100:
					msgText = "上传的文件数量已经超出系统限制的" + $('#file').uploadify('settings', 'uploadLimit') + "个文件！";
					break;
				case -110:
					msgText = "文件 [" + file.name + "] 大小超出系统限制的" + $('#file').uploadify('settings', 'fileSizeLimit') + "大小！";
					break;
				case -120:
					msgText = "文件 [" + file.name + "] 大小异常！";
					break;
				case -130:
					msgText = "文件 [" + file.name + "] 类型不正确！";
					break;
			}
			$('.customer-message-error .message-text').text(msgText);
			$('.customer-message-error').show();
			$('#message-modal').modal('show');
			setTimeout(function(){
				$('#message-modal').modal('hide');
				$('.customer-message-error').hide();
			},8800);
		},
		onUploadError : function(file,errorCode,errorMsg,errorString){
			// 手工取消不弹出提示
			if (errorCode == SWFUpload.UPLOAD_ERROR.FILE_CANCELLED || errorCode == SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED) {
				return;
			}
			var msgText = "上传失败\n";
			switch (errorCode) {
				case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
					msgText += "HTTP 错误\n" + errorMsg;
					break;
				case SWFUpload.UPLOAD_ERROR.MISSING_UPLOAD_URL:
					msgText += "上传文件丢失，请重新上传";
					break;
				case SWFUpload.UPLOAD_ERROR.IO_ERROR:
					msgText += "IO错误";
					break;
				case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
					msgText += "安全性错误\n" + errorMsg;
					break;
				case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
					msgText += "每次最多上传 " + this.settings.uploadLimit + "个";
					break;
				case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
					msgText += errorMsg;
					break;
				case SWFUpload.UPLOAD_ERROR.SPECIFIED_FILE_ID_NOT_FOUND:
					msgText += "找不到指定文件，请重新操作";
					break;
				case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
					msgText += "参数错误";
					break;
				default:
					msgText += "文件:" + file.name + "\n错误码:" + errorCode + "\n"
						+ errorMsg + "\n" + errorString;
			}
			$('.customer-message-error .message-text').text(msgText);
			$('.customer-message-error').show();
			$('#message-modal').modal('show');
			setTimeout(function(){
				$('#message-modal').modal('hide');
				$('.customer-message-error').hide();
			},800);
		}
	});
	
	//确定添加处理记录/验证表单数据
	$('#processingRecords').validate({
		focusInvalid : false,
		rules : {
			log_date : {
				required : true,
			},
			log_content : {
				required : true,
			},
			recorder : {
				required : true,
			},
		},
		messages : {
			log_date : {
				required : '请选择处理日期',
			},
			log_content : {
				required : '请输入处理记录',
			},
			recorder : {
				required : '请输入处理人',
			},
		},
		ignore : "",
		errorElement : 'b',
		errorPlacement : function(error, element) {
			if(element.attr('name')=='log_content'){
				error.appendTo(element.next());
			}else if(element.attr('name')=='log_date'){
				error.appendTo(element.parent().next());
			}else{
			    error.appendTo(element.next()); 
			}
		},
		submitHandler : function(form){
			//如果数组为空则值为空，反之则转换为json数据格式提交
			if(fileArr.length==0){
				var newFileArr = '';
			}else{
				var newFileArr = JSON.stringify(fileArr);	//将数组转换为json
			}
			$(form).ajaxSubmit({
				url : ThinkPHP['AJAX'] + '/RMA/addComplaintLog',
				type : 'POST',
				data : {
					cc_id : $('#cc_id').val(),
					log_date : $('#dtp_input2').val(),
					status : $('#status').val(),
					log_content : $('#log_content').val(),
					attachment : newFileArr,
					recorder : $('#recorder').val(),
				},
				dataType : 'json',
				success : function(response){
					if(response.flag==1){
						$('.customer-message-success .message-text').text(response.msg);
						$('.customer-message-success').show();
						$('#message-modal').modal('show');
						setTimeout(function(){
							$('.customer-message-success').hide();
							$('#file-list').remove('*');
							//清空图像数组
							fileArr.splice(0,fileArr.length);
							location.reload();
							//数量重置
							$('#fileTotal').text('0');
							//还原状态默认值
							$('#deal-text').text('正在处理');
							$('#status').val(0);
							//清空处理日期
							$('#deal-date').val('');
							//清空处理记录内容文本
							$('#log_content').val('');
						},1500);
					}else{
						$('.customer-message-error .message-text').text(response.msg);
						$('.customer-message-error').show();
						$('#message-modal').modal('show');
						setTimeout(function(){
							$('.customer-message-error').hide();
							$('#message-modal').modal('hide');
						},1500);
					}
				}
			});
		},
	});

	//数据筛选模态框初始化
	$('#FilterModal').modal({
		backdrop : true,
		show : false,
	});

	//将选中的状态添加到数据筛选
	$('#statusList button').click(function(){
		$('#filterStatus').val($(this).attr('state'));
		$(this).addClass('active').siblings().removeClass('active');
	});

	//数据筛选表单提交事件
	$('#FilterForm').submit(function(){
		$('#FilterForm input[type=text],#FilterForm input[type=hidden]').each(function(){
			if($(this).val()==''){
				$(this).remove();
			}
		});
	});

	//清空筛选条件
	$('#RemoveFilter').click(function(){
		$('#FilterForm input[type=text]').val('');
		$('#filterStatus').val(2);
		$('#statusList button').removeClass('active');
		$('#AllStatus').addClass('active');
	});

	//搜索
	$('#submitSearch').click(function(){
		$('#SearchHiddenText').val($('#SearchText').val());
		$('#SearchHiddenForm').submit();
	});
	
	//删除图像
	$('#file-preview').on('click','.remove-file',function(){
		var _this = $(this);
		var _index = _this.parent().index();
		var source = $(this).attr('source');
		$.post(ThinkPHP['AJAX'] + '/RMA/unlinkPic',{source:source},function(data){
			if(data=='true'){
				//图像删除后移除该DOM元素
				_this.parent().remove();
				//重新计算文件数量并删除对应数组内的元素
				fileArr.splice(_index,1);
				$('#fileTotal').text(fileArr.length);
			}
		},'json');
	});
	
	//选择处理状态
	$('#dropdown-list li').click(function(){
		$('#deal-text').text($(this).text());
		$('#status').val($(this).attr('status'));
	});





	//获取到筛选初始数据
	var initFilterBody = $('.filter-body').html();

	//初始化选择产品模态框
	$('#product-modal').modal({
		backdrop : false,
		show : false,
	});

	//展开筛选后显示产品列表并禁用mCustomScrollbar插件
	$('#select-product-btn').click(function(){
		$('#product-modal').modal('show');
		//$('#content-box').mCustomScrollbar('disable');
	});

	//当产品选择模态框被隐藏时激活滚动条
	/*$('#product-modal').on('hidden.bs.modal',function(e){
		$('#content-box').mCustomScrollbar('update');
	});*/
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
			console.log(flag);
		}else{
			//alert('false');
			_hidden_li.css('display','none');
			_this.find('span').text('更多');
			i.attr('class','icon-angle-down');
			dropdown = true;
			_this.attr('flag','true');
			console.log(flag);
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
		if($('#manager-id').val()==''){
			$('#manager-id').val(manager);
		}else{
			$('#manager-id').val($('#manager-id').val()+',' + manager);
		}
		//如果产品信息不为空则叠加
		if($.trim($('#pn').val())!=''){
			$('#pn').val($('#pn').val()+' , '+pn);
		}else{
			$('#pn').val(pn);
		}
		$('#product-modal').modal('hide');
	});

	//清空产品型号
	$('#clear-product').click(function(){
		if($(this).parent().prev().val()!=''){
			$(this).parent().prev().val('');
			$('#manager-id').val('');
		}
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
















