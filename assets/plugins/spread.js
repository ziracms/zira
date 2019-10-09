/**
 * Zira project
 * Spread effect for website
 * (c)2017 https://github.com/ziracms/zira
 * 
 * Usage:
 * 1. $('#container').ziraSpread([options]);
 * 2. $('body').ziraSnow([options]);
 * 3. $('header').ziraSnowStorm([options]);
 * Async call:
 * ZiraSpreadInit= function() {
 *     $('#container').ziraSpread([options]);
 * };
 */
(function($){
    $.fn.ziraSpread = function(options) {
        if ($(this).length==0) return;
        // disabling for IE and mobile
        if (navigator.userAgent.toLowerCase().indexOf('msie')>=0 || typeof(window.orientation) != "undefined") return;
        var target = this;
        var defaults = {
            count: 50,
            template: '<img id="%id" src="%src" width="%width" height="%height" class="zira-spread-plugin-item" style="display:none" />',
            idPrefix: 'zira-spread-plugin-item-',
            fuzzImage: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABmJLR0QAAAAAAAD5Q7t/AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH4QwSDBAKjSESuQAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAD6UlEQVRYw7WZ23LbMAxEAYqS4vz/r3Z0IUX0oaBnvQXtuGkyw5GrRPTR4kIAVfnmj5klEUkion6rX5uImKpe39lf/xFqEpHsawLAvgzW5auKSFFV+zFAM5tFZAE4BEy0X4drAFhF5BSRU1XbfwN0xVaHY8iJVBQwcWMFO6BDHt8GNLNFRD4ciiG7qRMs833bQL0Oefjan/lpfgH34XCriNwcagHImRTsgBIAFn/u9Ofu7mFmu6qWtwDN7OZwN4Ds1xkgUcWRiQsohy92Dy4zkwgyv1DuBqvDrWDqTF+WCLD73wkmPsgt7pFvZqaq9SmgR2o36SeBMuQMwZIozSBgV+9ZUJlD/sJUlIOkuxIIg34Q4EyKoIIV/G+myI9SUl/bSMEFAHlFkJhyUJXmX1rJ93KgHqt9mVnpps6U6zCVrAHsJ/17HQAa+d8iIjsp1yCRV1J78c8PCs6U4xaKXoS8kSug6dBkGBhT4AJ9FYIrZnaqas2unoI/LXRdn6jZ7/VnUcGuCEZtZNLaocgVZhGpGXwxwy9mUpJNHuXIDBZpYF42fRTdSwA3oYknSLgT/WEeQLK5F/CxRpHb1cNTpVAWyASXzSwjYN+IYUfKfgyiWgBwB+DouJuDyghXyu5/ieASQTLoQinpBmd1cqV2iuiTrMJCJIYTkZShqlHwlfQFaFQUIZMrhUpGYGnwfVj4ao5uUpZX2oQ3zBDtN/+8QYrhojaRGPydD/ewPDL4HNWLz35v0eZf3JefD6sZC64GR1Z0vzs+JuTN816BCqbRM416Fhbg4X5W1WZmFkAJbYyJlavkGYJD/XpAeV+oGLgIOHqJJiItB+cig1x0VnJd1/24+X2FJLxDaX/AkVbpRRuB34XKkECjxYd4VBVjYp4o7x0AeZKiJbAGg14IGHVgBRRbgqgUynMJ1ESl9wC0BLB/KYyAhcAKVcJHkMcMnu3mNrp/AiCDIiS7UVXVPyb2QEGlCrWHU1AJc833qtzqcNtA0cj8D/UgqnYEmX8KqmB8Ka73uFnfCZQD6AGyd3h3QFWtZobRuQdwXDJVf5FjAHjRSx+Biju9QIcMe5IjUC3qH1rQDPFLXKTySVG9AegGcAf2xw+A7ovHC7hRMxS1nY3+9gggdzL/8bQvVtUzmPlxqb4G6k0Dpa8g6CLAzdVrLycLqrp7nTjqW+uTVjJSkCdbkT9uXx59OOQGZzT3ESsVsIkad3aHGvgi5sft7eERKDma8fEQaApGwNfAzDx+q98dYOZgPvhqgGlBY44ppzic/cQImGeDmAOVTIyV0f0weKba/xiio+/lN4bo9d2p/z8BDv4bYpiW3p3s489vgE7PvsSVVRsAAAAASUVORK5CYII=',
            snowImage: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACgAAAAoCAYAAACM/rhtAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH4QwUDS8G+OBBRQAABIFJREFUWMPtmF9oVmUcx7/nOJe6TTfNrZZCq2VRF9l1g+lFK4SEIqkIokDLjHQXEXgRQZe5CiwtqGYtsguzmyihC7cIMwXJP4VCdDNpBG0495f5vvucLvod+/H0nvO+m6/lhQ88nPP8/jy/73n+/P4c6Xr7DxpQkpYkyeVx3nuWflXBAeuA08A3wAoHYIGX9YYD3o3AIZujs9oglwFHgMR6n9HfsvFZ4HYn3260BOgxWp/TPwIsrcR2TYUYE0kFN+4CXpe03cZ3SVov6TcbrzeaJHUDU5K6nP6lK141f45sBZ4CZtwqFPi7JcAU8KCTfQiYNh4mm+rNAE/m2fItzgJXKBRSYxuBTZLOSxoKVj+y9wFJhx3vsKR+e4+CnRqS9DuwCdgoScVicd5n75D78qw+DjwQXhKgy3jl9L+eL7gaYLICA6dyXNHpCvQngQVz2mJJiuO4KOnNCr5lJXBHHMfhCq6RtLIC/Z44jmezmFGW7zODCyUtk1QrqVFSkz273A2WpI/jOH42mGOfpGccabekbyWNSrpgz0uSLsZxXEhtzvkWZ2zdTcBxt01/Avc7fgcw7PjHgZasIJB3i/8FCoiA+4AXgY4Q5OzsbDr5FqDoQHxm57YG2O/oRWCz1w0iVIfZWgtEZcECjcB5m3wMuDfra4FjwYHvtO5px7J2xUCNmdwg0FjJ7W0JDJzJic/3BLJnXYhL+905R+VMINuct7Wx9TrgyyBi7LQJo7QnSRIZ7f0cF7I31ANSvZ1BhDkILElxpKtdA6xOkuRhSUss5iaShoOIsQ2IJU3+802X06ihnA35A9gReIsIqJP0fBBhRiRtNdkoSZIp4KsI6Je07hpNRfsjyzQWX6MApyNgq6S3Jc241GqhpDonOCtpIsfZZ+V2YzZfqVYvyYe4SUvp0uNwg6Tu9MAutQiRmMBLkl52yl9IesUZixzwTkl9GSCelvSdA+L135D0mJPdJeldh2E0juOxUle/DbjobtcocGuOWzrnZHE5YgKcy9Frs7m9nbZK/OAngat4IccPvhbI7gJ6AtqrOX5wWyC7rxKAvU7haE6IWu2iQAKMAE3WRxx9DFiVEzJ/dLK9lQBsBQ6Ys74lR+7DYDu3O2e/I9j2D3LmWWW2DgA3z7n+dbxFQDNwG/BEkK385MOUhcuTjj8MPG66zcCi+WDIW9FfgAvWR61I8kXQlhJ6zwVF1pTppvP8DLRWoyZ+r0zKPuRjepDtDJXR3VPOfiUp7GAZfiOwoUTRtMF8a147X43CfY+kaUkN5njbA/5iK86/j+N43MA1SOrOCKG/SvpU0rik3ivdXv9+Z+A+kuCmPuJkHw1ueBK4ozVX4ydSb2DoIDDhxj8A9daPOvpEkF8mwEfV/v0WFkEnLLH9PDDcYgWVp+032RNBkdVRLXC1wDtBhr3ZeCuslkisPqkHGlzFNwgsd0WWz6B3W1lbFYB73cQnA/5yK34a01ts4W4t0BTInvLlAFBbrVVsBwbsbLVm1bLhD8wS1Vyrxd4B/z/xqvz+/b/mud7m2v4CYV6kYm2SFZ4AAAAASUVORK5CYII=',
            type: 'fuzz',
            zIndex: 999999,               // item z-index
            initX: null,                  // set initial x-coord
            initY: null,                  // set initial y-coord
            minSize: 5,                   // range [1..40]
            maxSize: 10,                  // range [1..40]
            minOpacity: .2,               // range [0..1]
            maxOpacity: .4,               // range [0..1]
            minAngle: -45,                // range [-90..90]
            maxAngle: 45,                 // range [-90..90]
            randomizeAngle: false,        // set item angle randomly from range
            points: 10,                   // number of points per item
            splinePoints: 20,             // number of points per spline
            xAxisWidth: 300,              // x-axis width for each item
            randomizeX: true,             // set initX randomly within xAxisWidthRandom
            randomizeY: true,             // set initY randomly within yAxisHeightRandom
            xAxisWidthRandom: 300,        // x-axis width
            yAxisHeightRandom: 300,       // y-axis height
            yGravity: true,               // y-axis direction, if randomizeGravity is false
            randomizeGravity: true,       // set yGravity randomly
            interval: 100,                // update interval
            execTime: 60000,              // infinite if zero
            delay: 2000,                  // start delay time
            createInterval: 500,          // item creation interval
            useAnimationFrame: true,      // call requestAnimationFrame if available, otherwise use setInterval
            lifetime: 0,                  // item lifetime, infinite if zero
            algorythm: 'catmull-rom'      // bezier or catmull-rom
        };
        if (typeof(options)!="undefined") {
            $.extend(defaults, options);
        }
        var items = [], id = '', template = '', src = '', size = 0, opacity = 0, angle = 0, i = 0;;
        if (typeof(defaults[defaults.type+'Image'])!="undefined") {
            src = defaults[defaults.type+'Image']
        }

        window.setTimeout(function(){
            var useAnimationFrame = defaults.useAnimationFrame && typeof(requestAnimationFrame)!="undefined" ? true : false;
            var lastCallTime, lastCreateTime;
            lastCallTime = lastCreateTime = (new Date()).getTime();
            if (defaults.count>1) {
                var dAngle = (defaults.maxAngle - defaults.minAngle) / (defaults.count-1);
            } else {
                var dAngle = defaults.maxAngle - defaults.minAngle;
            }
            function ZiraSpreadLoop(){
                var t = (new Date()).getTime();
                if (i<defaults.count && t-lastCreateTime>=defaults.createInterval) {
                    id = defaults.idPrefix + i;
                    size = Math.round(Math.random() * (defaults.maxSize - defaults.minSize)) + defaults.minSize;
                    opacity = Math.random() * (defaults.maxOpacity - defaults.minOpacity) + defaults.minOpacity;
                    template = defaults.template.replace('%id', id).replace('%src', src).replace('%width', size).replace('%height', size);
                    if (defaults.randomizeAngle) {
                        angle = Math.random()*(defaults.maxAngle - defaults.minAngle) + defaults.minAngle;
                    } else {
                        angle = defaults.minAngle + dAngle * i;
                    }
                    if (defaults.randomizeGravity) {
                        var yGravity = Math.random()>.5 ? true : false;
                    } else {
                        var yGravity = defaults.yGravity;
                    }
                    items.push(new ZiraSpread(target, template, id, size, defaults.zIndex, opacity, defaults.initX, defaults.initY, defaults.points, defaults.splinePoints, defaults.xAxisWidth, defaults.randomizeX, defaults.randomizeY, defaults.xAxisWidthRandom, defaults.yAxisHeightRandom, yGravity, angle, defaults.algorythm, i));
                    i++;
                    lastCreateTime = t;
                }
                for (var y in items) {
                    if (defaults.lifetime && t-items[y].creationTime>defaults.lifetime && !items[y].disabled) {
                        items[y].disableCandidate = true;
                    } else if (defaults.lifetime && t-items[y].creationTime>defaults.lifetime*2 && items[y].disabled) {
                        items[y].enableCandidate = true;
                    }
                    items[y].shift();
                }
            };
            function ZiraSpreadRender() {
                if ((new Date()).getTime()-lastCallTime>=defaults.interval) {
                    ZiraSpreadLoop();
                    lastCallTime = (new Date()).getTime();
                }
                if (typeof(ZiraSpreadRender.stop)!="undefined" && ZiraSpreadRender.stop) return;
                requestAnimationFrame(ZiraSpreadRender);
            };
            if (useAnimationFrame) {
                requestAnimationFrame(ZiraSpreadRender);
            } else {
                var interval = window.setInterval(ZiraSpreadLoop, defaults.interval);
            }
            if (defaults.execTime) {
                window.setTimeout(function(){
                    if (useAnimationFrame) {
                        ZiraSpreadRender.stop = true;
                    } else {
                        window.clearInterval(interval);
                    }
                }, defaults.execTime);
            }
        }, defaults.delay);

        $(window).resize(function(){
            for (var y in items) {
                items[y].destroy();
            }
            items = [];
            i = 0;
        });
    };

    $.fn.ziraSnow = function(options) {
        if ($(this).length==0) return;
        if ($(this).prop('tagName').toLowerCase()!='body') {
            var x = $(this).offset().left + $(this).width()/4;
            var y = $(this).offset().top;
            var w = $(this).width()/2;
        } else {
            var x = $(window).width()/4;
            var y = 0;
            var w = $(window).width()/2;
        }
        var defaults = {
            type: 'snow',
            count: 100,
            initX: x,                     // set initial x-coord
            initY: y,                     // set initial y-coord
            minSize: 5,                   // range [1..40]
            maxSize: 14,                  // range [1..40]
            minOpacity: .3,               // range [0..1]
            maxOpacity: .6,               // range [0..1]
            minAngle: -15,                // range [-90..90]
            maxAngle: 75,                 // range [-90..90]
            randomizeAngle: true,         // set item angle randomly from range
            points: 10,                   // number of points per item
            splinePoints: 15,             // number of points per spline
            xAxisWidth: 100,              // x-axis width for each item
            randomizeX: true,             // set initX randomly within xAxisWidthRandom
            randomizeY: true,             // set initY randomly within yAxisHeightRandom
            xAxisWidthRandom: w,          // x-axis width
            yAxisHeightRandom: 300,       // y-axis height
            yGravity: true,               // y-axis direction, if randomizeGravity is false
            randomizeGravity: false,      // set yGravity randomly
            interval: 100,                // update interval
            execTime: 0,                  // infinite if zero
            delay: 2000,                  // start delay time
            createInterval: 100           // item creation interval
        };
        if (typeof(options)!="undefined") {
            $.extend(defaults, options);
        }
        $(this).ziraSpread(defaults);
    };

    $.fn.ziraSnowStorm = function(options) {
        if ($(this).length==0) return;
        var defaults = {
            count: 100,
            minAngle: -15,                // range [-90..90]
            maxAngle: 85,                 // range [-90..90]
            splinePoints: 8,              // number of points per spline
            xAxisWidth: 300,              // x-axis width for each item
            interval: 50,                 // update interval
            createInterval: 50,           // item creation interval
            algorythm: 'bezier'           // bezier or catmull-rom
        };
        if (typeof(options)!="undefined") {
            $.extend(defaults, options);
        }
        $(this).ziraSnow(defaults);
    };

    ZiraSpread = function(target, template, id, size, zIndex, opacity, initX, initY, pointsCount, splinePointsCount, xAxisWidth, randomizeX, randomizeY, xAxisWidthRandom, yAxisHeightRandom, toBottom, angle, algorythm, index) {
        if (pointsCount<4) throw 'At least 4 points required.';
        if (algorythm!='bezier' && algorythm!='catmull-rom') throw 'Unsupported algorythm.';
        this.target = target;
        this.template = template;
        this.id = id;
        this.size = size;
        this.zIndex = zIndex;
        this.opacity = opacity;
        this.initX = initX;
        this.initY = initY;
        this.resetX = initX;
        this.resetY = initY;
        this.pointsCount = pointsCount;
        this.splinePointsCount = splinePointsCount;
        this.index = index;
        if ($(this.target).prop('tagName').toLowerCase()!='body') {
            this.targetX = $(this.target).offset().left;
            this.targetY = $(this.target).offset().top;
            this.targetW = $(this.target).width();
            this.targetH = $(this.target).height();
        } else {
            this.targetX = 0;
            this.targetY = 0;
            this.targetW = $(window).width();
            this.targetH = $(window).height();
        }
        this.xAxisWidth = xAxisWidth;
        this.randomizeX = randomizeX;
        this.randomizeY = randomizeY;
        this.xAxisWidthRandom = xAxisWidthRandom;
        this.yAxisHeightRandom = yAxisHeightRandom;
        this.yCoef = toBottom ? 1 : -1;
        this.angle = angle;
        this.algorythm = algorythm;

        this.centerX = this.targetX + (this.targetW - this.size) / 2;
        this.centerY = this.targetY + (this.targetH - this.size) / 2;
        
        this.creationTime = (new Date()).getTime();
        this.disableCandidate = false;
        this.enableCandidate = false;
        this.disabled = false;
        
        $('body').append(template);
        this.init();
    };

    ZiraSpread.prototype.init = function(innerCall) {
        this.initX = this.resetX;
        this.initY = this.resetY;
        this.xPoints = [];
        this.yPoints = [];
        this.iteration = 0;
        this.pointIndex = 0;
        this.splinePoints = [];
        if (this.initX!==null && this.initY!==null) {
            this.left = this.initX;
            this.top = this.initY;
        } else {
            this.initX = this.left = this.centerX;
            this.initY = this.top = this.centerY;
        }
        if (this.randomizeX) {
            this.initX = this.left = Math.random() * (this.xAxisWidthRandom) - this.xAxisWidthRandom / 2 + this.initX;
        }
        if (this.randomizeY) {
            this.initY = this.top = Math.random() * (this.yAxisHeightRandom) - this.yAxisHeightRandom / 2 + this.initY;
        }
        if (this.yCoef>0) {
            var dy = this.targetY + this.targetH - this.initY;
        } else {
            var dy = this.initY - this.targetY;
        }
        dy += 100; // let it go outside container
        var dyd = dy / this.pointsCount;
        var prevY = this.initY, nextY = 0;
        var prevX = null;
        if (this.yCoef>0) {
            this.addPoint(null, this.initY-dyd, this.initY);
        } else {
            this.addPoint(null, this.initY+dyd, this.initY);
        }
        for (var i=0; i<=this.pointsCount; i++) {
            nextY = this.initY + this.yCoef * dyd * i;
            prevX = this.addPoint(prevX, prevY, nextY);
            prevY = nextY;
        }
        if (typeof(innerCall)=="undefined" || !innerCall) {
            if ($(this.target).prop('tagName').toLowerCase()!='body') {
                var position = 'absolute';
            } else {
                var position = 'fixed';
            }
            $('#'+this.id).css({
                position: position,
                display: 'block',
                width: this.size + 'px',
                height: this.size + 'px',
                zIndex: this.zIndex,
                opacity: this.opacity,
                left: this.left + 'px',
                top: this.top + 'px'
            });
        } else {
            $('#'+this.id).css({
                display: 'block',
                left: this.left + 'px',
                top: this.top + 'px'
            });
        }
        if (this.left<this.targetX || this.left>this.targetX+this.targetW-this.size || 
            this.top<this.targetY || this.top>this.targetY+this.targetH-this.size
        ) {
            $('#'+this.id).css({
                display: 'none'
            });
        }
        // skipping first spline
        this.shift();
        this.iteration = this.splinePointsCount;
    };

    ZiraSpread.prototype.addPoint = function(prevX, prevY, nextY) {
        if (this.angle % 90 == 0 && this.angle % 180 != 0) {
            this.angle = this.angle>0 ? this.angle-5 : this.angle+5;
        }
        
        var angle = this.angle * ( Math.PI / 180);
        var y = Math.random() * (nextY - prevY) + prevY;
        if (prevX !== null) {
            var xW = Math.random() * (this.xAxisWidth) - this.xAxisWidth / 2;
            var x = Math.tan(angle) * (nextY - prevY) + xW + prevX;
        } else {
            var x = this.initX;
        }

        this.xPoints.push(x);
        this.yPoints.push(y);

        return x;
    };

    ZiraSpread.prototype.update = function(left, top) {
        if (left>=this.targetX && left<=this.targetX+this.targetW-this.size && 
            top>=this.targetY && top<=this.targetY+this.targetH-this.size
        ) {
            this.left = left;
            this.top = top;
            $('#'+this.id).css({
                display: 'block',
                left: left + 'px',
                top: top + 'px'
            });
        } else if (this.splinePoints.length==0) {
            this.init(true);
            this.iteration--;
        } else {
            $('#'+this.id).css({
                display: 'none'
            });
        }
    };

    ZiraSpread.prototype.shift = function() {
        if (this.enableCandidate && this.disabled) {
            this.enableCandidate = false;
            this.disableCandidate = false;
            this.disabled = false;
            this.creationTime = (new Date()).getTime();
            this.init(true);
            return;
        }
        if (this.disabled) return;
        var offset = this.iteration % this.splinePointsCount;
        if (offset==0) {
            this.generateSplinePoints();
            if (this.algorythm == 'bezier') {
                this.pointIndex+=3;
            } else {
                this.pointIndex++;
            }
            if (this.splinePoints.length==0 && this.iteration>0) {
                if (this.disableCandidate) {
                    this.disabled = true;
                    return;
                }
                this.init(true);
                return;
            }
        }
        if (this.splinePoints.length>0 && this.splinePoints.length>offset) {
            var left = this.splinePoints[offset].x;
            var top = this.splinePoints[offset].y;
            this.update(left, top);
        } else if (this.splinePoints.length>0 && this.splinePoints.length<=offset) {
            do {
                this.iteration++;
            } while(this.iteration % this.splinePointsCount != 0);
            this.iteration--;
        }
        this.iteration++;
    };

    ZiraSpread.prototype.generateSplinePoints = function() {
        this.splinePoints = [];
        var x1, x2, x3, x4, y1, y2, y3, y4;
        if (this.pointIndex==0 || this.pointIndex >= this.pointsCount) return false;
        x1 = this.xPoints[this.pointIndex-1];
        y1 = this.yPoints[this.pointIndex-1];
        x2 = this.xPoints[this.pointIndex];
        y2 = this.yPoints[this.pointIndex];
        x3 = this.xPoints[this.pointIndex+1];
        y3 = this.yPoints[this.pointIndex+1];
        x4 = this.xPoints[this.pointIndex+2];
        y4 = this.yPoints[this.pointIndex+2];
        if (this.algorythm == 'catmull-rom') {
            this.splinePoints = this.centripetalCatmullRomSpline(x1, y1, x2, y2, x3, y3, x4, y4, this.splinePointsCount);
        } else if (this.algorythm == 'bezier') {
            this.splinePoints = this.cubicBezierCurve(x1, y1, x2, y2, x3, y3, x4, y4, this.splinePointsCount);  
        }
    };

    ZiraSpread.prototype.destroy = function() {
        this.xPoints = [];
        this.yPoints = [];
        this.pointsCount = 0;
        this.splinePoints = [];
        this.pointIndex = 0;
        this.iteration = 0;
        $('#'+this.id).remove();
    };

    /**
     * Cubic Bezier curve: (1-t)^3*P0 + 3*(1-t)^2*t*P1 + 3*(1-t)*t^2*P2 + t^3*P3
     * x1, y1 - point 1
     * x2, y2 - point 2
     * x3, y3 - point 3
     * x4, y4 - point 4
     * count - number of points between point 1 and point 4
     */
    ZiraSpread.prototype.cubicBezierCurve = function(x1, y1, x2, y2, x3, y3, x4, y4, count) {
        var points = [];
        var delta = 1/count;
        
        for(var t=0; t<1; t+=delta) {
            var xt = Math.pow(1-t,3)*x1 + 3*Math.pow(1-t,2)*t*x2 + 3*(1-t)*Math.pow(t,2)*x3 + Math.pow(t,3)*x4;
            var yt = Math.pow(1-t,3)*y1 + 3*Math.pow(1-t,2)*t*y2 + 3*(1-t)*Math.pow(t,2)*y3 + Math.pow(t,3)*y4;
            // if (xt>x2 && xt<x3 && yt>y2 && yt<y3) {
                points.push({
                    x: xt,
                    y: yt
                });
            // }
        }

        return points;
    };

    /**
     * Centripetal Catmull–Rom spline
     * x1, y1 - point 1
     * x2, y2 - point 2
     * x3, y3 - point 3
     * x4, y4 - point 4
     * count - number of points between point 2 and point 3
     */
    ZiraSpread.prototype.centripetalCatmullRomSpline = function(x1, y1, x2, y2, x3, y3, x4, y4, count) {
        function calcT(t, xa, ya, xb, yb) {
            return Math.pow(Math.pow(Math.pow((xb-xa),2)+Math.pow((yb-ya),2),.5),.5) + t;
        }

        var t0 = 0;
        var t1 = calcT(t0, x1, y1, x2, y2);
        var t2 = calcT(t1, x2, y2, x3, y3);
        var t3 = calcT(t2, x3, y3, x4, y4);

        var points = [];
        var delta = (t2-t1)/count;
        
        for(var t=t1+delta; t<t2; t+=delta) {
            var a1x = (t1-t)/(t1-t0)*x1 + (t-t0)/(t1-t0)*x2;
            var a1y = (t1-t)/(t1-t0)*y1 + (t-t0)/(t1-t0)*y2;

            var a2x = (t2-t)/(t2-t1)*x2 + (t-t1)/(t2-t1)*x3;
            var a2y = (t2-t)/(t2-t1)*y2 + (t-t1)/(t2-t1)*y3;

            var a3x = (t3-t)/(t3-t2)*x3 + (t-t2)/(t3-t2)*x4;
            var a3y = (t3-t)/(t3-t2)*y3 + (t-t2)/(t3-t2)*y4;

            var b1x = (t2-t)/(t2-t0)*a1x + (t-t0)/(t2-t0)*a2x;
            var b1y = (t2-t)/(t2-t0)*a1y + (t-t0)/(t2-t0)*a2y;

            var b2x = (t3-t)/(t3-t1)*a2x + (t-t1)/(t3-t1)*a3x;
            var b2y = (t3-t)/(t3-t1)*a2y + (t-t1)/(t3-t1)*a3y;
            
            var cx = (t2-t)/(t2-t1)*b1x + (t-t1)/(t2-t1)*b2x;
            var cy = (t2-t)/(t2-t1)*b1y + (t-t1)/(t2-t1)*b2y;

            points.push({
                x: cx,
                y: cy
            })
        }

        return points;
    };

    if (typeof(ZiraSpreadInit)!="undefined") {
        $(document).ready(function(){
            ZiraSpreadInit.call();
        });
    }
})(jQuery);