/**
 * 公用js
 */

$(function(){
	
	/**
	 * 关闭弹出层
	 */
	$('span.close').click(function(){
		$(this).parents('.modal').fadeOut('normal');
		if($(this).parents('.modal2')){
			$(this).parents('.modal2').fadeOut('normal');
		}
		if($(this).parents('.modal3')){
			$(this).parents('.modal3').fadeOut('normal');
		}
	});

	/**
	 * 单击刷新当前页
	 */
	$('h2 a.refresh').click(function(){
		location.reload();
	});
	
	/**
	 * 单击返回上一页
	 */
	$('h2 em.back').click(function(){
		history.back();
	});

	

	/**
	 * ----------------------------------------------------------------------------------------
	 * 如果分页数据未达到系统设定标准则不显示分页元素
	 * ----------------------------------------------------------------------------------------
	 */
	if($('#page div').children().length <= 1) {
		$('#page').remove();
	}
	
	
});






