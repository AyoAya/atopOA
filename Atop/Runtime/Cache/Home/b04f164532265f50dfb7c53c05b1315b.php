<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>

	<meta charset="UTF-8">
	<!--<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">-->
	<link href="/Public/Home/img/favicon.ico" rel="shortcut icon">

	
	<title>样品详情</title>
<!-- 网站标题 -->
	<link rel="stylesheet" href="/Public/layui/css/layui.css">
	<link rel="stylesheet" href="/Public/Home/css/bootstrap.min.css">
	<link rel="stylesheet" href="/Public/Home/css/font-awesome.min.css">
	<link rel="stylesheet" href="/Public/Home/css/jquery.mCustomScrollbar.css">
	<link rel="stylesheet" href="/Public/Home/css/iconfont.css">
	<link rel="stylesheet" href="/Public/Home/css/basic.css">
	<link rel="stylesheet" href="/Public/Home/css/<?php echo ($face["theme"]); ?>/index.css">
	<link rel="stylesheet" href="/Public/Home/css/promotion_index.css">

	
    <link rel="stylesheet" href="/Public/Home/css/jquery.toastmessage.css">
    <link rel="stylesheet" href="/Public/Huploadify/Huploadify.css">
	<link rel="stylesheet" href="/Public/Home/css/bootstrap-datetimepicker.min.css">
	<link rel="stylesheet" href="/Public/Home/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="/Public/Home/css/bs-is-fun.css">
	<link rel="stylesheet" href="/Public/Home/css/sample-details.css">
<!-- 引入css文件 -->
	<script src="/Public/Home/js/jquery.min.js"></script>
	<script src="/Public/Home/js/bootstrap.min.js"></script>
	<script src="/Public/layui/layui.js"></script>
	<script src="/Public/Home/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/Public/Home/js/index.js"></script>

	
	<!--<script src="/Public/home/uploadify/jquery.uploadify.min.js"></script>-->
	<script src="/Public/Huploadify/jquery.Huploadify.js"></script>
	<script src="/Public/Home/js/bootstrap-datetimepicker.js"></script>
	<script src="/Public/Home/js/bootstrap-datetimepicker.zh-CN.js"></script>
	<script src="/Public/Home/js/jquery.toastmessage.js"></script>
	<script src="/Public/Home/js/jquery.validate.min.js"></script>
	<script src="/Public/Home/js/jquery.form.js"></script>
	<script src="/Public/Home/js/sample.js"></script>
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
						<p>ATOP Corporation</p>
						<p>Copyright &copy; 2016</p>
					</div>

				</div>
			</div>
		

		<!-- 二级导航及用户信息/常用操作区 -->
		<div id="header">
			
	<ol class="breadcrumb breadcrumb-edit">
		<li><a href="/Sample">样品管理</a></li>
		<li class="active">样品详情</li>
	</ol>
	<!-- <div class="btn-group pull-right btn-group-edit" role="group">
		<a href="/index.php/Home/Sample/addCustomer" class="btn btn-primary">
			<span class="glyphicon glyphicon-plus"></span> 新增样品
		</a>
	</div> -->

			<div class="user-operation-box pull-right">
				<div class="user-operation-item pull-left">
					<a href="/notice"><i class="icon-bell"></i>&nbsp;&nbsp;&nbsp;通知</a>
				</div>
				<div class="user-operation-item pull-left user-options-btn">
					<a href="/index.php/Home/Center">
						<i class="layui-icon">&#xe612;</i>&nbsp;&nbsp;&nbsp;<?php echo ($_SESSION['user']['nickname']); ?>&nbsp;&nbsp;&nbsp;<i class="icon-caret-down"></i>
					</a>
				</div>
				<div class="clearfix"></div>

				<!--<div class="user-operation-item pull-left">
					<a href="/Logout"><i class="icon-signout"></i>&nbsp;&nbsp;退出</a>
				</div>-->
			</div>
			<div class="clearfix"></div>
		</div>


		<!-- 用户选项 -->
		<div class="user-options">
			<ul>
				<li><a href="#"><i class="icon-pencil"></i>&nbsp;&nbsp;修改资料</a></li>
				<li><a href="#"><i class="icon-github-alt"></i>&nbsp;&nbsp;修改头像</a></li>
				<li><a href="#"><i class="icon-signout"></i>&nbsp;&nbsp;退出登录</a></li>
			</ul>
		</div>

		<!-- 正文区域 -->
		<div id="content">
			<div class="container-fluid" id="content-box">
				

	<div id="warp">
		<!-- 审核状态提示 -->
		<!--<?php if(($sample["s_status"]) == "2"): if(!empty($sample["s_comment"]["error"]["0"]["message"])): ?><div class="alert alert-warning alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<p class="alert-text"><strong><i class="icon-info-sign icon-2x iconFontAwesome"></i>审核 [ 未通过 ]：</strong> <?php echo ($sample["s_comment"]["error"]["0"]["message"]); ?></p>
				</div><?php endif; endif; ?>
		<?php if(($sample["s_status"]) == "1"): if(!empty($sample["s_comment"]["success"]["0"]["message"])): ?><div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<p class="alert-text"><strong><i class="icon-ok-sign icon-2x iconFontAwesome"></i>审核 [ 通过 ]：</strong> <?php echo ($sample["s_comment"]["success"]["0"]["message"]); ?></p>
				</div><?php endif; endif; ?>-->
		<!-- 物料准备状态提示 -->
		<!--<?php if(($sample["w_status"]) == "2"): if(!empty($sample["w_comment"]["error"]["0"]["message"])): ?><div class="alert alert-warning alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<p class="alert-text"><strong><i class="icon-info-sign icon-2x iconFontAwesome"></i>物料准备 [ 未完成 ]：</strong> <?php echo ($sample["w_comment"]["error"]["0"]["message"]); ?></p>
				</div><?php endif; endif; ?>
		<?php if(($sample["w_status"]) == "1"): if(!empty($sample["w_comment"]["success"]["0"]["message"])): ?><div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<p class="alert-text"><strong><i class="icon-ok-sign icon-2x iconFontAwesome"></i>物料准备 [ 已完成 ]：</strong> <?php echo ($sample["w_comment"]["success"]["0"]["message"]); ?></p>
				</div><?php endif; endif; ?>-->
		<!-- 样品制作状态提示 -->
		<!--<?php if(($sample["c_status"]) == "2"): if(!empty($sample["c_comment"]["error"]["0"]["message"])): ?><div class="alert alert-warning alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<p class="alert-text"><strong><i class="icon-info-sign icon-2x iconFontAwesome"></i>样品制作 [ 未完成 ]：</strong> <?php echo ($sample["c_comment"]["error"]["0"]["message"]); ?></p>
				</div><?php endif; endif; ?>
		<?php if(($sample["c_status"]) == "1"): if(!empty($sample["c_comment"]["success"]["0"]["message"])): ?><div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<p class="alert-text"><strong><i class="icon-ok-sign icon-2x iconFontAwesome"></i>样品制作 [ 已完成 ]：</strong> <?php echo ($sample["c_comment"]["success"]["0"]["message"]); ?></p>
				</div><?php endif; endif; ?>-->
		<!-- 样品测试状态提示 -->
		<!--<?php if(($sample["y_status"]) == "2"): if(!empty($sample["y_comment"]["error"]["0"]["message"])): ?><div class="alert alert-warning alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<p class="alert-text"><strong><i class="icon-info-sign icon-2x iconFontAwesome"></i>样品测试 [ 未通过 ]：</strong> <?php echo ($sample["y_comment"]["error"]["0"]["message"]); ?></p>
				</div><?php endif; endif; ?>
		<?php if(($sample["y_status"]) == "1"): if(!empty($sample["y_comment"]["success"]["0"]["message"])): ?><div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<p class="alert-text"><strong><i class="icon-ok-sign icon-2x iconFontAwesome"></i>样品测试 [ 已通过 ]：</strong> <?php echo ($sample["y_comment"]["success"]["0"]["message"]); ?></p>
				</div><?php endif; endif; ?>-->

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">基本信息</h4>
            </div>
            <table class="table table-font-size">
                <thead>
                <tr>
					<th>当前进度</th>
                    <th>样品单号</th>
                    <th>产品类型</th>
                    <th>产品型号</th>
                    <th>产品经理</th>
                    <th>模块数量</th>
                    <th>客户名称</th>
                    <th>设备品牌</th>
                    <th>设备型号</th>
                </tr>
                </thead>
                <tbody>
                <tr>
					<td class="<?php if(($sample["color"]) == "0"): ?>do<?php endif; if(($sample["color"]) == "2"): ?>no<?php endif; if(($sample["color"]) == "3"): ?>yes<?php endif; ?>"><span><?php echo ($sample["state"]); ?></span></td>
                    <td><a href="/sampleOverview/<?php echo ($sample["totalorder"]); ?>"><?php echo ($sample["totalorder"]); ?></a></td>
                    <td><?php echo ($sample["type"]); ?></td>
                    <td><?php echo ($sample["product"]); ?></td>
                    <td><?php echo ($sample["manager"]); ?></td>
                    <td><?php echo ($sample["number"]); ?></td>
                    <td><?php echo ($sample["customer"]); ?></td>
                    <td><?php echo ($sample["brand"]); ?></td>
                    <td><?php echo ($sample["model"]); ?></td>
                </tr>
                </tbody>
            </table>
        </div>

		<!-- 样品步骤 -->
		<div class="header">
			<!--<div class="chapter">
				<p><?php echo ($sample["state"]); ?></p>
			</div>-->
			<ul class="nav nav-pills nav-justified step step-progress operation-log">
				<li class="active">
					<a>下单<span class="caret"></span></a>
					<div class="round">
						<span class="active">
							<div class="instructions">
								<span class="caret"></span>
								<p>下单人：<?php echo ($sample["nickname"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$sample["createtime"])); ?></small></p>
							</div>
						</span>
					</div>
				</li>
				<li class="<?php if(($sample["s_status"]) == "1"): ?>active<?php endif; if(($sample["s_status"]) == "2"): ?>error<?php endif; ?>">
					<a>审核
						<?php if(!empty($sample["s_comment"])): ?><!-- 成功后消息存在并且不为空时显示 -->
							<?php if(isset($sample["s_comment"]["success"])): if(!isset($sample["s_comment"]["save"])): if(!empty($sample["s_comment"]["success"])): ?><span class="log-save-icon-audit operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
							<!-- 失败后消息存在并且不为空时显示 -->
							<?php if(isset($sample["s_comment"]["error"])): if(!isset($sample["s_comment"]["save"])): if(!empty($sample["s_comment"]["error"])): ?><span class="log-save-icon-audit operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
							<!-- 更新日志后消息存在并且不为空时显示 -->
							<?php if(isset($sample["s_comment"]["save"])): if(!empty($sample["s_comment"]["save"])): ?><span class="log-save-icon-audit operation-log-color">
										<i class="icon-info-sign"></i>
									</span><?php endif; endif; ?>
							<div class="sr-only audit-operation-log">
								<ul>
                                    <?php if(($sample["s_status"]) == "2"): if(!empty($sample["s_comment"]["error"]["0"]["message"])): ?><li><strong class="text-danger"><p><i class="icon-remove-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$sample["s_comment"]["error"]["0"]["time"])); ?>] <?php echo ($sample["s_comment"]["error"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
                                    <?php if(($sample["s_status"]) == "1"): if(!empty($sample["s_comment"]["success"]["0"]["message"])): ?><li><strong class="text-success"><p><i class="icon-ok-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$sample["s_comment"]["success"]["0"]["time"])); ?>] <?php echo ($sample["s_comment"]["success"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
									<?php if(is_array($sample["s_comment"]["save"])): $i = 0; $__LIST__ = $sample["s_comment"]["save"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
											<p><i class="icon-minus-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$vo["time"])); ?>] <?php echo ($vo["message"]); ?></p>
										</li><?php endforeach; endif; else: echo "" ;endif; ?>
								</ul>
							</div><?php endif; ?>
						<span class="caret"></span>
					</a>
					<?php if(in_array(($sample["s_status"]), explode(',',"1,2"))): ?><div class="round">
							<span class="<?php if(($sample["s_status"]) == "1"): ?>active<?php endif; if(($sample["s_status"]) == "2"): ?>error<?php endif; ?>">
								<div class="instructions">
									<span class="caret"></span>
									<p>审核人：<?php echo ($sample["a_name"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$sample["s_time"])); ?></small></p>
								</div>
							</span>
						</div><?php endif; ?>
				</li>
				<li class="<?php if(($sample["w_status"]) == "1"): ?>active<?php endif; if(($sample["w_status"]) == "2"): ?>error<?php endif; ?>">
					<a>物料准备
						<?php if(!empty($sample["w_comment"])): ?><!-- 成功后消息存在并且不为空时显示 -->
							<?php if(isset($sample["w_comment"]["success"])): if(!isset($sample["w_comment"]["save"])): if(!empty($sample["w_comment"]["success"])): ?><span class="log-save-icon-metarial operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
							<!-- 失败后消息存在并且不为空时显示 -->
							<?php if(isset($sample["w_comment"]["error"])): if(!isset($sample["w_comment"]["save"])): if(!empty($sample["w_comment"]["error"])): ?><span class="log-save-icon-metarial operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
							<!-- 更新日志后消息存在并且不为空时显示 -->
							<?php if(isset($sample["w_comment"]["save"])): if(!empty($sample["w_comment"]["save"])): ?><span class="log-save-icon-metarial operation-log-color">
										<i class="icon-info-sign"></i>
									</span><?php endif; endif; ?>
							<div class="sr-only metarial-operation-log">
								<ul>
                                    <?php if(($sample["w_status"]) == "2"): if(!empty($sample["w_comment"]["error"]["0"]["message"])): ?><li><strong class="text-danger"><p><i class="icon-remove-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$sample["w_comment"]["error"]["0"]["time"])); ?>] <?php echo ($sample["w_comment"]["error"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
                                    <?php if(($sample["w_status"]) == "1"): if(!empty($sample["w_comment"]["success"]["0"]["message"])): ?><li><strong class="text-success"><p><i class="icon-ok-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$sample["w_comment"]["success"]["0"]["time"])); ?>] <?php echo ($sample["w_comment"]["success"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
									<?php if(is_array($sample["w_comment"]["save"])): $i = 0; $__LIST__ = $sample["w_comment"]["save"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
											<p><i class="icon-minus-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$vo["time"])); ?>] <?php echo ($vo["message"]); ?></p>
										</li><?php endforeach; endif; else: echo "" ;endif; ?>
								</ul>
							</div><?php endif; ?>
						<span class="caret"></span></a>
					<?php if(in_array(($sample["w_status"]), explode(',',"1,2"))): ?><div class="round">
							<span class="<?php if(($sample["w_status"]) == "1"): ?>active<?php endif; if(($sample["w_status"]) == "2"): ?>error<?php endif; ?>">
								<div class="instructions">
									<span class="caret"></span>
									<p>处理人：<?php echo ($sample["w_name"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$sample["w_time"])); ?></small></p>
								</div>
							</span>
						</div><?php endif; ?>
				</li>
				<li class="<?php if(($sample["c_status"]) == "1"): ?>active<?php endif; if(($sample["c_status"]) == "2"): ?>error<?php endif; ?>">
					<a>样品制作
						<?php if(!empty($sample["c_comment"])): ?><!-- 成功后消息存在并且不为空时显示 -->
							<?php if(isset($sample["c_comment"]["success"])): if(!isset($sample["c_comment"]["save"])): if(!empty($sample["c_comment"]["success"])): ?><span class="log-save-icon-productEngineer operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
							<!-- 失败后消息存在并且不为空时显示 -->
							<?php if(isset($sample["c_comment"]["error"])): if(!isset($sample["c_comment"]["save"])): if(!empty($sample["c_comment"]["error"])): ?><span class="log-save-icon-productEngineer operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
							<!-- 更新日志后消息存在并且不为空时显示 -->
							<?php if(isset($sample["c_comment"]["save"])): if(!empty($sample["c_comment"]["save"])): ?><span class="log-save-icon-productEngineer operation-log-color">
										<i class="icon-info-sign"></i>
									</span><?php endif; endif; ?>
							<div class="sr-only productEngineer-operation-log">
								<ul>
                                    <?php if(($sample["c_status"]) == "2"): if(!empty($sample["c_comment"]["error"]["0"]["message"])): ?><li><strong class="text-danger"><p><i class="icon-remove-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$sample["c_comment"]["error"]["0"]["time"])); ?>] <?php echo ($sample["c_comment"]["error"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
                                    <?php if(($sample["c_status"]) == "1"): if(!empty($sample["c_comment"]["success"]["0"]["message"])): ?><li><strong class="text-success"><p><i class="icon-ok-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$sample["c_comment"]["success"]["0"]["time"])); ?>] <?php echo ($sample["c_comment"]["success"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
									<?php if(is_array($sample["c_comment"]["save"])): $i = 0; $__LIST__ = $sample["c_comment"]["save"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
											<p><i class="icon-minus-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$vo["time"])); ?>] <?php echo ($vo["message"]); ?></p>
										</li><?php endforeach; endif; else: echo "" ;endif; ?>
								</ul>
							</div><?php endif; ?>
						<span class="caret"></span></a>
					<?php if(in_array(($sample["c_status"]), explode(',',"1,2"))): ?><div class="round">
							<span class="<?php if(($sample["c_status"]) == "1"): ?>active<?php endif; if(($sample["c_status"]) == "2"): ?>error<?php endif; ?>">
								<div class="instructions">
									<span class="caret"></span>
									<p>制作人：<?php echo ($sample["c_name"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$sample["c_time"])); ?></small></p>
								</div>
							</span>
						</div><?php endif; ?>
				</li>
				<li class="<?php if(($sample["y_status"]) == "1"): ?>active<?php endif; if(($sample["y_status"]) == "2"): ?>error<?php endif; ?>">
					<a>样品测试
                        <?php if(isset($sample["y_comment"])): if(!empty($sample["y_comment"])): ?><!-- 成功后消息存在并且不为空时显示 -->
								<?php if(isset($sample["y_comment"]["success"])): if(!isset($sample["y_comment"]["save"])): if(!empty($sample["y_comment"]["success"])): ?><span class="log-save-icon-testEngineer operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
								<!-- 失败后消息存在并且不为空时显示 -->
								<?php if(isset($sample["y_comment"]["error"])): if(!isset($sample["y_comment"]["save"])): if(!empty($sample["y_comment"]["error"])): ?><span class="log-save-icon-testEngineer operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
								<!-- 更新日志后消息存在并且不为空时显示 -->
								<?php if(isset($sample["y_comment"]["save"])): if(!empty($sample["y_comment"]["save"])): ?><span class="log-save-icon-testEngineer operation-log-color">
										<i class="icon-info-sign"></i>
									</span><?php endif; endif; ?>
								<div class="sr-only testEngineer-operation-log">
									<ul>
										<?php if(($sample["y_status"]) == "2"): if(!empty($sample["y_comment"]["error"]["0"]["message"])): ?><li><strong class="text-danger"><p><i class="icon-remove-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$sample["y_comment"]["error"]["0"]["time"])); ?>] <?php echo ($sample["y_comment"]["error"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
										<?php if(($sample["y_status"]) == "1"): if(!empty($sample["y_comment"]["success"]["0"]["message"])): ?><li><strong class="text-success"><p><i class="icon-ok-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$sample["y_comment"]["success"]["0"]["time"])); ?>] <?php echo ($sample["y_comment"]["success"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
										<?php if(is_array($sample["y_comment"]["save"])): $i = 0; $__LIST__ = $sample["y_comment"]["save"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
												<p><i class="icon-minus-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$vo["time"])); ?>] <?php echo ($vo["message"]); ?></p>
											</li><?php endforeach; endif; else: echo "" ;endif; ?>
									</ul>
								</div><?php endif; endif; ?>
                        <span class="caret"></span></a>
                    <?php if(in_array(($sample["y_status"]), explode(',',"1,2"))): ?><div class="round">
							<span class="<?php if(($sample["y_status"]) == "1"): ?>active<?php endif; if(($sample["y_status"]) == "2"): ?>error<?php endif; ?>">
								<div class="instructions">
									<span class="caret"></span>
									<p>测试人：<?php echo ($sample["y_name"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$sample["y_time"])); ?></small></p>
								</div>
							</span>
                        </div><?php endif; ?>
				</li>
				<li class="<?php if(($sample["f_status"]) == "1"): ?>active<?php endif; if(($sample["f_status"]) == "2"): ?>error<?php endif; ?>">
					<a>发货
						<?php if(isset($sample["f_comment"])): if(!empty($sample["f_comment"])): ?><!-- 成功后消息存在并且不为空时显示 -->
								<?php if(isset($sample["f_comment"]["success"])): if(!isset($sample["f_comment"]["save"])): if(!empty($sample["f_comment"]["success"])): ?><span class="log-save-icon-delivery operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
								<!-- 失败后消息存在并且不为空时显示 -->
								<?php if(isset($sample["f_comment"]["error"])): if(!isset($sample["f_comment"]["save"])): if(!empty($sample["f_comment"]["error"])): ?><span class="log-save-icon-delivery operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
								<!-- 更新日志后消息存在并且不为空时显示 -->
								<?php if(isset($sample["f_comment"]["save"])): if(!empty($sample["f_comment"]["save"])): ?><span class="log-save-icon-delivery operation-log-color">
										<i class="icon-info-sign"></i>
									</span><?php endif; endif; ?>
								<div class="sr-only delivery-operation-log">
									<ul>
										<?php if(($sample["f_status"]) == "2"): if(!empty($sample["f_comment"]["error"]["0"]["message"])): ?><li><strong class="text-danger"><p><i class="icon-remove-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$sample["f_comment"]["error"]["0"]["time"])); ?>] <?php echo ($sample["f_comment"]["error"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
										<?php if(($sample["f_status"]) == "1"): if(!empty($sample["f_comment"]["success"]["0"]["message"])): ?><li><strong class="text-success"><p><i class="icon-ok-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$sample["f_comment"]["success"]["0"]["time"])); ?>] <?php echo ($sample["f_comment"]["success"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
										<?php if(is_array($sample["f_comment"]["save"])): $i = 0; $__LIST__ = $sample["f_comment"]["save"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
												<p><i class="icon-minus-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$vo["time"])); ?>] <?php echo ($vo["message"]); ?></p>
											</li><?php endforeach; endif; else: echo "" ;endif; ?>
									</ul>
								</div><?php endif; endif; ?>
						<span class="caret"></span></a>
					<?php if(in_array(($sample["f_status"]), explode(',',"1,2"))): ?><div class="round">
							<span class="<?php if(($sample["f_status"]) == "1"): ?>active<?php endif; if(($sample["f_status"]) == "2"): ?>error<?php endif; ?>">
								<div class="instructions">
									<span class="caret"></span>
									<p>发货人：<?php echo ($sample["f_name"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$sample["f_time"])); ?></small></p>
								</div>
							</span>
						</div><?php endif; ?>
				</li>
				<li class="<?php if(($sample["k_status"]) == "1"): ?>active<?php endif; if(($sample["k_status"]) == "2"): ?>error<?php endif; ?>">
					<a>反馈
						<?php if(isset($sample["k_comment"])): if(!empty($sample["k_comment"])): ?><!-- 成功后消息存在并且不为空时显示 -->
								<?php if(isset($sample["k_comment"]["success"])): if(!isset($sample["k_comment"]["save"])): if(!empty($sample["k_comment"]["success"])): ?><span class="log-save-icon-feedback operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
								<!-- 失败后消息存在并且不为空时显示 -->
								<?php if(isset($sample["k_comment"]["error"])): if(!isset($sample["k_comment"]["save"])): if(!empty($sample["k_comment"]["error"])): ?><span class="log-save-icon-feedback operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
								<!-- 更新日志后消息存在并且不为空时显示 -->
								<?php if(isset($sample["k_comment"]["save"])): if(!empty($sample["k_comment"]["save"])): ?><span class="log-save-icon-feedback operation-log-color">
										<i class="icon-info-sign"></i>
									</span><?php endif; endif; ?>
								<div class="sr-only feedback-operation-log">
									<ul>
										<?php if(($sample["k_status"]) == "2"): if(!empty($sample["k_comment"]["error"]["0"]["message"])): ?><li><strong class="text-danger"><p><i class="icon-remove-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$sample["k_comment"]["error"]["0"]["time"])); ?>] <?php echo ($sample["k_comment"]["error"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
										<?php if(($sample["k_status"]) == "1"): if(!empty($sample["k_comment"]["success"]["0"]["message"])): ?><li><strong class="text-success"><p><i class="icon-ok-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$sample["k_comment"]["success"]["0"]["time"])); ?>] <?php echo ($sample["k_comment"]["success"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
										<?php if(is_array($sample["k_comment"]["save"])): $i = 0; $__LIST__ = $sample["k_comment"]["save"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
												<p><i class="icon-minus-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$vo["time"])); ?>] <?php echo ($vo["message"]); ?></p>
											</li><?php endforeach; endif; else: echo "" ;endif; ?>
									</ul>
								</div><?php endif; endif; ?>
						<span class="caret"></span></a>
					<?php if(in_array(($sample["k_status"]), explode(',',"1,2"))): ?><div class="round">
							<span class="<?php if(($sample["k_status"]) == "1"): ?>active<?php endif; if(($sample["k_status"]) == "2"): ?>error<?php endif; ?>">
								<div class="instructions">
									<span class="caret"></span>
									<p>反馈人：<?php echo ($sample["k_name"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$sample["k_time"])); ?></small></p>
								</div>
							</span>
						</div><?php endif; ?>
				</li>
			</ul>
		</div>

        <!-- 审核状态 -->
		<?php if($sample["s_status"] == '' || $sample["s_status"] == 3 || $sample["s_status"] == 2 AND $sample["w_status"] == '' AND $sample["c_status"] == '' AND $sample["y_status"] == '' AND $sample["f_status"] == ''): ?><div class="row">
				<div class="col-lg-12">
					<div class="col-lg-6 col-align">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">最新日志<a href="/sampleLog/<?php echo ($sample["order"]); ?>" class="pull-right alllog"><i class="icon-bookmark-empty">&nbsp;</i>所有日志</a></h4>
							</div>
							<div class="panel-body panel-body-min-height">
								<div>
									<p><span class="new-log">
										<?php if(!empty($lastLog)): echo (date("Y-m-d H:i:s",$lastLog["time"])); ?>　【<?php echo ($lastLog["manager"]); ?>】<?php echo ($lastLog["message"]); endif; ?>
									</span></p>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-align">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">交期</h4>
							</div>
							<div class="panel-body panel-body-margin">
								<span>要求交期：<?php echo ($sample["d_date"]); ?></span><?php if(!empty($sample["e_date"])): ?><span class="margin-span">预计交期：<?php echo ($sample["e_date"]); ?></span><?php endif; if(!empty($sample["a_date"])): ?><span class="margin-span">实际交期：<?php echo ($sample["a_date"]); ?></span><?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div><?php endif; ?>
        <!-- 物料准备状态 -->
        <?php if($sample["s_status"] == 1 AND $sample["w_status"] == '' || $sample["w_status"] == 3 || $sample["w_status"] == 2 AND $sample["c_status"] == '' AND $sample["y_status"] == '' AND $sample["f_status"] == ''): ?><div class="row">
				<div class="col-lg-12">
					<div class="col-lg-6 col-align">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">最新日志<a href="/sampleLog/<?php echo ($sample["order"]); ?>" class="pull-right alllog"><i class="icon-bookmark-empty">&nbsp;</i>所有日志</a></h4>
							</div>
							<div class="panel-body panel-body-min-height">
								<div>
									<p><span class="new-log">
										<?php if(!empty($lastLog)): echo (date("Y-m-d H:i:s",$lastLog["time"])); ?>　【<?php echo ($lastLog["manager"]); ?>】<?php echo ($lastLog["message"]); endif; ?>
									</span></p>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-align">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">交期</h4>
							</div>
							<div class="panel-body panel-body-margin">
								<span>要求交期：<?php echo ($sample["d_date"]); ?></span><?php if(!empty($sample["e_date"])): ?><span class="margin-span">预计交期：<?php echo ($sample["e_date"]); ?></span><?php endif; if(!empty($sample["a_date"])): ?><span class="margin-span">实际交期：<?php echo ($sample["a_date"]); ?></span><?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div><?php endif; ?>
        <!-- 样品制作状态 -->
        <?php if($sample["s_status"] == 1 AND $sample["w_status"] == 1 AND $sample["c_status"] == '' || $sample["c_status"] == 3 || $sample["c_status"] == 2 AND $sample["y_status"] == '' AND $sample["f_status"] == ''): ?><div class="row">
				<div class="col-lg-12">
					<div class="col-lg-6 col-align">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">最新日志<a href="/sampleLog/<?php echo ($sample["order"]); ?>" class="pull-right alllog"><i class="icon-bookmark-empty">&nbsp;</i>所有日志</a></h4>
							</div>
							<div class="panel-body panel-body-min-height">
								<div>
									<p><span class="new-log">
										<?php if(!empty($lastLog)): echo (date("Y-m-d H:i:s",$lastLog["time"])); ?>　【<?php echo ($lastLog["manager"]); ?>】<?php echo ($lastLog["message"]); endif; ?>
									</span></p>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-align">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">交期</h4>
							</div>
							<div class="panel-body panel-body-margin">
								<span>要求交期：<?php echo ($sample["d_date"]); ?></span><?php if(!empty($sample["e_date"])): ?><span class="margin-span">预计交期：<?php echo ($sample["e_date"]); ?></span><?php endif; if(!empty($sample["a_date"])): ?><span class="margin-span">实际交期：<?php echo ($sample["a_date"]); ?></span><?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div><?php endif; ?>
		<!-- 样品测试状态 -->
		<?php if($sample["s_status"] == 1 AND $sample["w_status"] == 1 AND $sample["c_status"] == 1 AND $sample["y_status"] == '' OR $sample["y_status"] == 3 OR $sample["y_status"] == 2 AND $sample["f_status"] == ''): ?><div class="row">
				<div class="col-lg-12">
					<div class="col-lg-6 col-align">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">最新日志<a href="/sampleLog/<?php echo ($sample["order"]); ?>" class="pull-right alllog"><i class="icon-bookmark-empty">&nbsp;</i>所有日志</a></h4>
							</div>
							<div class="panel-body panel-body-min-height">
								<div>
									<p><span class="new-log">
										<?php if(!empty($lastLog)): echo (date("Y-m-d H:i:s",$lastLog["time"])); ?>　【<?php echo ($lastLog["manager"]); ?>】<?php echo ($lastLog["message"]); endif; ?>
									</span></p>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-align">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">交期</h4>
							</div>
							<div class="panel-body panel-body-margin">
								<span>要求交期：<?php echo ($sample["d_date"]); ?></span><?php if(!empty($sample["e_date"])): ?><span class="margin-span">预计交期：<?php echo ($sample["e_date"]); ?></span><?php endif; if(!empty($sample["a_date"])): ?><span class="margin-span">实际交期：<?php echo ($sample["a_date"]); ?></span><?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div><?php endif; ?>
		<!-- 发货状态 -->
		<?php if($sample["s_status"] == 1 AND $sample["w_status"] == 1 AND $sample["c_status"] == 1 AND $sample["y_status"] == 1 AND $sample["f_status"] == '' || $sample["f_status"] == 3 || $sample["f_status"] == 2): ?><div class="row">
				<div class="col-lg-12">
					<div class="col-lg-6 col-align">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">最新日志<a href="/sampleLog/<?php echo ($sample["order"]); ?>" class="pull-right alllog"><i class="icon-bookmark-empty">&nbsp;</i>所有日志</a></h4>
							</div>
							<div class="panel-body panel-body-min-height">
								<div>
									<p><span class="new-log">
										<?php if(!empty($lastLog)): echo (date("Y-m-d H:i:s",$lastLog["time"])); ?>　【<?php echo ($lastLog["manager"]); ?>】<?php echo ($lastLog["message"]); endif; ?>
									</span></p>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-align">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">交期</h4>
							</div>
							<div class="panel-body panel-body-margin">
								<span>要求交期：<?php echo ($sample["d_date"]); ?></span><?php if(!empty($sample["e_date"])): ?><span class="margin-span">预计交期：<?php echo ($sample["e_date"]); ?></span><?php endif; if(!empty($sample["a_date"])): ?><span class="margin-span">实际交期：<?php echo ($sample["a_date"]); ?></span><?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div><?php endif; ?>
		<!-- 反馈状态 -->
		<?php if($sample["s_status"] == 1 AND $sample["w_status"] == 1 AND $sample["c_status"] == 1 AND $sample["y_status"] == 1 AND $sample["f_status"] == 1 AND $sample["k_status"] == '' || $sample["k_status"] == 3 || $sample["k_status"] == 2 || $sample["k_status"] == 1): ?><div class="row">
				<div class="col-lg-12">
					<div class="col-lg-6 col-align">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">最新日志<a href="/sampleLog/<?php echo ($sample["order"]); ?>" class="pull-right alllog"><i class="icon-bookmark-empty">&nbsp;</i>所有日志</a></h4>
							</div>
							<div class="panel-body panel-body-min-height">
								<div>
									<p><span class="new-log">
										<?php if(!empty($lastLog)): echo (date("Y-m-d H:i:s",$lastLog["time"])); ?>　【<?php echo ($lastLog["manager"]); ?>】<?php echo ($lastLog["message"]); endif; ?>
									</span></p>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-align">
						<div class="panel panel-default">
							<div class="panel-heading delivery-date-state">
								<h4 class="panel-title">交期<?php if(isset($deliveryDateError)): ?><span class="label label-danger pull-right"><?php echo ($deliveryDateError); ?></span><?php endif; if(isset($deliveryDateSuccess)): ?><span class="label label-success pull-right"><?php echo ($deliveryDateSuccess); ?></span><?php endif; ?></h4>
							</div>
							<div class="panel-body panel-body-margin">
								<span>要求交期：<?php echo ($sample["d_date"]); ?></span><?php if(!empty($sample["e_date"])): ?><span class="margin-span">预计交期：<?php echo ($sample["e_date"]); ?></span><?php endif; if(!empty($sample["a_date"])): ?><span class="margin-span">实际交期：<?php echo (date("Y-m-d",$sample["a_date"])); ?></span><?php endif; ?>
							</div>
						</div>
					</div>
				</div>
			</div><?php endif; ?>

        <!-- 备注 -->
		<?php if(!empty($sample["awb"])): ?><div class="row">
				<div class="col-lg-12">
					<div class="col-lg-6 col-align">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">物流信息<a class="alllog pull-right" href="/sampleLogistics/<?php echo ($sample["order"]); ?>"><i class="icon-truck">&nbsp;</i>物流状态</a></h4>
							</div>
							<div class="panel-body">
								<p>
									物流公司：
									<?php switch($sample["logistics"]): case "shunfeng": ?>顺丰物流<?php break;?>
										<?php case "dhl": ?>DHL<?php break;?>
										<?php case "fedex": ?>FedEx<?php break;?>
										<?php case "ups": ?>UPS<?php break;?>
										<?php case "shentong": ?>申通快递<?php break;?>
										<?php case "yuantong": ?>圆通速递<?php break;?>
										<?php case "zhongtong": ?>中通速递<?php break; endswitch;?>
									<span class="awb">
										运单号：<a href="/sampleLogistics/<?php echo ($sample["order"]); ?>"><?php echo ($sample["awb"]); ?></a>
									</span>
								</p>
							</div>
						</div>
					</div>
					<div class="col-lg-6 col-align">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">产品备注</h4>
							</div>
							<div class="panel-body panel-body-min-height"><?php echo ($sample["comment"]); ?></div>
						</div>
					</div>
				</div>
			</div>
		<?php else: ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">产品备注</h4>
				</div>
				<div class="panel-body panel-body-min-height"><?php echo ($sample["comment"]); ?></div>
			</div><?php endif; ?>

        <!-- 附件 -->
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">订单附件</h4>
            </div>
            <div class="panel-body">
                <ul class="attachmentList">
                    <?php if(is_array($attachment)): $i = 0; $__LIST__ = $attachment;if( count($__LIST__)==0 ) : echo "$noAttachment" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li class="attachment">
                            <div class="attachmentIconBox">
                                <img src="/Public/Home/img/file.png" alt="...">
                                <span class="text-uppercase"><?php echo ($value["type"]); ?></span>
                            </div>
                            <p><?php echo ($value["filename"]); ?></p>
                            <form action="/index.php/Home/Sample/downloadAttachment" method="post">
                                <input type="hidden" name="filepath" value="<?php echo ($value["filepath"]); ?>">
                                <input type="submit" class="btn btn-success icon-download" value="下载">
                            </form>
                        </li><?php endforeach; endif; else: echo "$noAttachment" ;endif; ?>
                </ul>
            </div>
        </div>

        <!-- 测试报告 -->
        <?php if(isset($sample["attachment"])): ?><div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">测试报告</h4>
                </div>
                <div class="panel-body">
                    <ul class="attachmentList">
                        <li class="attachment">
                            <div class="attachmentIconBox">
                                <img src="/Public/Home/img/file.png" alt="...">
                                <span class="text-uppercase"><?php echo ($sample["attachment"]["type"]); ?></span>
                            </div>
                            <p><?php echo ($sample["attachment"]["filename"]); ?></p>
                            <form action="/index.php/Home/Sample/downloadAttachment" method="post">
                                <input type="hidden" name="filepath" value="<?php echo ($sample["attachment"]["filepath"]); ?>">
                                <input type="submit" class="btn btn-success icon-download" value="下载">
                            </form>
                        </li>
                    </ul>
                </div>
            </div><?php endif; ?>


		<!-- 审核 -->
		<?php if(($sample["aid"]) == $_SESSION['user']['id']): if($sample["s_status"] == 0 || $sample["s_status"] == '' || $sample["s_status"] == 3): ?><div class="operation-opacity"  data-toggle="modal" data-target="#audit-modal">
					<div class="operation-btn">
						<p class="operation-icon"><img src="/Public/Home/img/audit.png" alt="..."></p>
						<p class="operation-text">审核</p>
					</div>
				</div><?php endif; endif; ?>
		<!-- 物料准备 -->
		<?php if(($sample["w_id"]) == $_SESSION['user']['id']): if(($sample["s_status"]) == "1"): if($sample["w_status"] == 0 || $sample["w_status"] == '' || $sample["w_status"] == 3): ?><div class="operation-opacity"  data-toggle="modal" data-target="#material-modal">
						<div class="operation-btn">
							<p class="operation-icon"><img src="/Public/Home/img/house-material.png" alt="..."></p>
							<p class="operation-text">物料准备</p>
						</div>
					</div><?php endif; endif; endif; ?>
		<!-- 样品制作 -->
		<?php if(($sample["c_id"]) == $_SESSION['user']['id']): if(($sample["w_status"]) == "1"): if($sample["c_status"] == 0 || $sample["c_status"] == '' || $sample["c_status"] == 3): ?><div class="operation-opacity"  data-toggle="modal" data-target="#productEngineer-modal">
						<div class="operation-btn">
							<p class="operation-icon"><img src="/Public/Home/img/conow-my-sample.png" alt="..."></p>
							<p class="operation-text">样品制作</p>
						</div>
					</div><?php endif; endif; endif; ?>
        <!-- 样品测试 -->
        <?php if(($sample["y_id"]) == $_SESSION['user']['id']): if(($sample["c_status"]) == "1"): if($sample["y_status"] == 0 || $sample["y_status"] == '' || $sample["y_status"] == 3): ?><div class="operation-opacity"  data-toggle="modal" data-target="#testEngineer-modal">
                        <div class="operation-btn">
                            <p class="operation-icon"><img src="/Public/Home/img/test.png" alt="..."></p>
                            <p class="operation-text">样品测试</p>
                        </div>
                    </div><?php endif; endif; endif; ?>
        <!-- 发货 -->
        <?php if(($sample["f_id"]) == $_SESSION['user']['id']): if(($sample["y_status"]) == "1"): if($sample["f_status"] == 0 || $sample["f_status"] == '' || $sample["f_status"] == 3): ?><div class="operation-opacity"  data-toggle="modal" data-target="#delivery-modal">
                        <div class="operation-btn">
                            <p class="operation-icon"><img src="/Public/Home/img/delivery.png" alt="..."></p>
                            <p class="operation-text">发货</p>
                        </div>
                    </div><?php endif; endif; endif; ?>
        <!-- 反馈 -->
        <?php if(($sample["k_id"]) == $_SESSION['user']['id']): if(($sample["f_status"]) == "1"): if($sample["k_status"] == 0 || $sample["k_status"] == '' || $sample["k_status"] == 3): ?><div class="operation-opacity"  data-toggle="modal" data-target="#feedback-modal">
                        <div class="operation-btn">
                            <p class="operation-icon"><img src="/Public/Home/img/feedback.png" alt="..."></p>
                            <p class="operation-text">反馈</p>
                        </div>
                    </div><?php endif; endif; endif; ?>

		<!-- 审核 -->
		<?php if(($sample["aid"]) == $_SESSION['user']['id']): if($sample["s_status"] == 0 || $sample["s_status"] == '' || $sample["s_status"] == 3): ?><div id="audit" class="auditContainer">
					<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="audit-modal">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
									<h4 class="modal-title">审核</h4>
								</div>
								<div class="modal-body">
									<form class="sampleAid form-inline" role="form">
										<div class="form-group">
											<label>状态</label>
											<div class="btn-group">
												<button type="button" class="btn btn-default stateText">通过</button>
												<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
													<span class="caret"></span>
												</button>
												<ul class="dropdown-menu state" role="menu">
													<li state="1"><a href="javascript:void(0);">通过</a></li>
													<li state="2"><a href="javascript:void(0);">拒绝</a></li>
													<li state="3"><a href="javascript:void(0);">添加日志</a></li>
												</ul>
											</div>
											<div class="help-block product-engineer-tip">&nbsp;</div>
										</div>　
										<div class="form-group productEngineerContainer">
											<label>物料准备</label>
											<div class="input-group">
												<input type="text" name="approval" value="" class="form-control approval min-approval material-default-value" readonly>
												<span class="input-group-btn">
													<button type="button" class="btn btn-default" data-toggle="modal" data-target="#ProductEngineer">选择</button>
												</span>
											</div>
											<div class="help-block product-engineer-tip">&nbsp;</div>
										</div>　
										<div class="form-group">
											<label>预计交期</label>
											<input type="text" name="e_date" value="<?php if(empty($sample["e_date"])): echo ($sample["d_date"]); else: echo ($sample["e_date"]); endif; ?>" class="form-control e-date" readonly>
											<div class="help-block product-engineer-tip">&nbsp;</div>
										</div>
										<div class="clearfix"></div>
										<div class="form-group">
											<textarea class="form-control textarea" name="comment"></textarea>
										</div>
										<div class="clearfix"></div>
										<div class="form-group">
											<input type="submit" class="btn btn-primary btn-lg doneBtn" value="确定">
										</div>
										<input type="hidden" name="status" value="1" class="status">
										<input type="hidden" name="wid" value="" class="cid material-hidden-value">
										<input type="hidden" name="assoc" value="<?php echo ($sample["id"]); ?>">
										<input type="hidden" name="total_order" value="<?php echo ($sample["totalorder"]); ?>">
										<input type="hidden" name="product" value="<?php echo ($sample["product"]); ?>">
										<input type="hidden" name="order" value="<?php echo ($sample["order"]); ?>">
										<input type="hidden" name="uid" value="<?php echo ($sample["uid"]); ?>">
									</form>
								</div>
							</div>
						</div>
					</div>
					<!--选择计划专员模态框-->
					<div class="modal fade bs-example-modal-lg" id="ProductEngineer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title" id="myModalLabel">选择计划专员</h4>
								</div>
								<div class="modal-body">
									<ul class="user-select-box material-user-list">
										<?php if(is_array($users)): $i = 0; $__LIST__ = $users;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><!--不显示超级管理员-->
											<?php if($value["department"] == 5): ?><li index="<?php echo ($value["id"]); ?>"><?php echo ($value["nickname"]); ?></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
									</ul>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
								</div>
							</div>
						</div>
					</div>
				</div><?php endif; endif; ?>

		<!-- 物料准备 -->
		<?php if(($sample["w_id"]) == $_SESSION['user']['id']): if($sample["w_status"] == 0 || $sample["w_status"] == '' || $sample["w_status"] == 3): ?><div id="audit" class="auditContainer">
					<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="material-modal">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
									<h4 class="modal-title">物料准备</h4>
								</div>
								<div class="modal-body">
									<form class="sampleWid form-inline" role="form">
										<div class="form-group">
											<label>状态</label>
											<div class="btn-group">
												<button type="button" class="btn btn-default stateText">已完成</button>
												<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
													<span class="caret"></span>
												</button>
												<ul class="dropdown-menu state" role="menu">
													<li state="1"><a href="javascript:void(0);">已完成</a></li>
													<li state="2"><a href="javascript:void(0);">未完成</a></li>
													<li state="3"><a href="javascript:void(0);">添加日志</a></li>
												</ul>
											</div>
											<div class="help-block product-engineer-tip">&nbsp;</div>
										</div>　
										<div class="form-group productEngineerContainer">
											<label>样品制作</label>
											<input type="text" name="approval" value="<?php echo ($sample["a_name"]); ?>" class="form-control approval min-approval" readonly>
											<div class="help-block product-engineer-tip">&nbsp;</div>
										</div>　
										<div class="form-group">
											<label>预计交期</label>
											<input type="text" name="e_date" value="<?php if(empty($sample["e_date"])): echo ($sample["d_date"]); else: echo ($sample["e_date"]); endif; ?>" class="form-control e-date" readonly>
											<div class="help-block product-engineer-tip">&nbsp;</div>
										</div>
										<div class="clearfix"></div>
										<div class="form-group">
											<textarea class="form-control textarea" name="comment"></textarea>
										</div>
										<div class="clearfix"></div>
										<div class="form-group">
											<input type="submit" class="btn btn-primary btn-lg doneBtn" value="确定">
										</div>
										<input type="hidden" name="status" value="1" class="status">
										<input type="hidden" name="cid" value="<?php echo ($sample["aid"]); ?>" class="cid">
										<input type="hidden" name="assoc" value="<?php echo ($sample["id"]); ?>">
										<input type="hidden" name="total_order" value="<?php echo ($sample["totalorder"]); ?>">
										<input type="hidden" name="product" value="<?php echo ($sample["product"]); ?>">
										<input type="hidden" name="order" value="<?php echo ($sample["order"]); ?>">
										<input type="hidden" name="uid" value="<?php echo ($sample["uid"]); ?>">
									</form>
								</div>
							</div>
						</div>
					</div>
					<!--选择产品工程师模态框-->
					<!--<div class="modal fade bs-example-modal-lg" id="ProductEngineer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title" id="myModalLabel">选择产品工程师</h4>
								</div>
								<div class="modal-body">
									<ul class="user-select-box">
										<?php if(is_array($users)): $i = 0; $__LIST__ = $users;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?>&lt;!&ndash;不显示超级管理员&ndash;&gt;
											<?php if($value["id"] == $_SESSION['user']['id'] || $value["account"] != 'admin' && $value["report"] == $_SESSION['user']['id']): ?><li index="<?php echo ($value["id"]); ?>"><?php echo ($value["nickname"]); ?></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
									</ul>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
								</div>
							</div>
						</div>
					</div>-->
				</div><?php endif; endif; ?>

		<!-- 样品制作 -->
		<?php if(($sample["c_id"]) == $_SESSION['user']['id']): if($sample["c_status"] == 0 || $sample["c_status"] == '' || $sample["c_status"] == 3): ?><div id="audit" class="auditContainer">
					<div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="productEngineer-modal">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
									<h4 class="modal-title">样品制作</h4>
								</div>
								<div class="modal-body">
									<form class="sampleCid form-inline" role="form">
										<div class="form-group">
											<label>状态</label>
											<div class="btn-group">
												<button type="button" class="btn btn-default stateText">已完成</button>
												<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
													<span class="caret"></span>
												</button>
												<ul class="dropdown-menu state" role="menu">
													<li state="1"><a href="javascript:void(0);">已完成</a></li>
													<li state="2"><a href="javascript:void(0);">无法完成</a></li>
													<li state="3"><a href="javascript:void(0);">制作中</a></li>
												</ul>
											</div>
											<div class="help-block product-engineer-tip">&nbsp;</div>
										</div>　
										<div class="form-group productEngineerContainer">
											<label>样品测试</label>
											<div class="input-group">
												<input type="text" name="approval" value="" class="form-control approval min-approval" readonly>
												<span class="input-group-btn">
													<button type="button" class="btn btn-default" data-toggle="modal" data-target="#ProductEngineer">选择</button>
												</span>
											</div>
											<div class="help-block product-engineer-tip">&nbsp;</div>
										</div>　
										<div class="form-group">
											<label>预计交期</label>
											<input type="text" name="e_date" value="<?php if(empty($sample["e_date"])): echo ($sample["d_date"]); else: echo ($sample["e_date"]); endif; ?>" class="form-control e-date" readonly>
											<div class="help-block product-engineer-tip">&nbsp;</div>
										</div>
										<div class="clearfix"></div>
										<div class="form-group">
											<textarea class="form-control textarea" name="comment"></textarea>
										</div>
										<div class="clearfix"></div>
										<div class="form-group">
											<input type="submit" class="btn btn-primary btn-lg doneBtn" value="确定">
										</div>
										<input type="hidden" name="status" value="1" class="status">
										<input type="hidden" name="cid" value="" class="cid">
										<input type="hidden" name="assoc" value="<?php echo ($sample["id"]); ?>">
										<input type="hidden" name="total_order" value="<?php echo ($sample["totalorder"]); ?>">
										<input type="hidden" name="product" value="<?php echo ($sample["product"]); ?>">
										<input type="hidden" name="order" value="<?php echo ($sample["order"]); ?>">
										<input type="hidden" name="uid" value="<?php echo ($sample["uid"]); ?>">
									</form>
								</div>
							</div>
						</div>
					</div>
					<!--选择产品工程师模态框-->
					<div class="modal fade bs-example-modal-lg" id="ProductEngineer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
						<div class="modal-dialog modal-lg">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title" id="myModalLabel">测试工程师</h4>
								</div>
								<div class="modal-body">
									<ul class="user-select-box">
										<?php if(is_array($users)): $i = 0; $__LIST__ = $users;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><!--不显示超级管理员及自身-->
											<?php if($value["account"] != 'admin' && $value["position"] == 11): ?><li index="<?php echo ($value["id"]); ?>"><?php echo ($value["nickname"]); ?></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
									</ul>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
								</div>
							</div>
						</div>
					</div>
				</div><?php endif; endif; ?>

        <!-- 样品测试 -->
        <?php if(($sample["y_id"]) == $_SESSION['user']['id']): if($sample["y_status"] == 0 || $sample["y_status"] == '' || $sample["y_status"] == 3): ?><div class="auditContainer">
                    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="testEngineer-modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                    <h4 class="modal-title">样品测试</h4>
                                </div>
                                <div class="modal-body">
                                    <form class="sampleYid form-inline" role="form">
                                        <div class="form-group">
                                            <label>状态</label>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default stateText">通过</button>
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu state" role="menu">
                                                    <li state="1"><a href="javascript:void(0);">通过</a></li>
                                                    <li state="2"><a href="javascript:void(0);">未通过</a></li>
                                                    <li state="3"><a href="javascript:void(0);">添加日志</a></li>
                                                </ul>
                                            </div>
                                            <div class="help-block product-engineer-tip">&nbsp;</div>
                                        </div>　
										<div class="form-group">
											<label>预计交期</label>
											<input type="text" name="e_date" value="<?php if(empty($sample["e_date"])): echo ($sample["d_date"]); else: echo ($sample["e_date"]); endif; ?>" class="form-control e-date" readonly>
											<div class="help-block product-engineer-tip">&nbsp;</div>
										</div>　
                                        <div class="clearfix"></div>
                                        <div class="form-group textarea-form-group">
                                            <textarea class="form-control textarea" name="comment"></textarea>
                                        </div>

                                        <div class="form-group">
                                            <input type="submit" class="btn btn-primary btn-lg doneBtn margin-doneBtn" value="确定">
                                        </div>
                                        <input type="hidden" name="status" value="1" class="status">
                                        <input type="hidden" name="cid" value="<?php echo ($sample["aid"]); ?>" class="cid">
                                        <input type="hidden" name="assoc" value="<?php echo ($sample["id"]); ?>">
                                        <input type="hidden" name="total_order" value="<?php echo ($sample["totalorder"]); ?>">
                                        <input type="hidden" name="product" value="<?php echo ($sample["product"]); ?>">
                                        <input type="hidden" name="order" value="<?php echo ($sample["order"]); ?>">
                                        <input type="hidden" name="uid" value="<?php echo ($sample["uid"]); ?>">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><?php endif; endif; ?>

        <!-- 发货 -->
        <?php if(($sample["f_id"]) == $_SESSION['user']['id']): if($sample["f_status"] == 0 || $sample["f_status"] == '' || $sample["f_status"] == 3): ?><div class="auditContainer">
                    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="delivery-modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                    <h4 class="modal-title">发货</h4>
                                </div>
                                <div class="modal-body">
                                    <form class="sampleFid form-inline" role="form">
                                        <div class="form-group">
                                            <label>状态</label>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default stateText">已发货</button>
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu state" role="menu">
                                                    <li state="1"><a href="javascript:void(0);">已发货</a></li>
                                                    <li state="2"><a href="javascript:void(0);">未发货</a></li>
                                                    <li state="3"><a href="javascript:void(0);">添加日志</a></li>
                                                </ul>
                                            </div>
                                            <div class="help-block product-engineer-tip">&nbsp;</div>
                                        </div>　
                                        <div class="form-group form-group-logistics">
                                            <label>物流</label>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default logistics-name">顺丰物流</button>
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu logistics-list" role="menu">
                                                    <li logistics="shunfeng"><a href="javascript:void(0);">顺丰物流</a></li>
                                                    <li logistics="dhl"><a href="javascript:void(0);">DHL</a></li>
                                                    <li logistics="fedex"><a href="javascript:void(0);">FedEx</a></li>
                                                    <li logistics="ups"><a href="javascript:void(0);">UPS</a></li>
                                                    <li logistics="shentong"><a href="javascript:void(0);">申通快递</a></li>
                                                    <li logistics="yuantong"><a href="javascript:void(0);">圆通速递</a></li>
                                                    <li logistics="zhongtong"><a href="javascript:void(0);">中通速递</a></li>
                                                </ul>
                                            </div>
                                            <div class="help-block logistics-tip">&nbsp;</div>
                                        </div>　
                                        <div class="form-group productEngineerContainer">
                                            <label>运单号</label>
                                            <input type="text" name="approval" value="" class="form-control approval">
                                            <div class="help-block product-engineer-tip logistics-tip">&nbsp;</div>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="form-group">
                                            <textarea class="form-control textarea" name="comment"></textarea>
                                        </div>
										<div class="clearfix"></div>
										<div class="form-group form-group-upload-btn">
											<!--<input type="file" name="testReport[]" id="testReport">-->
											<div id="testReport"></div>
											<div class="test-report-file-info"></div>
										</div>
										<hr />
                                        <div class="clearfix"></div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-primary btn-lg doneBtn margin-doneBtn" value="确定">
                                        </div>
                                        <input type="hidden" name="status" value="1" class="status">
                                        <input type="hidden" name="cid" value="<?php echo ($sample["uid"]); ?>" class="cid">
                                        <input type="hidden" name="logistics" value="shunfeng" class="logistics-code">
                                        <input type="hidden" name="assoc" value="<?php echo ($sample["id"]); ?>">
                                        <input type="hidden" name="total_order" value="<?php echo ($sample["totalorder"]); ?>">
                                        <input type="hidden" name="product" value="<?php echo ($sample["product"]); ?>">
                                        <input type="hidden" name="order" value="<?php echo ($sample["order"]); ?>">
                                        <input type="hidden" name="uid" value="<?php echo ($sample["uid"]); ?>">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><?php endif; endif; ?>

        <!-- 反馈 -->
        <?php if(($sample["k_id"]) == $_SESSION['user']['id']): if($sample["k_status"] == 0 || $sample["k_status"] == '' || $sample["k_status"] == 3): ?><div class="auditContainer">
                    <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="feedback-modal">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                                    <h4 class="modal-title">反馈</h4>
                                </div>
                                <div class="modal-body">
                                    <form class="sampleKid form-inline" role="form">
                                        <div class="form-group">
                                            <label>状态</label>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default stateText">已通过</button>
                                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                                    <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu state" role="menu">
                                                    <li state="1"><a href="javascript:void(0);">已通过</a></li>
                                                    <li state="2"><a href="javascript:void(0);">未通过</a></li>
                                                    <li state="3"><a href="javascript:void(0);">添加日志</a></li>
                                                </ul>
                                            </div>
                                            <div class="help-block product-engineer-tip">&nbsp;</div>
                                        </div>　
                                        <div class="clearfix"></div>
                                        <div class="form-group">
                                            <textarea class="form-control textarea" name="comment"></textarea>
                                        </div>
                                        <div class="clearfix"></div>
                                        <div class="form-group">
                                            <input type="submit" class="btn btn-primary btn-lg doneBtn" value="确定">
                                        </div>
                                        <input type="hidden" name="status" value="1" class="status">
                                        <input type="hidden" name="cid" value="<?php echo ($sample["aid"]); ?>" class="cid">
                                        <input type="hidden" name="assoc" value="<?php echo ($sample["id"]); ?>">
                                        <input type="hidden" name="total_order" value="<?php echo ($sample["totalorder"]); ?>">
                                        <input type="hidden" name="product" value="<?php echo ($sample["product"]); ?>">
                                        <input type="hidden" name="order" value="<?php echo ($sample["order"]); ?>">
                                        <input type="hidden" name="uid" value="<?php echo ($sample["uid"]); ?>">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><?php endif; endif; ?>

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

		<!-- loading -->
		<div class="loading-modal sr-only" id="loading">
			<div class="loading-icon">
				<p><i class="icon-spinner icon-spin icon-4x"></i></p>
			</div>
		</div>

    </div>


	<!--预计交期时间选择-->
	<script>
		$('.e-date').datetimepicker({
			language:  'zh-CN',
			format:'yyyy-mm-dd',
			weekStart: 1,
			todayBtn:  1,
			autoclose: 1,
			todayHighlight: 1,
			startDate : new Date(),
			pickerPosition: "bottom-left",
			startView: 2,
			minView: 2,
			forceParse: 0
		});
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