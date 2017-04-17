<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>

	<meta charset="UTF-8">
	<!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
	<link href="/Public/Home/img/favicon.ico" rel="shortcut icon">

	
    <title>编辑项目</title>
<!-- 网站标题 -->
	<link rel="stylesheet" href="/Public/Home/css/bootstrap.min.css">
	<link rel="stylesheet" href="/Public/Home/css/font-awesome.min.css">
	<link rel="stylesheet" href="/Public/Home/css/jquery.mCustomScrollbar.css">
	<link rel="stylesheet" href="/Public/Home/css/iconfont.css">
	<link rel="stylesheet" href="/Public/Home/css/basic.css">
	<link rel="stylesheet" href="/Public/Home/css/<?php echo ($face["theme"]); ?>/index.css">
	<link rel="stylesheet" href="/Public/Home/css/promotion_index.css">

	
    <link rel="stylesheet" href="/Public/Home/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="/Public/Home/css/project/edit.css">
<!-- 引入css文件 -->
	<script src="/Public/Home/js/jquery.min.js"></script>
	<script src="/Public/Home/js/bootstrap.min.js"></script>
	<script src="/Public/Home/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/Public/Home/js/index.js"></script>

	
    <script src="/Public/Home/js/bootstrap-datetimepicker.js"></script>
    <script src="/Public/Home/js/bootstrap-datetimepicker.zh-CN.js"></script>
    <script src="/Public/Home/js/jquery.validate.min.js"></script>
    <script src="/Public/Home/js/jquery.form.js"></script>
    <script src="/Public/layer/layer.js"></script>
    <script src="/Public/Home/js/project-edit.js"></script>
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
        <li><a href="/Project">项目管理</a></li>
        <li class="active">编辑项目</li>
    </ol>

		</div>

		<div id="content">
			<div class="container-fluid" id="content-box">
				
    <!--<div class="commingSoon">
        <p><i class="icon-info-sign icon-5x"></i></p>
        <h1>功能研发中</h1>
        <p>Function is developing</p>
        <p>Comming Soon.</p>
    </div>-->

    <div class="project-container">
        <h2 class="page-header">项目信息</h2>
        <form role="form" id="projectEditForm">
            <table class="table">
                <tbody>
                <tr>
                    <td class="column-name">项目编号</td>
                    <td class="td_padding"><input type="text" name="pj_num" class="form-control input-sm" value="<?php echo ($projects["pj_num"]); ?>" placeholder="请输入项目编号，例：Plan-Test-001"><div class="help-block">&nbsp;</div></td>
                    <td class="column-name">项目名称</td>
                    <td><input type="text" name="pj_name" class="form-control input-sm" value="<?php echo ($projects["pj_name"]); ?>" placeholder="请输入项目名称，例：CSFP+ 项目开发"><div class="help-block">&nbsp;</div></td>
                </tr>
                <tr>
                    <td class="column-name">项目负责人</td>
                    <td><input type="text" name="pj_responsible" dpmt="1" readonly value="<?php echo ($projects["pj_responsible"]); ?>" class="form-control input-sm open-modal" placeholder="请选择项目负责人"><div class="help-block">&nbsp;</div></td>
                    <td class="column-name">立项时间</td>
                    <td><input type="text" name="pj_add_pro_time" readonly value="<?php echo ($projects["pj_add_pro_time"]); ?>" class="pj_add_pro_time form-control input-sm project-manager-input" value="<?php echo ($_SESSION['user']['nickname']); ?>"><div class="help-block">&nbsp;</div></td>
                </tr>
                <tr>
                    <td class="column-name">硬件设计</td>
                    <td><input type="text" name="pj_design" dpmt="1" readonly value="<?php echo ($projects["pj_design"]); ?>" class="form-control input-sm open-modal" placeholder="请选择项目设计人员"><div class="help-block">&nbsp;</div></td>
                    <td class="column-name">产品类型</td>
                    <td><input type="text" name="pj_family" class="form-control input-sm" value="<?php echo ($projects["pj_family"]); ?>" placeholder="例：CSFP+(SFP 、XFP、AOC、DAC， etc)"><div class="help-block">&nbsp;</div></td>
                </tr>
                <tr>
                    <td class="column-name">FW开发</td>
                    <td><input type="text" name="pj_fw_development" dpmt="1" readonly value="<?php echo ($projects["pj_fw_development"]); ?>" class="form-control input-sm open-modal" placeholder="请选择FW开发人员"><div class="help-block">&nbsp;</div></td>
                    <td class="column-name">项目类型</td>
                    <td><input type="text" name="pj_type" class="form-control input-sm" value="<?php echo ($projects["pj_type"]); ?>" placeholder="例：新设计(新设计？新功能？降低成本？etc)"><div class="help-block">&nbsp;</div></td>
                </tr>
                <tr>
                    <td class="column-name">结构件设计</td>
                    <td><input type="text" name="pj_structure_design" dpmt="1" readonly value="<?php echo ($projects["pj_structure_design"]); ?>" class="form-control input-sm open-modal" placeholder="请选择结构件设计人员"><div class="help-block">&nbsp;</div></td>
                    <td class="column-name">标准品名</td>
                    <td><input type="text" name="pj_standard_name" class="form-control input-sm" value="<?php echo ($projects["pj_standard_name"]); ?>" placeholder="请输入标准品名，如有多个用/分割"><div class="help-block">&nbsp;</div></td>
                </tr>
                <tr>
                    <td class="column-name">光器件设计</td>
                    <td><input type="text" name="pj_light_design" dpmt="1" readonly class="form-control input-sm open-modal" value="<?php echo ($projects["pj_light_design"]); ?>" placeholder="请选择光器件设计人员"><div class="help-block">&nbsp;</div></td>
                    <td class="column-name">平台</td>
                    <td><input type="text" name="pj_platform" class="form-control input-sm" value="<?php echo ($projects["pj_platform"]); ?>" placeholder="例：DS4834+Max24016"><div class="help-block">&nbsp;</div></td>
                </tr>
                <tr>
                    <td class="column-name">ATE设计</td>
                    <td><input type="text" name="pj_ate_design" dpmt="1" readonly class="form-control input-sm open-modal" value="<?php echo ($projects["pj_ate_design"]); ?>" placeholder="请选择ATE设计人员"><div class="help-block">&nbsp;</div></td>
                    <td class="column-name">潜在市场需求</td>
                    <td><input type="text" name="pj_market_demand" class="form-control input-sm" value="<?php echo ($projects["pj_market_demand"]); ?>" placeholder="请输入市场潜在需求(2年)，由市场部提供"><div class="help-block">&nbsp;</div></td>
                </tr>
                <tr>
                    <td class="column-name">DVT测试</td>
                    <td><input type="text" name="pj_dvt_test" dpmt="1" readonly class="form-control input-sm open-modal" value="<?php echo ($projects["pj_dvt_test"]); ?>" placeholder="请选择DVT测试人员"><div class="help-block">&nbsp;</div></td>
                    <td class="column-name">目标客户</td>
                    <td><input type="text" name="pj_target_customer" class="form-control input-sm" value="<?php echo ($projects["pj_target_customer"]); ?>" placeholder="例：华为（市场部提供）"><div class="help-block">&nbsp;</div></td>
                </tr>
                <tr>
                    <td class="column-name">市场部</td>
                    <td><input type="text" name="pj_market" dpmt="9" readonly class="form-control input-sm open-modal" value="<?php echo ($projects["pj_market"]); ?>" placeholder="请选择市场部人员"><div class="help-block">&nbsp;</div></td>
                    <td class="column-name">目标成本</td>
                    <td><input type="text" name="pj_target_cost" class="form-control input-sm" value="<?php echo ($projects["pj_target_cost"]); ?>" placeholder="请输入目标成本"><div class="help-block">&nbsp;</div></td>
                </tr>
                <tr>
                    <td class="column-name">项目描述</td>
                    <td colspan="3">
                        <textarea style="min-height: 120px;max-width: 790px;min-width: 790px;" name="pj_describe" class="form-control" placeholder="请输入项目描述"><?php echo ($projects["pj_describe"]); ?></textarea>
                        <div class="help-block">&nbsp;</div>
                    </td>
                </tr>
                <tr>
                    <td class="column-name"></td>
                    <td colspan="3">
                        <input type="hidden" name="info_id" class="" value="<?php echo ($projects["id"]); ?>">
                        <button type="submit" class="btn btn-primary">保存</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </form>
        <h2 class="page-header">项目计划</h2>
        <table class="table" id="plan-table">
            <tbody>
                <?php if(is_array($plans)): $i = 0; $__LIST__ = $plans;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><tr>
                        <td class="td_gate">
                            <input type="hidden" name="plan_id" value="<?php echo ($value["id"]); ?>">
                            <input type="hidden" name="gate" value="<?php echo ($value["gate"]); ?>">
                            <input type="text" readonly name="gate_name" class="form-control" style="cursor: not-allowed !important;" value="Gate<?php echo ($value["gate"]); ?>">
                        </td>
                        <td class="special"><input type="text" name="mile_stone" value="<?php echo ($value["mile_stone"]); ?>" class="form-control" placeholder="里程碑"></td>
                        <td><input type="text" name="items" class="form-control" value="<?php echo ($value["items"]); ?>" placeholder="节点"></td>
                        <td class="td_plan_start"><input type="text" name="plan_start_time" class="form-control plan_start_time" value="<?php echo ($value["plan_start_time"]); ?>" readonly placeholder="计划开始时间"></td>
                        <td class="td_plan_stop"><input type="text" name="plan_stop_time" class="form-control plan_stop_time" value="<?php echo ($value["plan_stop_time"]); ?>" readonly placeholder="计划结束时间"></td>
                        <td><button type="button" class="btn btn-primary save-data-btn">保存</button></td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
    </div>

    <!-- push email -->
    <form id="pushEmailPersonList">
        <input type="hidden" name="pj_responsible" value="<?php echo ($participates["0"]); ?>">
        <input type="hidden" name="pj_management">
        <input type="hidden" name="pj_fw_development" value="<?php echo ($participates["2"]); ?>">
        <input type="hidden" name="pj_design" value="<?php echo ($participates["1"]); ?>">
        <input type="hidden" name="pj_structure_design" value="<?php echo ($participates["3"]); ?>">
        <input type="hidden" name="pj_dvt_test" value="<?php echo ($participates["6"]); ?>">
        <input type="hidden" name="pj_light_design" value="<?php echo ($participates["4"]); ?>">
        <input type="hidden" name="pj_ate_design" value="<?php echo ($participates["5"]); ?>">
        <input type="hidden" name="pj_market" value="<?php echo ($participates["7"]); ?>">
    </form>

    <!-- Modal -->
    <div class="modal fade bs-example-modal-lg" id="choosePerson" tabindex="-1" role="dialog" aria-labelledby="choosePersonModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="choosePersonTitle">Modal title</h4>
                </div>
                <div class="modal-body" id="choosePersonBody">
                    <ul>
                        <?php if(is_array($users)): $i = 0; $__LIST__ = $users;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li dpmtid="<?php echo ($value["department"]); ?>" userid="<?php echo ($value["id"]); ?>"><?php echo ($value["nickname"]); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="loading-modal sr-only" id="loading">
        <div class="loading-icon">
            <p><i class="icon-spinner icon-spin icon-2x"></i></p>
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