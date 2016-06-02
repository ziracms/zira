var dash_user_load = function() {
    desk_window_form_init(this);
    zira_datepicker($(this.content).find('.dash-datepicker'));
    $(this.sidebar).find('.dashwindow-userthumb-selector').click(this.bind(this,function(){
        desk_call(dash_user_image_select, this);
    }));
    if ($(this.content).find('input.dashwindow-user-image').val().length>0) {
        $(this.sidebar).find('.dashwindow-userthumb-delete').removeClass('disabled');
        this.enableItemsByProperty('action','noimage');
    }
    $(this.sidebar).find('.dashwindow-userthumb-delete').click(this.bind(this,function(){
        desk_call(dash_user_delete_image, this);
    }));
    if ($(this.content).find('input.dashwindow-user-verified').val()==dash_user_status_verified) {
        var verified = this.findToolbarItemByProperty('action','verified');
        if (verified) $(verified.element).addClass('active').children('.glyphicon').removeClass('glyphicon-ban-circle').addClass('glyphicon-ok');
    }
    if ($(this.content).find('input.dashwindow-user-active').val()==dash_user_status_active) {
        var active = this.findToolbarItemByProperty('action','active');
        if (active) $(active.element).addClass('active').children('.glyphicon').removeClass('glyphicon-ban-circle').addClass('glyphicon-ok');
    }
};

var dash_user_drop = function(element) {
    if (!(element instanceof FileList) && typeof(element.parent)!="undefined" && element.parent=='files' && typeof(element.type)!="undefined" && element.type=='image') {
        var src = element.data;
        var regexp = new RegExp('\\'+desk_ds, 'g');
        $(this.sidebar).find('.dashwindow-userthumb-selector').attr('src',baseUrl(src.replace(regexp,'/')));
        $(this.content).find('input.dashwindow-user-image').val(src);
        $(this.sidebar).find('.dashwindow-userthumb-delete').removeClass('disabled');
        this.enableItemsByProperty('action','noimage');
    }
};

var dash_user_image_select = function() {
    desk_file_selector(function(selected){
        if (selected && selected.length>0 && typeof(selected[0].type)!="undefined" && selected[0].type=='image') {
            var src = selected[0].data;
            var regexp = new RegExp('\\'+desk_ds, 'g');
            $(this.element).find('.dashwindow-userthumb-selector').attr('src',baseUrl(src.replace(regexp,'/')).replace(desk_ds,'/'));
            $(this.content).find('input.dashwindow-user-image').val(src);
            $(this.sidebar).find('.dashwindow-userthumb-delete').removeClass('disabled');
            this.enableItemsByProperty('action','noimage');
        }
    }, this);
};

var dash_user_verified = function() {
    var verified = this.findToolbarItemByProperty('action','verified');
    if ($(this.content).find('input.dashwindow-user-verified').val()==dash_user_status_verified) {
        if (verified) $(verified.element).removeClass('active').children('.glyphicon').removeClass('glyphicon-ok').addClass('glyphicon-ban-circle');
        $(this.content).find('input.dashwindow-user-verified').val(dash_user_status_not_verified);
    } else {
        if (verified) $(verified.element).addClass('active').children('.glyphicon').removeClass('glyphicon-ban-circle').addClass('glyphicon-ok');
        $(this.content).find('input.dashwindow-user-verified').val(dash_user_status_verified);
    }
};

var dash_user_active = function() {
    var active = this.findToolbarItemByProperty('action','active');
    if ($(this.content).find('input.dashwindow-user-active').val()==dash_user_status_active) {
        if (active) $(active.element).removeClass('active').children('.glyphicon').removeClass('glyphicon-ok').addClass('glyphicon-ban-circle');
        $(this.content).find('input.dashwindow-user-active').val(dash_user_status_not_active);
    } else {
        if (active) $(active.element).addClass('active').children('.glyphicon').removeClass('glyphicon-ban-circle').addClass('glyphicon-ok');
        $(this.content).find('input.dashwindow-user-active').val(dash_user_status_active);
    }
};

var dash_user_delete_image = function() {
    $(this.sidebar).find('.dashwindow-userthumb-selector').attr('src',dash_user_profile_nophoto_src);
    $(this.content).find('input.dashwindow-user-image').val('');
    $(this.sidebar).find('.dashwindow-userthumb-delete').addClass('disabled');
    this.disableItemsByProperty('action','noimage');
};