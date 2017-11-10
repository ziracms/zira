var Desk = {
    'events_element': 'body',
    'dashpanel_id': 'dashpanel-container',
    'body_on_resize_class': 'dashboard-window-resizing',
    'body_on_sidebar_resize_class': 'dashboard-window-sidebar-resizing',
    'dashpanel_overlay_class': 'dashboard-windows-overlay',
    'dashpanel': null,
    'dashpanel_height': 0,
    'dragging': false,
    'dragX': 0,
    'dragY': 0,
    'windows': {},
    'classNames': {},
    'minimized': [],
    'active_windows_count': 0,
    'overlay_is_active': false,
    'active': null,
    'z': 999,
    'initilized': false,
    'touchesEnabled': false,
    'shift_pressed': false,
    'ctrl_pressed': false,
    'alt_pressed': false,
    'keysArr': {
        'ctrl_pressed': false,
        'shift_pressed': false,
        'alt_pressed': false
    },
    'shift_tab_offset': 0,
    'shifted_window': null,
    'sorted_windows': null,
    'sorted_windows_z': {},
    'keys_pressed': 0,
    'key_pressed': null,
    'mouseup_called': false,
    'draggedItem': null,
    'eventsEnabled': true,
    'xhr': XMLHttpRequest,
    'xhrSend': XMLHttpRequest.prototype.send,
    'xhrOpen': XMLHttpRequest.prototype.open,
    'xhrSetRequestHeader': XMLHttpRequest.prototype.setRequestHeader,
    'xhrOverrideMimeType': XMLHttpRequest.prototype.overrideMimeType,
    'formData': typeof(FormData)!="undefined" ? FormData : null,
    'formDataAppend': typeof(FormData)!="undefined" ? FormData.prototype.append : null,
    'parseJSON': $.parseJSON,
    'dockUpdated': false,
    'initialize': function() {
        if (this.isFrame()) return;
        this.dashpanel = $('#'+this.dashpanel_id);
        if ($(this.dashpanel).length>0) {
            this.dashpanel_height = $(this.dashpanel).height();
        }
        $(window).resize(this.bind(this,function(){
            for(var id in this.windows) {
                if (this.windows[id] instanceof DashWindow) {
                    this.windows[id].onWindowResize();
                }
            }
        }));
        $(window).scroll(this.bind(this,function(){
            for(var id in this.windows) {
                if (this.windows[id] instanceof DashWindow) {
                    this.windows[id].onWindowScroll();
                }
            }
        }));
        $(this.events_element).bind('touchstart', this.bind(this, function(e){
            if (typeof(e.originalEvent)=="undefined") {
                e.stopPropagation();
                e.preventDefault();
                return;
            }
            if (!this.touchesEnabled) {
                for(var id in this.windows) {
                    if (this.windows[id] instanceof DashWindow) {
                        this.windows[id].setTouchesEnabled(true);
                    }
                }
                this.touchesEnabled = true;
            }
            e.pageX = e.originalEvent.touches[0].pageX;
            e.pageY = e.originalEvent.touches[0].pageY;
            this.onMouseDown(e);
        }));
        $(this.events_element).bind('touchmove', this.bind(this, function(e){
            if (typeof(e.originalEvent)=="undefined") {
                e.stopPropagation();
                e.preventDefault();
                return;
            }
            e.pageX = e.originalEvent.touches[0].pageX;
            e.pageY = e.originalEvent.touches[0].pageY;
            this.onMouseMove(e);
        }));
        $(this.events_element).bind('touchend', this.bind(this, function(e){
            this.onMouseUp();
        }));
        $(this.events_element).mousedown(this.bind(this, function(e){
            if (typeof(e.originalEvent)=="undefined") {
                e.stopPropagation();
                e.preventDefault();
                return;
            }
            if (this.touchesEnabled) return;
            if (e.button != 0) return;
            this.onMouseDown(e);
        }));
        $(this.events_element).mousemove(this.bind(this, function(e){
            if (this.touchesEnabled) return;
            this.onMouseMove(e);
        }));
        $(this.events_element).mouseup(this.bind(this, function(e){
            if (this.touchesEnabled) return;
            this.onMouseUp();
        }));
        $(this.events_element).mouseleave(this.bind(this,function(e){
            $(this.events_element).trigger('mouseup');
        }));
        $(this.events_element).keydown(this.bind(this,function(e){
            if (typeof(e.originalEvent)=="undefined") {
                e.stopPropagation();
                e.preventDefault();
                return;
            }
            if (this.touchesEnabled) return;
            this.onKeyDown(e);
        }));
        $(this.events_element).keyup(this.bind(this,function(e){
            if (this.touchesEnabled) return;
            this.onKeyUp(e);
        }));
        $(this.events_element).contextmenu(this.bind(this,function(e){
            if (this.touchesEnabled) return;
            this.onContextMenu(e);
        }));
        $(this.events_element).bind('dragstart', this.bind(this, function(e) {
            if (this.touchesEnabled) return;
            this.onDragStart(e);
        }));
        $(this.events_element).bind('dragover', this.bind(this, function(e) {
            if (this.touchesEnabled) return;
            this.onDragOver(e);
        }));
        $(this.events_element).bind('dragleave', this.bind(this, function(e) {
            if (this.touchesEnabled) return;
            this.onDragLeave(e);
        }));
        $(this.events_element).bind('drop',this.bind(this, function(e){
            if (this.touchesEnabled) return;
            this.onDrop(e);
        }));
        $(this.events_element).bind('dragend', this.bind(this, function(e) {
            if (this.touchesEnabled) return;
            this.onDragEnd(e);
        }));
        $(window).blur(this.bind(this,function(e){
            this.keys_pressed = 0;
            $(this.events_element).trigger('keyup');
        }));
        this.initilized = true;
    },
    'bind': function(object, method) {
        return function(arg) {
            return method.call(object,arg);
        }
    },
    'enableEvents': function() {
        this.eventsEnabled = true;
    },
    'disableEvents': function() {
        this.eventsEnabled = false;
    },
    'onMouseDown': function(e) {
        this.mouseup_called = false;
        if (!this.eventsEnabled) return;
        if (e.pageY<this.dashpanel_height) return;
        var current_active = null;
        this.active = null;
        for(var id in this.windows) {
            if (this.windows[id] instanceof DashWindow) {
                if (this.windows[id].isFocused()) current_active = this.windows[id];
                if (this.windows[id].isHovered(e.pageX, e.pageY) && (!this.active || this.windows[id].getZ()>this.active.getZ())) {
                    if (this.active instanceof DashWindow) {
                        this.active.setClicked(false);
                        this.active.setMoving(false);
                        this.active.setWindowResizing(false);
                        this.active.setSidebarResizing(false);
                        this.active.setContentClicked(false);
                    }
                    this.active = this.windows[id];
                    this.windows[id].setClicked(true);
                    if (this.windows[id].isWindowResizerHovered(e.pageX, e.pageY)) {
                        this.windows[id].setMoving(false);
                        this.windows[id].setWindowResizing(true);
                        this.windows[id].setSidebarResizing(false);
                        this.windows[id].setContentClicked(false);
                        this.dragging = true;
                        this.dragX = e.pageX;
                        this.dragY = e.pageY;
                        e.stopPropagation();
                        e.preventDefault();
                        $('body').addClass(this.body_on_resize_class);
                    } else if (this.windows[id].isSidebarResizerHovered(e.pageX, e.pageY)) {
                        this.windows[id].setMoving(false);
                        this.windows[id].setWindowResizing(false);
                        this.windows[id].setSidebarResizing(true);
                        this.windows[id].setContentClicked(false);
                        this.dragging = true;
                        this.dragX = e.pageX;
                        this.dragY = e.pageY;
                        e.stopPropagation();
                        e.preventDefault();
                        $('body').addClass(this.body_on_sidebar_resize_class);
                    } else if (this.windows[id].isMovingHovered(e.pageX, e.pageY)) {
                        this.windows[id].setMoving(true);
                        this.windows[id].setWindowResizing(false);
                        this.windows[id].setSidebarResizing(false);
                        this.windows[id].setContentClicked(false);
                        this.dragging = true;
                        this.dragX = e.pageX;
                        this.dragY = e.pageY;
                        e.stopPropagation();
                        e.preventDefault();
                    } else if (this.windows[id].isContentHovered(e.pageX, e.pageY)) {
                        this.windows[id].setMoving(false);
                        this.windows[id].setWindowResizing(false);
                        this.windows[id].setSidebarResizing(false);
                        this.windows[id].setContentClicked(true);
                    } else {
                        this.windows[id].setMoving(false);
                        this.windows[id].setWindowResizing(false);
                        this.windows[id].setSidebarResizing(false);
                        this.windows[id].setContentClicked(false);
                    }
                } else {
                    this.windows[id].setClicked(false);
                    this.windows[id].setMoving(false);
                    this.windows[id].setWindowResizing(false);
                    this.windows[id].setSidebarResizing(false);
                    this.windows[id].setContentClicked(false);
                }
                this.windows[id].blur(false);
            }
        }
        if ((current_active instanceof DashWindow) && (!(this.active instanceof DashWindow) || current_active.getId()!=this.active.getId())) {
            current_active.blur(true);
        }
        current_active = null;
        if (this.active) {
            this.activateOverlay();
            this.raiseZ(this.active);
            this.active.focus();
        } else {
            this.deactivateOverlay();
        }
    },
    'onMouseMove': function(e) {
        if (this.dragging) {
            var dx = e.pageX - this.dragX;
            var dy = e.pageY - this.dragY;
            if (this.active && (this.active instanceof DashWindow)) {
                if (this.active.isWindowResizing()) {
                    this.active.resize(dx, dy);
                } else if (this.active.isSidebarResizing()) {
                    this.active.resizeSidebar(dx, dy);
                } else if (this.active.isMoving()) {
                    this.active.move(dx, dy);
                    this.active.maximizeOnMove(e.pageX, e.pageY);
                }
            }
            this.dragX += dx;
            this.dragY += dy;
        }
    },
    'onMouseUp': function() {
        if (this.mouseup_called) return;
        this.mouseup_called = true;
        for(var id in this.windows) {
            if (this.windows[id] instanceof DashWindow) {
                if (this.dragging) {
                    this.windows[id].setClicked(false);
                    this.windows[id].setMoving(false);
                    this.windows[id].setWindowResizing(false);
                }
                if (this.windows[id].isContentClicked() && !this.windows[id].isItemClicked()) {
                    this.windows[id].unselectContentItems();
                }
                this.windows[id].setContentClicked(false);
                this.windows[id].setItemClicked(false);
                if (this.windows[id].isContextMenuOpened()) {
                    this.windows[id].hideContextMenu();
                }
                if (this.windows[id].isMenuDropdownOpened()) {
                    this.windows[id].hideMenuDropdown();
                }
                this.windows[id].setHovered(false);
            }
            this.active = null;
            if (this.dragging) {
                $('body').removeClass(this.body_on_resize_class);
                $('body').removeClass(this.body_on_sidebar_resize_class);
            }
        }
        this.dragging = false;
        this.doUpdateDock();
    },
    'onKeyDown': function(e) {
        if (!this.eventsEnabled) return;
        if (e.keyCode == this.key_pressed) return;
        this.keys_pressed++;
        this.key_pressed = e.keyCode;
        if (e.keyCode == 16 && this.keys_pressed==1) {
            this.shift_pressed = true;
            this.keysArr.shift_pressed = true;
        } else if (e.keyCode == 18 && this.keys_pressed==1) {
            this.alt_pressed = true;
            this.keysArr.alt_pressed = true;
        } else if (e.keyCode == 17 && this.keys_pressed==1) {
            this.ctrl_pressed = true;
            this.keysArr.ctrl_pressed = true;
        } else if (e.keyCode == 27 && this.keys_pressed==1) { // esc
            var active = this.findFocusedWindow();
            if (active instanceof DashWindow) {
                e.stopPropagation();
                e.preventDefault();
                $(active.getCloseButton()).trigger('mousedown');
            }
        } else if (e.keyCode == 65 && this.ctrl_pressed && this.keys_pressed==2) { // ctrl+a
            var active = this.findFocusedWindow();
            if (active instanceof DashWindow) {
                e.stopPropagation();
                e.preventDefault();
                active.selectContentItems();
            }
        } else if (e.keyCode == 37 && this.ctrl_pressed && this.keys_pressed==2) { // ctrl+left
            var active = this.findFocusedWindow();
            if (active instanceof DashWindow) {
                e.stopPropagation();
                e.preventDefault();
                active.maximizeLeft();
            }
        } else if (e.keyCode == 39 && this.ctrl_pressed && this.keys_pressed==2) { // ctrl+right
            var active = this.findFocusedWindow();
            if (active instanceof DashWindow) {
                e.stopPropagation();
                e.preventDefault();
                active.maximizeRight();
            }
        } else if (e.keyCode == 38 && this.ctrl_pressed && this.keys_pressed==2) { // ctrl+up
            var active = this.findFocusedWindow();
            if (active instanceof DashWindow) {
                e.stopPropagation();
                e.preventDefault();
                active.maximize_unmaximize();
            }
        } else if (e.keyCode == 40 && this.ctrl_pressed && this.keys_pressed==2) { // ctrl+down
            var active = this.findFocusedWindow();
            if (active instanceof DashWindow) {
                e.stopPropagation();
                e.preventDefault();
                active.minimize_unminimize();
            }
        } else if (e.keyCode == 9 && this.shift_pressed && this.keys_pressed==2) { // shift+tab
            e.stopPropagation();
            e.preventDefault();
            this.shift_tab_offset++;
            if (this.sorted_windows===null) this.sorted_windows = this.getSortedWindowsByZ();
            if (this.shifted_window instanceof DashWindow) this.shifted_window.unhighlightWindow();
            var next = this.shiftWindowFocus(this.shift_tab_offset);
            if (next instanceof DashWindow) {
                this.raiseZ(next);
                next.highlightWindow();
                this.shifted_window = next;
            }
        } else if (e.keyCode == 46 && this.keys_pressed==1) { // delete
            var active = this.findFocusedWindow();
            if (active instanceof DashWindow) {
                active.deleteBodyItems();
            }
        } else if (e.keyCode == 13 && this.keys_pressed==1) { // enter
            var active = this.findFocusedWindow();
            if (active instanceof DashWindow) {
                active.callBodyItem();
            }
        } else if (e.keyCode == 78 && this.ctrl_pressed && this.keys_pressed==2) { // ctrl+n
            var active = this.findFocusedWindow();
            if (active instanceof DashWindow) {
                e.stopPropagation();
                e.preventDefault();
                active.createBodyItem();
            }
        } else if (e.keyCode == 83 && this.ctrl_pressed && this.keys_pressed==2) { // ctrl+s
            var active = this.findFocusedWindow();
            if (active instanceof DashWindow) {
                e.stopPropagation();
                e.preventDefault();
                active.saveBody();
            }
        } else if ((e.keyCode == 37 || e.keyCode == 39 || e.keyCode == 38 || e.keyCode == 40) && this.keys_pressed==1) { // left, right, up, down
            var active = this.findFocusedWindow();
            if (active instanceof DashWindow) {
                active.selectNextBodyItem(e.keyCode == 37, e.keyCode == 39, e.keyCode == 38, e.keyCode == 40);
            }
        }
    },
    'onKeyUp': function(e) {
        this.key_pressed = null;
        this.keys_pressed--;
        if (this.keys_pressed<0) this.keys_pressed=0;
        if (this.keys_pressed==0) {
            this.shift_pressed = false;
            this.ctrl_pressed = false;
            this.alt_pressed = false;
            this.keysArr.shift_pressed = false;
            this.keysArr.alt_pressed = false;
            this.keysArr.ctrl_pressed = false;
            this.shift_tab_offset = 0;
            if (this.shifted_window instanceof DashWindow) {
                this.setShiftedWindowFocus(this.shifted_window);
                if (this.shifted_window!==null) this.shifted_window.unhighlightWindow();
            }
            this.shifted_window = null;
            this.sorted_windows = null;
        }
        this.doUpdateDock();
    },
    'onContextMenu': function(e) {
        if (!this.eventsEnabled) return;
        if (!this.mouseup_called) this.mouseup_called = true;
        var current_active = null;
        this.active = null;
        for(var id in this.windows) {
            if (this.windows[id] instanceof DashWindow) {
                if (this.windows[id].isFocused()) current_active = this.windows[id];
                this.windows[id].setClicked(false);
                this.windows[id].setMoving(false);
                this.windows[id].setWindowResizing(false);
                this.windows[id].setSidebarResizing(false);
                this.windows[id].setContentClicked(false);
                if (this.windows[id].isHovered(e.pageX, e.pageY) && (!this.active || this.windows[id].getZ()>this.active.getZ())) {
                    if (this.active instanceof DashWindow) {
                        this.active.setClicked(false);
                        this.active.setMoving(false);
                        this.active.setWindowResizing(false);
                        this.active.setSidebarResizing(false);
                        this.active.setContentClicked(false);

                    }
                    this.active = this.windows[id];
                    this.windows[id].setClicked(true);
                }
                this.windows[id].blur(false);
            }
        }
        if ((current_active instanceof DashWindow) && (!(this.active instanceof DashWindow) || current_active.getId()!=this.active.getId())) {
            current_active.blur(true);
        }
        current_active = null;
        if (this.active) {
            this.activateOverlay();
            this.raiseZ(this.active);
            this.active.focus();
        } else {
            this.deactivateOverlay();
        }

        if ((this.active instanceof DashWindow) && this.active.hasContextMenu() && !this.active.isDisabled() && !this.active.isMinimized()) {
            e.stopPropagation();
            e.preventDefault();
            if (this.active.isContextMenuOpened()) this.active.hideContextMenu();
            this.active.showContextMenu(e.pageX, e.pageY);
        }
        this.active = null;
        this.doUpdateDock();
    },
    'onDragStart': function(e) {
        if (!this.eventsEnabled) return;
        var target = null;
        if (typeof(e.originalEvent.target)=="undefined") return;
        var tag = e.originalEvent.target.tagName.toLowerCase();
        if (tag!='a') {
            var targetObject = $(e.originalEvent.target).parent('a');
            if ($(targetObject).length>0) target = $(targetObject).get(0);
        } else {
            target = e.originalEvent.target;
        }
        if(!target || typeof(target.id)=="undefined") return;
        var focused = this.findFocusedWindow();
        if (focused instanceof DashWindow) {
            this.draggedItem = focused.findBodyItemByProperty('id', target.id);
        }
    },
    'onDragEnd': function(e) {
        this.draggedItem = null;
    },
    'onDragOver': function(e) {
        if (!this.eventsEnabled) return;
        e.stopPropagation();
        e.preventDefault();
        if (typeof(e.pageX)=="undefined") e.pageX = e.originalEvent.pageX;
        if (typeof(e.pageY)=="undefined") e.pageY = e.originalEvent.pageY;

        var current_active = null;
        this.active = null;
        for(var id in this.windows) {
            if (this.windows[id] instanceof DashWindow) {
                if (this.windows[id].isFocused()) current_active = this.windows[id];
                this.windows[id].setClicked(false);
                this.windows[id].setMoving(false);
                this.windows[id].setWindowResizing(false);
                this.windows[id].setSidebarResizing(false);
                this.windows[id].setContentClicked(false);
                if (this.windows[id].isHovered(e.pageX, e.pageY) && (!this.active || this.windows[id].getZ()>this.active.getZ())) {
                    if (this.active instanceof DashWindow) {
                        this.active.setClicked(false);
                        this.active.setMoving(false);
                        this.active.setWindowResizing(false);
                        this.active.setSidebarResizing(false);
                        this.active.setContentClicked(false);

                    }
                    this.active = this.windows[id];
                    this.windows[id].setClicked(true);
                }
                this.windows[id].blur(false);
                this.windows[id].unhighlightWindow();
            }
        }
        if ((current_active instanceof DashWindow) && (!(this.active instanceof DashWindow) || current_active.getId()!=this.active.getId())) {
            current_active.blur(true);
        }
        current_active = null;
        if (this.active) {
            this.activateOverlay();
            this.raiseZ(this.active);
            this.active.focus();
            this.active.highlightWindow();
        }
        this.active = null;
    },
    'onDragLeave': function(e) {
        for(var id in this.windows) {
            if (this.windows[id] instanceof DashWindow) {
                this.windows[id].unhighlightWindow();
            }
        }
    },
    'onDrop': function(e) {
        if (!this.eventsEnabled) return;
        e.stopPropagation();
        e.preventDefault();
        this.onDragLeave(e);
        var focused = this.findFocusedWindow();
        if ((focused instanceof DashWindow) && !focused.isDisabled() && !focused.isMinimized()) {
            if (this.draggedItem!==null) {
                focused.drop(this.draggedItem);
            } else if (e.originalEvent.dataTransfer.files.length>0) {
                focused.drop(e.originalEvent.dataTransfer.files);
            }
        }
    },
    'openWnd': function(id, className, options) {
        if (!this.initilized) {
            this.initialize();
        }
        if (!this.initilized) return;
        var positions = [];
        for(var _id in this.windows) {
            if (this.windows[_id] instanceof DashWindow) {
                if (this.windows[_id].isFocused() && _id!=id) {
                    this.windows[_id].blur(true);
                } else {
                    this.windows[_id].blur(false);
                }
                positions.push(this.windows[_id].options.top+'-'+this.windows[_id].options.left)
            }
        }
        if (typeof(this.windows[id])!="undefined" && (this.windows[id] instanceof DashWindow)) {
            this.raiseZ(this.windows[id]);
            this.windows[id].focus();
            this.windows[id].blinkWindow();
            if (this.windows[id].isMinimized()) this.windows[id].unminimize();
            return;
        }
        if (typeof(options)=="undefined") options = {};
        if (typeof(options.edge_top)=="undefined") {
            options.edge_top = this.dashpanel_height;
        }
        if (typeof(options.maximize_top_offset)=="undefined") {
            options.maximize_top_offset = this.dashpanel_height;
        }
        this.active_windows_count++;
        this.activateOverlay();
        options.bad_positions = positions;
        this.windows[id] = new DashWindow(id, className, options);
        if (typeof(this.classNames[className])=="undefined") this.classNames[className] = [];
        if ($.inArray(id,this.classNames[className])<0) this.classNames[className].push(id);
        this.raiseZ(this.windows[id]);
        this.windows[id].focus();
        $(this.windows[id].getCloseButton()).mousedown(this.bind(this.windows[id],function(e){
            e.stopPropagation();
            e.preventDefault();
            if (this.isMinimized()) return;
            this.destroy();
            Desk.active_windows_count--;
            Desk.windows[this.getId()] = null;
            Desk.forceUpdateDock();
            if (Desk.active_windows_count<=0) {
                Desk.deactivateOverlay();
                Desk.dock_reset();
            }
        }));
        $(this.windows[id].getMinimizeButton()).mousedown(this.bind(this.windows[id],function(e){
            e.stopPropagation();
            e.preventDefault();
            if (this.isMinimized()) return;
            this.minimize();
            if (this.isMinimized()) {
                this.blur();
            }
            var all_minimized = true;
            for(var id in Desk.windows) {
                if (!(Desk.windows[id] instanceof DashWindow)) continue;
                if (!Desk.windows[id].isMinimized()) {
                    all_minimized = false;
                    break;
                }
            }
            if (all_minimized) Desk.deactivateOverlay();
            Desk.doUpdateDock();
        }));
        $(this.windows[id].getMaximizeButton()).mousedown(this.bind(this.windows[id],function(e){
            e.stopPropagation();
            e.preventDefault();
            if (this.isMinimized()) return;
            this.maximize_unmaximize();
            if (!this.isFocused()) {
                Desk.focusWindow(this);
            } else {
                Desk.doUpdateDock();
            }
        }));
        $(this.windows[id].getHeader()).dblclick(this.bind(this.windows[id],function(e){
            e.stopPropagation();
            e.preventDefault();
            if (this.isMinimized()) return;
            this.maximize_unmaximize();
            Desk.doUpdateDock();
        }));
        if (this.touchesEnabled) {
            this.windows[id].setTouchesEnabled(true);
        }
        if (navigator.userAgent.match(/android/i)) {
            this.windows[id].hideSidebar();
        }
        this.windows[id].setMinimizedArray(this.minimized);
        this.windows[id].setKeysArr(this.keysArr);
        this.windows[id].onFocusCallback = this.bind(this, this.forceUpdateDockFocus);
        this.windows[id].onMaximizeCallback = this.setDockUpdated;
        this.windows[id].onUnmaximizeCallback = this.setDockUpdated;
        this.windows[id].onMinimizeCallback = this.setDockUpdated;
        this.windows[id].onUnminimizeCallback = this.setDockUpdated;
        this.windows[id].onResizeCallback = this.dock_position;
        this.windows[id].onMenuItemCallback = this.bind(this, this.doUpdateDock);
        this.windows[id].onLoadCallback = this.bind(this, this.forceUpdateDock);
        this.forceUpdateDock();
        return this.windows[id];
    },
    'raiseZ': function(window) {
        if (window instanceof DashWindow) {
            window.setZ(this.z);
            this.z++;
        }
        if (this.dashpanel) {
            $(this.dashpanel).css('zIndex', this.z);
        }
    },
    'findFocusedWindow': function() {
        for(var id in this.windows) {
            if (!(this.windows[id] instanceof DashWindow)) continue;
            if (this.windows[id].isFocused()) {
                return this.windows[id];
            }
        }
        return null;
    },
    'findWindowById': function(search) {
        for(var id in this.windows) {
            if (!(this.windows[id] instanceof DashWindow)) continue;
            if (this.windows[id].getId()==search) {
                return this.windows[id];
            }
        }
        return null;
    },
    'getWindowIdsByClass': function(search) {
        if (typeof(this.classNames[search])!="undefined") return this.classNames[search];
        return null;
    },
    'findMaxZWindow' : function(ignore_id) {
        var active = null;
        for(var id in this.windows) {
            if (typeof(ignore_id)!="undefined" && id==ignore_id) continue;
            if (this.windows[id] instanceof DashWindow) {
                if (!active || this.windows[id].getZ()>active.getZ()) {
                    active = this.windows[id];
                }
            }
        }
        return active;
    },
    'getSortedWindowsByZ': function() {
        var z = [];
        for(var id in this.windows) {
            if (!(this.windows[id] instanceof DashWindow)) continue;
            z.push(this.windows[id]);
            this.sorted_windows_z[this.windows[id].getId()] = this.windows[id].getZ();
        }
        if (z.length>0){
            z.sort(function(a, b){
                return a.getZ() - b.getZ();
            });
            z.reverse();
        }
        return z;
    },
    'shiftWindowFocus': function(start) {
        if (this.sorted_windows.length>0){
            if (start>=this.sorted_windows.length) start = start % this.sorted_windows.length;
            return this.sorted_windows[start];
        }
        return null;
    },
    'setShiftedWindowFocus': function(shifted_window) {
        if (!(shifted_window instanceof DashWindow)) return;
        for(var id in this.windows) {
            if (!(this.windows[id] instanceof DashWindow)) continue;
            if (this.windows[id].getId() != shifted_window.getId()) {
                if (this.windows[id].isFocused()) {
                    this.windows[id].blur(true);
                } else {
                    this.windows[id].blur(false);
                }
                if (typeof(this.sorted_windows_z[this.windows[id].getId()])!=="undefined") {
                    this.windows[id].setZ(this.sorted_windows_z[this.windows[id].getId()]);
                }
            }
        }
        shifted_window.focus();
        if (shifted_window.isMinimized()) shifted_window.unminimize();
        this.activateOverlay();
    },
    'activateOverlay': function() {
        if (!this.overlay_is_active) {
            this.overlay_is_active = true;
            $('body').append('<div class="'+this.dashpanel_overlay_class+'"></div>');
            this.dock_open();
        }
    },
    'deactivateOverlay': function() {
        if (this.overlay_is_active) {
            this.overlay_is_active = false;
            $('body').css('overflow','auto');
            $('.'+this.dashpanel_overlay_class).remove();
            this.dock_close();
        }
    },
    'focusWindow' : function(wnd) {
        for(var id in this.windows) {
            if (this.windows[id] instanceof DashWindow) {
                if (this.windows[id].isFocused() && wnd != this.windows[id]) {
                    this.windows[id].blur(true);
                } else {
                    this.windows[id].blur(false);
                }
            }
        }
        this.activateOverlay();
        this.raiseZ(wnd);
        wnd.focus();
        this.doUpdateDock();
    },
    'isFrame': function() {
        return (window!=window.top);
    },
    'setDockUpdated': function() {
        Desk.dockUpdated = true;
    },
    'onDockUpdate': function() {
        this.dock_update();
        this.dock_position();
    },
    'doUpdateDock': function() {
        if (this.dockUpdated) {
            this.onDockUpdate();
            this.dockUpdated = false;
        }
    },
    'forceUpdateDock': function() {
        this.onDockUpdate();
        this.dockUpdated = false;
    },
    'forceUpdateDockFocus': function() {
        this.dock_update_focus();
        this.dock_position();
        this.dockUpdated = false;
    },
    'dock_click': function(wnd) {
        if (wnd instanceof DashWindow) {
            if (wnd.isMinimized()) {
                wnd.unminimize();
            }
            Desk.focusWindow(wnd);
            Desk.doUpdateDock();
        }
    },
    'dock_open': function() {},
    'dock_close': function() {},
    'dock_update': function() {},
    'dock_update_focus': function() {},
    'dock_position': function() {},
    'dock_reset': function() {}
};