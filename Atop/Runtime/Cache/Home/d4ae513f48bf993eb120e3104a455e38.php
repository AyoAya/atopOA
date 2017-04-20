<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>

	<meta charset="UTF-8">
	<!--<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">-->
	<link href="/Public/Home/img/favicon.ico" rel="shortcut icon">

	
    <title>组织架构图</title>
<!-- 网站标题 -->
	<link rel="stylesheet" href="/Public/layui/css/layui.css">
	<link rel="stylesheet" href="/Public/Home/css/bootstrap.min.css">
	<link rel="stylesheet" href="/Public/Home/css/font-awesome.min.css">
	<link rel="stylesheet" href="/Public/Home/css/jquery.mCustomScrollbar.css">
	<link rel="stylesheet" href="/Public/Home/css/iconfont.css">
	<link rel="stylesheet" href="/Public/Home/css/basic.css">
	<link rel="stylesheet" href="/Public/Home/css/<?php echo ($face["theme"]); ?>/index.css">
	<link rel="stylesheet" href="/Public/Home/css/promotion_index.css">

	
    <link rel="stylesheet" href="/Public/Home/css/jquery.orgchart.css">
    <link rel="stylesheet" href="/Public/Home/css/orgchart.style.css">
    <link rel="stylesheet" href="/Public/Home/css/organization.css">
<!-- 引入css文件 -->
	<script src="/Public/Home/js/jquery.min.js"></script>
	<script src="/Public/Home/js/bootstrap.min.js"></script>
	<script src="/Public/layui/layui.js"></script>
	<script src="/Public/Home/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/Public/Home/js/index.js"></script>

	
    <script type="text/javascript" src="/Public/Home/js/jquery.orgchart.js"></script>
<!-- 引入js文件 -->
	<script src="/Public/Home/js/jquery.rotate.min.js"></script>
	
</head>
<body>
	<script type="text/javascript">
		var ThinkPHP = {
				'AJAX' : '/index.php/Home',
				'ROOT' : '',
				'IMG' : '/Public/Home/img',
				'UPLOAD' : '/Uploads',
				'HTTP_HOST' : '<?php echo ($_SERVER['HTTP_HOST']); ?>',
				'UPLOADIFY' : '/Public/home/uploadify',
				'UPLOADIFY_CONFIG_FILESIZELIMIT' : '<?php echo (C("UPLOAD_FILESIZELIMIT")); ?>',
				'UPLOADIFY_CONFIG_UPLOADLIMIT' : '<?php echo (C("UPLOAD_UPLOADLIMIT")); ?>',
				'UPLOADIFY_CONFIG_FILETYPEEXTS' : '<?php echo (C("UPLOAD_FILETYPEEXTS")); ?>',
		}
	</script>
	<div id="bodyContainer">
		
			<div id="sidebar">
				<div class="sidebar-inset">

					<!-- Logo区 -->
					<div class="logo"></div>

					<!-- 导航区 -->
					<ul id="nav">
						<li>
							<a href="/Index"><i class="icon-home"></i><span>首页</span></a>
						</li>
						<li>
							<a class="secondary-menu"><i class="icon-wrench"></i><span>研发管理&nbsp;&nbsp;</span></a>
							<ol class="sr-only">
								<li><a href="/Project"><i class="icon-angle-right"></i>&nbsp;项目管理</a></li>
								<li><a href="/Sample"><i class="icon-angle-right"></i>&nbsp;样品管理</a></li>
								<li><a href="/Product"><i class="icon-angle-right"></i>&nbsp;产品管理</a></li>
								<li><a href="/Compatibility"><i class="icon-angle-right"></i>&nbsp;兼容表</a></li>
							</ol>
						</li>
						<!--<li id="Audit">
							<div class="auditBTN">
								<i class="icon-legal"></i><span>审批　　&nbsp;&nbsp;<span class="icon-caret-right"></span></span>
							</div>
							<div id="AuditChildMenu">
								<span><a href="/Expense"><i class="icon-credit-card"></i>&nbsp;&nbsp;报销</a></span>
							</div>
						</li>-->
						<li>
							<a href="/Approval"><i class="icon-legal"></i><span>审批</span></a>
						</li>
						<li>
							<a href="/DCC"><i class="icon-print"></i><span>文档中心</span></a>
						</li>
						<li>
							<a href="/Acronym"><i class="icon-book"></i><span>缩略词</span></a>
						</li>
						<li>
							<a href="/RMA"><i class="icon-comments-alt"></i><span>客诉处理</span></a>
						</li>
						<li>
							<a href="/Manage"><i class="icon-github-alt"></i><span>用户管理</span></a>
						</li>
						<?php if(($_SESSION['user']['account']) == "admin"): ?><li>
								<a href="/System"><i class="icon-cog"></i><span>系统</span></a>
							</li><?php endif; ?>
					</ul>

					<!-- 版权信息 -->
					<div class="copyright-info">
						<p>Copyright @ 2016</p>
						<p>华拓光通信股份有限公司</p>
						<p>版权所有</p>
					</div>

				</div>
			</div>
		

		<!-- 二级导航及用户信息/常用操作区 -->
		<div id="header">
			
    <ol class="breadcrumb breadcrumb-edit">
        <li><a href="/Manage">用户管理</a></li>
        <li class="active">组织架构图</li>
    </ol>

			<div class="user-operation-box pull-right">
				<div class="user-operation-item pull-left">
					<a href="/index.php/Home/Center">
						<img src="<?php echo ($face["face"]); ?>" width="30" alt="">
						<!--<i class="layui-icon">&#xe612;</i>-->&nbsp;&nbsp;<?php echo ($_SESSION['user']['nickname']); ?>
						<div class="clearfix"></div>
					</a>
				</div>
				<div class="user-operation-item pull-left">
					<a href="/notice"><i class="icon-bell"></i>&nbsp;&nbsp;通知</a>
				</div>
				<div class="user-operation-item pull-left">
					<a href="/Logout"><i class="icon-signout"></i>&nbsp;&nbsp;退出</a>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="clearfix"></div>
		</div>

		<!-- 正文区域 -->
		<div id="content">
			<div class="container-fluid" id="content-box">
				

    <div id="chart-container" style="min-height: 1600px;"></div>

    <script>
        'use strict';
        (function($){
            $(function() {
                var datascource = <?php echo ($orgchart); ?>;
                $('#chart-container').orgchart({
                    'data' : datascource,
                    'nodeContent': 'title',
                    'direction': 'l2r'
                });
            });
        })(jQuery);

    </script>

    <script>
        window.onload = function(){
            var orgchart_height = $('table').height();
            $('#chart-container').css('height',(orgchart_height+100)+'px');
        }
    </script>


			</div>
		</div>

		<!-- 消息提示模态框 -->
		
			<div class="modal fade bs-example-modal-sm" id="message-modal" role="dialog">
				<div class="modal-dialog modal-sm">
					<div class="modal-content">
						<div class="modal-body" id="message-modal-text">
							<p class="modal-message modal-message-success"><i class="icon-ok-sign icon-2x modal-icon-success"></i><span class="message-text">成功后的信息</span></p>
							<p class="modal-message modal-message-error"><i class="icon-remove-sign icon-2x modal-icon-error"></i><span class="message-text">失败后的信息</span></p>
							<p class="modal-message modal-message-info"><i class="icon-info-sign icon-2x modal-icon-info"></i><span class="message-text">错误的信息</span></p>
						</div>
					</div>
				</div>
			</div>
		
	</div>

	<!-- 检测IE（如果是低版本IE浏览器则直接屏蔽） -->
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

	<!-- 定义消息提示模态框 -->
	<div class="modal fade bs-example-modal-sm" id="MessageModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content" id="MessageText">
				<!-- 消息内容 -->
			</div>
		</div>
	</div>

	<!-- 回到顶部 -->
	<div id="scrollBackTop" class="sr-only" title="回到顶部"><i class="icon-arrow-up"></i></div>


	<script>
		//如果检测到用户浏览器为IE则禁用
		var str = navigator.userAgent;

		if(str.indexOf('MSIE')>0){
			alert(str);
			checkIE();
		}

		function checkIE(){
			var body = document.getElementsByName('body');
			var bodyContainer = document.getElementById('bodyContainer');
			var browser_ie = document.getElementById('browser_ie');
			bodyContainer.parentNode.removeChild(bodyContainer);
			browser_ie.style.display = 'block';
		}
	</script>

</body>
</html>