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
	<link rel="stylesheet" href="/Public/Home/css/Theme/<?php echo ($face["theme"]); ?>.css">
	<link rel="stylesheet" href="/Public/Home/css/promotion_index.css">

	
	<link rel="stylesheet" href="/Public/Home/css/Huploadify.css">
	<link rel="stylesheet" href="/Public/Home/css/bootstrap-datetimepicker.min.css">
	<link rel="stylesheet" href="/Public/Home/css/bootstrap-theme.min.css">
	<link rel="stylesheet" href="/Public/Home/css/oldcustomer/customer.css">
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
		<li><a href="/RMA">客诉处理</a></li>
		<li class="active">旧版客诉</li>
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
				

	<!-- 按钮栏 -->
	<div class="btn-group pull-left btn-group-edit" role="group">
		<button class="layui-btn layui-btn-primary" data-toggle="modal" data-target="#FilterModal">
			<span class="glyphicon glyphicon-th-list"></span> 筛选
		</button>
		<!--<a href="/customerChart" class="layui-btn layui-btn-primary">
			<span class="glyphicon glyphicon-signal"></span> 客诉统计
		</a>-->
	</div>

	<!-- 搜索栏 -->
	<div id="search" class="pull-right">
		<input type="text" name="search" id="SearchText" class="layui-input" value="<?php echo ($filter["searchtext"]); ?>" placeholder="请输入搜索内容">
		<span class="search-btn-box">
			<button id="submitSearch" type="submit"><span class="glyphicon glyphicon-search"></span></button>
		</span>
	</div>


	<div class="sr-only">
		<form id="SearchHiddenForm" action="/Customer" method="get">
			<input type="hidden" name="searchtext" value="" id="SearchHiddenText">
		</form>
	</div>

	<div class="clearfix" style="margin-bottom: 25px;"></div>


	<div class="customerContainer">
		<?php if(is_array($customer)): $i = 0; $__LIST__ = $customer;if( count($__LIST__)==0 ) : echo "$empty" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><div class="media media-edit">
				<a class="media-left media-top media-img" href="/Customer?salesperson=<?php echo ($value["salesperson"]); ?>">
					<img class="userFace" src="<?php echo ($value["face"]); ?>" alt="..." width="50" height="70">
				</a>
				<div class="media-body media-body-edit">
					<h4 class="media-heading media-heading-edit">
						<?php switch($value["status"]): case "-1": ?><span class="label label-danger">无法处理</span><?php break;?>
							<?php case "1": ?><span class="label label-primary">正在处理</span><?php break;?>
							<?php case "0": ?><span class="label label-success">已经处理</span><?php break; endswitch;?>
						<a href="/Customer/details/id/<?php echo ($value["id"]); ?>" class="title-text">
							<?php if(!empty($value["customer"])): echo ($value["customer"]); ?> &bull;<?php endif; ?>
							<?php if(!empty($value["pn"])): echo ($value["pn"]); ?> &bull;<?php endif; ?>
							<?php if(!empty($value["vendor"])): echo ($value["vendor"]); ?> &bull;<?php endif; ?>
							<?php echo ($value["error_message"]); ?>
						</a>
					</h4>
					<p class="time-box"><?php if(!empty($value["nickname"])): echo ($value["nickname"]); else: echo ($value["salesperson"]); endif; ?> ｜ <?php echo ($value["cc_time"]); ?></p>
					<div class="arrow-left-icon"></div>
				</div>
			</div><?php endforeach; endif; else: echo "$empty" ;endif; ?>
	</div>
	<!-- 数据分页 -->
	<div class="pageContainer">
		<ul class="pagination pagination-edit"><?php echo ($pageShow); ?></ul>
	</div>

	
	<!-- 回到顶部按钮 -->
	<div id="backtop">
		<span class="">回到<br/>顶部</span>
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
	

	<!-- 数据筛选模态框 -->
	<div class="modal fade" tabindex="1030" role="dialog" id="FilterModal" aria-labelledby="FilterModal" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span>
					</button>
					<h4 class="modal-title">筛选</h4>
				</div>
				<form class="form-inline" role="form" action="/Customer" method="get" id="FilterForm">
					<div class="modal-body">
						<div class="form-group form-group-margin">
							<label for="">状　　态&nbsp;</label>
							<div class="btn-group" id="statusList">
								<button type="button" class="btn btn-default <?php if(($filter["status"]) == "1"): ?>active<?php endif; ?>" state="1">正在处理</button>
								<button type="button" class="btn btn-default <?php if(($filter["status"]) == "-1"): ?>active<?php endif; ?>" state="-1">无法处理</button>
								<button type="button" class="btn btn-default <?php if(($filter["status"]) == "0"): ?>active<?php endif; ?>" state="0">已经处理</button>
								<button type="button" id="AllStatus" class="btn btn-default <?php if(!in_array(($filter["status"]), explode(',',"-1,0,1"))): ?>active<?php endif; ?>" state="2">所有状态</button>
							</div>
						</div>
						<div class="form-group form-group-margin">
							<label>客诉日期&nbsp;</label>
							<div class="input-group">
								<span class="input-group-addon">从</span>
								<input id="" type="text" class="form-control startDate" readonly name="startdate" value="<?php echo ($filter["startdate"]); ?>" placeholder="">
							</div>
							<div class="input-group">
								<span class="input-group-addon">至</span>
								<input id="" type="text" class="form-control endDate" readonly name="enddate" value="<?php echo ($filter["enddate"]); ?>" placeholder="" id="">
							</div>
						</div>
						<div class="form-group form-group-margin">
							<label>订单编号&nbsp;</label>
							<input type="text" class="form-control" name="order" value="<?php echo ($filter["sale_order"]); ?>" placeholder="" id="">　
							<label>销售人员&nbsp;</label>
							<input type="text" class="form-control" name="person" value="<?php echo ($filter["salesperson"]); ?>" placeholder="" id="">　
						</div>
						<div class="form-group form-group-margin">
							<label>客　　户&nbsp;</label>
							<input type="text" class="form-control" name="customer" value="<?php echo ($filter["customer"]); ?>" placeholder="" id="">　
							<label>设备厂商&nbsp;</label>
							<input type="text" class="form-control" name="vendor" value="<?php echo ($filter["vendor"]); ?>" placeholder="" id="">　
						</div>
					</div>
					<div class="modal-footer">
						<input type="hidden" name="status" value="<?php if(!in_array(($filter["status"]), explode(',',"-1,0,1"))): ?>2<?php else: echo ($filter["status"]); endif; ?>" id="filterStatus">
						<a href="/Customer" class="btn btn-default">清空条件</a>
						<input type="submit" class="btn btn-primary" value="确定">
					</div>
				</form>
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