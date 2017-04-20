<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>

	<meta charset="UTF-8">
	<!--<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">-->
	<link href="/Public/Home/img/favicon.ico" rel="shortcut icon">

	
    <title>样品总览</title>
<!-- 网站标题 -->
	<link rel="stylesheet" href="/Public/layui/css/layui.css">
	<link rel="stylesheet" href="/Public/Home/css/bootstrap.min.css">
	<link rel="stylesheet" href="/Public/Home/css/font-awesome.min.css">
	<link rel="stylesheet" href="/Public/Home/css/jquery.mCustomScrollbar.css">
	<link rel="stylesheet" href="/Public/Home/css/iconfont.css">
	<link rel="stylesheet" href="/Public/Home/css/basic.css">
	<link rel="stylesheet" href="/Public/Home/css/<?php echo ($face["theme"]); ?>/index.css">
	<link rel="stylesheet" href="/Public/Home/css/promotion_index.css">

	
    <link rel="stylesheet" href="/Public/Home/css/bs-is-fun.css">
    <link rel="stylesheet" href="/Public/Home/css/overview.css">
<!-- 引入css文件 -->
	<script src="/Public/Home/js/jquery.min.js"></script>
	<script src="/Public/Home/js/bootstrap.min.js"></script>
	<script src="/Public/layui/layui.js"></script>
	<script src="/Public/Home/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/Public/Home/js/index.js"></script>

	
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
        <li><a href="/sample">样品管理</a></li>
        <li class="active">样品总览</li>
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
				
    <div class="page-header">
        <h1 class="totalorder"><i class="icon-list-alt">&nbsp;&nbsp;</i><?php echo ($totalorder); ?><small class="">共计：<?php echo count($overview);?>个产品，<?php echo count($attachment);?>个附件</small><i class="createtime pull-right"><i class="icon-time">&nbsp;</i>订单创建时间：<?php echo (date("Y-m-d H:i:s",$overview["0"]["createtime"])); ?></i><i class="sale-person"><i class="icon-user">&nbsp;</i>销售：<?php echo ($overview["0"]["nickname"]); ?></i></h1>
    </div>
        <table class="layui-table">
            <thead>
            <tr>
                <th>产品类型</th>
                <th>产品型号</th>
                <th>产品经理</th>
                <th>模块数量</th>
                <th>客户名称</th>
                <th>设备品牌</th>
                <th>设备型号</th>
                <th>进度</th>
            </tr>
            </thead>
            <tbody>
                <?php if(is_array($overview)): $i = 0; $__LIST__ = $overview;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><tr>
                        <td><?php echo ($value["type"]); ?></td>
                        <td><a href="/sampleDetails/<?php echo ($value["order"]); ?>"><?php echo ($value["product"]); ?></a></td>
                        <td><?php echo ($value["manager"]); ?></td>
                        <td><?php echo ($value["number"]); ?></td>
                        <td><?php echo ($value["customer"]); ?></td>
                        <td><?php echo ($value["brand"]); ?></td>
                        <td><?php echo ($value["model"]); ?></td>
                        <td>
                            <?php if(isset($value["color"])): if(($value["color"]) == "0"): ?><span style="padding: 2px 5px;background: #f0ad4e;border-radius: 2px;color: #fff;"><?php echo ($value["state"]); ?></span><?php endif; ?>
                                <?php if(($value["color"]) == "2"): ?><span style="padding: 2px 5px;background: #d9534f;border-radius: 2px;color: #fff;"><?php echo ($value["state"]); ?></span><?php endif; ?>
                                <?php if(($value["color"]) == "3"): ?><span style="padding: 2px 5px;background: #5cb85c;border-radius: 2px;color: #fff;"><?php echo ($value["state"]); ?></span><?php endif; endif; ?>
                        </td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
    <?php if(is_array($sample)): $i = 0; $__LIST__ = $sample;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">产品：<a style="color: #428bca;" href="/sampleDetails/<?php echo ($value["order"]); ?>"><?php echo ($value["product"]); ?></a></h4>
            </div>
            <div class="panel-body">
                <ul class="nav nav-pills nav-justified step step-progress">
                    <!--<li class="active">
                        <a>下单<span class="caret"></span></a>
                        <div class="round">
                            <span class="active">
                                <div class="instructions">
                                    <span class="caret"></span>
                                    <p>下单人：<?php echo ($value["nickname"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$value["createtime"])); ?></small></p>
                                </div>
                            </span>
                        </div>
                    </li>
                    <li class="<?php if(($value["s_status"]) == "1"): ?>active<?php endif; if(($value["s_status"]) == "2"): ?>error<?php endif; ?>">
                        <a>审核<span class="caret"></span></a>
                        <?php if(in_array(($value["s_status"]), explode(',',"1,2"))): ?><div class="round">
                                <span class="<?php if(($value["s_status"]) == "1"): ?>active<?php endif; if(($value["s_status"]) == "2"): ?>error<?php endif; ?>">
                                    <div class="instructions">
                                        <span class="caret"></span>
                                        <p>审核人：<?php echo ($value["a_name"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$value["s_time"])); ?></small></p>
                                    </div>
                                </span>
                            </div><?php endif; ?>
                    </li>
                    <li class="<?php if(($value["w_status"]) == "1"): ?>active<?php endif; if(($value["w_status"]) == "2"): ?>error<?php endif; ?>">
                        <a>物料准备<span class="caret"></span></a>
                        <?php if(in_array(($value["w_status"]), explode(',',"1,2"))): ?><div class="round">
                                <span class="<?php if(($value["s_status"]) == "1"): ?>active<?php endif; if(($value["s_status"]) == "2"): ?>error<?php endif; ?>">
                                    <div class="instructions">
                                        <span class="caret"></span>
                                        <p>处理人：<?php echo ($value["w_name"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$value["w_time"])); ?></small></p>
                                    </div>
                                </span>
                            </div><?php endif; ?>
                    </li>
                    <li class="<?php if(($value["c_status"]) == "1"): ?>active<?php endif; if(($value["c_status"]) == "2"): ?>error<?php endif; ?>">
                        <a>样品制作<span class="caret"></span></a>
                        <?php if(in_array(($value["c_status"]), explode(',',"1,2"))): ?><div class="round">
                                <span class="<?php if(($value["c_status"]) == "1"): ?>active<?php endif; if(($value["c_status"]) == "2"): ?>error<?php endif; ?>">
                                    <div class="instructions">
                                        <span class="caret"></span>
                                        <p>审核人：<?php echo ($value["c_name"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$value["c_time"])); ?></small></p>
                                    </div>
                                </span>
                            </div><?php endif; ?>
                    </li>
                    <li class="<?php if(($value["y_status"]) == "1"): ?>active<?php endif; if(($value["y_status"]) == "2"): ?>error<?php endif; ?>">
                        <a>样品测试<span class="caret"></span></a>
                        <?php if(in_array(($value["y_status"]), explode(',',"1,2"))): ?><div class="round">
                                <span class="<?php if(($value["y_status"]) == "1"): ?>active<?php endif; if(($value["y_status"]) == "2"): ?>error<?php endif; ?>">
                                    <div class="instructions">
                                        <span class="caret"></span>
                                        <p>测试人：<?php echo ($value["y_name"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$value["y_time"])); ?></small></p>
                                    </div>
                                </span>
                            </div><?php endif; ?>
                    </li>
                    <li class="<?php if(($value["f_status"]) == "1"): ?>active<?php endif; if(($value["f_status"]) == "2"): ?>error<?php endif; ?>">
                        <a>发货<span class="caret"></span></a>
                        <?php if(in_array(($value["f_status"]), explode(',',"1,2"))): ?><div class="round">
                                <span class="<?php if(($value["f_status"]) == "1"): ?>active<?php endif; if(($value["f_status"]) == "2"): ?>error<?php endif; ?>">
                                    <div class="instructions">
                                        <span class="caret"></span>
                                        <p>测试人：<?php echo ($value["f_name"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$value["f_time"])); ?></small></p>
                                    </div>
                                </span>
                            </div><?php endif; ?>
                    </li>
                    <li class="<?php if(in_array(($value["k_status"]), explode(',',"1,2"))): ?>active<?php endif; ?>">
                        <a>反馈<span class="caret"></span></a>
                        <?php if(in_array(($value["k_status"]), explode(',',"1,2"))): ?><div class="round">
                                <span class="<?php if(in_array(($value["k_status"]), explode(',',"1,2"))): ?>active<?php endif; ?>">
                                    <div class="instructions">
                                        <span class="caret"></span>
                                        <p>反馈人：<?php echo ($value["k_name"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$value["k_time"])); ?></small></p>
                                    </div>
                                </span>
                            </div><?php endif; ?>
                    </li>-->
                    <li class="active">
                        <a>下单<span class="caret"></span></a>
                        <div class="round">
						<span class="active">
							<div class="instructions">
								<span class="caret"></span>
								<p>下单人：<?php echo ($value["nickname"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$value["createtime"])); ?></small></p>
							</div>
						</span>
                        </div>
                    </li>
                    <li class="<?php if(($value["s_status"]) == "1"): ?>active<?php endif; if(($value["s_status"]) == "2"): ?>error<?php endif; ?>">
                        <a>审核
                            <?php if(!empty($value["s_comment"])): ?><!-- 成功后消息存在并且不为空时显示 -->
                                <?php if(isset($value["s_comment"]["success"])): if(!isset($value["s_comment"]["save"])): if(!empty($value["s_comment"]["success"])): ?><span class="log-save-icon-audit operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
                                <!-- 失败后消息存在并且不为空时显示 -->
                                <?php if(isset($value["s_comment"]["error"])): if(!isset($value["s_comment"]["save"])): if(!empty($value["s_comment"]["error"])): ?><span class="log-save-icon-audit operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
                                <!-- 更新日志后消息存在并且不为空时显示 -->
                                <?php if(isset($value["s_comment"]["save"])): if(!empty($value["s_comment"]["save"])): ?><span class="log-save-icon-audit operation-log-color">
										<i class="icon-info-sign"></i>
									</span><?php endif; endif; ?>
                                <div class="sr-only audit-operation-log">
                                    <ul>
                                        <?php if(($value["s_status"]) == "2"): if(!empty($value["s_comment"]["error"]["0"]["message"])): ?><li><strong class="text-danger"><p><i class="icon-remove-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$value["s_comment"]["error"]["0"]["time"])); ?>] <?php echo ($value["s_comment"]["error"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
                                        <?php if(($value["s_status"]) == "1"): if(!empty($value["s_comment"]["success"]["0"]["message"])): ?><li><strong class="text-success"><p><i class="icon-ok-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$value["s_comment"]["success"]["0"]["time"])); ?>] <?php echo ($value["s_comment"]["success"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
                                        <?php if(is_array($value["s_comment"]["save"])): $i = 0; $__LIST__ = $value["s_comment"]["save"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                                                <p><i class="icon-minus-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$vo["time"])); ?>] <?php echo ($vo["message"]); ?></p>
                                            </li><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </ul>
                                </div><?php endif; ?>
                            <span class="caret"></span>
                        </a>
                        <?php if(in_array(($value["s_status"]), explode(',',"1,2"))): ?><div class="round">
							<span class="<?php if(($value["s_status"]) == "1"): ?>active<?php endif; if(($value["s_status"]) == "2"): ?>error<?php endif; ?>">
								<div class="instructions">
									<span class="caret"></span>
									<p>审核人：<?php echo ($value["a_name"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$value["s_time"])); ?></small></p>
								</div>
							</span>
                            </div><?php endif; ?>
                    </li>
                    <li class="<?php if(($value["w_status"]) == "1"): ?>active<?php endif; if(($value["w_status"]) == "2"): ?>error<?php endif; ?>">
                        <a>物料准备
                            <?php if(!empty($value["w_comment"])): ?><!-- 成功后消息存在并且不为空时显示 -->
                                <?php if(isset($value["w_comment"]["success"])): if(!isset($value["w_comment"]["save"])): if(!empty($value["w_comment"]["success"])): ?><span class="log-save-icon-metarial operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
                                <!-- 失败后消息存在并且不为空时显示 -->
                                <?php if(isset($value["w_comment"]["error"])): if(!isset($value["w_comment"]["save"])): if(!empty($value["w_comment"]["error"])): ?><span class="log-save-icon-metarial operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
                                <!-- 更新日志后消息存在并且不为空时显示 -->
                                <?php if(isset($value["w_comment"]["save"])): if(!empty($value["w_comment"]["save"])): ?><span class="log-save-icon-metarial operation-log-color">
										<i class="icon-info-sign"></i>
									</span><?php endif; endif; ?>
                                <div class="sr-only metarial-operation-log">
                                    <ul>
                                        <?php if(($value["w_status"]) == "2"): if(!empty($value["w_comment"]["error"]["0"]["message"])): ?><li><strong class="text-danger"><p><i class="icon-remove-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$value["w_comment"]["error"]["0"]["time"])); ?>] <?php echo ($value["w_comment"]["error"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
                                        <?php if(($value["w_status"]) == "1"): if(!empty($value["w_comment"]["success"]["0"]["message"])): ?><li><strong class="text-success"><p><i class="icon-ok-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$value["w_comment"]["success"]["0"]["time"])); ?>] <?php echo ($value["w_comment"]["success"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
                                        <?php if(is_array($value["w_comment"]["save"])): $i = 0; $__LIST__ = $value["w_comment"]["save"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                                                <p><i class="icon-minus-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$vo["time"])); ?>] <?php echo ($vo["message"]); ?></p>
                                            </li><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </ul>
                                </div><?php endif; ?>
                            <span class="caret"></span></a>
                        <?php if(in_array(($value["w_status"]), explode(',',"1,2"))): ?><div class="round">
							<span class="<?php if(($value["w_status"]) == "1"): ?>active<?php endif; if(($value["w_status"]) == "2"): ?>error<?php endif; ?>">
								<div class="instructions">
									<span class="caret"></span>
									<p>处理人：<?php echo ($value["w_name"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$value["w_time"])); ?></small></p>
								</div>
							</span>
                            </div><?php endif; ?>
                    </li>
                    <li class="<?php if(($value["c_status"]) == "1"): ?>active<?php endif; if(($value["c_status"]) == "2"): ?>error<?php endif; ?>">
                        <a>样品制作
                            <?php if(!empty($value["c_comment"])): ?><!-- 成功后消息存在并且不为空时显示 -->
                                <?php if(isset($value["c_comment"]["success"])): if(!isset($value["c_comment"]["save"])): if(!empty($value["c_comment"]["success"])): ?><span class="log-save-icon-productEngineer operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
                                <!-- 失败后消息存在并且不为空时显示 -->
                                <?php if(isset($value["c_comment"]["error"])): if(!isset($value["c_comment"]["save"])): if(!empty($value["c_comment"]["error"])): ?><span class="log-save-icon-productEngineer operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
                                <!-- 更新日志后消息存在并且不为空时显示 -->
                                <?php if(isset($value["c_comment"]["save"])): if(!empty($value["c_comment"]["save"])): ?><span class="log-save-icon-productEngineer operation-log-color">
										<i class="icon-info-sign"></i>
									</span><?php endif; endif; ?>
                                <div class="sr-only productEngineer-operation-log">
                                    <ul>
                                        <?php if(($value["c_status"]) == "2"): if(!empty($value["c_comment"]["error"]["0"]["message"])): ?><li><strong class="text-danger"><p><i class="icon-remove-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$value["c_comment"]["error"]["0"]["time"])); ?>] <?php echo ($value["c_comment"]["error"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
                                        <?php if(($value["c_status"]) == "1"): if(!empty($value["c_comment"]["success"]["0"]["message"])): ?><li><strong class="text-success"><p><i class="icon-ok-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$value["c_comment"]["success"]["0"]["time"])); ?>] <?php echo ($value["c_comment"]["success"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
                                        <?php if(is_array($value["c_comment"]["save"])): $i = 0; $__LIST__ = $value["c_comment"]["save"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                                                <p><i class="icon-minus-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$vo["time"])); ?>] <?php echo ($vo["message"]); ?></p>
                                            </li><?php endforeach; endif; else: echo "" ;endif; ?>
                                    </ul>
                                </div><?php endif; ?>
                            <span class="caret"></span></a>
                        <?php if(in_array(($value["c_status"]), explode(',',"1,2"))): ?><div class="round">
							<span class="<?php if(($value["c_status"]) == "1"): ?>active<?php endif; if(($value["c_status"]) == "2"): ?>error<?php endif; ?>">
								<div class="instructions">
									<span class="caret"></span>
									<p>制作人：<?php echo ($value["c_name"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$value["c_time"])); ?></small></p>
								</div>
							</span>
                            </div><?php endif; ?>
                    </li>
                    <li class="<?php if(($value["y_status"]) == "1"): ?>active<?php endif; if(($value["y_status"]) == "2"): ?>error<?php endif; ?>">
                        <a>样品测试
                            <?php if(isset($value["y_comment"])): if(!empty($value["y_comment"])): ?><!-- 成功后消息存在并且不为空时显示 -->
                                    <?php if(isset($value["y_comment"]["success"])): if(!isset($value["y_comment"]["save"])): if(!empty($value["y_comment"]["success"])): ?><span class="log-save-icon-testEngineer operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
                                    <!-- 失败后消息存在并且不为空时显示 -->
                                    <?php if(isset($value["y_comment"]["error"])): if(!isset($value["y_comment"]["save"])): if(!empty($value["y_comment"]["error"])): ?><span class="log-save-icon-testEngineer operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
                                    <!-- 更新日志后消息存在并且不为空时显示 -->
                                    <?php if(isset($value["y_comment"]["save"])): if(!empty($value["y_comment"]["save"])): ?><span class="log-save-icon-testEngineer operation-log-color">
										<i class="icon-info-sign"></i>
									</span><?php endif; endif; ?>
                                    <div class="sr-only testEngineer-operation-log">
                                        <ul>
                                            <?php if(($value["y_status"]) == "2"): if(!empty($value["y_comment"]["error"]["0"]["message"])): ?><li><strong class="text-danger"><p><i class="icon-remove-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$value["y_comment"]["error"]["0"]["time"])); ?>] <?php echo ($value["y_comment"]["error"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
                                            <?php if(($value["y_status"]) == "1"): if(!empty($value["y_comment"]["success"]["0"]["message"])): ?><li><strong class="text-success"><p><i class="icon-ok-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$value["y_comment"]["success"]["0"]["time"])); ?>] <?php echo ($value["y_comment"]["success"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
                                            <?php if(is_array($value["y_comment"]["save"])): $i = 0; $__LIST__ = $value["y_comment"]["save"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                                                    <p><i class="icon-minus-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$vo["time"])); ?>] <?php echo ($vo["message"]); ?></p>
                                                </li><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </ul>
                                    </div><?php endif; endif; ?>
                            <span class="caret"></span></a>
                        <?php if(in_array(($value["y_status"]), explode(',',"1,2"))): ?><div class="round">
							<span class="<?php if(($value["y_status"]) == "1"): ?>active<?php endif; if(($value["y_status"]) == "2"): ?>error<?php endif; ?>">
								<div class="instructions">
									<span class="caret"></span>
									<p>测试人：<?php echo ($value["y_name"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$value["y_time"])); ?></small></p>
								</div>
							</span>
                            </div><?php endif; ?>
                    </li>
                    <li class="<?php if(($value["f_status"]) == "1"): ?>active<?php endif; if(($value["f_status"]) == "2"): ?>error<?php endif; ?>">
                        <a>发货
                            <?php if(isset($value["f_comment"])): if(!empty($value["f_comment"])): ?><!-- 成功后消息存在并且不为空时显示 -->
                                    <?php if(isset($value["f_comment"]["success"])): if(!isset($value["f_comment"]["save"])): if(!empty($value["f_comment"]["success"])): ?><span class="log-save-icon-delivery operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
                                    <!-- 失败后消息存在并且不为空时显示 -->
                                    <?php if(isset($value["f_comment"]["error"])): if(!isset($value["f_comment"]["save"])): if(!empty($value["f_comment"]["error"])): ?><span class="log-save-icon-delivery operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
                                    <!-- 更新日志后消息存在并且不为空时显示 -->
                                    <?php if(isset($value["f_comment"]["save"])): if(!empty($value["f_comment"]["save"])): ?><span class="log-save-icon-delivery operation-log-color">
										<i class="icon-info-sign"></i>
									</span><?php endif; endif; ?>
                                    <div class="sr-only delivery-operation-log">
                                        <ul>
                                            <?php if(($value["f_status"]) == "2"): if(!empty($value["f_comment"]["error"]["0"]["message"])): ?><li><strong class="text-danger"><p><i class="icon-remove-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$value["f_comment"]["error"]["0"]["time"])); ?>] <?php echo ($value["f_comment"]["error"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
                                            <?php if(($value["f_status"]) == "1"): if(!empty($value["f_comment"]["success"]["0"]["message"])): ?><li><strong class="text-success"><p><i class="icon-ok-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$value["f_comment"]["success"]["0"]["time"])); ?>] <?php echo ($value["f_comment"]["success"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
                                            <?php if(is_array($value["f_comment"]["save"])): $i = 0; $__LIST__ = $value["f_comment"]["save"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                                                    <p><i class="icon-minus-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$vo["time"])); ?>] <?php echo ($vo["message"]); ?></p>
                                                </li><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </ul>
                                    </div><?php endif; endif; ?>
                            <span class="caret"></span></a>
                        <?php if(in_array(($value["f_status"]), explode(',',"1,2"))): ?><div class="round">
							<span class="<?php if(($value["f_status"]) == "1"): ?>active<?php endif; if(($value["f_status"]) == "2"): ?>error<?php endif; ?>">
								<div class="instructions">
									<span class="caret"></span>
									<p>发货人：<?php echo ($value["f_name"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$value["f_time"])); ?></small></p>
								</div>
							</span>
                            </div><?php endif; ?>
                    </li>
                    <li class="<?php if(($value["k_status"]) == "1"): ?>active<?php endif; if(($value["k_status"]) == "2"): ?>error<?php endif; ?>">
                        <a>反馈
                            <?php if(isset($value["k_comment"])): if(!empty($value["k_comment"])): ?><!-- 成功后消息存在并且不为空时显示 -->
                                    <?php if(isset($value["k_comment"]["success"])): if(!isset($value["k_comment"]["save"])): if(!empty($value["k_comment"]["success"])): ?><span class="log-save-icon-feedback operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
                                    <!-- 失败后消息存在并且不为空时显示 -->
                                    <?php if(isset($value["k_comment"]["error"])): if(!isset($value["k_comment"]["save"])): if(!empty($value["k_comment"]["error"])): ?><span class="log-save-icon-feedback operation-log-color">
											<i class="icon-info-sign"></i>
										</span><?php endif; endif; endif; ?>
                                    <!-- 更新日志后消息存在并且不为空时显示 -->
                                    <?php if(isset($value["k_comment"]["save"])): if(!empty($value["k_comment"]["save"])): ?><span class="log-save-icon-feedback operation-log-color">
										<i class="icon-info-sign"></i>
									</span><?php endif; endif; ?>
                                    <div class="sr-only feedback-operation-log">
                                        <ul>
                                            <?php if(($value["k_status"]) == "2"): if(!empty($value["k_comment"]["error"]["0"]["message"])): ?><li><strong class="text-danger"><p><i class="icon-remove-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$value["k_comment"]["error"]["0"]["time"])); ?>] <?php echo ($value["k_comment"]["error"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
                                            <?php if(($value["k_status"]) == "1"): if(!empty($value["k_comment"]["success"]["0"]["message"])): ?><li><strong class="text-success"><p><i class="icon-ok-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$value["k_comment"]["success"]["0"]["time"])); ?>] <?php echo ($value["k_comment"]["success"]["0"]["message"]); ?></p></strong></li><?php endif; endif; ?>
                                            <?php if(is_array($value["k_comment"]["save"])): $i = 0; $__LIST__ = $value["k_comment"]["save"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                                                    <p><i class="icon-minus-sign">&nbsp;</i>[<?php echo (date("Y-m-d H:i:s",$vo["time"])); ?>] <?php echo ($vo["message"]); ?></p>
                                                </li><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </ul>
                                    </div><?php endif; endif; ?>
                            <span class="caret"></span></a>
                        <?php if(in_array(($value["k_status"]), explode(',',"1,2"))): ?><div class="round">
							<span class="<?php if(($value["k_status"]) == "1"): ?>active<?php endif; if(($value["k_status"]) == "2"): ?>error<?php endif; ?>">
								<div class="instructions">
									<span class="caret"></span>
									<p>反馈人：<?php echo ($value["k_name"]); ?><br><small><?php echo (date("Y-m-d H:i:s",$value["k_time"])); ?></small></p>
								</div>
							</span>
                            </div><?php endif; ?>
                    </li>
                </ul>
            </div>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
    <!-- 附件 -->
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">订单附件</h4>
        </div>
        <div class="panel-body">
            <ul id="attachmentList">
                <?php if(is_array($attachment)): $i = 0; $__LIST__ = $attachment;if( count($__LIST__)==0 ) : echo "$noAttachment" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li class="attachment">
                        <div class="attachmentIconBox">
                            <img src="/Public/Home/img/file.png" alt="...">
                            <span class="text-uppercase"><?php echo ($value["type"]); ?></span>
                        </div>
                        <p><?php echo ($value["filename"]); ?></p>
                        <form action="/index.php/Home/Sample/downloadAttachment" method="post">
                            <input type="hidden" name="filepath" value="<?php echo ($value["filepath"]); ?>">
                            <input type="submit" class="btn btn-success icon-download" value="下载">
                        </form>
                    </li><?php endforeach; endif; else: echo "$noAttachment" ;endif; ?>
            </ul>
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