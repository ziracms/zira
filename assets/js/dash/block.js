var dash_block_load = function() {
    desk_window_form_init(this);
    var ta = $(this.content).find('textarea');
    $(ta).wrap('<div style="border:1px solid #ccc;border-radius:4px;overflow:hidden;width:100%;height:'+$(ta).height()+'px"></div>');
    this.editor = zira_codemirror($(this.content).find('textarea'));
};

var dash_block_update = function() {
    if (typeof(this.editor)=="undefined") return;
    this.editor.save();
};

var dash_block_resize = function() {
    if (typeof(this.editor)=="undefined") return;
    try {
        window.clearTimeout(this.timer);
    } catch(err) {}
    this.timer = window.setTimeout(this.bind(this, function(){
        this.editor.toTextArea();
        this.editor = zira_codemirror($(this.content).find('textarea'));
    }), 500);
};