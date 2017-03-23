

$(document).ready(function () {
    var email = $('#send-email'),
        preview = email.find('#preview')[0];

    var emails = $(window.parent.document).find('#send-email'),
        subject = $('.subject');
    emails.find('.variables').remove();
    $('.variables').insertBefore(emails.find('.highlighted-link'))
        // setupSelectize
        .find('input').each(window.parent.setupSelectize);
    emails.find('input[name="subject"]').val(subject.text());
    subject.remove();
    // TODO: recreate rows when variables change

    function autoFitFrame()
    {
        var margin = $(window).outerHeight() - $('body').height();
        var fit = $('.CodeMirror-sizer:visible, .headers:visible, #editor1:visible').first();
        $(window.parent.document).find('#send-email iframe').height(fit.outerHeight() + margin);
    }

    $(window).on('resize', autoFitFrame);
    $(window).on('scroll', autoFitFrame);
    $(window).on('keydown', autoFitFrame);
    $(window).on('keypress', autoFitFrame);
    $(window).on('keyup', autoFitFrame);
    setTimeout(autoFitFrame, 500);

    CKEDITOR.on('dialogDefinition', function(e) {
        var dialogDefinition = e.data.definition;
        dialogDefinition.onShow = function() {
            this.move($(window).width() - this.getSize().width,0); // Top center
        }
    });
    CKEDITOR.on( 'instanceCreated', function( event ) {
        var editor = event.editor,
            element = editor.element;

        // Customize editors for headers and tag list.
        // These editors don't need features like smileys, templates, iframes etc.
        if ( element.is( 'h1', 'h2', 'h3' ) || element.getAttribute( 'id' ) == 'taglist' ) {
            // Customize the editor configurations on "configLoaded" event,
            // which is fired after the configuration file loading and
            // execution. This makes it possible to change the
            // configurations before the editor initialization takes place.
            editor.on( 'configLoaded', function() {

                debugger;
                // Remove unnecessary plugins to make the editor simpler.
                editor.config.removePlugins = 'colorbutton,find,flash,font,' +
                'forms,iframe,image,newpage,removeformat,' +
                'smiley,specialchar,stylescombo,templates';

                // Rearrange the layout of the toolbar.
                editor.config.toolbarGroups = [
                    { name: 'editing',		groups: [ 'basicstyles', 'links' ] },
                    { name: 'undo' },
                    { name: 'clipboard',	groups: [ 'selection', 'clipboard' ] },
                    { name: 'about' }
                ];
            });
        }

        editor.on( 'instanceReady', function( ) {
            var rules = {
                indent: false,
                breakBeforeOpen: true,
                breakAfterOpen: false,
                breakBeforeClose: false,
                breakAfterClose: true
            };
            editor.dataProcessor.writer.setRules( 'p',rules);
            editor.dataProcessor.writer.setRules( 'div',rules);
            editor.dataProcessor.writer.setRules( 'hr',rules);
            editor.dataProcessor.writer.setRules( 'br',rules);
        });
    });


    var mirror = CodeMirror.fromTextArea($('#markdown')[0], {
        lineNumbers: true,
        lineWrapping: true,
        extraKeys: {
            "Ctrl-Space": "autocomplete",
            "Ctrl-Q": function(cm){ cm.foldCode(cm.getCursor()); }
        },
        fullscreen: true,
        mode: "text/html",
        foldGutter: true,
        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"]
    });

    var startView = $(window.parent.document.body)
        .find('#send-email .active a[href="#markdown"], #send-email .active a[href="#editor1"]')
        .first();
    if(startView.is('[href="#markdown"]')) {
        $('#editor1').hide();
        $('.headers').hide();
    }
    else if(startView.is('[href="#editor1"]')) {
        $('.CodeMirror').hide();
        $('.headers').hide();
    }
    else if(startView.is('[href="#headers"]')) {
        $('#editor1').hide();
        $('.CodeMirror').hide();
    }

    $(window.parent.document.body).on('click', '#send-email a[href="#markdown"], #send-email a[href="#editor1"], #send-email a[href="#headers"]', function (evt) {
        evt.preventDefault();
        var email = $('#send-email'),
            that = $(this);
        that.parents('ul').find('.active').removeClass('active');
        that.parents('li').addClass('active');
        if(that.is('[href="#editor1"]')) {
            CKEDITOR.instances.editor1.setData(mirror.getDoc().getValue());
            $('.CodeMirror').hide();
            $('#editor1').show();
            $('.headers').hide();
        }
        else if(that.is('[href="#markdown"]')) {
            mirror.getDoc().setValue(CKEDITOR.instances.editor1.getData());
            $('.CodeMirror').show();
            $('#editor1').hide();
            $('.headers').hide();
            mirror.refresh();
        }
        else if(that.is('[href="#headers"]')) {
            $('.CodeMirror').hide();
            $('#editor1').hide();
            $('.headers').show();
        }
        autoFitFrame();
    });

});
