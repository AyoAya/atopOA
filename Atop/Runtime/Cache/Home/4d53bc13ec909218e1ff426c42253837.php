<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>

	<meta charset="UTF-8">
	<!--<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">-->
	<link href="/Public/Home/img/favicon.ico" rel="shortcut icon">

	
    <title>项目详情</title>
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
    <link rel="stylesheet" href="/Public/layui/css/layui.css">
    <link rel="stylesheet" href="/Public/Huploadify/Huploadify.css">
    <link rel="stylesheet" href="/Public/Home/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" href="/Public/Home/css/project/details.css">
<!-- 引入css文件 -->
	<script src="/Public/Home/js/jquery.min.js"></script>
	<script src="/Public/Home/js/bootstrap.min.js"></script>
	<script src="/Public/layui/layui.js"></script>
	<script src="/Public/Home/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/Public/Home/js/index.js"></script>

	
    <script src="/Public/Home/js/jquery.toastmessage.js"></script>
    <script src="/Public/layui/layui.js"></script>
    <!--<script src="/Public/layer/layer.js"></script>-->
    <script src="/Public/Huploadify/jquery.Huploadify.js"></script>
    <script src="/Public/Home/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
    <script src="/Public/Home/js/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
    <script src="/Public/Home/js/project.js"></script>
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
        <li><a href="/Project">项目管理</a></li>
        <li class="active">项目详情</li>
    </ol>

		</div>

		<div id="content">
			<div class="container-fluid" id="content-box">
				
    <!--<div class="commingSoon">
        <p><i class="icon-comment icon-5x"></i></p>
        <h1>功能研发中</h1>
        <p>Function is developing</p>
        <p>Comming Soon.</p>
    </div>-->

    <div class="page-header">
        <h1>
            <i class="icon-list-alt"></i>&nbsp;<?php echo ($basicInfo["pj_num"]); ?>
            <small><?php echo ($basicInfo["pj_name"]); ?>
                <?php if(isset($basicInfo["label_info"])): ?><span class="<?php echo ($basicInfo["label_class"]); ?>"><?php echo ($basicInfo["label_text"]); ?></span>
                    <span class="label label-info"><?php echo ($basicInfo["label_info"]); ?></span><?php endif; ?>
            </small>
            <p class="pull-right">创建人：<?php echo ($basicInfo["pj_create_person"]); ?><br>创建时间：<?php echo (date("Y-m-d",$basicInfo["pj_create_time"])); ?></p>
        </h1>
    </div>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs">
        <li role="presentation" class="<?php if(($tab) == "overview"): ?>active<?php endif; ?>">
            <a href="/Project/details/tab/overview/id/<?php echo ($getID); ?>" aria-controls="overview" role="tab">项目概览</a>
        </li>
        <li role="presentation" class="<?php if(($tab) == "plan"): ?>active<?php endif; ?>">
            <a href="/Project/details/tab/plan/id/<?php echo ($getID); ?>" aria-controls="plan" role="tab">项目计划</a>
        </li>
        <?php if(($basicInfo["pj_child"]) != "1"): ?><li role="presentation" class="<?php if(($tab) == "subprojects"): ?>active<?php endif; ?>">
                <a href="/Project/details/tab/subprojects/id/<?php echo ($getID); ?>" aria-controls="subprojects" role="tab">子项目开发</a>
            </li><?php endif; ?>
        <li role="presentation" class="<?php if(($tab) == "sample"): ?>active<?php endif; ?>">
            <a href="/Project/details/tab/sample/id/<?php echo ($getID); ?>" aria-controls="sample" role="tab">样品状况</a>
        </li>
        <li role="presentation" class="<?php if(($tab) == "document"): ?>active<?php endif; ?>">
            <a href="/Project/details/tab/document/id/<?php echo ($getID); ?>" aria-controls="profile" role="tab">归档文件</a>
        </li>
        <li role="presentation" class="<?php if(($tab) == "discuss"): ?>active<?php endif; ?>">
            <a href="/Project/details/tab/discuss/id/<?php echo ($getID); ?>" aria-controls="messages" role="tab">讨论区</a>
        </li>
    </ul>


    <?php if(($tab) == "overview"): ?><div class="well">
            <b>项目描述：</b>
            <p id="describe"><?php echo ($projects["pj_describe"]); ?></p>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <table class="table table-bordered overview-table">
                    <tbody>
                    <tr>
                        <td>项目负责人</td>
                        <td><?php echo ($projects["pj_responsible"]); ?></td>
                    </tr>
                    <tr>
                        <td>硬件设计</td>
                        <td><?php echo ($projects["pj_design"]); ?></td>
                    </tr>
                    <tr>
                        <td>FW开发</td>
                        <td><?php echo ($projects["pj_fw_development"]); ?></td>
                    </tr>
                    <tr>
                        <td>结构件设计</td>
                        <td><?php echo ($projects["pj_structure_design"]); ?></td>
                    </tr>
                    <tr>
                        <td>光器件设计</td>
                        <td><?php echo ($projects["pj_light_design"]); ?></td>
                    </tr>
                    <tr>
                        <td>ATE设计</td>
                        <td><?php echo ($projects["pj_ate_design"]); ?></td>
                    </tr>
                    <tr>
                        <td>DVT测试</td>
                        <td><?php echo ($projects["pj_dvt_test"]); ?></td>
                    </tr>
                    <tr>
                        <td>市场部</td>
                        <td><?php echo ($projects["pj_market"]); ?></td>
                    </tr>
                    <tr>
                        <td>项目管理员</td>
                        <td><?php echo ($projects["pj_management"]); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-6">
                <table class="table table-bordered overview-table">
                    <tbody>
                    <tr>
                        <td>产品状态</td>
                        <td><span class="label <?php echo ($progress["class"]); ?> label-primary-size"><?php echo ($progress["progress"]); ?></span></td>
                    </tr>
                    <tr>
                        <td>产品类型</td>
                        <td><?php echo ($projects["pj_family"]); ?></td>
                    </tr>
                    <tr>
                        <td>项目类型</td>
                        <td><?php echo ($projects["pj_type"]); ?></td>
                    </tr>
                    <tr>
                        <td>标准品名</td>
                        <td><?php echo ($projects["pj_standard_name"]); ?></td>
                    </tr>
                    <tr>
                        <td>潜在市场需求</td>
                        <td><?php echo ($projects["pj_market_demand"]); ?></td>
                    </tr>
                    <tr>
                        <td>目标客户</td>
                        <td><?php echo ($projects["pj_target_customer"]); ?></td>
                    </tr>
                    <tr>
                        <td>平台</td>
                        <td><?php echo ($projects["pj_platform"]); ?></td>
                    </tr>
                    <tr>
                        <td>目标成本</td>
                        <td><?php echo ($projects["pj_target_cost"]); ?></td>
                    </tr>
                    <tr>
                        <td>立项时间</td>
                        <td><?php echo ($projects["pj_add_pro_time"]); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div><?php endif; ?>

    <?php if(($tab) == "plan"): ?><table class="table table-bordered plan-table" id="planTable">
            <thead>
                <tr>
                    <th class="th_gate">步骤</th>
                    <th class="th_mile_stone">里程碑</th>
                    <th class="th_items">节点</th>
                    <th class="th_plan_start_time">计划开始时间</th>
                    <th class="th_plan_complate_time">计划结束时间</th>
                    <th class="th_start_time">实际开始时间</th>
                    <th class="th_complate_time">实际结束时间</th>
                    <th class="th_comments">备注</th>
                    <th class="th_state">状态</th>
                    <?php if(($_SESSION['user']['id']) == $auth): ?><th class="th_editor">操作</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if(is_array($gateResult)): $i = 0; $__LIST__ = $gateResult;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><tr>
                        <?php if(isset($value['rowspan'])): ?><td rowspan="<?php echo ($value['rowspan']); ?>" class="<?php echo ($value["plan"]["class"]); ?> td_center">Gate<?php echo ($value["gate"]); ?></td><?php endif; ?>
                        <?php if(isset($value['rowspan'])): ?><td rowspan="<?php echo ($value['rowspan']); ?>" class="<?php echo ($value["plan"]["class"]); ?>"><?php echo ($value["mile_stone"]); ?></td><?php endif; ?>
                        <td class="<?php echo ($value["plan"]["class"]); ?>"><?php echo ($value["items"]); ?></td>
                        <td class="plan_start_time change_input td_center <?php echo ($value["plan"]["class"]); ?>" originally="<?php echo ($value["plan_start_time"]); ?>"><?php echo ($value["plan_start_time"]); ?></td>
                        <td class="plan_complete_time change_input td_center <?php echo ($value["plan"]["class"]); ?>" originally="<?php echo ($value["plan_stop_time"]); ?>"><?php echo ($value["plan_stop_time"]); ?></td>
                        <td class="start_time change_input td_center <?php echo ($value["plan"]["class"]); ?>" originally="<?php echo ($value["plan"]["start_time"]); ?>"><?php echo ($value["plan"]["start_time"]); ?></td>
                        <td class="complete_time change_input td_center <?php echo ($value["plan"]["class"]); ?>" originally="<?php echo ($value["plan"]["complete_time"]); ?>"><?php echo ($value["plan"]["complete_time"]); ?></td>
                        <td class="comments change_input <?php echo ($value["plan"]["class"]); ?>" originally="<?php echo ($value["plan"]["comments_entity"]); ?>"><?php echo ($value["plan"]["comments"]); ?></td>
                        <td class="td_center"><span class="label <?php echo ($value["plan"]["state_class"]); ?>"><?php echo ($value["plan"]["state_text"]); ?></span></td>
                        <?php if(($_SESSION['user']['id']) == $auth): ?><td class="operation_td <?php echo ($value["plan"]["class"]); ?>">
                                <?php if(isset($value['show']) AND $value['show'] == 'true'): ?><button class="editor-btn btn btn-default btn-sm" plan_project="<?php echo ($getID); ?>" step="<?php echo ($value["gate"]); ?>" plan_node="<?php echo ($value["id"]); ?>"><i class="icon-edit"></i></button><?php endif; ?>
                            </td><?php endif; ?>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table><?php endif; ?>

    <?php if(($tab) == "subprojects"): if(($_SESSION['user']['id']) == $auth): ?><div class="add-subprojects">
                <a href="/Project/add/child/true/belong/<?php echo ($getID); ?>" class="btn btn-warning">添加子项目</a>
            </div><?php endif; ?>
        <?php if(!empty($projects)): ?>
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>序号</th>
                    <th>项目号</th>
                    <th>项目名</th>
                    <th>最近更新时间</th>
                    <th>负责人</th>
                    <th>进度</th>
                    <?php if(($_SESSION['user']['id']) == $auth): ?><th class="th_editor">编辑</th><?php endif; ?>
                </tr>
                </thead>
                <tbody>
                <?php if(is_array($projects)): $i = 0; $__LIST__ = $projects;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><tr>
                        <td><?php echo ($i); ?></td>
                        <td><a href="/Project/details/id/<?php echo ($value["id"]); ?>"><?php echo ($value["pj_num"]); ?></a></td>
                        <td><?php echo ($value["pj_name"]); ?></td>
                        <td><?php if(!empty($value["pj_update_time"])): echo (date('Y-m-d H:i:s',$value["pj_update_time"])); endif; ?></td>
                        <td><?php echo ($value["pj_responsible"]); ?></td>
                        <td><span class="label <?php echo ($value["node"]["class"]); ?>"><?php echo ($value["node"]["progress"]); ?></span></td>
                        <?php if(($_SESSION['user']['id']) == $auth): ?><td><a href="/Project/edit/id/<?php echo ($value["id"]); ?>" class="btn btn-default btn-xs"><i class="icon-edit"></i> 编辑</a></td><?php endif; ?>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
        <?php else: ?>
            <span class="empty_data"><i class="icon-info-sign"></i> 该项目未添加子项目</span><?php endif; endif; ?>

    <?php if(($tab) == "sample"): if(!empty($samples)): ?><table class="table">
                <thead>
                    <tr>
                        <th>序号</th>
                        <th>单号</th>
                        <th>品号</th>
                        <th>销售</th>
                        <th>客户</th>
                        <th>品牌</th>
                        <th>型号</th>
                        <th>数量</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(is_array($samples)): $i = 0; $__LIST__ = $samples;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><tr>
                            <td><?php echo ($i); ?></td>
                            <td><a href="http://<?php echo $_SERVER['HTTP_HOST'];?>/sampleDetails/<?php echo ($value["order"]); ?>"><?php echo ($value["totalorder"]); ?></a></td>
                            <td><?php echo ($value["product"]); ?></td>
                            <td><?php echo ($value["saleperson"]); ?></td>
                            <td><?php echo ($value["customer"]); ?></td>
                            <td><?php echo ($value["brand"]); ?></td>
                            <td><?php echo ($value["model"]); ?></td>
                            <td><?php echo ($value["number"]); ?></td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                </tbody>
            </table>
        <?php else: ?>
            <span class="empty_data"><i class="icon-info-sign"></i> 该项目没有相关型号的样品订单</span><?php endif; endif; ?>

    <?php if(($tab) == "document"): ?><div class="document-container">
            <input type="hidden" value="<?php echo ($getID); ?>" id="documentUploadProjectId">
            <!--<input type="hidden" id="documentProjectGate">-->
            <?php if(($_SESSION['user']['id']) == $auth): ?><div id="file">
                    <div class="btn-group pull-left" id="gateSelect">
                        <button type="button" class="btn btn-default" id="gateNum">Gate<?php echo ($gates["0"]["gate"]); ?> - <?php echo ($gates["0"]["mile_stone"]); ?></button>
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu" id="gateList">
                            <?php if(is_array($gates)): $i = 0; $__LIST__ = $gates;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li gate="<?php echo ($value["gate"]); ?>"><a href="#">Gate<?php echo ($value["gate"]); ?> - <?php echo ($value["mile_stone"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                    </div>
                    <div class="pull-left" id="fileUpload"></div>
                    <div class="clearfix"></div>
                </div><?php endif; ?>
            <!--<?php if(is_array($documents)): $i = 0; $__LIST__ = $documents;if( count($__LIST__)==0 ) : echo "$empty" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li class="document-file-list">
                    <div class="document-file-icon">
                        <span class="document-ext"><?php echo ($value["document_ext"]); ?></span>
                    </div>
                    <p class="document-file-name"><?php echo ($value["document_name"]); ?></p>
                    <p class="document-upload-time"><?php echo (date('Y-m-d H:i:s',$value["upload_time"])); ?></p>
                    <div class="document-download-box">
                        <p class="document-file-name-hidden"><?php echo ($value["document_name"]); ?></p>
                        <p class="download-node">
                            <a href="<?php echo ($value["document_path"]); ?>" class="btn btn-success btn-sm document-download-btn">下载</a>
                            <input type="hidden" name="document_path" value="<?php echo ($value["document_path"]); ?>">
                            <?php if(($_SESSION['user']['id']) == $auth): ?><button href="#" class="btn btn-danger btn-sm document-unlink-btn">删除</button><?php endif; ?>
                            <input type="hidden" value="<?php echo ($value["id"]); ?>">
                        </p>
                    </div>
                </li><?php endforeach; endif; else: echo "$empty" ;endif; ?>-->
            <?php if(!empty($gates)): if(isset($empty)): ?><span class="empty_data"><i class="icon-info-sign"></i> 该项目没有归档文件</span>
                <?php else: ?>
                    <?php if(is_array($gates)): $i = 0; $__LIST__ = $gates;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i; if(!empty($value["document"])): ?><div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">Gate<?php echo ($value["gate"]); ?> - <?php echo ($value["mile_stone"]); ?></h3>
                            </div>
                            <div class="panel-body">
                                <ul class="documents">
                                    <?php if(!empty($value["document"])): if(is_array($value["document"])): $i = 0; $__LIST__ = $value["document"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><li class="document-file-list-2">
                                                <span class="file-name-box">
                                                    <?php if(strtolower($val['document_ext']) == 'xls' OR strtolower($val['document_ext']) == 'xlsx'): ?><a href="<?php echo ($val["document_path"]); ?>" class="hover-remove" title="<?php echo ($val["document_name"]); ?>"><i class="file-icon" style="background: url('/Public/Home/img/icon-xls.png') center center no-repeat;"></i>&nbsp;<?php echo ($val["document_name"]); ?></a>
                                                    <?php elseif(strtolower($val['document_ext']) == 'doc' OR strtolower($val['document_ext']) == 'docx'): ?>
                                                        <a href="<?php echo ($val["document_path"]); ?>" class="hover-remove" title="<?php echo ($val["document_name"]); ?>"><i class="file-icon" style="background: url('/Public/Home/img/icon-doc.png') center center no-repeat;"></i>&nbsp;<?php echo ($val["document_name"]); ?></a>
                                                    <?php elseif(strtolower($val['document_ext']) == 'pdf' OR strtolower($val['document_ext']) == 'PDF'): ?>
                                                        <a href="<?php echo ($val["document_path"]); ?>" class="hover-remove" title="<?php echo ($val["document_name"]); ?>"><i class="file-icon" style="background: url('/Public/Home/img/icon-pdf.png') center center no-repeat;"></i>&nbsp;<?php echo ($val["document_name"]); ?></a>
                                                    <?php elseif(strtolower($val['document_ext']) == 'zip' OR strtolower($val['document_ext']) == 'rar'): ?>
                                                        <a href="<?php echo ($val["document_path"]); ?>" class="hover-remove" title="<?php echo ($val["document_name"]); ?>"><i class="file-icon" style="background: url('/Public/Home/img/icon-zip.png') center center no-repeat;"></i>&nbsp;<?php echo ($val["document_name"]); ?></a>
                                                    <?php else: ?>
                                                       <a href="<?php echo ($val["document_path"]); ?>" class="hover-remove" title="<?php echo ($val["document_name"]); ?>"><i class="file-icon" style="background: url('/Public/Home/img/icon-file.png') center center no-repeat;"></i>&nbsp;<?php echo ($val["document_name"]); ?></a><?php endif; ?>
                                                </span>
                                                <?php if(($_SESSION['user']['id']) == $auth): ?><input type="hidden" name="document_path" value="<?php echo ($val["document_path"]); ?>">
                                                    <button href="#" class="btn btn-danger btn-xs document-unlink-btn" title="删除该文件"><i class="icon-remove"></i></button>
                                                    <input type="hidden" value="<?php echo ($val["id"]); ?>"><?php endif; ?>
                                            </li><?php endforeach; endif; else: echo "" ;endif; ?>
                                        <?php else: ?>
                                        <span class="empty_data"><i class="icon-info-sign"></i> 该节点没有归档文件</span><?php endif; ?>
                                </ul>
                            </div>
                        </div><?php endif; endforeach; endif; else: echo "" ;endif; endif; ?>
            <?php else: ?>
                <span class="empty_data"><i class="icon-info-sign"></i> 该项目没有归档文件</span><?php endif; ?>
        </div><?php endif; ?>

    <?php if(($tab) == "discuss"): if(isset($_GET['floor'])): ?><input type="hidden" id="floorNum" value="<?php echo ($_GET['floor']); ?>"><?php endif; ?>
        <div class="message-container">
            <div class="message-board">
                <div class="jump-floor sr-only" id="jumpFloor">
                    <div class="jump-info">
                        <span>共计<b></b>楼，跳转到 <input type="text" class="form-control input-sm"> <button id="jump" type="button" class="btn btn-primary btn-sm">跳转</button></span>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <?php if(!empty($discuss)): if(is_array($discuss)): $i = 0; $__LIST__ = $discuss;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><div class="media" name="floor<?php echo ($i); ?>">
                            <div class="media-left">
                                <img class="media-object" src="<?php echo ($value["face"]); ?>" width="50" alt="...">
                                <p style="margin-top: 5px;"><center><small style="color: #888;"><?php echo ($value["nickname"]); ?></small></center></p>
                            </div>
                            <div class="media-body">
                                <div class="well well-sm other-info-box">
                                    <p class="other-info">
                                        <span class=" pull-right floor"><?php echo (date('Y-m-d H:i:s',$value["reply_time"])); ?>　|　<?php echo ($i); ?> #</span>
                                    </p>
                                    <p class="other-info">收件人：<?php echo ($namelist); ?>　<?php if(isset($value["cc_person_list_info"])): ?>｜　抄送人：<?php echo ($value["cc_person_list_info"]); endif; ?></p>
                                </div>
                                <div class="context"><?php echo ($value["discuss_context"]); ?></div>
                            </div>
                        </div><?php endforeach; endif; else: echo "" ;endif; ?>
                <?php else: ?>
                    <span class="empty_data"><i class="icon-info-sign"></i> 还没有任何发表记录</span><?php endif; ?>
            </div>
            <div class="form-group">
                <div class="well">
                    <p class="prompt-info">内容发表后将会以邮件通知到以下人员：</p>
                    <div class="recipient-list well well-sm">
                        <span>收件人：</span>
                        <ul>
                            <?php if(is_array($recipientUserinfo)): $i = 0; $__LIST__ = $recipientUserinfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li email="<?php echo ($value["email"]); ?>"><?php echo ($value["nickname"]); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul>
                    </div>
                    <div class="cc-people well well-sm">
                        <span>抄送人：</span>
                        <ul id="ccPersonList">
                            <?php if(is_array($defaultCClist)): $i = 0; $__LIST__ = $defaultCClist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li email="<?php echo ($value["email"]); ?>" user-id="<?php echo ($value["id"]); ?>"><?php echo ($value["nickname"]); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
                            <i class="not-default"></i>
                        </ul>
                        <b id="add-cc-people">添加</b>
                        <b id="reset-cc-people">重置</b>
                    </div>
                    <script id="container" name="content" type="text/plain"></script>
                </div>
                <!-- 配置文件 -->
                <script type="text/javascript" src="/Public/UEditor/ueditor.config.js"></script>
                <!-- 编辑器源码文件 -->
                <script type="text/javascript" src="/Public/UEditor/ueditor.all.js"></script>
                <!-- 实例化编辑器 -->
                <script type="text/javascript">
                    var ue = UE.getEditor('container',{
                        initialFrameHeight : 200,
                        /*initialFrameWidth : 1000,*/
                        wordCount : false,
                        toolbars : [
                            ['fullscreen', 'undo', '|', 'fontfamily', 'fontsize', 'forecolor', 'backcolor', 'emotion', 'insertimage','attachment', '|', 'bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', '|', 'insertorderedlist', 'insertunorderedlist', '|', 'selectall', 'cleardoc']
                        ]
                    });
                </script>
            </div>
            <div class="form-group discuss-submit-box">
                <input type="hidden" id="cc_list">
                <button type="button" id="submit-discuss-content" class="btn btn-primary" discuss_id="<?php echo ($getID); ?>">发表</button>
            </div>
            <!-- 添加抄送人模态框 -->
            <div class="modal fade" id="CCpeopleModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <p class="modal-title" id="myModalLabel">添加抄送人</p>
                        </div>
                        <div class="modal-body">
                            <form class="layui-form layui-form-pane">
                                <div class="layui-form-item">
                                    <label class="layui-form-label">部门</label>
                                    <div class="layui-input-block">
                                        <select name="select" id="dpmtSelect" lay-filter="change">
                                            <?php if(is_array($departments)): $i = 0; $__LIST__ = $departments;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><option value="<?php echo ($value["id"]); ?>"><?php echo ($value["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </form>
                            <ul id="cc-person-list">
                                <?php if(is_array($users)): $i = 0; $__LIST__ = $users;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li email="<?php echo ($value["email"]); ?>" user-id="<?php echo ($value["id"]); ?>" title="<?php echo ($value["nickname"]); ?>"><?php echo ($value["nickname"]); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
                            </ul>
                            <div class="well well-sm sr-only" id="has-been-box">
                                <p class="has-been-add">已添加的抄送人员：</p>
                                <ul id="addList">

                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                            <button type="button" class="btn btn-primary" id="done-cc-list">确定</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 回到顶部按钮 -->
        <div id="backtop">
            <span class="">回到<br/>顶部</span>
        </div><?php endif; ?>

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