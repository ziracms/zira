var DashWindow = function(id, className, options) {
    this.window_class = 'dashboard-window';
    this.window_classic_class = 'dashboard-window-classic';
    this.header_class = 'dashboard-window-header';
    this.footer_class = 'dashboard-window-footer';
    this.sidebar_class = 'dashboard-window-sidebar';
    this.content_class = 'dashboard-window-content';
    this.menu_class = 'dashboard-window-menu';
    this.menu_list_class = 'dashboard-window-menu-list';
    this.menu_list_item_class = 'dashboard-window-menu-list-item';
    this.menu_list_item_link_class = 'dashboard-window-menu-list-item-link';
    this.window_resizer_class = 'dashboard-window-resizer';
    this.sidebar_resizer_class = 'dashboard-sidebar-resizer';
    this.close_button_class = 'dashboard-window-close-button';
    this.maximize_button_class = 'dashboard-window-maximize-button';
    this.minimize_button_class = 'dashboard-window-minimize-button';
    this.maximized_window_class = 'dashboard-window-maximized';
    this.minimized_window_class = 'dashboard-window-minimized';
    this.focused_window_class = 'dashboard-window-focused';
    this.animating_window_class = 'dashboard-window-animating';
    this.nosidebar_window_class = 'dashboard-window-nosidebar';
    this.title_text_class = 'dashboard-window-title';
    this.toolbar_class = 'dashboard-window-toolbar';
    this.notoolbar_window_class = 'dashboard-window-notoolbar';
    this.noresize_window_class = 'dashboard-window-noresize';
    this.sidebar_content_wrapper_class = 'dashboard-sidebar-content-wrapper';
    this.body_content_wrapper_class = 'dashboard-body-content-wrapper';
    this.body_full_content_wrapper_class = 'dashboard-body-full-content-wrapper';
    this.footer_content_wrapper_class = 'dashboard-footer-content-wrapper';
    this.dashwindow_content_icon_class = 'dashwindow-content-icon';
    this.dashwindow_content_icon_folder_class = 'dashwindow-content-icon-folder';
    this.dashwindow_content_icon_file_class = 'dashwindow-content-icon-file';
    this.dashwindow_content_icon_archive_class = 'dashwindow-content-icon-archive';
    this.dashwindow_content_icon_audio_class = 'dashwindow-content-icon-audio';
    this.dashwindow_content_icon_video_class = 'dashwindow-content-icon-video';
    this.dashwindow_content_icon_txt_class = 'dashwindow-content-icon-txt';
    this.dashwindow_content_icon_html_class = 'dashwindow-content-icon-html';
    this.dashwindow_content_icon_zira_class = 'dashwindow-content-icon-zira';
    this.dashwindow_content_icon_blank_class = 'dashwindow-content-icon-blank';
    this.dashwindow_content_list_class = 'dashwindow-content-list';
    this.dashwindow_content_grid_class = 'dashwindow-content-grid';
    this.dashwindow_content_column_class = 'dashwindow-content-column';
    this.dashwindow_content_column_row_class = 'dashwindow-content-column-row';
    this.disabled_window_class = 'dashboard-window-disabled';
    this.loading_window_class = 'dashboard-window-loading';
    this.loader_class = 'dashboard-window-loader';
    this.context_menu_class = 'dashboard-window-contextmenu';
    this.highlight_class = 'dashboard-window-highlight';
    this.blink_class = 'dashboard-window-blink';
    this.body_item_images_class = 'dashboard-window-body-item-image';
    this.dashwindow_content_noselect_class = 'dashboard-window-noselect-content';
    this.moving_window_class = 'dashboard-window-moving';

    this.id = id;
    this.className = className;
    this.initialized = false;
    this.maximized = false;
    this.minimized = false;
    this.disabled = false;
    this.touchesEnabled = false;
    this.loading = false;
    this.scrollY = 0;

    var defaults = {
        'container':  'body',
        'edge_top': 0,
        'edge_bottom': 0,
        'edge_left': 0,
        'edge_right': 0,
        'top': null,
        'left': null,
        'right': null,
        'bottom': null,
        'width': null,
        'height': null,
        'auto': false,
        'auto_ratio': 4/3,
        'auto_margin': 50,
        'auto_min_width': 640,
        'auto_min_height': 480,
        'auto_max_width': 800,
        'auto_max_height': 600,
        'resize': true,
        'resize_min_width': 400,
        'resize_min_height': 300,
        'min_options_to_window': true,
        'sidebar_resize_min_width': 100,
        'minimized_width': 250,
        'animate': true,
        'animation_duration': 100,
        'maximized': false,
        'sidebar': true,
        'sidebar_width': null,
        'sidebar_custom_width': null,
        'toolbar': true,
        'viewSwitcher': false,
        'bodyViewList': false,
        'maximize_on_move': true,
        'maximize_top_offset': 50,
        'maximize_left_offset': 5,
        'maximize_right_offset': 5,
        'icon_class': 'glyphicon glyphicon-th-large',
        'title': null,
        'menuItems': [],
        'toolbarItems': [],
        'toolbarContent': '',
        'sidebarItems': [],
        'sidebarContent': '',
        'bodyItems': [],
        'bodyContent': '',
        'bodyFullContent': '',
        'footerContent': '',
        'contextMenuItems': [],
        'onOpen': null,
        'onLoad': null,
        'onFocus': null,
        'onSelect': null,
        'onClose': null,
        'onBlur': null,
        'onDrop': null,
        'onResize': null,
        'load': null,
        'data': null,
        'nocache': false,
        'classic_mode': false
    };

    if(typeof(options)!="undefined") {
        this.options = $.extend(defaults, options);
    } else {
        this.options = defaults;
    }

    this.setWindowScroll();
    this.setWindowRect();
    this.checkSizeOptions();

    if (this.options.width!==null) {
        if (this.options.auto_min_width>this.options.width) {
            this.options.auto_min_width=this.options.width;
        }
        if (this.options.resize_min_width>this.options.width) {
            this.options.resize_min_width=this.options.width;
        }
    }
    if (this.options.height!==null) {
        if (this.options.auto_min_height>this.options.height) {
            this.options.auto_min_height=this.options.height;
        }
        if (this.options.resize_min_height>this.options.height) {
            this.options.resize_min_height=this.options.height;
        }
    }
    
    if (this.options.classic_mode) {
        this.window_class += ' ' + this.window_classic_class;
    }

    this.create();
    this.createMenu();
    this.body_view_list = this.options.bodyViewList;
    if (this.toolbar!==null) {
        this.createToolbar();
        this.appendToolbarContent(this.options.toolbarContent);
    }
    if (this.sidebar!==null) {
        this.createSidebarItems(this.options.sidebarItems);
        this.appendSidebarContent(this.options.sidebarContent);
        this.updateSidebarResizerPosition();
    }
    this.createBodyItems(this.options.bodyItems);
    this.appendBodyContent(this.options.bodyContent);
    this.appendBodyFullContent(this.options.bodyFullContent);
    this.appendFooterContent(this.options.footerContent);
    this.createContextMenu();

    if (this.options.bodyItems.length>0 && this.options.bodyContent.length==0 && this.options.bodyFullContent.length==0) {
        $(this.content).addClass(this.dashwindow_content_noselect_class);
    }

    this.clicked = false;
    this.hovered = false;
    this.moving = false;
    this.window_resizing = false;
    this.sidebar_resizing = false;
    this.z = 0;
    this.selected = [];
    this.content_clicked = false;
    this.item_clicked = false;
    this.maximized_array = [];
    this.minimized_array = [];
    this.menu_clicked = false;
    this.menu_opened = false;
    this.context_menu_opened = false;
    this.focusedElement = null;
    this.keys = null;
    this.maximized_type = null;

    this.onFocusCallback = null;
    this.onBlurCallback = null;
    this.onMaximizeCallback = null;
    this.onUnmaximizeCallback = null;
    this.onMinimizeCallback = null;
    this.onUnminimizeCallback = null;
    this.onResizeCallback = null;
    this.onDestroyCallback = null;
    this.onMenuItemCallback = null;
    this.onLoadCallback = null;
    
    if (this.options.maximized) {
        this.initialized = true;
        this.maximize(true, false, this.onInitialize);
    } else if (!this.options.animate) {
        this.initialized = true;
        this.onInitialize();
    } else {
        this.animateOpening();
    }
};

DashWindow.prototype.bind = function(object, method) {
    return function(arg) {
        return method.call(object,arg);
    };
};

DashWindow.prototype.getId = function() {
    return this.id;
};

DashWindow.prototype.getClass = function() {
    return this.className;
};

DashWindow.prototype.setClicked = function(clicked) {
    this.clicked = clicked;
};

DashWindow.prototype.setHovered = function(hovered) {
    this.hovered = hovered;
};

DashWindow.prototype.setMoving = function(moving) {
    this.moving = moving;
    $(this.element).removeClass(this.moving_window_class);
    if (this.moving) {
        $(this.element).addClass(this.moving_window_class);
    }
};

DashWindow.prototype.setWindowResizing = function(resizing) {
    this.window_resizing = resizing;
};

DashWindow.prototype.setSidebarResizing = function(resizing) {
    this.sidebar_resizing = resizing;
};

DashWindow.prototype.setZ = function(z) {
    this.z = z;
    this.updateZ();
};

DashWindow.prototype.getZ = function() {
    return this.z
};

DashWindow.prototype.updateZ = function() {
    $(this.element).css('zIndex', this.z);
};

DashWindow.prototype.setContentClicked = function(clicked) {
    this.content_clicked = clicked;
};

DashWindow.prototype.isContentClicked = function() {
    return this.content_clicked;
};

DashWindow.prototype.setItemClicked = function(clicked) {
    this.item_clicked = clicked;
};

DashWindow.prototype.isItemClicked = function() {
    return this.item_clicked;
};

DashWindow.prototype.setKeysArr = function(keys) {
    this.keys = keys;
};

DashWindow.prototype.focus = function() {
    if (!this.focused && this.options.onFocus !== null) {
        this.options.onFocus.call(this);
    }
    if (!this.focused && this.maximized && 
        !this.isTouchesEnabled()
    ) {
        $('body').css('overflow','hidden');
    }
    if (!this.focused) {
        $(this.element).addClass(this.focused_window_class);
        if (this.focusedElement!==null) $(this.focusedElement).focus();
    }
    this.focused = true;
    if (this.onFocusCallback !== null) {
        this.onFocusCallback.call(this);
    }
};

DashWindow.prototype.isFocused = function() {
    return this.focused;
};

DashWindow.prototype.blur = function(exec_callback) {
    if (typeof(exec_callback)=="undefined") exec_callback = false;
    this.focused = false;
    $(this.element).removeClass(this.focused_window_class);
    if (exec_callback) {
        if (this.options.onBlur !== null) this.options.onBlur.call(this);
        if (this.options.bodyItems.length==0 && (this.options.bodyContent.length>0 || this.options.bodyFullContent.length>0)) {
            var focusedElement = $(this.content).find(':focus');
            if ($(focusedElement).length > 0) this.focusedElement = focusedElement;
        }
        if (this.onBlurCallback !== null) {
            this.onBlurCallback.call(this);
        }
    }
};

DashWindow.prototype.disableWindow = function() {
    this.disabled = true;
    $(this.element).addClass(this.disabled_window_class);
};

DashWindow.prototype.enableWindow = function() {
    this.disabled = false;
    $(this.element).removeClass(this.disabled_window_class);
};

DashWindow.prototype.setTouchesEnabled = function(enabled) {
    this.touchesEnabled = enabled;
};

DashWindow.prototype.setMaximizedArray = function(arr) {
    this.maximized_array = arr;
};

DashWindow.prototype.setMinimizedArray = function(arr) {
    this.minimized_array = arr;
};

DashWindow.prototype.isInitialized = function() {
    return this.initialized;
};

DashWindow.prototype.isTouchesEnabled = function() {
    return this.touchesEnabled;
};

DashWindow.prototype.isMinimized = function() {
    return this.minimized;
};

DashWindow.prototype.isMaximized = function() {
    return this.maximized;
};

DashWindow.prototype.isDisabled = function() {
    return this.disabled;
};

DashWindow.prototype.highlightWindow = function() {
    $(this.element).addClass(this.highlight_class);
};

DashWindow.prototype.unhighlightWindow = function() {
    $(this.element).removeClass(this.highlight_class);
};

DashWindow.prototype.blinkWindow = function(iter) {
    if (typeof(iter)=="undefined") iter=0;
    if (iter%2==0) {
        $(this.element).addClass(this.blink_class)
    } else {
        $(this.element).removeClass(this.blink_class);
    }
    if (iter>=5) {
        $(this.element).removeClass(this.blink_class);
        return;
    }
    window.setTimeout(this.bind(this, function(){
        this.blinkWindow(++iter);
    }),this.options.animation_duration);
};

DashWindow.prototype.setWindowScroll = function() {
    this.window_scroll_left = $(window).scrollLeft();
    this.window_scroll_top = $(window).scrollTop();
};

DashWindow.prototype.setWindowRect = function() {
    this.window_width = $(window).width();
    this.window_height = $(window).height();
    this.window_top = this.options.edge_top;
    this.window_bottom = this.options.edge_bottom;
    this.window_left = this.options.edge_left;
    this.window_right = this.options.edge_right;
    this.window_inner_width = this.window_width - this.window_left - this.window_right;
    this.window_inner_height = this.window_height - this.window_top - this.window_bottom;
};

DashWindow.prototype.checkSizeOptions = function() {
    if (!this.options.min_options_to_window) return;
    if (this.options.auto_min_width>this.window_width-2*this.options.auto_margin) {
        this.options.auto_min_width=this.window_width-2*this.options.auto_margin;
    }
    if (this.options.auto_min_height>this.window_height-2*this.options.auto_margin) {
        this.options.auto_min_height=this.window_height-2*this.options.auto_margin;
    }
    if (this.options.resize_min_width>this.window_width) {
        this.options.resize_min_width=this.window_width;
    }
    if (this.options.resize_min_height>this.window_height) {
        this.options.resize_min_height=this.window_height;
    }
    if (this.options.sidebar_resize_min_width>this.window_width/2) {
        this.options.sidebar_resize_min_width=this.window_width/2;
    }
};

DashWindow.prototype.create = function() {
    $(this.options.container).append('<div id="'+this.id+'" class="'+this.window_class+'"></div>');
    this.element = $('#'+this.id);
    if (this.options.auto) {
        this.autoSize();
        this.autoPosition();
    }
    if (!this.options.resize) {
        $(this.element).addClass(this.noresize_window_class);
    }
    this.updateOuterSize();
    this.updatePosition();
    $(this.element).append('<div class="'+this.header_class+'"></div>');
    $(this.element).append('<div class="'+this.menu_class+'"></div>');
    if (this.options.toolbar) {
        $(this.element).append('<div class="'+this.toolbar_class+'"></div>');
        this.toolbar = $(this.element).children('.'+this.toolbar_class);
        this.options.toolbar_height = $(this.toolbar).outerHeight();
    } else {
        this.toolbar = null;
        $(this.element).addClass(this.notoolbar_window_class);
        this.options.toolbar_height = 0;
    }
    if (this.options.sidebar) {
        $(this.element).append('<div class="'+this.sidebar_class+'"></div>');
        this.sidebar = $(this.element).children('.'+this.sidebar_class);
    } else {
        this.sidebar = null;
        $(this.element).addClass(this.nosidebar_window_class);
    }
    $(this.element).append('<div class="'+this.content_class+'"></div>');
    $(this.element).append('<div class="'+this.footer_class+'"></div>');
    this.header = $(this.element).children('.'+this.header_class);
    this.menu = $(this.element).children('.'+this.menu_class);
    this.content = $(this.element).children('.'+this.content_class);
    this.footer = $(this.element).children('.'+this.footer_class);
    $(this.header).append('<a href="javascript:void(0)" class="'+this.close_button_class+'" title="Esc"></a>');
    $(this.header).append('<a href="javascript:void(0)" class="'+this.minimize_button_class+'" title="Ctrl + &dArr;"></a>');
    $(this.header).append('<a href="javascript:void(0)" class="'+this.maximize_button_class+'" title="Ctrl + &uArr;"></a>');
    this.close_button = $(this.header).children('.'+this.close_button_class);
    this.minimize_button = $(this.header).children('.'+this.minimize_button_class);
    this.maximize_button = $(this.header).children('.'+this.maximize_button_class);
    this.createWindowResizer();
    if (this.sidebar!==null) {
        if (this.options.sidebar_width===null) {
            this.options.sidebar_width = $(this.sidebar).outerWidth();
        }
        if (this.options.sidebar_width>this.options.width/2) {
            this.options.sidebar_width = this.options.width/2;
        }
        this.setSidebarWidth(this.options.sidebar_width);
        if (this.options.sidebar_resize_min_width>this.options.sidebar_width) {
            this.options.sidebar_resize_min_width=this.options.sidebar_width;
        }
        this.options.sidebar_custom_width = this.options.sidebar_width;
        this.createSidebarResizer();
    }
    $(this.header).append('<div class="'+this.title_text_class+'"></div>');
    if (this.options.title!==null) {
        this.setTitle();
    }
    this.createLoader();
    this.options.header_height = $(this.header).outerHeight();
    this.options.menu_height = $(this.menu).outerHeight();
    this.options.footer_height = $(this.footer).outerHeight();
    this.options.window_resizer_width = $(this.window_resizer).outerWidth();
    this.options.window_resizer_height = $(this.window_resizer).outerHeight();
    this.updateInnerSize();
    this.updateWindowResizerPosition();
};

DashWindow.prototype.setTitle = function(icon, text) {
    if (typeof(icon)=="undefined") icon = this.options.icon_class;
    if (typeof(text)=="undefined") text = this.options.title;
    var title = $(this.header).children('.'+this.title_text_class);
    if ($(title).length>0) {
        $(title).html('<span class="'+icon+'"></span>&nbsp;'+text);
        $(title).attr('title', text);
    }
};

DashWindow.prototype.updateTitle = function(text) {
    this.options.title = text;
    this.setTitle();
};

DashWindow.prototype.createLoader = function() {
    $(this.menu).append('<div class="'+this.loader_class+'"></div>');
};

DashWindow.prototype.createSidebarResizer = function() {
    $(this.content).append('<div class="'+this.sidebar_resizer_class+'"></div>');
    this.sidebar_resizer = $(this.content).children('.'+this.sidebar_resizer_class);
    this.options.sidebar_resizer_width = $(this.sidebar_resizer).outerWidth();
};

DashWindow.prototype.createWindowResizer = function() {
    $(this.footer).append('<div class="'+this.window_resizer_class+'"></div>');
    this.window_resizer = $(this.footer).children('.'+this.window_resizer_class);
};

DashWindow.prototype.animateOpening = function() {
    $(this.element).addClass(this.animating_window_class);
    $(this.element).css({
        'width': this.options.width,
        'height': this.options.height,
        'left': this.options.left,
        'top': -this.options.height,
        'opacity': 0
    }).animate({
        'width': this.options.width,
        'height': this.options.height,
        'left': this.options.left,
        'top': this.options.top,
        'opacity': 1
    },{
        'duration': this.options.animation_duration,
        'progress': this.bind(this,function(){
            var t = (new Date()).getTime();
            if (typeof(this.animation_progress_time)!="undefined" && t-this.animation_progress_time<50) return;
            this.animation_progress_time = t;
            var h = $(this.element).height() - this.options.header_height - this.options.menu_height - this.options.toolbar_height - this.options.footer_height;
            if (this.sidebar!==null) $(this.sidebar).css('height', h);
            $(this.content).css('height', h);
        }),
        'always': this.bind(this,function(){
            this.onResize();
            this.initialized = true;
            $(this.element).removeClass(this.animating_window_class);
            this.onInitialize();
        })
    });
};

DashWindow.prototype.onInitialize = function() {
    this.disableEditItems();
    this.disableDeleteItems();
    if (this.options.onOpen!==null) {
        this.options.onOpen.call(this);
    }
    this.loadBody();
};

DashWindow.prototype.loadBody = function(rememberState) {
    if (this.options.load!==null) {
        var data = {};
        this.load(this.options.load, data, rememberState);
    }
};

DashWindow.prototype.autoSize = function() {
    if (this.window_width>this.window_height) {
        this.options.height = this.window_height - 2*this.options.auto_margin - this.window_top - this.window_bottom;
        this.options.width = this.options.height * this.options.auto_ratio;
    } else {
        this.options.width = this.window_width - 2*this.options.auto_margin - this.window_left - this.window_right;
        this.options.height = this.options.width / this.options.auto_ratio;
    }
    if (this.options.width>this.window_inner_width) {
        this.options.width = this.window_inner_width;
    }
    if (this.options.height>this.window_inner_height) {
        this.options.height = this.window_inner_height;
    }
    if (this.options.width<this.options.auto_min_width) {
        this.options.width = this.options.auto_min_width;
    }
    if (this.options.height<this.options.auto_min_height) {
        this.options.height = this.options.auto_min_height;
    }
    if (this.options.width>this.options.auto_max_width) {
        this.options.width = this.options.auto_max_width;
    }
    if (this.options.height>this.options.auto_max_height) {
        this.options.height = this.options.auto_max_height;
    }
    this.checkSidebarWidth();
};

DashWindow.prototype.autoPosition = function() {
    this.options.left = (this.window_width+this.window_left-this.options.width)/2;
    if (this.options.left<this.window_left) this.options.left=this.window_left;
    this.options.top = (this.window_height+this.window_top-this.options.height)/2;
    if (typeof(this.options.bad_positions)!="undefined") {
         while ($.inArray(this.options.top+'-'+this.options.left, this.options.bad_positions)>=0) {
            this.options.top += 10;
            this.options.left += 10;

        }
    }
    this.checkPositionEdge();
};

DashWindow.prototype.checkPositionEdge = function() {
    if (this.options.left+this.options.width>this.window_width-this.window_right) {
        this.options.left = this.window_width - this.window_right - this.options.width;
    }
    if (this.options.top+this.options.height>this.window_height-this.window_bottom) {
        this.options.top = this.window_height - this.window_bottom - this.options.height;
    }
    if (this.options.left<this.window_left) {
        this.options.left = this.window_left;
    }
    if (this.options.top<this.window_top) {
        this.options.top = this.window_top;
    }
};

DashWindow.prototype.updateOuterSize = function() {
    if (this.options.width!==null) {
        $(this.element).css('width', this.options.width);
    } else {
        this.options.width = $(this.element).outerWidth();
    }
    if (this.options.height!==null) {
        $(this.element).css('height', this.options.height);
    } else {
        this.options.height = $(this.element).outerHeight();
    }
};

DashWindow.prototype.updatePosition = function() {
    if (this.options.top!==null) {
        $(this.element).css('top', this.options.top).css('bottom', 'auto');
    } else if (this.options.bottom!==null) {
        $(this.element).css('bottom', this.options.bottom).css('top', 'auto');
    }
    if (this.options.left!==null) {
        $(this.element).css('left', this.options.left).css('right', 'auto');
    } else if (this.options.right!==null) {
        $(this.element).css('right', this.options.right).css('left', 'auto');
    }
    if (this.options.left===null && this.options.right===null) {
        this.options.left = (this.window_width-this.options.width)/2;
        this.options.right = this.options.left + this.options.width;
        $(this.element).css('left', this.options.left);
    } else if (this.options.left!==null) {
        this.options.right = this.options.left + this.options.width;
    } else if (this.options.right!==null) {
        this.options.left = this.options.right - this.options.width;
    }
    if (this.options.top===null || this.options.bottom!==null) {
        this.options.top = parseInt($(this.element).css('top'));
        this.options.bottom = this.options.top + this.options.height;
    } else if (this.options.top!==null) {
        this.options.bottom = this.options.top + this.options.height;
    } else if (this.options.bottom!==null) {
        this.options.top = this.options.bottom - this.options.height;
    }
};

DashWindow.prototype.updateWindowResizerPosition = function() {
    this.options.window_resizer_top = $(this.window_resizer).offset().top-$(this.element).offset().top;
    this.options.window_resizer_left = $(this.window_resizer).offset().left-$(this.element).offset().left;
};

DashWindow.prototype.updateSidebarResizerSize = function() {
    if ($(this.sidebar_resizer).length>0) {
        $(this.sidebar_resizer).css('height', 1);
        $(this.sidebar_resizer).css('height', $(this.content).get(0).scrollHeight);
    }
};

DashWindow.prototype.updateSidebarResizerPosition = function() {
    if (this.sidebar!==null) {
        this.options.sidebar_resizer_top = $(this.sidebar_resizer).offset().top-$(this.element).offset().top;
        this.options.sidebar_resizer_left = $(this.sidebar_resizer).offset().left-$(this.element).offset().left;
        this.options.sidebar_resizer_bottom = this.options.sidebar_resizer_top+$(this.sidebar_resizer).outerHeight();
    }
};

DashWindow.prototype.updateContentPosition = function() {
    this.options.content_top = $(this.content).offset().top - $(this.element).offset().top;
    this.options.content_left = $(this.content).offset().left - $(this.element).offset().left;
    this.options.content_width = $(this.content).outerWidth();
    this.options.content_height = $(this.content).outerHeight();
    this.options.content_bottom = this.options.content_top + this.options.content_height;
    this.options.content_right = this.options.content_left + this.options.content_width;
};

DashWindow.prototype.updateInnerSize = function() {
    var h = this.options.height - this.options.header_height - this.options.menu_height - this.options.toolbar_height - this.options.footer_height - 2;
    $(this.content).css('height', h);
    this.updateContentPosition();
    if (this.sidebar!==null) {
        $(this.sidebar).css('height', h);
        this.updateSidebarResizerSize();
    }
};

DashWindow.prototype.onWindowResize = function() {
    var maximizedLeft = false;
    var maximizedRight = false;
    if (this.maximized && this.window_right == this.window_width / 2) maximizedLeft = true;
    else if (this.maximized && this.window_left == this.window_width / 2) maximizedRight = true;
    this.setWindowRect();
    this.checkSizeOptions();
    if (typeof(this.unminimize_maximize)!="undefined" && this.unminimize_maximize) {
        this.unminimize_maximize_window_left = this.window_left;
        this.unminimize_maximize_window_right = this.window_right;
    }
    if (this.minimized) {
        this.minimize(false, true);
    } else if (this.maximized) {
        if (maximizedLeft) this.maximizeLeft(false, true);
        else if (maximizedRight) this.maximizeRight(false, true);
        else this.maximize(false, true);
    } else if (this.options.auto) {
        this.autoSize();
        this.checkPositionEdge();
        this.onResize();
    } else {
        this.checkPositionEdge();
        this.onResize();
    }
    if (this.onResizeCallback !== null) {
        this.onResizeCallback.call(this);
    }
};

DashWindow.prototype.onResize = function() {
    this.updateOuterSize();
    this.updateInnerSize();
    this.updatePosition();
    this.updateWindowResizerPosition();
    this.updateSidebarResizerPosition();
    if (this.options.onResize!==null) {
        this.options.onResize.call(this);
    }
};

DashWindow.prototype.onWindowScroll = function() {
    this.setWindowScroll();
};

DashWindow.prototype.isHovered = function(pageX, pageY) {
    if (this.hovered) return true;
    var x = pageX - this.window_scroll_left;
    var y = pageY - this.window_scroll_top;
    return (x > this.options.left && x < this.options.right && y > this.options.top && y < this.options.bottom);
};

DashWindow.prototype.isHeaderHovered = function(pageX, pageY) {
    var x = pageX - this.window_scroll_left;
    var y = pageY - this.window_scroll_top;
    return (x > this.options.left && x < this.options.right && y > this.options.top && y < this.options.top + this.options.header_height);
};

DashWindow.prototype.isFooterHovered = function(pageX, pageY) {
    var x = pageX - this.window_scroll_left;
    var y = pageY - this.window_scroll_top;
    return (x > this.options.left && x < this.options.right && y > this.options.bottom - this.options.footer_height && y < this.options.bottom);
};

DashWindow.prototype.isMovingHovered = function(pageX, pageY) {
    //return this.isHeaderHovered(pageX, pageY) || this.isFooterHovered(pageX, pageY);
    return this.isHeaderHovered(pageX, pageY);
};

DashWindow.prototype.isWindowResizerHovered = function(pageX, pageY) {
    var x = pageX - this.window_scroll_left;
    var y = pageY - this.window_scroll_top;

    var l = this.options.left+this.options.window_resizer_left;
    var r = this.options.left+this.options.window_resizer_left + this.options.window_resizer_width;
    var t = this.options.top+this.options.window_resizer_top;
    var b = this.options.top+this.options.window_resizer_top + this.options.window_resizer_height;
    return (x>l && x<r && y>t && y<b);
};

DashWindow.prototype.isSidebarResizerHovered = function(pageX, pageY) {
    if (this.sidebar===null) return false;
    this.updateSidebarResizerPosition();
    var x = pageX - this.window_scroll_left;
    var y = pageY - this.window_scroll_top;

    var l = this.options.left+this.options.sidebar_resizer_left;
    var r = this.options.left+this.options.sidebar_resizer_left+this.options.sidebar_resizer_width;
    var t = this.options.top+this.options.sidebar_resizer_top;
    var b = this.options.top+this.options.sidebar_resizer_bottom;
    return (x>l && x<r && y>t && y<b);
};

DashWindow.prototype.isContentHovered = function(pageX, pageY) {
    var x = pageX - this.window_scroll_left;
    var y = pageY - this.window_scroll_top;

    var l = this.options.left+this.options.content_left;
    var r = this.options.left+this.options.content_right;
    var t = this.options.top+this.options.content_top;
    var b = this.options.top+this.options.content_bottom;
    return (x>l && x<r && y>t && y<b);
};

DashWindow.prototype.isClicked = function() {
    return this.clicked;
};

DashWindow.prototype.isMoving = function() {
    return this.moving;
};

DashWindow.prototype.isWindowResizing = function() {
    return this.window_resizing;
};

DashWindow.prototype.isSidebarResizing = function() {
    if (this.sidebar===null) return false;
    return this.sidebar_resizing;
};

DashWindow.prototype.move = function(dx, dy) {
    if (!this.initialized) return;
    if (this.maximized) return;
    if (this.minimized) return;
    this.options.left += dx;
    this.options.top += dy;
    this.updatePosition();
};

DashWindow.prototype.maximizeOnMove = function(pageX, pageY) {
    if (this.minimized) return;
    if (this.options.maximize_on_move && !this.maximized) {
        var x = pageX - this.window_scroll_left;
        var y = pageY - this.window_scroll_top;
        if (x<=this.options.maximize_left_offset) {
            this.maximizeLeft();
        } else if (x>=this.window_width-this.options.maximize_right_offset) {
            this.maximizeRight();
        } else if (y<=this.options.maximize_top_offset) {
            this.maximize();
        }
    }
};

DashWindow.prototype.resize = function(dx, dy) {
    if (!this.initialized) return;
    if (!this.options.resize) return;
    if (this.minimized) return;
    if (this.maximized) return;
    this.options.width += dx;
    this.options.height += dy;
    if (this.options.width < this.options.resize_min_width) {
        this.options.width = this.options.resize_min_width;
    }
    if (this.options.height < this.options.resize_min_height) {
        this.options.height = this.options.resize_min_height;
    }
    this.checkSidebarWidth();
    this.onResize();
};

DashWindow.prototype.resizeSidebar = function(dx, dy) {
    if (this.sidebar===null) return;
    if (!this.initialized) return;
    if (this.minimized) return;
    this.options.sidebar_width += dx;
    if (this.options.sidebar_width < this.options.sidebar_resize_min_width) {
        this.options.sidebar_width = this.options.sidebar_resize_min_width;
    }
    if (this.options.sidebar_width > this.options.width/2) {
        this.options.sidebar_width = this.options.width/2;
    }
    this.options.sidebar_custom_width = this.options.sidebar_width;
    this.setSidebarWidth(this.options.sidebar_width);
    this.onResize();
};

DashWindow.prototype.checkSidebarWidth = function() {
    if (this.sidebar!==null && this.options.sidebar_width > this.options.width/2) {
        this.options.sidebar_width = this.options.width/2;
        this.setSidebarWidth(this.options.sidebar_width);
    } else if (this.sidebar!==null && this.options.sidebar_custom_width && this.options.sidebar_custom_width <= this.options.width/2 && this.options.sidebar_width < this.options.sidebar_custom_width) {
        this.options.sidebar_width = this.options.sidebar_custom_width;
        this.setSidebarWidth(this.options.sidebar_width);
    }
};

DashWindow.prototype.setSidebarWidth = function(width) {
    if (this.sidebar!==null) {
        $(this.sidebar).css('width', width);
        $(this.content).css('marginLeft', width);
    }
};

DashWindow.prototype.getHeader = function() {
    return this.header;
};

DashWindow.prototype.getCloseButton = function() {
    return this.close_button;
};

DashWindow.prototype.getMinimizeButton = function() {
    return this.minimize_button;
};

DashWindow.prototype.getMaximizeButton = function() {
    return this.maximize_button;
};

DashWindow.prototype.getContent = function() {
    return this.content;
};

DashWindow.prototype.getSidebar = function() {
    return this.sidebar;
};

DashWindow.prototype.getToolbar = function() {
    return this.toolbar;
};

DashWindow.prototype.getFooter = function() {
    return this.footer;
};

DashWindow.prototype.destroy = function() {
    if (!this.initialized) return;
    
    if (this.maximized) {
        $('body').css('overflow','auto');
        $('body').css('overflow','');
        
        var maximized = this.findMaximizedArrayIndex();
        if (maximized!==null) {
            this.maximized_array.splice(maximized, 1);
        }
    }

    if (this.options.onClose!==null) {
        this.options.onClose.call(this);
    }

    if (this.hasContextMenu()) this.destroyContextMenu();

    this.initialized = false;
    if (this.options.animate) {
        this.animateClosing();
    } else {
        $(this.element).remove();
    }

    if (this.onDestroyCallback !== null) {
        this.onDestroyCallback.call(this);
    }
};

DashWindow.prototype.animateClosing = function() {
    $(this.element).addClass(this.animating_window_class);
    var dx = this.options.width / 2;
    var dy = this.options.height / 2;
    $(this.element).animate({
        'width': this.options.width-dx,
        'height': this.options.height-dy,
        'left': this.options.left + dx/2,
        'top': this.options.top + dy/2,
        'opacity': 0
    },{
        'duration': this.options.animation_duration,
        'progress': this.bind(this,function(){
            var t = (new Date()).getTime();
            if (typeof(this.animation_progress_time)!="undefined" && t-this.animation_progress_time<50) return;
            this.animation_progress_time = t;
            var h = $(this.element).height() - this.options.header_height - this.options.menu_height - this.options.toolbar_height - this.options.footer_height;
            if (this.sidebar!==null) $(this.sidebar).css('height', h);
            $(this.content).css('height', h);
        }),
        'always': this.bind(this,function(){
            $(this.element).removeClass(this.animating_window_class);
            $(this.element).remove();
        })
    });
};

DashWindow.prototype.maximize_unmaximize = function() {
    if (!this.maximized) {
        this.maximize();
    } else {
        this.unmaximize();
    }
};

DashWindow.prototype.maximize = function(remember_position, disable_animation, callback) {
    if (!this.initialized) return;

    var minimized = false;
    if (this.minimized) {
        this.unminimize(true);
        minimized = true;
    }

    this.maximized = true;
    $(this.element).addClass(this.maximized_window_class);

    if (typeof(remember_position)=="undefined") remember_position = true;
    if (remember_position) {
        this.maximized_array.push(this);
        this.unmaximize_left = this.options.left;
        this.unmaximize_top = this.options.top;
        this.unmaximize_width = this.options.width;
        this.unmaximize_height = this.options.height;
    }
    
    if (this.window_left == this.options.edge_left && this.window_right == this.options.edge_right) {
        this.maximized_type = 'all';
    }
    
    if (!this.isTouchesEnabled()) {
        $('body').css('overflow','hidden');
        this.setWindowRect();
        if (this.maximized_type == 'left') {
            this.window_right = this.window_width / 2;
        }
        if (this.maximized_type == 'right') {
            this.window_left = this.window_width / 2;
        }
    }

    this.options.left = this.window_left;
    this.options.top = this.window_top;
    this.options.width = this.window_width - this.window_left - this.window_right;
    this.options.height = this.window_height - this.window_top - this.window_bottom;

    this.checkSidebarWidth();

    if (typeof(disable_animation)=="undefined") disable_animation = false;
    if (this.options.animate && !disable_animation) {
        if (minimized) {
            $(this.menu).slideDown(this.options.animation_duration);
            this.showHeaderButtons();
            if (this.toolbar!==null) $(this.toolbar).slideDown(this.options.animation_duration);
        }
        this.animateMaximizing(callback);
    } else {
        if (minimized) {
            $(this.menu).show();
            this.showHeaderButtons();
            if (this.toolbar!==null) $(this.toolbar).show();
        }
        this.onResize();
        if (typeof(callback)!="undefined") {
            callback.call(this);
        }
    }

    if (this.onMaximizeCallback !== null) {
        this.onMaximizeCallback.call(this);
    }
};

DashWindow.prototype.maximizeLeft = function(remember_position, disable_animation, callback) {
    if (this.maximized && this.window_right == this.window_width / 2) return;
    if (this.maximized) {
        this.unmaximize(true);
    }
    this.maximized_type = 'left';
    this.window_right = this.window_width / 2;
    this.maximize(remember_position, disable_animation, callback);
};

DashWindow.prototype.maximizeRight = function(remember_position, disable_animation, callback) {
    if (this.maximized && this.window_left == this.window_width / 2) return;
    if (this.maximized) {
        this.unmaximize(true);
    }
    this.maximized_type = 'right';
    this.window_left = this.window_width / 2;
    this.maximize(remember_position, disable_animation, callback);
};

DashWindow.prototype.animateMaximizing = function(callback) {
    $(this.element).addClass(this.animating_window_class);
    this.initialized = false;
    $(this.element).animate({
        'width': this.options.width,
        'height': this.options.height,
        'left': this.options.left,
        'top': this.options.top
    },{
        'duration': this.options.animation_duration,
        'progress': this.bind(this,function(){
            var t = (new Date()).getTime();
            if (typeof(this.animation_progress_time)!="undefined" && t-this.animation_progress_time<50) return;
            this.animation_progress_time = t;
            var h = $(this.element).height() - this.options.header_height - this.options.menu_height - this.options.toolbar_height - this.options.footer_height;
            if (this.sidebar!==null) $(this.sidebar).css('height', h);
            $(this.content).css('height', h);
        }),
        'always': this.bind({'window':this, 'callback': callback},function(){
            this.window.onResize();
            this.window.initialized = true;
            $(this.window.element).removeClass(this.window.animating_window_class);
            if (typeof(this.callback)!="undefined") {
                this.callback.call(this.window);
            }
        })
    });
};

DashWindow.prototype.unmaximize = function(unmaximize_only) {
    if (!this.initialized) return;
    this.maximized = false;
    $(this.element).removeClass(this.maximized_window_class);
    this.options.left = this.unmaximize_left;
    this.options.top = this.unmaximize_top;
    this.options.width = this.unmaximize_width;
    this.options.height = this.unmaximize_height;
    
    var maximized = this.findMaximizedArrayIndex();
    if (maximized!==null) {
        this.maximized_array.splice(maximized, 1);
    }
    
    var edges = this.getMaximizedEdges();
    if (!edges.left && !edges.right) {
        $('body').css('overflow','auto');
        $('body').css('overflow','');
        this.setWindowRect();
    }
    
    if (typeof(unmaximize_only)!="undefined" && unmaximize_only) return;

    this.maximized_type = null;

    this.checkPositionEdge();
    this.checkSidebarWidth();

    if (this.options.animate) {
        this.animateUnmaximizing();
    } else {
        this.onResize();
    }

    if (this.onUnmaximizeCallback !== null) {
        this.onUnmaximizeCallback.call(this);
    }
};

DashWindow.prototype.animateUnmaximizing = function() {
    $(this.element).addClass(this.animating_window_class);
    this.initialized = false;
    $(this.element).animate({
        'width': this.options.width,
        'height': this.options.height,
        'left': this.options.left,
        'top': this.options.top
    },{
        'duration': this.options.animation_duration,
        'progress': this.bind(this,function(){
            var t = (new Date()).getTime();
            if (typeof(this.animation_progress_time)!="undefined" && t-this.animation_progress_time<50) return;
            this.animation_progress_time = t;
            var h = $(this.element).height() - this.options.header_height - this.options.menu_height - this.options.toolbar_height - this.options.footer_height;
            if (this.sidebar!==null) $(this.sidebar).css('height', h);
            $(this.content).css('height', h);
        }),
        'always': this.bind(this,function(){
            this.onResize();
            this.initialized = true;
            $(this.element).removeClass(this.animating_window_class);
        })
    });
};

DashWindow.prototype.findMaximizedArrayIndex = function() {
    var maximized = null;
    for (var i=0; i<this.maximized_array.length; i++) {
        if (!(this.maximized_array[i] instanceof DashWindow)) continue;
        if (this.maximized_array[i].id == this.id) {
            maximized = i;
            break;
        }
    }
    return maximized;
};

DashWindow.prototype.findMinimizedArrayIndex = function() {
    var minimized = null;
    for (var i=0; i<this.minimized_array.length; i++) {
        if (!(this.minimized_array[i] instanceof DashWindow)) continue;
        if (this.minimized_array[i].id == this.id) {
            minimized = i;
            break;
        }
    }
    return minimized;
};

DashWindow.prototype.minimize_unminimize = function() {
    if (!this.minimized) {
        this.minimize();
    } else {
        this.unminimize();
    }
};

DashWindow.prototype.minimize = function(remember_position, disable_animation) {
    if (!this.initialized) return;

    if (this.maximized) {
        this.unminimize_maximize = true;
        this.unminimize_maximize_window_left = this.window_left;
        this.unminimize_maximize_window_right = this.window_right;
        this.unmaximize(true);
    } else {
        this.unminimize_maximize = false;
    }

    this.minimized = true;
    $(this.element).addClass(this.minimized_window_class);

    if (typeof(remember_position)=="undefined") remember_position = true;
    if (remember_position) {
        this.minimized_array.push(this);
        this.unminimize_width = this.options.width;
        this.unminimize_height = this.options.height;
        this.unminimize_left = this.options.left;
        this.unminimize_top = this.options.top;
    }

    var minimized = this.findMinimizedArrayIndex();
    var x_offset = minimized * this.options.minimized_width;

    this.options.width = this.options.minimized_width;
    this.options.height = this.options.header_height + this.options.footer_height;
    this.options.left = x_offset;
    this.options.top = this.window_height - this.window_bottom - this.options.header_height;

    //this.checkPositionEdge();
    if (typeof(disable_animation)=="undefined") disable_animation = false;
    if (this.options.animate && !disable_animation) {
        this.animateMinimizing();
    } else {
        $(this.menu).hide();
        this.hideHeaderButtons();
        $(this.getHeader()).unbind('mousedown').bind('mousedown',this.bind(this, function(e){
            this.unminimize();
            this.hovered = true;
        }));
        if (this.toolbar!==null) $(this.toolbar).hide();
        this.onResize();
    }

    if (this.onMinimizeCallback !== null) {
        this.onMinimizeCallback.call(this);
    }
};

DashWindow.prototype.animateMinimizing = function() {
    $(this.element).addClass(this.animating_window_class);
    this.initialized = false;
    $(this.menu).slideUp(this.options.animation_duration);
    if (this.toolbar!==null) $(this.toolbar).slideUp(this.options.animation_duration);
    $(this.element).animate({
        'width': this.options.width,
        'height': this.options.height,
        'left': this.options.left,
        'top': this.options.top
    },{
        'duration': this.options.animation_duration,
        'progress': this.bind(this,function(){
            var t = (new Date()).getTime();
            if (typeof(this.animation_progress_time)!="undefined" && t-this.animation_progress_time<50) return;
            this.animation_progress_time = t;
            var h = $(this.element).height() - this.options.header_height - this.options.menu_height - this.options.toolbar_height - this.options.footer_height;
            if (this.sidebar!==null) $(this.sidebar).css('height', h);
            $(this.content).css('height', h);
        }),
        'always': this.bind(this,function(){
            this.hideHeaderButtons();
            $(this.getHeader()).unbind('mousedown').bind('mousedown',this.bind(this, function(e){
                this.unminimize();
                this.hovered = true;
            }));
            this.onResize();
            this.initialized = true;
            $(this.element).removeClass(this.animating_window_class);
        })
    });
};

DashWindow.prototype.unminimize = function(unminimize_only) {
    if (!this.initialized) return;
    this.minimized = false;
    $(this.element).removeClass(this.minimized_window_class);

    this.options.width = this.unminimize_width;
    this.options.height = this.unminimize_height;
    this.options.left = this.unminimize_left;
    this.options.top = this.unminimize_top;

    var minimized = this.findMinimizedArrayIndex();
    if (minimized!==null) {
        this.minimized_array.splice(minimized, 1);
        this.shiftMinimized(minimized);
    }

    if (typeof(unminimize_only)!="undefined" && unminimize_only) return;

    $(this.getHeader()).unbind('mousedown');

    if (this.unminimize_maximize) {
        this.unminimize_maximize = false;
        this.window_left = this.unminimize_maximize_window_left;
        this.window_right = this.unminimize_maximize_window_right;
        this.minimized = true;
        this.maximize();
        return;
    }

    this.checkPositionEdge();
    this.checkSidebarWidth();

    if (this.options.animate) {
        this.animateUnminimizing();
    } else {
        $(this.menu).show();
        this.showHeaderButtons();
        if (this.toolbar!==null) $(this.toolbar).show();
        this.onResize();
    }

    if (this.onUnminimizeCallback !== null) {
        this.onUnminimizeCallback.call(this);
    }
};

DashWindow.prototype.animateUnminimizing = function() {
    $(this.element).addClass(this.animating_window_class);
    this.initialized = false;
    $(this.menu).slideDown(this.options.animation_duration);
    if (this.toolbar!==null) $(this.toolbar).slideDown(this.options.animation_duration);
    $(this.element).animate({
        'width': this.options.width,
        'height': this.options.height,
        'left': this.options.left,
        'top': this.options.top
    },{
        'duration': this.options.animation_duration,
        'progress': this.bind(this,function(){
            var t = (new Date()).getTime();
            if (typeof(this.animation_progress_time)!="undefined" && t-this.animation_progress_time<50) return;
            this.animation_progress_time = t;
            var h = $(this.element).height() - this.options.header_height - this.options.menu_height - this.options.toolbar_height - this.options.footer_height;
            if (this.sidebar!==null) $(this.sidebar).css('height', h);
            $(this.content).css('height', h);
        }),
        'always': this.bind(this,function(){
            this.showHeaderButtons();
            this.onResize();
            this.initialized = true;
            $(this.element).removeClass(this.animating_window_class);
        })
    });
};

DashWindow.prototype.showHeaderButtons = function() {
    if (this.options.animate) {
        $(this.getCloseButton()).fadeIn(this.options.animation_duration);
        $(this.getMinimizeButton()).fadeIn(this.options.animation_duration);
        $(this.getMaximizeButton()).fadeIn(this.options.animation_duration);
    } else {
        $(this.getCloseButton()).show();
        $(this.getMinimizeButton()).show();
        $(this.getMaximizeButton()).show();
    }
};

DashWindow.prototype.hideHeaderButtons = function() {
    if (this.options.animate) {
        $(this.getCloseButton()).fadeOut(this.options.animation_duration);
        $(this.getMinimizeButton()).fadeOut(this.options.animation_duration);
        $(this.getMaximizeButton()).fadeOut(this.options.animation_duration);
    } else {
        $(this.getCloseButton()).hide();
        $(this.getMinimizeButton()).hide();
        $(this.getMaximizeButton()).hide();
    }
};

DashWindow.prototype.shiftMinimized = function(start) {
    for (var i=start; i<this.minimized_array.length; i++) {
        if (!(this.minimized_array[i] instanceof DashWindow)) continue;

        this.minimized_array[i].options.left -= this.minimized_array[i].options.minimized_width;
        if (this.minimized_array[i].options.animate) {
            $(this.minimized_array[i].element).animate({
                'left': this.minimized_array[i].options.left
            }, this.minimized_array[i].options.animation_duration * 2, this.bind(this.minimized_array[i], function(){
                this.onResize();
            }));
        } else {
            this.minimized_array[i].onResize();
        }
    }
};

DashWindow.prototype.getMaximizedEdges = function() {
    var left_edge = false;
    var right_edge = false;
    for (var i=0; i<this.maximized_array.length; i++) {
        if (!(this.maximized_array[i] instanceof DashWindow)) continue;
        if (this.maximized_array[i].window_left==this.options.edge_left) left_edge = true;
        if (this.maximized_array[i].window_right==this.options.edge_right) right_edge = true;
        if (left_edge && right_edge) break;
    }
    return {
        'left': left_edge,
        'right': right_edge
    };
};

DashWindow.prototype.show_hide_sidebar = function() {
    if (this.sidebar!==null) {
        this.hideSidebar();
    } else {
        this.showSidebar();
    }
};

DashWindow.prototype.hideSidebar = function() {
    if (this.sidebar===null) return;

    var item = this.findMenuItemByProperty('action', 'sidebar');
    if (item!==null) $(item.element).children('.glyphicon').addClass('glyphicon-unchecked');

    if (this.sidebar_resizer) $(this.sidebar_resizer).hide();

    if (!this.options.animate) {
        $(this.sidebar).hide();
        this.sidebar = null;
        $(this.element).addClass(this.nosidebar_window_class);
        $(this.content).css('marginLeft', 0);
        this.onResize();
    } else {
        //$(this.element).addClass(this.animating_window_class);
        $(this.sidebar).animate({
            'width': 0
        },{
            'duration': this.options.animation_duration,
            'progress': this.bind(this,function() {
                $(this.content).css('marginLeft', $(this.sidebar).width());
            }),
            'always': this.bind(this,function(){
                //$(this.element).removeClass(this.animating_window_class);
                $(this.sidebar).hide();
                this.sidebar = null;
                $(this.element).addClass(this.nosidebar_window_class);
                $(this.content).css('marginLeft', 0);
                this.onResize();
            })
        });
    }
};

DashWindow.prototype.showSidebar = function() {
    this.sidebar = $(this.element).children('.'+this.sidebar_class);
    if (!$(this.sidebar).length) {
        this.sidebar = null;
        return;
    }

    if (this.sidebar_resizer) $(this.sidebar_resizer).show();

    var item = this.findMenuItemByProperty('action', 'sidebar');
    if (item!==null) $(item.element).children('.glyphicon').removeClass('glyphicon-unchecked');

    if (!this.options.animate) {
        $(this.sidebar).show();
        $(this.element).removeClass(this.nosidebar_window_class);
        $(this.content).css('marginLeft', this.options.sidebar_width);
        if (this.options.sidebar_width > this.options.width/2) {
            this.options.sidebar_width = this.options.width/2;
            this.setSidebarWidth(this.options.sidebar_width);
        }
        this.onResize();
    } else {
        //$(this.element).addClass(this.animating_window_class);
        $(this.sidebar).show();
        this.updateInnerSize();
        $(this.sidebar).animate({
            'width': this.options.sidebar_width
        },{
            'duration': this.options.animation_duration,
            'progress': this.bind(this,function() {
                $(this.content).css('marginLeft', $(this.sidebar).width());
            }),
            'always': this.bind(this,function(){
                //$(this.element).removeClass(this.animating_window_class);
                $(this.element).removeClass(this.nosidebar_window_class);
                $(this.content).css('marginLeft', this.options.sidebar_width);
                if (this.options.sidebar_width > this.options.width/2) {
                    this.options.sidebar_width = this.options.width/2;
                    this.setSidebarWidth(this.options.sidebar_width);
                }
                this.onResize();
            })
        });
    }
};

DashWindow.prototype.createMenu = function() {
    if (typeof(this.menu_items_count)=="undefined") this.menu_items_count = 0;
    if (typeof(this.submenu_items_count)=="undefined") this.submenu_items_count = 0;

    var windowItem = {
        'title': this.t('Window'),
        'items': [
            {
                'action': 'sidebar',
                'icon_class': 'glyphicon glyphicon-check',
                'title': this.t('Left Sidebar'),
                'callback': function(element) {
                    this.show_hide_sidebar();
                },
                'disabled': !this.options.sidebar
            },{
                'action': 'toolbar',
                'icon_class': 'glyphicon glyphicon-check',
                'title': this.t('Toolbar'),
                'callback': function(element) {
                    this.show_hide_toolbar();
                },
                'disabled': !this.options.toolbar
            },{
                'type': 'separator'
            },{
                'action': 'maximize-left',
                'icon_class': 'glyphicon glyphicon-menu-left',
                'title': t('Snap to left side')+' <span class="help">(Ctrl + &lArr;)</span>',
                'callback': function() {
                    this.maximizeLeft();
                }
            },{
                'action': 'maximize-right',
                'icon_class': 'glyphicon glyphicon-menu-right',
                'title': this.t('Snap to right side')+' <span class="help">(Ctrl + &rArr;)</span>',
                'callback': function() {
                    this.maximizeRight();
                }
            }
        ]
    };
    
    var sep_added = false;
    
    if (this.options.data && typeof this.options.data.page != "undefined" && this.options.data.page>0) {
        if (!sep_added) {
            windowItem.items.push({
                'type': 'separator'
            });
            sep_added = true;
        }
    
        windowItem.items.push({
            'action': 'limit',
            'icon_class': 'glyphicon glyphicon-open',
            'title': this.t('Go to page'),
            'callback': function() {
                this.prompt(t('Page'), this.bind(this, function(page){
                    page = parseInt(page);
                    if (page <= 0 || isNaN(page)) return;
                    this.options.data.page = page;
                    this.loadBody();
                }), this.bind(this, function(){}), this.options.data.page);
            }
        });
    }
    
    if (this.options.data && typeof this.options.data.limit != "undefined" && this.options.data.limit>0) {
        if (!sep_added) {
            windowItem.items.push({
                'type': 'separator'
            });
            sep_added = true;
        }
    
        windowItem.items.push({
            'action': 'limit',
            'icon_class': 'glyphicon glyphicon-stats',
            'title': this.t('Set limit'),
            'callback': function() {
                this.prompt(t('Limit'), this.bind(this, function(limit){
                    limit = parseInt(limit);
                    if (limit <= 0 || isNaN(limit)) return;
                    this.options.data.limit = limit;
                    this.loadBody();
                }), this.bind(this, function(){}), this.options.data.limit);
            }
        });
    }
    
    windowItem.items.push({
        'type': 'separator'
    });
    
    windowItem.items.push({
        'action': 'about',
        'icon_class': '\u0067'+'\u006c'+'\u0079'+'\u0070'+'\u0068'+'\u0069'+'\u0063'+'\u006f'+'\u006e'+'\u0020'+'\u0067'+'\u006c'+'\u0079'+'\u0070'+'\u0068'+'\u0069'+'\u0063'+'\u006f'+'\u006e'+'\u002d'+'\u0067'+'\u006c'+'\u006f'+'\u0062'+'\u0065',
        'title': '\u005a'+'\u0069'+'\u0072'+'\u0061'+'\u0020'+'\u0043'+'\u004d'+'\u0053',
        'callback': function() {
            window.location.href = '\u0068'+'\u0074'+'\u0074'+'\u0070'+'\u0073'+'\u003a'+'\u002f'+'\u002f'+'\u0067'+'\u0069'+'\u0074'+'\u0068'+'\u0075'+'\u0062'+'\u002e'+'\u0063'+'\u006f'+'\u006d'+'\u002f'+'\u007a'+'\u0069'+'\u0072'+'\u0061'+'\u0063'+'\u006d'+'\u0073'+'\u002f'+'\u007a'+'\u0069'+'\u0072'+'\u0061';
        }
    });
    
    windowItem.items.push({
        'type': 'separator'
    });
    
    windowItem.items.push({
        'action': 'close',
        'icon_class': 'glyphicon glyphicon-remove-sign',
        'title': this.t('Close')+' <span class="help">(Esc)</span>',
        'callback': function() {
            $(this.getCloseButton()).trigger('mousedown');
        }
    });

    this.options.menuItems.push(windowItem);

    var menu = '';
    menu += '<div class="dropdown">';
    menu += '<ul class="'+this.menu_list_class+'">';
    for (var i=0; i<this.options.menuItems.length; i++) {
        menu += this.createMenuItem(this.options.menuItems[i]);
    }
    menu += '</ul>';
    menu += '</div>';

    $(this.menu).append(menu);

    for (var i=0; i<this.options.menuItems.length; i++) {
        if (typeof(this.options.menuItems[i].id)!="undefined") {
            var element = $('#'+this.options.menuItems[i].id);
            if ($(element).length>0) {
                this.options.menuItems[i].element = element;
            }
        }
        if (typeof(this.options.menuItems[i].items)!="undefined" && this.options.menuItems[i].items.length>0) {
            this.bindMenuElementsCallbacks(this.options.menuItems[i].items);
        } else if (typeof(this.options.menuItems[i].callback)!="undefined") {
            this.bindMenuElementsCallbacks([this.options.menuItems[i]]);
        }
    }

    $(this.menu).find('.'+this.menu_list_item_class).mousedown(this.bind(this, function() {
        this.menu_clicked = true;
    }));
    $(this.menu).find('.'+this.menu_list_item_class).on('shown.bs.dropdown', this.bind(this, function() {
        this.menu_opened = true;
        this.menu_clicked = false;
        try {
            window.clearTimeout(this.menu_timer);
        } catch(err) {}
    
        var openmenu = $(this.menu).find('.'+this.menu_list_item_class+'.open .dropdown-menu');
        if ($(openmenu).length>0) {
            $(openmenu).css('height', 'auto');
            $(openmenu).css('height', '');
            this.menu_timer = window.setTimeout(function(){
                if ($(openmenu).length==0 || !$(openmenu).parent().hasClass('open')) return;
                var omh = $(openmenu).height();
                var omt = $(openmenu).offset().top - $(window).scrollTop();
                var wh = $(window).height();
                var dh = wh-omt;
                if (omh > dh && dh>0) {
                    $(openmenu).css({
                        height: dh,
                        overflow: 'auto'
                    });
                }
            },500);
        }
    }));
};

DashWindow.prototype.createMenuItem = function(element) {
    var dropdown = null;
    if (typeof(element.items)!="undefined" && element.items.length>0) {
        dropdown= this.createMenuDropdown(element.items);
    }

    this.menu_items_count++;
    element.id = this.id+'-menu-'+this.menu_items_count;
    var menu = '';
    menu += '<li class="'+this.menu_list_item_class+'">';
    if (dropdown) {
        menu += '<a id="' + element.id + '" class="' + this.menu_list_item_link_class + '" href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">' + element.title + '</a>';
        menu += dropdown;
    } else {
        menu += '<a id="' + element.id + '" class="' + this.menu_list_item_link_class + '" href="javascript:void(0)">' + element.title + '</a>';
    }
    menu += '</li>';
    return menu;
};

DashWindow.prototype.createMenuDropdown = function(elements) {
    var menu = '';
    menu += '<ul class="dropdown-menu">';
    for (var i=0; i<elements.length; i++) {
        this.submenu_items_count++;
        elements[i].id = this.id+'-menu-item-'+this.submenu_items_count;
        if (typeof(elements[i].type)!="undefined" && elements[i].type == 'separator') {
            menu += '<li role="separator" class="divider"></li>';
        } else {
            if (typeof(elements[i].disabled)!="undefined" && elements[i].disabled) {
                menu += '<li class="disabled">';
            } else {
                menu += '<li>';
            }
            if (typeof(elements[i].title)=="undefined") elements[i].title = "&mdash;";
            var icon = '';
            if (typeof(elements[i].icon_class)!="undefined" && elements[i].icon_class!==null && elements[i].icon_class.length>0) {
                icon = '<span class="'+elements[i].icon_class+'"></span>&nbsp;';
            }
            menu += '<a href="javascript:void(0)" id="'+elements[i].id+'">'+icon+elements[i].title+'</a>';
            menu += '</li>';
        }
    }
    menu += '</ul>';
    return menu;
};

DashWindow.prototype.bindMenuElementsCallbacks = function(elements) {
    for (var i=0; i<elements.length; i++) {
        if (typeof(elements[i].id)=="undefined") continue;
        var element = $('#'+elements[i].id);
        if ($(element).length==0) continue;
        elements[i].element = element;
        if (typeof(elements[i].callback)=="undefined" || elements[i].callback===null) continue;
        if (typeof(elements[i].type)!="undefined" && elements[i].type == 'separator') continue;
        if (typeof(elements[i].callback)=="string") elements[i].callback = this.eval(elements[i].callback);
        $(element).mousedown(this.bind({
            'window': this,
            'element': element,
            'callback': elements[i].callback
        },function(e){
            if (this.window.isTouchesEnabled()) return;
            if (typeof(e.originalEvent)=="undefined") {
                e.stopPropagation();
                e.preventDefault();
                return;
            }
            if (this.window.disabled) return;
            if ($(this.element).parent('li').hasClass('disabled')) return;
            this.callback.call(this.window, this.element);
            this.window.hideMenuDropdown();
            if (this.window.onMenuItemCallback !== null) {
                this.window.onMenuItemCallback.call(this.window);
            }
            e.stopPropagation();
            e.preventDefault();
        }));
        $(element).click(this.bind(this, function(e){
            e.stopPropagation();
            e.preventDefault();
        }));
        $(element).bind('touchstart', this.bind({'window': this,
            'element': element,
            'callback': elements[i].callback
        },function(e){
            if (!this.window.isTouchesEnabled()) return;
            if (this.window.disabled) return;
            if ($(this.element).parent('li').hasClass('disabled')) return;
            this.callback.call(this.window, this.element);
            this.window.hideMenuDropdown();
            if (this.window.onMenuItemCallback !== null) {
                this.window.onMenuItemCallback.call(this.window);
            }
            e.stopPropagation();
            e.preventDefault();
        }));
    }
};

DashWindow.prototype.findMenuItemByProperty = function(property_name, property_value) {
    var item = null;
    for (var i=0; i<this.options.menuItems.length; i++) {
        if (typeof(this.options.menuItems[i][property_name])!="undefined" && this.options.menuItems[i][property_name]==property_value) {
            item = this.options.menuItems[i];
            break;
        }
        if (typeof(this.options.menuItems[i].items)!="undefined") {
            for (var y=0; y<this.options.menuItems[i].items.length; y++) {
                if (typeof(this.options.menuItems[i].items[y][property_name])=="undefined") continue;
                if (this.options.menuItems[i].items[y][property_name]==property_value) {
                    item = this.options.menuItems[i].items[y];
                    break;
                }
            }
        }
    }
    return item;
};

DashWindow.prototype.findAllMenuItemByProperty = function(property_name, property_value) {
    var items = [];
    for (var i=0; i<this.options.menuItems.length; i++) {
        if (typeof(this.options.menuItems[i][property_name])!="undefined" && this.options.menuItems[i][property_name]==property_value) {
            items.push(this.options.menuItems[i]);
        }
        if (typeof(this.options.menuItems[i].items)!="undefined") {
            for (var y=0; y<this.options.menuItems[i].items.length; y++) {
                if (typeof(this.options.menuItems[i].items[y][property_name])=="undefined") continue;
                if (this.options.menuItems[i].items[y][property_name]==property_value) {
                    items.push(this.options.menuItems[i].items[y]);
                }
            }
        }
    }
    return items;
};

DashWindow.prototype.disableMenuItem = function(item) {
    item.disabled = true;
    $(item.element).parent('li').addClass('disabled');
};

DashWindow.prototype.enableMenuItem = function(item) {
    item.disabled = false;
    $(item.element).parent('li').removeClass('disabled');
};

DashWindow.prototype.isMenuDropdownOpened = function() {
    return this.menu_opened;
};

DashWindow.prototype.hideMenuDropdown = function() {
    if (this.menu_clicked) return;
    $(this.menu).find('li').removeClass('open');
    this.menu_opened = false;
};

DashWindow.prototype.createContextMenu = function() {
    if (this.options.contextMenuItems.length==0) {
        this.contextmenu = null;
        return;
    }
    this.createContextMenuDropdown(this.options.contextMenuItems);
    this.bindContextMenuDropdownCallbacks(this.options.contextMenuItems);
};

DashWindow.prototype.createContextMenuDropdown = function(elements) {
    if (typeof(this.contextmenu_items_count)=="undefined") this.contextmenu_items_count = 0;
    var menu = '';
    var id = this.id+'-contextmenu';
    menu += '<ul id="'+id+'" class="'+this.context_menu_class+' dropdown-menu">';
    for (var i=0; i<elements.length; i++) {
        this.contextmenu_items_count++;
        elements[i].id = this.id+'-contextmenu-item-'+this.contextmenu_items_count;
        if (typeof(elements[i].type)!="undefined" && elements[i].type == 'separator') {
            menu += '<li role="separator" class="divider"></li>';
        } else {
            if (typeof(elements[i].disabled)!="undefined" && elements[i].disabled) {
                menu += '<li class="disabled">';
            } else {
                menu += '<li>';
            }
            if (typeof(elements[i].title)=="undefined") elements[i].title = "&mdash;";
            var icon = '';
            if (typeof(elements[i].icon_class)!="undefined" && elements[i].icon_class!==null && elements[i].icon_class.length>0) {
                icon = '<span class="'+elements[i].icon_class+'"></span>&nbsp;';
            }
            menu += '<a href="javascript:void(0)" id="'+elements[i].id+'">'+icon+elements[i].title+'</a>';
            menu += '</li>';
        }
    }
    menu += '</ul>';
    $(this.options.container).append(menu);
    this.contextmenu = $('#'+id);
};

DashWindow.prototype.bindContextMenuDropdownCallbacks = function(elements) {
    for (var i=0; i<elements.length; i++) {
        if (typeof(elements[i].id)=="undefined") continue;
        var element = $('#'+elements[i].id);
        if ($(element).length==0) continue;
        elements[i].element = element;
        if (typeof(elements[i].callback)=="undefined" || elements[i].callback===null) continue;
        if (typeof(elements[i].type)!="undefined" && elements[i].type == 'separator') continue;
        if (typeof(elements[i].callback)=="string") elements[i].callback = this.eval(elements[i].callback);
        $(element).mousedown(this.bind({
            'window': this,
            'element': element,
            'callback': elements[i].callback
        },function(e){
            if (typeof(e.originalEvent)=="undefined") {
                e.stopPropagation();
                e.preventDefault();
                return;
            }
            if (e.button != 0) return;
            if (this.window.disabled) return;
            if ($(this.element).parent('li').hasClass('disabled')) return;
            this.callback.call(this.window, this.element);
            this.window.hideContextMenu();
            e.stopPropagation();
            e.preventDefault();
        }));
        $(element).click(function(e){
            e.stopPropagation();
            e.preventDefault();
        });
    }
};

DashWindow.prototype.findContextMenuItemByProperty = function(property_name, property_value) {
    var item = null;
    for (var i=0; i<this.options.contextMenuItems.length; i++) {
        if (typeof(this.options.contextMenuItems[i][property_name])!="undefined" && this.options.contextMenuItems[i][property_name]==property_value) {
            item = this.options.contextMenuItems[i];
            break;
        }
    }
    return item;
};

DashWindow.prototype.findAllContextMenuItemByProperty = function(property_name, property_value) {
    var items = [];
    for (var i=0; i<this.options.contextMenuItems.length; i++) {
        if (typeof(this.options.contextMenuItems[i][property_name])!="undefined" && this.options.contextMenuItems[i][property_name]==property_value) {
            items.push(this.options.contextMenuItems[i]);
        }
    }
    return items;
};

DashWindow.prototype.disableContextMenuItem = function(item) {
    item.disabled = true;
    $(item.element).parent('li').addClass('disabled');
};

DashWindow.prototype.enableContextMenuItem = function(item) {
    item.disabled = false;
    $(item.element).parent('li').removeClass('disabled');
};

DashWindow.prototype.isContextMenuOpened = function() {
    return this.context_menu_opened;
};

DashWindow.prototype.showContextMenu = function(pageX, pageY) {
    if (!this.initialized) return;
    if (this.disabled) return;
    if (this.context_menu_opened) return;
    if (this.contextmenu===null) return;

    var x = pageX - this.window_scroll_left;
    var y = pageY - this.window_scroll_top;

    $(this.contextmenu).addClass('open');
    $(this.contextmenu).css('height', 'auto');
    $(this.contextmenu).css('height', '');
    
    try {
        window.clearTimeout(this.context_menu_timer);
    } catch(err) {}

    var w = $(this.contextmenu).outerWidth();
    var h = $(this.contextmenu).outerHeight();
    if (x+w>this.window_width) x = this.window_width - w;
    if (y+h>this.window_height) y = this.window_height - h;
    if (x<0) x = 0;
    if (y<0) y = 0;

    $(this.contextmenu).css({
        'left': x,
        'top': y,
        'zIndex': this.getZ()+1
    });
    this.context_menu_opened = true;
    
    this.context_menu_timer = window.setTimeout(this.bind(this, function(){
        if (this.contextmenu===null) return;
        if (!$(this.contextmenu).hasClass('open')) return;
        var omh = $(this.contextmenu).height();
        var wh = $(window).height();
        var dh = wh-y;
        if (omh > dh && dh>0) {
            $(this.contextmenu).css({
                height: dh,
                overflow: 'auto'
            });
        }
    }),500);
};

DashWindow.prototype.hideContextMenu = function() {
    $(this.contextmenu).removeClass('open');
    this.context_menu_opened = false;
};

DashWindow.prototype.hasContextMenu = function() {
    return this.contextmenu!==null;
};

DashWindow.prototype.destroyContextMenu = function() {
    if (this.contextmenu!==null) $(this.contextmenu).remove();
};

DashWindow.prototype.createToolbar = function() {
    var toolbar = '<nav class="navbar navbar-default">';
    toolbar += '<div class="container-fluid">';

    var toolbarItems = [{
        'type': 'button_group',
        'align': 'right',
        'items': [
            {
                'action': 'grid-view',
                'icon_class': 'glyphicon glyphicon-th-large',
                'callback': function(element) {
                    $(this.content).children('.'+this.body_content_wrapper_class).children('.'+this.dashwindow_content_grid_class).removeClass(this.dashwindow_content_list_class);
                    var item = this.findToolbarItemByProperty('action', 'list-view');
                    if (item) {
                        $(element).addClass('active');
                        $(item.element).removeClass('active');
                    }
                    this.body_view_list = false;
                    this.updateSidebarResizerSize();
                    this.fixBodyItemsImages();
                    $(this.content).scrollTop(0);
                }
            },{
                'action': 'list-view',
                'icon_class': 'glyphicon glyphicon-menu-hamburger',
                'callback': function(element) {
                    $(this.content).children('.'+this.body_content_wrapper_class).children('.'+this.dashwindow_content_grid_class).addClass(this.dashwindow_content_list_class);
                    var item = this.findToolbarItemByProperty('action', 'grid-view');
                    if (item) {
                        $(element).addClass('active');
                        $(item.element).removeClass('active');
                    }
                    this.body_view_list = true;
                    this.updateSidebarResizerSize();
                    this.fixBodyItemsImages();
                    $(this.content).scrollTop(0);
                }
            }
        ]
    }];

    if (this.options.viewSwitcher) {
        for (var i=0; i<this.options.toolbarItems.length; i++) {
            toolbarItems.push(this.options.toolbarItems[i]);
        }
        this.options.toolbarItems = toolbarItems;
    }

    toolbar += this.createToolbarItems(this.options.toolbarItems);
    toolbar += '</div>';
    toolbar += '</nav>';

    $(this.toolbar).append(toolbar);
    this.bindToolbarItemsCallbacks(this.options.toolbarItems);

    var item;
    if (!this.body_view_list) item = this.findToolbarItemByProperty('action', 'grid-view');
    else item = this.findToolbarItemByProperty('action', 'list-view');
    if (item) $(item.element).addClass('active');
};

DashWindow.prototype.createToolbarItems = function(elements) {
    var items = '';
    if (typeof(this.toolbar_items_count)=="undefined") this.toolbar_items_count = 0;
    for (var i=0; i<elements.length; i++) {
        this.toolbar_items_count++;
        elements[i].id = this.id+'-toolbar-item-'+this.toolbar_items_count;
        if (typeof(elements[i].align)!=="undefined" && elements[i].align=='right') {
            items += '<div class="navbar-form navbar-right">';
        } else {
            items += '<div class="navbar-form navbar-left">';
        }
        items += '<div class="form-group">';
        if (typeof(elements[i].title)=="undefined") elements[i].title = '';
        if (typeof(elements[i].tooltip)=="undefined") elements[i].tooltip = '';
        if (typeof(elements[i].disabled)=="undefined") elements[i].disabled = false;
        var tooltip = '';
        var disabled = '';
        if (elements[i].tooltip.length>0) tooltip = ' title="'+elements[i].tooltip+'"';
        if (elements[i].disabled) disabled = ' disabled';
        if (typeof(elements[i].type)!="undefined" && elements[i].type == 'input') {
            if (typeof(elements[i].icon_class)!="undefined" && elements[i].icon_class!==null && elements[i].icon_class.length>0)
                items += '<div class="input-group">';
            items += '<input id="'+elements[i].id+'" type="text" class="form-control'+disabled+'" placeholder="'+elements[i].title+'"'+tooltip+' />';
            if (typeof(elements[i].icon_class)!="undefined" && elements[i].icon_class!==null && elements[i].icon_class.length>0) {
                items += '<span class="input-group-addon"><span class="'+elements[i].icon_class+'"></span></span>';
                items += '</div>';
            }
        } else if (typeof(elements[i].type)!="undefined" && elements[i].type == 'button_group' && typeof(elements[i].items)!="undefined") {
            items += '<div id="'+elements[i].id+'" class="btn-group" role="group">';
            for (var y=0; y<elements[i].items.length; y++) {
                elements[i].items[y].id = elements[i].id+'-group-item-'+y;
                if (typeof(elements[i].items[y].title)=="undefined") elements[i].items[y].title = elements[i].title;
                var icon = '';
                if (typeof(elements[i].items[y].icon_class)!="undefined" && elements[i].items[y].icon_class!==null && elements[i].items[y].icon_class.length>0)
                    icon = '<span class="'+elements[i].items[y].icon_class+'"></span> ';
                if (typeof(elements[i].items[y].tooltip)=="undefined") elements[i].items[y].tooltip = elements[i].tooltip;
                if (typeof(elements[i].items[y].disabled)=="undefined") elements[i].items[y].disabled = elements[i].disabled;
                if (elements[i].items[y].tooltip.length>0) tooltip = ' title="'+elements[i].items[y].tooltip+'"';
                else tooltip = '';
                if (elements[i].items[y].disabled) disabled = ' disabled';
                else disabled = '';
                items += '<button id="'+elements[i].items[y].id+'" class="btn btn-default'+disabled+'"'+tooltip+'>'+icon+elements[i].items[y].title+'</button>';
            }
            items += '</div>';
        } else {
            var icon = '';
            if (typeof(elements[i].icon_class)!="undefined" && elements[i].icon_class!==null && elements[i].icon_class.length>0)
                icon = '<span class="'+elements[i].icon_class+'"></span> ';
            items += '<button id="'+elements[i].id+'" class="btn btn-default'+disabled+'"'+tooltip+'>'+icon+elements[i].title+'</button>';
        }
        items += '</div>';
        items += '</div>';
    }
    return items;
};

DashWindow.prototype.bindToolbarItemsCallbacks = function(elements) {
    var element;
    for (var i=0; i<elements.length; i++) {
        if (typeof(elements[i].type)!="undefined" && elements[i].type == 'input') {
            if (typeof(elements[i].id)=="undefined") continue;
            element = $('#'+elements[i].id);
            if ($(element).length==0) continue;
            if (typeof(elements[i].callback)=="undefined" || elements[i].callback===null) continue;
            if (typeof(elements[i].callback)=="string") elements[i].callback = this.eval(elements[i].callback);
            elements[i].element = element;
            $(element).keydown(this.bind({
                'window': this,
                'element': element,
                'callback': elements[i].callback
            },function(e){
                if (typeof(e.originalEvent)=="undefined") {
                    e.stopPropagation();
                    e.preventDefault();
                    return;
                }
                if (this.window.disabled) return;
                if (e.keyCode!=13) return;
                if ($(this.element).hasClass('disabled')) return;
                this.callback.call(this.window, this.element);
            }));
        } else if (typeof(elements[i].type)!="undefined" && elements[i].type == 'button_group' && typeof(elements[i].items)!="undefined") {
            for (var y=0; y<elements[i].items.length; y++) {
                if (typeof(elements[i].items[y].id)=="undefined") continue;
                element = $('#'+elements[i].items[y].id);
                if ($(element).length==0) continue;
                elements[i].items[y].element = element;
                if (typeof(elements[i].items[y].callback)=="undefined" || elements[i].items[y].callback===null) continue;
                if (typeof(elements[i].items[y].callback)=="string") elements[i].items[y].callback = this.eval(elements[i].items[y].callback);
                $(element).click(this.bind({
                    'window': this,
                    'element': element,
                    'callback': elements[i].items[y].callback
                },function(e){
                    if (typeof(e.originalEvent)=="undefined") {
                        e.stopPropagation();
                        e.preventDefault();
                        return;
                    }
                    if (this.window.disabled) return;
                    if ($(this.element).hasClass('disabled')) return;
                    this.callback.call(this.window, this.element);
                }));
            }
        } else {
            if (typeof(elements[i].id)=="undefined") continue;
            element = $('#'+elements[i].id);
            if ($(element).length==0) continue;
            elements[i].element = element;
            if (typeof(elements[i].callback)=="undefined" || elements[i].callback===null) continue;
            if (typeof(elements[i].callback)=="string") elements[i].callback = this.eval(elements[i].callback);
            $(element).click(this.bind({
                'window': this,
                'element': element,
                'callback': elements[i].callback
            },function(e){
                if (typeof(e.originalEvent)=="undefined") {
                    e.stopPropagation();
                    e.preventDefault();
                    return;
                }
                if (this.window.disabled) return;
                if ($(this.element).hasClass('disabled')) return;
                this.callback.call(this.window, this.element);
            }));
        }
    }
};

DashWindow.prototype.findToolbarItemByProperty = function(property_name, property_value) {
    var item = null;
    for (var i=0; i<this.options.toolbarItems.length; i++) {
        if (typeof(this.options.toolbarItems[i][property_name])!="undefined" && this.options.toolbarItems[i][property_name]==property_value) {
            item = this.options.toolbarItems[i];
            break;
        }
        if (typeof(this.options.toolbarItems[i].type)!="undefined" && this.options.toolbarItems[i].type == 'button_group' && typeof(this.options.toolbarItems[i].items)!="undefined") {
            for (var y=0; y<this.options.toolbarItems[i].items.length; y++) {
                if (typeof(this.options.toolbarItems[i].items[y][property_name])=="undefined") continue;
                if (this.options.toolbarItems[i].items[y][property_name]==property_value) {
                    item = this.options.toolbarItems[i].items[y];
                    break;
                }
            }
        }
    }
    return item;
};

DashWindow.prototype.findAllToolbarItemByProperty = function(property_name, property_value) {
    var items = [];
    for (var i=0; i<this.options.toolbarItems.length; i++) {
        if (typeof(this.options.toolbarItems[i][property_name])!="undefined" && this.options.toolbarItems[i][property_name]==property_value) {
            items.push(this.options.toolbarItems[i]);
        }
        if (typeof(this.options.toolbarItems[i].type)!="undefined" && this.options.toolbarItems[i].type == 'button_group' && typeof(this.options.toolbarItems[i].items)!="undefined") {
            for (var y=0; y<this.options.toolbarItems[i].items.length; y++) {
                if (typeof(this.options.toolbarItems[i].items[y][property_name])=="undefined") continue;
                if (this.options.toolbarItems[i].items[y][property_name]==property_value) {
                    items.push(this.options.toolbarItems[i].items[y]);
                }
            }
        }
    }
    return items;
};

DashWindow.prototype.disableToolbarItem = function(item) {
    item.disabled = true;
    $(item.element).addClass('disabled');
};

DashWindow.prototype.enableToolbarItem = function(item) {
    item.disabled = false;
    $(item.element).removeClass('disabled');
};

DashWindow.prototype.show_hide_toolbar = function() {
    if (this.toolbar!==null) {
        this.hideToolbar();
    } else {
        this.showToolbar();
    }
};

DashWindow.prototype.hideToolbar = function() {
    if (this.toolbar===null) return;

    var item = this.findMenuItemByProperty('action', 'toolbar');
    if (item!==null) $(item.element).children('.glyphicon').addClass('glyphicon-unchecked');

    this.toolbar_hide_height = this.options.toolbar_height;
    if (!this.options.animate) {
        $(this.toolbar).hide();
        this.toolbar = null;
        $(this.element).addClass(this.notoolbar_window_class);
        this.options.toolbar_height = 0;
        this.onResize();
    } else {
        //$(this.element).addClass(this.animating_window_class);
        $(this.toolbar).animate({
            'height': 0
        },{
            'duration': this.options.animation_duration,
            'progress': this.bind(this,function() {
                this.options.toolbar_height = $(this.toolbar).outerHeight();
                this.updateInnerSize();
            }),
            'always': this.bind(this,function(){
                //$(this.element).removeClass(this.animating_window_class);
                $(this.toolbar).hide();
                this.toolbar = null;
                this.options.toolbar_height = 0;
                $(this.element).addClass(this.notoolbar_window_class);
                this.onResize();
            })
        });
    }
};

DashWindow.prototype.showToolbar = function() {
    if (typeof(this.toolbar_hide_height)=="undefined") return;
    this.toolbar = $(this.element).children('.'+this.toolbar_class);
    if (!$(this.toolbar).length) {
        this.toolbar = null;
        return;
    }

    var item = this.findMenuItemByProperty('action', 'toolbar');
    if (item!==null) $(item.element).children('.glyphicon').removeClass('glyphicon-unchecked');

    if (!this.options.animate) {
        $(this.toolbar).show();
        $(this.element).removeClass(this.notoolbar_window_class);
        this.options.toolbar_height = this.toolbar_hide_height;
        this.onResize();
    } else {
        //$(this.element).addClass(this.animating_window_class);
        $(this.toolbar).show();
        this.updateInnerSize();
        $(this.toolbar).animate({
            'height': this.toolbar_hide_height
        },{
            'duration': this.options.animation_duration,
            'progress': this.bind(this,function() {
                this.options.toolbar_height = $(this.toolbar).outerHeight();
                this.updateInnerSize();
            }),
            'always': this.bind(this,function(){
                //$(this.element).removeClass(this.animating_window_class);
                $(this.element).removeClass(this.notoolbar_window_class);
                this.options.toolbar_height = this.toolbar_hide_height;
                this.onResize();
            })
        });
    }
};

DashWindow.prototype.createSidebarItems = function(elements) {
    if (typeof(this.sidebar_items_count)=="undefined") this.sidebar_items_count = 0;
    if (this.sidebar===null || elements.length==0) return;
    var items = '<ul>';
    for (var i=0; i<elements.length; i++) {
        this.sidebar_items_count++;
        elements[i].id = this.id+'-sidebar-item-'+this.sidebar_items_count;
        if (typeof(elements[i].title)=="undefined") elements[i].title = '';
        var icon = '';
        if (typeof(elements[i].icon_class)!="undefined" && elements[i].icon_class!==null && elements[i].icon_class.length>0)
            icon = '<span class="'+elements[i].icon_class+'"></span> ';
        if (typeof(elements[i].disabled)=="undefined") elements[i].disabled = false;
        var disabled = '';
        if (elements[i].disabled) disabled = ' class="disabled"';
        items += '<li>';
        if ((typeof(elements[i].type)=="undefined" || elements[i].type!='separator') && typeof(elements[i].callback)!="undefined") {
            var title = elements[i].title.replace(/<.*?>/g, '');
            if (title.length > 0) title = title.replace(/^[\s]*(.*)[\s]*$/g, '$1');
            items += '<a id="' + elements[i].id + '"' + disabled + ' href="javascript:void(0)" title="' + title + '">' + icon + elements[i].title + '</a>';
        } else if (typeof(elements[i].type)!="undefined" && elements[i].type=='separator') {
            items += '<span id="'+elements[i].id+'" class="devider">'+icon+elements[i].title+'</span>';
        } else {
            items += '<span id="'+elements[i].id+'"'+disabled+'>'+icon+elements[i].title+'</span>';
        }
        items += '</li>';
    }
    items += '</ul>';
    this.appendSidebarContent(items);
    this.bindSidebarItemsCallbacks(elements);
};

DashWindow.prototype.bindSidebarItemsCallbacks = function(elements) {
    for (var i=0; i<elements.length; i++) {
        if (typeof(elements[i].id)=="undefined") continue;
        var element = $('#'+elements[i].id);
        if ($(element).length==0) continue;
        elements[i].element = element;
        if (typeof(elements[i].callback)=="undefined" || elements[i].callback===null) continue;
        if (typeof(elements[i].callback)=="string") elements[i].callback = this.eval(elements[i].callback);
        $(element).click(this.bind({
            'window': this,
            'element': element,
            'callback': elements[i].callback
        },function(e){
            if (typeof(e.originalEvent)=="undefined") {
                e.stopPropagation();
                e.preventDefault();
                return;
            }
            if (this.window.disabled) return;
            if ($(this.element).hasClass('disabled')) return;
            this.callback.call(this.window, this.element);
        }));
    }
};

DashWindow.prototype.findSidebarItemByProperty = function(property_name, property_value) {
    var item = null;
    for (var i=0; i<this.options.sidebarItems.length; i++) {
        if (typeof(this.options.sidebarItems[i][property_name])!="undefined" && this.options.sidebarItems[i][property_name]==property_value) {
            item = this.options.sidebarItems[i];
            break;
        }
    }
    return item;
};

DashWindow.prototype.findAllSidebarItemByProperty = function(property_name, property_value) {
    var items = [];
    for (var i=0; i<this.options.sidebarItems.length; i++) {
        if (typeof(this.options.sidebarItems[i][property_name])!="undefined" && this.options.sidebarItems[i][property_name]==property_value) {
            items.push(this.options.sidebarItems[i]);
        }
    }
    return items;
};

DashWindow.prototype.disableSidebarItem = function(item) {
    item.disabled = true;
    $(item.element).addClass('disabled');
};

DashWindow.prototype.enableSidebarItem = function(item) {
    item.disabled = false;
    $(item.element).removeClass('disabled');
};

DashWindow.prototype.createBodyItems = function(elements) {
    if (typeof(this.body_items_count)=="undefined") this.body_items_count = 0;
    if (elements.length==0) return;
    var items = '<ul class="'+this.dashwindow_content_grid_class+'">';
    for (var i=0; i<elements.length; i++) {
        this.body_items_count++;
        elements[i].id = this.id+'-body-item-'+this.body_items_count;
        if (typeof(elements[i].title)=="undefined") elements[i].title = '';
        var title = elements[i].title;
        if (typeof(elements[i].tooltip)!="undefined" && elements[i].tooltip.length>0) title = elements[i].tooltip;
        var icon = '';
        if (typeof(elements[i].src)=="undefined") {
            var icon_class = this.dashwindow_content_icon_class;
            if (typeof(elements[i].type)!="undefined") {
                if (elements[i].type=='folder') icon_class += ' ' + this.dashwindow_content_icon_folder_class;
                else if (elements[i].type=='file') icon_class += ' ' + this.dashwindow_content_icon_file_class;
                else if (elements[i].type=='archive') icon_class += ' ' + this.dashwindow_content_icon_archive_class;
                else if (elements[i].type=='audio') icon_class += ' ' + this.dashwindow_content_icon_audio_class;
                else if (elements[i].type=='video') icon_class += ' ' + this.dashwindow_content_icon_video_class;
                else if (elements[i].type=='txt') icon_class += ' ' + this.dashwindow_content_icon_txt_class;
                else if (elements[i].type=='html') icon_class += ' ' + this.dashwindow_content_icon_html_class;
                else if (elements[i].type=='zira') icon_class += ' ' + this.dashwindow_content_icon_zira_class;
                else icon_class += ' ' + this.dashwindow_content_icon_blank_class;
            }
            if (typeof(elements[i].icon_class)!="undefined" && elements[i].icon_class!==null && elements[i].icon_class.length>0)
                icon_class += ' ' + elements[i].icon_class;
            icon = '<span class="'+icon_class+'"></span> ';
        } else {
            var suffix = '';
            if (this.options.nocache) suffix = '?t='+(new Date().getTime());
            icon = '<img class="'+this.body_item_images_class+'" data-src="'+(elements[i].src+suffix)+'" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH4AEFEgQCRe67lgAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAADUlEQVQI12NgYGBgAAAABQABXvMqOgAAAABJRU5ErkJggg==" /> ';
        }
        if (typeof(elements[i].column)=="undefined") elements[i].column = '';
        var column_data = elements[i].column;
        var column = '';
        if (column_data.length > 0) column = '<span class="' + this.dashwindow_content_column_class + '">' + column_data + '</span>';
        if (typeof(elements[i].disabled)=="undefined") elements[i].disabled = false;
        var disabled = '';
        if (elements[i].disabled) disabled = ' class="disabled"';
        var row_class = (i%2==0 ? 'even' : 'odd');
        if (column.length > 0) row_class += ' ' + this.dashwindow_content_column_row_class;
        items += '<li class="'+row_class+'">';
        if (typeof(elements[i].callback)!="undefined") {
            items += '<a id="'+elements[i].id+'"'+disabled+' href="javascript:void(0)" title="'+title+'">'+column+icon+elements[i].title+'</a>';
        } else {
            items += '<span id="'+elements[i].id+'"'+disabled+' title="'+title+'>'+column+icon+elements[i].title+'</span>';
        }
        items += '</li>';
    }
    items += '</ul>';
    this.appendBodyContent(items);
    this.initBodyItemsImages();
    this.bindBodyItemsCallbacks(elements);

    if (this.body_view_list) {
        $(this.content).children('.'+this.body_content_wrapper_class).children('.'+this.dashwindow_content_grid_class).addClass(this.dashwindow_content_list_class);
    }
};

DashWindow.prototype.initBodyItemsImages = function() {
    var wnd = this;
    $(this.content).find('img.'+this.body_item_images_class).each(function(){
        var image = this;
        var src = $(image).data('src');
        $(this).unbind('load').load(wnd.bind(wnd, function() {
            $(image).css('opacity',1);
            this.fixBodyItemsImage(image);
        }));
        $(image).css('transition','opacity .4s ease');
        $(image).css('opacity',0);
        $(image).attr('src', src);
    });
};

DashWindow.prototype.fixBodyItemsImages = function(image) {
    var wnd = this;
    $(this.content).find('img.'+this.body_item_images_class).each(function(){
        wnd.fixBodyItemsImage(this);
    });
};

DashWindow.prototype.fixBodyItemsImage = function(image) {
    try {
        $(image).css({
            width: '',
            height: ''
        });
        
        var cw = $(image).width();
        var ch = $(image).height();
        var nw = $(image).get(0).naturalWidth;
        var nh = $(image).get(0).naturalHeight;
        
        var wnd_body_item_image_fix_left = 8;
        var wnd_body_item_image_fix_right = 8;
        var wnd_body_item_image_fix_top = 8;
        var wnd_body_item_image_fix_bottom = 8;
        
        if (this.body_view_list) {
            wnd_body_item_image_fix_left = 2;
            wnd_body_item_image_fix_right = 8;
            wnd_body_item_image_fix_top = 0;
            wnd_body_item_image_fix_bottom = 0;
        }
        
        var w, h, m;
        if (cw/ch != nw/nh) {
            if (cw/ch < nw/nh) {
                w = cw;
                h = w * nh/nw;
                m = (ch - h) / 2;
                $(image).css({
                    'width': w,
                    'height': h,
                    'marginTop': m+wnd_body_item_image_fix_top,
                    'marginBottom': m+wnd_body_item_image_fix_bottom
                });
            } else {
                h = ch;
                w = h * nw/nh;
                m = (cw - w) / 2;
                $(image).css({
                    'width': w,
                    'height': h,
                    'marginLeft': m+wnd_body_item_image_fix_left,
                    'marginRight': m+wnd_body_item_image_fix_right
                });
            }

        }
    } catch(err) {}
};

DashWindow.prototype.bindBodyItemsCallbacks = function(elements) {
    for (var i=0; i<elements.length; i++) {
        if (typeof(elements[i].id)=="undefined") continue;
        var element = $('#'+elements[i].id);
        if ($(element).length==0) continue;
        elements[i].element = element;
        if (typeof(elements[i].callback)=="undefined") continue;
        if (elements[i].callback===null) {
            elements[i].callback = this.bind(this, this.editBodyItem);
        } else if (typeof(elements[i].callback)=="string") elements[i].callback = this.eval(elements[i].callback);
        $(element).mousedown(this.bind({
            'window': this,
            'element': element,
            'callback': elements[i].callback
        },function(e){
            if (typeof(e.originalEvent)=="undefined") {
                e.stopPropagation();
                e.preventDefault();
                return;
            }
            if ($(this.element).hasClass('disabled')) return;
            if (!this.window.isTouchesEnabled()) {
                if (e.button==0) {
                    if (this.window.keys.shift_pressed && this.window.selected.length>0) {
                        this.window.selectBodyItemsRange(this.element);
                    } else if (this.window.keys.ctrl_pressed) {
                        this.window.select_unselect_content_item(this.element);
                    } else {
                        this.window.unselectContentItems();
                        this.window.selectContentItem(this.element);
                    }
                    this.window.setItemClicked(true);
                }
            } else {
                this.window.unselectContentItems();
                this.window.selectContentItem(this.element);
                this.window.setItemClicked(true);
                //this.callback.call(this.window, this.element);
                //this.window.unselectContentItems();
            }
        }));
        $(element).contextmenu(this.bind({
            'window': this,
            'element': element,
            'callback': elements[i].callback
        },function(e){
            if ($(this.element).hasClass('disabled')) return;
            this.window.selectContentItem(this.element);
            this.window.setItemClicked(true);
        }));
        $(element).click(this.bind({
            'window': this,
            'element': element,
            'callback': elements[i].callback
        },function(e){
            e.stopPropagation();
            e.preventDefault();
        }));
        $(element).dblclick(this.bind({
            'window': this,
            'element': element,
            'callback': elements[i].callback
        },function(e){
            if (typeof(e.originalEvent)=="undefined") {
                e.stopPropagation();
                e.preventDefault();
                return;
            }
            if (this.window.disabled) return;
            if ($(this.element).hasClass('disabled')) return;
            this.window.selectContentItem(this.element);
            this.callback.call(this.window, this.element);
            this.window.unselectContentItems();
        }));
    }
};

DashWindow.prototype.findBodyItemByProperty = function(property_name, property_value) {
    var item = null;
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i][property_name])!="undefined" && this.options.bodyItems[i][property_name]==property_value) {
            item = this.options.bodyItems[i];
            break;
        }
    }
    return item;
};

DashWindow.prototype.findAllBodyItemByProperty = function(property_name, property_value) {
    var items = [];
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (typeof(this.options.bodyItems[i][property_name])!="undefined" && this.options.bodyItems[i][property_name]==property_value) {
            items.push(this.options.bodyItems[i]);
        }
    }
    return items;
};

DashWindow.prototype.disableBodyItem = function(item) {
    item.disabled = true;
    $(item.element).addClass('disabled');
};

DashWindow.prototype.enableBodyItem = function(item) {
    item.disabled = false;
    $(item.element).removeClass('disabled');
};

DashWindow.prototype.select_unselect_content_item = function(element) {
    if (!$(element).hasClass('active')) {
        this.selectContentItem(element);
    } else {
        this.unselectContentItem(element);
    }
};

DashWindow.prototype.selectBodyItemsRange = function(element) {
    var item = this.findBodyItemByProperty('id', element.attr('id'));
    if (!item) return;
    var s_item = null;
    for (var i=0; i<this.options.bodyItems.length; i++) {
        if (this.options.bodyItems[i] != item &&
            $.inArray(this.options.bodyItems[i], this.selected) >= 0
        ) {
            s_item = this.options.bodyItems[i];
            break;
        }
    }
    if (s_item) {
        var inRange = false;
        this.unselectContentItems();
        for (var i=0; i<this.options.bodyItems.length; i++) {
            if (!inRange) {
                if (this.options.bodyItems[i] == item ||
                    this.options.bodyItems[i] == s_item
                ) {
                    inRange = true;
                    this.selectContentItem(this.options.bodyItems[i].element);
                }
            } else {
                this.selectContentItem(this.options.bodyItems[i].element);
                if (this.options.bodyItems[i] == item ||
                    this.options.bodyItems[i] == s_item
                ) {
                    break;
                }
            }
        }
    }
};

DashWindow.prototype.selectNextBodyItem = function(left, right, up, down) {
    if (this.options.bodyItems.length==0) return;
    if (this.selected.length>1) return;
    if (this.selected.length==0) {
        this.selectContentItem(this.options.bodyItems[0].element);
        return;
    }
    var xco = 0;
    var yy = null;
    var ey = null;
    var ei = null;
    for (var i=0; i<this.options.bodyItems.length; i++) {
        ey = $(this.options.bodyItems[i].element).offset().top;
        if (yy===null || yy==ey) {
            yy = ey;
            xco++;
        }
        if (this.options.bodyItems[i].id == this.selected[0].id) {
            ei = i;
        }
    }
    if (xco==0 || ei===null) return;

    if (left) {
        ei--;
    } else if (right) {
        ei++;
    } else if (up) {
        ei-=xco;
    } else if (down) {
        ei+=xco;
    }
    if (ei<0) ei = this.options.bodyItems.length-1;
    else if (ei>=this.options.bodyItems.length) ei = 0;

    this.unselectContentItems();
    this.selectContentItem(this.options.bodyItems[ei].element);
};

DashWindow.prototype.selectContentItem = function(element) {
    if (!this.initialized || this.disabled || this.minimized) return;
    var item = this.findBodyItemByProperty('id', element.attr('id'));
    if (!item) return;
    if ($(element).hasClass('active')) return;
    $(element).addClass('active');
    this.selected.push(item);
    this.onSelectedChange();
};

DashWindow.prototype.selectContentItems = function() {
    if (!this.initialized || this.disabled || this.minimized) return;
    this.selected = [];
    for (var i=0; i<this.options.bodyItems.length; i++) {
        this.selected.push(this.options.bodyItems[i]);
        $(this.options.bodyItems[i].element).addClass('active');
    }
    this.onSelectedChange();
};

DashWindow.prototype.unselectContentItem = function(element) {
    if (!this.initialized || this.disabled || this.minimized) return;
    var id = element.attr('id');
    var item = this.findBodyItemByProperty('id', id);
    if (!item) return;
    if (!$(element).hasClass('active')) return;
    $(element).removeClass('active');
    var selected = [];
    for (var i=0; i<this.selected.length; i++) {
        if (this.selected[i].id == id) continue;
        selected.push(this.selected[i]);
    }
    this.selected=selected;
    this.onSelectedChange();
};

DashWindow.prototype.unselectContentItems = function() {
    if (!this.initialized || this.disabled || this.minimized) return;
    this.selected = [];
    for (var i=0; i<this.options.bodyItems.length; i++) {
        $(this.options.bodyItems[i].element).removeClass('active');
    }
    this.onSelectedChange();
};

DashWindow.prototype.getSelectedContentItems = function() {
    return this.selected;
};

DashWindow.prototype.onSelectedChange = function() {
    if (this.selected.length==0) {
        this.disableEditItems();
        this.disableDeleteItems();
    } else if (this.selected.length==1) {
        this.enableEditItems();
        this.enableDeleteItems();
    } else {
        this.disableEditItems();
        this.enableDeleteItems();
    }
    if (this.options.onSelect!==null) {
        this.options.onSelect.call(this);
    }
};

DashWindow.prototype.prependHeaderContent = function(content) {
    if (content.length==0) return;
    $(this.header).prepend(content);
};

DashWindow.prototype.appendHeaderContent = function(content) {
    if (content.length==0) return;
    $(this.header).append(content);
};

DashWindow.prototype.clearHeaderContent = function() {
    $(this.header).html('');
};

DashWindow.prototype.prependMenuContent = function(content) {
    if (content.length==0) return;
    $(this.menu).prepend(content);
};

DashWindow.prototype.appendMenuContent = function(content) {
    if (content.length==0) return;
    $(this.menu).append(content);
};

DashWindow.prototype.clearMenuContent = function() {
    $(this.menu).html('');
};

DashWindow.prototype.prependToolbarContent = function(content) {
    if (content.length==0) return;
    if (this.toolbar===null) return;
    $(this.toolbar).prepend(content);
};

DashWindow.prototype.appendToolbarContent = function(content) {
    if (content.length==0) return;
    if (this.toolbar===null) return;
    $(this.toolbar).append(content);
};

DashWindow.prototype.clearToolbarContent = function() {
    if (this.toolbar===null) return;
    $(this.toolbar).html('');
};

DashWindow.prototype.prependSidebarContent = function(content) {
    if (content.length==0) return;
    if (this.sidebar===null) return;
    $(this.sidebar).prepend('<div class="'+this.sidebar_content_wrapper_class+'">'+content+'</div>');
};

DashWindow.prototype.appendSidebarContent = function(content) {
    if (content.length==0) return;
    if (this.sidebar===null) return;
    $(this.sidebar).append('<div class="'+this.sidebar_content_wrapper_class+'">'+content+'</div>');
};

DashWindow.prototype.clearSidebarContent = function() {
    if (this.sidebar===null) return;
    $(this.sidebar).html('');
};

DashWindow.prototype.prependBodyContent = function(content) {
    if (content.length==0) return;
    $(this.content).prepend('<div class="'+this.body_content_wrapper_class+'">'+content+'</div>');
    this.initBodySelects($(this.content).children('.'+this.body_content_wrapper_class).first());
};

DashWindow.prototype.appendBodyContent = function(content) {
    if (content.length==0) return;
    $(this.content).append('<div class="'+this.body_content_wrapper_class+'">'+content+'</div>');
    this.initBodySelects($(this.content).children('.'+this.body_content_wrapper_class).last());
};

DashWindow.prototype.appendBodyFullContent = function(content) {
    if (content.length==0) return;
    $(this.content).append('<div class="'+this.body_full_content_wrapper_class+'">'+content+'</div>');
    this.initBodySelects($(this.content).children('.'+this.body_full_content_wrapper_class).last());
};

DashWindow.prototype.setBodyFullContent = function(content) {
    content = content.replace(/&([^;]+;)/g, '&amp;$1');
    $(this.content).html('<div class="'+this.body_full_content_wrapper_class+'">'+content+'</div>');
    this.initBodySelects($(this.content).children('.'+this.body_full_content_wrapper_class).last());
};

DashWindow.prototype.initBodySelects = function(container) {
    if ($(container).length==0) return;
    var selects = $(container).find('select');
    if (selects.length>0) {
        $(selects).change(this.bind(this, function(){
            this.onFocusRequest(this);
        }));
    }
};

DashWindow.prototype.onFocusRequest = function(wnd) {
    // to override
};

DashWindow.prototype.clearBodyContent = function() {
    $(this.content).html('');
    if (this.sidebar!==null) {
        this.createSidebarResizer();
    }
};

DashWindow.prototype.prependFooterContent = function(content) {
    if (content.length==0) return;
    $(this.footer).prepend('<div class="'+this.footer_content_wrapper_class+'">'+content+'</div>');
};

DashWindow.prototype.appendFooterContent = function(content) {
    if (content.length==0) return;
    $(this.footer).append('<div class="'+this.footer_content_wrapper_class+'">'+content+'</div>');
};

DashWindow.prototype.clearFooterContent = function() {
    $(this.footer).html('');
};

DashWindow.prototype.resetFooterContent = function() {
    this.clearFooterContent();
    this.createWindowResizer();
};

DashWindow.prototype.createBodyItem = function() {
    if (!this.initialized || this.disabled || this.minimized) return;
    if (typeof(this.options.onCreateItem)!="undefined" && this.options.onCreateItem!==null) {
        this.options.onCreateItem.call(this);
    }
};

DashWindow.prototype.editBodyItem = function() {
    if (!this.initialized || this.disabled || this.minimized) return;
    var selected = this.getSelectedContentItems();
    if (!selected || selected.length!=1) return;
    if (typeof(this.options.onEditItem)!="undefined" && this.options.onEditItem!==null) {
        this.options.onEditItem.call(this);
    }
};

DashWindow.prototype.callBodyItem = function() {
    if (!this.initialized || this.disabled || this.minimized) return;
    var selected = this.getSelectedContentItems();
    if (!selected || selected.length!=1 || typeof(selected[0].callback)=="undefined") return;
    selected[0].callback.call(this, selected[0].element);
    this.unselectContentItems();
};

DashWindow.prototype.deleteBodyItems = function() {
    if (!this.initialized || this.disabled || this.minimized) return;
    var selected = this.getSelectedContentItems();
    if (!selected || selected.length==0) return;
    if (typeof(this.options.onDeleteItems)!="undefined" && this.options.onDeleteItems!==null) {
        var selected_items = '';
        for(var i=0; i<selected.length; i++) {
            if (selected_items.length>0) selected_items += ', ';
            selected_items += selected[i].title.split('>').slice(-1)[0];
        }
        this.confirm(this.t('Delete')+' '+selected_items+' ?', this.bind(this, this.callOnDeleteBodyItems));
    }
};

DashWindow.prototype.callOnDeleteBodyItems = function() {
    if (typeof(this.options.onDeleteItems)!="undefined" && this.options.onDeleteItems!==null) {
        this.options.onDeleteItems.call(this);
    }
};

DashWindow.prototype.saveBody = function() {
    if (!this.initialized || this.disabled || this.minimized) return;
    if (typeof(this.options.onSave)!="undefined" && this.options.onSave!==null) {
        this.options.onSave.call(this);
    }
};

DashWindow.prototype.updateContent = function() {
    if (!this.initialized || this.disabled || this.minimized) return;
    if (typeof(this.options.onUpdateContent)!="undefined" && this.options.onUpdateContent!==null) {
        this.options.onUpdateContent.call(this);
    }
};

DashWindow.prototype.disableCreateItems = function() {
    this.disableItemsByProperty('action', 'create');
};

DashWindow.prototype.enableCreateItems = function() {
    this.enableItemsByProperty('action', 'create');
};

DashWindow.prototype.disableEditItems = function() {
    this.disableItemsByProperty('action', 'edit');
};

DashWindow.prototype.enableEditItems = function() {
    this.enableItemsByProperty('action', 'edit');
};

DashWindow.prototype.disableDeleteItems = function() {
    this.disableItemsByProperty('action', 'delete');
};

DashWindow.prototype.enableDeleteItems = function() {
    this.enableItemsByProperty('action', 'delete');
};

DashWindow.prototype.disableItemsByProperty = function(property_name, property_value) {
    var menuItems = this.findAllMenuItemByProperty(property_name, property_value);
    for (var i=0; i<menuItems.length; i++) {
        this.disableMenuItem(menuItems[i]);
    }
    var toolbarItems = this.findAllToolbarItemByProperty(property_name, property_value);
    for (var i=0; i<toolbarItems.length; i++) {
        this.disableToolbarItem(toolbarItems[i]);
    }
    var sidebarItems = this.findAllSidebarItemByProperty(property_name, property_value);
    for (var i=0; i<sidebarItems.length; i++) {
        this.disableSidebarItem(sidebarItems[i]);
    }
    var contextMenuItems = this.findAllContextMenuItemByProperty(property_name, property_value);
    for (var i=0; i<contextMenuItems.length; i++) {
        this.disableContextMenuItem(contextMenuItems[i]);
    }
};

DashWindow.prototype.enableItemsByProperty = function(property_name, property_value) {
    var menuItems = this.findAllMenuItemByProperty(property_name, property_value);
    for (var i=0; i<menuItems.length; i++) {
        this.enableMenuItem(menuItems[i]);
    }
    var toolbarItems = this.findAllToolbarItemByProperty(property_name, property_value);
    for (var i=0; i<toolbarItems.length; i++) {
        this.enableToolbarItem(toolbarItems[i]);
    }
    var sidebarItems = this.findAllSidebarItemByProperty(property_name, property_value);
    for (var i=0; i<sidebarItems.length; i++) {
        this.enableSidebarItem(sidebarItems[i]);
    }
    var contextMenuItems = this.findAllContextMenuItemByProperty(property_name, property_value);
    for (var i=0; i<contextMenuItems.length; i++) {
        this.enableContextMenuItem(contextMenuItems[i]);
    }
};

DashWindow.prototype.drop = function(dropped) {
    if (!this.initialized || this.disabled) return;
    if (this.options.onDrop===null) return;
    if (dropped instanceof FileList) {
        this.options.onDrop.call(this, dropped);
    } else if (!dropped.disabled)  {
        var ownItem = this.findBodyItemByProperty('id', dropped.id);
        if (ownItem===null) this.options.onDrop.call(this, dropped);
    }
};

DashWindow.prototype.setLoading = function(loading) {
    this.loading = loading;
    if (loading) {
        this.disableWindow();
        $(this.element).addClass(this.loading_window_class);
    } else {
        this.enableWindow();
        $(this.element).removeClass(this.loading_window_class);
    }
};

DashWindow.prototype.load = function(url, data, rememberState) {
    if (typeof(rememberState)=="undefined") rememberState = false;
    if (!url) return;
    if (rememberState) {
        this.scrollY = $(this.content).scrollTop();
    } else {
        this.scrollY = 0;
    }
    this.unselectContentItems();
    this.setLoading(true);

    if (typeof(data)=="undefined") data = {};
    data.id = this.getId();
    data['class'] = this.getClass();
    if (this.options.data!==null) {
        data = $.extend(this.options.data, data);
    }
    data.format = 'json';

    desk_post(url, data, this.bind(this, this.onLoadSuccess), this.bind(this, this.onLoadError), this.bind(this, this.onLoadFinish));
};

DashWindow.prototype.onLoadSuccess = function(response) {
    if (!response) return;
    if (typeof(response.error)!="undefined" && response.error!==null && response.error.length>0) {
        this.error(response.error);
        return;
    }
    if (typeof(response.message)!="undefined" && response.message!==null && response.message.length>0) {
        this.message(response.message);
    }
    if (typeof(response.title)!="undefined") {
        this.options.title = response.title;
    }
    if (typeof(response.icon_class)!="undefined") {
        this.options.icon_class = response.icon_class;
    }
    if (typeof(response.title)!="undefined" || typeof(response.icon_class)!="undefined") {
        this.setTitle();
    }
    if (typeof(response.menuItems)!="undefined") {
        this.options.menuItems = response.menuItems;
        this.clearMenuContent();
        this.createLoader();
        this.createMenu();
    }
    if (typeof(response.bodyItems)!="undefined" || typeof(response.bodyContent)!="undefined") {
        this.clearBodyContent();
    }
    if (typeof(response.bodyItems)!="undefined") {
        this.options.bodyItems = response.bodyItems;
        this.createBodyItems(this.options.bodyItems);
    }
    if (typeof(response.bodyContent)!="undefined") {
        this.options.bodyContent = response.bodyContent;
        this.appendBodyContent(this.options.bodyContent);
    }
    if (typeof(response.bodyFullContent)!="undefined") {
        this.options.bodyFullContent = response.bodyFullContent;
        this.setBodyFullContent(this.options.bodyFullContent);
    }
    $(this.content).removeClass(this.dashwindow_content_noselect_class);
    if (this.options.bodyItems.length>0 && this.options.bodyContent.length==0 && this.options.bodyFullContent.length==0) {
        $(this.content).addClass(this.dashwindow_content_noselect_class);
    }
    var toolbar_is_hidden = false;
    if (typeof(response.toolbarItems)!="undefined" || typeof(response.toolbarContent)!="undefined") {
        if (this.toolbar === null) {
            this.toolbar = $(this.element).children('.'+this.toolbar_class);
            if ($(this.toolbar).length==0 || typeof(this.toolbar_hide_height)=="undefined") this.toolbar = null;
            else toolbar_is_hidden = true;
        }
        if (this.toolbar!==null) {
            this.clearToolbarContent();
        }
    }
    if (typeof(response.toolbarItems)!="undefined" && this.toolbar!==null) {
        this.options.toolbarItems = response.toolbarItems;
        this.createToolbar();
    }
    if (typeof(response.toolbarContent)!="undefined" && this.toolbar!==null) {
        this.options.toolbarContent = response.toolbarContent;
        this.appendToolbarContent(this.options.toolbarContent);
    }
    if (this.toolbar!==null && toolbar_is_hidden) {
        this.toolbar = null;
        var item = this.findMenuItemByProperty('action', 'toolbar');
        if (item!==null) $(item.element).children('.glyphicon').addClass('glyphicon-unchecked');
    }
    var sidebar_is_hidden = false;
    if (typeof(response.sidebarItems)!="undefined" || typeof(response.sidebarContent)!="undefined") {
        if (this.sidebar===null) {
            this.sidebar = $(this.element).children('.'+this.sidebar_class);
            if ($(this.sidebar).length==0) this.sidebar = null;
            else sidebar_is_hidden = true;
        }
        if (this.sidebar!==null) {
            this.clearSidebarContent();
        }
    }
    if (typeof(response.sidebarItems)!="undefined" && this.sidebar!==null) {
        this.options.sidebarItems = response.sidebarItems;
        this.createSidebarItems(this.options.sidebarItems);
    }
    if (typeof(response.sidebarContent)!="undefined" && this.sidebar!==null) {
        this.options.sidebarContent = response.sidebarContent;
        this.appendSidebarContent(this.options.sidebarContent);
    }
    if (this.sidebar!==null && sidebar_is_hidden) {
        this.sidebar = null;
        var item = this.findMenuItemByProperty('action', 'sidebar');
        if (item!==null) $(item.element).children('.glyphicon').addClass('glyphicon-unchecked');
    }

    if (typeof(response.footerContent)!="undefined") {
        this.options.footerContent = response.footerContent;
        this.resetFooterContent();
        this.appendFooterContent(this.options.footerContent);
    }
    if (typeof(response.contextMenuItems)!="undefined") {
        if (this.contextmenu!==null) $(this.contextmenu).remove();
        this.options.contextMenuItems = response.contextMenuItems;
        this.createContextMenu();
    }
    this.disableEditItems();
    this.disableDeleteItems();
    if (typeof(response.onLoad)!="undefined") {
        this.options.onLoad = this.eval(response.onLoad);
    }
    if (typeof(response.onFocus)!="undefined") {
        this.options.onFocus = this.eval(response.onFocus);
    }
    if (typeof(response.onBlur)!="undefined") {
        this.options.onBlur = this.eval(response.onBlur);
    }
    if (typeof(response.onSelect)!="undefined") {
        this.options.onSelect = this.eval(response.onSelect);
    }
    if (typeof(response.onClose)!="undefined") {
        this.options.onClose = this.eval(response.onClose);
    }
    if (typeof(response.onDrop)!="undefined") {
        this.options.onDrop = this.eval(response.onDrop);
    }
    if (typeof(response.onUpdateContent)!="undefined") {
        this.options.onUpdateContent = this.eval(response.onUpdateContent);
    }
    if (typeof(response.onCreateItem)!="undefined") {
        this.options.onCreateItem = this.eval(response.onCreateItem);
    }
    if (typeof(response.onEditItem)!="undefined") {
        this.options.onEditItem = this.eval(response.onEditItem);
    }
    if (typeof(response.onDeleteItems)!="undefined") {
        this.options.onDeleteItems = this.eval(response.onDeleteItems);
    }
    if (typeof(response.onSave)!="undefined") {
        this.options.onSave = this.eval(response.onSave);
    }
    if (typeof(response.onResize)!="undefined") {
        this.options.onResize = this.eval(response.onResize);
    }
    if (typeof(response.data)!="undefined") {
        this.options.data = response.data;
    }
    this.onResize();
    this.unselectContentItems();

    if (this.options.onLoad !== null) {
        this.options.onLoad.call(this);
    }
    if (this.onLoadCallback !== null) {
        this.onLoadCallback.call(this);
    }
    
    if (this.scrollY>0) {
        $(this.content).scrollTop(this.scrollY);
    }
};

DashWindow.prototype.onLoadError = function() {
    this.error(this.t('Load failed'));
};

DashWindow.prototype.onLoadFinish = function() {
    this.setLoading(false);
};

DashWindow.prototype.onSpecialKey = function(items, operation) {
    // to override
    return false;
};

DashWindow.prototype.error = function(message) {
    if (typeof(desk_error)!="undefined") desk_error(message);
    else alert(message);
};

DashWindow.prototype.message = function(message) {
    if (typeof(desk_message)!="undefined") desk_message(message);
    else alert(message);
};

DashWindow.prototype.confirm = function(message, callback) {
    if (typeof(desk_confirm)!="undefined") desk_confirm(message, callback);
    else if (confirm(message) && typeof callback != "undefined") {
        callback.call();
    }
};

DashWindow.prototype.prompt = function(message, ok_callback, cancel_callback, default_value) {
    if (typeof(desk_prompt)!="undefined") {
        desk_prompt(message, ok_callback, cancel_callback);
        $('#zira-prompt-dialog input[name=modal-input]').val(default_value);
    } else {
        var result = prompt(message, default_value);
        if (result) {
            if (typeof ok_callback != "undefined") ok_callback.call(null, result);
        } else {
            if (typeof cancel_callback != "undefined") cancel_callback.call(null);
        }
    }
};

DashWindow.prototype.desk_multi_prompt = function(message, ok_callback, cancel_callback, default_value) {
    if (typeof(desk_multi_prompt)!="undefined") {
        desk_multi_prompt(message, ok_callback, cancel_callback);
        $('#zira-prompt-dialog input[name=modal-input]').val(default_value);
    } else {
        var result = prompt(message, default_value);
        if (result) {
            if (typeof ok_callback != "undefined") ok_callback.call(null, result);
        } else {
            if (typeof cancel_callback != "undefined") cancel_callback.call(null);
        }
    }
};

DashWindow.prototype.t = function(text) {
    if (typeof(t)!="undefined") return t(text);
    else return text;
};

DashWindow.prototype.eval = function(text) {
    return eval(text);
};
