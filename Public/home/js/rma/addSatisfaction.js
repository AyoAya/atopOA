/**
 * Created by Fulwin on 2017/7/7.
 */
$(function(){

    layui.use(['form','layer'], function(){

        $('#content').scroll(function(){

            var top = $('#content').scrollTop();
            setTimeout(function(){
                $('.sf-box-l').css({top: (top+15)+'px',bottom: '0px'});
            },200);

        });


    });

});