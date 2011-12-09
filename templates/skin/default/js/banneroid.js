/**
 * Banneroid (for Mootools-compatible template)
 */
window.addEvent('load', function() {

    new DatePicker('.datepicker', {
        pickerClass: 'datepicker_vista',
        format: 'Y-m-d',
        minDate: '',
        inputOutputFormat: 'Y-m-d'
    });

    var kinds = $('kinds');
    if(kinds){
        kinds.getElements('input[name=banner_kind]').addEvent('change', function() {
            if (this.value=='kind_html') {
                $('kind_image').setStyle('display','none');
                $('kind_html').setStyle('display','block');
            } else {
                $('kind_image').setStyle('display','block');
                $('kind_html').setStyle('display','none');
            };
        });

        // Submit clear
        $('fmBanneroid').addEvent('submit',function() {
            selected_kind = kinds.getElements('input[name=banner_kind]:checked').get('value');
            if (selected_kind == 'kind_image') {
                $('banner_html').set('value','');
            } else {
                $('banner_image').set('value','');
            }
        });
    }
});

