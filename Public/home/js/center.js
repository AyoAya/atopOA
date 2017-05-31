/**
 * center个人中心js
 */
$(function(){

	//初始化修改资料模态框
	$('#modify-modal').modal({
		backdrop:false,
		show:false,
	});

	//修改基本信息
	$('#basicForm').validate({
		focusInvalid : true,
		rules : {
			nickname : {
				required : true,
			},
			email : {
				required : true,
				email : true,
			},
		},
		messages : {
			nickname : {
				required : '用户名不能为空',
			},
			email : {
				required : '邮箱不能为空',
				email : '请输入合法的电子邮箱',
			},
		},
		ignore : "",
		errorElement : 'b',
		errorPlacement : function(error, element) {
			error.appendTo(element.next());
		},
		submitHandler : function(form){
			$(form).ajaxSubmit({
				url : ThinkPHP['AJAX'] + '/Center/changeEameEmail',
				type : 'POST',
				data : {
					nickname : $('#basicForm input[name=nickname]').val(),
					email : $('#basicForm input[name=email]').val(),
				},
				dataType : 'json',
				success : function(response){
					if(response.flag > 0){
						$('.modal-message-success .message-text').text(response.msg);
						$('.modal-message-success').show();
						$('#modify-modal').modal('show');
						setTimeout(function(){
							$('.modal-message-success').hide();
							location.reload();
						},1000);
					}else{
						$('.modal-message-error .message-text').text(response.msg);
						$('.modal-message-error').show();
						$('#modify-modal').modal('show');
						setTimeout(function(){
							$('.modal-message-error').hide();
							$('#modify-modal').modal('hide');
						},1000);
					}
				}
			});
		},
	});

	//修改密码
	$('#passwordForm').validate({
		focusInvalid : true,
		rules : {
			oldpassword : {
				required : true,
				remote : {
					url : ThinkPHP['AJAX'] + '/Center/checkOldpassword',
					type : 'POST',
					dataType : 'json',
					data : {
						oldpassword : function(){
							return $('#passwordForm input[name=oldpassword]').val();
						}
					}
				},
			},
			newpassword : {
				required : true,
				minlength : 6,
				maxlength : 20,
			},
			repassword : {
				required : true,
				equalTo : '#newpassword',
			},
		},
		messages : {
			oldpassword : {
				required : '原密码不能为空',
				remote : '原密码错误',
			},
			newpassword : {
				required : '密码不能为空',
				minlength : '密码长度不能小于6位',
				maxlength : '密码长度不能大于20位',
			},
			repassword : {
				required : '请再次输入密码',
				equalTo : '密码输入不一致',
			},
		},
		ignore : "",
		errorElement : 'b',
		errorPlacement : function(error, element) {
			error.appendTo(element.next());
		},
		submitHandler : function(form){
			$(form).ajaxSubmit({
				url : ThinkPHP['AJAX'] + '/Center/editPassword',
				type : 'POST',
				data : {
					password : $('#passwordForm input[name=newpassword]').val(),
				},
				dataType : 'json',
				success : function(response){
					if(response.flag > 0){
						$('.modal-message-success .message-text').text(response.msg);
						$('.modal-message-success').show();
						$('#modify-modal').modal('show');
						setTimeout(function(){
							$('.modal-message-success').hide();
							location.reload();
						},1000);
					}else{
						$('.modal-message-error .message-text').text(response.msg);
						$('.modal-message-error').show();
						$('#modify-modal').modal('show');
						setTimeout(function(){
							$('.modal-message-error').hide();
							$('#modify-modal').modal('hide');
						},1000);
					}
				}
			});
		},
	});

	$('#message-modal').modal({
		backdrop: true,
		show: false,
	});

	//修改头像
	$('#faceList li').click(function(){
		var _this = $(this);
		var face = $(this).attr('face');
		$.post(ThinkPHP['AJAX'] + '/Center/saveFace',{face:face},function(data){
			if(data.flag){
				$('.modal-message-success .message-text').text(data.msg);
				$('.modal-message-success').show();
				$('#message-modal').modal('show');
				setTimeout(function(){
					$('#face-picture').attr('src',face);
					$('#faceList li').removeClass('active');
					_this.addClass('active');
					$('#message-modal').modal('hide');
					$('.modal-message-success').hide();
				},500);
			}else{
				$('.modal-message-error .message-text').text(data.msg);
				$('.modal-message-error').show();
				$('#message-modal').modal('show');
				setTimeout(function(){
					$('#message-modal').modal('hide');
					$('.modal-message-error').hide();
				},500);
			}
		},'json');
	});

	$('#file').Huploadify({
		auto : true,
		//提交到服务器
		uploader : ThinkPHP['AJAX'] + '/Center/uploadFace',
		//定义显示在默认按钮上的文本
		buttonText : '上传头像',
		//设置上传按钮的宽高
		fileObjName : 'Filedata',
		//设置上传按钮的宽高
		width : 120,
		height : 40,
		//设置值为false时，一次只能选中一个文件
		multi : false,
		//图片类型
		fileTypeDesc : '图片类型',
		//过滤文件格式
		fileTypeExts : '*.jpg;*.png;*.gif;*.jpeg;',
		//限制文件大小5m
		fileSizeLimit : 5242880,
		//上传成功之后执行回调函数(file:返回文件信息，data：返回服务器回调信息，response：返回真或假)
		onUploadSuccess : function(file,data,response){
			if(data){
				$('#file').hide();
				$('#faceList').hide();
				$('#uploadFaceBTN').hide();
				var obj = $.parseJSON(data);
				var FileSavePath = obj.FileSavePath;
				if($('#target').attr('src')!='' && $('#FacePreviewBox').attr('src')!=''){
					$.post(ThinkPHP['AJAX']+'/Center/cancelUploadFace',{
						path : $('#target').attr('src'),
					},function(data){
						if(data=='true'){
							FileSavePath = obj.FileSavePath;
						}
					});
				}
				$('#target').attr('src',FileSavePath);
				$('#FacePreviewBox').attr('src',FileSavePath);
				//NewFileName
				$('#NewFileName').val(obj.NewFileName);
				$('#face-preview').show();
				jcropInit();
			}
		},
		onFallback : function(){
			$('#flashFooter').show();
			$('.modal-message-flash-info').show();
			$('#message-modal').modal('show');
			setTimeout(function(){
				$('#message-modal').modal('hide');
				$('.modal-message-flash-info').hide();
				$('#flashFooter').hide();
			},10000);
			$('#file').remove();
			$('#file').uploadify('disabled');
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
			$('.modal-message-error .message-text').text(msgText);
			$('.modal-message-error').show();
			$('#message-modal').modal('show');
			setTimeout(function(){
				$('#message-modal').modal('hide');
				$('.modal-message-error').hide();
			},800);
			//alert(msgText);
		}
	});

	//图像裁剪插件
	function jcropInit(){
			window.jcrop_api;
			window.boundx;
			window.boundy;
			// Grab some information about the preview pane
			window.preview = $('#preview-pane');
			window.pcnt = $('#preview-pane .preview-container');
			window.pimg = $('#preview-pane .preview-container img');
			window.xsize = window.pcnt.width();
			window.ysize = window.pcnt.height();
		$('#target').Jcrop({
			onChange: updatePreview,
			onSelect: updatePreview,
			aspectRatio: 1 / 1,
			minSize:[70,70],
			boxHeight:470,
		},function(){
			// Use the API to get the real image size
			window.bounds = this.getBounds();
			window.boundx = window.bounds[0];
			window.boundy = window.bounds[1];
			// Store the API in the jcrop_api variable
			jcrop_api = this;

			// Move the preview into the jcrop container for css positioning
			window.preview.appendTo(window.jcrop_api.ui.holder);
		});
	}
	function updatePreview(c){
		if (parseInt(c.w) > 0){
			var rx = window.xsize / c.w;
			var ry = window.ysize / c.h;
			window.pimg.css({
				width: Math.round(rx * window.boundx) + 'px',
				height: Math.round(ry * window.boundy) + 'px',
				marginLeft: '-' + Math.round(rx * c.x) + 'px',
				marginTop: '-' + Math.round(ry * c.y) + 'px'
			});
		}
	};

	//确定保存上传头像
	$('#faceDone').click(function(){
		var cutSizeInfo = window.jcrop_api.tellSelect();
		var getBounds = window.jcrop_api.getBounds();
		$.post(ThinkPHP['AJAX'] + '/Center/saveUploadFace',{
			path : $('#target').attr('src'),
			name : $('#NewFileName').val(),
			x : cutSizeInfo.x,
			y : cutSizeInfo.y,
			w : cutSizeInfo.w,
			h : cutSizeInfo.h,
		},function(data){
			if(data.flag){
				$('#uploadFaceBTN').show();
				$('.modal-message-success .message-text').text(data.msg);
				$('.modal-message-success').show();
				$('#message-modal').modal('show');
				setTimeout(function(){
					location.reload();
					$('#face-picture').attr('src',data.facepath);
					$('#message-modal').modal('hide');
					$('.modal-message-success').hide();
					$('#face-preview').hide();
					$('#faceList').show();
				},800);
			}else{
				$('.modal-message-error .message-text').text(data.msg);
				$('.modal-message-error').show();
				$('#message-modal').modal('show');
				setTimeout(function(){
					$('#message-modal').modal('hide');
					$('.modal-message-error').hide();
				},800);
			}
		},'json');
	});

	//取消上传头像
	$('button#faceCancel').bind('click',function(){
		var path = $('#target').attr('src');
		$.post(ThinkPHP['AJAX'] + '/Center/cancelUploadFace',{
			path : path,
		},function(data){
			if(data=='true'){
				$('#file').show();
				$('#faceList').show();
				$('#uploadFaceBTN').show();
				$('#face-preview').hide();
			}
		});
	});

});










