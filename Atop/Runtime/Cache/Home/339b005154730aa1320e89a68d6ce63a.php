<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>

	<meta charset="UTF-8">
	<!--<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">-->
	<link href="/Public/Home/img/favicon.ico" rel="shortcut icon">

	
	<title>添加用户</title>
<!-- 网站标题 -->
	<link rel="stylesheet" href="/Public/layui/css/layui.css">
	<link rel="stylesheet" href="/Public/Home/css/bootstrap.min.css">
	<link rel="stylesheet" href="/Public/Home/css/font-awesome.min.css">
	<link rel="stylesheet" href="/Public/Home/css/jquery.mCustomScrollbar.css">
	<link rel="stylesheet" href="/Public/Home/css/iconfont.css">
	<link rel="stylesheet" href="/Public/Home/css/basic.css">
	<link rel="stylesheet" href="/Public/Home/css/<?php echo ($face["theme"]); ?>/index.css">
	<link rel="stylesheet" href="/Public/Home/css/promotion_index.css">

	
	<link rel="stylesheet" href="/Public/Home/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="/Public/Home/css/addPerson.css">
<!-- 引入css文件 -->
	<script src="/Public/Home/js/jquery.min.js"></script>
	<script src="/Public/Home/js/bootstrap.min.js"></script>
	<script src="/Public/layui/layui.js"></script>
	<script src="/Public/Home/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/Public/Home/js/index.js"></script>

	
	<script src="/Public/Home/js/jquery.validate.min.js"></script>
	<script src="/Public/Home/js/jquery.form.js"></script>
	<script src="/Public/Home/js/person.js"></script>
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
							<ol class="sr-only">
								<li><a href="/Project" class="<?php if((CONTROLLER_NAME) == "RMA"): ?>active<?php endif; ?>"><i class="icon-angle-right"></i>&nbsp;项目管理</a></li>
								<li><a href="/Sample" class="<?php if((CONTROLLER_NAME) == "RMA"): ?>active<?php endif; ?>"><i class="icon-angle-right"></i>&nbsp;样品管理</a></li>
								<li><a href="/Product" class="<?php if((CONTROLLER_NAME) == "RMA"): ?>active<?php endif; ?>"><i class="icon-angle-right"></i>&nbsp;产品管理</a></li>
								<li><a href="/Compatibility" class="<?php if((CONTROLLER_NAME) == "RMA"): ?>active<?php endif; ?>"><i class="icon-angle-right"></i>&nbsp;兼容表</a></li>
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
							<a href="/Manage" class="<?php if((CONTROLLER_NAME) == "Manage"): ?>active<?php endif; ?>"><i class="icon-github-alt"></i><span>用户管理</span></a>
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
		<li><a href="/Manage">用户管理</a></li>
		<li class="active">添加用户</li>
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
				
	
	<div class="col-lg-6 col-lg-offset-3 wrap">
		<form id="addForm" role="form">
			<div class="page-header"><h2>基本信息</h2></div>
			<div class="form-group">
				<label for="account">账号</label>
				<div class="input-group">
					<span class="input-group-addon"><i class="icon-user"></i></span>
					<input type="text" class="form-control" name="account" id="account" placeholder="请输入账号">
				</div>
				<p class="help-block help-block-color"></p>
			</div>
			<div class="form-group">
				<label for="password">密码</label>
				<div class="input-group">
					<span class="input-group-addon"><i class="icon-lock"></i></span>
					<input type="text" class="form-control" name="password" id="password" placeholder="请输入密码">
					<span class="input-group-btn">
						<button type="button" class="btn btn-default" id="random">随机</button>
					</span>
				</div>
				<p class="help-block help-block-color"></p>
			</div>
			<div class="form-group">
				<label for="nickname">用户姓名</label>
				<div class="input-group">
					<span class="input-group-addon"><i class="icon-user"></i></span>
					<input type="text" class="form-control" name="nickname" id="nickname" placeholder="请输入用户姓名">
				</div>
				<p class="help-block help-block-color"></p>
			</div>
			<div class="form-group">
				<label for="email">电子邮箱</label>
				<div class="input-group">
					<span class="input-group-addon"><i class="icon-envelope"></i></span>
					<input type="text" class="form-control" name="email" id="email" placeholder="请输入电子邮箱">
				</div>
				<p class="help-block help-block-color"></p>
			</div>
			<div class="page-header"><h2>部门职位</h2></div>
			<div class="form-group form-group-edit">
				<label>部门&nbsp;</label>
				<div class="btn-group">
					<button class="btn btn-default" type="button" id="department-value">研发部</button>
					<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu" id="department-list">
						<?php if(is_array($department)): $i = 0; $__LIST__ = $department;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li index="<?php echo ($value["id"]); ?>"><a href="javascript:void(0);"><?php echo ($value["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</div>
				<p class="help-block help-block-color"></p>
			</div>
			<div class="form-group form-group-edit">
				<label>级别 </label>
				<div class="btn-group">
					<button class="btn btn-default" type="button" id="level-value">助理/实习生</button>
					<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu" id="level-list">
						<?php if(is_array($level)): $i = 0; $__LIST__ = $level;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li index="<?php echo ($value["id"]); ?>"><a href="javascript:void(0);"><?php echo ($value["levelname"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</div>
				<p class="help-block"></p>
			</div>
			<div class="form-group form-group-edit form-group-edit-last">
				<label>职位&nbsp;</label>
				<div class="btn-group">
					<button class="btn btn-default" type="button" id="position-value">销售总监</button>
					<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu" id="position-list">
						<?php if(is_array($position)): $i = 0; $__LIST__ = $position;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li index="<?php echo ($value["id"]); ?>"><a href="javascript:void(0);"><?php echo ($value["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</div>
				<p class="help-block"></p>
			</div>
			<div class="page-header"><h2>汇报关系 <small class="small-font-size">[ 无需汇报则不选 ]</small></h2></div>
			<div class="form-group" id="report">
				<?php if(is_array($departmentList)): $i = 0; $__LIST__ = $departmentList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><div class="page-header page-header-department"><h4><kbd><?php echo ($value["name"]); ?>:</kbd></h4></div>
					<?php if(is_array($alluser)): $i = 0; $__LIST__ = $alluser;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(($vo["department"]) == $value["id"]): ?><span class="name-box" index="<?php echo ($vo["id"]); ?>"><?php echo ($vo["nickname"]); ?></span><?php endif; endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
			</div>
			<div class="page-header page-header-edit">
				<h2>权限配置 <small class="small-font-size">[ 配置可访问的功能模块 ]</small></h2>
				<div class="btn-group btn-group-edit pull-right">
					<button class="btn btn-default btn-sm" id="checked-all" type="button">全选</button>
					<button class="btn btn-default btn-sm" id="checked-rev" type="button">反选</button>
				</div>
			</div>
			<div class="checkbox checkbox-edit">
				<?php if(is_array($authrule)): $i = 0; $__LIST__ = $authrule;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><label>
						<?php if($value["title"] == '后台' OR $value["title"] == '个人中心' OR $value["title"] == '通知中心'): ?><input type="checkbox" name="permissions[]" checked="checked" value="<?php echo ($value["id"]); ?>"><?php echo ($value["title"]); ?>
						<?php elseif($value["title"] == '系统'): ?>
						<?php else: ?>
							<input type="checkbox" name="permissions[]" value="<?php echo ($value["id"]); ?>"><?php echo ($value["title"]); endif; ?>
					</label><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
			<div class="form-group form-group-submit">
				<input type="hidden" name="department" value="1" id="department-id">
				<input type="hidden" name="position" value="1" id="position-id">
				<input type="hidden" name="report" value="" id="report-id">
				<input type="hidden" name="level" value="1" id="level-id">
				<input type="submit" class="btn btn-primary btn-lg input-reset" value="添加"> 
				<input type="reset" class="btn btn-default btn-lg input-submit" value="重置">
			</div>
		</form>
	</div>

	<!-- 添加用户状态模态框 -->
	<div class="modal fade bs-example-modal-sm" id="adduser-modal" tabindex="-1" role="modal" aria-labelledby="addUserModal" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-body">
					<p class="adduser-message adduser-message-success"><i class="icon-ok-sign icon-2x modal-icon-success"></i><span class="message-text">成功后的信息</span></p>
					<p class="adduser-message adduser-message-error"><i class="icon-remove-sign icon-2x modal-icon-error"></i><span class="message-text">失败后的信息</span></p>
					<p class="adduser-message adduser-message-info"><i class="icon-info-sign icon-2x modal-icon-info"></i><span class="message-text">错误的信息</span></p>
				</div>
			</div>
		</div>
	</div>

	<!-- 成功添加用户模态框 -->
	<div class="modal fade bs-example-modal-sm" id="addUserSuccess-modal" tabindex="-1" role="modal" aria-labelledby="addUserModal" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content" id="addUserModal">
				<div class="modal-body">
					<p class="addUserSuccess-message addUserSuccess-message-success"><i class="icon-ok-sign icon-2x modal-icon-success"></i><span class="message-text">添加成功，是否邮件通知他/她？</span></p>
				</div>
				<div class="modal-footer">
					<input type="hidden" name="sendEmail" value="2737583968@qq.com" id="sendMail">
					<button class="btn btn-success" id="EmailNotice">通知</button>
					<button class="btn btn-primary" data-dismiss="modal">不用</button>
				</div>
			</div>
		</div>
	</div>

	<div class="loading-modal sr-only" id="loading">
		<div class="loading-icon">
			<p><i class="icon-spinner icon-spin icon-4x"></i></p>
		</div>
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