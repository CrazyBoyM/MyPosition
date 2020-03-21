$(function() {
    if($('#wmd-button-row').length>0)$('#wmd-button-row').prepend('<li class="wmd-button" id="wmd-position-button" title="写作位置" style=""><i class="fa fa-compass"></i></li>');
    $(document).on('click','#wmd-position-button',function() {
        addPosition();
    });
});