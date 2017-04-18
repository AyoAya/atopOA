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
    <link rel="stylesheet" href="/Public/Home/css/customer-details.css">
    <link rel="stylesheet" href="/Public/Home/css/customer/detail.css">
    <link rel="stylesheet" href="/Public/Home/css/timeline.css"/>
    <link rel="stylesheet" href="/Public/Home/css/procedure.css"/>
<!-- 引入css文件 -->
	<script src="/Public/Home/js/jquery.min.js"></script>
	<script src="/Public/Home/js/bootstrap.min.js"></script>
	<script src="/Public/layui/layui.js"></script>
	<script src="/Public/Home/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/Public/Home/js/index.js"></script>

	
    <script src="/Public/Home/js/jquery.Huploadify.js"></script>
    <script src="/Public/Home/js/timeline.js"></script>
    <script src="/Public/Home/js/jquery.validate.min.js"></script>
    <script src="/Public/Home/js/jquery.form.js"></script>
    <script src="/Public/Home/js/bootstrap-datetimepicker.js"></script>
    <script src="/Public/Home/js/bootstrap-datetimepicker.zh-CN.js"></script>
    <script src="/Public/Home/js/jquery.toastmessage.js"></script>
    <script src="/Public/Home/js/customer.js"></script>
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
        <li><a href="/customer">客诉处理</a></li>
        <li class="active">客诉详情</li>
    </ol>
    <?php if(($details["status"]) == "0"): ?><div class="btn-group pull-right btn-group-edit" role="group">
            <a href="/index.php/Home/RMA/addCustomer" class="btn btn-primary" disabled="disabled">
                <span class="glyphicon glyphicon-plus"></span> 添加处理记录
            </a>
        </div>
        <?php else: ?>
        <div class="btn-group pull-right btn-group-edit" role="group">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#customer-modal">
                <span class="glyphicon glyphicon-plus"></span> 添加处理记录
            </button>
        </div><?php endif; ?>

		</div>

		<div id="content">
			<div class="container-fluid" id="content-box">
				
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
                <td>5195</td>
                <td>2017-03-27</td>
                <td>胡磊</td>
                <td>Levira</td>
                <td>无</td>
                <td>APSD34LM3CDLB2 , APSD35LM3CDLB2</td>
                <td>
                    <span class="label label-primary">正在处理</span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading panel-heading-edit"><h4>设备信息</h4></div>
        <div class="panel-body">
            设备厂商：<kbd>Netinsight</kbd>　　设备型号：<kbd>Nimbra</kbd>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading panel-heading-edit"><h4>错误现象</h4></div>
        <div class="panel-body">
            <code>I have plugged in the NetInsight coded SFPs to the Nimbra device, but they are not recognised at all..  I have also tried with different cards (STM1, STM4, STM16) and in different devices, and the same outcome..

                When they were plugged in into the Ethernet slot in Nimbra, then the „NetInsight“ coded device was reported to be 1000Base.. type and basic parameters were also given (e.g. they (ch 34 and 35 transceivers are with different vendor name etc))..

                Also, when it comes to Cisco SFPs, then when I plugged it in to Nimbra device (NetInisght), then in the beginning no DDM readings were given.. then we tried in Cisco switch, and it started to report the temperature values. After re-inserting to Nimbra device the DDM readings were also there..

                I hope that the transceivers are physically OK, but we need to start checking the code then at least for NetInsight-coded things..</code>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading panel-heading-edit"><h4>原因分析</h4></div>
        <div class="panel-body">
            暂时无法确定是否是对码有要求
        </div>
    </div>
    <?php if(!empty($details["comments"])): ?><div class="panel panel-default">
            <div class="panel-heading panel-heading-edit"><h4>备注信息</h4></div>
            <div class="panel-body">
                <?php echo ($details["comments"]); ?>
            </div>
        </div><?php endif; ?>
    <!--<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingOne">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        原因初步判断并尝试远程处理 <i class="icon-ok-sign text-success"></i>
                    </a>
                </h4>
            </div>
            <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                <div class="panel-body">
                    <div class="cl-bx">
                        <div class="lf-tx pull-left">
                            <div class="tx-bx">
                                <img src="/Public/Home/img/face/face_01.png" width="50" alt="">
                            </div>
                            <p class="yh-mz">Evan</p>
                        </div>
                        <div class="rg-if pull-left">
                            <div class="hf-nr">
                                考虑到客户那边有时能识别到模块，大多数的情况都不行，可以排除I2C上拉的原因。之前有过接触不良导致设备不识别模块的的客诉，（链接：http://61.139.89.33:8088/customerDetails/5191），不排除同样问题的可能性。
                            </div>
                            <div class="hfsj-bx">
                                <i class="icon-time"></i> 2017-03-17
                            </div>
                            <div class="sjx-fh"></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="cl-bx">
                        <div class="lf-tx pull-left">
                            <div class="tx-bx">
                                <img src="/Public/Home/img/face/face_01.png" width="50" alt="">
                            </div>
                            <p class="yh-mz">Evan</p>
                        </div>
                        <div class="rg-if pull-left">
                            <div class="hf-nr">
                                检查firmware版本，是0015_V12，已经修复在华为设备上读取有问题的bug。和肖艾佑确认，模块内部没有接I2C上拉电阻，不确定设备内部是否有上拉。
                            </div>
                            <div class="hfsj-bx">
                                <i class="icon-time"></i> 2017-03-17
                            </div>
                            <div class="sjx-fh"></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="cl-bx">
                        <div class="lf-tx pull-left">
                            <div class="tx-bx">
                                <img src="/Public/Home/img/face/face_01.png" width="50" alt="">
                            </div>
                            <p class="yh-mz">Evan</p>
                        </div>
                        <div class="rg-if pull-left">
                            <div class="hf-nr">
                                经过和Janno多次远程，总结如下：<br>
                                1：D34偶尔能被Netinsight设备识别，D35从未被识别；<br>
                                2：写Netinsight原厂模块的码，现象一致；<br>
                                3：写WTD模块的码，现象一致；<br>
                                4：WTD模块写ATOP的T码，能正常识别。
                            </div>
                            <div class="hfsj-bx">
                                <i class="icon-time"></i> 2017-03-17
                            </div>
                            <div class="sjx-fh"></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="cl-bx">
                        <div class="lf-tx pull-left">
                            <div class="tx-bx">
                                <img src="/Public/Home/img/face/face_01.png" width="50" alt="">
                            </div>
                            <p class="yh-mz">Evan</p>
                        </div>
                        <div class="rg-if pull-left">
                            <div class="hf-nr">
                                新客诉。
                            </div>
                            <div class="hfsj-bx">
                                <i class="icon-time"></i> 2017-03-17
                            </div>
                            <div class="sjx-fh"></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingTwo">
                <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        初步RMA分析报告 <i class="icon-ok-sign text-success"></i>
                    </a>
                </h4>
            </div>
            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                <div class="panel-body">
                    <div class="cl-bx">
                        <div class="lf-tx pull-left">
                            <div class="tx-bx">
                                <img src="/Public/Home/img/face/face_01.png" width="50" alt="">
                            </div>
                            <p class="yh-mz">Evan</p>
                        </div>
                        <div class="rg-if pull-left">
                            <div class="hf-nr">
                                检查firmware版本，是0015_V12，已经修复在华为设备上读取有问题的bug。和肖艾佑确认，模块内部没有接I2C上拉电阻，不确定设备内部是否有上拉。
                            </div>
                            <div class="hfsj-bx">
                                <i class="icon-time"></i> 2017-03-17
                            </div>
                            <div class="sjx-fh"></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="cl-bx">
                        <div class="lf-tx pull-left">
                            <div class="tx-bx">
                                <img src="/Public/Home/img/face/face_01.png" width="50" alt="">
                            </div>
                            <p class="yh-mz">Evan</p>
                        </div>
                        <div class="rg-if pull-left">
                            <div class="hf-nr">
                                经过和Janno多次远程，总结如下：<br>
                                1：D34偶尔能被Netinsight设备识别，D35从未被识别；<br>
                                2：写Netinsight原厂模块的码，现象一致；<br>
                                3：写WTD模块的码，现象一致；<br>
                                4：WTD模块写ATOP的T码，能正常识别。
                            </div>
                            <div class="hfsj-bx">
                                <i class="icon-time"></i> 2017-03-17
                            </div>
                            <div class="sjx-fh"></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="headingThree">
                <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        协助处理并审核RMA报告 <i class="icon-exclamation-sign text-primary"></i>
                    </a>
                </h4>
            </div>
            <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                <div class="panel-body">
                    <div class="cl-bx">
                        <div class="lf-tx pull-left">
                            <div class="tx-bx">
                                <img src="/Public/Home/img/face/face_01.png" width="50" alt="">
                            </div>
                            <p class="yh-mz">Evan</p>
                        </div>
                        <div class="rg-if pull-left">
                            <div class="hf-nr">
                                目前仍有287pcs (序列号含14开头） 不能工作，因为年前来不及换货，所以孙彬尝试更改，年后再统一换货
                            </div>
                            <div class="hfsj-bx">
                                <i class="icon-time"></i> 2017-03-17
                            </div>
                            <div class="sjx-fh"></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="cl-bx">
                        <div class="lf-tx pull-left">
                            <div class="tx-bx">
                                <img src="/Public/Home/img/face/face_01.png" width="50" alt="">
                            </div>
                            <p class="yh-mz">Evan</p>
                        </div>
                        <div class="rg-if pull-left">
                            <div class="hf-nr">
                                下退换货申请单 RMA141-ATG-A，先发1对给客户测试，如果可以使用后续再发
                            </div>
                            <div class="hfsj-bx">
                                <i class="icon-time"></i> 2017-03-17
                            </div>
                            <div class="sjx-fh"></div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>-->

    <!-- 步骤插件 -->
    <div class="procedure">
        <table class="procedure-table">
            <tr class="procedure-wrap">
                <td class="active">
                    <div class="procedure-title">下单</div>
                    <div class="procedure-step">&radic;</div>
                    <div class="procedure-info">2017-04-06 14:56</div>
                    <div class="procedure-hollow"></div>
                </td>
                <td class="active">
                    <div class="procedure-title">审核</div>
                    <div class="procedure-step">&radic;</div>
                    <div class="procedure-info">2017-04-06 14:56</div>
                    <div class="procedure-hollow"></div>
                </td>
                <td>
                    <div class="procedure-title">物料</div>
                    <div class="procedure-step">3</div>
                    <div class="procedure-info">2017-04-06 14:56</div>
                    <div class="procedure-hollow"></div>
                </td>
                <td>
                    <div class="procedure-title">制作</div>
                    <div class="procedure-step">4</div>
                    <div class="procedure-info">2017-04-06 14:56</div>
                    <div class="procedure-hollow"></div>
                </td>
                <td>
                    <div class="procedure-title">测试</div>
                    <div class="procedure-step">5</div>
                    <div class="procedure-info">2017-04-06 14:56</div>
                    <div class="procedure-hollow"></div>
                </td>
                <td>
                    <div class="procedure-title">发货</div>
                    <div class="procedure-step">6</div>
                    <div class="procedure-info">2017-04-06 14:56</div>
                    <div class="procedure-hollow"></div>
                </td>
                <td>
                    <div class="procedure-title">反馈</div>
                    <div class="procedure-step">7</div>
                    <div class="procedure-info">2017-04-06 14:56</div>
                    <div class="procedure-hollow"></div>
                </td>
            </tr>
        </table>
    </div>

    <!-- 时间轴插件 -->
    <div class="timeline">
        <div class="timeline-heading">
            <div class="timeline-table"><!-- 如果要隐藏子节点列表为该元素添加 timeline-hide -->
                <div class="timeline-title-box">
                    <div class="timeline-title pull-left">
                        <div class="timeline-step">1</div>
                    </div>
                    <div class="timeline-title-text pull-left">
                        <span>原因初步判定并尝试远程处理 <i class="icon-caret-down"></i></span>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <table class="timeline-table">
                    <tr>
                        <td class="timeline-info">
                            <div class="timeline-time-big">03-19</div>
                            <div class="timeline-time-small">2017</div>
                            <div class="timeline-circle"></div>
                        </td>
                        <td class="timeline-content">
                            检查firmware版本，是0015_V12，已经修复在华为设备上读取有问题的bug。<br>
                            和肖艾佑确认，模块内部没有接I2C上拉电阻，不确定设备内部是否有上拉。
                            <div class="timeline-other"><!--<img src="/Public/Home/img/face/face_05.png" width="30" alt="">-->李平<span class="timeline-divider">|</span>2017-04-06 14:25</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="timeline-info">
                            <div class="timeline-time-big">03-27</div>
                            <div class="timeline-time-small">2017</div>
                            <div class="timeline-circle"></div>
                        </td>
                        <td class="timeline-content">
                            考虑到客户那边有时能识别到模块，大多数的情况都不行，可以排除I2C上拉的原因。之前有过接触不良导致设备不识别模块的的客诉，（链接：http://61.139.89.33:8088/customerDetails/5191），不排除同样问题的可能性。
                            <div class="timeline-other"><!--<img src="/Public/Home/img/face/face_05.png" width="30" alt="">-->李平<span class="timeline-divider">|</span>2017-04-06 14:25</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="timeline-info">
                            <div class="timeline-time-big">03-29</div>
                            <div class="timeline-time-small">2017</div>
                            <div class="timeline-circle"></div>
                        </td>
                        <td class="timeline-content">
                            经过和Janno多次远程，总结如下：<br>
                            1：D34偶尔能被Netinsight设备识别，D35从未被识别；<br>
                            2：写Netinsight原厂模块的码，现象一致；<br>
                            3：写WTD模块的码，现象一致；<br>
                            4：WTD模块写ATOP的T码，能正常识别。
                            <div class="timeline-other"><!--<img src="/Public/Home/img/face/face_05.png" width="30" alt="">-->李平<span class="timeline-divider">|</span>2017-04-06 14:25</div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="timeline-table"><!-- 如果要隐藏子节点列表为该元素添加 timeline-hide -->
                <div class="timeline-title-box">
                    <div class="timeline-title pull-left">
                        <div class="timeline-step">2</div>
                    </div>
                    <div class="timeline-title-text pull-left">
                        <span>RMA分析报告 <i class="icon-caret-down"></i></span>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <table class="timeline-table">
                    <tr>
                        <td class="timeline-info">
                            <div class="timeline-time-big">04-01</div>
                            <div class="timeline-time-small">2017</div>
                            <div class="timeline-circle"></div>
                        </td>
                        <td class="timeline-content">
                            检查firmware版本，是0015_V12，已经修复在华为设备上读取有问题的bug。<br>
                            和肖艾佑确认，模块内部没有接I2C上拉电阻，不确定设备内部是否有上拉。
                            <div class="timeline-other"><!--<img src="/Public/Home/img/face/face_05.png" width="30" alt="">-->李平<span class="timeline-divider">|</span>2017-04-06 14:25</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="timeline-info">
                            <div class="timeline-time-big">04-03</div>
                            <div class="timeline-time-small">2017</div>
                            <div class="timeline-circle"></div>
                        </td>
                        <td class="timeline-content">
                            考虑到客户那边有时能识别到模块，大多数的情况都不行，可以排除I2C上拉的原因。之前有过接触不良导致设备不识别模块的的客诉，（链接：http://61.139.89.33:8088/customerDetails/5191），不排除同样问题的可能性。
                            <div class="timeline-other"><!--<img src="/Public/Home/img/face/face_05.png" width="30" alt="">-->李平<span class="timeline-divider">|</span>2017-04-06 14:25</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="timeline-info">
                            <div class="timeline-time-big">04-06</div>
                            <div class="timeline-time-small">2017</div>
                            <div class="timeline-circle"></div>
                        </td>
                        <td class="timeline-content">
                            经过和Janno多次远程，总结如下：<br>
                            1：D34偶尔能被Netinsight设备识别，D35从未被识别；<br>
                            2：写Netinsight原厂模块的码，现象一致；<br>
                            3：写WTD模块的码，现象一致；<br>
                            4：WTD模块写ATOP的T码，能正常识别。
                            <div class="timeline-other"><!--<img src="/Public/Home/img/face/face_05.png" width="30" alt="">-->李平<span class="timeline-divider">|</span>2017-04-06 14:25</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!--<script>
        $('#accordion').on('hidden.bs.collapse', function ( e ) {
            // do something…
            console.log(e.target);
        })
    </script>-->

    <!-- 新增客诉模态框 -->
    <div class="modal fade bs-example-modal-lg" id="customer-modal" role="dialog">
        <div class="modal-dialog modal-lg" id="customer-modal-width">
            <div class="modal-content">
                <form class="form-inline" role="form" id="processingRecords">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                            <span class="sr-only">close</span>
                        </button>
                        <h4 class="modal-title">添加处理记录</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>处理日期&nbsp;</label>
                            <div class="input-group date form_date" data-date="" data-date-format="dd MM yyyy" data-link-format="yyyy-mm-dd" data-link-field="dtp_input2" id="datetimepicker">
                                <input class="form-control" type="text" name="log_date" id="deal-date" readonly placeholder="选择处理日期">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                            </div>
                            <div class="help-block help-block-date help-block-position" id="change-date-remove"></div>
                        </div>
                        <div class="form-group">
                            <label>　　状态&nbsp;</label>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default" id="deal-text">正在处理</button>
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu" id="dropdown-list">
                                    <li status="1"><a href="javascript:void(0);">正在处理</a></li>
                                    <li status="0"><a href="javascript:void(0);">已经处理</a></li>
                                    <li status="-1"><a href="javascript:void(0);">无法处理</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>　　处理人&nbsp;</label>
                            <input type="text" name="recorder" value="<?php echo ($_SESSION['user']['account']); ?>" class="form-control" id="recorder">
                            <div class="help-block help-block-recorder help-block-position"></div>
                        </div>
                        <textarea class="form-control block-textarea"  placeholder="输入最新的处理记录" name="log_content" id="log_content"></textarea>
                        <div class="help-block help-block-textarea" id="log-content-error"></div>
                        <div class="form-group form-group-file-upload">
                            <label>附件</label>
                            <p class="help-block" id="help-block-info">已上传 <b id="fileTotal">0</b> 个文件</p>
                            <div id="file"></div>　　
                            <div id="file-preview">
                                <ul id="file-list"></ul>
                            </div>
                        </div>
                        <input type="hidden" name="cc_id" value="<?php echo ($_GET['id']); ?>" id="cc_id">
                        <input type="hidden" id="dtp_input2" value="" />
                        <input type="hidden" name="status" value="1" id="status">
                        <input type="hidden" name="attachment" value="" id="file-data">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                        <input type="submit" class="btn btn-primary" value="确定">
                    </div>
                </form>
            </div>
        </div>
    </div>

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