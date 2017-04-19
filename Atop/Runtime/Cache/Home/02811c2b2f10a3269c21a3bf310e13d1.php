<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>

	<meta charset="UTF-8">
	<!--<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">-->
	<link href="/Public/Home/img/favicon.ico" rel="shortcut icon">

	
	<title>修改用户</title>
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
	<link rel="stylesheet" href="/Public/Home/css/editPerson.css">
<!-- 引入css文件 -->
	<script src="/Public/Home/js/jquery.min.js"></script>
	<script src="/Public/Home/js/bootstrap.min.js"></script>
	<script src="/Public/layui/layui.js"></script>
	<script src="/Public/Home/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/Public/Home/js/index.js"></script>

	
	<script src="/Public/Home/js/jquery.validate.min.js"></script>
	<script src="/Public/Home/js/jquery.form.js"></script>
	<script src="/Public/Home/js/editPerson.js"></script>
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
		<li><a href="/Manage">用户管理</a></li>
		<li class="active">修改用户</li>
	</ol>

		</div>

		<div id="content">
			<div class="container-fluid" id="content-box">
				
	
	<div class="col-lg-6 col-lg-offset-3 wrap">
		<form id="addForm" role="form">
			<div class="page-header"><h2>基本信息</h2></div>
			<div class="form-group">
				<label for="account">账号</label>
				<div class="input-group">
					<span class="input-group-addon"><i class="icon-user"></i></span>
					<input type="text" class="form-control" name="account" id="account" placeholder="请输入账号" value="<?php echo ($personData["account"]); ?>" readonly>
				</div>
				<p class="help-block help-block-color"></p>
			</div>
			<div class="form-group">
				<label for="password">密码</label>
				<div class="input-group">
					<span class="input-group-addon"><i class="icon-lock"></i></span>
					<input type="password" class="form-control" name="password" id="password" placeholder="请输入密码" value="<?php echo ($personData["password"]); ?>" readonly>
					<span class="input-group-btn">
						<button type="button" class="btn btn-default" id="resetPassword" nowUser="<?php echo ($_GET['id']); ?>">重置密码</button>
					</span>
				</div>
				<p class="help-block help-block-color"></p>
			</div>
			<div class="form-group">
				<label for="nickname">用户姓名</label>
				<div class="input-group">
					<span class="input-group-addon"><i class="icon-user"></i></span>
					<input type="text" class="form-control" name="nickname" id="nickname" placeholder="请输入用户姓名" value="<?php echo ($personData["nickname"]); ?>">
				</div>
				<p class="help-block help-block-color"></p>
			</div>
			<div class="form-group">
				<label for="email">电子邮箱</label>
				<div class="input-group">
					<span class="input-group-addon"><i class="icon-envelope"></i></span>
					<input type="text" class="form-control" name="email" id="email" placeholder="请输入电子邮箱" value="<?php echo ($personData["email"]); ?>">
				</div>
				<p class="help-block help-block-color"></p>
			</div>
			<div class="page-header"><h2>部门职位</h2></div>
			<div class="form-group form-group-edit">
				<label>部门&nbsp;</label>
				<div class="btn-group">
					<button class="btn btn-default" type="button" id="department-value"><?php echo ($personData["department_name"]); ?></button>
					<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu" id="department-list">
						<?php if(is_array($department)): $i = 0; $__LIST__ = $department;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li index="<?php echo ($value["id"]); ?>"><a href="javascript:;"><?php echo ($value["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</div>
				<p class="help-block help-block-color"></p>
			</div>
			<div class="form-group form-group-edit">
				<label>级别 </label>
				<div class="btn-group">
					<button class="btn btn-default" type="button" id="level-value">
						<?php if(is_array($level)): $i = 0; $__LIST__ = $level;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(($value["id"]) == $personData["level"]): echo ($value["levelname"]); endif; endforeach; endif; else: echo "" ;endif; ?>
					</button>
					<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu" id="level-list">
						<?php if(is_array($level)): $i = 0; $__LIST__ = $level;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li index="<?php echo ($value["id"]); ?>"><a href="javascript:;"><?php echo ($value["levelname"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</div>
				<p class="help-block"></p>
			</div>
			<div class="form-group form-group-edit">
				<label>职位&nbsp;</label>
				<div class="btn-group">
					<button class="btn btn-default" type="button" id="position-value"><?php echo ($personData["position_name"]); ?></button>
					<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu" id="position-list">
						<?php if(is_array($position)): $i = 0; $__LIST__ = $position;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li index="<?php echo ($value["id"]); ?>"><a href="javascript:;"><?php echo ($value["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</div>
				<p class="help-block"></p>
			</div>
			<div class="page-header"><h2>汇报关系 <small class="small-font-size"></small></h2></div>
			<div class="form-group" id="report">
				<?php if(is_array($departmentList)): $i = 0; $__LIST__ = $departmentList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><div class="page-header page-header-department"><h4><kbd><?php echo ($value["name"]); ?>:</kbd></h4></div>
					<?php if(is_array($alluser)): $i = 0; $__LIST__ = $alluser;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(($vo["department"]) == $value["id"]): ?><!-- 部门分组 -->
							<?php if(($personData["report"]) == $vo["id"]): ?><!-- 如果当前report等于汇报人的id则选中 -->
								<?php if(($vo["id"]) != $personData["id"]): ?><!-- 无法选中自己（避免自己跟自己汇报） -->
									<span class="name-box active" index="<?php echo ($vo["id"]); ?>"><?php echo ($vo["nickname"]); ?></span><?php endif; ?>
							<?php else: ?>
								<?php if(($vo["id"]) != $personData["id"]): ?><span class="name-box" index="<?php echo ($vo["id"]); ?>"><?php echo ($vo["nickname"]); ?></span><?php endif; endif; endif; endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
			</div>
			<div class="page-header"><h2>状态 <small class="small-font-size"></small></h2></div>
			<div class="btn-group">
				<button class="btn btn-default" type="button" id="state-value">
					<?php switch($personData["state"]): case "1": ?>启用<?php break;?>
						<?php case "2": ?>禁用<?php break;?>
						<?php case "3": ?>离职<?php break; endswitch;?>
				</button>
				<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu" id="state-list">
					<li index="1" state="1"><a href="javascript:;">启用</a></li>
					<li index="2" state="2"><a href="javascript:;">禁用</a></li>
					<li index="3" state="3"><a href="javascript:;">离职</a></li>
				</ul>
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
						<?php if(($value["title"]) != "系统"): if(in_array(($value["id"]), is_array($authgroup["rules"])?$authgroup["rules"]:explode(',',$authgroup["rules"]))): ?><input type="checkbox" name="permissions[]" checked="checked" value="<?php echo ($value["id"]); ?>"><?php echo ($value["title"]); ?>
						<?php else: ?>
							<input type="checkbox" name="permissions[]" value="<?php echo ($value["id"]); ?>"><?php echo ($value["title"]); endif; endif; ?>
					</label><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
			<div class="form-group form-group-submit">
				<input type="hidden" name="id" value="<?php echo ($_GET['id']); ?>" id="user-id">
				<input type="hidden" name="department" value="<?php echo ($personData["department"]); ?>" id="department-id">
				<input type="hidden" name="position" value="<?php echo ($personData["position"]); ?>" id="position-id">
				<input type="hidden" name="report" value="<?php if(($personData["report"]) != ""): echo ($personData["report"]); endif; ?>" id="report-id">
				<input type="hidden" name="level" value="<?php echo ($personData["level"]); ?>" id="level-id">
				<input type="hidden" name="state" value="<?php echo ($personData["state"]); ?>" id="state-id">
				<input type="submit" class="btn btn-primary btn-lg input-reset" value="修改">
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

	<!-- 重置密码模态框 -->
	<div class="modal fade bs-example-modal-sm" id="modal-message" tabindex="-1" role="modal" aria-labelledby="addUserModal" aria-hidden="true">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">提示</h4>
				</div>
				<div class="modal-body">
					<p class="modal-message modal-message-info">
						<i class="icon-info-sign icon-2x modal-icon-info"></i>
						<span class="message-text">密码重置成功</span>
					</p>
					<p class="modal-message modal-message-new-password">
						<span class="message-text">新密码为：<b id="newPassword"></b></span>
					</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" data-dismiss="modal">关闭</button>
				</div>
			</div>
		</div>
	</div>

	<div class="loading-modal sr-only" id="loading">
		<div class="loading-icon">
			<p><i class="icon-spinner icon-spin icon-2x"></i></p>
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