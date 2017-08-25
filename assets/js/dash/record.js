var dash_record_load = function() {
    desk_window_form_init(this);
    $(this.element).find('#dashrecordform_access_label').click(zira_bind(this, function(){
        var button = $(this.element).find('#dashrecordform_access_button');
        var container = $(this.element).find('#dashrecordform_access_container');
        if ($(container).css('display')=='none') {
            $(container).slideDown();
            $(button).find('.glyphicon').removeClass('glyphicon-menu-right').addClass('glyphicon glyphicon-menu-down');
        } else {
            $(container).slideUp();
            $(button).find('.glyphicon').removeClass('glyphicon-menu-down').addClass('glyphicon glyphicon-menu-right');
        }
    }));
};

