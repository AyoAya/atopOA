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
	<link rel="stylesheet" href="/Public/Home/css/bootstrap-datetimepicker.min.css">
	<link rel="stylesheet" href="/Public/Home/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="/Public/Home/css/rma/customer.css">
<!-- 引入css文件 -->
	<script src="/Public/Home/js/jquery.min.js"></script>
	<script src="/Public/Home/js/bootstrap.min.js"></script>
	<script src="/Public/layui/layui.js"></script>
	<script src="/Public/Home/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/Public/Home/js/index.js"></script>

	
	<script src="/Public/Home/js/jquery.Huploadify.js"></script>
	<script src="/Public/webuploader/js/webuploader.js"></script>
	<script src="/Public/Home/js/jquery.validate.min.js"></script>
	<script src="/Public/Home/js/jquery.form.js"></script>
	<script src="/Public/Home/js/bootstrap-datetimepicker.js"></script>
	<script src="/Public/Home/js/bootstrap-datetimepicker.zh-CN.js"></script>
	<script src="/Public/Home/js/rma/customer.js"></script>
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
		<li class="active">客诉处理</li>
	</ol>
	<!--<div class="total-info">
		共 <b><?php echo ($customerAllTotal); ?></b> 条记录，正在处理 <?php echo ($customerTotal["d"]); ?> 条，已经处理 <?php echo ($customerTotal["y"]); ?> 条，无法处理 <?php echo ($customerTotal["n"]); ?> 条
	</div>-->

		</div>

		<div id="content">
			<div class="container-fluid" id="content-box">
				
	<div class="customerContainer">


		<div class="pull-left">
			<a class="layui-btn layui-btn-primary">
				<span class="glyphicon glyphicon-th-list"></span> 筛选
			</a>
			<!--<a href="/RMA/chart" class="layui-btn layui-btn-primary">
				<span class="glyphicon glyphicon-signal"></span> 客诉统计
			</a>-->
			<a href="/customer" class="layui-btn layui-btn-primary">
				<span class="icon-comments-alt"></span> 旧版客诉
			</a>
			<?php if(($face["department"]) == "4"): ?><a href="/RMA/addCustomer" class="layui-btn layui-btn-primary">
					<span class="glyphicon glyphicon-plus"></span> 新增客诉
				</a><?php endif; ?>
		</div>

		<div class="pull-right">
			<form class="layui-form">
				<div class="layui-inline rma-serach">
					<input type="text" name="searchtext" value="<?php echo ($_GET['searchtext']); ?>" class="layui-input" placeholder="搜索">
					<button lay-submit lay-filter="search" class="submit-serach-icon"><i class="layui-icon">&#xe615;</i></button>
				</div>
			</form>
		</div>

		<div class="clearfix" style="margin-bottom: 25px;"></div>


		<!-- 筛选 -->
		<div id="rma-filter">
			<form class="layui-form">
				<div class="layui-form-item">
					<label class="layui-form-label">与我相关</label>
					<div class="layui-input-inline">
						<input type="radio" name="withme" title="是">
						<input type="radio" name="withme" title="否" checked>
					</div>
				</div>
				<div class="layui-form-item">
					<div class="layui-inline">
						<label class="layui-form-label">当前进度</label>
						<div class="layui-input-inline">
							<select name="step_name">
								<option value="">选择指定步骤</option>
								<option value="1">步骤1</option>
								<option value="2">步骤2</option>
								<option value="3">步骤3</option>
								<option value="4">步骤4</option>
								<option value="5">步骤5</option>
							</select>
						</div>
					</div>
				</div>
				<div class="layui-form-item">
					<div class="layui-inline">
						<label class="layui-form-label">客诉日期</label>
						<div class="layui-input-inline" style="width: 150px;">
							<input type="text" name="start_date" placeholder="起始时间" autocomplete="off" class="layui-input">
						</div>
						<div class="layui-form-mid">-</div>
						<div class="layui-input-inline" style="width: 150px;">
							<input type="text" name="end_date" placeholder="结束时间" autocomplete="off" class="layui-input">
						</div>
					</div>
				</div>
				<div class="layui-form-item short-input-box">
					<div class="layui-inline">
						<label class="layui-form-label">订单编号</label>
						<div class="layui-input-inline">
							<input type="text" name="order" class="layui-input" placeholder="订单编号">
						</div>
					</div>
					<div class="layui-inline">
						<label class="layui-form-label">销售人员</label>
						<div class="layui-input-inline">
							<input type="text" name="saleperson" class="layui-input" placeholder="销售人员">
						</div>
					</div>
					<div class="layui-inline">
						<label class="layui-form-label">客户名称</label>
						<div class="layui-input-inline">
							<input type="text" name="customer" class="layui-input" placeholder="客户名称">
						</div>
					</div>
					<div class="layui-inline">
						<label class="layui-form-label">设备厂商</label>
						<div class="layui-input-inline">
							<input type="text" name="vendor" class="layui-input" placeholder="设备厂商">
						</div>
					</div>
				</div>
				<div class="layui-form-item" style="margin-bottom: 0">
					<div class="layui-input-block" style="margin-left: 80px;">
						<button class="layui-btn">筛选</button>
						<button type="reset" class="layui-btn layui-btn-primary">重置</button>
					</div>
				</div>
			</form>
			<div class="arrow-icon"></div>
		</div>


		<table class="customer-table">
			<tbody>
				<?php if(is_array($customer)): $i = 0; $__LIST__ = $customer;if( count($__LIST__)==0 ) : echo "$empty" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><tr>
						<td class="customer-left">
							<a href="/RMA?salesperson=<?php echo ($value["salesperson"]); ?>"><img src="<?php echo ($value["face"]); ?>" alt="..." width="60"></a>
						</td>
						<td class="customer-right">
							<a href="/RMA/details/id/<?php echo ($value["id"]); ?>" class="customer-content">
								<p>
									<?php if(($value["version"]) == "old"): switch($value["status"]): case "-1": ?><span class="tag-danger">无法处理</span><?php break;?>
											<?php case "1": ?><span class="tag-primary">正在处理</span><?php break;?>
											<?php case "0": ?><span class="tag-success">已经处理</span><?php break; endswitch;?>
									<?php else: ?>
										<?php if(($value["rma_state"]) == "N"): ?><span class="tag-primary"><?php echo ($value["step"]["step_name"]); ?></span>
										<?php else: ?>
											<span class="tag-success">已关闭</span><?php endif; endif; ?>
									<?php if(!empty($value["customer"])): ?><span class="tag-hollow">客户：<?php echo ($value["customer"]); ?></span><?php endif; ?>
									<?php if(!empty($value["pn"])): ?><span class="tag-hollow">产品：<?php echo ($value["pn"]); ?></span><?php endif; ?>
									<?php if(!empty($value["vendor"])): ?><span class="tag-hollow">品牌：<?php echo ($value["vendor"]); ?></span><?php endif; ?>
									<?php if(!empty($value["model"])): ?><span class="tag-hollow">型号：<?php echo ($value["model"]); ?></span><?php endif; ?>
								</p>
								<p class="customer-reason"><b>错误信息：</b><?php echo ($value["error_message"]); ?></p>
								<p class="customer-other"><?php if(!empty($value["nickname"])): echo ($value["nickname"]); else: echo ($value["salesperson"]); endif; ?>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo ($value["cc_time"]); ?></p>
							</a>
						</td>
					</tr><?php endforeach; endif; else: echo "$empty" ;endif; ?>
			</tbody>
		</table>

	</div>
	<!-- 数据分页 -->
	<div class="pageContainer">
		<ul class="pagination pagination-edit"><?php echo ($pageShow); ?></ul>
	</div>







	<script>
		$('.startDate').datetimepicker({
			language:  'zh-CN',
			format:'yyyy-mm-dd',
			weekStart: 1,
			todayBtn:  1,
			autoclose: 1,
			todayHighlight: 1,
			startDate : '2014-3-21',
			endDate : new Date(),
			startView: 2,
			minView: 2,
			forceParse: 0
		});
		$('.endDate').datetimepicker({
			language:  'zh-CN',
			format:'yyyy-mm-dd',
			weekStart: 1,
			todayBtn:  1,
			autoclose: 1,
			todayHighlight: 1,
			startDate : '2014-3-21',
			endDate : new Date(),
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