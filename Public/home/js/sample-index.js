/**
 * Created by Fulwin on 2016/12/6.
 */

$(function(){

    $('.panel-click-slide').each(function(index){
        var _this = $(this);
        _this.find('.slide-btn').click(function(){
            if($(this).parent().next().css('display')=='block'){
                $(this).find('i').attr('class','icon-plus');
                $(this).parent().next().slideUp('fast');
            }else{
                $(this).find('i').attr('class','icon-minus');
                $(this).parent().next().slideDown('fast');
            }
        });
    });

/*

    layui.use(['layer','form'],function(){
        var layer = layui.layer;
        var form = layui.form();
        form.on('submit(sample)',function(data){
            var sample = $('#SearchHiddenForm');
            var Search = sample.find('#SearchText').val();	//获取到订单号
        })

    })
*/



});
