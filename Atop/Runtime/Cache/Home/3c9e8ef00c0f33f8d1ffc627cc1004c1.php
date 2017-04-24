<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>

	<meta charset="UTF-8">
	<!--<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">-->
	<link href="/Public/Home/img/favicon.ico" rel="shortcut icon">

	
    <title>兼容表</title>
<!-- 网站标题 -->
	<link rel="stylesheet" href="/Public/layui/css/layui.css">
	<link rel="stylesheet" href="/Public/Home/css/bootstrap.min.css">
	<link rel="stylesheet" href="/Public/Home/css/font-awesome.min.css">
	<link rel="stylesheet" href="/Public/Home/css/jquery.mCustomScrollbar.css">
	<link rel="stylesheet" href="/Public/Home/css/iconfont.css">
	<link rel="stylesheet" href="/Public/Home/css/basic.css">
	<link rel="stylesheet" href="/Public/Home/css/Theme/<?php echo ($face["theme"]); ?>.css">
	<link rel="stylesheet" href="/Public/Home/css/promotion_index.css">

	
    <link rel="stylesheet" href="/Public/layui/css/layui.css">
    <link rel="stylesheet" href="/Public/Home/css/compatible-add.css">
    <link rel="stylesheet" href="/Public/Home/css/compatible.css">
<!-- 引入css文件 -->
	<script src="/Public/Home/js/jquery.min.js"></script>
	<script src="/Public/Home/js/bootstrap.min.js"></script>
	<script src="/Public/layui/layui.js"></script>
	<script src="/Public/Home/js/jquery.mCustomScrollbar.concat.min.js"></script>
	<script src="/Public/Home/js/index.js"></script>

	
    <script src="/Public/layui/layui.js"></script>
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
        <li class="active">兼容表</li>
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
				


    <div class="pull-left" role="group">
        <?php if($_GET['pn']!= '' OR $_GET['vendor']!= '' OR $_GET['model']!= '' OR $_GET['search']!= '' OR $_GET['state']!= '' OR isset($condition)): ?><a href="/Compatibility" class="layui-btn layui-btn-danger">
                <span class="glyphicon glyphicon-remove"></span> 清除筛选
            </a><?php endif; ?>
        <!--<button class="btn btn-primary" data-toggle="modal" data-target="#filter">
            <span class="glyphicon glyphicon-filter"></span> 筛选
        </button>-->
        <a href="/Compatibility/add" class="layui-btn layui-btn-primary">
            <span class="glyphicon glyphicon-plus"></span> 添加兼容记录
        </a>
    </div>

    <div class="pull-right">
        <form class="layui-form" method="get" action="/Compatibility">
            <div class="layui-inline layui-inline-rewrite">
                <div class="layui-input-inline">
                    <input type="text" name="search" placeholder="搜索" class="layui-input" value="<?php if(($_GET['search']) != ""): echo ($_GET['search']); endif; ?>">
                    <button class="search-btn"><i class="layui-icon">&#xe615;</i></button>
                </div>
            </div>
        </form>
    </div>

    <div class="clearfix" style="margin-bottom: 15px;"></div>



    <?php if(!empty($compatiblematrix)): ?><!--<div class="well">
            <form role="form" class="form-inline" id="form-filter">
                <div class="form-group">
                    <label>状态：</label>
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-default btn-sm <?php if(($condition["state"]) == "1"): ?>active<?php endif; ?>">
                            <input type="radio" name="Full" id="option1" autocomplete="off"> 完全兼容
                        </label>
                        <label class="btn btn-default btn-sm <?php if(($condition["state"]) == "2"): ?>active<?php endif; ?>">
                            <input type="radio" name="General" id="option2" autocomplete="off"> 能够使用
                        </label>
                        <label class="btn btn-default btn-sm <?php if(($condition["state"]) == "3"): ?>active<?php endif; ?>">
                            <input type="radio" name="Cannot" id="option3" autocomplete="off"> 不能兼容
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <input type="text" placeholder="产品型号" name="pn_name" id="pn_value" class="form-control input-sm" readonly data-target="#product-modal" data-toggle="modal" value="<?php if(isset($condition["pn_name"])): echo ($condition["pn_name"]); endif; ?>">
                </div>
                <div class="form-group">
                    <input type="text" placeholder="设备品牌" name="vendor_name" id="vendor_value" class="form-control input-sm" readonly data-target="#vendorModal" data-toggle="modal" value="<?php if(isset($condition["vendor_name"])): echo ($condition["vendor_name"]); endif; ?>">
                </div>
                <div class="form-group">
                    <input type="text" placeholder="设备型号" name="model" class="form-control input-sm" value="<?php if(isset($condition["model"])): echo ($condition["model"]); endif; ?>">
                </div>
                <div class="form-group">
                    <input type="text" placeholder="原厂型号" name="original" class="form-control input-sm" value="<?php if(isset($condition["original"])): echo ($condition["original"]); endif; ?>">
                </div>
                <div class="form-group">
                    <button type="reset" class="btn btn-default btn-sm">重置</button>
                    <button type="submit" class="btn btn-primary btn-sm">筛选</button>
                </div>
            </form>
        </div>-->
        <table class="layui-table table-compatibility">
            <thead>
                <tr>
                    <th>序号</th>
                    <th>产品型号</th>
                    <th>设备品牌</th>
                    <th>设备型号</th>
                    <th>状态</th>
                    <th>版本</th>
                    <th>原厂型号</th>
                    <th>备注</th>
                </tr>
            </thead>
            <tbody>
                <?php if(is_array($compatiblematrix)): $i = 0; $__LIST__ = $compatiblematrix;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><tr>
                        <td class="td_num">
                            <?php if($_GET['p']AND $_GET['p']!= 1): echo ($pagenumber*$limitsize+$i); ?>
                            <?php else: ?>
                                <?php echo ($i); endif; ?>
                        </td>
                        <td class="td_pn"><a href="/Compatibility/details/id/<?php echo ($value["id"]); ?>" style="color: #428bca;"><?php echo ($value["Productrelationships"]["pn"]); ?></a></td>
                        <td class="td_brand"><?php echo ($value["VendorBrand"]["brand"]); ?></td>
                        <td class="td_model"><?php echo ($value["model"]); ?></td>
                        <?php if($value["state"] == 1): ?><td class="state"><a href="/Compatibility?state=<?php echo ($value["state"]); ?>"><span class="pass"><?php echo ($value["state_text"]); ?></span></a></td>
                        <?php elseif($value["state"] == 2): ?>
                            <td class="state"><a href="/Compatibility?state=<?php echo ($value["state"]); ?>"><span class="comm"><?php echo ($value["state_text"]); ?></span></a></td>
                        <?php else: ?>
                            <td class="state"><a href="/Compatibility?state=<?php echo ($value["state"]); ?>"><span class="fail"><?php echo ($value["state_text"]); ?></span></a></td><?php endif; ?>
                        <td class="td_version"><?php echo ($value["version"]); ?></td>
                        <td class="td_original"><?php echo ($value["original"]); ?></td>
                        <td class="td_comment"><?php echo ($value["comment"]); ?></td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
        <ul class="pagination"><?php echo ($pageShow); ?></ul>
    <?php else: ?>
        <div id="empty-data">
            <p><i class="icon-info-sign"></i></p>
            <p>没有数据</p>
        </div><?php endif; ?>



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
	

    <!-- 产品型号选择模态框 -->
    <div class="modal fade bs-example-modal-lg" id="product-modal" role="dialog" tabindex="0" aria-labelledby="productLabel">
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
                    <span class="modal-title">选择设备品牌</span>
                </div>
                <div class="modal-body">
                    <ul id="brandList">
                        <?php if(is_array($vendor)): $i = 0; $__LIST__ = $vendor;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$value): $mod = ($i % 2 );++$i;?><li belong="<?php echo ($value["id"]); ?>"><?php echo ($value["brand"]); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>



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