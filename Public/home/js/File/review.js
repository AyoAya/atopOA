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
            $(this).parent()    .hide(0);
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

                            var html = '<div class="layui-form-item add-review"><label class="layui-form-label"><span class="num">'+tmpArr[i][0]+'</span> 级评审</label><div class="post-box post-box'+tmpArr[i][0]+'"></div><div class="selected-section pos'+tmpArr[i][0]+'">';

                            for(var j=0;j<relLen;j++){
                                //console.log(tmpArr[i]['rel'][j]['name']);
                                html += '<div class="selected-group">'+tmpArr[i]['rel'][j]['name']+'</div>';

                                let userLens = tmpArr[i]['user'][j].length;

                                for(var l=0;l<userLens;l++){
                                    //console.log(tmpArr[i]['user'][k][l]['nickname']);
                                    html += '<div class="selected-item" uId="'+tmpArr[i]['user'][j][l]['id']+'" email="'+tmpArr[i]['user'][j][l]['email']+'">'+tmpArr[i]['user'][j][l]['nickname']+'</div>';

                                }
                            }

                            html +='<div class="btn-ok"><button class="layui-btn close-rev" type="button">确定</button></div></div></div>';

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
                        $('.prompt').html(prompt);

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

        // 隐藏或开启评审选人
        $('.reviewGrade').on('click','.post-box',function(){
            if($(this).next().css('display') == 'none'){
                $(this).next().slideDown();
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
        $('.reviewGrade').on('click','.close-x',function(){
            $(this).parent().remove();
        });

       /* var step_num = 1;
        // 移除一列
        $('.approval-wrapper').on('click','.close-review',function(){
            let _parent = $(this).parents('.email-push-selector');
            let children_length = $('.approval-wrapper').children().length;
            //如果页面只有一栏则不允许用户再删除并提示
            if( children_length > 1 ){
                step_num--;
                _parent.remove();
                //为防止用户删除时导致的序号错误，当用户删除任何一栏时重新进行排序
                $('.approval-wrapper .email-push-selector').each(function(index){
                    $(this).find('.num').text( index + 1 );
                });
            }else{
                layer.msg('至少保留一栏', {icon: 2, time: 2000});
            }
        });
*/

       /* // 添加一列
       $('.add-approval').click(function(){
           $('.approval-wrapper').append(ApprovalSlideDownBox);
           $('.approval-wrapper .layui-form-item').each(function(index){
                $(this).find('.num').text(index+1);
           });
           $('.email-block-wrapper').emailBlock();
            form.render();
       });*/

       // var SUB_NAME, NUM_ID,FILE_NO,VERSION,attachmentList = new Array();

       /* // uploader参数配置
        var logUploaderOption = {
            auto: false,
            server: ThinkPHP['AJAX'] + '/File/uploadAttachment',
            pick: '#filePick',
            fileVal : 'Filedata',
            accept: {
                title: 'file',
                extensions: 'zip,rar,doc,docx,xls,xlsx,pdf'
            },
            method: 'POST',
        };
        // 实例化uploader
        APRuploader = WebUploader.create( logUploaderOption );
        // 添加到队列时
        APRuploader.on('fileQueued', function( file ){
            var fileItem = '<div class="file-item" id="'+ file.id +'">' +
                '<div class="pull-left"><i class="file-icon file-icon-ext-'+ file.ext +'"></i> '+ file.name +'</div>' +
                '<div class="pull-right"><i class="icon-remove" title="移除该文件"></i></div>' +
                '<div class="clearfix"></div>' +
                '</div>';
            $(logUploaderOption.pick).next().append(fileItem);
        });
        // 上传错误时
        APRuploader.on('error', function( code ){
            var msg = '';
            switch(code){
                case 'Q_EXCEED_NUM_LIMIT':
                    msg = '只能上传一个文件';
                    break;
                case 'Q_EXCEED_SIZE_LIMIT':
                    msg = '文件大小超出限制';
                    break;
                case 'Q_TYPE_DENIED':
                    msg = '文件格式不允许';
                    break;
                case 'F_DUPLICATE':
                    msg = '文件已存在';
                    break;
                default:
                    msg = code;
            }
            layer.msg(msg, {icon: 2, time: 2000});
        });
        // 上传成功时
        APRuploader.on('uploadSuccess', function( file,response ){

            if( response.flag > 0 ){
                var attachmentObject = new Object();
                attachmentObject.ext = response.ext;
                attachmentObject.savename = response.savename;
                attachmentObject.path = response.path;
                attachmentList.push(attachmentObject);
            }else{
                layer.closeAll();
                layer.msg(response.msg, { icon : 2,time : 2000 });
            }

        });*/


        // 空白隐藏
        /*$(document).on("click", function(event) {
            var $ele = $(".selected-section");
            var $eve = $(".post-box");
            if (!$(event.target).closest($ele)[0]) {
                $ele.hide();
            }
        });*/

        // 所有文件上传结束时
        /*APRuploader.on('uploadFinished', function( data ){

            var sub_name = SUB_NAME,
                numId = NUM_ID,
                version = VERSION,
                attachments = JSON.stringify(attachmentList);

            //console.log(SUB_NAME);

            $.ajax({
                url: ThinkPHP['AJAX'] + '/File/saveDetailAttachment',
                dataType: 'json',
                type: 'POST',
                data: {
                    attachments : attachments,
                    id : SUB_NAME,
                    num : NUM_ID,
                    version : VERSION
                },
                beforeSend: function(){
                    layer.load(1, {
                        shade: [0.5,'#fff'] //0.1透明度的白色背景
                    });
                },
                success : function( response ){
                    if(response.flag > 0){
                        layer.msg(response.msg, {icon: 1, time: 2000});
                        setTimeout(function () {
                            location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/File/fileDetail/no/'+FILE_NO+'/version/'+VERSION;
                        })
                    }else{
                        layer.msg(response.msg, {icon: 2, time: 2000});
                        setTimeout(function () {
                            location.replace(location.href);
                        })
                    }

                }
            })

        });


        // 删除队列文件
        $('.uploader-attachment-queue').on('click', '.icon-remove', function(){
            var id = $(this).parent().parent().attr('id');
            // 删除队列中的文件
            APRuploader.removeFile( id, true );
            // 删除dom节点
            $(this).parent().parent().remove();
        });*/

        form.on('submit(submit)',function( data ){

            //console.log(JSON.stringify(data.field));

            if(!$('.file-no-box span').length){
                layer.msg('请选择编号，没有请申请！',{time:2000});
                return false;
            }

            if(!$('.post-box .person').length){
                layer.msg('请选择评审人！',{time:2000});
                return false;
            }


            var AllUserItem = new Array();
            var AllRules = new Array();

            $('.fileNo .file-span').each(function( index ){
                var file_no = $(this).attr('file_id');
                AllRules.push(file_no)
            })

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

            $.ajax({
                url : ThinkPHP['AJAX'] + '/File/review',
                dataType : 'json',
                type : 'POST',
                data : {
                    data : data.field,
                    num : num,
                    file_no : AllRules
                },
                beforeSend: function(){
                    layer.load(1, {
                        shade: [0.5,'#fff'] //0.1透明度的白色背景
                    });
                },
                success: function( response ){
                    // 根据返回的结果检查队列里是否包含文件，如果有则上传，没有则直接跳转到详情页
                    if( response.flag > 0 ){

                        if( APRuploader.getFiles().length > 0 ){ //检查队列里是否包含文件

                            SUB_NAME = response.flag;
                            NUM_ID = response.num;
                            FILE_NO = response.file_no;
                            VERSION = response.version;
                            APRuploader.option('formData', {SUB_NAME: SUB_NAME,NUM_ID: NUM_ID,FILE_NO:FILE_NO,VERSION:VERSION});
                            APRuploader.upload();
                        }else{
                            layer.msg(response.msg, {icon: 1, time: 1500});
                            setTimeout(function () {
                                location.href = 'http://' + ThinkPHP['HTTP_HOST'] + '/File/fileDetail/no/'+response.file_no+'/version/'+response.version;
                            })
                        }
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