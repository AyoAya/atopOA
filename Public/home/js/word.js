/**
 * Created by Fulwin on 2016/12/28.
 */
$(function(){

    //上传文件
    $('#file-upload').Huploadify({
        auto : true,
        width : 140,
        height : 42,
        uploader : ThinkPHP['AJAX'] + '/Center/uploadFace',
        buttonText : '<i class="icon-folder-open"></i>&nbsp;上传文件',
        fileObjName : 'Filedata',
        multi : true,
        fileTypeDesc : '图片类型',
        fileTypeExts : ThinkPHP['UPLOADIFY_CONFIG_FILETYPEEXTS'],
        fileSizeLimit : ThinkPHP['UPLOADIFY_CONFIG_FILESIZELIMIT']*1024*1024,
        uploadLimit : ThinkPHP['UPLOADIFY_CONFIG_UPLOADLIMIT'],
        onUploadSuccess : function(file,data){
            //....上传成功后的操作
        }
    });

    //准备存放各部门文件json数据变量
    //window.jsonstr = '';

    //点击选中该部门并将目录锁定为公开的
    /*$('.col-box').click(function(){
        $('.col-box').removeClass('active');
        $(this).addClass('active');
        $('.tabs-edit li').removeClass('active');
        $('.tabs-edit li:first-child').addClass('active');
        window.jsonstr = '';
        var departmentid = $(this).attr('departmentid');
        //提交选中的部门id，默认访问公开目录
        $.post(ThinkPHP['AJAX']+'/Dcc/changeDepartment',{
            departmentid : departmentid
        },function(data){
            //递归文件数据
            isJsonData(data);
            $('.folders-list').html(window.jsonstr);
        },'json');
    });*/

    //点击切换public和private
    /*$('.tabs-edit li').click(function(){
        var folder = $(this).attr('folder');
        //每当切换一次就将window.jsonstr重置为空，准备重新写入数据
        window.jsonstr = '';
        $(this).tab('show');
        $('.col-box').each(function(index) {
            if ($(this).hasClass('active')) {
                //获取用户选中的部门id和用户点击选中的目录
                $.post(ThinkPHP['AJAX']+'/Dcc/privateFolder',{
                    department : $(this).attr('departmentid'),
                    folder : folder
                },function(data){
                    if(data==-1){
                        //后台比对：如果当前登录用户的部门id和选中的部门id不等则没有权限访问该目录并返回-1
                        $('.folders-list').html('<li class="notPower"><i class="icon-info-sign"></i><span style="font-size: 16px;">您没有权限查看该部门内部文件</span><br><span style="font-size: 11px;">You dont have permission to view this department internal documents.</span></li>');
                    }else{
                        //如果比对相等则递归文件目录及文件数据
                        isJsonData(data);
                        $('.folders-list').html(window.jsonstr);
                    }
                });
            }
        });
    });*/

    //递归文件数据
    /*function isJsonData(json){
        if(typeof(json)=='object'){
            for(key in json){
                if(typeof(json[key])=='object'){
                    if(key!='dir' && key!='file'){
                        window.jsonstr += '<li class="folder-icon bg-primary"><i class="icon-folder-open-alt"></i>&nbsp;' + cutFolderOrFile(key) + '</li>'+'\r\n';
                    }
                    isJsonData(json[key]);
                }else{
                    window.jsonstr += '<li class="file-icon" file-path="' + json[key] + '" ><a href="' + json[key] + '" target="_blank"><i class="icon-file-alt"></i>&nbsp;' + cutFolderOrFile(json[key]) + '</a></li>'+'\r\n';
                }
            }
        }
    }*/

    //截取文件名或目录名
    /*function cutFolderOrFile(obj){
        var arr = obj.split('\\');
        return arr[arr.length-1];
    }*/

    /*$('.folders-list .file-icon').each(function(){
        $(this).popover({
            trigger : 'click',
            placement : 'top',
            content : $(this).attr('title-text'),
        });
    });*/

    //自定义右键菜单
    //阻止浏览器右键默认行为
    /*$('body').bind("contextmenu", function(){
        return false;
    });*/
    //点击空白区域隐藏右键菜单
    /*$(document).click(function(){
        $('.file-icon').removeClass('active');
        $('#right-click-menu').click(function(){
            return;
        })
        $('#right-click-menu').hide();
    });*/
    //右键菜单行为
    /*$('.folders-list .file-icon').bind('mousedown',function(e){
        if(e.which==3){
            //console.log(e.target);
            $('.file-icon').removeClass('active');
            if($(e.target).hasClass('file-icon')){
                $(e.target).addClass('active');
            }else{
                $(e.target).parents('.file-icon').addClass('active');
            }
            //获取当前鼠标指针所在dom
            var windowWidth = $(window).width();
            var documentHeight = $(document).height();
            //alert(documentHeight);
            var x = e.pageX;
            var y = e.pageY;
            if((windowWidth-x)<120 && (documentHeight-y)>130){
                $('#right-click-menu').css({top:y,left:x-120});
            }else if((windowWidth-x)>120 && (documentHeight-y)>130){
                $('#right-click-menu').css({top:y,left:x});
            }else if((windowWidth-x)>120 && (documentHeight-y)<130){
                $('#right-click-menu').css({top:y-130,left:x});
            }else{
                $('#right-click-menu').css({top:y-130,left:x-120});
            }
            //$('#right-click-menu').css({top:y,left:x});
            $('#right-click-menu').show();
        }
    });*/

    //重命名模态框初始化
    /*$('#rename-modal').modal({
        backdrop : false,
        show : false
    });*/
    //详细信息模态框初始化
    /*$('#rename-modal').modal({
        backdrop : false,
        show : false
    });*/

    //点击展开模态框
    /*$('#file-rename').click(function(){
        var file = $('.folders-list li.active p').text();
        var filename = getFileName(file);
        //console.log(filename);
        var file_ext = (/[.]/.exec(file)) ? /[^.]+$/.exec(file.toLowerCase()) : '';
        $('.file-ext').text('.'+file_ext);
        $('.rename-input').val(filename);
        $('#rename-modal').modal('show');
    });*/
    //点击展开详细信息
    /*$('#file-details').click(function(){
        var _this = $(this);
        var _operation = _this.attr('operation');
        var _filepath = $('.folders-list .active').attr('file-path');
        $.post(ThinkPHP['AJAX']+'/Dcc/operation',{
            operation : _operation,
            filepath : _filepath
        },function(data){

        },'json');
        $('#details-modal').modal('show');
    });*/

    //取得文件名称
    /*function getFileName(o){
        var newfilename = '';
        var arr = o.split('.');
        $.each(arr,function(index,item){
            if(item==''){
                arr.splice(index,1);
            }
        });
        if(arr.length <= 2){
            return arr[0];
        }else{
            for(var i=0;i<arr.length-1;i++){
                newfilename += arr[i] + '.';
            }
            return newfilename.substring(0,newfilename.length-1);
        }
    }*/

    //文件操作
    /*$('.file-operation').click(function(){
        var _filepath = $('.folders-list .active').attr('file-path');
        var _this = $(this);
        var _operation = _this.attr('operation');
        var _path = $('#now-path').val();
        $.post(ThinkPHP['AJAX']+'/Dcc/operation',{
            operation : _operation,
            filepath : _path + '/' + _filepath
        },function(data){

        },'json');
    });*/


});
