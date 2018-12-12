var dash_holidays_load = function() {
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i].inactive)!="undefined" && this.options.bodyItems[i].inactive) {
            $(this.options.bodyItems[i].element).addClass('inactive');
        }
    }
};

var dash_holidays_preview = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        desk_window_request(this, url('holiday/dash/preview'),{'item':selected[0].data}, this.bind(this, function(response){
            if (!response || typeof(response.data)=="undefined") return;
            $('.zira-celebration').remove();
            $('.zira-celebration-bg').remove();
            $('#celebration-audio').remove();
            $('body').append(response.data);
            zira_celebrate();
        }));
    }
};

var dash_holidays_settings = function() {
    desk_call(dash_holiday_settings_wnd);
};

var dash_holiday_load = function() {
    $(this.content).find('input.image_input').parent().append('<span class="glyphicon glyphicon-folder-open" style="position:absolute;right:30px;top:10px;cursor:pointer"></span>');
    $(this.content).find('input.image_input').parent().children('.glyphicon').click(this.bind(this, function(){
        desk_file_selector(function(selected){
            if (selected && selected.length>0 && (typeof(selected[0].type)!="undefined" && selected[0].type=='image')) {
                var src = selected[0].data;
                var regexp = new RegExp('\\'+desk_ds, 'g');
                $(this.content).find('input.image_input').val(src.replace(regexp,'/'));
            }
        }, this);
    }));
    
    $(this.content).find('input.audio_input').parent().append('<span class="glyphicon glyphicon-folder-open" style="position:absolute;right:30px;top:10px;cursor:pointer"></span>');
    $(this.content).find('input.audio_input').parent().children('.glyphicon').click(this.bind(this, function(){
        desk_file_selector(function(selected){
            if (selected && selected.length>0 && (typeof(selected[0].type)!="undefined" && selected[0].type=='audio' || selected[0].data.substr(-4)=='.mp3')) {
                var src = selected[0].data;
                var regexp = new RegExp('\\'+desk_ds, 'g');
                $(this.content).find('input.audio_input').val(src.replace(regexp,'/'));
            }
        }, this);
    }));
    
    var month = $(this.element).find('select.holiday_month_select');
    var day = $(this.element).find('select.holiday_day_select');
    var date = $(this.element).find('.holiday_date_hidden').val();
    $(month).change(zira_bind(this, dash_holiday_set_date));
    $(day).change(zira_bind(this, dash_holiday_set_date));
    if (date.length>0) {
        var parts = date.split('.');
        if (parts.length==2) {
            $(month).val(parts[1]);
            $(day).val(parts[0]);
        }
    }
};

var dash_holiday_set_date = function() {
    var month = $(this.element).find('select.holiday_month_select').val();
    var day = $(this.element).find('select.holiday_day_select').val();
    if (month.length>0 && day.length>0 && month>0 && day>0) {
        $(this.element).find('.holiday_date_hidden').val(day+'.'+month);
    } else {
        $(this.element).find('.holiday_date_hidden').val('');
    }
};