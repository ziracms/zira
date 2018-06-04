var desk_message = function(message, callback, icon) {
    Desk.disableEvents();
    zira_message(message, function() {
        Desk.enableEvents();
        if (typeof(callback)!="undefined" && callback!==null) callback.call();
    }, icon);
    $('.zira-modal').css('zIndex',Desk.z+9);
    $('.modal-backdrop').css('zIndex',Desk.z+8);
};

var desk_timeout_message = function(message, icon) {
    Desk.disableEvents();
    zira_message(message, function() {
        Desk.enableEvents();
        var focus = Desk.findMaxZWindow();
        if (focus instanceof DashWindow) {
            Desk.focusWindow(focus);
        }
        try {
            window.clearTimeout(desk_timeout_message.timer);
        } catch (e) {}
    }, icon);
    $('.zira-modal').css('zIndex',Desk.z+9);
    $('.modal-backdrop').css('zIndex',Desk.z+8);
    desk_timeout_message.timer = window.setTimeout(function(){
        var dialog = $('#zira-message-dialog');
        $(dialog).find('button.btn-default').trigger('click');
    },1500);
};

var desk_error = function(message, icon) {
    Desk.disableEvents();
    zira_error(message, function() {
        Desk.enableEvents();
    }, icon);
    $('.zira-modal').css('zIndex',Desk.z+9);
    $('.modal-backdrop').css('zIndex',Desk.z+8);
};

var desk_confirm = function(message, yes_callback, no_callback) {
    Desk.disableEvents();
    zira_confirm(message, function() {
        Desk.enableEvents();
        if (typeof(yes_callback)!="undefined") yes_callback.call();
    },function() {
        Desk.enableEvents();
        if (typeof(no_callback)!="undefined") no_callback.call();
    });
    $('.zira-modal').css('zIndex',Desk.z+9);
    $('.modal-backdrop').css('zIndex',Desk.z+8);
};

var desk_prompt = function(message, ok_callback, cancel_callback) {
    Desk.disableEvents();
    zira_prompt(message, function(val) {
        Desk.enableEvents();
        if (typeof(ok_callback)!="undefined") ok_callback.call(null, val);
    },function() {
        Desk.enableEvents();
        if (typeof(cancel_callback)!="undefined") cancel_callback.call();
    });
    $('.zira-modal').css('zIndex',Desk.z+9);
    $('.modal-backdrop').css('zIndex',Desk.z+8);
};

var desk_multi_prompt = function(message, ok_callback, cancel_callback) {
    Desk.disableEvents();
    zira_multi_prompt(message, function(val) {
        Desk.enableEvents();
        if (typeof(ok_callback)!="undefined") ok_callback.call(null, val);
    },function() {
        Desk.enableEvents();
        if (typeof(cancel_callback)!="undefined") cancel_callback.call();
    });
    $('.zira-modal').css('zIndex',Desk.z+9);
    $('.modal-backdrop').css('zIndex',Desk.z+8);
};

var desk_modal_progress = function() {
    Desk.disableEvents();
    zira_modal_progress();
    $('.zira-modal').css('zIndex',Desk.z+9);
    $('.modal-backdrop').css('zIndex',Desk.z+8);
};

var desk_modal_progress_update = function(percent) {
    zira_modal_progress_update(percent);
};

var desk_modal_progress_hide = function(callback) {
    Desk.enableEvents();
    zira_modal_progress_hide(callback);
};

var desk_window = function(className, options, data) {
    if (typeof(data)!="undefined") {
        if (typeof(options.data)!="undefined" && typeof(options.data.token)!="undefined") {
            if (typeof(data.data)=="undefined") data.data = {};
            data.data.token = options.data.token;
        }
        options = jQuery.extend(options, data);
    }
    var single = false;
    if (typeof(options.singleInstance)!="undefined" && options.singleInstance)
        single = true;
    var id = className+'1';
    if (!single) {
        var classes = Desk.getWindowIdsByClass(className);
        if (classes!==null) {
            id = className+(classes.length+1);
        }
    }
    Desk.openWnd(id, className, options);
};

// overwritten
var desk_file_selector = function() {
    desk_error(t('An error occurred'));
};

// overwritten
var desk_image_editor = function() {
    desk_error(t('An error occurred'));
};

// overwritten
var desk_text_editor = function() {
    desk_error(t('An error occurred'));
};

// overwritten
var desk_html_editor = function() {
    desk_error(t('An error occurred'));
};

// overwritten
var desk_record_editor = function() {
    desk_error(t('An error occurred'));
};

// overwritten
var desk_record_category = function() {
    desk_error(t('An error occurred'));
};


var desk_window_close = function(wnd) {
    wnd = desk_get_window(wnd);
    if (wnd instanceof DashWindow) {
        wnd.getCloseButton().trigger('mousedown');
    }
};

var desk_get_window = function(wnd) {
    if (wnd instanceof DashWindow) {
        return wnd;
    } else {
        return Desk.findWindowById(wnd);
    }
};

var desk_get_all_windows = function(className) {
    var ids = Desk.getWindowIdsByClass(className);
    if (ids===null) return null;
    var wnds = [];
    for(var i=0; i<ids.length; i++) {
        var wnd = Desk.findWindowById(ids[i]);
        if (wnd instanceof DashWindow) {
            wnds.push(wnd);
        }
    }
    return wnds;
};

var desk_window_reload = function(wnd, rememberState) {
    if (typeof(rememberState)=="undefined") rememberState = false;
    wnd = desk_get_window(wnd);
    if (wnd===null) return;
    wnd.loadBody(rememberState);
};

var desk_window_reload_all = function(window_class, rememberState) {
    if (typeof(rememberState)=="undefined") rememberState = true;
    var wnds = desk_get_all_windows(window_class);
    if (wnds===null || wnds.length==0) return;
    for (var i=0; i<wnds.length; i++) {
        wnds[i].loadBody(rememberState);
    }
};

var desk_window_request = function(wnd, url, data, success_callback, error_callback, finish_callback) {
    if (typeof(desk_window_request.inprogress)=="undefined") desk_window_request.inprogress = null;
    if (desk_window_request.inprogress!==null) return;
    desk_window_request.inprogress = desk_get_window(wnd);
    desk_window_request.success_callback = success_callback;
    desk_window_request.error_callback = error_callback;
    desk_window_request.finish_callback = finish_callback;
    if (desk_window_request.inprogress===null) return;
    if (desk_window_request.inprogress.disabled) return;
    if (typeof(data)=="undefined") data = {};
    data.id = desk_window_request.inprogress.getId();
    data['class'] = desk_window_request.inprogress.getClass();
    data.format = 'json';
    if (typeof(desk_window_request.inprogress.options.data)!="undefined" && typeof(desk_window_request.inprogress.options.data.token)!="undefined") {
        data.token = desk_window_request.inprogress.options.data.token;
    }
    desk_window_request.inprogress.setLoading(true);
    desk_post(url, data, desk_window_request_success, desk_window_request_error, desk_window_request_finish);
};

var desk_window_request_success = function(response) {
    if (!response) {
        desk_window_request_error();
        return;
    }
    if (typeof(response.message)!="undefined" && response.message.length>0) {
        desk_timeout_message(response.message);
    } else if (typeof(response.error)!="undefined" && response.error.length>0) {
        desk_error(response.error);
    }
    if (typeof(response.reload)!="undefined" && response.reload.length>0) {
        desk_window_reload_all(response.reload);
    } else if (typeof(response.close)!="undefined" && response.close && desk_window_request.inprogress!==null) {
        desk_window_close(desk_window_request.inprogress);
    }
    if (typeof(desk_window_request.success_callback)!="undefined" && desk_window_request.success_callback!==null) {
        desk_window_request.success_callback.call(null, response);
    }
};

var desk_window_request_error = function() {
    if (typeof(desk_window_request.error_callback)!="undefined" && desk_window_request.error_callback!==null) {
        desk_window_request.error_callback.call(null);
    } else {
        desk_error(t('Load failed'));
    }
};

var desk_window_request_finish = function() {
    if (typeof(desk_window_request.inprogress)!="undefined" && (desk_window_request.inprogress instanceof DashWindow)) {
        desk_window_request.inprogress.setLoading(false);
    }
    var callback = desk_window_request.finish_callback;

    desk_window_request.inprogress = null;
    desk_window_request.success_callback = null;
    desk_window_request.error_callback = null;
    desk_window_request.finish_callback = null;

    if (typeof(callback)!="undefined" && callback!==null) {
        callback.call(null);
    }
};

var desk_window_content = function(wnd) {
    wnd = desk_get_window(wnd);
    if (wnd===null) return {};
    wnd.updateContent();
    var data = $(wnd.getContent()).find('form').eq(0).serializeArray();
    var content = {};
    for (var i=0; i<data.length; i++) {
        if (data[i]['name'].substring(data[i]['name'].length-2)=='[]') {
            var _n = data[i]['name'].substring(0, data[i]['name'].length-2);
            if (typeof(content[_n])=="undefined") content[_n] = [];
            content[_n].push(data[i]['value']);
        } else {
            content[data[i]['name']]=data[i]['value'];
        }
    }

    return content;
};

var desk_window_selected = function(wnd, limit) {
    wnd = desk_get_window(wnd);
    if (wnd===null) return {};
    data = wnd.getSelectedContentItems();
    var content = {
        'items': []
    };
    for (var i=0; i<data.length; i++) {
        if (typeof(limit)!="undefined" && i>=limit) break;
        if (typeof(data[i].data)=="undefined") continue;
        content.items.push(data[i].data);
    }
    return content;
};

var desk_window_save = function(wnd) {
    wnd = desk_get_window(wnd);
    if (!(wnd instanceof DashWindow)) return;
    wnd.saveBody();
};

var desk_window_create_item = function(wnd) {
    wnd = desk_get_window(wnd);
    if (!(wnd instanceof DashWindow)) return;
    wnd.createBodyItem();
};

var desk_window_edit_item = function(wnd) {
    wnd = desk_get_window(wnd);
    if (!(wnd instanceof DashWindow)) return;
    wnd.editBodyItem();
};

var desk_window_call_item = function(wnd) {
    wnd = desk_get_window(wnd);
    if (!(wnd instanceof DashWindow)) return;
    wnd.callBodyItem();
};

var desk_window_delete_items = function(wnd) {
    wnd = desk_get_window(wnd);
    if (!(wnd instanceof DashWindow)) return;
    wnd.deleteBodyItems();
};

var desk_window_select_items = function(wnd) {
    wnd = desk_get_window(wnd);
    if (!(wnd instanceof DashWindow)) return;
    wnd.selectContentItems();
};

var desk_window_unselect_items = function(wnd) {
    wnd = desk_get_window(wnd);
    if (!(wnd instanceof DashWindow)) return;
    wnd.unselectContentItems();
};

var desk_window_search = function(wnd, text) {
    wnd = desk_get_window(wnd);
    if (wnd===null) return;
    if (wnd.isDisabled()) return;
    if (wnd.options.data===null) wnd.options.data = {};
    wnd.options.data.search = text;
    var search = wnd.findToolbarItemByProperty('action','search');
    if (!search || typeof(search.element)=="undefined") return;

    if (text.length>0 &&  (typeof(search.notEmpty)=="undefined" || !search.notEmpty)) {
        $(search.element).parent().find('.input-group-addon .glyphicon')
            .removeClass('glyphicon-search')
            .addClass('glyphicon-remove')
            .addClass('dashboard-glyphicon-pointer')
            .unbind('click')
            .click(function(){
                if (wnd.isDisabled()) return;
                $(search.element).val('');
                desk_window_search(wnd, '');
            })
        ;
        search.notEmpty = true;
    } else if (text.length==0 && typeof(search.notEmpty)!="undefined" && search.notEmpty) {
        $(search.element).parent().find('.input-group-addon .glyphicon')
            .removeClass('glyphicon-remove')
            .removeClass('dashboard-glyphicon-pointer')
            .addClass('glyphicon-search')
            .unbind('click')
        ;
        search.notEmpty = false;
    }
    try {
        window.clearTimeout(desk_window_search_init.timer);
    } catch(e){}
    wnd.loadBody();
};

var desk_window_search_init = function(wnd) {
    wnd = desk_get_window(wnd);
    if (wnd === null) return;

    var search = wnd.findToolbarItemByProperty('action','search');
    if (!search || typeof(search.element)=="undefined") return;

    if (typeof(search.isBinded)=="undefined" || !search.isBinded) {
        $(search.element).unbind('keyup').keyup(function(e){
            if (typeof(e.keyCode)=="undefined") return;
            try {
                window.clearTimeout(desk_window_search_init.timer);
            } catch(e){}
            if (e.keyCode == 13) return;
            if (wnd.isDisabled()) return;
            desk_window_search_init.timer = window.setTimeout(function(){
                var text = $(search.element).val();
                if (typeof(wnd.options.data.search)!="undefined" && wnd.options.data.search==text) {
                    return;
                }
                desk_window_search(wnd, text);
            },1000);
        });
        search.isBinded = true;
    }
    
    if ((typeof (wnd.options) == "undefined" || 
            typeof (wnd.options.data)== "undefined" || 
            typeof (wnd.options.data.search) == "undefined" || 
            wnd.options.data.search.length==0) && 
        typeof(search.notEmpty)!="undefined" && search.notEmpty
        ) {
        $(search.element).parent().find('.input-group-addon .glyphicon')
            .removeClass('glyphicon-remove')
            .removeClass('dashboard-glyphicon-pointer')
            .addClass('glyphicon-search')
            .unbind('click')
        ;
        search.notEmpty = false;
    }
};

var desk_window_pagination_next = function(wnd) {
    wnd = desk_get_window(wnd);
    if (wnd===null) return;
    if (wnd.options.data===null) wnd.options.data = {};
    if (typeof(wnd.options.data.page)=="undefined" || wnd.options.data.page===null) wnd.options.data.page = 1;
    wnd.options.data.page++;

    if (typeof(wnd.options.data.pages)!="undefined") {
        if (wnd.options.data.page>wnd.options.data.pages) wnd.options.data.page = wnd.options.data.pages;
        if (wnd.options.data.page==wnd.options.data.pages) {
            wnd.disableToolbarItem(wnd.findToolbarItemByProperty('action','pagination-next'));
        }
    }
    if (wnd.options.data.page>1) {
        wnd.enableToolbarItem(wnd.findToolbarItemByProperty('action','pagination-prev'));
    }

    wnd.loadBody();
};

var desk_window_pagination_prev = function(wnd) {
    wnd = desk_get_window(wnd);
    if (wnd===null) return;
    if (wnd.options.data===null) wnd.options.data = {};
    if (typeof(wnd.options.data.page)=="undefined" || wnd.options.data.page===null) wnd.options.data.page = 1;
    wnd.options.data.page--;
    if (wnd.options.data.page<1) wnd.options.data.page = 1;

    if (wnd.options.data.page==1) {
        wnd.disableToolbarItem(wnd.findToolbarItemByProperty('action','pagination-prev'));
    }
    if (typeof(wnd.options.data.pages)!="undefined" && wnd.options.data.page<wnd.options.data.pages) {
        wnd.enableToolbarItem(wnd.findToolbarItemByProperty('action','pagination-next'));
    }

    wnd.loadBody();
};

var desk_window_pagination_init = function(wnd) {
    wnd = desk_get_window(wnd);
    if (wnd===null) return;
    if (wnd.options.data===null) wnd.options.data = {};
    if (typeof(wnd.options.data.page)=="undefined" || wnd.options.data.page===null) wnd.options.data.page = 1;
    if (wnd.options.data.page<1) wnd.options.data.page = 1;

    if (typeof(wnd.options.data.pages)!="undefined") {
        wnd.disableToolbarItem(wnd.findToolbarItemByProperty('action','pagination-prev'));
        wnd.disableToolbarItem(wnd.findToolbarItemByProperty('action','pagination-next'));

        if (wnd.options.data.page>1) {
            wnd.enableToolbarItem(wnd.findToolbarItemByProperty('action','pagination-prev'));
        }
        if (wnd.options.data.page<wnd.options.data.pages) {
            wnd.enableToolbarItem(wnd.findToolbarItemByProperty('action','pagination-next'));
        }
        if (wnd.options.data.pages>1) {
            wnd.resetFooterContent();
            var footerContent = '<span contenteditable="true" class="footer-page-editable">'+wnd.options.data.page+'</span> <span>'+t('from')+' '+wnd.options.data.pages+'</span>';
            if (typeof wnd.options.data.limit != "undefined") {
                footerContent += '&nbsp;&nbsp;/&nbsp;&nbsp;<span contenteditable="true" class="footer-limit-editable">'+wnd.options.data.limit+'</span>';
            }
            wnd.appendFooterContent(footerContent);
            $(wnd.footer).find('.footer-page-editable').keydown(zira_bind(wnd, function(e){
                if (e.keyCode != 48 && e.keyCode != 49 && e.keyCode != 50 && e.keyCode != 51 && e.keyCode != 52 && e.keyCode != 53 && e.keyCode != 54 && e.keyCode != 55 && e.keyCode != 56 && e.keyCode != 57 && e.keyCode != 8 && e.keyCode != 46 && e.keyCode != 37 && e.keyCode != 39) {
                    e.stopPropagation();
                    e.preventDefault();
                    if (e.keyCode == 13) {
                        var page = $(this.footer).find('.footer-page-editable').text();
                        if (page.length==0) page = this.options.data.page;
                        page = parseInt(page);
                        if (page<=1) page = 1;
                        if (page>this.options.data.pages) page = this.options.data.pages;
                        $(this.footer).find('.footer-page-editable').text(page);
                        this.options.data.page = page;
                        $(this.footer).find('.footer-page-editable').get(0).blur();
                        this.loadBody();
                    }
                }
            })).keyup(function(e){
                e.stopPropagation();
            }).blur(zira_bind(wnd, function(e){
                $(this.footer).find('.footer-page-editable').text(this.options.data.page);
            }));
            $(wnd.footer).find('.footer-limit-editable').keydown(zira_bind(wnd, function(e){
                if (e.keyCode != 48 && e.keyCode != 49 && e.keyCode != 50 && e.keyCode != 51 && e.keyCode != 52 && e.keyCode != 53 && e.keyCode != 54 && e.keyCode != 55 && e.keyCode != 56 && e.keyCode != 57 && e.keyCode != 8 && e.keyCode != 46 && e.keyCode != 37 && e.keyCode != 39) {
                    e.stopPropagation();
                    e.preventDefault();
                    if (e.keyCode == 13) {
                        var limit = $(this.footer).find('.footer-limit-editable').text();
                        if (limit.length==0) limit = this.options.data.limit;
                        limit = parseInt(limit);
                        if (limit<=1) limit = 1;
                        if (limit>1000) limit = 1000;
                        $(this.footer).find('.footer-limit-editable').text(limit);
                        this.options.data.limit = limit;
                        $(this.footer).find('.footer-limit-editable').get(0).blur();
                        this.loadBody();
                    }
                }
            })).keyup(function(e){
                e.stopPropagation();
            }).blur(zira_bind(wnd, function(e){
                $(this.footer).find('.footer-limit-editable').text(this.options.data.limit);
            }));
        } else {
            wnd.resetFooterContent();
        }
    }
};

var desk_window_sort_asc = function(wnd) {
    wnd = desk_get_window(wnd);
    if (wnd===null) return;
    if (wnd.options.data===null) wnd.options.data = {};

    wnd.options.data.order = 'asc';
    wnd.disableToolbarItem(wnd.findToolbarItemByProperty('action','order-asc'));
    wnd.enableToolbarItem(wnd.findToolbarItemByProperty('action','order-desc'));

    wnd.loadBody();
};

var desk_window_sort_desc = function(wnd) {
    wnd = desk_get_window(wnd);
    if (wnd===null) return;
    if (wnd.options.data===null) wnd.options.data = {};

    wnd.options.data.order = 'desc';
    wnd.disableToolbarItem(wnd.findToolbarItemByProperty('action','order-desc'));
    wnd.enableToolbarItem(wnd.findToolbarItemByProperty('action','order-asc'));

    wnd.loadBody();
};

var desk_window_sorter_init = function(wnd) {
    wnd = desk_get_window(wnd);
    if (wnd===null) return;
    if (wnd.options.data===null) wnd.options.data = {};
    if (typeof(wnd.options.data.order)=="undefined" || wnd.options.data.order===null) wnd.options.data.order = 'desc';

    if (wnd.options.data.order=='desc') {
        wnd.enableToolbarItem(wnd.findToolbarItemByProperty('action','order-asc'));
    } else if (wnd.options.data.order=='asc') {
        wnd.enableToolbarItem(wnd.findToolbarItemByProperty('action','order-desc'));
    }
};

var desk_upload = function (token, url, dir, files, callback, max_upload_size, max_upload_files, className) {
    if (typeof(files)=="undefined" || !(files instanceof FileList)) return;
    if (typeof(desk_upload.inprogress)=="undefined") desk_upload.inprogress = false;
    if (desk_upload.inprogress) return;

    var xhr = desk_get_xhr();

    xhr.upload.addEventListener("progress", function(e) {
        if (e.lengthComputable) {
            var percentage = Math.round((e.loaded * 100) / e.total);
            desk_modal_progress_update(percentage);
        }
    }, false);

    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            desk_upload.inprogress = false;
            desk_modal_progress_hide(callback);
            var response = Desk.parseJSON(xhr.responseText);

            if (typeof(response.message)!="undefined" && response.message.length>0) {
                desk_message(response.message);
            } else if (typeof(response.error)!="undefined" && response.error.length>0) {
                desk_error(response.error);
            }
            if (typeof(response.reload)!="undefined" && response.reload.length>0) {
                desk_window_reload_all(response.reload);
            }
        } else if (xhr.readyState == 4 && xhr.status != 200) {
            desk_upload.inprogress = false;
            desk_modal_progress_hide(callback);
            desk_error(t('Load failed'));
        }
    };

    try {
        xhr.open("POST", url);
    } catch(err) {
        desk_error(t('Sorry, but it seems that your browser is not supported.'));
        return;
    }

    xhr.overrideMimeType('text/plain; charset=x-user-defined-binary');

    var data = desk_get_form_data();
    data.append('format','json');
    data.append('dirroot', dir);
    data.append('token', token);
    if (typeof(className)!="undefined") data.append('class', className);
    var co = 0;
    if (typeof(max_upload_size)=="undefined") max_upload_size = null;
    if (typeof(max_upload_files)=="undefined") max_upload_files = 0;
    if (max_upload_files>0 && files.length>max_upload_files) {
        desk_error(t('Maximum files count per upload:')+' '+max_upload_files);
        return;
    }
    var total_size = 0;
    for (var i=0; i<files.length; i++) {
        if (files[i].size>0) {
            data.append('files[]', files[i]);
            co++;
            total_size+=files[i].size;
        }
    }

    if (co>0) {
        if (max_upload_size!==null && total_size>max_upload_size) {
            max_upload_size = (max_upload_size / 1048576).toFixed(1);
            desk_error(t('Maximum upload size:')+' '+max_upload_size+' MB');
            return;
        }
        desk_upload.inprogress = true;
        desk_modal_progress();
        xhr.send(data);
    }
};

var desk_post = function(url, data, onSuccess, onError, onFinish) {
    var xhr = desk_get_xhr();

    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            try {
                var response = Desk.parseJSON(xhr.responseText);
                if (typeof(onSuccess) != "undefined" && onSuccess !== null) onSuccess.call(null, response);
                if (typeof(onFinish) != "undefined" && onFinish !== null) onFinish.call();
            } catch(err) {
                if (typeof(onError)!="undefined" && onError!==null) onError.call();
                if (typeof(onFinish)!="undefined" && onFinish!==null) onFinish.call();
            }
        } else if (xhr.readyState == 4 && xhr.status != 200) {
            if (typeof(onError)!="undefined" && onError!==null) onError.call();
            if (typeof(onFinish)!="undefined" && onFinish!==null) onFinish.call();
        }
    };

    try {
        xhr.open("POST", url);
    } catch(err) {
        desk_error(t('Sorry, but it seems that your browser is not supported.'));
        return;
    }

    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    var urldata = '';
    for(var i in data) {
        if (urldata.length>0) urldata += '&';
        if (typeof(data[i])=='object') {
            for (var y in data[i]) {
                if (urldata.length>0) urldata += '&';
                urldata += i+'[]'+'='+encodeURIComponent(data[i][y]);
            }
        } else {
            urldata += i+'='+encodeURIComponent(data[i]);
        }
    }

    xhr.send(urldata);
    return xhr;
};

var desk_get_xhr = function() {
    var xhr = new Desk.xhr();
    xhr.send = Desk.xhrSend;
    xhr.open = Desk.xhrOpen;
    xhr.overrideMimeType = Desk.xhrOverrideMimeType;
    xhr.setRequestHeader = Desk.xhrSetRequestHeader;
    return xhr;
};

var desk_get_form_data = function() {
    var data = new Desk.formData();
    data.append = Desk.formDataAppend;
    return data;
};

var desk_window_form_init = function(window) {
    if (window instanceof DashWindow) {
        $(window.content).find('form.dash-window-form').submit(function(e) {
            e.stopPropagation();
            e.preventDefault();
        });
        $(window.content).find('.form-dropdown .dropdown-menu a').click(zira_init_form_dropdown);
        $(window.content).find('.form-file-button :file').change(zira_init_form_file_button);
        $(window.content).find('form.dash-window-form .form-group:odd').addClass('odd');
        var window_id = window.getId();
        $(window.content).find('form.dash-window-form .form-control').each(function(){
            var id = $(this).attr('id');
            if (id.length>0) {
                $(this).attr('id', id + '-' + window_id);
            }
            var label = $(this).parents('.form-group').find('.control-label');
            if ($(label).length>0) {
                var for_id = $(label).attr('for');
                $(label).attr('for',for_id+'-'+window_id);
            }
            var inline_label = $(this).parents('.control-label-inline');
            if ($(inline_label).length>0) {
                var i_for_id = $(inline_label).attr('for');
                $(inline_label).attr('for',i_for_id+'-'+window_id);
            }
        });
    }
};

var desk_call = function(name, object, arg) {
    if (typeof(name)=='string') {
        if (typeof(object)!="undefined") eval(name + '.call(object, arg);');
        else eval(name + '.call(null, arg);');
    } else {
        if (typeof(object)!="undefined") name.call(object, arg);
        else name.call(null, arg);
    }
};

var token = function() {
    if (typeof(desk_token)=="undefined") throw('Token is not defined');
    return desk_token;
};

var t = function(str) {
    if (typeof(desk_strings)!="undefined" && typeof(desk_strings[str])!="undefined") {
        return desk_strings[str];
    } else if (typeof (zira_strings)!="undefined" && typeof(zira_strings[str])!="undefined") {
        return zira_strings[str];
    } else {
        return str;
    }
};

var url = function(path) {
    var u = '';
    if (typeof(desk_url)!="undefined") {
        u += desk_url;
    }
    if (u.substr(-1)=='/') {
        u = u.substr(0, u.length-1);
    }
    if (typeof(path)=="undefined") path = '';
    if (path.substr(0,1)=='/') {
        path = path.substr(1);
    }
    return u + '/' + path;
};

var baseUrl = function(path) {
    var u = '';
    if (typeof(desk_base)!="undefined") {
        u += desk_base;
    } else if (typeof(zira_base)!="undefined") {
        u += zira_base;
    }
    if (u.substr(-1)=='/') {
        u = u.substr(0, u.length-1);
    }
    if (typeof(path)=="undefined") path = '';
    if (path.substr(0,1)=='/') {
        path = path.substr(1);
    }
    return u + '/' + path;
};

var desk_editor_category_callback = function() {
    var item = $(this).data('item');
    if (typeof(item)=="undefined") return;
    desk_record_category(item);
};

var desk_editor_record_callback = function() {
    var item = $(this).data('item');
    if (typeof(item)=="undefined") return;
    desk_record_editor(item);
};