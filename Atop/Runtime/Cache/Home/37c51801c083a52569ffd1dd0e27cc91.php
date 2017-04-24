<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>

	<meta charset="UTF-8">
	<!--<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">-->
	<link href="/Public/Home/img/favicon.ico" rel="shortcut icon">

	
    <title>ATOP</title>
<!-- 网站标题 -->
	<link rel="stylesheet" href="/Public/layui/css/layui.css">
	<link rel="stylesheet" href="/Public/Home/css/bootstrap.min.css">
	<link rel="stylesheet" href="/Public/Home/css/font-awesome.min.css">
	<link rel="stylesheet" href="/Public/Home/css/jquery.mCustomScrollbar.css">
	<link rel="stylesheet" href="/Public/Home/css/iconfont.css">
	<link rel="stylesheet" href="/Public/Home/css/basic.css">
	<link rel="stylesheet" href="/Public/Home/css/<?php echo ($face["theme"]); ?>/index.css">
	<link rel="stylesheet" href="/Public/Home/css/promotion_index.css">

	
    <style>
        .wrap-container {
            padding-bottom: 300px;
        }
        .ol-padding {
            list-style-type: square !important;
            margin-left: 30px;
        }
        code {
            background: none;
            color: #555;
            font-size: 14px;
        }
        .ol-padding li {
            padding: 8px 0;
            border-bottom: solid 1px #eee;
        }
        .ol-padding .page-header {
            margin: 30px 0 0 0;
            border-bottom: none;
        }
        .ol-padding .page-header h4 {
            padding: 5px 15px;
            background: #666;
            color: #fff;
            font-size: 18px;
        }
    </style>
<!-- 引入css文件 -->
	<script src="/Public/Home/js/jquery.min.js"></script>
	<script src="/Public/Home/js/bootstrap.min.js"></script>
	<script src="/Public/layui/layui.js"></script>
	<script src="/Public/Home/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/Public/Home/js/index.js"></script>

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
					<a href="/Index" class="logo"></a>

					<!-- 导航区 -->
					<ul id="nav">
						<li>
							<a class="secondary-menu"><i class="icon-wrench"></i><span>研发管理&nbsp;&nbsp;</span></a>
							<ol class="<?php if(in_array((CONTROLLER_NAME), explode(',',"Project,Sample,Product,Compatibility"))): else: ?>sr-only<?php endif; ?>">
								<li><a href="/Project" class="<?php if((CONTROLLER_NAME) == "Project"): ?>active<?php endif; ?>">&nbsp;&nbsp;项目管理</a></li>
								<li><a href="/Sample" class="<?php if((CONTROLLER_NAME) == "Sample"): ?>active<?php endif; ?>">&nbsp;&nbsp;样品管理</a></li>
								<li><a href="/Product" class="<?php if((CONTROLLER_NAME) == "Product"): ?>active<?php endif; ?>">&nbsp;&nbsp;产品管理</a></li>
								<li><a href="/Compatibility" class="<?php if((CONTROLLER_NAME) == "Compatibility"): ?>active<?php endif; ?>">&nbsp;&nbsp;兼容表</a></li>
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
							<a href="/Approval" class="<?php if((CONTROLLER_NAME) == "Approval"): ?>active<?php endif; ?>"><i class="icon-legal"></i><span>审批</span></a>
						</li>
						<li>
							<a href="/DCC" class="<?php if((CONTROLLER_NAME) == "DCC"): ?>active<?php endif; ?>"><i class="icon-print"></i><span>文档中心</span></a>
						</li>
						<li>
							<a href="/Acronym" class="<?php if((CONTROLLER_NAME) == "Acronym"): ?>active<?php endif; ?>"><i class="icon-book"></i><span>缩略词</span></a>
						</li>
						<li>
							<a href="/RMA" class="<?php if((CONTROLLER_NAME) == "RMA"): ?>active<?php endif; ?>"><i class="icon-comments-alt"></i><span>客诉处理</span></a>
						</li>
						<li>
							<a href="/Manage" class="<?php if((CONTROLLER_NAME) == "Manage"): ?>active<?php endif; ?>"><i class="icon-user"></i><span>用户管理</span></a>
						</li>
						<?php if(($_SESSION['user']['account']) == "admin"): ?><li>
								<a href="/System" class="<?php if((CONTROLLER_NAME) == "System"): ?>active<?php endif; ?>"><i class="icon-cog"></i><span>系统</span></a>
							</li><?php endif; ?>
					</ul>

					<!-- 版权信息 -->
					<div class="copyright-info">
						<p>ATOP Corporation</p>
						<p>Copyright &copy; 2016</p>
					</div>

				</div>
			</div>
		

		<!-- 二级导航及用户信息/常用操作区 -->
		<div id="header">
			<!-- 面包屑导航 -->
			
    <ol class="breadcrumb breadcrumb-edit">
        <li><a href="/Sample">样品管理</a></li>
        <li class="active"><a href="/Sample/details/order/<?php echo ($order); ?>">样品详情</a></li>
        <li class="active">所有日志</li>
    </ol>


			<div class="user-operation-box pull-right">
				<div class="user-operation-item pull-left">
					<a href="/Notice" class="option-name"><i class="icon-bell"></i>&nbsp;&nbsp;&nbsp;通知</a>
				</div>
				<div class="user-operation-item pull-left user-options-btn">
					<a class="option-name">
						<i class="layui-icon">&#xe612;</i>&nbsp;&nbsp;&nbsp;<?php echo ($_SESSION['user']['nickname']); ?>&nbsp;&nbsp;&nbsp;<i class="icon-caret-down"></i>
					</a>
					<!-- 用户选项 -->
					<div class="user-options">
						<ul>
							<li><a href="/Center/modify"><i class="icon-pencil"></i>&nbsp;&nbsp;修改资料</a></li>
							<li><a href="/Center/face"><i class="icon-github-alt"></i>&nbsp;&nbsp;修改头像</a></li>
							<li><a href="/Logout"><i class="icon-signout"></i>&nbsp;&nbsp;退出登录</a></li>
						</ul>
					</div>
				</div>
				<div class="clearfix"></div>

				<!--<div class="user-operation-item pull-left">
					<a href="/Logout"><i class="icon-signout"></i>&nbsp;&nbsp;退出</a>
				</div>-->
			</div>
			<div class="clearfix"></div>
		</div>



		<!-- 正文区域 -->
		<div id="content">
			<div class="container-fluid" id="content-box">
				
    <div class="col-lg-8 col-lg-offset-2 wrap-container">
        <div class="page-header">
            <h2><i class="icon-bullhorn">&nbsp;&nbsp;</i>所有日志</h2>
        </div>
        <ul class="ol-padding">
            <!--审核步骤日志-->
            <div class="page-header"><h4>审核</h4></div>
            <?php if(isset($allLog["s_comment"]["save"])): if(is_array($allLog["s_comment"]["save"])): $i = 0; $__LIST__ = $allLog["s_comment"]["save"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(!empty($value["message"])): ?><li><p><code><?php echo (date("Y-m-d H:i:s",$value["time"])); ?></code>&nbsp;&nbsp;【<?php echo ($value["manager"]); ?>】<?php echo ($value["message"]); ?></p></li><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
            <?php if(isset($allLog["s_comment"]["success"])): if(is_array($allLog["s_comment"]["success"])): $i = 0; $__LIST__ = $allLog["s_comment"]["success"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(!empty($value["message"])): ?><li><p><code><?php echo (date("Y-m-d H:i:s",$value["time"])); ?></code>&nbsp;&nbsp;【<?php echo ($value["manager"]); ?>】<?php echo ($value["message"]); ?></p></li><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
            <?php if(isset($allLog["s_comment"]["error"])): if(is_array($allLog["s_comment"]["error"])): $i = 0; $__LIST__ = $allLog["s_comment"]["error"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(!empty($value["message"])): ?><li><p><code><?php echo (date("Y-m-d H:i:s",$value["time"])); ?></code>&nbsp;&nbsp;【<?php echo ($value["manager"]); ?>】<?php echo ($value["message"]); ?></p></li><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
            <!--物料准备步骤日志-->
            <div class="page-header"><h4>物料准备</h4></div>
            <?php if(isset($allLog["w_comment"]["save"])): if(is_array($allLog["w_comment"]["save"])): $i = 0; $__LIST__ = $allLog["w_comment"]["save"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(!empty($value["message"])): ?><li><p><code><?php echo (date("Y-m-d H:i:s",$value["time"])); ?></code>&nbsp;&nbsp;【<?php echo ($value["manager"]); ?>】<?php echo ($value["message"]); ?></p></li><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
            <?php if(isset($allLog["w_comment"]["success"])): if(is_array($allLog["w_comment"]["success"])): $i = 0; $__LIST__ = $allLog["w_comment"]["success"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(!empty($value["message"])): ?><li><p><code><?php echo (date("Y-m-d H:i:s",$value["time"])); ?></code>&nbsp;&nbsp;【<?php echo ($value["manager"]); ?>】<?php echo ($value["message"]); ?></p></li><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
            <?php if(isset($allLog["w_comment"]["error"])): if(is_array($allLog["w_comment"]["error"])): $i = 0; $__LIST__ = $allLog["w_comment"]["error"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(!empty($value["message"])): ?><li><p><code><?php echo (date("Y-m-d H:i:s",$value["time"])); ?></code>&nbsp;&nbsp;【<?php echo ($value["manager"]); ?>】<?php echo ($value["message"]); ?></p></li><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
            <!--样品制作步骤日志-->
            <div class="page-header"><h4>样品制作</h4></div>
            <?php if(isset($allLog["c_comment"]["save"])): if(is_array($allLog["c_comment"]["save"])): $i = 0; $__LIST__ = $allLog["c_comment"]["save"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(!empty($value["message"])): ?><li><p><code><?php echo (date("Y-m-d H:i:s",$value["time"])); ?></code>&nbsp;&nbsp;【<?php echo ($value["manager"]); ?>】<?php echo ($value["message"]); ?></p></li><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
            <?php if(isset($allLog["c_comment"]["success"])): if(is_array($allLog["c_comment"]["success"])): $i = 0; $__LIST__ = $allLog["c_comment"]["success"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(!empty($value["message"])): ?><li><p><code><?php echo (date("Y-m-d H:i:s",$value["time"])); ?></code>&nbsp;&nbsp;【<?php echo ($value["manager"]); ?>】<?php echo ($value["message"]); ?></p></li><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
            <?php if(isset($allLog["c_comment"]["error"])): if(is_array($allLog["c_comment"]["error"])): $i = 0; $__LIST__ = $allLog["c_comment"]["error"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(!empty($value["message"])): ?><li><p><code><?php echo (date("Y-m-d H:i:s",$value["time"])); ?></code>&nbsp;&nbsp;【<?php echo ($value["manager"]); ?>】<?php echo ($value["message"]); ?></p></li><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
            <!--样品测试步骤日志-->
            <div class="page-header"><h4>样品测试</h4></div>
            <?php if(isset($allLog["y_comment"]["save"])): if(is_array($allLog["y_comment"]["save"])): $i = 0; $__LIST__ = $allLog["y_comment"]["save"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(!empty($value["message"])): ?><li><p><code><?php echo (date("Y-m-d H:i:s",$value["time"])); ?></code>&nbsp;&nbsp;【<?php echo ($value["manager"]); ?>】<?php echo ($value["message"]); ?></p></li><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
            <?php if(isset($allLog["y_comment"]["success"])): if(is_array($allLog["y_comment"]["success"])): $i = 0; $__LIST__ = $allLog["y_comment"]["success"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(!empty($value["message"])): ?><li><p><code><?php echo (date("Y-m-d H:i:s",$value["time"])); ?></code>&nbsp;&nbsp;【<?php echo ($value["manager"]); ?>】<?php echo ($value["message"]); ?></p></li><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
            <?php if(isset($allLog["y_comment"]["error"])): if(is_array($allLog["y_comment"]["error"])): $i = 0; $__LIST__ = $allLog["y_comment"]["error"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(!empty($value["message"])): ?><li><p><code><?php echo (date("Y-m-d H:i:s",$value["time"])); ?></code>&nbsp;&nbsp;【<?php echo ($value["manager"]); ?>】<?php echo ($value["message"]); ?></p></li><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
            <!--发货步骤日志-->
            <div class="page-header"><h4>发货</h4></div>
            <?php if(isset($allLog["f_comment"]["save"])): if(is_array($allLog["f_comment"]["save"])): $i = 0; $__LIST__ = $allLog["f_comment"]["save"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(!empty($value["message"])): ?><li><p><code><?php echo (date("Y-m-d H:i:s",$value["time"])); ?></code>&nbsp;&nbsp;【<?php echo ($value["manager"]); ?>】<?php echo ($value["message"]); ?></p></li><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
            <?php if(isset($allLog["f_comment"]["success"])): if(is_array($allLog["f_comment"]["success"])): $i = 0; $__LIST__ = $allLog["f_comment"]["success"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(!empty($value["message"])): ?><li><p><code><?php echo (date("Y-m-d H:i:s",$value["time"])); ?></code>&nbsp;&nbsp;【<?php echo ($value["manager"]); ?>】<?php echo ($value["message"]); ?></p></li><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
            <?php if(isset($allLog["f_comment"]["error"])): if(is_array($allLog["f_comment"]["error"])): $i = 0; $__LIST__ = $allLog["f_comment"]["error"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(!empty($value["message"])): ?><li><p><code><?php echo (date("Y-m-d H:i:s",$value["time"])); ?></code>&nbsp;&nbsp;【<?php echo ($value["manager"]); ?>】<?php echo ($value["message"]); ?></p></li><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
            <!--反馈步骤日志-->
            <div class="page-header"><h4>反馈</h4></div>
            <?php if(isset($allLog["k_comment"]["save"])): if(is_array($allLog["k_comment"]["save"])): $i = 0; $__LIST__ = $allLog["k_comment"]["save"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(!empty($value["message"])): ?><li><p><code><?php echo (date("Y-m-d H:i:s",$value["time"])); ?></code>&nbsp;&nbsp;【<?php echo ($value["manager"]); ?>】<?php echo ($value["message"]); ?></p></li><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
            <?php if(isset($allLog["k_comment"]["success"])): if(is_array($allLog["k_comment"]["success"])): $i = 0; $__LIST__ = $allLog["k_comment"]["success"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(!empty($value["message"])): ?><li><p><code><?php echo (date("Y-m-d H:i:s",$value["time"])); ?></code>&nbsp;&nbsp;【<?php echo ($value["manager"]); ?>】<?php echo ($value["message"]); ?></p></li><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
            <?php if(isset($allLog["k_comment"]["error"])): if(is_array($allLog["k_comment"]["error"])): $i = 0; $__LIST__ = $allLog["k_comment"]["error"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(!empty($value["message"])): ?><li><p><code><?php echo (date("Y-m-d H:i:s",$value["time"])); ?></code>&nbsp;&nbsp;【<?php echo ($value["manager"]); ?>】<?php echo ($value["message"]); ?></p></li><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
        </ul>
    </div>

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

	<!-- Modal集 -->
	

	

	<!-- 回到顶部 -->
	<div class="footer-operation-bar">
		<div id="scrollBackTop" class="sr-only" title="回到顶部"><i class="icon-arrow-up"></i></div>
	</div>


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