(function($) {
    function radians(degrees) {
        return (Math.PI/180)*degrees;
    }
    function drawClock(ctx, canvas_size, unix_timestamp) {
        ctx.clearRect(0, 0, canvas_size, canvas_size);

        var centerX = canvas_size / 2;
        var centerY = canvas_size / 2;
        var offset = canvas_size / 40;
        var radius = Math.min(centerX, centerY) - offset;

        var grd=ctx.createRadialGradient(centerX, centerY,0,centerX, centerY, radius);
        grd.addColorStop(0,"#ffffff");
        grd.addColorStop(1,"#E6E6E8");

        ctx.beginPath();
        ctx.strokeStyle = "#F8F4F4";
        ctx.lineWidth = 1;
        ctx.fillStyle = grd;
        ctx.arc(centerX, centerY, canvas_size / 2 - 2, 0, Math.PI*2, false);
        ctx.closePath();
        ctx.fill();
        ctx.stroke();

        ctx.beginPath();
        ctx.strokeStyle = "#dedede";
        ctx.lineWidth = 1;
        ctx.arc(centerX, centerY, canvas_size / 2 - 1, 0, Math.PI*2, false);
        ctx.closePath();
        ctx.stroke();

        ctx.fillStyle = "#444444";
        ctx.font = "16px Arial";
        for (var i=0; i<12; i++) {
            var a = i * 360 / 12;
            var size = radius - (canvas_size / 12);
            var x = centerX + size * Math.sin(radians(a));
            var y = centerY - size * Math.cos(radians(a));
            var txt = i>0 ? i.toString() : 12;
            var txt_w = ctx.measureText(txt).width;
            ctx.moveTo(x,y);
            ctx.fillText(txt, x - txt_w/2, y+8);
        }

        drawArrows(ctx, canvas_size, unix_timestamp);
    }

    function drawArrows(ctx, canvas_size, unix_timestamp) {
        var centerX = canvas_size / 2;
        var centerY = canvas_size / 2;
        var offset = canvas_size / 40;
        var radius = Math.min(centerX, centerY) - offset;

        var grd=ctx.createRadialGradient(centerX, centerY,0,centerX, centerY, radius);
        grd.addColorStop(0,"#ffffff");
        grd.addColorStop(1,"#E6E6E6");

        ctx.beginPath();
        ctx.fillStyle = grd;
        ctx.arc(centerX, centerY, radius - canvas_size / 7, 0, Math.PI*2, false);
        ctx.closePath();
        ctx.fill();

        var h = unix_timestamp%86400/3600;
        var _h = h % 12;
        var m = unix_timestamp%3600/60;
        var s = unix_timestamp%60;

        var f_h = 9;
        var digitsY = centerY - radius / 3 + f_h;
        if (_h<=3 || _h>=9) digitsY = centerY + radius / 3;

        ctx.font = "12px Arial";
        var t_h = Math.floor(h).toString();
        var t_m = Math.floor(m).toString();
        var t_s = Math.floor(s).toString();
        if (t_h.length==1) t_h = '0'+t_h;
        if (t_m.length==1) t_m = '0'+t_m;
        if (t_s.length==1) t_s = '0'+t_s;
        var txt = t_h+':'+t_m+':'+t_s;
        //var txt_w = ctx.measureText(txt).width;
        var txt_w = 47;
        ctx.fillStyle = "#789789";
        ctx.fillText(txt,centerX - txt_w / 2,digitsY);

        var logoY = centerY - radius / 3 + f_h;
        if (_h>3 && _h<9) logoY = centerY + radius / 3;

        ctx.font = "10px Arial";
        var logo = 'Z'+' '+'i'+' '+'r'+' '+'a';
        var logo_w = ctx.measureText(logo).width;
        ctx.fillStyle = "#789944";
        ctx.fillText(logo,centerX - logo_w / 2,logoY);

        var a1 = _h * 360 / 12;
        var a2 = m * 360 / 60;
        var a3 = s * 360 / 60;

        var offset1 = canvas_size / 3.5;
        var offset2 = canvas_size / 4.5;
        var offset3 = canvas_size / 6;
        var size1 = radius - offset1;
        var size2 = radius - offset2;
        var size3 = radius - offset3;
        var x1 = centerX + size1 * Math.sin(radians(a1));
        var y1 = centerY - size1 * Math.cos(radians(a1));
        var x2 = centerX + size2 * Math.sin(radians(a2));
        var y2 = centerY - size2 * Math.cos(radians(a2));
        var x3 = centerX + size3 * Math.sin(radians(a3));
        var y3 = centerY - size3 * Math.cos(radians(a3));

        ctx.beginPath();
        ctx.strokeStyle = "#777777";
        ctx.lineWidth = canvas_size / 18;
        ctx.lineWidth = 4;
        ctx.lineCap="round";
        ctx.moveTo(centerX, centerY);
        ctx.lineTo(x1, y1);
        ctx.moveTo(centerX, centerY);
        ctx.closePath();
        ctx.stroke();

        ctx.beginPath();
        ctx.strokeStyle = "#98008B";
        ctx.lineWidth = canvas_size / 26;
        ctx.lineWidth = 3;
        ctx.lineCap="round";
        ctx.moveTo(centerX, centerY);
        ctx.lineTo(x2, y2);
        ctx.moveTo(centerX, centerY);
        ctx.closePath();
        ctx.stroke();

        ctx.beginPath();
        ctx.lineWidth = 1;
        ctx.strokeStyle = "#028BEA";
        ctx.moveTo(centerX, centerY);
        ctx.lineTo(x3, y3);
        ctx.moveTo(centerX, centerY);
        ctx.closePath();
        ctx.stroke();

        ctx.beginPath();
        ctx.lineWidth = canvas_size / 75;
        ctx.strokeStyle = "#028BEA";
        ctx.fillStyle = "#A1D4F8";
        ctx.arc(centerX, centerY, radius / 10, 0, Math.PI*2, false);
        ctx.closePath();
        ctx.fill();
        ctx.stroke();
    }

    dashboard_local_clock = function(update) {
        var canvas = $('#dashboard_local_clock').get(0);

        if (typeof(dashboard_local_clock.ctx)=="undefined") {
            if (!canvas.getContext) dashboard_local_clock.ctx = null;
            dashboard_local_clock.ctx = canvas.getContext('2d');
        }
        if (!dashboard_local_clock.ctx) return;

        var canvas_size = Math.min(canvas.width, canvas.height);

        var date = new Date();
        var unix_timestamp = date.getTime() / 1000;
        var timezone_offset = date.getTimezoneOffset() * 60;
        var local_timestamp = Math.floor(unix_timestamp - timezone_offset);

        if (typeof(update)=="undefined" || !update) {
            drawClock(dashboard_local_clock.ctx, canvas_size, local_timestamp);
        } else {
            drawArrows(dashboard_local_clock.ctx, canvas_size, local_timestamp);
        }
    };

    dashboard_remote_clock = function(update) {
        if (typeof(dashboard_remote_clock.start_timestamp)=="undefined") return;
        if (typeof(dashboard_remote_clock.remote_timestamp)=="undefined") return;

        var canvas = $('#dashboard_remote_clock').get(0);

        if (typeof(dashboard_remote_clock.ctx)=="undefined") {
            if (!canvas.getContext) dashboard_remote_clock.ctx = null;
            dashboard_remote_clock.ctx = canvas.getContext('2d');
        }
        if (!dashboard_remote_clock.ctx) return;

        var canvas_size = Math.min(canvas.width, canvas.height);

        var date = new Date();
        var unix_timestamp = Math.floor(date.getTime() / 1000);
        var remote_timestamp = Math.floor(dashboard_remote_clock.remote_timestamp + unix_timestamp - dashboard_remote_clock.start_timestamp);

        if (typeof(update)=="undefined" || !update) {
            drawClock(dashboard_remote_clock.ctx, canvas_size, remote_timestamp);
        } else {
            drawArrows(dashboard_remote_clock.ctx, canvas_size, remote_timestamp);
        }
    };

    dashboard_clock = function() {
        if (typeof(dashboard_clock.update)=="undefined") {
            dashboard_clock.update = false;
        }
        //dashboard_local_clock(dashboard_clock.update);
        dashboard_remote_clock(dashboard_clock.update);
        dashboard_clock.update = true;
    };

    dashboard_notification = function(message, callback) {
        var last = $('.dashboard-notification:last-child');
        $('body').append('<div class="dashboard-notification"><span class="dashboard-notification-close glyphicon glyphicon-remove-sign"></span>'+message+'</div>');

        var t = 60;
        if ($(last).length>0) {
            t = $(last).offset().top + $(last).outerHeight() + 10;
        }

        var dn = $('.dashboard-notification:last-child');
        $(dn).css({
            'top': t,
            'left': ($(window).width() - $(dn).outerWidth()) / 2
            //'left': $(window).width() - $(dn).outerWidth() - $('#dashboard-sidebar').outerWidth() - 20
        }).fadeIn();

        $(dn).click(function() {
            desk_call(callback);
            $(dn).children('.dashboard-notification-close').trigger('click');
        });

        $(dn).children('.dashboard-notification-close').click(function(e){
            e.stopPropagation();
            e.preventDefault();
            $(this).parent('.dashboard-notification').fadeOut(200, function(){
                $(this).remove();
                dashboard_notification_update_position();
            });
        });

        $(window).unbind('resize',dashboard_notification_update_position).resize(dashboard_notification_update_position);
    };

    dashboard_notification_update_position = function() {
        var t = 60;
        $('.dashboard-notification').each(function(){
            $(this).css({
                'top': t,
                'left': ($(window).width() - $('.dashboard-notification').outerWidth()) / 2
            });
            t += $(this).outerHeight() + 10;
        });
    };
    
    dashboard_init_background_setter = function(callback) {
        var setter = $('#dashboard-background-setter');
        if (!$(setter).length) return;
        $(setter).show().tooltip();
        $(setter).click(function(){
            $('body.dashboard #dashboard-canvas-wrapper').removeClass('cover').css('background-image','none');
            if (typeof callback != "undefined") callback.call(null, '');
            desk_file_selector(function(selected){
                if (selected && selected.length>0 && (typeof(selected[0].type)!="undefined" && selected[0].type=='image')) {
                    var src = selected[0].data;
                    var regexp = new RegExp('\\'+desk_ds, 'g');
                    var url = encodeURI(baseUrl(src.replace(regexp,'/')));
                    $('body.dashboard #dashboard-canvas-wrapper').addClass('cover').css('background-image', 'url('+url+')');
                    if (typeof callback != "undefined") callback.call(null, url);
                }
            });
        });
        var bg = $('body.dashboard #dashboard-canvas-wrapper').data('bg');
        if (typeof bg != "undefined" && bg.length>0) {
            var img = new Image();
            img.onload = function() {
                $('body.dashboard #dashboard-canvas-wrapper').addClass('cover').css('background-image', 'url('+img.src+')');
            };
            img.src = bg;
        }
    };

    Dock = {
        'windows': {},
        'windows_co': 0,
        'fontSize': null,
        'z': 999,
        'init': function(){
            $('body').mousemove(function(e){
                if (Dock.windows_co>0 && e.pageX<2 && ($('#dashboard-dock').length == 0 || $('#dashboard-dock').css('display')=='none')) {
                    Dock.show(false);
                    Dock.updateFocus(false);
                    Dock.position(false);
                    for(var id in Dock.windows) {
                        if (!(Dock.windows[id] instanceof DashWindow)) continue;
                        if (Dock.windows[id].z>=Dock.z) {
                            Dock.z = Dock.windows[id].z+1;
                        }
                    }
                }
            });
        },
        'show': function(update_position) {
            if ($('#dashboard-dock').length==0) {
                $('body').append('<div id="dashboard-dock"></div>');
            }
            if (typeof(update_position)=="undefined" || update_position) {
                Dock.position();
            }
        },
        'hide': function() {
            $('#dashboard-dock').remove();
        },
        'update': function(check_focus, append) {
            var has_focused = false;
            if ($('#dashboard-dock').length==0) return;
            $('#dashboard-dock').show();
            $('#dashboard-dock').html('<a href="javascript:void(0)" class="dashboard-dock-item minimize-all" data-toggle="tooltip" data-placement="right" title="'+t('Minimize all')+'"><span class="glyphicon glyphicon-blackboard"></span>');
            if (typeof(append)=="undefined" || append) {
                Dock.reset();
                Dock.z = this.z;
            }
            var co = 0;
            for(var id in this.windows) {
                if (!(this.windows[id] instanceof DashWindow)) continue;
                if (typeof(check_focus)=="undefined" || check_focus) {
                    var focused = false;
                    if (this.windows[id].isFocused() && !has_focused) {
                        has_focused = true;
                        focused = true;
                    }
                    Dock.add(this.windows[id], focused, co);
                } else {
                    Dock.add(this.windows[id], false, co);
                }
                if (typeof(append)=="undefined" || append) {
                    Dock.windows[id]=this.windows[id];
                    Dock.windows_co++;
                }
                co++;
            }
            $('#dashboard-dock a').mousedown(function(e){
                e.stopPropagation();
                e.preventDefault();
                if ($(this).hasClass('minimize-all')) {
                    Dock.minimizeAll();
                    return;
                }
                var wndID = $(this).attr('rel');
                if (typeof(wndID)=="undefined" || !wndID) return;
                Dock.click(desk_get_window(wndID));
            });
            $('#dashboard-dock a').tooltip();
        },
        'updateFocus': function(append) {
            if ($('#dashboard-dock').length==0) return;
            $('#dashboard-dock').show();
            if ($('#dashboard-dock a').length==0) {
                Dock.update(true, false);
                return;
            }
            $('#dashboard-dock a.active').removeClass('active');
            if (typeof(append)=="undefined" || append) {
                Dock.z = this.z;
            }
            for(var id in this.windows) {
                if (!(this.windows[id] instanceof DashWindow)) continue;
                if (this.windows[id].isFocused()) {
                    $('#dashboard-dock a[rel='+id+']').addClass('active');
                    break;
                }
            }
        },
        'add': function(wnd,active,i) {
            if (!(wnd instanceof DashWindow)) throw 'Argument is not a window';
            var icon = wnd.options.icon_class;
            var title = wnd.options.title;
            i = i % 7;
            var className = ' c'+i;
            if (typeof(active) != "undefined" && active) className += ' active';
            $('#dashboard-dock').append('<a href="javascript:void(0)" rel="'+wnd.getId()+'" class="dashboard-dock-item'+className+'" data-toggle="tooltip" data-placement="right" title="'+title+'"><span class="'+icon+'"></span>');
        },
        'position': function(check_maximized) {
            if ($('#dashboard-dock').length==0) return;
            if (typeof(check_maximized)=="undefined" || check_maximized) {
                for (var id in this.windows) {
                    if (!(this.windows[id] instanceof DashWindow)) continue;
                    if (this.windows[id].isMaximized()) {
                        $('#dashboard-dock').hide();
                        return;
                    }
                }
            }
            $('#dashboard-dock').css('zIndex', this.z);

            if (Dock.fontSize===null) {
                var fontSize = $('#dashboard-dock a').css('fontSize');
                if (typeof(fontSize)!="undefined") {
                    Dock.fontSize = parseInt(fontSize);
                }
            }
            if ($('#dashboard-dock a').length>0) {
                Dock.calcPosition();
            }

        },
        'calcPosition': function(r) {
            if (typeof(r)=="undefined") r = 0;

            if (r!=0) {
                var fontSize = parseInt($('#dashboard-dock a').css('fontSize'));
                fontSize -= r*2;
                if (fontSize<=6 || fontSize>Dock.fontSize) {
                    return;
                }
                $('#dashboard-dock a').css('fontSize',fontSize);
            }

            var min_top = 50;
            var wh = $(window).height();
            var dh = $('#dashboard-dock').outerHeight();
            var top = (wh - dh) / 2;
            if (top < min_top) top = min_top;

            $('#dashboard-dock').css('top', top);

            if (top + dh > wh-min_top) {
                Dock.calcPosition(r+1);
            } else if (top + dh < wh-2*min_top) {
                Dock.calcPosition(r-1);
            }
        },
        'click': function(wnd) {
            // overwritten
        },
        'minimizeAll': function() {
            if (Dock.windows_co>0) {
                for(var id in this.windows) {
                    if (!(this.windows[id] instanceof DashWindow)) continue;
                    if (!this.windows[id].isMinimized()) {
                        this.windows[id].getMinimizeButton().trigger('mousedown');
                    }
                }
            }
        },
        'reset': function() {
            Dock.windows = {};
            Dock.windows_co = 0;
        }
    };
})(jQuery);