/**
 * 后台首页js
 */


$(function(){

	//$('.browser_list img').tooltip('show');
	$.post(ThinkPHP['AJAX'] + '/Todolist/getTodolistCount', function(response){
			if( response != 0 ){
				if( $('#header .user-operation-item:first-child a span.badge').length ){
					$('#header .user-operation-item a span.badge').text(response);
				}else{
					$('#header .user-operation-item:first-child a').append('<span class="badge" style="display: inline-block;background-color: #d9534f;">'+ response +'</span>');
				}
			}else{
				if( $('#header .user-operation-item:first-child a span.badge').length ){
					$('#header .user-operation-item a span.badge').remove();
				}
			}
		}
	);



	// 当滚动条大于100显示回到顶部按钮
	$('#content').scroll(function(){
		if( $(this).scrollTop() > 100 ){
			$('#scrollBackTop').removeClass('sr-only');
		}else{
			$('#scrollBackTop').addClass('sr-only');
		}
	});


	//点击回到顶部
	$('#scrollBackTop').click(function(){
		$('#content').scrollTop(0);
	});


	// 点击显示二级菜单
	$('#nav .secondary-menu').click(function(){
		var secondary_menu = $(this).next();
		if( $(secondary_menu).hasClass('sr-only') ){
			$(secondary_menu).removeClass('sr-only');
		}else{
			$(secondary_menu).addClass('sr-only');
		}
	});


	
	
	//ajax轮询查收通知信息
	/*var timer = setInterval(function(){
		$.ajax({
			url : ThinkPHP['AJAX'] + '/Notice/getNotice',
			type : 'POST',
			dataType : 'json',
			success : function(response){
				if(response.flag==1){
					if(response.number>0){
						$('.badge').text(response.number).show();
					}else{
						$('.badge').text(response.number).hide();
					}
				}
			}
		});
	},1000*30);*/
	
	
	
	
});







