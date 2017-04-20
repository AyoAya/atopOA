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

	
	<link rel="stylesheet" href="/Public/Home/css/Huploadify.css">
	<link rel="stylesheet" href="/Public/Home/css/jquery.toastmessage.css">
	<link rel="stylesheet" href="/Public/Home/css/bootstrap-datetimepicker.min.css">
	<link rel="stylesheet" href="/Public/Home/css/oldcustomer/customer-details.css">
<!-- 引入css文件 -->
	<script src="/Public/Home/js/jquery.min.js"></script>
	<script src="/Public/Home/js/bootstrap.min.js"></script>
	<script src="/Public/layui/layui.js"></script>
	<script src="/Public/Home/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/Public/Home/js/index.js"></script>

	
	<script src="/Public/Home/js/jquery.Huploadify.js"></script>
	<script src="/Public/Home/js/jquery.validate.min.js"></script>
	<script src="/Public/Home/js/jquery.form.js"></script>
	<script src="/Public/Home/js/bootstrap-datetimepicker.js"></script>
	<script src="/Public/Home/js/bootstrap-datetimepicker.zh-CN.js"></script>
	<script src="/Public/Home/js/jquery.toastmessage.js"></script>
	<script src="/Public/Home/js/oldcustomer/customer.js"></script>
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
		<li><a href="/RMA">客诉处理</a></li>
		<li><a href="/customer">旧版客诉</a></li>
		<li class="active">客诉详情</li>
	</ol>

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
				

	<!-- 旧版客诉不允许操作，只能转为新客诉之后才有操作【PS：只允许该条客诉的销售进行此操作】 -->
	<?php if(($_SESSION['user']['account']) == $details["salesperson"]): ?><div class="pull-left" role="group">
			<button type="button" class="layui-btn layui-btn-primary" data-toggle="modal" data-target="#changeNewVersion">
				<span class="glyphicon glyphicon-plus"></span> 转为新版客诉
			</button>
		</div>

		<div class="clearfix" style="margin-bottom: 25px;"></div><?php endif; ?>

	<div class="panel panel-default">
		<div class="panel-heading panel-heading-edit"><h4>基本信息</h4></div>
		<table class="table" id="table">
			<thead>
				<tr>
					<th>客诉ID</th>
					<th>客诉时间</th>
					<th>销售人员</th>
					<th>客户</th>
					<th>订单号</th>
					<th>产品</th>
					<th>状态</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo ($details["id"]); ?></td>
					<td><?php echo ($details["cc_time"]); ?></td>
					<td><?php echo ($details["nickname"]); ?></td>
					<td><?php echo ($details["customer"]); ?></td>
					<td><?php echo ($details["sale_order"]); ?></td>
					<td><?php echo ($details["pn"]); ?></td>
					<td>
						<?php switch($details["status"]): case "-1": ?><span class="label label-danger">无法处理</span><?php break;?>
							<?php case "1": ?><span class="label label-primary">正在处理</span><?php break;?>
							<?php case "0": ?><span class="label label-success">已经处理</span><?php break; endswitch;?>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading panel-heading-edit"><h4>设备信息</h4></div>
		<div class="panel-body">
			设备厂商：<kbd><?php echo ($details["vendor"]); ?></kbd>　　设备型号：<kbd><?php echo ($details["model"]); ?></kbd>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading panel-heading-edit"><h4>错误现象</h4></div>
		<div class="panel-body">
			<code><?php echo ($details["error_message"]); ?></code>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading panel-heading-edit"><h4>原因分析</h4></div>
		<div class="panel-body">
			<?php echo ($details["reason"]); ?>
		</div>
	</div>
	<?php if(!empty($details["comments"])): ?><div class="panel panel-default">
		<div class="panel-heading panel-heading-edit"><h4>备注信息</h4></div>
		<div class="panel-body">
			<?php echo ($details["comments"]); ?>
		</div>
	</div><?php endif; ?>
	<div class="panel panel-default" id="deal-record">
		<div class="panel-heading panel-heading-edit"><h4>处理记录</h4></div>
		<div class="panel-body">
			<?php if(is_array($details["log"])): $i = 0; $__LIST__ = $details["log"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="media media-edit">
					<span class="media-left media-top media-img" href="###">
						<img class="userface" src="<?php echo ($vo["face"]); ?>" width="100" height="100" alt="...">
					</span>
					<div class="media-body">
						<p class="log-info"><kbd><?php echo ($vo["log_date"]); ?></kbd> <?php echo ($vo["log_content"]); ?></p>
						<?php if(!empty($vo["picture"])): if(is_array($vo["picture"])): $i = 0; $__LIST__ = $vo["picture"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; if(!empty($v)): ?><!-- 如果图像为空则不输出 -->
									<div class="pic-group pull-left">
										<p class="float-pic">
											<img class="customer-complaint-img" src="/.<?php echo ($v["filepath"]); ?>" alt="图像加载错误">
										</p>
										<span class="pic-info"><?php echo ($v["filename"]); ?></span>
									</div><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
						<div class="clearfix"></div>
						<?php if(!empty($vo["attachment"])): if(is_array($vo["attachment"])): $i = 0; $__LIST__ = $vo["attachment"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; if(($v["filetype"]) == "other"): ?><div class="attachment-box">
										<div class="attachment-box-dom">
											<p class="file-name">
												<span class="file-name-info pull-left"><i class="icon-file-alt"></i> <?php echo ($v["filename"]); ?></span>
												<form action="/index.php/Home/Customer/downloadFile" method="post" class="pull-left">
													<input type="hidden" name="filepath" value="<?php echo ($v["filepath"]); ?>">
													<input type="submit" class="btn btn-default btn-xs download-file" value="下载">
												</form>
											</p>
										</div>
									</div>
								<?php else: ?>
									<?php if(($v) != ""): ?><!-- 如果图像为空则不输出 -->
										<div class="pic-group pull-left">
											<p class="float-pic">
												<img class="customer-complaint-img" src="/<?php echo ($v["filepath"]); ?>" alt="图像加载错误">
											</p>
											<span class="pic-info"><?php echo ($v["filename"]); ?></span>
										</div><?php endif; endif; endforeach; endif; else: echo "" ;endif; endif; ?>
						<div class="clearfix"></div>
						<p class="log-time"><?php echo ($vo["recorder"]); ?> | <?php echo ($vo["timestamp"]); ?></p>
					</div>
				</div><?php endforeach; endif; else: echo "" ;endif; ?>
		</div>
	</div>
	
	<!-- 转为新版客诉模态框 -->
	<?php if(($_SESSION['user']['account']) == $details["salesperson"]): ?><div class="modal fade bs-example-modal-sm" id="changeNewVersion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="layui-form">
				<div class="modal-dialog modal-sm" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title" id="myModalLabel">转为新版客诉</h4>
						</div>
						<div class="modal-body">
							<p class="warning-prompt"><i class="icon-warning-sign"></i>转为新版客诉后，将无法再回到旧版客诉</p>
							<div class="layui-form-item">
								<label class="layui-form-label">FAE</label>
								<div class="layui-input-block">
									<select name="operation_person">
										<?php if(is_array($FAE_person_list)): $i = 0; $__LIST__ = $FAE_person_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><option value="<?php echo ($value["id"]); ?>"><?php echo ($value["nickname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
									</select>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<input type="hidden" name="main_assoc" value="<?php echo ($details["id"]); ?>">
							<button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
							<button lay-submit lay-filter="changeNewVersion" type="button" class="btn btn-primary">确定</button>
						</div>
					</div>
				</div>
			</div>
		</div><?php endif; ?>

	<!-- 消息提示模态框 -->
	<div class="modal fade bs-example-modal-sm" id="message-modal" role="dialog">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-body" id="message-modal-text">
					<p class="customer-message customer-message-success"><i class="icon-ok-sign icon-2x modal-icon-success"></i><span class="message-text">成功后的信息</span></p>
					<p class="customer-message customer-message-error"><i class="icon-remove-sign icon-2x modal-icon-error"></i><span class="message-text">失败后的信息</span></p>
					<p class="customer-message customer-message-info"><i class="icon-info-sign icon-2x modal-icon-info"></i><span class="message-text">错误的信息</span></p>
				</div>
			</div>
		</div>
	</div>

	<!-- 图像展示 -->
	<div id="picturePreview">
		<div class="pictureContainer"></div>
		<span class="closePreview" title="关闭"><i class="icon-remove icon-2x"></i></span>
	</div>

	<script>
	$('.form_date').datetimepicker({
        language:  'zh-CN',
        format:'yyyy-mm-dd',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		endDate : new Date(),
		startView: 2,
		minView: 2,
		forceParse: 0
    }).on('changeDate',function(ev){
    	$('#change-date-remove').html('');
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