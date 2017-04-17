/**
 * Created by Fulwin on 2017/1/6.
 */
$(function(){

    //上传附件
    $('#attachment-btn').Huploadify({
        method : 'post',
        auto : true,
        uploader : ThinkPHP['AJAX'] + '/Approval/uploadAttachment',
        buttonText : '<i class="icon-paper-clip">&nbsp;</i>上传附件',
        fileObjName : 'Filedata',
        width : 140,
        height : 80,
        multi : true,
        fileTypeDesc : '文件类型',
        fileTypeExts : '*.jpg;*.png;*.gif;*.bmp;*.jpeg;',
        fileSizeLimit : 5242880,
        onUploadSuccess : function(file,data,response){
            if(data){
                var obj = $.parseJSON(data);
                var str = '';
                str += '<li class="file-name" filepath="'+ obj.savepath +'"><i class="icon-file-alt">&nbsp;</i>'+ obj.name +'<i class="remove-file icon-remove" title="删除该附件"></i></li>';
                $('#file-list').append(str);
            }else{
                $().toastmessage('showToast', {
                    text : '上传失败',
                    sticky : false,
                    position : 'top-right',
                    type : 'error',
                    inEffectDuration : 600,
                    stayTime : 3000,
                });
            }
        }
    });

    //获取到报销明细表单dom
    var expenseHtml = $('.expense-form-container').html();
    //动态添加报销明细单
    $('.add-customer-btn').click(function(){
        $('.expense-form-container').append(expenseHtml);
        //计算当前有多少个明细单
        $('.expense-form-container .expense-form').each(function(index){
            $(this).find('.expense-number').text(index+1);
            //如果当前报销明细单大于1个则添加删除按钮
            if((index+1)>1){
                $(this).find('.remove-customer').css('display','block');
            }
        });
    });

    //当点击删除时，移除该订单明细并重新计算报销明细单数量
    $('.expense-form-container').on('click','.remove-customer',function(){
        var totalmoney = 0;
        $(this).parents('.expense-form').remove();
        $('.expense-form-container .expense-form').each(function(index){
            $(this).find('.expense-number').text(index+1);
            //如果当前报销明细单大于1个则显示删除按钮
            if((index+1)>1){
                $(this).find('.remove-customer').css('display','block');
            }
        });
        //如果页面存在多个报销明细单，则累计报销金额；如果只有一个则直接显示报销金额
        $('.expense-form-container .expense-form').each(function(index){
            //金额不为空的时候才叠加
            if($(this).find('.input-money').val()!=''){
                totalmoney += parseInt($(this).find('.input-money').val());
            }
        });
        // toFixed()：解决javascript计算BUG
        $('.expense-box .total-money .lead').text(totalmoney);
        $('#total-money').val(totalmoney);
    });

    //动态计算报销总额
    $('.expense-form-container').on('keyup','.input-money',function(){
        var _money = $(this).val();
        var totalmoney = 0;
        //如果页面存在多个报销明细单，则累计报销金额；如果只有一个则直接显示报销金额
        if($('.expense-form-container .expense-form').length > 1){
            $('.expense-form-container .expense-form').each(function(index){
                //金额不为空的时候才叠加
                if($(this).find('.input-money').val()!=''){
                    totalmoney += parseInt($(this).find('.input-money').val());
                }
            });
            // toFixed()：解决javascript计算BUG
            $('.expense-box .total-money .lead').text(totalmoney);
            $('#total-money').val(totalmoney);
        }else{
            var money = $(this).val();
            $('.expense-box .total-money .lead').text(money);
            $('#total-money').val(money);
        }
    });

    //点击删除上传的附件
    $('#file-list').on('click','.remove-file',function(){
        var _this = $(this);
        //获取到点击文件的地址
        var filepath = $(this).parent().attr('filepath');
        $.post(ThinkPHP['AJAX']+'/Approval/removeAttachment',{
            filepath : filepath
        },function(data){
            //如果操作成功则删除该dom元素
            if(data==1){
                _this.parent().remove();
            }
        });
    });

    //提交验证
    $('#submit-btn').click(function(){
        var valid = true;
        //循环验证每一个报销明细单
        $('.expense-form-container .expense-form').each(function(index){
            var _this = $(this);
            //为避免重复提示，当有一个元素验证失败则提示激活焦点并退出
            if(_this.find('.input-money').val()==''){
                _this.find('.input-money').focus();
                formValidate('请输入报销金额');
                valid = false;
                return false;
            }
            if(_this.find('.input-category').val()==''){
                _this.find('.input-category').focus();
                formValidate('请输入报销类别');
                valid = false;
                return false;
            }
        });
        if( $('#file-list').children().length > 0 ){
            var filepathArr = new Array();
            $('#file-list li').each(function(index){
                filepathArr.push($(this).attr('filepath'));
            });
            var attachmentstr = filepathArr.join(',');
        }else{
            var attachmentstr = '';
        }
        if(valid===false){
            return false;
        }
        //加载loading
        $('#loading').css('display','block');
        $.ajax({
            url : ThinkPHP['AJAX'] + '/Approval/addApproval',
            type : 'POST',
            data : {
                title : $('.approval-title').val(),
                dda_name : $('#dda-name').val(),
                dda_id : $('#dda-id').val(),
                fda_name : $('#fda-name').val(),
                fda_id : $('#fda-id').val(),
                vpa_name : $('#vpa-name').val(),
                vpa_id : $('#vpa-id').val(),
                money : $('#total-money').val(),
                type : 'expense',
                attachment : attachmentstr
            },
            dataType : 'json',
            success : function(response){
                if(response){
                    $('.expense-form-container .expense-form').each(function(index){
                        var money = $(this).find('.input-money').val();
                        var category = $(this).find('.input-category').val();
                        var detail = $(this).find('.textrea-customer').val();
                        $.post(ThinkPHP['AJAX'] + '/Approval/addApprovalAffiliated',{
                            money : money,
                            category : category,
                            detail : detail,
                            belong : response
                        },function(data){});
                    });
                    setTimeout(function(){
                        location.href = 'http://'+ ThinkPHP['HTTP_HOST'] +'/Approval';
                    },50);
                }
            }
        });
    });

    //封装提示组件
    function formValidate(msg){
        $().toastmessage('showToast', {
            text : msg,
            sticky : false,
            position : 'top-right',
            type : 'error',
            inEffectDuration : 600,
            stayTime : 3000,
        });
    }

    //重置所有表单
    $('#reset-btn').click(function(){
        $('.expense-form-container .expense-form').each(function(index){
            $(this).find('input').val('');
            $(this).find('textarea').val('');
        });
        //将所有input元素以及textarea元素清空并将报销总额归零
        $('.expense-box .total-money .lead').text('0');
        $('#total-money').val('0');
    });

});