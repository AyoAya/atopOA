$(function(){

    $('.myTooltip').hover(function(){
        $("[data-toggle='popover']").popover('show');
    },function()
    {
        $("[data-toggle='popover']").popover('hide');
    });


});