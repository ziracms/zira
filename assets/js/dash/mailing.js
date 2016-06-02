var dash_mailing_load = function() {
    desk_window_form_init(this);
    var users = this.findToolbarItemByProperty('action','users');
    if (users) {
        $(users.element).text(t('Message')+' ('+this.options.data.users+')');
    }
    var subscribers = this.findToolbarItemByProperty('action','subscribers');
    if (subscribers) {
        $(subscribers.element).text(t('Email')+' ('+this.options.data.subscribers+')');
    }
    var type = $(this.content).find('.dash-mailing-type').eq(0).val();
    if (type == 'email') {
        this.enableItemsByProperty('action','users');
    } else if (type == 'message') {
        this.enableItemsByProperty('action','subscribers');
    }
    $(this.content).find('.dash-mailing-subject').unbind('keyup').keyup(this.bind(this,function(e){
        var subject_l = $(this.content).find('.dash-mailing-subject').eq(0).val().length;
        var message_l = $(this.content).find('.dash-mailing-message').eq(0).val().length;
        if (subject_l>0 && message_l>0){
            this.enableItemsByProperty('action','mail');
        } else {
            this.disableItemsByProperty('action','mail');
        }
    }));
    $(this.content).find('.dash-mailing-message').unbind('keyup').keyup(this.bind(this,function(e){
        var subject_l = $(this.content).find('.dash-mailing-subject').eq(0).val().length;
        var message_l = $(this.content).find('.dash-mailing-message').eq(0).val().length;
        if (subject_l>0 && message_l>0){
            this.enableItemsByProperty('action','mail');
        } else {
            this.disableItemsByProperty('action','mail');
        }
        $(this.content).find('.dash-mailing-message').eq(0).parent().children('.help-block').text(message_l);
    }));
    this.mail = function(){
        $(this.content).find('.dash-mailing-offset').eq(0).val(this.options.data.offset);
        var data = desk_window_content(this);
        desk_window_request(this, url('dash/system/mailing'), data, this.bind(this, function(response){
            if (!response || typeof(response.error)!="undefined") {
                desk_modal_progress_hide();
                return;
            }
            if (response.left>0){
                this.options.data.offset = ++response.offset;
                desk_modal_progress_update(Math.round((response.sent / response.total)*100));
                desk_window_request.finish_callback = this.bind(this, this.mail);
            } else {
                this.options.data.offset = 0;
                desk_modal_progress_hide();
                if (response.type=='email'){
                    desk_message(t('Successfully finished. Emails sent:')+' '+response.sent);
                } else if (response.type=='message'){
                    desk_message(t('Successfully finished. Messages sent:')+' '+response.sent);
                }
            }
        }));
    };
};

var dash_mailing_type_email = function() {
    this.enableItemsByProperty('action','users');
    this.disableItemsByProperty('action','subscribers');
    $(this.content).find('.dash-mailing-type').eq(0).val('email');
};

var dash_mailing_type_message = function() {
    this.enableItemsByProperty('action','subscribers');
    this.disableItemsByProperty('action','users');
    $(this.content).find('.dash-mailing-type').eq(0).val('message');
};

var dash_mialing_send = function() {
    var subject = $(this.content).find('.dash-mailing-subject').eq(0).val();
    var message = $(this.content).find('.dash-mailing-message').eq(0).val();
    if (subject.length>0 && message.length>0) {
        desk_modal_progress();
        this.mail();
    }
};