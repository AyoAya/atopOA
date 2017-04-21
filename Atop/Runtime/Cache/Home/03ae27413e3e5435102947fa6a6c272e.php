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
	<link rel="stylesheet" href="/Public/webuploader/css/webuploader.css">
	<link rel="stylesheet" href="/Public/Home/css/animate.css">
	<link rel="stylesheet" href="/Public/Home/css/jquery.toastmessage.css">
	<link rel="stylesheet" href="/Public/Home/css/bootstrap-datetimepicker.min.css">
	<link rel="stylesheet" href="/Public/Home/css/timeline.css"/>
	<link rel="stylesheet" href="/Public/Home/css/rma/customer-details.css">
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
	<script src="/Public/Home/js/rma/customer.js"></script>
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
		<li class="active">客诉详情</li>
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
				


	<?php if(($details["rma_state"]) == "N"): ?>
		<?php if(($details["version"]) == "new"): if(in_array(($_SESSION['user']['id']), is_array($details["operation_person"])?$details["operation_person"]:explode(',',$details["operation_person"]))): ?>
				<div class="addOperationLog" data-toggle="modal" data-target="#customer-rma-modal" title="添加处理记录">
					<p>
						<span>添加</span>
						<span>处理记录</span>
					</p>
				</div><?php endif; ?>
		<?php else: ?>
			<div class="pull-left">
				<button type="button" class="layui-btn layui-btn-primary" data-toggle="modal" data-target="#customer-rma-modal">
					<span class="glyphicon glyphicon-plus"></span> 添加处理记录
				</button>
			</div>
			<div class="clearfix" style="margin-bottom: 25px;"></div><?php endif; endif; ?>


	<div class="title-bar" style="margin: 0 0 15px 0;">
		<h4>基本信息</h4>
	</div>

	<table class="layui-table" id="table">
		<thead>
			<tr>
				<th>客诉ID</th>
				<th>客诉时间</th>
				<th>销售人员</th>
				<th>客户</th>
				<th>订单号</th>
				<th>产品型号</th>
				<th>
                    <?php if(($details["version"]) == "new"): ?>
                        当前进度
                    <?php else: ?>
                        状态<?php endif; ?>
                </th>
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
				<td>
                    <?php if(($details["version"]) == "new"): ?>
                        <?php if(($details["rma_state"]) == "N"): ?><span class="label label-primary">Step<?php echo ($details["now_step"]["id"]); ?>-<?php echo ($details["now_step"]["step_name"]); ?></span>
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

	<table class="layui-table layui-table-rewrite" lay-skin="line" style="margin: 0;">
		<tbody>
			<tr>
				<td>设备厂商：</td>
				<td><p><?php echo ($details["vendor"]); ?></p></td>
			</tr>
			<tr>
				<td>设备型号：</td>
				<td><p><?php echo ($details["model"]); ?></p></td>
			</tr>
			<tr>
				<td>错误现象：</td>
				<td><p><?php echo ($details["error_message"]); ?></p></td>
			</tr>
			<tr>
				<td>备注信息：</td>
				<td><p>
					<?php if(!empty($details["comments"])): echo ($details["comments"]); ?>
					<?php else: ?>
						无<?php endif; ?>
				</p></td>
			</tr>
		</tbody>
	</table>

	<div class="title-bar" style="margin: 15px 0 15px 0;">
		<h4>初步分析</h4>
	</div>
	<p style="text-indent: 15px;"><?php echo ($details["reason"]); ?></p>

	<div class="title-bar" style="margin: 30px 0 30px 0;">
		<h4>处理记录</h4>
	</div>

	<!-- 时间轴插件 -->
	<div class="timeline">
		<div class="timeline-heading">
			<?php if(is_array($nodeData)): $i = 0; $__LIST__ = $nodeData;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><div class="timeline-table"><!-- 如果要隐藏子节点列表为该元素添加 timeline-hide <?php echo ($value["class"]); ?> -->

						<?php if(($value["id"]) <= $details["now_step"]["id"]): ?><div class="timeline-title-box">
								<div class="timeline-title pull-left">
									<div class="timeline-step">Step<?php echo ($value["id"]); ?></div>
								</div>
								<div class="timeline-title-text pull-left">
                                    <?php if(($details["version"]) == "new"): ?><span><?php echo ($value["step_name"]); ?> <i class="<?php if(!empty($value["class"])): ?>icon-caret-down<?php else: ?>icon-caret-up<?php endif; ?>"></i></span>
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
												<div class="timeline-content-left pull-left">
													<div class="timeline-user-face">
														<img src="<?php echo ($val["face"]); ?>" width="48" alt="">
													</div>
												</div>
												<div class="timeline-content-right pull-right">
													<?php echo ($val["log_content"]); ?>
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
													<div class="timeline-other">
														<?php if(($val["recorder"]) == "OASystem"): ?>OA系统
														<?php else: ?>
															<?php echo ($val["nickname"]); endif; ?>
														<span class="timeline-divider">|</span><?php echo ($val["timestamp"]); ?>
													</div>
												</div>
												<div class="clearfix"></div>
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

	<!-- 图像展示 -->
	<div id="picturePreview">
		<div class="pictureContainer"></div>
		<span class="closePreview" title="关闭"><i class="icon-remove icon-2x"></i></span>
	</div>


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
															<option value="<?php echo ($details["next_step"]["id"]); ?>">推送到：Step-<?php echo ($details["next_step"]["id"]); ?> <?php echo ($details["next_step"]["step_name"]); ?></option>
															<?php else: ?>
															<?php if(is_array($details["next_step"])): $i = 0; $__LIST__ = $details["next_step"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><option value="<?php echo ($value["id"]); ?>">推送到：Step<?php echo ($value["id"]); ?>-<?php echo ($value["step_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; endif; endif; endif; ?>
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
												<span>回退到：<?php echo ($fallback["step_name"]); ?></span><span>处理人：<?php echo ($fallback["nickname"]); ?></span>
											</div>
										</div><?php endif; ?>

									<div class="layui-form-item layui-form-text">
										<textarea name="log_content" placeholder="请输入处理内容" class="layui-textarea"></textarea>
									</div>
									<div class="layui-inline">
										<div class="layui-input-inline">
											<input type="file" name="Filedata"  lay-ext="zip|rar|jpg|png|gif|xls|xlsx|doc|docx|pdf" class="layui-upload-file" multiple>
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
										<input type="file" name="Filedata"  lay-ext="zip|rar|jpg|png|gif|xls|xlsx|doc|docx|pdf" class="layui-upload-file" multiple>
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