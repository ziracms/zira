var dash_options_load = function() {
    desk_window_form_init(this);
    $.get(baseUrl('dash/index/ping'),{}, null,'json').fail(this.bind(this, function(){
        $(this.content).find('input.clean_url_option').attr('disabled','disabled').trigger('change');
    }));
    $(this.content).find('input.watermark_option').parent().append('<span class="glyphicon glyphicon-folder-open" style="position:absolute;right:30px;top:10px;cursor:pointer"></span>');
    $(this.content).find('input.watermark_option').parent().children('.glyphicon').click(this.bind(this, function(){
        desk_file_selector(function(selected){
            if (selected && selected.length>0 && typeof(selected[0].type)!="undefined" && selected[0].type=='image') {
                var src = selected[0].data;
                var regexp = new RegExp('\\'+desk_ds, 'g');
                $(this.content).find('input.watermark_option').val(src.replace(regexp,'/'));
            }
        }, this);
    }));

    $(this.content).find('select.captcha_select').change(this.bind(this, function(){
        var captcha_type = $(this.content).find('select.captcha_select').val();
        if (captcha_type == 'recaptcha') {
            $(this.content).find('.recaptcha3_inputs').hide();
            $(this.content).find('.recaptcha_inputs').show();
        } else if (captcha_type == 'recaptcha3') {
            $(this.content).find('.recaptcha_inputs').hide();
            $(this.content).find('.recaptcha3_inputs').show();
        } else {
            $(this.content).find('.recaptcha_inputs').hide();
            $(this.content).find('.recaptcha3_inputs').hide();
        }
    }));
    $(this.content).find('select.captcha_select').trigger('change');
};