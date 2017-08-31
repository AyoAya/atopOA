/**
 * Created by Fulwin on 2017/6/21.
 */
$(function(){

    //实例化email推送
    //$('.email-block-wrapper').emailBlock();

    $('.add-approval').click(function(){
        var approval_dom = '';
        $('.approval-wrapper').append(approval_dom);
    });

    if( $('.approval-wrapper') ){
        var ApprovalSlideDownBox = $('.approval-wrapper').html();
    }




    layui.use(['form','layer','upload'], function(){
        var form = layui.form(),
            layer = layui.layer;

        // 点击文件号添加队列以及判断
        $('.file_span').click(function(){
            let file_id = $(this).attr('file_no');
            let file_no = $(this).text();
            let html = "<span class='file-span file-span"+file_id+"' file_id="+file_id+">"+file_no+" <i class='icon-remove close-file' title='移除'></i></span>";
            let parent = $(this).parent().prev();

            if( parent.find('.file-span'+file_id+'').length ){
                layer.msg( file_no + '已存在');
            }else{
                parent.append(html);
            }

        })
        //移除一个文件号
        $('.file-no-box').on('click','.close-file',function(){
            $(this).parent().remove();
        })
        $('#form .file-no-dis .fileNo').click(function(){
            $(this).next().show(0);
            $(this).next().next().show(0);
        })
        $('#form .file-no-dis .ok .layui-btn').click(function(){
            $(this).parent().prev().hide(0);
            $(this).parent().hide(0);
        })

        $('.add-a').click(function(){

            let fId = $(this).attr('fid');
            let fileName = $(this).attr('fileName');

            if($(this).text() == '添加'){

                var nameHtml = '<span class="fileName fileName'+fId+'" fid="'+fId+'">'+fileName+'</span>';
                $('.fileNo-box').append(nameHtml);
                $(this).text('撤回')

            }else{
                if($('.fileNo-box').find('.fileName'+fId+'').length){
                    $('.fileNo-box').find('.fileName'+fId+'').remove();
                    $(this).text('添加')
                }
            }



        })

        // 监听评审规则
        form.on('select(revRules)',function( data ){
            //console.log(data.value); //得到被选中的值
            $.ajax({

                url: ThinkPHP['AJAX'] + '/File/selectReview',
                dataType: 'json',
                type: 'POST',
                data: {
                    id : data.value
                },
                success : function( response ){
                    if(response.flag > 0){

                        let tmpArr = response.arr;

                        let len = tmpArr.length;

                        var inner = '';
                        var prompt = '';
                        for (var i=0;i<len;i++){
                            let relLen = tmpArr[i]['rel'].length;
                            let userLen = tmpArr[i]['user'].length;

                            var html = '<div class="layui-form-item add-review"><label class="layui-form-label"><span class="num">'+tmpArr[i][0]+'</span> 级评审</label><div class="pos-person"><div class="post-box post-box'+tmpArr[i][0]+'"></div><div class="selected-section pos'+tmpArr[i][0]+'">';

                            for(var j=0;j<relLen;j++){
                                //console.log(tmpArr[i]['rel'][j]['name']);
                                html += '<div class="selected-group">'+tmpArr[i]['rel'][j]['name']+'</div>';

                                let userLens = tmpArr[i]['user'][j].length;

                                for(var l=0;l<userLens;l++){
                                    //console.log(tmpArr[i]['user'][k][l]['nickname']);
                                    html += '<div class="selected-item" uId="'+tmpArr[i]['user'][j][l]['id']+'" email="'+tmpArr[i]['user'][j][l]['email']+'">'+tmpArr[i]['user'][j][l]['nickname']+'</div>';

                                }
                            }

                            html +='<div class="btn-ok"><button class="layui-btn close-rev" type="button">确定</button></div></div></div></div>';

                            inner += html;

                        }

                        // 提示此规则的岗位等级
                        for (var i=0;i<len;i++){
                            let relLen = tmpArr[i]['rel'].length;
                            let userLen = tmpArr[i]['user'].length;

                            var inHtml = '<p><span class="num">'+tmpArr[i][0]+'</span> 级评审</p><div class="position-box">';

                            for(var j=0;j<relLen;j++){
                                //console.log(tmpArr[i]['rel'][j]['name']);
                                inHtml += '<div class="rev-pos">'+tmpArr[i]['rel'][j]['name']+'</div>';
                            }
                            inHtml +='</div><div class="next-rev"><i class="glyphicon glyphicon-arrow-down"></i></div>';
                            prompt += inHtml;

                        }
                        prompt += '<p>DCC评审</p>';
                        $('.reviewGrade').html(inner);
                        $('.step').html(prompt);

                    }
                }
            })
        });

        // 添加评审人
        $('.layui-form').on('click', '.choice-approval-user', function(){
            layer.open({
                type: 1,
                title: '选择评审人',
                area: ['1100px'], //宽高
                content: layerApprovalContext
            });
        });

        $(document).on('click', '.user-item', function(){
            if( $(this).hasClass('active') ){
                $(this).removeClass('active');
            }else{
               $(this).addClass('active');
            }
        });

        // 点击添加将此条信息添加进文件列
        /*$(document).on('click','.add-a',function(){

            /!*!//console.log($(this).attr('filename'));
            let fileHtml = $(this).parents('tr');
            let file_id = $(this).attr('file_id');
            let filetable = $(this).parents('table');
            let _par = $(this).parent().prev();
                fileHtml.find('.add-a').parent().remove();


            let _fl = _par.parents('tr');
            filetable.css('border','none')
            _fl.find('.fl-ctx').addClass('hd-text');
            _fl.find('tr').css('border','none');

            //console.log(fl_html);
            //let fileHtml = '<tr><td>'+$(this)+'</td><td></td><td></td><td></td></tr>';
            let fl_html = _par.parents('tr').html();
            $('.fl-tab').append('<tr>'+fl_html+'</tr>');
            $(this).parents('tr').remove();
            layer.closeAll();*!/



        })*/



        $('.add-fl').click(function(){



            layer.open({
                type: 1,
                area: ['1200px','500px'], //宽高
                title : '请选择文件',
                shade: [0.5 ,'#000'],
                content: refushFileList(numberData,true,true)
            });

            /*$.ajax({
                url : ThinkPHP['AJAX'] + '/File/review',
                dataType : 'json',
                type : 'POST',
                data : {
                   type : 'file_info',
                },
                success: function( response ){
                   // 根据返回的结果检查队列里是否包含文件，如果有则上传，没有则直接跳转到详情页

                    console.log(response);

                    var html = '<div class="file-box"><table class="layui-table"><thead><tr><th>文件号</th><th>版本号</th><th>附件</th><th>文件描述</th><th width="60">选择</th></tr></thead>';

                    if( response ){

                        for( var i=0;i<response.length;i++ ){
                            html += '<tr><td>'+response[i].file_no+'</td><td>'+response[i].version+'</td><td>';

                            for(var j=0;j<response[i]['attachment'].length;j++){
                                console.log(j<response[i]['attachment']);
                                html += '<div class="file-item"><div class="file-info" style="width:200px;overflow: hidden;text-overflow: ellipsis;white-space: nowrap;"><i class="file-icon file-icon-ext-'+response[i]['attachment'][j].ext+'"></i>&nbsp;&nbsp;<a href="'+ThinkPHP['ROOT']+'/'+response[i]['attachment'][j].path+'" target="_blank" title="'+response[i]['attachment'][j].savename+'">'+response[i]['attachment'][j].savename+'</a></div></div>'
                            }

                            html += '</td> <td>'+response[i].content+'</td><td><a href="javascript:void(0);" class="add-a" fid="'+response[i].id+'" fileName="'+response[i].file_no+'">添加</a></td></tr>'
                        }

                        html += '</table></div>';

                        layer.open({
                            type: 1,
                            skin: 'layui-layer-rim', //加上边框
                            area: ['1200px', '450px'], //宽高
                            title : '请选择文件',
                            content: html
                        });

                    }else{
                       location.replace(location.href);
                       layer.msg('ERROR', {icon: 2, time: 2000});
                    }

                }

            });*/




        })




        // 隐藏或开启评审选人
        $('.reviewGrade').on('click','.post-box',function(){
            if($(this).next().css('display') == 'none'){
                $(this).next().slideDown();
            }else{
                $(this).next().slideUp();
            }
        });

        $('.reviewGrade').on('click','.close-rev',function(){
            $(this).parents('.selected-section').slideUp();
        });

        // 将选择人添加进评审框
        $('.reviewGrade').on('click','.selected-item',function(){
            let uid = $(this).attr('uId');
            let email = $(this).attr('email');
            let nickname = $(this).text();
            let personHtml = "<div class='person person"+uid+"' email='"+email+"' uid='"+uid+"'>"+nickname+" <i class='icon-remove close-x' title='移除'></i></div>";
            let _parent = $(this).parents('.selected-section').prev();

            if(_parent.find('.person').length > 0){
                if(_parent.find('.person' + uid).length){
                    layer.msg(nickname+'已存在!')
                }else{
                    _parent.append(personHtml);
                }
            }else{
                _parent.append(personHtml);
            }

        });
        // close 关掉此用户
        $('.reviewGrade').on('click','.close-x',function(event){
            $(this).parent().remove();

            event.stopPropagation();

            if($(this).parent().parent().next().css('display') == 'none'){
                $(this).parent().parent().next().css('display','none')
            }else{
                $(this).parent().parent().next().css('display','block')
            }
        });

        form.on('submit(submit)',function( data ){

            /*if(!$('.fileNo-box span').length){
                layer.msg('请添加编号，没有请先申请！',{time:2000});
                return false;
            }*/

            if(!$('.post-box .person').length){
                layer.msg('请选择评审人！',{time:2000});
                return false;
            }


            var AllUserItem = new Array();
            var AllRules = new Array();
            var AllFileId = new Array();

            $('.fileNo .file-span').each(function( index ){
                var file_no = $(this).attr('file_id');
                AllRules.push(file_no)
            })

            // 统计选择的文件
            $('.fileNo-box .fileName').each(function(){
                var fid = $(this).attr('fid');
                AllFileId.push(fid);
            });

            //获取多级评审评审人数据
            $('.layui-form .add-review').each(function(index){
                var userItemsArr = new Array();
                var emailBlockRecipient = $(this).find('.post-box');
                emailBlockRecipient.find('.person').each(function(){
                    userItemsArr.push({
                        userId: $(this).attr('uid'),
                        userEmail: $(this).attr('email')
                    });
                });
                AllUserItem.push(JSON.stringify(userItemsArr));
            });

            var num = $('.file_num').find("option:selected").attr('num');

            data.field.allUserItem = AllUserItem;
            data.field.num = num;
            data.field.selected = selecteds;

            $.ajax({
                url : ThinkPHP['AJAX'] + '/File/review',
                dataType : 'json',
                type : 'POST',
                data : {
                    data : data.field,
                    num : num,
                    file_no : AllRules,
                    fileId : AllFileId
                },
                beforeSend: function(){
                    layer.load(1, {
                        shade: [0.5,'#fff'] //0.1透明度的白色背景
                    });
                },
                success: function( response ){
                    // 根据返回的结果检查队列里是否包含文件，如果有则上传，没有则直接跳转到详情页
                    if( response.flag > 0 ){

                        layer.msg(response.msg, {icon: 1, time: 1500});
                        setTimeout(function () {
                            location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/File/reviewDetail/id/'+response.flag;
                        })

                    }else{
                        location.replace(location.href);
                        layer.msg('ERROR', {icon: 2, time: 2000});
                    }
                }
            });

            return false;

        });


    });




});