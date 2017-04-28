$(function(){

    //如果有留言就显示
    $('.myTooltip').hover(function(){
        $("[data-toggle='popover']").popover('show');
    },function()
    {
        $("[data-toggle='popover']").popover('hide');
    });




    layui.use(['layer','form'], function() {
        var layer = layui.layer;
        var form = layui.form();
        var AllData = '{"sample_detail":[';	//定义空对象用于存放订单内容
        form.on('submit(formDemo)',function(data){
           var detail_form = $('#detail_form');


           var state = $('.modal-body').find('select[name=state]').val(),
               desc = $('.modal-body').find('textarea[name =desc]').val();

           AllData += '{"state":"'+ state +'","desc":"'+ desc +'"},';
           AllData = AllData.substring(0,AllData.length-1);
           AllData += ']}';

            $.ajax({
                url : ThinkPHP['AJAX'] + '/Sample/detail',
                type : 'post',
                dataType : 'JSON',
                data : {
                    data : AllData,
                },
                success : function( response ){
                    if (response.flag > 0){

                    }else{

                    }
                }
            })


            return false; //阻止表单跳转。
        })


    })


});