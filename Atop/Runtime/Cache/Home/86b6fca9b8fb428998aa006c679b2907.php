<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>

	<meta charset="UTF-8">
	<!--<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">-->
	<link href="/Public/Home/img/favicon.ico" rel="shortcut icon">

	
	<title>产品管理</title>
<!-- 网站标题 -->
	<link rel="stylesheet" href="/Public/layui/css/layui.css">
	<link rel="stylesheet" href="/Public/Home/css/bootstrap.min.css">
	<link rel="stylesheet" href="/Public/Home/css/font-awesome.min.css">
	<link rel="stylesheet" href="/Public/Home/css/jquery.mCustomScrollbar.css">
	<link rel="stylesheet" href="/Public/Home/css/iconfont.css">
	<link rel="stylesheet" href="/Public/Home/css/basic.css">
	<link rel="stylesheet" href="/Public/Home/css/<?php echo ($face["theme"]); ?>/index.css">
	<link rel="stylesheet" href="/Public/Home/css/promotion_index.css">

	
	<link rel="stylesheet" href="/Public/layui/css/layui.css">
	<link rel="stylesheet" href="/Public/Home/css/product/index.css">
<!-- 引入css文件 -->
	<script src="/Public/Home/js/jquery.min.js"></script>
	<script src="/Public/Home/js/bootstrap.min.js"></script>
	<script src="/Public/layui/layui.js"></script>
	<script src="/Public/Home/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/Public/Home/js/index.js"></script>

	
	<script src="/Public/layui/layui.js"></script>
	<script src="/Public/Home/js/product.js"></script>
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
		<li class="active">产品管理</li>
	</ol>

		</div>

		<div id="content">
			<div class="container-fluid" id="content-box">
				

	<!-- 筛选数据 -->
	<!--<form class="layui-form filter-box">
		<div class="layui-inline">
			<label class="layui-form-label">封装类型</label>
			<div class="layui-input-inline">
				<select name="type" lay-verify="required">
					<?php if(is_array($filter["type"])): $i = 0; $__LIST__ = $filter["type"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><option value="<?php echo ($value["type"]); ?>"><?php echo ($value["type"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
			</div>
		</div>
		<div class="layui-inline">
			<label class="layui-form-label">波长</label>
			<div class="layui-input-inline">
				<select name="type" lay-verify="required">
					<?php if(is_array($filter["wavelength"])): $i = 0; $__LIST__ = $filter["wavelength"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><option value="<?php echo ($value["wavelength"]); ?>"><?php echo ($value["wavelength"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
			</div>
		</div>
		<div class="layui-inline">
			<label class="layui-form-label">波长</label>
			<div class="layui-input-inline">
				<select name="type" lay-verify="required">
					<?php if(is_array($filter["wavelength"])): $i = 0; $__LIST__ = $filter["wavelength"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><option value="<?php echo ($value["wavelength"]); ?>"><?php echo ($value["wavelength"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
			</div>
		</div>
	</form>-->


	<?php if(($position) == "产品经理"): ?><div class="pull-left">
			<a href="/Product/add" class="layui-btn layui-btn-primary">
				<i class="icon-plus"></i> 添加产品
			</a>
		</div><?php endif; ?>


	<!-- 搜索 -->
	<form class="layui-form filter-box">
		<div class="layui-inline pull-right">
			<div class="layui-input-inline layui-input-inline-search">
				<input type="text" name="search" placeholder="输入产品型号" value="<?php echo ($search); ?>" class="layui-input input-search">
				<button type="submit" class="layui-btn"><i class="icon-search"></i></button>
			</div>
		</div>
		<div class="clearfix"></div>
	</form>

	<table class="layui-table layui-table-rewrite">
		<colgroup>
			<col width="54">
		</colgroup>
		<thead>
			<tr>
				<th class="th_number">序号</th>
				<th class="th_type">封装类型</th>
				<th class="th_pn">产品型号</th>
				<th class="th_rate">速率</th>
				<th class="th_wavelength">波长</th>
				<th class="th_component">光组件</th>
				<th class="th_txpwr">发端功率</th>
				<th class="th_rxsens">接收灵敏度</th>
				<th class="th_connector">接口</th>
				<th class="th_casetemp">温度范围</th>
				<th class="th_reach">距离</th>
				<th class="th_multirate">多速率支持</th>
				<th class="th_manager">产品经理</th>
				<!--<?php if(($position) == "产品经理"): ?><th class="th_operation">操作</th><?php endif; ?>-->
			</tr>
		</thead>
		<tbody>
			<?php if(is_array($products)): $i = 0; $__LIST__ = $products;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><tr>
					<td>
						<?php if($_GET['p']AND $_GET['p']!= 1): echo ($pagenumber*$limitsize+$i); ?>
							<?php else: ?>
							<?php echo ($i); endif; ?>
					</td>
					<td><?php echo ($value["type"]); ?></td>
					<td><?php echo ($value["pn"]); ?></td>
					<td><?php echo ($value["rate"]); ?></td>
					<td><?php echo ($value["wavelength"]); ?></td>
					<td><?php echo ($value["component"]); ?></td>
					<td><?php echo ($value["txpwr"]); ?></td>
					<td><?php echo ($value["rxsens"]); ?></td>
					<td><?php echo ($value["connector"]); ?></td>
					<td><?php echo ($value["casetemp"]); ?></td>
					<td><?php echo ($value["reach"]); ?></td>
					<td><?php echo ($value["multirate"]); ?></td>
					<td><?php echo ($value["nickname"]); ?></td>
					<!--<?php if(($position) == "产品经理"): ?><td>
							<div class="layui-btn-group">
								<a href="/Product/update/id/<?php echo ($value["id"]); ?>" class="layui-btn layui-btn-small layui-btn-primary" title="编辑"><i class="icon-pencil"></i></a>
								<a href="/Product/delete/id/<?php echo ($value["id"]); ?>" class="layui-btn layui-btn-small layui-btn-primary" title="删除"><i class="icon-trash"></i></a>
							</div>
						</td><?php endif; ?>-->
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		</tbody>
	</table>

	<!-- 分页 -->
	<ul class="pagination pagination-edit"><?php echo ($pageShow); ?></ul>


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