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

	
	<link rel="stylesheet" href="/Public/Home/css/bootstrap-datetimepicker.min.css">
	<link rel="stylesheet" href="/Public/Home/css/oldcustomer/addCustomer.css">
<!-- 引入css文件 -->
	<script src="/Public/Home/js/jquery.min.js"></script>
	<script src="/Public/Home/js/bootstrap.min.js"></script>
	<script src="/Public/layui/layui.js"></script>
	<script src="/Public/Home/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/Public/Home/js/index.js"></script>

	
	<script src="/Public/Home/js/jquery.validate.min.js" charset="UTF-8"></script>
	<script src="/Public/Home/js/jquery.form.js" charset="UTF-8"></script>
	<script src="/Public/Home/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
	<script src="/Public/Home/js/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
	<script src="/Public/Home/js/jquery.Huploadify.js"></script>
	<script src="/Public/Home/js/oldcustomer/customer.js" charset="UTF-8"></script>
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
		<li><a href="/Customer">客诉处理</a></li>
		<li class="active">新增客诉</li>
	</ol>

		</div>

		<div id="content">
			<div class="container-fluid" id="content-box">
				
	<div class="add-customer">
		<form role="form" id="form">
			<div class="form-group form-group-edit col-lg-6" >
				<label>客诉日期</label>
				<div class="input-group date form_date" data-date="" data-date-format="dd MM yyyy" data-link-format="yyyy-mm-dd" data-link-field="dtp_input2" id="datetimepicker2">
					<input class="form-control" id="cc_time" type="text" readonly name="cc_time" placeholder="请选择日期" />
					<span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
				</div>
				<input type="hidden" id="dtp_input2" value="" />
			</div>
			<label>销售人员</label>
			<div class="input-group input-group-edit col-lg-6">
				<input class="form-control" id="salesperson" type="text" name="salesperson" placeholder="请选择销售人员" readonly />
				<span class="input-group-btn">
					<button class="btn btn-default" data-toggle="modal" data-target="#sales-modal" type="button">选择</button>
				</span>
			</div>
			<p class="help-block help-block-color help-block-edit" id="salesperson_error"></p>
			<div class="form-group form-group-edit">
				<label>客户</label>
				<input class="form-control" id="customer" type="text" name="customer" placeholder="例如 Orienta" />
				<p class="help-block help-block-color"></p>
			</div>
			<div class="form-group form-group-edit">
				<label>订单号</label>
				<input class="form-control" id="sale_order" type="text" name="sale_order" placeholder="例如 188-Orienta-A" />
				<p class="help-block help-block-color"></p>
			</div>
			<div class="form-group form-group-edit">
				<label>产品信息</label>
				<div class="input-group col-lg-12">
					<input class="form-control" id="pn" type="text" name="pn" placeholder="例如 APCSFP43123CDL20" disabled/>
					<span class="input-group-btn">
						<button type="button" id="clear-product" class="btn btn-danger" data-dismiss="modal" data-toggle="tooltip" data-placement="top" title="清空" onMouseOver="$(this).tooltip('show')"><i class="icon-remove"></i></button>
						<button class="btn btn-default" id="select-product-btn" type="button">添加</button>
					</span>
				</div>
				<p class="help-block help-block-color"></p>
			</div>
			<div class="form-group form-group-edit">
				<label>设备厂商</label>
				<input class="form-control" id="vendor" type="text" name="vendor" placeholder="例如 Huawei" />
				<p class="help-block help-block-color"></p>
			</div>
			<div class="form-group form-group-edit">
				<label>设备型号</label>
				<input class="form-control" id="model" type="text" name="model" placeholder="例如 MA5600T" />
				<p class="help-block help-block-color"></p>
			</div>
			<div class="form-group form-group-edit">
				<label>错误信息</label>
				<textarea class="form-control" id="error_message" name="error_message" placeholder="输入详细报错信息，包括交换机报错信息、客户反馈的详细信息等等"></textarea>
				<p class="help-block help-block-color"></p>
			</div>
			<div class="form-group form-group-edit">
				<label>原因分析</label>
				<textarea class="form-control" id="reason" name="reason" placeholder="根据客户反馈的信息做出的初步分析原因"></textarea>
				<p class="help-block help-block-color"></p>
			</div>
			<div class="form-group form-group-edit">
				<label>备注</label>
				<textarea class="form-control" id="comments" name="comments" placeholder="其他补充信息，例如故障产品的数量，序列号等。"></textarea>
				<p class="help-block help-block-color"></p>
			</div>
			<div class="form-group btn-box form-group-edit">
				<input type="hidden" name="userAccount" value="" id="userAccount">
				<button type="reset" class="btn btn-default btn-lg">重置</button>
				<button type="submit" class="btn btn-primary btn-lg" id="submit-btn">确定</button>
			</div>
			<input type="hidden" id="manager-id">
		</form>
	</div>
	
	<!-- 销售人员选择模态框 -->
	<div class="modal fade bs-example-modal-lg" id="sales-modal" role="dialog">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span>
						<span class="sr-only">close</span>
					</button>
					<h4 class="modal-title">选择销售人员</h4>
				</div>
				<div class="modal-body">
					<ul id="user-list">
						<?php if(is_array($userlist)): $i = 0; $__LIST__ = $userlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li account="<?php echo ($value["account"]); ?>"><?php echo ($value["nickname"]); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
					</ul>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary done-btn" data-dismiss="modal">确定</button>
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

	<!-- 消息提示模态框 -->
	<div class="modal fade bs-example-modal-lg" id="product-modal" role="dialog">
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

	<div id="loading">
		<div class="loading-icon">
			<i class="icon-spinner icon-spin"></i>
		</div>
	</div>

	<form class="sr-only" id="filterForm">
		<input type="hidden" name="type">
		<input type="hidden" name="wavelength">
		<input type="hidden" name="reach">
		<input type="hidden" name="connector">
		<input type="hidden" name="casetemp">
	</form>
	
	<script>
	$('.form_date').datetimepicker({
        language:  'zh-CN',
        format:'yyyy-mm-dd',
        weekStart: 1,
        todayBtn:  1,
		autoclose: 1,
		todayHighlight: 1,
		endDate : new Date(),
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