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
	<link rel="stylesheet" href="/Public/Home/css/addCustomer.css">
	<link rel="stylesheet" href="/Public/Home/css/productFilter.css">
<!-- 引入css文件 -->
	<script src="/Public/Home/js/jquery.min.js"></script>
	<script src="/Public/Home/js/bootstrap.min.js"></script>
	<script src="/Public/layui/layui.js"></script>
	<script src="/Public/Home/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/Public/Home/js/index.js"></script>

	
	<script src="/Public/Home/js/jquery.validate.min.js" charset="UTF-8"></script>
	<script src="/Public/webuploader/js/webuploader.js"></script>
	<script src="/Public/Home/js/jquery.form.js" charset="UTF-8"></script>
	<script src="/Public/Home/js/productFilter.js" charset="UTF-8"></script>
	<script src="/Public/Home/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
	<script src="/Public/Home/js/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
	<script src="/Public/Home/js/jquery.Huploadify.js"></script>
	<script src="/Public/Home/js/customer.js" charset="UTF-8"></script>
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
		<li class="active">新增客诉</li>
	</ol>

		</div>

		<div id="content">
			<div class="container-fluid" id="content-box">
				

	<div class="add-customer">

		<form class="layui-form">
			<div class="layui-inline">
				<label class="layui-form-label">客诉日期</label>
				<div class="layui-input-inline">
					<input type="text" name="cc_time" readonly class="layui-input form_date" lay-verify="required" placeholder="请选择日期">
				</div>
			</div>
			<div class="layui-inline">
				<label class="layui-form-label">处理人</label>
				<div class="layui-input-inline">
					<select name="operation_person">
						<?php if(is_array($userlist)): $i = 0; $__LIST__ = $userlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><option value="<?php echo ($value["id"]); ?>"><?php echo ($value["nickname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
					</select>
				</div>
			</div>
			<div class="layui-form-item layui-form-item-rewrite">
				<label class="layui-form-label">客户</label>
				<div class="layui-input-block">
					<input type="text" name="customer" class="layui-input" placeholder="例如 Orienta">
				</div>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label">订单号</label>
				<div class="layui-input-block">
					<input type="text" name="sale_order" class="layui-input" placeholder="例如 188-Orienta-A">
				</div>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label">产品</label>
				<div class="layui-input-block pn-position-relative">
					<input type="hidden" name="managers" value="">
					<input type="text" name="pn" readonly class="layui-input product-select" lay-verify="required" placeholder="例如 APCSFP43123CDL20">
					<button type="button" class="clear-empty" title="清空"><i class="layui-icon">&#x1006;</i></button>
				</div>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label">设备厂商</label>
				<div class="layui-input-block">
					<input type="text" name="vendor" class="layui-input" placeholder="例如 Huawei">
				</div>
			</div>
			<div class="layui-form-item">
				<label class="layui-form-label">设备型号</label>
				<div class="layui-input-block">
					<input type="text" name="model" class="layui-input" placeholder="例如 MA5600T">
				</div>
			</div>
			<div class="layui-form-item layui-form-text">
				<label class="layui-form-label">错误信息</label>
				<div class="layui-input-block">
					<textarea name="error_message" lay-verify="required" placeholder="输入详细报错信息，包括交换机报错信息、客户反馈的详细信息等等" class="layui-textarea"></textarea>
				</div>
			</div>
			<div class="layui-form-item layui-form-text">
				<label class="layui-form-label">原因分析</label>
				<div class="layui-input-block">
					<textarea name="reason" lay-verify="required" placeholder="根据客户反馈的信息做出的初步分析原因" class="layui-textarea"></textarea>
				</div>
			</div>
			<div class="layui-form-item layui-form-text">
				<label class="layui-form-label">备注</label>
				<div class="layui-input-block">
					<textarea name="comments" placeholder="其他补充信息，例如故障产品的数量，序列号等。" class="layui-textarea"></textarea>
				</div>
			</div>
			<div class="layui-form-item">
				<div class="layui-input-block">
					<input type="file" name="Filedata" class="layui-upload-file add-customer-upload-file">
					<ul id="attachment-list"></ul>
				</div>
			</div>
			<div class="layui-form-item">
				<div class="layui-input-block">
					<input type="hidden" name="salesperson" value="<?php echo ($face["account"]); ?>">
					<button lay-submit lay-filter="customer" class="layui-btn">提交</button>
					<button type="reset" class="layui-btn layui-btn-primary">重置</button>
				</div>
			</div>
		</form>
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

	<div id="loading">
		<div class="loading-icon">
			<i class="icon-spinner icon-spin"></i>
		</div>
	</div>

	<!-- 产品筛选 -->
	<div id="layer-open-content" style="display: none;">
		<div class="product-filter-box">
			<div class="filter-search">
				<form class="layui-form">
					<div class="filter-search-block">
						<input type="text" name="search" placeholder="搜索产品型号" class="layui-input">
						<button lay-submit class="product-search-btn" lay-filter="productSearch"><i class="layui-icon">&#xe615;</i></button>
					</div>
				</form>
			</div>
			<div class="product-category-box">
				<div class="filter-category">
					<div class="filter-name pull-left">类型：</div>
					<div class="filter-items pull-left">
						<ul class="filter-items-type">
							<?php if(is_array($productFilter["types"])): $i = 0; $__LIST__ = $productFilter["types"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if($i > 6): ?><li category="type" class="filter-item overflow" title="<?php echo ($value["type"]); ?>"><?php echo ($value["type"]); ?></li>
									<?php else: ?>
									<li category="type" class="filter-item" title="<?php echo ($value["type"]); ?>"><?php echo ($value["type"]); ?></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
						</ul>
					</div>
					<div class="filter-more pull-left">
						<?php if(count($productFilter['types']) > 6): ?><i class="icon-chevron-down an-more-btn" flag="off"></i><?php endif; ?>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="filter-category">
					<div class="filter-name pull-left">波长：</div>
					<div class="filter-items pull-left">
						<ul class="filter-items-wavelength">
							<?php if(is_array($productFilter["wavelengths"])): $i = 0; $__LIST__ = $productFilter["wavelengths"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if($i > 6): ?><li category="wavelength" class="filter-item overflow" as_name="<?php echo ($value["wavelength"]); ?>" title="<?php echo ($value["wavelength"]); ?>"><?php echo ($value["wavelength"]); ?></li>
									<?php else: ?>
									<li category="wavelength" class="filter-item" as_name="<?php echo ($value["wavelength"]); ?>" title="<?php echo ($value["wavelength"]); ?>"><?php echo ($value["wavelength"]); ?></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
						</ul>
					</div>
					<div class="filter-more pull-left">
						<?php if(count($productFilter['wavelengths']) > 6): ?><i class="icon-chevron-down an-more-btn" flag="off"></i><?php endif; ?>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="filter-category">
					<div class="filter-name pull-left">距离：</div>
					<div class="filter-items pull-left">
						<ul class="filter-items-reach">
							<?php if(is_array($productFilter["reachs"])): $i = 0; $__LIST__ = $productFilter["reachs"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if($i > 6): ?><li category="reach" class="filter-item overflow" title="<?php echo ($value["reach"]); ?>"><?php echo ($value["reach"]); ?></li>
									<?php else: ?>
									<li category="reach" class="filter-item" title="<?php echo ($value["reach"]); ?>"><?php echo ($value["reach"]); ?></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
						</ul>
					</div>
					<div class="filter-more pull-left">
						<?php if(count($productFilter['reachs']) > 6): ?><i class="icon-chevron-down an-more-btn" flag="off"></i><?php endif; ?>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="filter-category">
					<div class="filter-name pull-left">接口：</div>
					<div class="filter-items pull-left">
						<ul class="filter-items-connector">
							<?php if(is_array($productFilter["connectors"])): $i = 0; $__LIST__ = $productFilter["connectors"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if($i > 6): ?><li category="connector" class="filter-item overflow" title="<?php echo ($value["connector"]); ?>"><?php echo ($value["connector"]); ?></li>
									<?php else: ?>
									<li category="connector" class="filter-item" title="<?php echo ($value["connector"]); ?>"><?php echo ($value["connector"]); ?></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
						</ul>
					</div>
					<div class="filter-more pull-left">
						<?php if(count($productFilter['connectors']) > 6): ?><i class="icon-chevron-down an-more-btn" flag="off"></i><?php endif; ?>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="filter-category">
					<div class="filter-name pull-left">环境：</div>
					<div class="filter-items pull-left">
						<ul class="filter-items-casetemp">
							<?php if(is_array($productFilter["casetemps"])): $i = 0; $__LIST__ = $productFilter["casetemps"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if($i > 6): ?><li category="casetemp" class="filter-item overflow" as_name="<?php echo ($value["casetemp"]); ?>" title="<?php echo ($value["casetemp_as_name"]); ?>"><?php echo ($value["casetemp_as_name"]); ?></li>
									<?php else: ?>
									<li category="casetemp" class="filter-item" as_name="<?php echo ($value["casetemp"]); ?>" title="<?php echo ($value["casetemp_as_name"]); ?>"><?php echo ($value["casetemp_as_name"]); ?></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
						</ul>
					</div>
					<div class="filter-more pull-left">
						<?php if(count($productFilter['casetemps']) > 6): ?><i class="icon-chevron-down an-more-btn" flag="off"></i><?php endif; ?>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
			<hr>
			<div class="product-list-box">
				<ul>
					<?php if(is_array($productFilter["defaultData"])): $i = 0; $__LIST__ = $productFilter["defaultData"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li pro_id="<?php echo ($value["id"]); ?>" manager="<?php echo ($value["manager"]); ?>"><?php echo ($value["pn"]); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
			</div>
			<form id="filter-map" style="display: none;">
				<input type="hidden" name="type" value="">
				<input type="hidden" name="wavelength" value="">
				<input type="hidden" name="reach" value="">
				<input type="hidden" name="connector" value="">
				<input type="hidden" name="casetemp" value="">
			</form>
			<div class="reset-filter-box">
				<div class="pull-right"><button class="layui-btn layui-btn-danger reset-filter-btn">重置筛选</button></div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>


	
	<script>

		/**
		 * 获取当前时间
		 */
		function p(s) {
			return s < 10 ? '0' + s: s;
		}
		var myDate = new Date();
		//获取当前年
		var year = myDate.getFullYear();
		//获取当前月
		var month = myDate.getMonth()+1;
		//获取当前日
		var date = myDate.getDate();
		var now = year+'-'+p(month)+"-"+p(date);
		$('.form_date').val(now);	//填充当天时间

		//定义初始化数据
		var filter_content = $('#layer-open-content').html();
		$('.form_date').datetimepicker({
			language:  'zh-CN',
			format:'yyyy-mm-dd',
			weekStart: 1,
			todayBtn:  1,
			autoclose: 1,
			todayHighlight: 1,
			endDate : new Date(),
			pickerPosition: "bottom-right",
			startView: 2,
			minView: 2,
			forceParse: 0
		});

		$('.clear-empty').click(function(){
			$(this).prev().val('');
		});

		layui.use(['layer','form'], function(){
			var layer = layui.layer,
					form = layui.form();

			form.on('submit(productSearch)', function(data){
				$.ajax({
					url : ThinkPHP['AJAX'] + '/Sample/productSearch',
					type : 'POST',
					data : data.field,
					dataType : 'json',
					success : function( response ){
						if( response.flag > 0 ){
							var pns = '';
							for( var key in response.data ){	//拼装所有产品型号数据
								pns += '<li pro_id="'+ response.data[key].id +'">'+ response.data[key].pn +'</li>\r\n';
							}
							$('.product-list-box ul').html(pns);
						}else{
							layer.msg(response.msg);
						}
					}
				});
				return false;
			});
		});

		//点击选择产品展开筛选器
		var pro_element = null;
		$(document).on('click', '.product-select', function(){
			pro_element = $(this);
			var filter_dialog = layer.open({
				type : 1,
				title : '选择产品',
				area: ['1200px'],
				shade: ['0.5','#000'],
				content: filter_content,
				cancel : function(index, layero){
					$('#filter-map input').val('');
					layer.close(index);
				}
			});
		});


		//将产品信息添加到栏位
		$(document).on('click', '.product-list-box ul li' ,function(){
			if( pro_element.val() == '' ){
				pro_element.val($(this).text());
			}else{
				pro_element.val( pro_element.val() + ' , ' + $(this).text() );
			}
			if( pro_element.prev().val() == '' ){
				pro_element.prev().val($(this).attr('manager'));
			}else{
				pro_element.prev().val( pro_element.prev().val() + ',' + $(this).attr('manager') );
			}
			/*pro_element.prev().val($(this).attr('pro_id'));
			pro_element.prev().prev().val($(this).attr('manager'));*/
			layer.closeAll();
			$('#filter-map input').val('');
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