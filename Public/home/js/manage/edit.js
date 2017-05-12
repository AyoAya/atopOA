/**
 * 修改管理员页面js
 */

$(function(){

	//初始化职位部门默认选择第一项
	//$('#position-value').text($('#position-list li:first-child a').text());
	//$('#position-id').val($('#position-list li:first-child').attr('index'));
	
	//如果部门下面没有选项则移除父容器
	$('#report .name-container').each(function(index){
		if($(this).children().length < 1){
			$(this).parent().remove();
		}
		/*if($(this).html()==''){
			$(this).parent().remove();
		}*/
	});

	$('.checkbox-edit label').each(function(){
		if($(this).children().length==0){
			$(this).remove();
		}
	});
	
	//权限选择
	$('.auth-box .auth-content ul span').click(function(){
		if($(this).hasClass('check-active')){
			$(this).removeClass('check-active');
		}else{
			$(this).addClass('check-active');
		}
	});
	
	//下拉列表（部门/职位）
	$('#department-list li').click(function(){
		var departmentValue = $(this).text();
		$('#department-value').text(departmentValue);
		$('#department-id').val($(this).attr('index'));
		$.post(ThinkPHP['AJAX']+'/Manage/changeDepartment',{id:$(this).attr('index')},function(data){
			if(data.flag){
				var str = '';
				for(var i=0;i<data.data.length;i++){
					str += '<li index="'+data.data[i].id+'"><a href="javascript:void(0);">'+data.data[i].name+'</a></li>';
				}
				$('#position-list').html(str);
				setTimeout(function(){
					$('#position-value').text($('#position-list li:first-child a').text());
					$('#position-id').val($('#position-list li:first-child').attr('index'));
				},50);
			}
		},'json');
	});
	$('#position-list').on('click','li',function(){
		var positionValue = $(this).text();
		$('#position-value').text(positionValue);
		$('#position-id').val($(this).attr('index'));
	});
	
	//全选
	var checkedStatus = true;
	$('#checked-all').click(function(){
		$('#addForm input[type=checkbox]').prop('checked',true);
	});
	//反选
	$('#checked-rev').click(function(){
		$('#addForm input[type=checkbox]').each(function(index){
			if($(this).prop('checked')){
				$(this).prop('checked',false); 
			}else{ 
				$(this).prop("checked",true); 
			} 
		});
	});
	
	//初始化模态框参数
	$('#adduser-modal').modal({
		backdrop : false,
		show : false,
	});

	//级别
	$('#level-list li').click(function(){
		$('#level-value').text($(this).text());
		$('#level-id').val($(this).attr('index'));
	});
	
	//验证表单并提交修改数据
	$('#addForm').validate({
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
				required : '用户姓名不能为空',
			},
			email : {
				required : '邮箱不能为空',
				email : '请输入合法的电子邮箱',
			},
		},
		ignore : "",
		errorElement : 'b',
		errorPlacement : function(error, element) {
			error.appendTo(element.parent().next()); 
		},
		submitHandler : function(form){
			$(form).ajaxSubmit({
				url : ThinkPHP['AJAX'] + '/Manage/saveEdit',
				type : 'POST',
				data : {
					id : $('#form input[name=id]').val(),
					account : $('#form input[name=account]').val(),
					password : $('#form input[name=password]').val(),
					nickname : $('#form input[name=nickname]').val(),
					email : $('#form input[name=email]').val(),
					department : $('#department-id').val(),
					position : $('#position-id').val(),
					report : $('#report-id').val(),
					permissions : $('#form input[name=permissions]').val(),
					level : $('#form input[name=level]').val(),
					state : $('#form input[name=state]').val(),
				},
				dataType : 'json',
				success : function(response){
					if(response.flag > 0){
						$('.adduser-message-success .message-text').text(response.msg);
						$('.adduser-message-success').show();
						$('#adduser-modal').modal('show');
						setTimeout(function(){
							$('.adduser-message-success').hide();
							location.href = ThinkPHP['ROOT'] + '/Manage';
						},1500);
					}else{
						$('.adduser-message-error .message-text').text(response.msg);
						$('.adduser-message-error').show();
						$('#adduser-modal').modal('show');
						setTimeout(function(){
							$('.adduser-message-error').hide();
							$('#adduser-modal').modal('hide');
						},1500);
					}
				}
			});
		},
	});
	
	//汇报关系
	$('#report .name-box').click(function(){
		$(this).addClass('active').siblings().removeClass('active');
		$('#report-id').val($(this).attr('index'));
	});

	//重置密码
	$('#resetPassword').click(function(){
		$.ajax({
			url : ThinkPHP['AJAX']+'/Manage/resetPassword',
			type : 'POST',
			data : {
				id:$(this).attr('nowUser'),
				password:_getRandomString(10)
			},
			dataType : 'json',
			beforeSend : function(){
				$('#loading').removeClass('sr-only');
			},
			success : function(data){
				if(data.flag){
					$('#loading').addClass('sr-only');
					$('#newPassword').text(data.newpassword);
					$('#password').val(data.sha1);
					$('#modal-message').modal('show');
				}
			}
		});
		/*$.post(ThinkPHP['AJAX']+'/Manage/resetPassword',{id:$(this).attr('nowUser'),password:_getRandomString(10)},function(data){
			if(data.flag){
				$('#newPassword').text(data.newpassword);
				$('#password').val(data.sha1);
				$('#modal-message').modal('show');
			}
		},'json');*/
	});

	//初始化重置密码模态框
	$('#modal-message').modal({
		backdrop : false,
		show : false,
	});

	//随机字符串
	function _getRandomString(len) {
		len = len || 32;
		var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678.,+-*~@_'; // 默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1
		var maxPos = $chars.length;
		var pwd = '';
		for (i = 0; i < len; i++) {
			pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
		}
		return pwd;
	}

	// 修改状态
	$('#state-list li').click(function(){
		var _state = $(this).attr('state');
		var _stateText = $(this).text();
		$('#state-value').text(_stateText);
		$('#state-id').val(_state);
	});

});