$(function(){

    $('.myTooltip').hover(function(){
        alert(1);
        $("[data-toggle='popover']").popover('show');
    },function()
    {
        $("[data-toggle='popover']").popover('hide');
    });


});