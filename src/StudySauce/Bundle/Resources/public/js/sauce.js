var DASHBOARD_MARGINS = {};

window.sincluding = [];
window.visits = [];
window.players = [];
window.jsErrors = [];
window.noError = false;

$(window).unload(function() {
    window.noError = true;
});
function getQueryObject(url) {
    var match,
        pl     = /\+/g,  // Regex for replacing addition symbol with a space
        search = /([^&=]+)=?([^&]*)/g,
        decode = function (s) { return decodeURIComponent(s.replace(pl, " ")); },
        query  = url.substr(url.indexOf('?') + 1);

    var urlParams = {};
    if(url.indexOf('?') > -1) {
        while (match = search.exec(query)) {
            var key = decode(match[1]);
            assignSubKey(urlParams, key, decode(match[2]));
        }
    }

    return urlParams;
}

function assignSubKey(obj, key, value) {
    var keys = key.split(/\]?\[/ig);
    for(var k = 0; k < keys.length; k++) {
        var subKey = keys[k];
        if(subKey.substr(-1) == ']') {
            subKey = subKey.substr(0, subKey.length-1);
        }
        if (k == keys.length - 1) {
            obj[subKey] = value;
        }
        else if (typeof obj[subKey] == 'undefined') {
            obj[subKey] = {};
        }
        obj = obj[subKey];
    }
}

function setSelectionRange(input, selectionStart, selectionEnd) {
    if (input.setSelectionRange) {
        input.focus();
        input.setSelectionRange(selectionStart, selectionEnd);
    }
    else if (input.createTextRange) {
        var range = input.createTextRange();
        range.collapse(true);
        range.moveEnd('character', selectionEnd);
        range.moveStart('character', selectionStart);
        range.select();
    }
}

if (typeof key != 'undefined') {
    key.filter = function (event) {
        //var tagName = (event.target || event.srcElement).tagName;
        //key.setScope(/^(INPUT|TEXTAREA|SELECT)$/.test(tagName) ? 'input' : 'other');
        return true;
    };
}

+function ($) {
    'use strict';

    // TAB CLASS DEFINITION
    // ====================

    var Tab = function (element) {
        this.element = $(element)
    }

    Tab.VERSION = '3.2.0'

    Tab.prototype.show = function () {
        var $this    = this.element
        var $ul      = $this.closest('ul:not(.dropdown-menu)')
        var selector = $this.data('target')

        if (!selector) {
            selector = $this.attr('href')
            selector = selector && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
        }

        if ($this.parent('li').hasClass('active')) return

        var previous = $ul.find('.active:last a')[0]
        var e        = $.Event('show.bs.tab', {
            relatedTarget: previous
        })

        $this.trigger(e)

        if (e.isDefaultPrevented()) return

        var $target = $(selector)

        this.activate($this.closest('li'), $ul)
        this.activate($target, $target.parent(), function () {
            $this.trigger({
                type: 'shown.bs.tab',
                relatedTarget: previous
            })
        })
    }

    Tab.prototype.activate = function (element, container, callback) {
        var $active    = container.find('> .active')
        var transition = callback
            && $.support.transition
            && $active.hasClass('fade')

        function next() {
            $active
                .removeClass('active')
                .find('> .dropdown-menu > .active')
                .removeClass('active')

            element.addClass('active')

            if (transition) {
                element[0].offsetWidth // reflow for transition
                element.addClass('in')
            } else {
                element.removeClass('fade')
            }

            if (element.parent('.dropdown-menu')) {
                element.closest('li.dropdown').addClass('active')
            }

            callback && callback()
        }

        transition ?
            $active
                .one('bsTransitionEnd', next)
                .emulateTransitionEnd(150) :
            next()

        $active.removeClass('in')
    }


    // TAB PLUGIN DEFINITION
    // =====================

    function Plugin(option) {
        return this.each(function () {
            var $this = $(this)
            var data  = $this.data('bs.tab')

            if (!data) $this.data('bs.tab', (data = new Tab(this)))
            if (typeof option == 'string') data[option]()
        })
    }

    var old = $.fn.tab

    $.fn.tab             = Plugin
    $.fn.tab.Constructor = Tab


    // TAB NO CONFLICT
    // ===============

    $.fn.tab.noConflict = function () {
        $.fn.tab = old
        return this
    }


    // TAB DATA-API
    // ============

    $(document).on('click', '[data-toggle="tab"], [data-toggle="pill"]', function (e) {
        e.preventDefault()
        Plugin.call($(this), 'show')
    })

}(jQuery);

window.onerror = function (errorMessage, url, lineNumber) {
    var message = "Error: [" + errorMessage + "], url: [" + url + "], line: [" + lineNumber + "]";
    window.jsErrors.push(message);
    if(window.noError)
        return false;
    var dialog = $('#error');
    if(dialog.length > 0)
    {
        dialog.find('.modal-body').html(errorMessage);
        dialog.modal({show:true});
    }
    return true;
};
RegExp.escape= function(s) {
    return s.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
};
// datepicker modifications to turn onAfterUpdate in to a settable event callback
$.datepicker._defaults.onAfterUpdate = null;

var datepicker__updateDatepicker = $.datepicker._updateDatepicker;
$.datepicker._updateDatepicker = function( inst ) {
    datepicker__updateDatepicker.call( this, inst );

    var onAfterUpdate = this._get(inst, 'onAfterUpdate');
    if (onAfterUpdate)
        onAfterUpdate.apply((inst.input ? inst.input[0] : null),
            [(inst.input ? inst.input.val() : ''), inst]);
};

function centerize() {
    $(this).each(function () {
        if(!($(this).is('.centerized'))) {
            $(this).addClass('centerized');
        }
        $(this).css('margin-top', '').css('top', '');
        var myheight = $(this).outerHeight(true);
        var relativeParent = $(this).parents().filter(function () {return (/relative|absolute|fixed/i).test($(this).css('position'));}).first();
        var relativeHeight = relativeParent.outerHeight();
        if(relativeParent.length == 0) {
            relativeParent = parent();
            relativeHeight = relativeParent.height();
        }
        else {

        }
        var offsetY = (relativeHeight - myheight) / 2;
        if(relativeParent.css('overflow') == 'auto' && offsetY < 0) {
            // this should scroll instead of centering
            offsetY = 0;
        }
        $(this).css((/relative/i).test($(this).css('position')) ? 'top' : 'margin-top', offsetY + 'px');
        if($(this).is('img')) {
            $(this).one('load', centerize);
        }
    });
}

function loadingAnimation(that)
{
    if(typeof that != 'undefined' && that.length > 0 && that.find('.squiggle').length == 0)
    {
        return loadingAnimation.call($('<small class="squiggle">&nbsp;</small>').appendTo(that), that);
    }
    else if ($(this).is('.squiggle'))
    {
        var width = $(this).parent().outerWidth(false);
        return $(this).css('width', 0).css('left', 0)
            .animate({width: width}, 1000, 'swing', function () {
                var width = $(this).parent().outerWidth(false);
                $(this).css('width', width).css('left', 0)
                    .animate({left: width, width: 0}, 1000, 'swing', loadingAnimation);
            });
    }
    else if(typeof that != 'undefined')
        return that.find('.squiggle');
}

function setupYoutubePlayer()
{
    var frame = $(this),
        ytPlayer = new YT.Player(frame.attr('id'), {
            events: {
                'onStateChange': function (e) {
                    var x = frame.offset().left,
                        y = frame.offset().top,
                        w = frame.width(),
                        h = frame.height();
                    frame.attr('class', (frame.attr('class') || '').replace(/yt-state-([0-9\-]*)(\s|$)/ig, '') + ' yt-state-' + e.data).trigger('yt' + e.data);
                    if(e.data == 1)
                    {
                        frame.css({
                            top: y,
                            left: x,
                            bottom: window.innerHeight - y + h,
                            right: window.innerWidth - x + w,
                            width: w / window.innerWidth * 100 + '%',
                            height: w / window.innerHeight * 100 + '%',
                            opacity: 0})
                            .animate({
                                top: 0,
                                bottom: 0,
                                right: 0,
                                left: 0,
                                height: '100%',
                                width: '100%',
                                opacity: 1}, 159);
                    }
                    /*
                     -1 – unstarted
                     0 – ended
                     1 – playing
                     2 – paused
                     3 – buffering
                     5 – video cued
                     */
                    _gaq.push(['_trackPageview', location.pathname + location.search  + '#yt' + e.data]);
                    visits[visits.length] = {path: window.location.pathname, query: window.location.search, hash: '#yt' + e.data, time:(new Date()).toJSON()};
                }
            }
        });
    $(ytPlayer).data('frame', frame);
    window.players[window.players.length] = ytPlayer;
}

function onYouTubeIframeAPIReady() {
    var frames = $(this).find('iframe[src*="youtube.com/embed"]');
    var delayed = function () {
        if(typeof YT != 'undefined' && typeof YT.Player != 'undefined')
            frames.each(setupYoutubePlayer);
        else
            setTimeout(delayed, 100);
    };
    delayed();
}

function isElementInViewport (el) {

    //special bonus for those using jQuery
    if (typeof jQuery === "function" && el instanceof jQuery) {
        el = el[0];
    }

    var rect = el.getBoundingClientRect();

    return !(0 > rect.right || document.documentElement.clientWidth < rect.left
        || 0 > rect.bottom || document.documentElement.clientHeight < rect.top);
}

function ssMergeStyles(content)
{
    var styles = $.merge(content.filter('link[type*="css"], style[type*="css"]'), content.find('link[type*="css"], style[type*="css"]'));

    var any = false;
    $(styles).each(function () {
        // remove version string
        var url;
        if (typeof (url = $(this).attr('href')) != 'undefined') {
            url = url.replace(/\?.*/ig, '');
            if ($('link[href*="' + url + '"]').length == 0) {
                $('head').append('<link href="' + url + '" type="text/css" rel="stylesheet" />');
                any = true;
            }
        }
        else {
            var re = (/url\("(.*?)"\)/ig),
                match,
                media = $(this).attr('media'),
                imports = false;
            while (match = re.exec($(this).html())) {
                imports = true;
                // remove version string
                url = match[1].replace(/\?.*/ig, '');
                if ($('link[href="' + url + '"]').length == 0 &&
                    $('style:contains("' + url + '")').length == 0) {
                    if (typeof media == 'undefined' || media == 'all') {
                        $('head').append('<link href="' + url + '" type="text/css" rel="stylesheet" />');
                        any = true;
                    }
                    else {
                        $('head').append('<style media="' + media + '">@import url("' + url + '");');
                        any = true;
                    }
                }
            }
            if(!imports)
                $('head').append($(this));
        }
    });

    // queue stylesheets if we are loading for a tab
    var pane;
    if (any && (pane = content.filter('.panel-pane').first()).length > 0) {

        //Wait for style to be loaded
        var wait = setInterval(function(){
            //Check for the style to be applied to the body
            if($('.css-loaded.' + pane.attr('id')).css('content').indexOf('loading-' + pane.attr('id')) > -1) {
                //CSS ready
                window.sincluding.splice(window.sincluding.indexOf('loading-' + pane.attr('id')), 1);
            }
            // clear loading if done loading all css
            var loading = 0;
            for(var i = 0; i < window.sincluding.length; i++) {
                if(window.sincluding[i].substr(0, 8) == 'loading-')
                    loading++;
            }
            if(loading == 0) {
                clearInterval(wait);
            }
        }, 100);

        $('<div class="css-loaded ' + pane.attr('id') + '"></div>').appendTo($('body'));
        window.sincluding.push('loading-' + pane.attr('id'))
    }

    return styles;
}

var alreadyLoadedScripts = [];
function ssMergeScripts(content)
{
    var scripts = $.merge(content.filter('script[type="text/javascript"]'), content.find('script[type="text/javascript"]'));
    $(scripts).each(function () {
        // TODO: remove version information from link, only load one version per page refresh
        var url = ($(this).attr('src') || '').replace(/\?.*/ig, '');
        if (url != '') {
            // only load script if it hasn't already been loaded
            if ($('script[src*="' + url + '"]').length == 0 && alreadyLoadedScripts.indexOf(url) == -1) {
                console.log(url);
                window.sincluding.push(url);
                $.getScript(url);
                alreadyLoadedScripts[alreadyLoadedScripts.length] = url;
            }
        }
        else {
            try
            {
                eval($(this).text());
            }
            catch(e)
            {
                console.log(e);
            }
        }
    });
    return scripts;
}

centerize.apply($('body').find('.centerized:visible'));

function gatherFields(fields, visibleOnly) {
    var fieldMatch = function (f) {
        return '[name="' + f + '"], [name^="' + f + '-"], [name^="' + f + '["]';
    };
    var context = $(this),
        form = context.closest('[class*="-row"],form').last().add('+ .expandable:not([class*="-row"])');
    var result = {};
    var formFields = [];
    if (form.is('form')) {
        formFields = form.serializeArray();
    }
    else {
        for(var f in fields) {
            if (fields.hasOwnProperty(f)) {
                var inputField = context.find(fieldMatch(fields[f]));
                var key = fields[f];
                if(inputField.is('[name^="' + fields[f] + '["]')) {
                    key = inputField.attr('name');
                }
                var value;
                if (inputField.is('[type="checkbox"],[type="radio"]')) {
                    value = inputField.filter(':checked').val();
                }
                else if (inputField.is('.dateTimePicker')) {
                    value = inputField.datetimepicker('getValue');
                }
                else if (inputField.length > 0) {
                    value = inputField.val();
                }
                if(typeof value != 'undefined') {
                    formFields[formFields.length] = {name: key, value: value};
                }
            }
        }
    }
    for(var i = 0; i < formFields.length; i++) {
        if(fields.indexOf(formFields[i].name.split(/[-\[]/ig)[0]) > -1 &&
            (!visibleOnly || form.find(fieldMatch(fields[f])).filter(':visible').length > 0)) {
            assignSubKey(result, formFields[i].name, formFields[i].value);
        }
    }
    return result;
}

function applyFields(fields) {
    var context = $(this);
    for(var f in fields) {
        if (fields.hasOwnProperty(f)) {
            var inputField = context.find('[name="' + fields[f] + '"]:visible');
            if (inputField.is('[type="checkbox"],[type="radio"]')) {
                inputField.each(function () {
                    if($(this).val() == fields[f] || ($(this).val() == 'true' && fields[f]) || ($(this).val() == 'false' && !fields[f])) {
                        $(this).prop('checked', true);
                    }
                });
            }
            else if (inputField.is('.dateTimePicker')) {
                inputField.datetimepicker('setOptions', {value: new Date(fields[f])});
            }
            else {
                inputField.val(fields[f])
            }
        }
    }
}

$(document).ready(function () {
    var body = $('body');

    centerize.apply(body.find('.centerized:visible'));


    $(document).tooltip({
        items: '*[title]:not(iframe):not(.cke_editable):not(.cke),*[original-title]:not(iframe):not(.cke_editable):not(.cke)',
        position: { my: "center top", at: "center bottom+10" },
        content: function() {
            var element = $(this);
            if ( element.is( "[title]" ) ) {
                return $(this).attr('title');
            }
        }
    });

    /*
    $(document).on('mouseover', 'rect[title],path[title]', function () {
        var that = $(this);
        setTimeout(function () {
            var id = that.data('ui-tooltip-id'),
                tip = $('#' + id);
            tip.position({my: 'left-10 center'});
            tip.css('left', that.offset().left + that[0].getBBox().width + 10).css('top', that.offset().top + that[0].getBBox().height / 2 - tip.height() / 2);
            tip.addClass('left-arrow')
        }, 15);
    });
    */

    body.on('click', 'a[href="#yt-pause"]', function (evt) {
        evt.preventDefault();
        var frame = $(this).prev().closest('iframe');
        for(var i = 0; i < window.players.length; i++) {
            if ($(window.players[i]).data('frame').is(frame)) {
                window.players[i].pauseVideo();
            }
        }
    });

    $('.sinclude').each(function () {
        var that = $(this),
            url = that.attr('data-src');
        window.sincluding.push(url);
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'text',
            success: function (data)
            {
                var visible = $('.panel-pane:visible'),
                    newStuff = $(data),
                    styles = ssMergeStyles(newStuff),
                    scripts = ssMergeScripts(newStuff);
                newStuff = newStuff.not(styles).not(scripts);
                // do not merge top level items with IDs that already exist
                newStuff.filter('[id]').each(function () {
                    var id = $(this).attr('id');
                    if($('#' + id).length > 0)
                        newStuff = newStuff.not('#' + id);
                });
                that.replaceWith(newStuff);
            },
            error: function () {
            }
        });
    });

    function activatePanel(panel) {
        // animate panels
        var triggerShow = setInterval(function () {
            if (window.sincluding.length == 0) {
                body.css('overflow', 'hidden');
                var panels = body.find('.panel-pane:visible')
                    .css({'position': 'absolute', left: 0}).animate({left: '-100%'}, { duration: 500, queue: false, done: function () {
                        panels.hide();
                    } });
                panel.css({'position': 'absolute', 'left': '100%'}).show().animate({left: '0'}, { duration: 500, queue: false, done: function () {
                    panel.css('position', '');
                    body.css('overflow', '');
                } });
                centerize.apply(panel.find('.centerized:visible'));
                // poll for panel visibility and fire events
                var triggerHide = setInterval(function () {
                    if (panels.is(':visible'))
                        return;
                    panels.trigger('hide');
                    setTimeout(function () {
                        panel.scrollintoview(DASHBOARD_MARGINS).trigger('show')
                    }, 75);
                    clearInterval(triggerHide);
                }, 50);
                clearInterval(triggerShow);
            }
        }, 50);
    }
    window.activatePanel = activatePanel;

    // show the already visible tabs
    var panel = body.find('.panel-pane').first();
    if(panel.length > 0) {
        var key = panel.attr('id').replace(/-[a-z]+[0-9]+$/ig, '');
        if (Routing.getRoute(key)) {
            var path = Routing.generate(key),
                item = body.find('.main-menu a[href^="' + path + '"]').first();

            if (item.parents('nav').find('ul.collapse.in') != item.parents('ul.collapse.in'))
                item.parents('nav').find('ul.collapse.in').removeClass('in');
            item.addClass('active').parents('ul.collapse').addClass('in').css('height', '');
            var host;
            body.find('#welcome-message .main-menu a').each(function () {
                var parts = $(this).attr('href').split('/');
                parts[parts.length-1] = path.substr(1);
                $(this).attr('href', parts.join('/'));
            });
            if(!(host = body.find('#welcome-message .main-menu a[href*="' + window.location.hostname +  '"]')).is('.active')) {
                host.addClass('active');
            }
        }
        ssMergeStyles(body);
        activatePanel.apply(body, [panel]);
    }
});

$.ajaxPrefilter(function (options, originalOptions) {
    // do not send data for POST/PUT/DELETE
    if (originalOptions.type !== 'GET' || options.type !== 'GET') {
        return;
    }

    var data = originalOptions.data;
    if (originalOptions.data !== undefined) {
        if (Object.prototype.toString.call(originalOptions.data) === '[object String]') {
            data = $.deparam(originalOptions.data); // see http://benalman.com/code/projects/jquery-bbq/examples/deparam/
        }
    } else {
        data = {};
    }

    var visits = window.visits.slice(0, Math.min(10, window.visits.length));
    window.visits = window.visits.length <= 10 ? [] : window.visits.slice(10, window.visits.length);
    options.data = $.param($.extend(data, { __visits: visits }));
});

if(typeof window.jqAjax == 'undefined') {
    window.jqAjax = $.ajax;
    $.ajax = function (settings) {
        var success = settings.success,
            error = settings.error,
            url = settings.url;
        settings.success = function (data, textStatus, jqXHR) {
            window.sincluding.splice(window.sincluding.indexOf(url), 1);
            if (typeof data == 'string' && data.indexOf('{"redirect":') > -1) {
                try {
                    data = JSON.parse(data.substr(0, 4096)); // no way a redirect would be longer than that
                } catch (ignore) {

                }
            }
            if (data != null && typeof data.redirect != 'undefined') {
                var a = document.createElement('a');
                a.href = data.redirect;
                if (window.location.pathname == data.redirect) {
                    // do nothing because we are already on the page
                }
                else {
                    if (typeof window.handleLink == 'undefined' || window.handleLink.apply(a, [jQuery.Event('click')])) {
                        window.location = data.redirect;
                    }
                }
            }
            if (typeof success != 'undefined')
                success(data, textStatus, jqXHR);
        };
        settings.error = function ( jqXHR, textStatus, errorThrown) {
            window.sincluding.splice(window.sincluding.indexOf(url), 1);
            var message = "Error: [" + errorThrown + "], url: [" + url + "], status: [" + textStatus + "]";
            window.jsErrors.push(message);
            if (typeof error != 'undefined')
                error(jqXHR, textStatus, errorThrown);
        };
        return window.jqAjax(settings);
    };
}

$(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
    if(window.noError)
        return false;
    var dialog = $('#error');
    if(dialog.length > 0 && thrownError !== "abort" && jqXHR.status !== 0)
    {
        var error = '';
        try {
            var content = $(jqXHR.responseText);
            if(content.filter('#error'))
                error = content.filter('#error').find('.pane-content').html();
        } catch(ex) {
            error = jqXHR.responseText;
        }
        finally {
            dialog.find('.modal-body').html(error);
            dialog.modal({show:true});
            throw thrownError;
        }
    }
});

// set some extra utility functions globally
Date.prototype.getWeekNumber = function () {
// Create a copy of this date object
    var target  = new Date(this.valueOf());

    // ISO week date weeks start on monday
    // so correct the day number
    var dayNr   = (this.getDay() + 6) % 7;

    // ISO 8601 states that week 1 is the week
    // with the first thursday of that year.
    // Set the target date to the thursday in the target week
    target.setDate(target.getDate() - dayNr + 3);

    // Store the millisecond value of the target date
    var firstThursday = target.valueOf();

    // Set the target to the first thursday of the year
    // First set the target to january first
    target.setMonth(0, 1);
    // Not a thursday? Correct the date to the next thursday
    if (target.getDay() != 4) {
        target.setMonth(0, 1 + ((4 - target.getDay()) + 7) % 7);
    }

    // The weeknumber is the number of weeks between the
    // first thursday of the year and the thursday in the target week
    return 1 + Math.ceil((firstThursday - target) / 604800000); // 604800000 = 7 * 24 * 3600 * 1000
};

Date.prototype.getFirstDayOfWeek = function () {
    var d = new Date(+this);
    d.setHours(0, 0, 0, 0);
    var day = d.getDay(),
        diff = d.getDate() - day + (day == 0 ? 0:0); // adjust when day is sunday
    return new Date(d.setDate(diff));
};

Date.prototype.addHours = function(h){
    this.setHours(this.getHours()+h);
    return this;
};

$.fn.selectRange = function(start, end) {
    if(!end) end = start;
    return this.each(function() {
        if (this.setSelectionRange) {
            this.focus();
            this.setSelectionRange(start, end);
        } else if (this.createTextRange) {
            var range = this.createTextRange();
            range.collapse(true);
            range.moveEnd('character', end);
            range.moveStart('character', start);
            range.select();
        }
    });
};

$.fn.redraw = function(){
    var that = $(this);
    setTimeout(function () {
        that.each(function(){
            var redraw = this.offsetHeight,
                oldZ = this.zIndex;
            if(typeof this.style != 'undefined') {
                this.style.zIndex = 2;
                this.style.zIndex = oldZ;
                this.style.webkitTransform = 'scale(1)';
                this.style.webkitTransform = '';
            }
        });
    }, 10);
};