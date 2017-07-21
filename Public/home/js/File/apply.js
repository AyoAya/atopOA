/**
 * Created by GCX on 2017/7/11.
 */
$(function(){

   layui.use(['form','layer'],function(){
       var form = layui.form(),
           layer = layui.layer;


       $('.banner-type li').on('click',function(){
           alert($(this).text());
       })

       form.on('submit(submit)',function( data ){

           $.ajax({
               url : ThinkPHP['AJAX'] + '/File/apply',
               dataType : 'json',
               type : 'POST',
               data : data.field,
               success : function( response ){
                   if( response.flag > 0 ){
                       layer.alert('您的编号为：'+response.num, {icon: 6,title:'申请编号成功'},function(){

                               location.reload();

                       });
                        //layer.msg(response.msg,{icon:1,time:2000})

                   }else {
                       layer.msg(response.msg,{icon:2,time:2000})
                   }
               }


           });

           return false;

       })



   })



});