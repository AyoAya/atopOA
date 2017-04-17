/**
 * 后台首页js
 */


$(function(){


	//alert(ThinkPHP['SESSIONNAME']);
	//alert(ThinkPHP['SESSIONID']);

	//$('.browser_list img').tooltip('show');
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

	/*var browserHeight = $(document).innerHeight();
	var browserWidth = $(document).innerWidth();
	var windowWidth = $(window).innerWidth();
	var contentWidth = windowWidth-240;
	var windowHeight = $(window).height();
	var scrollAmount = null;
	//检测当前电脑屏幕宽度，如果大于等于1920则滚动量调整为360，如果小于1920一律调整为200
	if(windowWidth>=1920){
		scrollAmount = 300;
	}else{
		scrollAmount = 150;
	}*/
	//设置侧边栏和路径导航栏的尺寸
	/*$('#header,#content').css({
		width : (windowWidth-200)+'px',
	});
	/!*$('#content').css({
		width : (windowWidth-200)+'px',
	});*!/
	// alert(contentWidth);
	$('#sidebar').css({
		height : windowHeight+'px',
		width : '200px'
	});
	$('#content').css({'height':windowHeight-50+'px','position':'absolute','left':'200px','top':'50px'});
	$('#header').css({'height':'50px','position':'absolute','left':'200px','top':'0'});*/
	/*$('#content-box').mCustomScrollbar({
		axis : 'y',			//设置y轴可见滚动条 可选参数：['y'|'x'|'yx']
		theme : 'dark',		//设置滚动条主题
		// 可选主题：['light','dark','minimal','light-2','dark-2','light-3','dark-3','light-thick','dark-thick','light-thin','dark-thin','inset','inset-dark','inset-2','inset-2-dark']
		// ['inset-3','inset-3-dark','rounded','rounded-dark','rounded-dots','rounded-dots-dark','3d','3d-dark','3d-thick','3d-thick-dark']
		// light适用深色背景，dark适用浅色背景
		setWidth : contentWidth+'px',		//设置滚动区域宽度
		setHeight : windowHeight-80+'px',		//设置滚动区域高度
		setTop : '0',		//设置初始置顶高度
		setLeft : '0',		//设置初始左侧距离
		mouseWheelPixels: scrollAmount,	//设置滚动量
		advanced:{ updateOnBrowserResize:true },
		scrollbarPosition : 'outside',	//设置滚动条的位置与内容  可选参数：['inside'|'outside']
		scrollInertia : '500',		//滚动动量的数量设置为动画持续时间,以毫秒为单位。更高的价值等于大滚动动量转换流畅更进步的动画。设置为0禁用。
		autoDraggerLength : true,	//设置滑块是否始终保持原始高度，默认为true
		autoHideScrollbar : true,	//设置滚动条闲置时是否隐藏，默认为false
		autoExpandScrollbar : false,	//设置鼠标放在滚动条上时是否加宽，默认为false
		alwaysShowScrollbar : 0,	//0 -禁用(默认)|1 -保持滚动块铁路可见|2 -让所有滚动条组件(滑块、铁路、按钮等)可见
		scrollButtons : { enable: false },	//是否开启滚动条上下按钮，默认为false
		keyboard : { enable: true },	//启用或禁用内容滚动通过键盘。
	    callbacks : {
	        onScroll:function(){
	            myCustomFn(this);
	        }
	    }
	});*/

	//当滚动条距离顶部超过100px显示回到顶部按钮
	/*function myCustomFn(el){
	    if(el.mcs.top<-100){
	    	$('#backtop').fadeIn('fast');
	    }else{
	    	$('#backtop').fadeOut('fast');
	    }
	}*/
	
	//点击回到顶部
	/*$('#backtop').click(function(){
		backToTop();
	});*/
	
	//回到顶部
	/*function backToTop(){
		$('#content-box').mCustomScrollbar('scrollTo','top');
	}*/

	// 初始化消息提示模态框
	$('#MessageModal').modal({
		backdrop : false,
		show : false
	});
	
	//点击当前登录用户名弹出下拉选项
	var rotate = true;
	$('#user_info').click(function(){
		if(rotate){
			$('#rotate-caret').rotate({
				duration : 300,
				animateTo : 180
			});
			$('#face_dropdown').css('display','block');
			rotate = false;
		}else{
			$('#rotate-caret').rotate({
				duration : 300,
				animateTo : 0
			});
			$('#face_dropdown').css('display','none');
			rotate = true;
		}
	});

	//退出登录
	$('#exit').click(function(){
		$('.modal').fadeIn('normal');
	});
	
	//取消退出
	$('.exit-cancel').click(function(){
		$('.modal').fadeOut('normal');
	});
	
	//退出登录确定ajax请求
	$('.exit-done').click(function(){
		$.ajax({
			url : ThinkPHP['AJAX'] + '/Login/logout',
			type : 'POST',
			dateType : 'json',
			success : function(response){
				if(response=='true'){
					location.href = ThinkPHP['AJAX'] + '/Login/index';
				}
			}
		});
	});

	$('#nav .secondary-menu').click(function(){
		var secondary_menu = $(this).next();
		if( $(secondary_menu).hasClass('sr-only') ){
			$(secondary_menu).removeClass('sr-only');
		}else{
			$(secondary_menu).addClass('sr-only');
		}
	});

	/*//研发管理二级菜单
	$('#Reserach').mouseover(function(){
		$('#ResearchChildMenu').show();
	});
	$('#Reserach').mouseout(function(){
		$('#ResearchChildMenu').hide();
	});
	//添加移入移出样式
	$('#ResearchChildMenu').mouseover(function(){
		$('.reserchBTN').addClass('active');
		$('#ResearchChildMenu').show();
	});
	$('#ResearchChildMenu').mouseout(function(){
		$('.reserchBTN').removeClass('active');
		$('#ResearchChildMenu').hide();
	});*/
	
	//切换皮肤
	$('#theme').mouseover(function(){
		$(this).find('.change-theme').show();
	});
	$('#theme').mouseout(function(){
		$(this).find('.change-theme').hide();
	});
	
	//切换皮肤
	$('.change-theme .theme-box').click(function(){
		var theme = $(this).attr('theme');
		var userid = $(this).parent().find('input[type=hidden]').val();
		$.ajax({
			url : ThinkPHP['AJAX'] + '/System/changetheme',
			type : 'post',
			data : {
				id : userid,
				theme : theme,
			},
			dataType : 'json',
			success : function(response){
				if(response=='true'){
					location.reload();
				}else if(response=='false'){
					
				}else{
					alert(response);
				}
			}
		});
	});
	
	
	//ajax轮询查收通知信息
	var timer = setInterval(function(){
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
	},1000*30);
	
	
	
	
});







