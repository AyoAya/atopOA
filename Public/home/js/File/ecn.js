/**
 * Created by GCX on 2017/8/10.
 */
$(function(){

    layui.use(['form','layer'],function(){
        var form = layui.form(),
            layer = layui.layer;

        $('.pos-box').on('click','.pos-info',function(){
            if($(this).next().css('display') == 'none'){
                $(this).next().css('display','block');
            }
        })

       var hidInfo = $('.layui-form .pos-box').html();
       var step_num = 1;

        // 添加一列
        $('.layui-form').on('click','.add-hid',function(){
            $('.pos-box').append(hidInfo);
            $('.pos-box .layui-form-item').each(function(index){
                $(this).find('.num').text(index + 1);
            })
        });

        // 移除一列
        $('.pos-box').on('click','.close-rev',function(){
            let _parent = $(this).parents('.layui-form-item');
            let children_length = $('.pos-box').children().length;
            //如果页面只有一栏则不允许用户再删除并提示
            if( children_length > 1 ){
                step_num--;
                _parent.remove();
                //为防止用户删除时导致的序号错误，当用户删除任何一栏时重新进行排序
                $('.pos-box .layui-form-item').each(function(index){
                    $(this).find('.num').text( index + 1 );
                });
            }else{
                layer.msg('至少保留一栏', {icon: 2, time: 2000});
            }
        });

        $('.pos-box').on('click','.ok-btn',function(){
            $(this).parents('.hid-position').css('display','none');
        })

        // 点击添加将选择的部门添加进显示框
        $('.pos-box').on('click','.pos-span',function(){

            var vid = $(this).attr('revid'),
                vTitle = $(this).text();

            var boxCont = '<div class="act act_'+ vid +'" revid="'+vid+'">'+vTitle+' <i class="icon-remove x-close"></i></div>';
            var boxInfo = $(this).parents('.hid-position').prev();

            if( boxInfo.find('.act_' + vid).length ){
                layer.msg(vTitle + '已存在');
            }else{
                boxInfo.append(boxCont);
            }

        })
        //点击X删除队列中的职位
        $('.position-box').on('click','.x-close',function(){
            $(this).parent().remove();
        })

        //抄送职位
        $('.position-box').on('click','.pos-info',function(){
            if($(this).next().css('display') == 'none'){
                $(this).next().css('display','block');
            }
        })
        //抄送职位
        $('.position-box').on('click','.ok-btn',function(){
            $(this).parents('.hid-position').css('display','none');
        })

        // 抄送职位点击添加将选择的部门添加进显示框
        $('.position-box').on('click','.pos-span',function(){

            var vid = $(this).attr('revid'),
                vTitle = $(this).text();

            var boxCont = '<div class="act act_'+ vid +'" revid="'+vid+'">'+vTitle+' <i class="icon-remove x-close"></i></div>';
            var boxInfo = $(this).parents('.hid-position').prev();

            if( boxInfo.find('.act_' + vid).length ){
                layer.msg(vTitle + '已存在');
            }else{
                boxInfo.append(boxCont);
            }

        })
        //抄送职位点击X删除队列中的职位
        $('.pos-box').on('click','.x-close',function(){
            $(this).parent().remove();
            if($(this).parents('.pos-info').css('display') == 'none'){
                $(this).parents('.pos-info').css('display','none')
            }else{
                $(this).parents('.pos-info').css('display','block')
            }
        })

        form.on('submit(submit)',function(data){

            if(!(data.field.name)){
                layer.msg('请输入规则名称!',{time:2000});
                return false;
            }

            // 抄送职位
            var tmpArr = [];
            $('.position-box').find('.pos-info .act').each(function(index){
                tmpArr.push($(this).attr('revid'));
            })

            //评审职位
            var review = [];
            $('.pos-box .layui-form-item').each(function(index){
                var tmpArray = new Array();
                $(this).find('.pos-info .act').each(function(i){
                    var tmpReview = new Object();
                    tmpReview.rev = ($(this).attr('revid'));
                    tmpArray.push(tmpReview)
                })
                review.push(tmpArray);
            })

            if(tmpArr == ''){
                layer.msg('请选择抄送职位!',{time:2000});
                return false;
            }
            if(review == ''){
                layer.msg('请选择评审人!',{time:2000});
                return false;
            }


            $.ajax({
                url : ThinkPHP['AJAX'] + '/File/ecn',
                type : 'POST',
                dataType : 'json',
                data : {
                    position : tmpArr,
                    data : data.field,
                    revData : review
                },
                success : function( response ){
                    if( response.flag > 0 ){
                        layer.msg(response.msg,{icon:1,time:2000});
                        location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/File';

                    }else{

                        layer.msg(response.msg,{icon:2,time:2000});
                        location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/File/ecn';
                    }
                }
            });



            return false;


        })








    })

});