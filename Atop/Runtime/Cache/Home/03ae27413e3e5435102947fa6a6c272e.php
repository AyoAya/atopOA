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
	<link rel="stylesheet" href="/Public/webuploader/css/webuploader.css">
	<link rel="stylesheet" href="/Public/Home/css/animate.css">
	<link rel="stylesheet" href="/Public/Home/css/jquery.toastmessage.css">
	<link rel="stylesheet" href="/Public/Home/css/bootstrap-datetimepicker.min.css">
	<link rel="stylesheet" href="/Public/Home/css/timeline.css"/>
	<link rel="stylesheet" href="/Public/Home/css/customer-details.css">
<!-- 引入css文件 -->
	<script src="/Public/Home/js/jquery.min.js"></script>
	<script src="/Public/Home/js/bootstrap.min.js"></script>
	<script src="/Public/layui/layui.js"></script>
	<script src="/Public/Home/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/Public/Home/js/index.js"></script>

	
	<script src="/Public/Home/js/jquery.Huploadify.js"></script>
	<script src="/Public/Home/js/timeline.js"></script>
	<script src="/Public/webuploader/js/webuploader.js"></script>
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
		<li><a href="/RMA">客诉处理</a></li>
		<li class="active">客诉详情</li>
	</ol>

		</div>

		<div id="content">
			<div class="container-fluid" id="content-box">
				




	<?php if(($details["rma_state"]) == "N"): ?>
		<?php if(($details["version"]) == "new"): ?><div class="pull-left">
				<?php if(in_array(($_SESSION['user']['id']), is_array($details["operation_person"])?$details["operation_person"]:explode(',',$details["operation_person"]))): ?>
					<button type="button" class="layui-btn layui-btn-primary" data-toggle="modal" data-target="#customer-rma-modal">
						<span class="glyphicon glyphicon-plus"></span> 添加处理记录
					</button><?php endif; ?>
			</div>
			<div class="clearfix" style="margin-bottom: 25px;"></div>
		<?php else: ?>
			<div class="pull-left">
				<button type="button" class="layui-btn layui-btn-primary" data-toggle="modal" data-target="#customer-rma-modal">
					<span class="glyphicon glyphicon-plus"></span> 添加处理记录
				</button>
			</div>
			<div class="clearfix" style="margin-bottom: 25px;"></div><?php endif; endif; ?>


	<div class="title-bar" style="margin: 0 0 25px 0;">
		<h4>基本信息</h4>
	</div>

	<table class="layui-table" id="table" lay-skin="row">
		<thead>
			<tr>
				<td>客诉ID</td>
				<td>客诉时间</td>
				<td>销售人员</td>
				<td>客户</td>
				<td>订单号</td>
				<td>产品</td>
				<td>设备厂商</td>
				<td>设备型号</td>
				<td>
                    <?php if(($details["version"]) == "new"): ?>
                        进度
                    <?php else: ?>
                        状态<?php endif; ?>
                </td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo ($details["id"]); ?></td>
				<td><?php echo ($details["cc_time"]); ?></td>
				<td><?php echo ($details["nickname"]); ?></td>
				<td><?php echo ($details["customer"]); ?></td>
				<td><?php echo ($details["sale_order"]); ?></td>
				<td><?php echo ($details["pn"]); ?></td>
				<td><?php echo ($details["vendor"]); ?></td>
				<td><?php echo ($details["model"]); ?></td>
				<td>
                    <?php if(($details["version"]) == "new"): ?>
                        <?php if(($details["rma_state"]) == "N"): ?><span class="label label-primary"><?php echo ($details["now_step"]["step_name"]); ?></span>
                        <?php else: ?>
                            <span class="label label-success">客诉已关闭</span><?php endif; ?>
                    <?php else: ?>
                        <?php switch($details["status"]): case "-1": ?><span class="label label-danger">无法处理</span><?php break;?>
                            <?php case "1": ?><span class="label label-primary">正在处理</span><?php break;?>
                            <?php case "0": ?><span class="label label-success">已经处理</span><?php break; endswitch; endif; ?>
				</td>
			</tr>
		</tbody>
	</table>

	<div class="title-bar" style="margin: 70px 0 15px 0;">
		<h4>客诉分析</h4>
	</div>

	<table class="layui-table layui-table-rewrite" lay-skin="line" style="margin: 0;">
		<tbody>
			<tr>
				<td>错误现象：</td>
				<td><?php echo ($details["error_message"]); ?></td>
			</tr>
			<tr>
				<td>原因分析：</td>
				<td><?php echo ($details["reason"]); ?></td>
			</tr>
			<?php if(!empty($details["comments"])): ?><tr>
					<td>备注信息：</td>
					<td><?php echo ($details["comments"]); ?></td>
				</tr><?php endif; ?>
		</tbody>
	</table>

	<div class="title-bar" style="margin: 50px 0 40px 0;">
		<h4>处理记录</h4>
	</div>

	<!-- 时间轴插件 -->
	<div class="timeline">
		<div class="timeline-heading">
			<?php if(is_array($nodeData)): $i = 0; $__LIST__ = $nodeData;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><div class="timeline-table <?php echo ($value["class"]); ?>"><!-- 如果要隐藏子节点列表为该元素添加 timeline-hide -->

						<?php if(($value["id"]) <= $details["now_step"]["id"]): ?><div class="timeline-title-box">
								<div class="timeline-title pull-left">
									<div class="timeline-step"><?php echo ($key+1); ?></div>
								</div>
								<div class="timeline-title-text pull-left">
                                    <?php if(($details["version"]) == "new"): ?><span><?php echo ($value["step_name"]); ?> <i class="<?php if(!empty($value["class"])): ?>icon-caret-up<?php else: ?>icon-caret-down<?php endif; ?>"></i></span>
                                    <?php else: ?>
                                        <span>处理记录 <i class="<?php if(!empty($value["class"])): ?>icon-caret-up<?php else: ?>icon-caret-down<?php endif; ?>"></i></span><?php endif; ?>
								</div>
								<div class="clearfix"></div>
							</div><?php endif; ?>

						<?php if(!empty($value["log"])): ?><table class="timeline-table">
								<?php if(!empty($value["log"])): if(is_array($value["log"])): $i = 0; $__LIST__ = $value["log"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>
											<td class="timeline-info">
												<div class="timeline-time-big"><?php echo (mb_substr($val["log_date"],5,5)); ?></div>
												<div class="timeline-time-small"><?php echo (mb_substr($val["log_date"],0,4)); ?></div>
												<div class="timeline-circle"></div>
											</td>
											<td class="timeline-content">
												<p><?php echo ($val["log_content"]); ?></p>
												<?php if(!empty($val["picture"])): if(is_array($val["picture"])): $i = 0; $__LIST__ = $val["picture"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; if(!empty($v)): ?><div class="pic-group pull-left">
																<img class="customer-complaint-img" src="/<?php echo ($v["filepath"]); ?>" alt="加载失败">
																<span class="pic-info"><?php echo ($v["filename"]); ?></span>
															</div><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
												<div class="clearfix"></div>
												<?php if(!empty($val["attachment"])): if(is_array($val["attachment"])): $i = 0; $__LIST__ = $val["attachment"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; if(($v["filetype"]) == "other"): ?><div class="attachment-box">
																<div class="attachment-box-dom">
																	<p class="file-name">
																		<a href="/<?php echo ($v["filepath"]); ?>"><i class="layui-icon">&#xe621;</i> <?php echo ($v["filename"]); ?></a>
																	</p>
																</div>
															</div>
														<?php else: ?>
															<?php if(($v) != ""): ?><div class="pic-group pull-left">
																	<img class="customer-complaint-img" src="/<?php echo ($v["filepath"]); ?>" height="120" alt="加载失败">
																	<span class="pic-info"><?php echo ($v["filename"]); ?></span>
																</div><?php endif; endif; endforeach; endif; else: echo "" ;endif; endif; ?>
												<div class="clearfix"></div>
												<div class="timeline-other"><?php echo ($val["recorder"]); ?><span class="timeline-divider">|</span><?php echo ($val["timestamp"]); ?></div>
											</td>
										</tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
							</table>
						<?php else: ?>
                            <?php if(($details["version"]) == "new"): ?><table class="timeline-table">
                                    <tr>
                                        <td class="timeline-info">
                                            <div class="timeline-circle"></div>
                                        </td>
                                        <td class="timeline-content">
                                            <p style="font-size: 16px;color: #888;"><i class="icon-info-sign"></i> 没有数据</p>
                                        </td>
                                    </tr>
                                </table><?php endif; endif; ?>

					</div><?php endforeach; endif; else: echo "" ;endif; ?>
		</div>
	</div>

	<!-- customer-rma-modal -->
    <?php if(($details["rma_state"]) == "N"): ?>
        <?php if(($details["version"]) == "new"): if(in_array(($_SESSION['user']['id']), is_array($details["operation_person"])?$details["operation_person"]:explode(',',$details["operation_person"]))): ?>
                <div class="modal fade" id="customer-rma-modal">
                    <form class="layui-form customer-rma-form">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">添加处理记录</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="layui-inline">
                                        <label class="layui-form-label">处理类型</label>
                                        <div class="layui-input-inline">
                                            <select name="operation_type" lay-filter="operation">
                                                <option value="X">添加日志</option>
                                                <?php if(($details["now_step"]["transfer"]) == "Y"): ?><option value="Z">转交</option><?php endif; ?>
                                                <?php if(($details["now_step"]["fallback"]) == "Y"): ?><option value="H">回退</option><?php endif; ?>
                                                <?php if(($details["now_step"]["id"]) == "3"): ?><option value="N">关闭客诉</option><?php endif; ?>
                                                <?php if(($details["now_step"]["id"]) == $maxStep): ?><option value="N">关闭客诉</option><?php endif; ?>
                                                <?php if(($details["now_step"]["id"]) < $maxStep): if(($details["rma_state"]) == "N"): ?>
                                                        <?php if(($details["now_step"]["id"]) != "4"): ?>
                                                            <option value="<?php echo ($details["next_step"]["id"]); ?>">推送到：<?php echo ($details["next_step"]["step_name"]); ?></option>
                                                        <?php else: ?>
                                                            <?php if(is_array($details["next_step"])): $i = 0; $__LIST__ = $details["next_step"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><option value="<?php echo ($value["id"]); ?>">推送到：<?php echo ($value["step_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; endif; endif; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <?php if(isset($QAlist)): ?><div class="layui-inline operation-person-select sr-only">
                                            <label class="layui-form-label">处理人</label>
                                            <div class="layui-input-inline">
                                                <select name="operation_person">
                                                    <?php if(is_array($QAlist)): $i = 0; $__LIST__ = $QAlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(($value["id"]) != $_SESSION['user']['id']): ?><option value="<?php echo ($value["id"]); ?>"><?php echo ($value["nickname"]); ?></option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                                </select>
                                            </div>
                                        </div><?php endif; ?>

                                    <?php if(isset($FAElist)): ?><div class="layui-inline fae-person-select sr-only">
                                            <label class="layui-form-label">FAE</label>
                                            <div class="layui-input-inline">
                                                <select name="operation_person">
                                                    <?php if(is_array($FAElist)): $i = 0; $__LIST__ = $FAElist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(($value["id"]) != $_SESSION['user']['id']): ?><option value="<?php echo ($value["id"]); ?>"><?php echo ($value["nickname"]); ?></option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                                                </select>
                                            </div>
                                        </div><?php endif; ?>

                                    <?php if(isset($fallback)): ?><div class="layui-inline step-select sr-only">
                                            <div class="layui-input-inline">
                                                <p>回退到：<?php echo ($fallback["step_name"]); ?><br>处理人：<?php echo ($fallback["nickname"]); ?></p>
                                            </div>
                                        </div><?php endif; ?>

                                    <div class="layui-form-item layui-form-text">
                                        <textarea name="log_content" placeholder="请输入处理内容" class="layui-textarea"></textarea>
                                    </div>
                                    <div class="layui-inline">
                                        <div class="layui-input-inline">
                                            <input type="file" name="Filedata" lay-type="file|image" class="layui-upload-file" multiple>
                                        </div>
                                    </div>
                                    <ul id="attachment-list"></ul>
                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="recorder" value="<?php echo ($_SESSION['user']['account']); ?>">
                                    <input type="hidden" name="cc_id" value="<?php echo ($details["id"]); ?>">
                                    <input type="hidden" name="step" value="<?php echo ($details["now_step"]["id"]); ?>">
                                    <input type="hidden" name="version" value="<?php echo ($details["version"]); ?>">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                                    <button lay-submit lay-filter="rmaCustomer" class="btn btn-primary">确定</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div><?php endif; ?>
        <?php else: ?>
            <div class="modal fade" id="customer-rma-modal">
                <form class="layui-form customer-rma-form">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="myModalLabel">添加处理记录</h4>
                            </div>
                            <div class="modal-body">
                                <div class="layui-inline">
                                    <label class="layui-form-label">处理类型</label>
                                    <div class="layui-input-inline">
                                        <select name="operation_type" lay-filter="operation">
                                            <option value="X">添加日志</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="layui-form-item layui-form-text">
                                    <textarea name="log_content" placeholder="请输入处理内容" class="layui-textarea"></textarea>
                                </div>
                                <div class="layui-inline">
                                    <div class="layui-input-inline">
                                        <input type="file" name="Filedata" lay-type="file|image" class="layui-upload-file" multiple>
                                    </div>
                                </div>
                                <ul id="attachment-list"></ul>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="recorder" value="<?php echo ($_SESSION['user']['account']); ?>">
                                <input type="hidden" name="cc_id" value="<?php echo ($details["id"]); ?>">
                                <input type="hidden" name="step" value="<?php echo ($details["now_step"]["id"]); ?>">
                                <input type="hidden" name="version" value="<?php echo ($details["version"]); ?>">
                                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                                <button lay-submit lay-filter="rmaCustomer" class="btn btn-primary">确定</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div><?php endif; endif; ?>




	<!--<div class="panel panel-default">
		<div class="panel-heading panel-heading-edit"><h4>错误现象</h4></div>
		<div class="panel-body">
			<code><?php echo ($details["error_message"]); ?></code>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading panel-heading-edit"><h4>原因分析</h4></div>
		<div class="panel-body">
			<?php echo ($details["reason"]); ?>
		</div>
	</div>

	<?php if(!empty($details["comments"])): ?><div class="panel panel-default">
		<div class="panel-heading panel-heading-edit"><h4>备注信息</h4></div>
		<div class="panel-body">
			<?php echo ($details["comments"]); ?>
		</div>
	</div><?php endif; ?>-->

	<!--<div class="panel panel-default" id="deal-record">
		<div class="panel-heading panel-heading-edit"><h4>处理记录</h4></div>
		<div class="panel-body">
			<?php if(is_array($details["log"])): $i = 0; $__LIST__ = $details["log"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="media media-edit">
					<span class="media-left media-top media-img" href="###">
						<img class="userface" src="<?php echo ($vo["face"]); ?>" width="100" height="100" alt="...">
					</span>
					<div class="media-body">
						<p class="log-info"><kbd><?php echo ($vo["log_date"]); ?></kbd> <?php echo ($vo["log_content"]); ?></p>
						<?php if(!empty($vo["picture"])): if(is_array($vo["picture"])): $i = 0; $__LIST__ = $vo["picture"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; if(!empty($v)): ?>&lt;!&ndash; 如果图像为空则不输出 &ndash;&gt;
									<div class="pic-group pull-left">
										<p class="float-pic">
											<img class="customer-complaint-img" src="<?php echo ($v["filepath"]); ?>" alt="图像加载错误">
										</p>
										<span class="pic-info"><?php echo ($v["filename"]); ?></span>
									</div><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
						<div class="clearfix"></div>
						<?php if(!empty($vo["attachment"])): if(is_array($vo["attachment"])): $i = 0; $__LIST__ = $vo["attachment"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i; if(($v["filetype"]) == "other"): ?><div class="attachment-box">
										<div class="attachment-box-dom">
											<p class="file-name">
												<span class="file-name-info pull-left"><i class="icon-file-alt"></i> <?php echo ($v["filename"]); ?></span>
												<form action="/index.php/Home/RMA/downloadFile" method="post" class="pull-left">
													<input type="hidden" name="filepath" value="<?php echo ($v["filepath"]); ?>">
													<input type="submit" class="btn btn-default btn-xs download-file" value="下载">
												</form>
											</p>
										</div>
									</div>
								<?php else: ?>
									<?php if(($v) != ""): ?>&lt;!&ndash; 如果图像为空则不输出 &ndash;&gt;
										<div class="pic-group pull-left">
											<p class="float-pic">
												<img class="customer-complaint-img" src="<?php echo ($v["filepath"]); ?>" alt="图像加载错误">
											</p>
											<span class="pic-info"><?php echo ($v["filename"]); ?></span>
										</div><?php endif; endif; endforeach; endif; else: echo "" ;endif; endif; ?>
						<div class="clearfix"></div>
						<p class="log-time"><?php echo ($vo["recorder"]); ?> | <?php echo ($vo["timestamp"]); ?></p>
					</div>
				</div><?php endforeach; endif; else: echo "" ;endif; ?>
		</div>
	</div>-->
	
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

	<!-- 是否转为RMA -->
	<!--<?php if(($details["sale_audit"]) == "1"): if(($face["account"]) == $details["salesperson"]): ?><div class="is_rma_box animated bounceInDown">
				<div class="rma-solid">RMA</div>
			</div><?php endif; endif; ?>-->






	<script>
		$('.form_date').datetimepicker({
			language:  'zh-CN',
			format:'yyyy-mm-dd',
			weekStart: 1,
			todayBtn:  1,
			autoclose: 1,
			todayHighlight: 1,
			endDate : new Date(),
			startView: 2,
			minView: 2,
			forceParse: 0
		}).on('changeDate',function(ev){
			$('#change-date-remove').html('');
		});


		$('.processing-time').datetimepicker({
			language:  'zh-CN',
			format:'yyyy-mm-dd',
			weekStart: 1,
			todayBtn:  1,
			autoclose: 1,
			todayHighlight: 1,
			endDate : new Date(),
			startView: 2,
			minView: 2,
			forceParse: 0
		})
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