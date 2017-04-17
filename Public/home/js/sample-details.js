/**
 * 样品详情页js
 */

$(function(){
	
	$('#step li').each(function(){
		//alert(parentWidth)
		if($(this).find('.instructions')){
			var parentWidth = $(this).width();
			var childWidth = $(this).find('.instructions').width();
			var px = (parentWidth - childWidth) / 2;
			$(this).find('.instructions').css('left',px + 'px');
		}
	});
	
	var innerHeight = $(html).innerHeight();
	alert(innerHeight);
	$('.container').css('height',(innerHeight-95)+'px');
	
	
	//审批下拉列表
	$('#drop-down').click(function(){
		$(this).next().slideToggle('fast');
	});
	
	$('.drop-down-list ul li').click(function(){
		$('.drop-down-list').slideUp('fast');
		if($(this).attr('index')==2){
			$('.select-box').hide();
			$('#drop-down').val($(this).text());
			$('#cid').val('');
			$('#enginner').val('选择');
		}else{
			$('.select-box').show();
			$('#drop-down').val($(this).text());
		}
		$('#status').val($(this).attr('index'));
	});
	
	//单击选择产品工程师
	$('#enginner').click(function(){
		//配置dialog弹出层样式配置及内容
		layer.open({
			type : 1,	//layer提供了5种层类型。可传入的值有：0（信息框，默认）1（页面层）2（iframe层）3（加载层）4（tips层）。
			btn : ['确定'],
			yes : function(index,layero){
				$('#user-select-box li').each(function(){
					if($(this).hasClass('active')){
						$('#enginner').val($(this).text());
						$('#cid').val($(this).attr('index'));
					}
				});
				layer.close(index);
			},
			title : '选择产品工程师',
			area : ['640px','400px'],
			shadeClose : true,		//点击幕布区域是否关闭弹出层
			shade : 0.5,			//幕布透明度
			fix : true,				//鼠标滚动时，层是否固定在可视区域
			moveType : 1,			//拖动效果
			shift : 5,				//动画0-6
			content : $('.users-box'),
		});
	});
	
	
	$('.users-box ul li').click(function(){
		$(this).addClass('active').siblings().removeClass('active');
	});
	
	$('#ajax-submit').click(function(){
		$.ajax({
			url : ThinkPHP['AJAX'] + '/Sample/audit',
			type : 'POST',
			data : $('.sampleAid').serialize(),
			dataType : 'json',
			beforeSend : function(){
				var loading = layer.load(2);
			},
			success : function(response){
				if(response.flag){
					$('#audit').fadeOut('normal');
					layer.msg(response.msg,{icon:1,time:2000});
					layer.closeAll('loading');
					setTimeout(function(){
						$('#audit').remove();
						location.reload();
					},400);
				}else{
					layer.msg(response.msg,{icon:0,time:2000});
					layer.closeAll('loading');
				}
			}
		});
	});
	
	
	
	
});














