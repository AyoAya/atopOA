<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>后台登录</title>
	<link rel="stylesheet" href="/Public/Home/css/basic.css"/>
	<link rel="stylesheet" href="/Public/Home/css/login.css"/>
	<link rel="stylesheet" href="/Public/Home/css/font-awesome.min.css"/>
	<script src="/Public/Home/js/jquery.min.js"></script>
	<link rel="stylesheet" href="/Public/Home/css/promotion_index.css">
</head>
<body>
<div id="bodyContainer">
	<div id="logo">
		<span>华拓光通信OA系统</span>
	</div>

	<div id="wrap">
		<div class="opacity">
			<div id="login">
				<form action="/index.php/Home/Login/login" method="post" id="form">
					<p><i class="account"></i><input type="text" name="account" placeholder="账号"/></p>
					<p><i class="password"></i><input type="password" name="password" placeholder="密码"/></p>
					<?php if((C("LOGIN_VERIFY")) == "1"): ?><p><input type="text" name="verify" placeholder="验证码" class="verify"/></p><?php endif; ?>
					<p class="remember"><i class="icon-check-empty"></i><span>一个月内免登录</span></p>
					<p class="submit">
						<input type="submit" value="登录"/>
						<?php if((C("LOGIN_VERIFY")) == "1"): ?><img id="verify" src="/index.php/Home/Login/code" style="cursor:pointer;" onclick="this.src='/index.php/Home/Login/code'" alt=""/><?php endif; ?>
					</p>
					<span class="copy">&copy; 2014-2016 ATOP.</span>
					<input type="hidden" id="rememberValue" name="remember" value="0">
				</form>
			</div>
		</div>
	</div>
</div>
<div style="display: none" id=browser_ie>
	<div class=brower_info>
		<div class="browser_box">
			<div class=notice_info><h3>你的浏览器版本过低，可能导致网站不能正常访问<br>为了你能正常使用网站功能，请使用以下浏览器。</h3></div>
			<div class=browser_list>
				<span><a href="javascript:void(0);" target=_blank><img src="/Public/Home/img/Chrome.png" title="谷歌浏览器" data-toggle="tooltip" data-placement="bottom"></a></span>
				<span><a href="javascript:void(0);" target=_blank><img src="/Public/Home/img/Firefox.png" title="火狐浏览器" data-toggle="tooltip" data-placement="bottom"></a></span>
				<span><a href="javascript:void(0);" target=_blank><img src="/Public/Home/img/Safari.png" title="safari浏览器" data-toggle="tooltip" data-placement="bottom"></a></span>
				<span><a href="javascript:void(0);" target=_blank><img src="/Public/Home/img/Opera.jpg" title="Opera浏览器" data-toggle="tooltip" data-placement="bottom"></a></span>
			</div>
		</div>
	</div>
</div>

<script>

	//如果检测到用户浏览器为IE则禁用
	var str = navigator.userAgent;

	if(str.indexOf('MSIE')>0){
		alert(str);
		checkIE();
	}

	/*if (document.all && document.addEventListener && !window.atob) {
	 checkIE();
	 }

	 if (document.all && document.querySelector && !document.addEventListener) {
	 checkIE();
	 }

	 if (document.all && window.XMLHttpRequest && !document.querySelector) {
	 checkIE();
	 }

	 if (document.all && document.compatMode && !window.XMLHttpRequest) {
	 checkIE();
	 }*/

	function checkIE(){
		var body = document.getElementsByName('body');
		var bodyContainer = document.getElementById('bodyContainer');
		var browser_ie = document.getElementById('browser_ie');
		bodyContainer.parentNode.removeChild(bodyContainer);
		browser_ie.style.display = 'block';
	}
</script>

<script>
	$(function(){
		var formHeight = $('#form').height();
		$('#form').css({
			marginTop : (400-formHeight)/2+'px',
		});
		$('.remember').click(function(){
			if($(this).find('i').hasClass('icon-check-empty')){
				$(this).find('i').attr('class','icon-check');
				$('#rememberValue').val(1);
			}else{
				$(this).find('i').attr('class','icon-check-empty');
				$('#rememberValue').val(0);
			}
		});
	});
</script>
</body>
</html>