var dash_console_create = function() {
    this.exec = this.options.data.exec; this.secret = ''; this.hide_input = false; this.hidden_input = ''; this.execs = []; this.exec_index=0; this.captured_prefix=null; this.captured_suffix=null;
    $(this.content).find('#dashboard-console').append('<div id="dashboard-console-line" style="word-wrap: break-word;"><span class="dashboard-console-dir" style="color:#1355E0;white-space: nowrap;">'+t('Initialization')+'</span>&nbsp;<span class="dashboard-console-sign" style="color:#9600FF">...</span>&nbsp;<span class="dashboard-console-cmd-prefix" style="white-space:pre-wrap;"></span><span id="dashboard-console-caret" style="position:absolute;color:#9600FF;visibility:hidden">_</span><span class="dashboard-console-cmd-suffix" style="white-space:pre-wrap;"></span></div>');
    // keydown event
    $(this.content).find('#dashboard-console').keydown(this.bind(this,function(e){
        if (this.isDisabled() || this.isMinimized()) return;
        if (!this.keys.ctrl_pressed && (e.keyCode==37 || e.keyCode==38 || e.keyCode==39 || e.keyCode==40 || e.keyCode==8 || e.keyCode==13)) {
            e.stopPropagation();e.preventDefault();
        }
        var cmd_prefix = $(this.content).find('#dashboard-console-line .dashboard-console-cmd-prefix');
        var cmd_suffix = $(this.content).find('#dashboard-console-line .dashboard-console-cmd-suffix');
        var txt_prefix = $(cmd_prefix).text();
        var txt_suffix = $(cmd_suffix).text();
        if (e.keyCode!=90 && e.keyCode!=17 && e.keyCode!=37 && e.keyCode!=39) {
            this.captured_prefix = null;
            this.captured_suffix = null;
        }
        // ctrl+z
        if (!this.hide_input && e.keyCode==90 && this.keys.ctrl_pressed) {
            if (this.captured_prefix!==null && this.captured_suffix!==null) {
                $(cmd_prefix).html(this.captured_prefix);
                $(cmd_suffix).html(this.captured_suffix);
                this.captured_prefix = null;
                this.captured_suffix = null;
            }
        // arrow left
        } else if (!this.hide_input && e.keyCode==37 && txt_prefix.length>0) {
            var c = txt_prefix.substr(-1);
            $(cmd_suffix).prepend(c);
            $(cmd_prefix).text(txt_prefix.substr(0,txt_prefix.length-1));
        // arrow right
        } else if (!this.hide_input && e.keyCode==39 && txt_suffix.length>0) {
            var c = txt_suffix.substr(0,1);
            $(cmd_prefix).append(c);
            $(cmd_suffix).text(txt_suffix.substr(1));
        // arrow up
        } else if (!this.hide_input && e.keyCode==38 && this.execs.length>0 && this.execs.length>this.exec_index) {
            this.exec_index++;
            $(cmd_prefix).text(this.execs[this.execs.length-this.exec_index]);
            $(cmd_suffix).text('');
        // arrow down
        } else if (!this.hide_input && e.keyCode==40 && this.execs.length>0 && this.exec_index>1) {
            this.exec_index--;
            $(cmd_prefix).text(this.execs[this.execs.length-this.exec_index]);
            $(cmd_suffix).text('');
        // backspace and delete (check selected text first)
        } else if (e.keyCode==8 || e.keyCode==46) {
            try { // delete selected text
                if (this.hide_input) throw 'ignore';
                if (!window.getSelection().isCollapsed){
                    var range = window.getSelection().getRangeAt(0);
                    if ($(range.startContainer).parents('.dashboard-console-cmd-prefix').length>0 || $(range.startContainer).parents('.dashboard-console-cmd-suffix').length>0 || $(range.endContainer).parents('.dashboard-console-cmd-prefix').length>0 || $(range.endContainer).parents('.dashboard-console-cmd-suffix').length>0){
                        var startRange = range.cloneRange();
                        var endRange = range.cloneRange();
                        startRange.collapse(true);
                        endRange.collapse(false);
                        startRange.insertNode($('<span id="dashboard-console-range-marker-start" class="dashboard-console-range-marker"></span>').get(0));
                        endRange.insertNode($('<span id="dashboard-console-range-marker-end" class="dashboard-console-range-marker"></span>').get(0));
                        window.getSelection().removeAllRanges();
                        var marker1 = $(this.content).find('#dashboard-console-line #dashboard-console-range-marker-start');
                        var marker2 = $(this.content).find('#dashboard-console-line #dashboard-console-range-marker-end');
                        if ($(marker1).parents('#dashboard-console-caret').length>0) $(marker1).appendTo(cmd_prefix);
                        if ($(marker2).parents('#dashboard-console-caret').length>0) $(marker2).appendTo(cmd_prefix);
                        if (txt_suffix.length>0){
                            $(cmd_prefix).append($(cmd_suffix).html());
                            $(cmd_suffix).html('');
                            this.exec_index=0;
                        }
                        var p1 = $(cmd_prefix).html().indexOf('<span id="dashboard-console-range-marker-start" class="dashboard-console-range-marker"></span>');
                        if (p1<0) p1=0;
                        $(this.content).find('#dashboard-console-line #dashboard-console-range-marker-start').remove();
                        var p2 = $(cmd_prefix).html().indexOf('<span id="dashboard-console-range-marker-end" class="dashboard-console-range-marker"></span>');
                        $(this.content).find('#dashboard-console-line #dashboard-console-range-marker-end').remove();
                        var html_prefix = $(cmd_prefix).html();
                        if (p2<0) p2 = html_prefix.length;
                        var html1 = html_prefix.substr(0,p1);
                        var html2 = html_prefix.substr(p2);
                        $(cmd_prefix).html(html1+html2);
                    }
                    return;
                }
            } catch(err) {}
            // backspace if text is not selected
            if (e.keyCode==8 && txt_prefix.length>0) $(cmd_prefix).text(txt_prefix.substr(0,txt_prefix.length-1));
            if (this.hide_input && e.keyCode==8 && this.hidden_input.length>0) this.hidden_input=this.hidden_input.substr(0,this.hidden_input.length-1);
            // delete if text is not selected
            else if (e.keyCode==46 && txt_suffix.length>0) $(cmd_suffix).text(txt_suffix.substr(1));
        // enter
        } else if (e.keyCode==13) {
            var dir = '';
            if (typeof(this.options.data.mode)!="undefined" && this.options.data.mode!='sh') dir = this.options.data.mode;
            else if (typeof(this.options.data.cd)!="undefined" && this.options.data.cd!==null) dir = this.options.data.cd;
            var _dir = dir.split('/').slice(-1).toString();
            if (_dir.length==0) _dir='/';
            var sign = '$';
            if (typeof(this.options.data.mode)!="undefined" && this.options.data.mode!='sh') sign = '>';
            if (!this.hide_input) $(this.content).find('#dashboard-console-line').before('<div class="dashboard-console-line" style="word-wrap: break-word;"><span class="dashboard-console-dir" style="color:#1355E0;white-space: nowrap;" title="'+dir+'">'+_dir+'</span>&nbsp;<span class="dashboard-console-sign" style="color:#9600FF">'+sign+'</span>&nbsp;<span class="dashboard-console-cmd" style="white-space:pre-wrap;">'+txt_prefix+txt_suffix+'</span></div>');
            $(cmd_prefix).text('');
            $(cmd_suffix).text('');
            var cons = $(this.content).find('#dashboard-console');
            $(cons).scrollTop($(cons).get(0).scrollHeight);
            if (!this.hide_input) {
                this.options.data.exec=(txt_prefix+txt_suffix);
                this.exec = this.options.data.exec;
                this.options.data.secret = dash_console_encrypt(this.options.data.exec+this.options.data.mode+this.secret);
            } else {
                this.options.data.exec = dash_console_encrypt(this.hidden_input);
                this.options.data.code = 'password';
            }
            if (this.options.data.exec.length>0) {
                $(this.content).find('#dashboard-console #dashboard-console-line').hide();
                if (this.exec_index>0 && this.execs[this.execs.length-1]!=this.options.data.exec) this.exec_index--;
                if (this.exec_index>0) this.execs = this.execs.slice(0,this.execs.length-this.exec_index);
                if (!this.hide_input && (this.execs.length==0 || this.execs[this.execs.length-1]!=this.options.data.exec)) this.execs.push(this.options.data.exec);
                this.exec_index=0;
                this.hidden_input='';
                this.loadBody();
            }
        // ctrl + v
        } else if (this.keys.ctrl_pressed && e.keyCode==86 && $(this.content).find('#dashboard-console #dashboard-console-clipboard').length==0) {
            $(this.content).find('#dashboard-console').append('<input type="text" id="dashboard-console-clipboard" style="opacity:0" />');
            $(this.content).find('#dashboard-console #dashboard-console-clipboard').focus();
        }
    }));
    // keypress event
    $(this.content).find('#dashboard-console').keypress(this.bind(this,function(e){
        if (this.isDisabled() || this.isMinimized()) return;
        if (e.which!=99 && e.which!=118) { e.stopPropagation();e.preventDefault(); }
        if (e.altKey || e.ctrlKey) return;
        var cmd_prefix = $(this.content).find('#dashboard-console-line .dashboard-console-cmd-prefix');
        var cmd_suffix = $(this.content).find('#dashboard-console-line .dashboard-console-cmd-suffix');
        var txt_prefix = $(cmd_prefix).text();
        var txt_suffix = $(cmd_suffix).text();
        if (e.which>0 && e.which!=13 && e.which!=8){
            var c = String.fromCharCode(e.which);
            if (!this.hide_input) $(cmd_prefix).append(c);
            else { this.hidden_input+=c; $(cmd_prefix).append('*'); }
            var cons = $(this.content).find('#dashboard-console');
            $(cons).scrollTop($(cons).get(0).scrollHeight);
        }
    }));
    // paste event
    $(this.content).find('#dashboard-console').bind('paste',this.bind(this,function(e){
        this.captured_prefix = $(this.content).find('#dashboard-console-line .dashboard-console-cmd-prefix').html();
        this.captured_suffix = $(this.content).find('#dashboard-console-line .dashboard-console-cmd-suffix').html();
        var clipboard = $(this.content).find('#dashboard-console #dashboard-console-clipboard');
        if ($(clipboard).length>0){
            try {
                var c = e.originalEvent.clipboardData.getData('text/plain');
                if (!this.hide_input) $(this.content).find('#dashboard-console-line .dashboard-console-cmd-prefix').append(c);
                else { this.hidden_input+=c; $(this.content).find('#dashboard-console-line .dashboard-console-cmd-prefix').append('*'.repeat(c.length));}
                var cons = $(this.content).find('#dashboard-console');
                $(cons).scrollTop($(cons).get(0).scrollHeight);
            } catch(err){}
            $(clipboard).remove();
        }
        $(this.content).find('#dashboard-console').focus();
    }));
};

var dash_console_load = function() {
    if (typeof(this.options.data.code)!="undefined" && this.options.data.code=='secret') {
        this.secret = this.options.data.result;
        this.options.data.exec = this.exec;
        this.options.data.secret = dash_console_encrypt(this.options.data.exec+this.options.data.mode+this.secret);
        this.options.data.result=null;
        this.options.data.code=null;
        this.hidden_input='';
        this.loadBody();
    } else if (typeof(this.options.data.code)!="undefined" && this.options.data.code=='password') {
        $(this.content).find('#dashboard-console #dashboard-console-line').show();
        $(this.content).find('#dashboard-console #dashboard-console-caret').css('visibility','visible');
        this.hide_input = true;
        $(this.content).find('#dashboard-console #dashboard-console-line .dashboard-console-dir').attr('title','').text(t('Password'));
        $(this.content).find('#dashboard-console #dashboard-console-line .dashboard-console-sign').text(':');
    } else {
        $(this.content).find('#dashboard-console #dashboard-console-line').show();
        $(this.content).find('#dashboard-console #dashboard-console-caret').css('visibility','visible');
        this.hide_input = false;
        $(this.content).find('#dashboard-console #dashboard-console-line .dashboard-console-dir').text('');
        $(this.content).find('#dashboard-console #dashboard-console-line .dashboard-console-sign').text('');
        if (typeof(this.options.data.cd)!="undefined" && this.options.data.cd!==null){
            var _dir = this.options.data.cd.split('/').slice(-1).toString();
            if (_dir.length==0) _dir='/';
            $(this.content).find('#dashboard-console #dashboard-console-line .dashboard-console-dir').attr('title',this.options.data.cd).text(_dir);
        }
        if (typeof(this.options.data.result)!="undefined" && this.options.data.result!==null){
            $(this.content).find('#dashboard-console-line').before('<div class="dashboard-console-line"><span class="dashboard-console-result">'+this.options.data.result+'</span></div>');
        }
        if (typeof(this.options.data.mode)!="undefined" && this.options.data.mode!='sh'){
            $(this.content).find('#dashboard-console #dashboard-console-line .dashboard-console-sign').text('>');
            $(this.content).find('#dashboard-console #dashboard-console-line .dashboard-console-dir').text(this.options.data.mode);
            $(this.content).find('#dashboard-console #dashboard-console-line .dashboard-console-dir').attr('title',this.options.data.mode);
        } else {
            $(this.content).find('#dashboard-console #dashboard-console-line .dashboard-console-sign').text('$');
        }
    }
    if (typeof(this.options.data.code)!="undefined" && this.options.data.code=='exit'){
        this.getCloseButton().trigger('click');
    } else {
        var cons = $(this.content).find('#dashboard-console');
        $(cons).scrollTop($(cons).get(0).scrollHeight);
        this.options.data.result=null;
    }
    this.options.data.code=null;
    this.hidden_input='';
};

var dash_console_focus = function() {
    $(this.content).find('#dashboard-console #dashboard-console-caret').css('visibility','visible');
    $(this.content).find('#dashboard-console').focus();
};

var dash_console_blur = function() {
    $(this.content).find('#dashboard-console #dashboard-console-caret').css('visibility','hidden');
    $(this.content).find('#dashboard-console').blur();
};

var dash_console_encrypt = function(str) {
    return md5(encodeURIComponent(str).replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\!/g, '%21').replace(/\*/g, '%2A'));
}