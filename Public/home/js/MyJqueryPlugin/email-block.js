/**
 * Created by Fulwin on 2017/6/7.
 */
;(function($){

    $.fn.emailBlock = function(option){

        var _this = this;

        var slide = true;

        var defaultOption = {};

        layui.use(['layer'], function(){


            // 根据收件人框的高度设置userbox的top
            function setUserboxTop( elem = '' ){

                if( elem == '' ){

                    var users_box_top = _this.find('.email-block-recipient').outerHeight();

                    _this.find('.email-block-users-box').css({top: (users_box_top-2)+'px'});

                }else{

                    var users_box_top = $(elem).outerHeight();

                    _this.find('.email-block-users-box').css({top: (users_box_top-2)+'px'});

                }

            }

            setUserboxTop();

            _this.find('.email-block-recipient').click(function(event){

                var _index = $('.email-block-recipient').index( $(this) );

                event.stopImmediatePropagation();

                $('.email-block-recipient').each(function(index){

                    if( index != _index ){

                        $(this).find('.email-block-users-box').slideUp('fast');

                    }else{

                        $(this).find('.email-block-users-box').slideDown('fast');

                    }

                });

                setUserboxTop($(this));

            });

            _this.find('.email-block-users-box .email-block-item').click(function(){

                event.stopImmediatePropagation();

                var user_id = $(this).attr('user-id'),
                    user_email = $(this).attr('user-email'),
                    user_name = $(this).text();

                //添加时检查是否已存在该收件人
                if( $(this).parents('.email-block-recipient').find('.email-block-user-items').find('span[user-id="'+ user_id +'"]').length > 0 ){

                    layer.msg(user_name+'已存在');

                }else{

                    var _item = '<span class="email-block-user-item-span" user-id="'+ user_id +'" user-email="'+ user_email +'">'+ user_name +' <i class="icon-remove" title="移除"></i></span>';

                    $(this).parents('.email-block-users-box').prev().append(_item);

                    setUserboxTop($(this).parents('.email-block-recipient'));

                }

            });

            _this.find('.email-block-user-items').on('click', '.email-block-user-item-span i.icon-remove', function(){

                event.stopImmediatePropagation();

                $(this).parent().remove();

                setUserboxTop( $(this).parents('.email-block-user-items').prev() );

            });

            _this.find('button.email-block-done-btn').click(function(){

                event.stopImmediatePropagation();

                _this.find('.email-block-recipient .email-block-users-box').stop().slideUp('fast');

            });

            _this.find('button.email-block-clear-btn').click(function(){

                event.stopImmediatePropagation();

                $(this).parents('.email-block-users-box').prev().html('');

                setUserboxTop( $(this).parents('.email-block-recipient') );

            });

            $(document).bind('click', function(evt){
                if(evt.target != $('.email-block-users-box').get(0)) {
                    $('.email-block-users-box').slideUp('fast');
                }
            });

        });

        return _this;

    }

})(jQuery);