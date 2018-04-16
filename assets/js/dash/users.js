var dash_users_delete_image = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].data)!="undefined") {
        desk_window_request(this, url('dash/users/noimage'),{'user_id':selected[0].data});
    }
};

var dash_users_deactivate = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length>0) {
        desk_window_request(this, url('dash/users/deactivate'),{'users':selected.items});
    }
};

var dash_users_activate = function() {
    var selected = desk_window_selected(this);
    if (selected && typeof(selected.items)!="undefined" && selected.items.length>0) {
        desk_window_request(this, url('dash/users/activate'),{'users':selected.items});
    }
};

var dash_users_select = function() {
    var selected = this.getSelectedContentItems();
    if (!selected || selected.length!=1 || typeof(selected[0].src)=="undefined" || selected[0].src==dash_user_profile_nophoto_src) {
        this.disableItemsByProperty('typo','noimage');
    }
    if (!selected || selected.length!=1 || typeof(selected[0].avatar)=="undefined" || selected[0].avatar.length==0) {
        this.disableItemsByProperty('typo','show_avatar');
    }
    if (!selected || selected.length!=1 || typeof(selected[0].activated)=="undefined" || selected[0].activated!=dash_user_status_active) {
        this.disableItemsByProperty('typo','view_profile');
    }
    if (selected && selected.length>0) {
        this.disableItemsByProperty('typo','deactivate');
        for (var i=0; i<selected.length; i++) {
            if (typeof(selected[i].activated)!="undefined" && selected[i].activated!=dash_user_status_not_active) {
                this.enableItemsByProperty('typo','deactivate');
                break;
            }
        }
        this.disableItemsByProperty('typo','activate');
        for (var i=0; i<selected.length; i++) {
            if (typeof(selected[i].activated)!="undefined" && selected[i].activated!=dash_user_status_active) {
                this.enableItemsByProperty('typo','activate');
                break;
            }
        }
    }
    if (selected && selected.length && selected.length==1 && (typeof(this.info_last_item)=="undefined" || this.info_last_item!=selected[0].data || $(this.element).find('.user-infobar').html().length==0)) {
        this.info_last_item = selected[0].data;
        $(this.element).find('.user-infobar').html('');
        try { window.clearTimeout(this.timer); } catch(err) {};
        this.timer = window.setTimeout(this.bind(this,function(){
            $(this.element).find('.user-infobar').html('');
            var selected = this.getSelectedContentItems();
            if (!selected || !selected.length || selected.length!=1) return;
            desk_post(url('dash/users/info'),{'user_id':selected[0].data, 'token':token()}, this.bind(this, function(response){
                if (response && response.length>0) {
                    $(this.element).find('.user-infobar').append('<div style="cursor:default;padding:0px;margin:5px 0px 0px"><span class="glyphicon glyphicon-info-sign"></span> '+t('Information')+':</div>');
                    $(this.element).find('.user-infobar').append('<div style="border-top:1px solid #B3B6D1;border-bottom:1px solid #EDEDF6;height:1px;padding:0px;margin:5px 0px"></div>');
                    for (var i=0; i<response.length; i++) {
                        var title = response[i].replace(/^.+title="([^"]+?)".+$/, '$1');
                        $(this.element).find('.user-infobar').append('<div style="font-weight:normal;padding:2px 0px;cursor:default;text-overflow:ellipsis;overflow:hidden" title="'+title+': '+response[i].split('>').slice(-1)[0]+'">'+response[i]+'</div>');
                    }
                }
            }));
        }),1000);
    }
};

var dash_users_show_avatar = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1 && typeof(selected[0].avatar)!="undefined" && selected[0].avatar.length>0) {
        $('body').append('<a href="'+selected[0].avatar+'" data-lightbox="user-avatar" id="dashwindow-user-avatar-lightbox"></a>');
        $('#dashwindow-user-avatar-lightbox').trigger('click');
        $('#dashwindow-user-avatar-lightbox').remove();
    }
};

var dash_users_edit = function() {
    try { window.clearTimeout(this.timer); } catch(err) {};
    desk_window_edit_item(this);
};

var dash_users_groups = function() {
  desk_call(dash_users_group_wnd, null);
};

var dash_users_group_filter = function(element) {
    var group_id = this.options.data.group_id;
    var id = $(element).attr('id');
    var item = this.findMenuItemByProperty('id',id);
    if (item && typeof(item.group_id)!="undefined") {
        if (item.group_id!=group_id) {
            this.options.data.group_id=item.group_id;
            desk_window_reload(this);
            $(element).parents('ul').find('.glyphicon-ok').removeClass('glyphicon-ok').addClass('glyphicon-filter');
            $(element).find('.glyphicon').removeClass('glyphicon-filter').addClass('glyphicon-ok');
        } else {
            this.options.data.group_id=0;
            desk_window_reload(this);
            $(element).parents('ul').find('.glyphicon-ok').removeClass('glyphicon-ok').addClass('glyphicon-filter');
        }
    }
};

var dash_users_view_profile = function() {
    var selected = this.getSelectedContentItems();
    if (selected && selected.length==1) {
        window.location.href=url('user/'+selected[0].data);
    }
};

var dash_users_load = function() {
    $(this.element).find('.user-infobar').html('');
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i].activated)!="undefined" && this.options.bodyItems[i].activated==dash_user_status_not_active) {
            $(this.options.bodyItems[i].element).addClass('inactive');
        }
    }
};