/**
 * Created by GCX on 2017/7/11.
 */
$(function(){

   layui.use(['form','layer'],function(){
       var form = layui.form(),
           layer = layui.layer;


       form.on('submit(submit)',function( data ){

           $.ajax({
               url : ThinkPHP['AJAX'] + '/File/apply',
               dataType : 'json',
               type : 'POST',
               data : data.field,
               success : function( response ){
                   if( response.flag > 0 ){
                        layer.msg(response.msg,{icon:1,time:2000})
                       setTimeout(function () {
                           location.reload();
                       },2000);
                   }else {
                       layer.msg(response.msg,{icon:2,time:2000})
                   }
               }
           });

           return false;

       })



   })



});