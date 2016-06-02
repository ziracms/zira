var dash_image_open = function() {
    if (typeof(this.options.data)=="undefined" || typeof(this.options.data.file)=="undefined") return;
    desk_window_request(this,url('dash/image/open'),{'file':this.options.data.file},this.bind(this, function(response){
        if (!response || !response.width || !response.height || !response.src) return;
        desk_window_request.inprogress = null;
        this.setLoading(true);
        this.options.data.image = new Image();
        this.options.data.image.onload=this.bind(this,function(){
            this.setLoading(false);
            this.options.data.image_width=this.options.data.image.width;
            this.options.data.image_height=this.options.data.image.height;
            this.clearBodyContent();
            this.appendBodyContent('<div><div><img src="'+response.src+'" width="'+this.options.data.image.width+'" height="'+this.options.data.image.height+'" style="display:inline-block;" /></div></div>');
            this.options.data.image=$(this.content).find('img');
            $(this.options.data.image).parent('div').css({'display':'table-cell','height':'100%','textAlign':'center','verticalAlign':'middle'});
            $(this.options.data.image).parent('div').parent('div').css({'display':'table-row'});
            $(this.options.data.image).parent('div').parent('div').parent('div').css({'display':'table','width':'100%','height':'100%'});
            this.enableItemsByProperty('action','zoom');
            this.enableItemsByProperty('action','resize');
            this.enableItemsByProperty('action','reload');
            this.enableItemsByProperty('action','crop');
            this.enableItemsByProperty('action','save');
            this.enableItemsByProperty('action','watermark');
            this.updateTitle(this.options.data.file);
            this.resetFooterContent();
            this.appendFooterContent(this.options.data.image_width+'x'+this.options.data.image_height+'px');
        });
        var regexp = new RegExp('\\'+desk_ds, 'g');
        this.options.data.image.src=baseUrl(this.options.data.file.replace(regexp,'/'))+'?t='+(new Date().getTime());
    }));
};

var dash_image_close = function() {
    if (typeof(this.options.data)=="undefined" || typeof(this.options.data.file)=="undefined") return;
    desk_window_request(this,url('dash/image/close'),{'file':this.options.data.file});
};

var dash_image_resize = function() {
    if (typeof(this.cropper)!="undefined" && this.cropper!=null) {
        this.cropper.destroy();
        this.cropper = null;
        this.disableItemsByProperty('action','cut');
        this.disableItemsByProperty('action','ratio');
    }
};

var dash_image_focus = function() {
    if (typeof(this.cropper)=="undefined" || this.cropper===null) return;
    this.cropper.disabled = false;
};

var dash_image_blur = function() {
    if (typeof(this.cropper)=="undefined" || this.cropper===null) return;
    this.cropper.disabled = true;
};

var dash_image_save = function() {
    desk_confirm(t('Replace image ?'), this.bind(this, function(){
        desk_window_request(this, url('dash/image/save'), {'file': this.options.data.file}, this.bind(this, function(response){
            if (!response || !response.message) return;
            desk_window_reload_all(dash_image_files_wnd);
        }));
    }));
};

var dash_image_zoom_in = function() {
    if (typeof(this.cropper)!="undefined" && this.cropper!=null) {
        this.cropper.destroy();
        this.cropper = null;
        this.disableItemsByProperty('action','cut');
        this.disableItemsByProperty('action','ratio');
    }
    var width = $(this.options.data.image).width();
    var height = $(this.options.data.image).height();
    $(this.options.data.image).css({'width':width*2,'height':height*2});
};

var dash_image_zoom_out = function() {
    if (typeof(this.cropper)!="undefined" && this.cropper!=null) {
        this.cropper.destroy();
        this.cropper = null;
        this.disableItemsByProperty('action','cut');
        this.disableItemsByProperty('action','ratio');
    }
    var width = $(this.options.data.image).width();
    var height = $(this.options.data.image).height();
    if (width<10 || height<10) return;
    $(this.options.data.image).css({'width':width/2,'height':height/2});
};

var dash_image_change_width = function() {
    if (typeof(this.cropper)!="undefined" && this.cropper!=null) {
        this.cropper.destroy();
        this.cropper = null;
        this.disableItemsByProperty('action','cut');
        this.disableItemsByProperty('action','ratio');
    }
    desk_prompt(t('Image width'), this.bind(this, function(width){
        var _width = parseInt(width);
        if (_width<=0) return;
        desk_window_request(this, url('dash/image/width'),{'width':_width, 'file':this.options.data.file}, this.bind(this, function(response){
            if (!response || !response.width || !response.height) return;
            this.options.data.image_width=response.width;
            this.options.data.image_height=response.height;
            $(this.options.data.image).css({'width':response.width,'height':response.height});
            if (response.src) $(this.options.data.image).attr('src',response.src);
            this.resetFooterContent();
            this.appendFooterContent(this.options.data.image_width+'x'+this.options.data.image_height+'px');
        }), function(){
            desk_error(t('An error occurred'));
        });
    }));
    $('#zira-prompt-dialog input[name=modal-input]').val(this.options.data.image_width);
};

var dash_image_change_height = function() {
    if (typeof(this.cropper)!="undefined" && this.cropper!=null) {
        this.cropper.destroy();
        this.cropper = null;
        this.disableItemsByProperty('action','cut');
        this.disableItemsByProperty('action','ratio');
    }
    desk_prompt(t('Image height'), this.bind(this, function(height){
        var _height = parseInt(height);
        if (_height<=0) return;
        desk_window_request(this, url('dash/image/height'),{'height':_height, 'file':this.options.data.file}, this.bind(this, function(response){
            if (!response || !response.width || !response.height) return;
            this.options.data.image_width=response.width;
            this.options.data.image_height=response.height;
            $(this.options.data.image).css({'width':response.width,'height':response.height});
            if (response.src) $(this.options.data.image).attr('src',response.src);
            this.resetFooterContent();
            this.appendFooterContent(this.options.data.image_width+'x'+this.options.data.image_height+'px');
        }), function(){
            desk_error(t('An error occurred'));
        });
    }));
    $('#zira-prompt-dialog input[name=modal-input]').val(this.options.data.image_height);
};

var dash_image_crop_width = function() {
    if (typeof(this.cropper)!="undefined" && this.cropper!=null) {
        this.cropper.destroy();
        this.cropper = null;
        this.disableItemsByProperty('action','cut');
        this.disableItemsByProperty('action','ratio');
    }
    desk_prompt(t('Image width'), this.bind(this, function(width){
        var _width = parseInt(width);
        if (_width<=0) return;
        desk_window_request(this, url('dash/image/cropwidth'),{'width':_width, 'file':this.options.data.file}, this.bind(this, function(response){
            if (!response || !response.width || !response.height) return;
            this.options.data.image_width=response.width;
            this.options.data.image_height=response.height;
            $(this.options.data.image).css({'width':response.width,'height':response.height});
            if (response.src) $(this.options.data.image).attr('src',response.src);
            this.resetFooterContent();
            this.appendFooterContent(this.options.data.image_width+'x'+this.options.data.image_height+'px');
        }), function(){
            desk_error(t('An error occurred'));
        });
    }));
    $('#zira-prompt-dialog input[name=modal-input]').val(this.options.data.image_width);
};

var dash_image_crop_height = function() {
    if (typeof(this.cropper)!="undefined" && this.cropper!=null) {
        this.cropper.destroy();
        this.cropper = null;
        this.disableItemsByProperty('action','cut');
        this.disableItemsByProperty('action','ratio');
    }
    desk_prompt(t('Image height'), this.bind(this, function(height){
        var _height = parseInt(height);
        if (_height<=0) return;
        desk_window_request(this, url('dash/image/cropheight'),{'height':_height, 'file':this.options.data.file}, this.bind(this, function(response){
            if (!response || !response.width || !response.height) return;
            this.options.data.image_width=response.width;
            this.options.data.image_height=response.height;
            $(this.options.data.image).css({'width':response.width,'height':response.height});
            if (response.src) $(this.options.data.image).attr('src',response.src);
            this.resetFooterContent();
            this.appendFooterContent(this.options.data.image_width+'x'+this.options.data.image_height+'px');
        }), function(){
            desk_error(t('An error occurred'));
        });
    }));
    $('#zira-prompt-dialog input[name=modal-input]').val(this.options.data.image_height);
};

var dash_image_reload = function() {
    if (typeof(this.cropper)!="undefined" && this.cropper!=null) {
        this.cropper.destroy();
        this.cropper = null;
        this.disableItemsByProperty('action','cut');
        this.disableItemsByProperty('action','ratio');
    }
    desk_window_request(this, url('dash/image/open'), {'file':this.options.data.file}, this.bind(this, function(response){
        if (!response || !response.width || !response.height || !response.src) return;
        this.options.data.image_width=response.width;
        this.options.data.image_height=response.height;
        $(this.options.data.image).css({'width':response.width,'height':response.height});
        $(this.options.data.image).attr('src',response.src);
        this.resetFooterContent();
        this.appendFooterContent(this.options.data.image_width+'x'+this.options.data.image_height+'px');
    }));
};

var dash_image_crop = function() {
    if (typeof(this.cropper)!="undefined" && this.cropper!=null) {
        this.cropper.destroy();
        this.cropper = null;
        this.disableItemsByProperty('action','cut');
        this.disableItemsByProperty('action','ratio');
    } else {
        this.enableItemsByProperty('action','cut');
        this.enableItemsByProperty('action','ratio');
        this.disableItemsByProperty('typo','0_0');
        this.cropper = $(this.options.data.image).cropper({'preview':false,'block_mode':true,'fixed':false,'sel_w':$(this.options.data.image).width()/2,'sel_h':$(this.options.data.image).height()/2,'sel_mw':10,'sel_mh':10});
        $(this.content).unbind('dblclick').on('dblclick','.image-cropper-selector',this.bind(this, function(){
            desk_call(dash_image_cut, this);
        }));
    }
};

var dash_image_aspect_ratio = function(element) {
    if (!this.cropper) return;
    this.cropper.destroy();
    var id = $(element).attr('id');
    var item = this.findToolbarItemByProperty('id',id);
    if (!item.typo) return;
    var ratio=item.typo.split('_');
    if (ratio.length!=2) return;
    ratio[0]=parseInt(ratio[0]);
    ratio[1]=parseInt(ratio[1]);
    this.enableItemsByProperty('action','ratio');
    this.disableItemsByProperty('typo',item.typo);
    if (!ratio[0] || !ratio[1]) {
        this.cropper = $(this.options.data.image).cropper({'preview':false,'block_mode':true,'fixed':false,'sel_w':$(this.options.data.image).width()/2,'sel_h':$(this.options.data.image).height()/2,'sel_mw':10,'sel_mh':10});
    } else {
        var w, h;
        if (this.options.data.image_width/this.options.data.image_height > ratio[0]/ratio[1]) {
            h = $(this.options.data.image).height()/2;
            w = (h * ratio[0]) / ratio[1];
        } else {
            w = $(this.options.data.image).width()/2;
            h = (w * ratio[1]) / ratio[0];
        }
        this.cropper = $(this.options.data.image).cropper({'preview':false,'block_mode':true,'fixed':true,'sel_w':w,'sel_h':h,'sel_mw':10,'sel_mh':10});
    }
};

var dash_image_cut = function() {
    if (!this.cropper) return;
    var rect = this.cropper.getRect();
    this.cropper.destroy();
    this.cropper = null;
    this.disableItemsByProperty('action','cut');
    this.disableItemsByProperty('action','ratio');
    desk_window_request(this, url('dash/image/crop'), {'file': this.options.data.file, 'width': rect.w, 'height': rect.h, 'left': rect.x, 'top': rect.y}, this.bind(this, function(response){
        if (!response || !response.width || !response.height || !response.src) return;
        this.options.data.image_width=response.width;
        this.options.data.image_height=response.height;
        $(this.options.data.image).css({'width':response.width,'height':response.height});
        $(this.options.data.image).attr('src',response.src);
        this.resetFooterContent();
        this.appendFooterContent(this.options.data.image_width+'x'+this.options.data.image_height+'px');
    }));
};

var dash_image_save_as = function() {
    desk_prompt(t('Enter name'), this.bind(this, function(name){
        if (!name || name.length==0) return;
        desk_window_request(this, url('dash/image/saveas'), {'file': this.options.data.file, 'name': name}, this.bind(this, function(response){
            if (!response || !response.message) return;
            desk_window_reload_all(dash_image_files_wnd);
        }));
    }));
    $('#zira-prompt-dialog input[name=modal-input]').val(this.options.data.file.split(desk_ds).slice(-1)[0].split('.').slice(0)[0]);
};

var dash_image_watermark = function() {
    desk_window_request(this, url('dash/image/watermark'), {'file': this.options.data.file}, this.bind(this, function(response){
        if (!response || !response.width || !response.height || !response.src) return;
        this.options.data.image_width=response.width;
        this.options.data.image_height=response.height;
        $(this.options.data.image).css({'width':response.width,'height':response.height});
        $(this.options.data.image).attr('src',response.src);
        this.resetFooterContent();
        this.appendFooterContent(this.options.data.image_width+'x'+this.options.data.image_height+'px');
    }));
};

var desk_image_editor = function(file) {
    try {
        var data = {'file':file};
        desk_call(dash_image_wnd, null, {'data':data});
    } catch(e) {
        desk_error(t('An error occurred'));
    }
};