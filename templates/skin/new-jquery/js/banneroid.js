jQuery(document).ready(function(){
    var datepicker = jQuery('.datepicker');
    datepicker.datepicker({
        dateFormat: 'yy-mm-dd'
    });

    jQuery.each(datepicker, function(el, data){
        if(!data.value || (data.value == '0000-00-00')){
            jQuery(data).datepicker("setDate", "+0d");
        }
    });

    jQuery('#kinds input[name=banner_kind]').click(function() {
        if (this.value=='kind_html') {
            $('#kind_image').hide();
            $('#kind_html').show();
        } else {
            $('#kind_image').show();
            $('#kind_html').hide();
        };
    });

    // Submit clear
    jQuery('#fmBanneroid').submit(function() {
        selected_kind = jQuery('#kinds input[name="banner_kind"]:checked').val();
        if (selected_kind == 'kind_image') {
            $('#banner_html').val('');
        } else {
            $('#banner_image').val('');
        }
    });
});