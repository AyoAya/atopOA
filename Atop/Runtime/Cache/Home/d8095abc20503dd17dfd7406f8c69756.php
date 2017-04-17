<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>

	<meta charset="UTF-8">
	<!--<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">-->
	<link href="/Public/Home/img/favicon.ico" rel="shortcut icon">

	
    <title>添加兼容记录</title>
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
    <link rel="stylesheet" href="/Public/Home/css/compatible-add.css">
<!-- 引入css文件 -->
	<script src="/Public/Home/js/jquery.min.js"></script>
	<script src="/Public/Home/js/bootstrap.min.js"></script>
	<script src="/Public/layui/layui.js"></script>
	<script src="/Public/Home/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/Public/Home/js/index.js"></script>

	
    <script src="/Public/Home/js/jquery.toastmessage.js"></script>
    <script src="/Public/Home/js/compatible.js"></script>
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
        <li><a href="/Compatibility">兼容表</a></li>
        <li class="active">添加兼容记录</li>
    </ol>

		</div>

		<div id="content">
			<div class="container-fluid" id="content-box">
				

    <div class="compatible-container">
        <form role="form" class="compatible-form">
            <div class="form-group">
                <label>产品:</label>
                <input type="text" class="form-control" readonly name="pn" id="pn_value" data-toggle="modal" data-target="#product-modal">
                <input type="hidden" id="pn">
            </div>
            <div class="form-group">
                <label>设备厂商:</label>
                <input type="text" class="form-control" readonly name="vendor" id="vendor_value" data-toggle="modal" data-target="#vendorModal">
                <input type="hidden" id="vendor">
            </div>
            <div class="form-group">
                <label>型号:</label>
                <input type="text" class="form-control" name="model" id="model">
            </div>
            <div class="form-group">
                <label>状态:</label>
                <div class="btn-group">
                    <button type="button" class="btn btn-default stateText" id="stateText">完全兼容</button>
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu state" role="menu" id="state">
                        <li state="1"><a href="javascript:void(0);">完全兼容</a></li>
                        <li state="2"><a href="javascript:void(0);">能够使用</a></li>
                        <li state="3"><a href="javascript:void(0);">不能兼容</a></li>
                    </ul>
                </div>
                <input type="hidden" name="state" id="state_flag" value="1">
            </div>
            <div class="form-group">
                <label>版本:</label>
                <input type="text" class="form-control" id="version" name="version">
            </div>
            <div class="form-group">
                <label>原厂型号:</label>
                <input type="text" class="form-control" id="original" name="original">
            </div>
            <div class="form-group">
                <label>备注:</label>
                <textarea name="comment" class="form-control" id="comment"></textarea>
            </div>
            <div class="form-group pull-right">
                <input type="reset" class="btn btn-default btn-reset" value="重置">
                <button type="button" class="btn btn-primary btn-submit">添加</button>
            </div>
        </form>
    </div>

    <!-- 产品型号选择模态框 -->
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
            </div>
        </div>
    </div>

    <form class="sr-only" id="filterForm">
        <input type="hidden" name="type">
        <input type="hidden" name="wavelength">
        <input type="hidden" name="reach">
        <input type="hidden" name="connector">
        <input type="hidden" name="casetemp">
    </form>

    <!-- 设备厂商选择模态框 -->
    <div class="modal fade bs-example-modal-lg" id="vendorModal" tabindex="-1" role="dialog" aria-labelledby="vendorModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" class="vendorModalLabel">选择设备厂商</h4>
                </div>
                <div class="modal-body">
                    <ul id="brandList">
                        <?php if(is_array($vendor)): $i = 0; $__LIST__ = $vendor;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li belong="<?php echo ($value["id"]); ?>"><?php echo ($value["brand"]); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" id="prompt" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content" id="prompt-content">
                <i class="icon-ok-sign"></i>
                <span>添加成功</span>
            </div>
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