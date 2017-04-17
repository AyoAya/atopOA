/**
 * research 页面js
 */
$(function(){

	
	/*//选择审核人员
	$('#approval').click(function(){
		//配置dialog弹出层样式配置及内容
		layer.open({
			type : 1,	//layer提供了5种层类型。可传入的值有：0（信息框，默认）1（页面层）2（iframe层）3（加载层）4（tips层）。
			btn : ['确定'],
			yes : function(index,layero){
				$('#user-select-box li').each(function(){
					if($(this).hasClass('active')){
						$('#approval').val($(this).text());
						$('#aid').val($(this).attr('index'));
					}
					if($('#approval').val()!=''){
			    		$('#approval').css('border','solid 1px #ccc');
			    		$('#approval').prev().find('em').remove();
			    	}
				});
				layer.close(index);
			},
			title : '选择审核人员',
			area : ['640px','400px'],
			shadeClose : true,		//点击幕布区域是否关闭弹出层
			shade : 0.5,			//幕布透明度
			fix : true,				//鼠标滚动时，层是否固定在可视区域
			moveType : 1,			//拖动效果
			shift : 5,				//动画0-6
			content : $('#dialog-content'),
		});
	});
	
	
	
	$('#dialog-content ul li').click(function(){
		$(this).addClass('active').siblings().removeClass('active');
	});*/

	//获取到筛选初始数据
	var initFilterBody = $('.filter-body').html();


	//初始化模态框
	$('#ProductEngineer').modal({
		backdrop : false,
		show : false,
	});

	//审核模态框
	$('#audit-modal').modal({
		backdrop : false,
		show : false,
	});

	//物料准备模态框
	$('#metarial-modal').modal({
		backdrop : false,
		show : false,
	});

	//样品制作模态框
	$('#productEngineer-modal').modal({
		backdrop : false,
		show : false,
	});

	//样品测试模态框
	$('#testEngineer-modal').modal({
		backdrop : false,
		show : false,
	});

	//发货模态框
	$('#delivery-modal').modal({
		backdrop : false,
		show : false,
	});

	//反馈模态框
	$('#feedback-modal').modal({
		backdrop : false,
		show : false,
	});


	var jsonStr = '{"allOrder":[';	//将所有产品型号拼装为json格式

	$('#AllDynamicBox .dynamicboxContainer').eq(0).find('.input-d-date').datetimepicker({
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

	//随机生成订单号
	function generateOrderNumber(number){
		var order = '';
		var time = new Date().getTime();
		var letter = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
		var arr = letter.split('');
		for(var i=0;i<2;i++){
			order += arr[Math.floor(1 + Math.random() * (letter.length - 1))];
		}
		order += Math.floor(100 + Math.random() * (999 - 100));
		order += time;
		return order;
	}
	//alert(Math.floor(1 + Math.random() * (24 - 1)));
	//$('.nowOrderNo').val();
	//alert(generateOrderNumber(2));

	//添加样品输入栏
	var orderTotal = 1;
	//获取到需要动态生成的dom元素内容
	var html = $('.dynamicboxContainer').eq(0).html();
	$('.btn-box').on('click','.sidebarAdd',function(){
		$('#filterForm input').val('');
		$('.filter-body').html(initFilterBody);
		//将动态dom元素追加到后方
		$('#AllDynamicBox').append('<div class="panel panel-primary dynamicboxContainer">'+html+'</div>');
		jsonStr = '{"allOrder":[';
		orderTotal++;
		$('#AllDynamicBox .dynamicboxContainer').each(function(index){
			$(this).find('.orderNO').text(index+1);
		});
		var containerLength = $('#AllDynamicBox .dynamicboxContainer').length;
		$('#AllDynamicBox .dynamicboxContainer').eq(length-1).find('.input-d-date').datetimepicker({
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
		//console.log(html);
	});


	//点击审批人员添加class
	$('#user-select-box li').click(function(){
		//$('.approvalBox .help-block').html('');
		$('#aid').val($(this).attr('index'));
		$('#approval').val($(this).text());
		$(this).addClass('active').siblings().removeClass('active');
	});

	//移除订单
	$('#AllDynamicBox').on('click','.removeOrder',function(){
		var _this = $(this);
		if($('.removeOrder').length>1){
			_this.parents('.dynamicboxContainer').slideUp('normal');
			setTimeout(function(){
				_this.parents('.dynamicboxContainer').remove();
				jsonStr = '{"allOrder":[';
				orderTotal--;
				$('#AllDynamicBox .dynamicboxContainer').each(function(index){
					$(this).find('.orderNO').text(index+1);
				});
				if($('.removeOrder').length==1){
					$('#AllDynamicBox .dynamicboxContainer .orderNO').text('');
				}
			},400);
		}else{
			//当页面只存在一个订单时，不允许移除并给予提示
			$().toastmessage('showToast', {
				text : '至少保留一个',
				sticky : false,
				position : 'top-right',
				type : 'warning',
				inEffectDuration : 600,
				stayTime : 3000,
			});
		}
	});

	//初始化选择产品模态框
	$('#select-product').modal({
		backdrop : false,
		show : false,
	});

	//点击选择产品
	var nowDynamicbox = null;
	$('#AllDynamicBox').on('click','.btn-select-filter',function(){
		var _this = $(this);
		//为保证当前操作独立，将当前操作订单表的父辈存放变量
		var _parents = _this.parents('.dynamicbox');
		nowDynamicbox = _this.parents('.dynamicboxContainer');
		//获取到当前点击元素的文本
		var type = $(this).find('a').text();
		$('#select-product').modal('show');
		$('#content-box').mCustomScrollbar('disable');
	});

	$('#select-product').on('hidden.bs.modal',function(e){
		$('#content-box').mCustomScrollbar('update');
	});
	//查看更多
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



	//点击产品类型联动产品[动态绑定事件]
	$('#AllDynamicBox').on('click','.product-type-list li',function(){
		var _this = $(this);
		//为保证当前操作独立，将当前操作订单表的父辈存放变量
		var _parents = _this.parents('.dynamicbox');
		//获取到当前点击元素的文本
		var type = $(this).find('a').text();
		var str = '';
		$.post(ThinkPHP['AJAX']+'/Sample/changeProductType',{
			type : type,
		},function(data){
			_parents.find('.product-type-name').text(type);
			for(key in data){
				//遍历后台获取的数据并叠加保存成字符串
				str += '<li p_id="'+data[key].pid+'" nickname="'+ data[key].nickname +'" manager="'+ data[key].manager +'" pn="'+data[key].pn+'"><a href="javascript:void(0);">'+ data[key].pn +'</a></li>';
			}
			//将叠加的数据添加到产品型号列
			_parents.find('.product-list').html(str);
			//获取首行数据添加到对应栏
			_parents.find('.input-product-model').text(_parents.find('.product-list li:first-child').find('a').text());
			_parents.find('.input-product-manager').val(_parents.find('.product-list li:first-child').attr('nickname'));
			_parents.find('.form-group-input-hidden-manager').val(_parents.find('.product-list li:first-child').attr('manager'));
			_parents.find('.hidden-input-pid').val(_parents.find('.product-list li:first-child').attr('p_id'));
			_parents.find('.input-product-manager').val(_parents.find('.product-list li:first-child').attr('nickname'));
			//console.log(data);
		},'json');
	});

	//点击产品型号锁定产品经理
	$('#AllDynamicBox').on('click','.product-list li',function(){
		var _this = $(this);
		//为保证当前操作独立，将当前操作订单表的父辈存放变量
		var _parents = _this.parents('.dynamicbox');
		var pn = _this.attr('pn');
		var nickname = _this.attr('nickname');
		var manager = _this.attr('manager');
		var pid = _this.attr('p_id');
		_parents.find('.input-product-model').text(pn);
		_parents.find('.form-group-input-hidden-manager').val(manager);
		_parents.find('.hidden-input-pid').val(pid);
		_parents.find('.input-product-manager').val(nickname);
	});

	//上传附件
	var attachment = new Array();
	var subscript = 0;
	$('#fileinput').Huploadify({
		method : 'post',
		auto : true,
		//提交到服务器
		uploader : ThinkPHP['AJAX'] + '/Sample/uploadAttachment',
		buttonText : '<i class="icon-paper-clip icon-2x"></i><span>上传附件</span>',
		fileObjName : 'Filedata',
		//定义允许的最大上传数量
		uploadLimit : 8,
		//设置值为false时，一次只能选中一个文件
		multi : true,
		//图片类型
		fileTypeDesc : '附件类型',
		//过滤文件格式
		fileTypeExts : '*.doc;*.docx;*.wps;*.xls;*.xlsx;*.ppt;*.pptx;*.pdf;*.zip;*.rar;',
		//限制文件大小5m
		fileSizeLimit : 5242880,
		//上传成功之后执行回调函数(file:返回文件信息，data：返回服务器回调信息，response：返回真或假)
		onUploadSuccess : function(file,data,response){
			if(data){
				var obj = eval('('+data+')');
				attachment[subscript] = [obj['type'],obj['NewFileName'],obj['FileSavePath']];
				subscript++;
				//console.log(attachment);
				$('.upload-file-list').append('<li><i class="icon-file-alt"></i>&nbsp;<span class="upload-file-text">'+obj['NewFileName']+'</span><i class="icon-remove remove-file" title="删除" filepath="'+obj['FileSavePath']+'"></i></li>');
				$('.upload-file-box').removeClass('sr-only');
				$('.upload-file-box .attachment-number').text($('.upload-file-list').children().length);
			}else{
				$().toastmessage('showToast', {
					text : '上传失败',
					sticky : false,
					position : 'top-right',
					type : 'error',
					inEffectDuration : 600,
					stayTime : 3000,
				});
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
			$().toastmessage('showToast', {
				text : msgText,
				sticky : false,
				position : 'top-right',
				type : 'error',
				inEffectDuration : 600,
				stayTime : 3000,
			});
		},
		onFallback : function(){
			$().toastmessage('showToast', {
				text : '系统检测到你当前的浏览器尚未安装<a href="https://get2.adobe.com/cn/flashplayer/" target="_blank" style="color:yellow;text-decoration:underline;">Flash插件</a>导致上传头像功能无法使用',
				sticky : false,
				position : 'top-right',
				type : 'warning',
				inEffectDuration : 600,
				stayTime : 10000,
			});
			$('#fileinput').remove();
			$('#fileinput').uploadify('disabled');
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
			$().toastmessage('showToast', {
				text : msgText,
				sticky : false,
				position : 'top-right',
				type : 'error',
				inEffectDuration : 600,
				stayTime : 3000,
			});
		}
	});



	//删除上传文件
	$('.upload-file-list').on('click','li i.remove-file',function(){
		var _this = $(this);
		var _index = _this.parent().index();
		var _parent = _this.parent();
		var filepath = $(this).attr('filepath');
		$.post(ThinkPHP['AJAX']+'/Sample/removeFile',{
			filepath : filepath,
		},function(data){
			if(data=='true'){
				subscript--;
				_parent.remove();
				attachment.splice(_index-1,1);
				//如果附件全部被删除则隐藏div
				if($('.upload-file-list').children().length==0){
					$('.upload-file-box').addClass('sr-only');
				}
				$('.upload-file-box .attachment-number').text($('.upload-file-list').children().length);
			}else{
				$().toastmessage('showToast', {
					text : '操作失败，未知错误',
					sticky : false,
					position : 'top-right',
					type : 'error',
					inEffectDuration : 600,
					stayTime : 3000,
				});
			}
		});
	});

	$(document).on('click','#product-list li',function(){
		nowDynamicbox.find('.product-type-name').val($(this).attr('type'));
		nowDynamicbox.find('.hidden-input-pid').val($(this).attr('p_id'));
		nowDynamicbox.find('.input-product-model').val($(this).attr('pn'));
		nowDynamicbox.find('.input-product-manager').val($(this).attr('nickname'));
		nowDynamicbox.find('.form-group-input-hidden-manager').val($(this).attr('manager'));
		$('#select-product').modal('hide');
	});

	//验证表单数据
	var attachmentJsonStr = '{"attachment":[';
	$('#submitAllOrder').click(function(){
		var valid = true;
		//验证输入字段
		if($('.totalOrder').val()==''){
			$('.totalOrder').focus();
			$().toastmessage('showToast', {
				text : '请输入样品单号',
				sticky : false,
				position : 'top-right',
				type : 'error',
				inEffectDuration : 400,
				stayTime : 3000,
			});
			valid = false;
			return false;
		}
		//验证样品单号唯一性
		$.ajax({
			type : 'post',
			url : ThinkPHP['AJAX']+'/Sample/checkTotalOrder',
			data : {
				totalorder : $('.totalOrder').val(),
			},
			dataType : 'json',
			async : false,
			success : function(response){
				if(response.flag==0){
					$().toastmessage('showToast', {
						text : response.msg,
						sticky : false,
						position : 'top-right',
						type : 'error',
						inEffectDuration : 400,
						stayTime : 3000,
					});
					valid = false;
				}
			}
		});
		$('.dynamicboxContainer').each(function(index){
			var _this = $(this);
			if(_this.find('input[name=product]').val()==''){
				_this.find('input[name=product]').focus();
				$().toastmessage('showToast', {
					text : '请选择产品',
					sticky : false,
					position : 'top-right',
					type : 'error',
					inEffectDuration : 400,
					stayTime : 3000,
				});
				valid = false;
				return false;
			}
			if(_this.find('input[name=customer]').val()==''){
				_this.find('input[name=customer]').focus();
				$().toastmessage('showToast', {
					text : '请输入客户名称',
					sticky : false,
					position : 'top-right',
					type : 'error',
					inEffectDuration : 400,
					stayTime : 3000,
				});
				valid = false;
				return false;
			}
			if(_this.find('input[name=brand]').val()==''){
				_this.find('input[name=brand]').focus();
				$().toastmessage('showToast', {
					text : '请输入设备品牌',
					sticky : false,
					position : 'top-right',
					type : 'error',
					inEffectDuration : 400,
					stayTime : 3000,
				});
				valid = false;
				return false;
			}
			if(_this.find('input[name=model]').val()==''){
				_this.find('input[name=model]').focus();
				$().toastmessage('showToast', {
					text : '请输入设备型号',
					sticky : false,
					position : 'top-right',
					type : 'error',
					inEffectDuration : 400,
					stayTime : 3000,
				});
				valid = false;
				return false;
			}
			var reg = /^\d+$/;
			if(_this.find('input[name=number]').val()==''){
				_this.find('input[name=number]').focus();
				$().toastmessage('showToast', {
					text : '请输入模块数量',
					sticky : false,
					position : 'top-right',
					type : 'error',
					inEffectDuration : 400,
					stayTime : 3000,
				});
				valid = false;
				return false;
			}else if(_this.find('input[name=number]').val()!='' && _this.find('input[name=number]').val()==0){
				_this.find('input[name=number]').focus();
				$().toastmessage('showToast', {
					text : '模块数量不正确',
					sticky : false,
					position : 'top-right',
					type : 'error',
					inEffectDuration : 400,
					stayTime : 3000,
				});
				valid = false;
				return false;
			}else if(_this.find('input[name=number]').val()!='' && _this.find('input[name=number]').val()!=0 && !reg.test(_this.find('input[name=number]').val())){
				_this.find('input[name=number]').focus();
				$().toastmessage('showToast', {
					text : '模块数量格式错误',
					sticky : false,
					position : 'top-right',
					type : 'error',
					inEffectDuration : 400,
					stayTime : 3000,
				});
				valid = false;
				return false;
			}
			if(_this.find('input[name=d_date]').val()==''){
				_this.find('input[name=d_date]').focus();
				$().toastmessage('showToast', {
					text : '请选择交期时间',
					sticky : false,
					position : 'top-right',
					type : 'error',
					inEffectDuration : 400,
					stayTime : 3000,
				});
				valid = false;
				return false;
			}
			//如果页面存在多个产品型号则将所有产品型号单拼装为json格式方便后台处理
			jsonStr += '{"totalorder":"'+$('.form-group-total-order input[name=totalorder]').val()+'","type":"'+_this.find('.product-type-name').val()+'","product":"'+_this.find('.input-product-model').val()+'","p_id":"'+_this.find('.hidden-input-pid').val()+'","manager":"'+_this.find('.input-product-manager').val()+'","customer":"'+_this.find('.input-customer-name').val()+'","brand":"'+_this.find('.input-equipment-brand').val()+'","model":"'+_this.find('.input-equipment-model').val()+'","comment":"'+_this.find('.textarea-comment').val().replace(/\n/g,'<br/>')+'","number":"'+_this.find('.input-number').val()+'","d_date":"'+_this.find('.input-d-date').val()+'","aid":"'+_this.find('.form-group-input-hidden-manager').val()+'"},'
		});
		//去掉json字符串最后一个字符，
		jsonStr = jsonStr.substring(0,jsonStr.length-1);
		jsonStr += ']}';
		//拼装附件json
		if($('.upload-file-list').children().length>0){
			for(var i=0;i<attachment.length;i++){
				attachmentJsonStr += '{"type":"'+attachment[i][0]+'","filename":"'+attachment[i][1]+'","filepath":"'+attachment[i][2]+'"},';
			}
			attachmentJsonStr = attachmentJsonStr.substring(0,attachmentJsonStr.length-1);
			attachmentJsonStr += ']}';
		}else{
			attachmentJsonStr = '';
		}
		//所有验证成功才提交数据
		if(valid===true){
			$.ajax({
				url: ThinkPHP['AJAX'] + '/Sample/add',
				type: 'post',
				data: {
					allorder: jsonStr,
					attachment: attachmentJsonStr,
					operation: 'audit',
				},
				dataType: 'json',
				async : false,
				beforeSend: function () {
					if ($('#loading').hasClass('sr-only')) {
						$('#loading').removeClass('sr-only');
					}
				},
				success: function (response) {
					if (response.flag > 0) {
						$('#loading').addClass('sr-only');
						$('.modal-message-success .message-text').text(response.msg);
						$('.modal-message-success').show();
						$('#message-modal').modal('show');
						setTimeout(function () {
							//$('.modal-message-success').hide();
							location.href = 'http://' + response.url;
						}, 1000);
					} else {
						$('.modal-message-error .message-text').text(response.msg);
						$('.modal-message-error').show();
						$('#message-modal').modal('show');
						setTimeout(function () {
							$('#message-modal').modal('hide');
							$('.modal-message-error').hide();
						}, 1000);
					}
				}
			});
		}
	});


	//选择审核人员模态框初始化
	$('#selectApprovalPerson').modal({
		backdrop : false,
		show : false,
	});

	//点击审批人员添加class
	$('.user-select-box li').click(function(){
		$('.product-engineer-tip').html('&nbsp;');
		//$('.approvalBox .help-block').html('');
		$('input[name=wid]').val($(this).attr('index'));
		$('.cid').val($(this).attr('index'));
		$('.approval').val($(this).text());
		$(this).addClass('active').siblings().removeClass('active');
	});

	//选择状态
	$('.state li').click(function(){
		var state = $(this).attr('state');
		var text = $(this).find('a').text();
		if(state==2 || state==3){
			if(!$('.productEngineerContainer').hasClass('sr-only')){
				$('.productEngineerContainer').addClass('sr-only');
			}
			if($('.form-group-upload-btn')){
                $('.form-group-upload-btn .test-report-file-info').find('*').remove();
                $('.form-group-upload-btn').hide();
            }
            $('.form-group-logistics').hide();
			//$('input[name=cid]').val('');
			$('input[name=logistics]').val('');
			$('.approval').val('');
			$('.approval').attr('name','notValidate');
		}else{
			if($('.productEngineerContainer').hasClass('sr-only')){
				$('.productEngineerContainer').removeClass('sr-only');
			}
			$('.form-group-logistics').show();
            if($('.form-group-upload-btn')){
                $('.form-group-upload-btn').show();
            }
			$('.logistics-name').text($('.logistics-list li:first-child').text());
			$('input[name=logistics]').val('SF');
			$('.approval').attr('name','approval');
		}
		$('.status').val(state);
		$('.stateText').text(text);
	});

	//选择物流公司
	$('.logistics-list li').click(function(){
		var logisticsCode = $(this).attr('logistics');
		var logisticsName = $(this).text();
		$('.logistics-name').text(logisticsName);
		$('.logistics-code').val(logisticsCode);
	});

	var ccc = '';
	$('.log-save-icon-audit').each(function(index){
		var _this = $(this);
		var _index = index;
		_this.mouseover(function(){
			ccc = _index;
		});
	});

	//审核日志
	$('.log-save-icon-audit').each(function(index){
		$(this).popover({
			title : '审核日志',
			trigger : 'hover',
			placement : 'bottom',
			html : true,
			content : $(this).next().html(),
		});
	});
	//物料准备日志
	$('.log-save-icon-metarial').each(function(index){
		$(this).popover({
			title : '物料准备日志',
			trigger : 'hover',
			placement : 'bottom',
			html : true,
			content : $(this).next().html(),
		});
	});
    //样品制作日志
	$('.log-save-icon-productEngineer').each(function(index){
		$(this).popover({
			title : '样品制作日志',
			trigger : 'hover',
			placement : 'bottom',
			html : true,
			content : $(this).next().html(),
		});
	});
	//样品测试日志
	$('.log-save-icon-testEngineer').each(function(index){
		$(this).popover({
			title : '样品测试日志',
			trigger : 'hover',
			placement : 'bottom',
			html : true,
			content : $(this).next().html(),
		});
	});
	//发货日志
	$('.log-save-icon-delivery').each(function(index){
		$(this).popover({
			title : '发货日志',
			trigger : 'hover',
			placement : 'bottom',
			html : true,
			content : $(this).next().html(),
		});
	});
	$('.log-save-icon-delivery').hover(function(){
		var wrap = parseInt($(this).next().css('left'));
		$(this).next().css({
			left : wrap-23+'px',
		});
		var arrow = parseInt($(this).next().find('.arrow').css('left'));
		$(this).next().find('.arrow').css({
			marginLeft : arrow-212+'px',
		});
	});
	//反馈日志
	$('.log-save-icon-feedback').each(function(index){
		$(this).popover({
			title : '反馈日志',
			trigger : 'hover',
			placement : 'bottom',
			html : true,
			content : $(this).next().html(),
		});
	});
	$('.log-save-icon-feedback').hover(function(){
		var wrap = parseInt($(this).next().css('left'));
		$(this).next().css({
			left : wrap-42+'px',
		});
		var arrow = parseInt($(this).next().find('.arrow').css('left'));
		$(this).next().find('.arrow').css({
			left : (arrow+42)+'px',
		});
	});

    //默认
    $('.material-default-value').val($('.material-user-list li:first').text());
    $('.material-hidden-value').val($('.material-user-list li:first').attr('index'));


	//审核提交
	$('.sampleAid').validate({
		focusInvalid : true,
		rules : {
			approval : {
				required : true,
			},
		},
		messages : {
			approval : {
				required : '请选物料准备人员',
			},
		},
		ignore : "",
		errorElement : 'b',
		errorPlacement : function(error, element) {
			if(element.attr('name')=='approval'){
				error.appendTo(element.parent().next());
			}
		},
		submitHandler : function(form){
            //如果状态为添加日志，并且日志内容为空则不允许提交
            if($('input[name=status]').val()==3 && $.trim($('textarea[name=comment]').val())==''){
                $().toastmessage('showToast', {
                    text : '请输入日志内容',
                    sticky : false,
                    position : 'top-right',
                    type : 'error',
                    inEffectDuration : 600,
                    stayTime : 3000,
                });
                return false;
            }
			$(form).ajaxSubmit({
				url : ThinkPHP['AJAX'] + '/Sample/audit',
				type : 'post',
				data : {
					s_assoc : $('input[name=assoc]').val(),
					s_status : $('input[name=status]').val(),
					e_date : $('input[name=e_date]').val(),
					s_comment : $('textarea[name=comment]').val(),
					total_order : $('input[name=total_order]').val(),
					wid : $('input[name=wid]').val(),
					uid : $('input[name=uid]').val(),
					operation : 'audit',
				},
				dataType : 'json',
				beforeSend : function(){
					if($('#loading').hasClass('sr-only')){
						$('#loading').removeClass('sr-only');
					}
				},
				success : function(response){
					if(response.flag > 0){
						$('#loading').addClass('sr-only');
						$('.modal-message-success .message-text').text(response.msg);
						$('.modal-message-success').show();
						$('#message-modal').modal('show');
						setTimeout(function(){
							//$('.modal-message-success').hide();
							//不保留页面数据缓存

							location.replace(location.href);
						},1000);
					}else{
						$('.modal-message-error .message-text').text(response.msg);
						$('.modal-message-error').show();
						$('#message-modal').modal('show');
						setTimeout(function(){
							$('#message-modal').modal('hide');
							$('.modal-message-error').hide();
						},1000);
					}
				}
			});
		},
	});

	//准备物料提交
	$('.sampleWid').validate({
		focusInvalid : true,
		rules : {
			approval : {
				required : true,
			},
		},
		messages : {
			approval : {
				required : '请选择产品工程师',
			},
		},
		ignore : "",
		errorElement : 'b',
		errorPlacement : function(error, element) {
			if(element.attr('name')=='approval'){
				error.appendTo(element.parent().next());
			}
		},
		submitHandler : function(form){
            //如果状态为添加日志，并且日志内容为空则不允许提交
            if($('input[name=status]').val()==3 && $.trim($('textarea[name=comment]').val())==''){
                $().toastmessage('showToast', {
                    text : '请输入日志内容',
                    sticky : false,
                    position : 'top-right',
                    type : 'error',
                    inEffectDuration : 600,
                    stayTime : 3000,
                });
                return false;
            }
			$(form).ajaxSubmit({
				url : ThinkPHP['AJAX'] + '/Sample/metaRial',
				type : 'post',
				data : {
					w_assoc : $('input[name=assoc]').val(),
					w_status : $('input[name=status]').val(),
					e_date : $('input[name=e_date]').val(),
					w_comment : $('textarea[name=comment]').val(),
					total_order : $('input[name=total_order]').val(),
					cid : $('input[name=cid]').val(),
					uid : $('input[name=uid]').val(),
					operation : 'material',
				},
				dataType : 'json',
				beforeSend : function(){
					if($('#loading').hasClass('sr-only')){
						$('#loading').removeClass('sr-only');
					}
				},
				success : function(response){
					if(response.flag > 0){
						$('#loading').addClass('sr-only');
						$('.modal-message-success .message-text').text(response.msg);
						$('.modal-message-success').show();
						$('#message-modal').modal('show');
						setTimeout(function(){
							//$('.modal-message-success').hide();
							//不保留页面数据缓存
							location.replace(location.href);
						},1000);
					}else{
						$('.modal-message-error .message-text').text(response.msg);
						$('.modal-message-error').show();
						$('#message-modal').modal('show');
						setTimeout(function(){
							$('#message-modal').modal('hide');
							$('.modal-message-error').hide();
						},1000);
					}
				}
			});
		},
	});

	//样品制作提交
	$('.sampleCid').validate({
		focusInvalid : true,
		rules : {
			approval : {
				required : true,
			},
		},
		messages : {
			approval : {
				required : '请选择测试工程师',
			},
		},
		ignore : "",
		errorElement : 'b',
		errorPlacement : function(error, element) {
			if(element.attr('name')=='approval'){
				error.appendTo(element.parent().next());
			}
		},
		submitHandler : function(form){
            //如果状态为添加日志，并且日志内容为空则不允许提交
            if($('input[name=status]').val()==3 && $.trim($('textarea[name=comment]').val())==''){
                $().toastmessage('showToast', {
                    text : '请输入日志内容',
                    sticky : false,
                    position : 'top-right',
                    type : 'error',
                    inEffectDuration : 600,
                    stayTime : 3000,
                });
                return false;
            }
			$(form).ajaxSubmit({
				url : ThinkPHP['AJAX'] + '/Sample/productEngineer',
				type : 'post',
				data : {
					c_assoc : $('input[name=assoc]').val(),
					c_status : $('input[name=status]').val(),
					e_date : $('input[name=e_date]').val(),
					c_comment : $('textarea[name=comment]').val(),
					total_order : $('input[name=total_order]').val(),
					cid : $('input[name=cid]').val(),
					uid : $('input[name=uid]').val(),
					operation : 'productEngineer',
				},
				dataType : 'json',
				beforeSend : function(){
					if($('#loading').hasClass('sr-only')){
						$('#loading').removeClass('sr-only');
					}
				},
				success : function(response){
					console.log(response);
					if(response.flag > 0){
						$('#loading').addClass('sr-only');
						$('.modal-message-success .message-text').text(response.msg);
						$('.modal-message-success').show();
						$('#message-modal').modal('show');
						setTimeout(function(){
							//$('.modal-message-success').hide();
							//不保留页面数据缓存
							location.replace(location.href);
						},1000);
					}else{
						$('.modal-message-error .message-text').text(response.msg);
						$('.modal-message-error').show();
						$('#message-modal').modal('show');
						setTimeout(function(){
							$('#message-modal').modal('hide');
							$('.modal-message-error').hide();
						},1000);
					}
				}
			});
		},
	});

    //样品测试报告上传附件
	$('#testReport').Huploadify({
		//提交到服务器
		method : 'post',
		auto : true,
		uploader : ThinkPHP['AJAX'] + '/Sample/uploadTestReport',
		//定义显示在默认按钮上的文本
		buttonText : '上传测试报告',
		//设置上传按钮的宽高
		fileObjName : 'Filedata',
		width : 140,
		height : 80,
		//设置值为false时，一次只能选中一个文件
		multi : false,
		//图片类型
		fileTypeDesc : '文件类型',
		//过滤文件格式
		fileTypeExts : '*.doc;*.docx;*.wps;*.xls;*.xlsx;*.ppt;*.pptx;*.pdf;*.zip;*.rar',
		//限制文件大小5m
		fileSizeLimit : 5242880,
		//上传成功之后执行回调函数(file:返回文件信息，data：返回服务器回调信息，response：返回真或假)
		onUploadSuccess : function(file,data,response){
			if(data){
				var obj = $.parseJSON(data);
				$('.test-report-file-info').html('<p class="test-report-file-name"><i class="icon-file-alt">&nbsp;</i><span class="report-file-name" filepath="'+obj['FileSavePath']+'">'+obj['NewFileName']+'</span></p>');
				$('.test-report-file-info').show();
			}else{
				$().toastmessage('showToast', {
					text : '上传失败',
					sticky : false,
					position : 'top-right',
					type : 'error',
					inEffectDuration : 600,
					stayTime : 3000,
				});
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
			$().toastmessage('showToast', {
				text : msgText,
				sticky : false,
				position : 'top-right',
				type : 'error',
				inEffectDuration : 600,
				stayTime : 3000,
			});
		},
		onFallback : function(){
			$().toastmessage('showToast', {
				text : '系统检测到你当前的浏览器尚未安装<a href="https://get2.adobe.com/cn/flashplayer/" target="_blank" style="color:yellow;text-decoration:underline;">Flash插件</a>导致上传头像功能无法使用',
				sticky : false,
				position : 'top-right',
				type : 'warning',
				inEffectDuration : 600,
				stayTime : 10000,
			});
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
			$().toastmessage('showToast', {
				text : msgText,
				sticky : false,
				position : 'top-right',
				type : 'error',
				inEffectDuration : 600,
				stayTime : 3000,
			});
		}
	});

	//样品测试提交
	$('.sampleYid').validate({
		submitHandler : function(form){
            //如果状态为添加日志，并且日志内容为空则不允许提交
            if($('input[name=status]').val()==3 && $.trim($('textarea[name=comment]').val())==''){
                $().toastmessage('showToast', {
                    text : '请输入日志内容',
                    sticky : false,
                    position : 'top-right',
                    type : 'error',
                    inEffectDuration : 600,
                    stayTime : 3000,
                });
                return false;
            }
			//如果未上传测试报告将不允许提交
			/*if($('.form-group-upload-btn').css('display')=='inline-block'){
				if ($('.test-report-file-info').children().length > 0) {
					if ($('.test-report-file-info .report-file-name').attr('filepath') == '') {
						$().toastmessage('showToast', {
							text: '请上传测试报告',
							sticky: false,
							position: 'top-right',
							type: 'error',
							inEffectDuration: 600,
							stayTime: 3000,
						});
						return false;
					}
				} else {
					$().toastmessage('showToast', {
						text: '请上传测试报告',
						sticky: false,
						position: 'top-right',
						type: 'error',
						inEffectDuration: 600,
						stayTime: 3000,
					});
					return false;
				}
			}*/
			$(form).ajaxSubmit({
				url : ThinkPHP['AJAX'] + '/Sample/testEngineer',
				type : 'post',
				data : {
					y_assoc : $('input[name=assoc]').val(),
					y_status : $('input[name=status]').val(),
					y_comment : $('textarea[name=comment]').val(),
					total_order : $('input[name=total_order]').val(),
					fid : $('input[name=cid]').val(),
					//attachment : $('.test-report-file-info .report-file-name').attr('filepath'),
					uid : $('input[name=uid]').val(),
					operation : 'testEngineer',
				},
				dataType : 'json',
				beforeSend : function(){
					if($('#loading').hasClass('sr-only')){
						$('#loading').removeClass('sr-only');
					}
				},
				success : function(response){
					if(response.flag > 0){
						$('#loading').addClass('sr-only');
						$('.modal-message-success .message-text').text(response.msg);
						$('.modal-message-success').show();
						$('#message-modal').modal('show');
						setTimeout(function(){
							//$('.modal-message-success').hide();
							location.replace(location.href);
						},1000);
					}else{
						$('.modal-message-error .message-text').text(response.msg);
						$('.modal-message-error').show();
						$('#message-modal').modal('show');
						setTimeout(function(){
							$('#message-modal').modal('hide');
							$('.modal-message-error').hide();
						},1000);
					}
				}
			});
		},
	});

	//发货提交
	$('.sampleFid').validate({
		focusInvalid : true,
		rules : {
			approval : {
				required : true,
			},
		},
		messages : {
			approval : {
				required : '请输入运单号',
			},
		},
		ignore : "",
		errorElement : 'b',
		errorPlacement : function(error, element) {
			if(element.attr('name')=='approval'){
				error.appendTo(element.next());
			}
		},
		submitHandler : function(form){
			//如果未上传测试报告将不允许提交
			if($('.form-group-upload-btn').css('display')=='inline-block'){
				if ($('.test-report-file-info').children().length > 0) {
					if ($('.test-report-file-info .report-file-name').attr('filepath') == '') {
						$().toastmessage('showToast', {
							text: '请上传测试报告',
							sticky: false,
							position: 'top-right',
							type: 'error',
							inEffectDuration: 600,
							stayTime: 3000,
						});
						return false;
					}
				} else {
					$().toastmessage('showToast', {
						text: '请上传测试报告',
						sticky: false,
						position: 'top-right',
						type: 'error',
						inEffectDuration: 600,
						stayTime: 3000,
					});
					return false;
				}
			}
            //如果状态为添加日志，并且日志内容为空则不允许提交
            if($('input[name=status]').val()==3 && $.trim($('textarea[name=comment]').val())==''){
                $().toastmessage('showToast', {
                    text : '请输入日志内容',
                    sticky : false,
                    position : 'top-right',
                    type : 'error',
                    inEffectDuration : 600,
                    stayTime : 3000,
                });
                return false;
            }
			$(form).ajaxSubmit({
				url : ThinkPHP['AJAX'] + '/Sample/delivery',
				type : 'post',
				data : {
					f_assoc : $('input[name=assoc]').val(),
					f_status : $('input[name=status]').val(),
					f_comment : $('textarea[name=comment]').val(),
					total_order : $('input[name=total_order]').val(),
					awb : $('.approval').val(),
					logistics : $('input[name=logistics]').val(),
					attachment : $('.test-report-file-info .report-file-name').attr('filepath'),
					fid : $('input[name=cid]').val(),
					uid : $('input[name=uid]').val(),
					operation : 'delivery',
				},
				dataType : 'json',
				beforeSend : function(){
					if($('#loading').hasClass('sr-only')){
						$('#loading').removeClass('sr-only');
					}
				},
				success : function(response){
					if(response.flag > 0){
						$('#loading').addClass('sr-only');
						$('.modal-message-success .message-text').text(response.msg);
						$('.modal-message-success').show();
						$('#message-modal').modal('show');
						setTimeout(function(){
							//$('.modal-message-success').hide();
							location.replace(location.href);
						},1000);
					}else{
						$('.modal-message-error .message-text').text(response.msg);
						$('.modal-message-error').show();
						$('#message-modal').modal('show');
						setTimeout(function(){
							$('#message-modal').modal('hide');
							$('.modal-message-error').hide();
						},1000);
					}
				}
			});
		},
	});

	//反馈提交
	$('.sampleKid').validate({
		submitHandler : function(form){
            //如果状态为添加日志，并且日志内容为空则不允许提交
            if($('input[name=status]').val()==3 && $.trim($('textarea[name=comment]').val())==''){
                $().toastmessage('showToast', {
                    text : '请输入日志内容',
                    sticky : false,
                    position : 'top-right',
                    type : 'error',
                    inEffectDuration : 600,
                    stayTime : 3000,
                });
                return false;
            }
			$(form).ajaxSubmit({
				url : ThinkPHP['AJAX'] + '/Sample/feedback',
				type : 'post',
				data : {
					k_assoc : $('input[name=assoc]').val(),
					k_status : $('input[name=status]').val(),
					k_comment : $('textarea[name=comment]').val(),
					total_order : $('input[name=total_order]').val(),
					kid : $('input[name=cid]').val(),
					uid : $('input[name=uid]').val(),
					operation : 'feedback',
				},
				dataType : 'json',
				beforeSend : function(){
					if($('#loading').hasClass('sr-only')){
						$('#loading').removeClass('sr-only');
					}
				},
				success : function(response){
					if(response.flag > 0){
						$('#loading').addClass('sr-only');
						$('.modal-message-success .message-text').text(response.msg);
						$('.modal-message-success').show();
						$('#message-modal').modal('show');
						setTimeout(function(){
							//$('.modal-message-success').hide();
							location.replace(location.href);
						},1000);
					}else{
						$('.modal-message-error .message-text').text(response.msg);
						$('.modal-message-error').show();
						$('#message-modal').modal('show');
						setTimeout(function(){
							$('#message-modal').modal('hide');
							$('.modal-message-error').hide();
						},1000);
					}
				}
			});
		},
	});

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















