/**
 * person管理员页面js
 */

$(function(){

	//初始化职位部门默认选择第一项
	$('#position-value').text($('#position-list li:first-child a').text());
	$('#position-id').val($('#position-list li:first-child').attr('index'));

	//激活弹出层(添加管理员)
	$('a.addmanage').click(function(){
		$('div.modal').fadeIn('normal');
	});
	
	//关闭弹出层
	$('.modal span.close').click(function(){
		$(this).parents('.modal-panel').find('.left-box').find('i').attr('class','').text('');
		$(this).parents('.modal-panel').find('.left-box').find('b').attr('class','');
		$(this).parents('.modal-panel').find('.left-box').find('input').val('');
		$('.report').fadeOut('fast');
		$('.auth-box').fadeOut('fast');
	});
	$('.modal2 span.close').click(function(){
		$('.auth-box').fadeOut('fast');
		$('.report2').fadeOut('fast');
	});
	
	//选择框下拉菜单
	var rotate = true;
	var _arrow = null;
	$('.select-box').click(function(){
		var rolelist = $(this).find('.role-list');
		var arrow = $(this).find('.arrow');
		var flag = $(this).find('.role-list').css('display');
		if(flag=='none'){
			//如果存在上一次记录，则回到 0°
			if(_arrow){
				_arrow.rotate({
					duration : 300,
					animateTo : 0
				});
			}
			//点击之后下拉图标旋转 180°
			arrow.rotate({
				duration : 300,
				animateTo : 180
			});
			//将所有已展开的下拉列表关闭
			$('.role-list').each(function(index){
				$(this).slideUp('fast');
			});
			$(this).css('border','solid 1px #555');
			rolelist.css({'border':'solid 1px #555','border-top':'none'});
			//展开当前点击的下拉框
			rolelist.slideDown('fast');
			//记录上一次点击
			_arrow = arrow;
		}else{
			//如果下拉框已经被展开，收起下拉框并把图标旋转回 0°
			arrow.rotate({
				duration : 300,
				animateTo : 0
			});
			$(this).css('border','solid 1px #ccc');
			rolelist.css({'border':'solid 1px #ccc','border-top':'none'});
			rolelist.slideUp('fast');
		}
	});
	
	//点击选中之后将文本放入隐藏域
	$('.role-list li').click(function(){
		$(this).parent().parent().find('h5').text($(this).text());
		$(this).parent().parent().find('input[type=hidden]').val($(this).attr('index'));
	});

	//汇报关系(展开选择框):新增
	$('.add-form .right-box .select-person-box').click(function(){
		if($('.auth-box').css('display')=='block'){
			$('.auth-box').fadeOut('fast');
		}
		$('.report').fadeToggle('fast');
	});
	$('.report li').click(function(){
		var text = $(this).text();
		var personid = $(this).attr('index');
		$('.select-person-box h5').text(text);
		$('#report-id').val(personid);
		$(this).parents('.report').fadeOut('fast');
	});
	
	//汇报关系(展开选择框):修改
	$('.edit-role .select-person-box').click(function(){
		if($('.edit-role .auth-box').css('display')=='block'){
			$('.edit-role .auth-box').fadeOut('fast');
		}
		$('.report2').fadeToggle('fast');
	});
	$('.report2 li').click(function(){
		var text = $(this).text();
		var personid = $(this).attr('index');
		$(this).parents('.report2').prev().find('.last-box').find('h5').text(text);
		$('#report-id-edit').val(personid);
		$(this).parents('.report2').fadeOut('fast');
	});

	//权限控制
	$('.role-box .select-box').click(function(){
		if($('.report').css('display')=='block'){
			$('.report').fadeOut('fast');
		}
		$(this).next().fadeToggle('fast');
	});
	
	//保存已选择的权限
	$('.role-box .auth-box .done-rule').click(function(){
		var rulestr = '';
		var rulenum = 0;
		var allSpan = $(this).parents('.auth-box').find('.auth-content').find('span');
		for(var i=0;i<allSpan.length;i++){
			if($(allSpan[i]).hasClass('check-active')){
				rulenum++;
				rulestr += $(allSpan[i]).attr('index') + ',';
			}
		}
		var rules = rulestr.substring(0,rulestr.length-1);
		if(rulenum > 0){
			$(this).parents('.role-box').find('.select-box').find('h5').text('已选择'+ rulenum +'项');
		}else{
			$(this).parents('.role-box').find('.select-box').find('h5').text('未选择');
		}
		$(this).parents('.role-box').find('input[name=rules]').val(rules);
		$(this).parents('.auth-box').fadeOut('fast');
	});
	
	//默认选中第一行数据
	$('.right-box .default-active').each(function(index){
		var roleList = $(this).find('.role-list');
		$(this).find('h5').text(roleList.find('li').eq(0).text());
		$(this).find('input[type=hidden]').val(roleList.find('li').eq(0).attr('index'));
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

	layui.use(['form'],function(){

        var layer = layui.layer,
            form = layui.form();

        //全选
        var checkedStatus = true;
        $('#checked-all').click(function(){
            $('#addForm .checkbox-lay-box input[type=checkbox]').each(function(index){
                $(this).prop('checked',true);
                form.render('checkbox');
			})
        });

        //反选
        $('#checked-rev').click(function(){
            $('#addForm .checkbox-lay-box input[type=checkbox]').each(function(index){
                if($(this).prop('checked')){
                    $(this).prop('checked',false);form.render('checkbox');
                }else{
                    $(this).prop("checked",true);form.render('checkbox');
                }
            });

        });


	})


	//自定义验证规则
	$.validator.addMethod("Letter", function(value, element) {
		var tel = /^[a-zA-Z]+$/;
		return this.optional(element) || (tel.test(value));
	}, "账号只能包含数字字母下划线");
	$.validator.addMethod("passwordRule", function(value, element) {
		var tel = /^[a-zA-Z0-9\,\.\+\-\*\~\@\_]+$/;
		return this.optional(element) || (tel.test(value));
	}, "账号只能包含数字字母下划线");
	
	$('#adduser-modal').modal({
		backdrop : false,
		show : false,
	});
	
	//验证表单并提交数据
	$('#addForm').validate({
		focusInvalid : true,
		rules : {
			account : {
				required : true,
				Letter : true,
				minlength : 3,
				maxlength : 13,
				remote : {
					url : ThinkPHP['AJAX'] + '/Manage/checkUniqueAccount',
					type : 'POST',
					dataType : 'json',
					data : {
						account : function(){
							return $('#account').val();
						}
					}
				},
			},
			password : {
				required : true,
				passwordRule : true,
				minlength : 6,
				maxlength : 20,
			},
			nickname : {
				required : true,
			},
			email : {
				required : true,
				email : true,
			},
		},
		messages : {
			account : {
				required : '账号不能为空',
				Letter : '账号只能是字母[不区分大小写]',
				minlength : '账号不能低于3位',
				maxlength : '账号不能大于13位',
				remote : '账号已被占用',
			},
			password : {
				required : '密码不能为空',
				passwordRule : '密码格式错误，密码只能包含数字/字母/下划线以及,.+-*~@',
				minlength : '密码长度不能小于6位',
				maxlength : '密码长度不能大于20位',
			},
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
				url : ThinkPHP['AJAX'] + '/Manage/addManageData',
				type : 'POST',
				data : {
					account : $('#form input[name=account]').val(),
					password : $('#form input[name=password]').val(),
					nickname : $('#form input[name=nickname]').val(),
					email : $('#form input[name=email]').val(),
                    pos : $('#form input[name=pos]').val(),
					department : $('#department-id').val(),
					//position : $('#position-id').val(),
					report : $('#report-id').val(),
                    permissions : $('#form input[name=permissions]').val(),
                    sex : $('#form input[name=radio]').val(),
					level : $('#form input[name=level]').val(),
				},
				dataType : 'json',
				beforeSubmit : function(){
					if($('#loading').hasClass('sr-only')){
						$('#loading').removeClass('sr-only');
					}
				},
				success : function(response){
					if(response.flag > 0){
						$('#loading').addClass('sr-only');
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

	$('#addUserSuccess-modal').modal({
		backdrop : false,
		show : false,
	});

	//邮件通知
	$('#EmailNotice').click(function(){
		$.post(ThinkPHP['AJAX'] + '/Manage/EmailNotice',{sendEmail:$('#sendMail').val()},function(data){
			console.log(data);
		},'json');
	});

	//点击生成随机密码
	$('#random').click(function(){
		$('#password').val(_getRandomString(10));
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

	//汇报关系
	$('#report .name-box').click(function(){
		$(this).addClass('active').siblings().removeClass('active');
		$('#report-id').val($(this).attr('index'));
	});

	//级别
	$('#level-list li').click(function(){
		$('#level-value').text($(this).text());
		$('#level-id').val($(this).attr('index'));
	});

	//删除用户(弹出确认对话框)
	$('.delete-user').click(function(){
		var id = $(this).attr('personid');
		$('#hiddenID').val(id);
		$('#deluser-modal').modal('show');
	});
	
	//删除用户（数据交互）
	$('#done-delete').click(function(){
		var hiddenID = $('#hiddenID').val();
		$.post(ThinkPHP['AJAX'] + '/Manage/del',{id:hiddenID},function(data){
			if(data=='true'){
				location.reload();
			}else{
				alert('错误');
			}
		});
	});

	//点击展开修改角色弹窗
	$('.edit-role .role-box .select-box').click(function(){
		if($('.report2').css('display')=='block'){
			$('.report2').fadeOut('fast');
		}
		$(this).parents('.edit-role').find('.auth-box').fadeToggle('fast');
	});

	//选项选择确定后保存数据并关闭弹出层
	$('.edit-role .auth-box .done-rule').click(function(){
		var rulesstr = '';
		var rulesnumber = 0;
		$('.edit-role .auth-box .auth-content ul span').each(function(index){
			if($(this).hasClass('check-active')){
				rulesnumber++;
				rulesstr += $(this).attr('index') + ',';
			}
		});
		var rulesstring = rulesstr.substring(0,rulesstr.length-1);
		if(rulesnumber > 0){
			$('.edit-role .three-box h5').text('已选择'+ rulesnumber +'项');
		}else{
			$('.edit-role .three-box h5').text('未选择');
		}
		$('.edit-role .three-box').find('input[name=rules]').val(rulesstring);
		$(this).parents('.auth-box').fadeOut('fast');
	});
	
	//点击修改角色获取用户信息
	$('.table-edit').click(function(){
		var personid = $(this).attr('personid');
		$('.save-role').attr('userid',personid);
		$.ajax({
			url : ThinkPHP['AJAX'] + '/Manage/getuserinfo',
			type : 'POST',
			data : {
				id : personid,
			},
			dataType : 'json',
			success : function(response){
				//console.log(response);
				$('.u-account').text(response.account);
				$('.u-nickname').text(response.nickname);
				//设置默认该用户部门
				$('.edit-role .first-box h5').text(response.department_name);
				$('.edit-role .first-box input[type=hidden]').val(response.department_id);
				//设置默认该用户职位
				$('.edit-role .two-box h5').text(response.position_name);
				$('.edit-role .two-box input[type=hidden]').val(response.position_id);
				//设置默认用户已选择的权限
				var rules = response.rules;
				var number = 0;
				var rulesArr = rules.split(',');
				$('.edit-role .auth-content ul span').each(function(index){
					for(var i=0;i<rulesArr.length;i++){
						if($(this).attr('index')==rulesArr[i]){
							$(this).attr('class','check-active');
						}
					}
					if($(this).hasClass('check-active')){
						number++;
					}
				});
				if(number > 0){
					$('.edit-role .role-box h5').text('已选择'+ number +'项');
				}else{
					$('.edit-role .role-box h5').text('未选择');
				}
				//设置默认该用户汇报关系
				if(response.report_name!=null){
					$('.edit-role .last-box h5').text(response.report_name);
				}else{
					$('.edit-role .last-box h5').text('无');
				}
				$('.edit-role .last-box input[type=hidden]').val(response.report);
				$('.edit-role input[name=authgroupid]').val(response.groupid);
				$('.modal2').fadeIn('normal');
			}
		});
	});
	
	//保存角色
	$('.save-role').click(function(){
		var userid = $(this).attr('userid');	//点击的用户id
		var departmentid = $('.edit-role .first-box input[type=hidden]').val();	//获取到部门id
		var positionid = $('.edit-role .two-box input[type=hidden]').val();		//获取到职位id
		var groupid = $('.edit-role input[name=authgroupid]').val();			//获取到角色id
		var rules = $('.edit-role .role-box input[type=hidden]').val();			//获取到角色权限规则
		var reportid = $('.edit-role .last-box input[type=hidden]').val();		//获取到汇报人id
		$.ajax({
			url : ThinkPHP['AJAX'] + '/Manage/changerole',
			type : 'POST',
			data : {
				id : userid,
				department : departmentid,
				position : positionid,
				groupid : groupid,
				rules : rules,
				report : reportid,
			},
			dataType : 'json',
			success : function(response){
				if(response=='true'){
					$('#message').text('修改成功').fadeIn('normal');
					setTimeout(function(){
						$('#message').text('').fadeOut('normal');
						$('.modal2').fadeOut('normal');
						location.reload();
					},800);
				}else{
					$('#message').text('修改失败').fadeIn('normal');
					setTimeout(function(){
						$('#message').text('').fadeOut('normal');
					},800);
				}
			}
		});
	});
	
	//删除用户(激活弹出层)
	$('.table-del').click(function(){
		var personid = $(this).attr('personid');
		$('.del-id').val(personid);
		$('.modal3').fadeIn('normal');
	});
	
	
	//取消删除
	$('.del-box p button.close').click(function(){
		$('.modal3').fadeOut('normal');
	});
	
	//确定删除用户
	$('.confirm-del').click(function(){
		var delid = $(this).parents('.del-box').find('.del-id').val();
		$.ajax({
			url : ThinkPHP['AJAX'] + '/Manage/deluser',
			type : 'POST',
			data : {
				id : delid,
			},
			dataType : 'json',
			success : function(response){
				if(response=='true'){
					$('#message').text('删除成功').fadeIn('normal');
					setTimeout(function(){
						$('#message').text('').fadeOut('normal');
						$('.modal3').fadeOut('normal');
						location.reload();
					},800);
				}else{
					$('#message').text('删除失败').fadeIn('normal');
					setTimeout(function(){
						$('#message').text('').fadeOut('normal');
					},800);
				}
			}
		});
	});
	
	$('.checkbox-edit label').each(function(){
		if($(this).children().length==0){
			$(this).remove();
		}
	});
	
	
});




