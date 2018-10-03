(function($){
    $(document).ready(function(){
        $('.container #content').on('submit','form.xhr-form',xhrSubmit);
    });

    function xhrSubmit(e) {
        e.stopPropagation();
        e.preventDefault();

        $(this).trigger('xhr-submit-start');

        var uploadApi=new UploadAPI($(this).attr('action'));
        uploadApi.onProgress=bind_xhr_callback(xhrSendProgress,this);
        uploadApi.onSuccess=bind_xhr_callback(xhrSendSuccess,this);
        uploadApi.onError=bind_xhr_callback(xhrSendError,this);
        uploadApi.init($(this).get(0));
        
        var progress = false;
        var enctype = $(this).attr('enctype');
        if (typeof enctype != "undefined" && enctype == 'multipart/form-data') {
            progress = true;
        }
        $(this).data('progress', progress);

        if (progress) {
            try {
                desk_modal_progress();
            } catch (err) {
                zira_modal_progress();
            }
        } else {
            $(this).find('input[type=submit],button[type=submit]').eq(0).after('<div class="zira-loader-wrapper"><span class="zira-loader glyphicon glyphicon-refresh"></span> <span class="percent"></span></div>');
        }
    }

    function bind_xhr_callback(method,object) {
        return function(arg1,arg2) {
            return method.call(object,arg1,arg2);
        };
    }

    function xhrSendProgress(percent) {
        var progress = $(this).data('progress');
        if (typeof progress != "undefined" && progress) {
            try {
                desk_modal_progress_update(percent);
            } catch (err) {
                zira_modal_progress_update(percent);
            }
        } else {
            $(this).find('.zira-loader-wrapper .percent').text(percent+'%');
        }
    }

    function xhrSendSuccess(text,ignore_empty) {
        if (typeof(ignore_empty)!='undefined' && ignore_empty && !text) return;

        $(this).find('input[type=file]').attr('value','');
        //$(this).get(0).reset();

        if (typeof(text)=='undefined') return;
        var response=null;

        if (text.length) {
            try {
                response=jQuery.parseJSON(text);
            } catch(err) {
                //console.log(err);
            }
        }
        
        var progress = $(this).data('progress');
        if (typeof progress != "undefined" && progress) {
            try {
                desk_modal_progress_hide(function(){
                    if (response.error && response.error.length>0) {
                        desk_error(response.error);
                    } else if(response.message && response.message.length>0) {
                        desk_message(response.message);
                    }
                });
            } catch (err) {
                zira_modal_progress_hide(function(){
                    if (response.error && response.error.length>0) {
                        zira_error(response.error);
                    } else if(response.message && response.message.length>0) {
                        zira_message(response.message);
                    }
                });
            }
        } else {
            $(this).find('.zira-loader-wrapper').remove();
            if (response.error && response.error.length>0) {
                try {
                    desk_error(response.error);
                } catch (err) {
                    zira_error(response.error);
                }
            } else if(response.message && response.message.length>0) {
                try {
                    desk_message(response.message);
                } catch (err) {
                    zira_message(response.message);
                }
            }
        }

        if (response.error) {
            $(this).trigger('xhr-submit-error', response);
        } else {
            $(this).trigger('xhr-submit-success', response);
        }
        $(this).trigger('xhr-submit-end', response);
    }

    function xhrSendError(text,status) {
        if (!text || !text.length) {
            text='{"error":"'+t('Load failed')+'"}';
        }

        xhrSendSuccess.call(this,text,true);
    }
})(jQuery);