
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

            this.$backdrop = $('<div class="modal-backdrop ' + animate + '"><div class="modal-backdrop-top" /><div class="modal-backdrop-right" /><div class="modal-backdrop-bottom" /><div class="modal-backdrop-left" /></div>')
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

$(document).ready(function () {
    var body = $('body');

    // show unsupported dialog if it is needed
    $('#unsupported').modal({
        backdrop: 'static',
        keyboard: false,
        show: true
    });

    if(!body.is('.landing-home')) {
        var appUrl = 'studysauce://' + window.location.hostname + window.location.search;

        var appDialog = $('#gettheapp').modal({show: true});
        if (appDialog.length > 0 && (window.location.pathname == '/login' || window.location.pathname == '/register' || window.location.pathname == '/reset')) {
            appUrl += (window.location.search.indexOf('?') > -1 ? '&' : '?') + $('input').map(function () {
                    return $(this).attr('name') + '=' + $(this).attr('value');
                }).toArray().join("&");
            // TODO: show invite dialog
            appDialog.find('.highlighted-link a').attr('href', appUrl);
        }
    }

    var centerTimeout = null;
    $(window).resize(function () {
        DASHBOARD_MARGINS = {padding: {top: $('.header-wrapper').outerHeight(), bottom: 0, left: 0, right: 0}};
        if (centerTimeout != null) {
            clearTimeout(centerTimeout);
        }
        centerTimeout = setTimeout(function () {
            centerize.apply(body.find('.centerized:visible'));
        }, 50);
        adjustBackdrop();
    });
    $(window).trigger('resize');

    body.on('show.bs.modal shown.bs.modal', function () {
        centerize.apply($('body').find('.centerized:visible'));
        adjustBackdrop();
    });

    body.on('hidden.bs.modal hide.bs.modal', function () {
        $(this).find('.modal-content').stop();
        $(this).find('.modal-content').css('top', '');
        $(this).find('.modal-content').css('left', '');
    });

    body.on('shown.bs.modal', '.modal', function () {
        if($(this).is('.modal')) {
            var modals = $('.modal');
            //if(backdrops.length > 1)
            if(modals.length > 0)
                modals.not(this).filter(':visible').each(function () {
                    $(this).modal('hide').finish();
                    if($(this).data('bs.modal') != null) {
                        $(this).data('bs.modal').removeBackdrop();
                    }
                });
        }
    });

    var alreadyDragging = false;
    body.on('mousedown', '.modal-header', function (evt) {
        if(alreadyDragging) {
            return;
        }
        var content = $(this).parents('.modal-content');
        if(!content.is('.ui-draggable')) {
            alreadyDragging = true;
            setupDialog.apply(content).trigger(evt);
            alreadyDragging = false;
        }
    });

    function setupDialog () {
        var content = $(this);
        if(!content.is('.ui-draggable')) {
            content.draggable({
                handle: '.modal-header',
                drag: adjustBackdrop,
                containment: content.parents('.modal')
            });
        }
        return content;
    }

    var goingToY = 0;
    function adjustBackdrop(evt) {
        setTimeout(function () {
            var backdrop = $('.modal-backdrop:visible');
            var container = $('.modal:visible');
            var currentDialog = container.find('.modal-content');
            if(currentDialog.length == 0 || backdrop.length == 0) {
                return;
            }
            setupDialog.apply(currentDialog);
            var height = $(window).height();
            var width = $(window).width();
            var dialogHeight = currentDialog.outerHeight();
            var dialogWidth = currentDialog.outerWidth();
            var offset = currentDialog.offset();
            offset.top = offset.top - $(window).scrollTop();
            offset.left = offset.left - $(window).scrollLeft();
            backdrop.find('.modal-backdrop-left').css('margin-right', (width / 2) - offset.left - 1);
            backdrop.find('.modal-backdrop-right').css('margin-left', offset.left - (width / 2) + dialogWidth - 1);
            backdrop.find('.modal-backdrop-bottom').css('margin-top', offset.top - (height / 2) + dialogHeight - 1);
            backdrop.find('.modal-backdrop-top').css('margin-bottom', (height / 2) - offset.top - 1);
            var containment;
            if((typeof evt == 'undefined' || evt.type != 'drag') && typeof (containment = currentDialog.draggable('instance').containment) != 'undefined') {
                if(offset.top + dialogHeight > height && goingToY != containment[3] - containment[1]) {
                    container.css('overflow', 'hidden');
                    currentDialog.draggable('option', 'containment', false);
                    currentDialog.draggable('instance')._setContainment();
                    currentDialog.draggable('option', 'containment', container);
                    currentDialog.draggable('instance').helperProportions = {
                        width: dialogWidth,
                        height: dialogHeight
                    };
                    currentDialog.draggable('instance')._setContainment();
                    containment = currentDialog.draggable('instance').containment;
                    goingToY = containment[3] - containment[1];
                    if (goingToY < containment[1]) {
                        goingToY = containment[1];
                    }
                    currentDialog.stop().animate({top: containment[3] - containment[1], step: function () {
                        //adjustBackdrop();
                    }}, 250, function () {
                        // done?
                        //container.css('overflow', '');
                    });
                }
            }
        }, 13);
    }
    window.adjustBackdrop = adjustBackdrop;

    $(window).on('scroll', function () {
        adjustBackdrop();
    });

    body.on('hidden.bs.modal', '#general-dialog', function () {
        $(this).find('.modal-body').html('<p>put message here</p>');
    });

    body.on('hidden.bs.modal', '#general-dialog', function () {
        // don't do this too early, give the confirm button a chance to response
        setTimeout(function () {
            body.off('click.modify_entities_confirm');
            body.off('click.publish_confirm');
            body.off('click.confirm_action');
            body.off('click.confirm_navigation');
        }, 100);
    });

    body.on('change', 'input[data-confirm], select:has(option[data-confirm])', function () {
       // TODO: reset to oldValue, show confirmation dialog, then set to new value
    });

    body.on('click', 'a[data-confirm][data-toggle="modal"]', function (evt) {
        evt.preventDefault();
        var that = $(this);
        body.one('click.confirm_action', '#general-dialog a[href="#submit"]', function () {
            if(that.is('[data-action]')) {

                $.ajax({
                    url: that.data('action'),
                    type: 'GET',
                    dataType: that.data('type') || 'json',
                    success: function (data) {
                        if (that.data('type') == 'text') {
                            loadContent.apply(that.parents('.results'), [data]);
                        }
                        that.parents('.results').trigger('resulted');
                    }
                });

            }
            else {
                // TODO: set the field value?
            }

        });

        $('#general-dialog').find('.modal-body').html(that.data('dialog'));
    });

});