/**
 * Created by Fulwin on 2017/6/21.
 */
$(function(){

    layui.use(['form','layer','upload','element'], function(){
        var form = layui.form(),
            layer = layui.layer;

        var step_num = 1;

        //获取基本结构
        var base_column = $('.approval-container').html();

        //点击添加一栏
        $('.add-column').click( function(){

            $('.approval-container').append(base_column);

            $('.approval-container .layui-form:last-child').find('.approval-number p span').text( ++step_num );

            form.render();

        });

        //移除一栏
        $('.approval-container').on('click', '.remove-column', function(){
            let _parent = $(this).parents('.layui-form');
            let children_length = $('.approval-container').children().length;
            //如果页面只有一栏则不允许用户再删除并提示
            if( children_length > 1 ){
                step_num--;
                _parent.remove();
                //为防止用户删除时导致的序号错误，当用户删除任何一栏时重新进行排序
                $('.approval-container .layui-form').each(function(index){
                    $(this).find('.approval-number p span').text( index + 1 );
                });
            }else{
                layer.msg('至少保留一栏', {icon: 2, time: 2000});
            }
        });

        form.on('select(apr-type)', function(data){

            let _parent = $(this).parents('.layui-form');

            if( $(this).text() === '职位' ){
                _parent.find('.approval-category .apr-pst').css('display','block');
                _parent.find('.approval-category .apr-dpmt').css('display','none');
            }else{
                _parent.find('.approval-category .apr-pst').css('display','none');
                _parent.find('.approval-category .apr-dpmt').css('display','block');
            }

        });

        //提交
        $('.apr-submit').click(function(){

            let flag = true;

            let data = new Object();

            let basic_data = new Object();

            if( $('.basic-container .layui-form input[name=set_name]').val() == '' ){
                layer.msg('集名称不能为空', {icon: 2, time: 2000});
                $('.basic-container .layui-form input[name=set_name]').focus();
                return false;
            }else{
                basic_data.set_name = $('.basic-container .layui-form input[name=set_name]').val();
            }

            if( $('.basic-container .layui-form input[name=archive_dir]').val() == '' ){
                layer.msg('请录入归档目录', {icon: 2, time: 2000});
                $('.basic-container .layui-form input[name=archive_dir]').focus();
                return false;
            }else{
                basic_data.archive_dir = $('.basic-container .layui-form input[name=archive_dir]').val();
            }

            let tmpArr = new Array();

            $('.approval-container .layui-form').each(function(index){

                let _input = $(this).find('input[name=step_name]');

                //验证用户是否录入信息
                if( $.trim( _input.val() ) == '' ){
                    _input.focus();
                    layer.msg('步骤名称不能为空', {icon: 2, time: 2000});
                    flag = false;
                    return false;
                }

                //验证通过后将数据存放入临时的对象
                let tmpObj = new Object();

                tmpObj.step_name = $(this).find('input[name=step_name]').val();
                tmpObj.type = $(this).find('.approval-type .layui-anim .layui-this').attr('lay-value');
                if( tmpObj.type == 'department' ){
                    tmpObj.department = $(this).find('.apr-dpmt .layui-anim .layui-this').attr('lay-value');
                }else{
                    tmpObj.position = $(this).find('.apr-pst .layui-anim .layui-this').attr('lay-value');
                }

                //手动监听是否可以转交
                if($(this).find('input[name=transfer]').next().hasClass('layui-form-checked')){
                    tmpObj.transfer = 'Y';
                }else {
                    tmpObj.transfer = 'N';
                }
                //手动监听是否可以拒绝
                if($(this).find('input[name=refuse]').next().hasClass('layui-form-checked')){
                    tmpObj.refuse = 'Y';
                }else {
                    tmpObj.refuse = 'N';
                }


                //单栏数据收集完毕后将信息存放入数组
                tmpArr.push(tmpObj);
            });

            //确保数据填写的完整性之后将数据打包
            if( flag ){
                data.basic = basic_data;
                data.step = tmpArr;

                $.ajax({

                    url: ThinkPHP['AJAX'] + '/Approval/add',
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function(response){

                        console.log(response.flag);

                        if( response.flag > 0 ){

                            layer.msg(response.msg,{icon:1,time:2000});
                            setTimeout(function () {
                                //location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/Approval/index';
                            },2000);
                        }else{

                            layer.msg(response.msg,{icon:2,time:2000});

                        }
                    }

                });
            }else{
                layer.msg('请确认数据的完整性', {icon: 2, time: 2000});
            }


return false;

        });


    });



});