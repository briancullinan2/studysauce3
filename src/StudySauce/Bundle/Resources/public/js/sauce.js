var DASHBOARD_MARGINS = {};

window.sincluding = [];
window.visits = [];
window.players = [];
window.jsErrors = [];
window.noError = false;

$(window).unload(function() {
    window.noError = true;
});

// ref: http://stackoverflow.com/a/1293163/2343
// This will parse a delimited string into an array of
// arrays. The default delimiter is the comma, but this
// can be overriden in the second argument.
function CSVToArray( strData, strDelimiter ){
    // Check to see if the delimiter is defined. If not,
    // then default to comma.
    strDelimiter = (strDelimiter || ",");

    // Create a regular expression to parse the CSV values.
    var objPattern = new RegExp(
        (
            // Delimiters.
            "(\\" + strDelimiter + "|\\r?\\n|\\r|^)" +

                // Quoted fields.
            "(?:\"([^\"]*(?:\"\"[^\"]*)*)\"|" +

                // Standard fields.
            "([^\"\\" + strDelimiter + "\\r\\n]*))"
        ),
        "gi"
    );


    // Create an array to hold our data. Give the array
    // a default empty first row.
    var arrData = [[]];

    // Create an array to hold our individual pattern
    // matching groups.
    var arrMatches = null;


    // Keep looping over the regular expression matches
    // until we can no longer find a match.
    while (arrMatches = objPattern.exec( strData )){

        // Get the delimiter that was found.
        var strMatchedDelimiter = arrMatches[ 1 ];

        // Check to see if the given delimiter has a length
        // (is not the start of string) and if it matches
        // field delimiter. If id does not, then we know
        // that this delimiter is a row delimiter.
        if (
            strMatchedDelimiter.length &&
            strMatchedDelimiter !== strDelimiter
        ){

            // Since we have reached a new row of data,
            // add an empty row to our data array.
            arrData.push( [] );

        }

        var strMatchedValue;

        // Now that we have our delimiter out of the way,
        // let's check to see which kind of value we
        // captured (quoted or unquoted).
        if (arrMatches[ 2 ]){

            // We found a quoted value. When we capture
            // this value, unescape any double quotes.
            strMatchedValue = arrMatches[ 2 ].replace(
                new RegExp( "\"\"", "g" ),
                "\""
            );

        } else {

            // We found a non-quoted value.
            strMatchedValue = arrMatches[ 3 ];

        }


        // Now that we have our value string, let's add
        // it to the data array.
        arrData[ arrData.length - 1 ].push( strMatchedValue );
    }

    // Return the parsed data.
    return( arrData );
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

+function ($) {
    'use strict';

    // MODAL CLASS DEFINITION
    // ======================

    var Modal = function (element, options) {
        this.options        = options
        this.$body          = $(document.body)
        this.$element       = $(element)
        this.$backdrop      =
            this.isShown        = null
        this.scrollbarWidth = 0

        if (this.options.remote) {
            this.$element
                .find('.modal-content')
                .load(this.options.remote, $.proxy(function () {
                    this.$element.trigger('loaded.bs.modal')
                }, this))
        }
    }

    Modal.VERSION  = '3.2.0'

    Modal.DEFAULTS = {
        backdrop: true,
        keyboard: true,
        show: true
    }

    Modal.prototype.toggle = function (_relatedTarget) {
        return this.isShown ? this.hide() : this.show(_relatedTarget)
    }

    Modal.prototype.show = function (_relatedTarget) {
        var that = this
        var e    = $.Event('show.bs.modal', { relatedTarget: _relatedTarget })

        this.$element.trigger(e)

        if (this.isShown || e.isDefaultPrevented()) return

        this.isShown = true

        this.checkScrollbar()
        this.$body.addClass('modal-open')

        this.setScrollbar()
        this.escape()

        this.$element.on('click.dismiss.bs.modal', '[data-dismiss="modal"]', $.proxy(this.hide, this))

        this.backdrop(function () {
            var transition = $.support.transition && that.$element.hasClass('fade')

            if (!that.$element.parent().length) {
                that.$element.appendTo(that.$body) // don't move modals dom position
            }

            that.$element
                .show()
                .scrollTop(0)

            if (transition) {
                that.$element[0].offsetWidth // force reflow
            }

            that.$element
                .addClass('in')
                .attr('aria-hidden', false)

            that.enforceFocus()

            var e = $.Event('shown.bs.modal', { relatedTarget: _relatedTarget })

            transition ?
                that.$element.find('.modal-dialog') // wait for modal to slide in
                    .one('bsTransitionEnd', function () {
                        that.$element.trigger('focus').trigger(e)
                    })
                    .emulateTransitionEnd(300) :
                that.$element.trigger('focus').trigger(e)
        })
    }

    Modal.prototype.hide = function (e) {
        if (e) e.preventDefault()

        e = $.Event('hide.bs.modal')

        this.$element.trigger(e)

        if (!this.isShown || e.isDefaultPrevented()) return

        this.isShown = false

        this.$body.removeClass('modal-open')

        this.resetScrollbar()
        this.escape()

        $(document).off('focusin.bs.modal')

        this.$element
            .removeClass('in')
            .attr('aria-hidden', true)
            .off('click.dismiss.bs.modal')

        $.support.transition && this.$element.hasClass('fade') ?
            this.$element
                .one('bsTransitionEnd', $.proxy(this.hideModal, this))
                .emulateTransitionEnd(300) :
            this.hideModal()
    }

    Modal.prototype.enforceFocus = function () {
        $(document)
            .off('focusin.bs.modal') // guard against infinite focus loop
            .on('focusin.bs.modal', $.proxy(function (e) {
                if (this.$element[0] !== e.target && !this.$element.has(e.target).length) {
                    this.$element.trigger('focus')
                }
            }, this))
    }

    Modal.prototype.escape = function () {
        if (this.isShown && this.options.keyboard) {
            this.$element.on('keyup.dismiss.bs.modal', $.proxy(function (e) {
                e.which == 27 && this.hide()
            }, this))
        } else if (!this.isShown) {
            this.$element.off('keyup.dismiss.bs.modal')
        }
    }

    Modal.prototype.hideModal = function () {
        var that = this
        this.$element.hide()
        this.backdrop(function () {
            that.$element.trigger('hidden.bs.modal')
        })
    }

    Modal.prototype.removeBackdrop = function () {
        this.$backdrop && this.$backdrop.remove()
        this.$backdrop = null
    }

    Modal.prototype.backdrop = function (callback) {
        var that = this
        var animate = this.$element.hasClass('fade') ? 'fade' : ''

        if (this.isShown && this.options.backdrop) {
            var doAnimate = $.support.transition && animate

            this.$backdrop = $('<div class="modal-backdrop ' + animate + '" />')
                .appendTo(this.$body)

            this.$element.on('click.dismiss.bs.modal', $.proxy(function (e) {
                if (e.target !== e.currentTarget) return
                this.options.backdrop == 'static'
                    ? this.$element[0].focus.call(this.$element[0])
                    : this.hide.call(this)
            }, this))

            if (doAnimate) this.$backdrop[0].offsetWidth // force reflow

            this.$backdrop.addClass('in')

            if (!callback) return

            doAnimate ?
                this.$backdrop
                    .one('bsTransitionEnd', callback)
                    .emulateTransitionEnd(150) :
                callback()

        } else if (!this.isShown && this.$backdrop) {
            this.$backdrop.removeClass('in')

            var callbackRemove = function () {
                that.removeBackdrop()
                callback && callback()
            }
            $.support.transition && this.$element.hasClass('fade') ?
                this.$backdrop
                    .one('bsTransitionEnd', callbackRemove)
                    .emulateTransitionEnd(150) :
                callbackRemove()

        } else if (callback) {
            callback()
        }
    }

    Modal.prototype.checkScrollbar = function () {
        if (document.body.clientWidth >= window.innerWidth) return
        this.scrollbarWidth = this.scrollbarWidth || this.measureScrollbar()
    }

    Modal.prototype.setScrollbar = function () {
        var bodyPad = parseInt((this.$body.css('padding-right') || 0), 10)
        if (this.scrollbarWidth) this.$body.css('padding-right', bodyPad + this.scrollbarWidth)
    }

    Modal.prototype.resetScrollbar = function () {
        this.$body.css('padding-right', '')
    }

    Modal.prototype.measureScrollbar = function () { // thx walsh
        var scrollDiv = document.createElement('div')
        scrollDiv.className = 'modal-scrollbar-measure'
        this.$body.append(scrollDiv)
        var scrollbarWidth = scrollDiv.offsetWidth - scrollDiv.clientWidth
        this.$body[0].removeChild(scrollDiv)
        return scrollbarWidth
    }


    // MODAL PLUGIN DEFINITION
    // =======================

    function Plugin(option, _relatedTarget) {
        return this.each(function () {
            var $this   = $(this)
            var data    = $this.data('bs.modal')
            var options = $.extend({}, Modal.DEFAULTS, $this.data(), typeof option == 'object' && option)

            if (!data) $this.data('bs.modal', (data = new Modal(this, options)))
            if (typeof option == 'string') data[option](_relatedTarget)
            else if (options.show) data.show(_relatedTarget)
        })
    }

    var old = $.fn.modal

    $.fn.modal             = Plugin
    $.fn.modal.Constructor = Modal


    // MODAL NO CONFLICT
    // =================

    $.fn.modal.noConflict = function () {
        $.fn.modal = old
        return this
    }


    // MODAL DATA-API
    // ==============

    $(document).on('click.bs.modal.data-api', '[data-toggle="modal"]', function (e) {
        var $this   = $(this)
        var href    = $this.attr('href')
        var $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))) // strip for ie7
        var option  = $target.data('bs.modal') ? 'toggle' : $.extend({ remote: !/#/.test(href) && href }, $target.data(), $this.data())

        if ($this.is('a')) e.preventDefault()

        $target.one('show.bs.modal', function (showEvent) {
            if (showEvent.isDefaultPrevented()) return // only register focus restorer if modal will actually get shown
            $target.one('hidden.bs.modal', function () {
                $this.is(':visible') && $this.trigger('focus')
            })
        })
        Plugin.call($target, option, this)
    })

}(jQuery);

window.onerror = function (errorMessage, url, lineNumber) {
    var message = "Error: [" + errorMessage + "], url: [" + url + "], line: [" + lineNumber + "]";
    window.jsErrors.push(message);
    if(window.noError)
        return false;
    var dialog = $('#error-dialog');
    if(dialog.length > 0)
    {
        dialog.find('.modal-body').html(errorMessage);
        dialog.modal({show:true});
    }
    return true;
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
    var text = $('<textarea></textarea>')
        .css('padding', 0)
        .css('margin', 0)
        .css('position', 'fixed')
        .css('top', 0)
        .css('left', -10000)
        .css('opacity', '0')
        .css('height', 1)
        .css('border', 0)
        .css('width', 1).appendTo('body');

    $(this).each(function () {
        if(!($(this).is('.centerized'))) {
            $(this).addClass('centerized');
        }
        $(this).css('padding-top', '');
        text.val($(this).text());
        text.width($(this).outerWidth() + 18);
        text.css('font-size', $(this).css('font-size'));
        if ($(this).is('img')) {
            if ($(this).parent().height() > $(this).height()) {
                $(this).css('padding-top', (($(this).parent().height() - $(this).height()) / 2) + 'px')
            }
        }
        else {
            if (text[0].scrollHeight < $(this).height()) {
                $(this).css('padding-top', (($(this).height() - text[0].scrollHeight) / 2) + 'px')
            }
        }
    });

    text.remove();
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
        var url = $(this).attr('href');
        if (typeof url != 'undefined') {
            if ($('link[href="' + url + '"]').length == 0) {
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
                if ($('link[href="' + match[1] + '"]').length == 0 &&
                    $('style:contains("' + match[1] + '")').length == 0) {
                    if (typeof media == 'undefined' || media == 'all') {
                        $('head').append('<link href="' + match[1] + '" type="text/css" rel="stylesheet" />');
                        any = true;
                    }
                    else {
                        $('head').append('<style media="' + media + '">@import url("' + match[1] + '");');
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
            if ($('script[src^="' + url + '"]').length == 0 && alreadyLoadedScripts.indexOf(url) == -1) {
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

$(document).ready(function () {
    var body = $('body');

    centerize.apply(body.find('.centerized:visible'));

    body.on('shown.bs.modal', function () {
        centerize.apply($('body').find('.centerized:visible'));
    });

    if(!body.is('.landing-home')) {
        var appUrl = 'studysauce://' + window.location.hostname + window.location.search;

        var appDialog = $('#gettheapp').modal({show: true});
        if (appDialog.length > 0) {
            appUrl += (window.location.search.indexOf('?') > -1 ? '&' : '?') + $('input').map(function () {
                    return $(this).attr('name') + '=' + $(this).attr('value');
                }).toArray().join("&");
            // TODO: show invite dialog
            appDialog.find('.highlighted-link a').attr('href', appUrl);
        }
    }

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

    $(document).on('mouseover', 'rect[title],path[title]', function () {
        var that = $(this);
        setTimeout(function () {
            var id = that.data('ui-tooltip-id'),
                tip = $('#' + id);
            tip.position({my: 'left-10 center'});
            tip.css('left', that.offset().left + that[0].getBBox().width + 10).css('top', that.offset().top + that[0].getBBox().height / 2 - tip.height() / 2)
            tip.addClass('left-arrow')
        }, 15);
    });

    body.on('click', 'a[href="#yt-pause"]', function (evt) {
        evt.preventDefault();
        var frame = $(this).prev().closest('iframe');
        for(var i = 0; i < window.players.length; i++) {
            if ($(window.players[i]).data('frame').is(frame)) {
                window.players[i].pauseVideo();
            }
        }
    });

    var centerTimeout = null;
    $(window).resize(function () {
        DASHBOARD_MARGINS = {padding: {top: $('.header-wrapper').outerHeight(), bottom: 0, left: 0, right: 0}};
        if (centerTimeout != null) {
            clearTimeout(centerTimeout);
        }
        centerTimeout = setTimeout(function () {
            centerize.apply(body.find('.centerized:visible'));
        }, 50);
    });
    $(window).trigger('resize');

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

    // show unsupported dialog if it is needed
    $('#unsupported').modal({
        backdrop: 'static',
        keyboard: false,
        show: true
    });

    body.on('show.bs.modal', '.modal', function () {
        var modals = $('.modal');
        //if(backdrops.length > 1)
        if(modals.length > 0)
            modals.not(this).filter(':visible').each(function () {
                $(this).modal('hide').finish();
                if($(this).data('bs.modal') != null) {
                    $(this).data('bs.modal').removeBackdrop();
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
                if (typeof window.handleLink == 'undefined' || window.handleLink.apply(a, [jQuery.Event('click')])) {
                    window.location = data.redirect;
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
    var dialog = $('#error-dialog');
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