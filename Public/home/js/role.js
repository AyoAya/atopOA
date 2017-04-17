/**
 * role角色页面js
 */

$(function(){
	
	/**
	 * 获取角色分组
	 */
	$('.changeauth').click(function(){
		var index = $(this).attr('index');
		$.ajax({
			url : ThinkPHP['AJAX'] + '/Manage/getRole',
			type : 'POST',
			data : {
				id : index,
			},
			dataType : 'json',
			success : function(response){
				var str = '';
				for(value in response){
					if(value=='rules'){
						var rules = response[value];
					}else{
						var authrule = response[value];
					}
				}
				var obj = eval(authrule);
				var arr = rules.split(',');
				for(i=0;i<obj.length;i++){
					str += "<span><input type='checkbox' name='rules[]' value='"+ obj[i]['id'] +"'/>"+ obj[i]['title'] +"</span>";
				}
				$('.rule').html(str);
				$('.ruleid').val(index);
				$('.rule span').each(function(index){
					var input = $(this).find('input');
					for(i=0;i<arr.length;i++){
						if(input.val()==arr[i]){
							input.attr('checked','checked');
						}
					}
				});
				$('.modal').fadeIn('normal');
			}
		});
	});
	
	/**
	 * 确定修改角色权限
	 */
	$('.rule-done').click(function(){
		var str = '';
		var checkbox = $('.rule input[type=checkbox]:checked');
		var ruleid = $(this).parents('.modal-panel').find('.ruleid').val();
		for(i=0;i<checkbox.length;i++){
			str += $(checkbox[i]).val() + ',';
		}
		var string = str.substring(0,str.length-1);
		$.ajax({
			url : ThinkPHP['AJAX'] + '/Manage/editrule',
			type : 'POST',
			data : {
				id : ruleid,
				rules : string,
			},
			dataType : 'json',
			success : function(response){
				if(response=='false'){
					$('#message').text('保存失败').fadeIn('normal');
					setTimeout(function(){
						$('#message').text('').fadeOut('normal');
					},800);
				}else{
					$('#message').text('保存成功').fadeIn('normal');
					setTimeout(function(){
						$('#message').text('').fadeOut('normal');
						$('.modal').fadeOut('normal');
					},800);
				}
			}
		});
	});
	
	/**
	 * flag状态开关(控制角色的启用或禁用)
	 */
	$('.switch button').click(function(){
		var _this = $(this);
		var flag = $(this).attr('flag');
		var index = $(this).attr('index');
		$.ajax({
			url : ThinkPHP['AJAX'] + '/Manage/flag',
			type : 'POST',
			data : {
				flag : flag,
				id : index,
			},
			dataType : 'json',
			success : function(response){
				if(response=='true'){
					if(_this.attr('flag')=='off'){
						_this.text('已禁用').attr('flag','on');
					}else{
						_this.text('已开启').attr('flag','off');
					}
				}
			}
		});
	});
	
});