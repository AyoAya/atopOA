<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>

	<meta charset="UTF-8">
	<!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
	<link href="/Public/Home/img/favicon.ico" rel="shortcut icon">

	
    <title>ATOP</title>
<!-- 网站标题 -->
	<link rel="stylesheet" href="/Public/Home/css/bootstrap.min.css">
	<link rel="stylesheet" href="/Public/Home/css/font-awesome.min.css">
	<link rel="stylesheet" href="/Public/Home/css/jquery.mCustomScrollbar.css">
	<link rel="stylesheet" href="/Public/Home/css/iconfont.css">
	<link rel="stylesheet" href="/Public/Home/css/basic.css">
	<link rel="stylesheet" href="/Public/Home/css/<?php echo ($face["theme"]); ?>/index.css">
	<link rel="stylesheet" href="/Public/Home/css/promotion_index.css">

	
    <style>
        .wrap-container {
            padding-bottom: 50px;
        }
        .table tr,.table th {
            text-align: center;
        }
        #logistics-list li {
            padding: 5px 0;
        }
        small {
            font-size: 14px !important;
            position: relative;
            top: 18px;
        }
    </style>
<!-- 引入css文件 -->
	<script src="/Public/Home/js/jquery.min.js"></script>
	<script src="/Public/Home/js/bootstrap.min.js"></script>
	<script src="/Public/Home/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/Public/Home/js/index.js"></script>

	
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
					<li id="Reserach">
						<div class="reserchBTN">
							<i class="icon-wrench"></i><span>研发管理&nbsp;&nbsp;<span class="icon-caret-right"></span></span>
						</div>
						<div id="ResearchChildMenu">
							<span><a href="/Project"><i class="icon-globe"></i>&nbsp;&nbsp;项目管理</a></span>
							<span><a href="/Sample"><i class="icon-beaker"></i>&nbsp;&nbsp;样品管理</a></span>
							<span><a href="/Compatibility"><i class="icon-bar-chart"></i>&nbsp;&nbsp;兼容表</a></span>
						</div>
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
					<li><a href="/Customer"><i class="icon-comments-alt"></i><span>客诉处理</span></a></li>
					<li><a href="/Manage"><i class="icon-github-alt"></i><span>用户管理</span></a></li>
					<?php if(($_SESSION['user']['account']) == "admin"): ?><li><a href="/System"><i class="icon-cog"></i><span>系统</span></a></li><?php endif; ?>
				</ul>
			</div>
		



		<div id="header">
			
    <ol class="breadcrumb breadcrumb-edit">
        <li><a href="/Sample">样品管理</a></li>
        <li class="active"><a href="/sampleDetails/<?php echo ($order); ?>">样品详情</a></li>
        <li class="active">物流状态</li>
    </ol>

		</div>

		<div id="content">
			<div class="container-fluid" id="content-box">
				

    <div class="col-lg-10 col-lg-offset-1 wrap-container">
        <div class="page-header">
            <h2>基本信息</h2>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>订单号</th>
                    <th>物流公司</th>
                    <th>运单号</th>
                    <th>订单创建时间</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo ($sample["totalorder"]); ?></td>
                    <td>
                        <?php switch($sample["logistics"]): case "shunfeng": ?>顺丰物流<?php break;?>
                            <?php case "dhl": ?>DHL<?php break;?>
                            <?php case "fedex": ?>FedEx<?php break;?>
                            <?php case "ups": ?>UPS<?php break;?>
                            <?php case "shentong": ?>申通快递<?php break;?>
                            <?php case "yuantong": ?>圆通速递<?php break;?>
                            <?php case "zhongtong": ?>中通速递<?php break; endswitch;?>
                    </td>
                    <td><?php echo ($sample["awb"]); ?></td>
                    <td><?php echo (date("Y-m-d H:i:s",$sample["createtime"])); ?></td>
                </tr>
            </tbody>
        </table>
        <div class="page-header">
            <h2>物流状态<small class="pull-right"><a href="https://www.kuaidi100.com/" target="_blank">无法查询？转到快递100查询</a></small></h2>
        </div>
        <?php if(($logistics["status"]) == "200"): ?><ul id="logistics-list">
                <?php if(is_array($logistics["data"])): $i = 0; $__LIST__ = $logistics["data"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(($i) == "1"): ?><li><mark><b><i class="icon-time"></i>&nbsp;<?php echo ($value["time"]); ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ($value["context"]); ?></b></mark></li>
                    <?php else: ?>
                        <li><i class="icon-time"></i>&nbsp;<?php echo ($value["time"]); ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ($value["context"]); ?></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
            </ul>
        <?php else: ?>
            <?php echo ($logistics["message"]); endif; ?>
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

	<!-- Before Loading -->
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