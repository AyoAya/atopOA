$(function(){

	/*layui.use('element', function(){
		var element = layui.element();
	});*/

	/*$('.step-square li').hover(function(){
		$(this).find('.dispose-info').removeClass('sr-only');
	},function(){
		$(this).find('.dispose-info').addClass('sr-only');
	});*/


	//如果有留言就显示
	$('.myTooltip').hover(function(){
		$(this).find('.popover-box').removeClass('sr-only');
	},function(){
		$(this).find('.popover-box').addClass('sr-only');
	});




});