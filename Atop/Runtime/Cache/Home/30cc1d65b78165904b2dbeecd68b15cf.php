<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>

	<meta charset="UTF-8">
	<!--<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">-->
	<link href="/Public/Home/img/favicon.ico" rel="shortcut icon">

	
	<title>文档中心</title>
<!-- 网站标题 -->
	<link rel="stylesheet" href="/Public/layui/css/layui.css">
	<link rel="stylesheet" href="/Public/Home/css/bootstrap.min.css">
	<link rel="stylesheet" href="/Public/Home/css/font-awesome.min.css">
	<link rel="stylesheet" href="/Public/Home/css/jquery.mCustomScrollbar.css">
	<link rel="stylesheet" href="/Public/Home/css/iconfont.css">
	<link rel="stylesheet" href="/Public/Home/css/basic.css">
	<link rel="stylesheet" href="/Public/Home/css/<?php echo ($face["theme"]); ?>/index.css">
	<link rel="stylesheet" href="/Public/Home/css/promotion_index.css">

	
	<link rel="stylesheet" href="/Public/Home/css/Huploadify.css">
	<link rel="stylesheet" type="text/css" href="/Public/Home/css/word.css">
<!-- 引入css文件 -->
	<script src="/Public/Home/js/jquery.min.js"></script>
	<script src="/Public/Home/js/bootstrap.min.js"></script>
	<script src="/Public/layui/layui.js"></script>
	<script src="/Public/Home/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/Public/Home/js/index.js"></script>

	
	<script src="/Public/Home/js/jquery.Huploadify.js"></script>
	<script src="/Public/Home/js/word.js"></script>
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
					<div class="logo">

					</div>
					<div id="face">
						<div class="face-box">
							<div class="face">
								<a href="/index.php/Home/Center">
									<img id="face-picture" src="<?php echo ($face["face"]); ?>" alt="" width="70" height="70">
								</a>
							</div>
							<p id="userName">Hello
								<?php if(empty($_SESSION['user']['nickname'])): ?><span id="user_info"><?php echo ($_SESSION['user']['account']); ?> <span class="caret" id="rotate-caret"></span></span>
								<?php else: ?>
									<span id="user_info"><?php echo ($_SESSION['user']['nickname']); ?> <span class="caret" id="rotate-caret"></span></span><?php endif; ?>
							</p>
							<a href="/notice" style="display: block;"><span class="sidebar-message"><span class="badge sidebar-message-badge"></span></span></a>
							<a href="javascript:void(0);" style="display: block;"><span class="sidebar-notice"></span></a>
							<div id="face_dropdown">
								<ul>
									<li><a href="/Center/modify"><i class="icon-list-alt"></i> 修改资料</a></li>
									<li><a href="/Center/face"><i class="icon-github-alt"></i> 修改头像</a></li>
									<li><a href="/Logout"><i class="icon-signout"></i> 退出登录</a></li>
								</ul>
							</div>
						</div>
					</div>
					<!-- <ul id="nav"> -->
					<ul id="nav">
						<li><a href="/Index"><b class="icon-home"></b><span>首页</span></a></li>
						<li>
							<a class="secondary-menu"><i class="icon-wrench"></i><span>研发管理&nbsp;&nbsp;</span></a>
							<ol class="sr-only">
								<li><a href="/Project">项目管理</a></li>
								<li><a href="/Sample">样品管理</a></li>
								<li><a href="/Product">产品管理</a></li>
								<li><a href="/Compatibility">兼容表</a></li>
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
						<li><a href="/Approval"><i class="icon-legal"></i><span>审批</span></a></li>
						<li><a href="/DCC"><i class="icon-print"></i><span>文档中心</span></a></li>
						<li><a href="/Acronym"><i class="icon-book"></i><span>缩略词</span></a></li>
						<li><a href="/RMA"><i class="icon-comments-alt"></i><span>客诉处理</span></a></li>
						<li><a href="/Manage"><i class="icon-github-alt"></i><span>用户管理</span></a></li>
						<?php if(($_SESSION['user']['account']) == "admin"): ?><li><a href="/System"><i class="icon-cog"></i><span>系统</span></a></li><?php endif; ?>
					</ul>
				</div>
			</div>
		

		<!--<div id="ResearchChildMenu">
			<span><a href="/Project"><i class="icon-globe"></i>&nbsp;&nbsp;项目管理</a></span>
			<span><a href="/Sample"><i class="icon-beaker"></i>&nbsp;&nbsp;样品管理</a></span>
			<span><a href="/Product"><i class="icon-th"></i>&nbsp;&nbsp;产品管理</a></span>
			<span><a href="/Compatibility"><i class="icon-bar-chart"></i>&nbsp;&nbsp;兼容表</a></span>
		</div>-->



		<div id="header">
			
	<ol class="breadcrumb breadcrumb-edit">
		<li class="active">文档中心</li>
	</ol>

		</div>

		<div id="content">
			<div class="container-fluid" id="content-box">
				
	<!--<div class="commingSoon">
		<p><i class="icon-info-sign icon-5x"></i></p>
		<h1>功能研发中</h1>
		<p>Function is developing</p>
		<p>Comming Soon.</p>
	</div>-->

	<div class="department-btn">
		<a href="/DCC?department=6" class="col-box plan-box <?php if(($userDepartment) == "6"): ?>active<?php endif; ?>" departmentid="6">
			<div class="department-icon administration-icon"></div>
			<div class="department-name">行政部</div>
		</a>
		<a href="/DCC?department=1" class="col-box sales-box <?php if(($userDepartment) == "1"): ?>active<?php endif; ?>" departmentid="1">
			<div class="department-icon  development-icon"></div>
			<div class="department-name">研发部</div>
		</a>
		<a href="/DCC?department=2" class="col-box administration-box <?php if(($userDepartment) == "2"): ?>active<?php endif; ?>" departmentid="2">
			<div class="department-icon production-icon"></div>
			<div class="department-name">生产部</div>
		</a>
		<a href="/DCC?department=3" class="col-box development-box <?php if(($userDepartment) == "3"): ?>active<?php endif; ?>" departmentid="3">
			<div class="department-icon personnel-icon"></div>
			<div class="department-name">品质部</div>
		</a>
		<a href="/DCC?department=4" class="col-box personnel-box <?php if(($userDepartment) == "4"): ?>active<?php endif; ?>" departmentid="4">
			<div class="department-icon sales-icon"></div>
			<div class="department-name">销售部</div>
		</a>
		<a href="/DCC?department=5" class="col-box plan-box <?php if(($userDepartment) == "5"): ?>active<?php endif; ?>" departmentid="5">
			<div class="department-icon plan-icon"></div>
			<div class="department-name">计划部</div>
		</a>
	</div>

	<!--<ul class="nav nav-tabs tabs-edit" role="tablist">
		<li role="presentation" folder="public" class="active"><a href="/Dcc?department=<?php echo ($userDepartment); ?>&type=public" role="tab" data-toggle="tab"><i class="icon-eye-open"></i>&nbsp;公开的</a></li>
		<li role="presentation" folder="private"><a href="/Dcc?department=<?php echo ($userDepartment); ?>&type=private" role="tab" data-toggle="tab"><i class="icon-lock"></i>&nbsp;内部的</a></li>
		&lt;!&ndash;<div class="file-upload" id="file-upload"></div>&ndash;&gt;
	</ul>-->
	<div class="content-body">
		<!--<div class="operation-bar">
			<a href="#"><i class="icon-reply"></i>&nbsp;返回上一级</a>
			<a href="#" class="pull-right"><i class="icon-list-ol"></i>&nbsp;排序</a>
			<a href="#" class="pull-right add-folder"><i class="icon-plus"></i>&nbsp;新建文件夹</a>
			<div class="input-group input-group-edit pull-right">
				<input type="text" class="form-control form-control-edit" name="" placeholder="输入搜索内容...">
				<span class="input-group-btn input-group-btn-edit">
					<button class="btn btn-primary btn-primary-edit"><i class="icon-search"></i></button>
				</span>
			</div>
		</div>-->
		<div class="panel panel-default">
			<div class="panel-heading">
				<h2 class="panel-title">公开文档</h2>
			</div>
			<div class="panel-body">
				<div class="file-box">
					<ul class="folders-list">
						<?php if(isset($folders["public"]["file"])): if(is_array($folders["public"]["file"])): $i = 0; $__LIST__ = $folders["public"]["file"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li class="file-icon" file-path="<?php echo ($value); ?>" title="<?php echo getBasename($value);?>"><a href="<?php echo ($value); ?>" target="_blank"><i class="icon-file-alt"></i>&nbsp;<?php echo getBasename($value);?></a></li><?php endforeach; endif; else: echo "" ;endif; endif; ?>
						<?php if(!empty($folders["public"])): if(isset($folders["public"]["dir"])): echo traverse_document($folders['public']['dir']); endif; ?>
						<?php else: ?>
							没有数据<?php endif; ?>
					</ul>
				</div>
			</div>
		</div>

		<?php if(is_array($folders['private'])): ?><div class="panel panel-default">
				<div class="panel-heading">
					<h2 class="panel-title">内部文档</h2>
				</div>
				<div class="panel-body">
					<div class="file-box">
						<ul class="folders-list">
							<?php if(isset($folders["private"]["file"])): if(is_array($folders["private"]["file"])): $i = 0; $__LIST__ = $folders["private"]["file"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li class="file-icon" file-path="<?php echo ($value); ?>"><a href="<?php echo ($value); ?>" target="_blank"><i class="icon-file-alt"></i>&nbsp;<?php echo getBasename($value);?></a></li><?php endforeach; endif; else: echo "" ;endif; endif; ?>
							<?php if(!empty($folders["private"])): if(isset($folders["private"]["dir"])): echo traverse_document($folders['private']['dir']); endif; ?>
							<?php else: ?>
								没有数据<?php endif; ?>
						</ul>
					</div>
				</div>
			</div><?php endif; ?>

	</div>

	<!-- 自定义右键菜单 -->
	<!--<div id="right-click-menu" style="display: none;">
		<ul>
			<li operation="rename" id="file-rename"><i class="icon-pencil"></i>&nbsp;重命名</li>
			<li class="file-operation" operation="delete" id="file-delete"><i class="icon-remove-sign"></i>&nbsp;删除</li>
			<li operation="details" id="file-details"><i class="icon-info-sign"></i>&nbsp;详细信息</li>
			<hr>
			<li class="file-operation" operation="download" id="file-download"><i class="icon-circle-arrow-down"></i>&nbsp;下载文件</li>
		</ul>
	</div>-->

	<!-- 文件重命名模态框 -->
	<!--<div class="modal fade bs-example-modal-sm" id="rename-modal" tabindex="-1" role="dialog" aria-labelledby="rename-modal" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">重命名</h4>
				</div>
				<div class="modal-body">
					<div class="input-group">
						<input type="text" class="form-control rename-input" name="new-filename">
						<span class="input-group-addon file-ext"></span>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
					<button type="button" class="btn btn-primary">确定</button>
				</div>
			</div>
		</div>
	</div>-->
	<!-- 文件详情模态框 -->
	<!--<div class="modal fade bs-example-modal-sm" id="details-modal" tabindex="-1" role="dialog" aria-labelledby="rename-modal" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">详细信息</h4>
				</div>
				<div class="modal-body">
					<p>文件名称：<span class="details-file-name"></span></p>
					<p>文件类型：<span class="details-file-type"></span></p>
					<p>文件大小：<span class="details-file-size"></span></p>
					<p>创建日期：<span class="details-file-create"></span></p>
					<p>修改日期：<span class="details-file-update"></span></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
				</div>
			</div>
		</div>
	</div>-->

	<!-- 记录用户当前所在目录 -->
	<input type="hidden" name="" value="<?php echo ($path); ?>" class="path" id="now-path">


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

	<div class="modal fade bs-example-modal-sm" id="MessageModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
		<div class="modal-dialog modal-sm" role="document">
			<div class="modal-content" id="MessageText">
				<!-- 消息内容 -->
				<!--<p><i class="icon-ok-sign text-success"></i>&nbsp;添加成功</p>-->
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

</body>
</html>