/**
 * 新增客诉页面js
 */

$(function(){

	//验证表单
	$('#customer-form').validate({
		rules : {
			cc_time : {
				required : true,
			},
			salesperson : {
				required : true,
			},
			customer : {
				required : true,
			},
			sale_order : {
				required : true,
			},
			pn : {
				required : true,
			},
			vendor : {
				required : true,
			},
			model : {
				required : true,
			},
			error_message : {
				required : true,
			}
		},
		messages : {
			cc_time : {
				required : '日期不得为空',
			},
			salesperson : {
				required : '请选择销售人员',
			},
			customer : {
				required : '请输入客户',
			},
			sale_order : {
				required : '请输入订单号',
			},
			pn : {
				required : '请输入产品信息',
			},
			vendor : {
				required : '请输入设备厂商',
			},
			model : {
				required : '请输入设备型号',
			},
			error_message : {
				required : '请输入错误信息',
			}
		},
		ignore : "",
		errorElement : 'em',
		errorPlacement : function(error, element) {
		    error.appendTo(element.parent().find('p'));  
		    element.css({
		    	'border' : 'solid 1px red',
		    });
		},
		success : function(label){
			console.log(label.parent().parent().find('input').css({'border':'solid 1px #ccc'}));
		},
		debug : true,
		submitHandler : function(form){
			alert('提交事件');
		}
	});
	
});



