<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>

	<meta charset="UTF-8">
	<!--<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">-->
	<link href="/Public/Home/img/favicon.ico" rel="shortcut icon">

	
	<title>添加样品</title>
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
	<link rel="stylesheet" href="/Public/Home/css/Huploadify.css">
	<link rel="stylesheet" href="/Public/Home/css/bootstrap-datetimepicker.min.css">
	<link rel="stylesheet" href="/Public/Home/css/sample.css">
<!-- 引入css文件 -->
	<script src="/Public/Home/js/jquery.min.js"></script>
	<script src="/Public/Home/js/bootstrap.min.js"></script>
	<script src="/Public/layui/layui.js"></script>
	<script src="/Public/Home/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/Public/Home/js/index.js"></script>

	
	<!--<script src="/Public/home/uploadify/jquery.uploadify.min.js"></script>-->
	<script src="/Public/Home/js/jquery.Huploadify.js"></script>
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
		<li class="active">添加样品</li>
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
				
	<div id="sampleContainer">
		<form id="sample-form" role="form" class="form-inline">

			<!-- 集所有动态添加DOM元素的总样品订单号 -->
			<div class="form-group form-group-total-order">
				<label>样品单号</label>
				<input type="text" name="totalorder" placeholder="请输入单号" class="form-control totalOrder">
			</div>
			<div class="clearfix"></div>

			<!-- 包裹层[包裹所有动态添加的元素] start -->
			<div id="AllDynamicBox">
				<input type="hidden" class="nowOrderNo" value="">
				<!-- 动态添加元素[以此DOM作为动态添加元素的基础] start -->
				<div class="panel panel-primary dynamicboxContainer">
					<div class="panel-heading"><i class="icon-file-alt"></i>&nbsp;&nbsp;产品型号<b class="orderNO"></b><span class="close removeOrder" title="删除该订单">&times;</span></div>
					<div class="panel-body">
						<div class="dynamicbox">
							<div class="form-group form-group-row">
								<label>选择产品</label>
								<input type="text" class="form-control btn-select-filter input-product-model" name="product" readonly>
								<!--<div class="btn-group">
									<button type="button" class="btn btn-default product-type-name">SFP</button>
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
										<span class="caret"></span>
									</button>
									<ul class="dropdown-menu product-type-list" role="menu">
										<?php if(is_array($typeList)): $i = 0; $__LIST__ = $typeList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li class="changeType"><a href="javascript:void(0);"><?php echo ($value["type"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
									</ul>
								</div>-->
								<input type="hidden" name="product-type-name" class="product-type-name" value="">
							</div>
							<div class="form-group form-group-row">
								<label>产品经理</label>
								<!--<div class="btn-group">
									<button type="button" class="btn btn-default input-product-model">APS31123CDL20</button>
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
										<span class="caret"></span>
									</button>
									<ul class="dropdown-menu product-list" role="menu">
										<?php if(is_array($pnList)): $i = 0; $__LIST__ = $pnList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li p_id="<?php echo ($value["pid"]); ?>" nickname="<?php echo ($value["nickname"]); ?>" manager="<?php echo ($value["manager"]); ?>" uid="<?php echo ($value["uid"]); ?>" pn="<?php echo ($value["pn"]); ?>"><a href="javascript:void(0);"><?php echo ($value["pn"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
									</ul>
									<input type="hidden" name="pid" value="<?php echo ($pnList["0"]["pid"]); ?>" class="hidden-input-pid">
								</div>-->
								<input type="text" class="form-control input-product-manager" readonly>
								<input type="hidden" name="pid" value="" class="hidden-input-pid">
								<input type="hidden" class="form-control input-product-model" readonly>
								<input type="hidden" class="form-control form-group-input-hidden-manager" readonly>
							</div>
							<div class="form-group form-group-row">
								<label>模块数量</label>
								<input type="text" class="form-control input-number" value="1" name="number">
							</div>

							<div class="form-group">
								<label>客户名称</label>
								<input type="text" class="form-control form-group-input-inline input-customer-name" name="customer" placeholder="例如 Orienta">
								<span class="help-block"></span>
							</div>
							<div class="form-group">
								<label>设备品牌</label>
								<input type="text" class="form-control form-group-input-inline input-equipment-brand" name="brand" placeholder="例如 Huawei">
						 		<span class="help-block"></span>
							</div>
							<div class="form-group">
								<label>设备型号</label>
								<input type="text" class="form-control form-group-input-inline input-equipment-model" name="model" placeholder="例如 MA5600T">
								<span class="help-block"></span>
							</div>
							<div class="form-group form-group-textarea">
								<textarea class="form-control form-group-textarea-inline textarea-comment" id="test-comment" name="comment" placeholder="备注信息"></textarea>
								<span class="help-block"></span>
							</div>
							<div class="form-group">
								<label>要求交期</label>
								<input type="text" class="form-control input-d-date" value="" name="d_date" readonly>
							</div>
						</div>
					</div>
				</div>
				<!-- 动态添加元素 end -->
			</div>
			<!-- 包裹层 end -->

			<!-- 提交所有动态添加元素的值 -->
			<div class="btn-box">
				<div class="sidebarAdd">
					<button type="button" class="btn btn-primary"><i class="icon-plus icon-2x"></i><br/>添加</button>
				</div>
				<!--<input type="file" name="attachment" id="fileinput" class="fileinput">-->
				<div id="fileinput"></div>
				<input id="aid" type="hidden" name="aid" value="">
				<input id="uid" type="hidden" name="uid" value="<?php echo ($_SESSION['user']['id']); ?>">
				<input type="reset" value="重置" class="btn btn-default btn-lg"> <input type="button" id="submitAllOrder" value="下单" class="btn btn-primary btn-lg">
			</div>

		</form>
		<div class="upload-file-box sr-only">
			<p class="attachment-list">附件列表[已上传<span class="attachment-number"></span>个附件]：</p>
			<ul class="upload-file-list"></ul>
		</div>
	</div>


	<div class="loading-modal sr-only" id="loading">
		<div class="loading-icon">
			<p><i class="icon-spinner icon-spin icon-4x"></i></p>
		</div>
	</div>

	<form class="sr-only" id="filterForm">
		<input type="hidden" name="type">
		<input type="hidden" name="wavelength">
		<input type="hidden" name="reach">
		<input type="hidden" name="connector">
		<input type="hidden" name="casetemp">
	</form>

	<!--交期事件选择-->
	<!--<script>
		$('.input-d-date').datetimepicker({
			language:  'zh-CN',
			format:'yyyy/mm/dd',
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
	</script>-->

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

	<!-- 消息提示模态框 -->
	<div class="modal fade bs-example-modal-lg" id="select-product" role="dialog">
		<div class="modal-dialog modal-lg modal-dialog-editsize">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					<span class="modal-title">选择产品型号</span>
				</div>
				<div class="modal-body filter-body">
					<ul id="conditon-list"></ul>
					<div class="filter-col">
						<span class="label-type">类型</span>
						<ul class="condition-type">
							<!--<?php if(is_array($filter["filterType"])): $i = 0; $__LIST__ = $filter["filterType"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(($i) <= "7"): ?><li onclick="filter('type','<?php echo ($value["type"]); ?>');"><?php echo ($value["type"]); ?></li><?php else: ?><li style="display:none;" class="default-hide-li" onclick="filter('type','<?php echo ($value["type"]); ?>');"><?php echo ($value["type"]); ?></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>-->
							<li onclick="filter('type','SFP')">SFP</li><li onclick="filter('type','SFP BIDI')">SFP BIDI</li><li onclick="filter('type','SFP Copper')">SFP Copper</li><li onclick="filter('type','SFP CWDM')">SFP CWDM</li><li onclick="filter('type','SFP DWDM')">SFP DWDM</li><li onclick="filter('type','CSFP')">CSFP</li>
							<p></p>
							<li style="display: none;" class="default-hide-li" onclick="filter('type','SFP+')">SFP+</li><li style="display: none;" class="default-hide-li" onclick="filter('type','SFP+ AOC')">SFP+ AOC</li><li style="display: none;" class="default-hide-li" onclick="filter('type','SFP+ BIDI')">SFP+ BIDI</li><li style="display: none;" class="default-hide-li" onclick="filter('type','SFP+ CWDM')">SFP+ CWDM</li><li style="display: none;" class="default-hide-li" onclick="filter('type','SFP+ DAC Active')">SFP+ DAC Active</li><li style="display: none;" class="default-hide-li" onclick="filter('type','SFP+ DAC Passive')">SFP+ DAC Passive</li><li style="display: none;" class="default-hide-li" onclick="filter('type','SFP+ DWDM')">SFP+ DWDM</li>
							<p></p>
							<li style="display: none;" class="default-hide-li" onclick="filter('type','XFP')">XFP</li><li style="display: none;" class="default-hide-li" onclick="filter('type','XFP BIDI')">XFP BIDI</li><li style="display: none;" class="default-hide-li" onclick="filter('type','XFP CWDM')">XFP CWDM</li><li style="display: none;" class="default-hide-li" onclick="filter('type','XFP DWDM')">XFP DWDM</li>
							<p></p>
							<li style="display: none;" class="default-hide-li" onclick="filter('type','QSFP+')">QSFP+</li><li style="display: none;" class="default-hide-li" onclick="filter('type','QSFP+ AOC')">QSFP+ AOC</li><li style="display: none;" class="default-hide-li" onclick="filter('type','QSFP+ DAC Active')">QSFP+ DAC Active</li><li style="display: none;" class="default-hide-li" onclick="filter('type','QSFP+ DAC Passive')">QSFP+ DAC Passive</li><li style="display: none;" class="default-hide-li" onclick="filter('type','QSFP+ to 4xSFP+')">QSFP+ to 4xSFP+</li>
							<p></p>
							<li style="display: none;" class="default-hide-li" onclick="filter('type','CFP2')">CFP2</li>
						</ul>
						<span flag="true" class="pull-right label-more"><i class="icon-angle-down">&nbsp;</i><span>更多<span></span>
					</div>
					<div class="filter-col">
						<span class="label-type">波长</span>
						<ul class="condition-wavelength">
							<?php if(is_array($filter["filterWavelength"])): $i = 0; $__LIST__ = $filter["filterWavelength"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(strlen($value['wavelength']) < 12 AND !strpos($value['wavelength'],'.')): if(($i) <= "9"): ?><li onclick="filter('wavelength','<?php echo ($value["wavelength"]); ?>',this);"><?php echo ($value["wavelength"]); ?></li><?php else: ?><li style="display:none;" class="default-hide-li" onclick="filter('wavelength','<?php echo ($value["wavelength"]); ?>',this);"><?php echo ($value["wavelength"]); ?></li><?php endif; endif; endforeach; endif; else: echo "" ;endif; ?>
						</ul>
						<span flag="true" class="pull-right label-more"><i class="icon-angle-down">&nbsp;</i><span>更多<span></span>
					</div>
					<div class="filter-col">
						<span class="label-type">距离</span>
						<ul class="condition-reach">
							<?php if(is_array($filter["filterReach"])): $i = 0; $__LIST__ = $filter["filterReach"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(($i) <= "7"): ?><li onclick="filter('reach','<?php echo ($value["reach"]); ?>');"><?php echo ($value["reach"]); ?></li><?php else: ?><li style="display:none;" class="default-hide-li" onclick="filter('reach','<?php echo ($value["reach"]); ?>');"><?php echo ($value["reach"]); ?></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
						</ul>
						<span flag="true" class="pull-right label-more"><i class="icon-angle-down">&nbsp;</i><span>更多<span></span>
					</div>
					<div class="filter-col">
						<span class="label-type">接口</span>
						<ul class="condition-connector">
							<?php if(is_array($filter["filterConnector"])): $i = 0; $__LIST__ = $filter["filterConnector"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li onclick="filter('connector','<?php echo ($value["connector"]); ?>');"><?php echo ($value["connector"]); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
						</ul>
						<!--<span class="pull-right label-more"><i class="icon-angle-down">&nbsp;</i><span>更多<span></span>-->
					</div>
					<div class="filter-col">
						<span class="label-type">环境</span>
						<ul class="condition-casetemp">
							<?php if(is_array($filter["filterCasetemp"])): $i = 0; $__LIST__ = $filter["filterCasetemp"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(($value["casetemp"]) == "C"): ?><li value="<?php echo ($value["casetemp"]); ?>" onclick="filter('casetemp','<?php echo ($value["casetemp"]); ?>');">C档（0-70°）</li><?php else: ?><li value="<?php echo ($value["casetemp"]); ?>" onclick="filter('casetemp','<?php echo ($value["casetemp"]); ?>');">I档（-40°-85°）</li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
						</ul>
						<!--<span class="pull-right label-more"><i class="icon-angle-down">&nbsp;</i><span>更多<span></span>-->
					</div>
					<hr>
					<ul id="product-list">
						<?php if(is_array($filterdata)): $i = 0; $__LIST__ = $filterdata;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(($i) < "18"): ?><li p_id="<?php echo ($value["pid"]); ?>" nickname="<?php echo ($value["nickname"]); ?>" manager="<?php echo ($value["manager"]); ?>" pn="<?php echo ($value["pn"]); ?>" type="<?php echo ($value["type"]); ?>"><?php echo ($value["pn"]); ?></li>
								<?php else: ?>
								<li class="sr-only" p_id="<?php echo ($value["pid"]); ?>" nickname="<?php echo ($value["nickname"]); ?>" manager="<?php echo ($value["manager"]); ?>" pn="<?php echo ($value["pn"]); ?>" type="<?php echo ($value["type"]); ?>"><?php echo ($value["pn"]); ?></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</div>
				<div class="modal-footer">

				</div>
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