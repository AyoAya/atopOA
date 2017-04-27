$(function(){

	layui.use('element', function(){
		var element = layui.element();
	});

	$('.step-square li').hover(function(){
		$(this).find('.dispose-info').removeClass('sr-only');
	},function(){
		$(this).find('.dispose-info').addClass('sr-only');
	});







});