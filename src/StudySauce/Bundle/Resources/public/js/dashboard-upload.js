
$(document).ready(function () {

    // handle all uploading functions
    var body = $('body');

    body.on('hidden.bs.modal', '#upload-file', function () {
        var dialog = $('#upload-file');
        setTimeout(function () {
            body.off('click.upload');
            dialog.find('.plupload img').attr('src', defaultImage).removeClass('add').load(function () {
                centerize.apply($(this));
            });
            dialog.find('.file').remove();
        }, 100);
    });

    body.on('dragover', '#upload-file', function () {
        $(this).addClass('dragging');
    });

    body.on('click', 'a[data-target="#upload-file"], a[href="#upload-file"]', function () {
        var dialog;
        if ((dialog = $('#upload-file')).length == 0) {
            dialog = $(window.views.render.apply(body, ['upload-file', {}])).appendTo(body);
        }
        // update field next to upload link
        var row = $(this).parents('[class*="-row"]');
        body.one('click.upload', 'a[href="#submit-upload"]', function () {
            var url = dialog.find('img').attr('src');
            // TODO user some sort of data binding api to update this part
            row.addClass('changed').find('input[name="upload"]').val(url).trigger('change').siblings('img').attr('src', url).removeClass('default').load(function () {
                if($(this).is('.centerized')) {
                    centerize.apply(this);
                }
            });
        });

        if (dialog.find('.plupload').is('.init'))
            return;
        defaultImage = dialog.find('.plupload img.default').attr('src');
        var upload = new plupload.Uploader({
            chunk_size: '5MB',
            runtimes: 'html5,flash,silverlight,html4',
            drop_element: 'upload-file',
            dragdrop: true,
            browse_button: 'file-upload-select', // you can pass in id...
            container: plupload[0], // ... or DOM Element itself
            url: Routing.generate('file_create'),
            unique_names: true,
            max_files: 0,
            multipart: false,
            multiple_queues: true,
            urlstream_upload: false,
            filters: {
                max_file_size: '1gb',
                mime_types: [
                    {
                        title: "Image files",
                        extensions: "jpg,jpeg,gif,png,bmp,tiff"
                    },
                    {
                        title: "Audio files",
                        extensions: "mp3,ogg,m4a,mp4"
                    },
                    {
                        title : "Video files",
                        extensions : "mov,avi,mpg,mpeg,wmv,mp4,webm,flv,m4v,mkv,ogv,ogg,rm,rmvb,m4v"
                    }
                ]
            },
            flash_swf_url: Routing.generate('_welcome') + 'bundles/studysauce/js/plupload/js/Moxie.swf',
            silverlight_xap_url: Routing.generate('_welcome') + 'bundles/studysauce/js/plupload/js/Moxie.xap',
            init: {
                PostInit: function (up) {
                    dialog.find('.plupload').addClass('init');
                    dialog.find('#file-upload-select').on('click', function () {
                        up.splice();
                    });
                },
                FilesAdded: function (up, files) {
                    plupload.each(files, function (file) {
                        $('<div id="' + file.id + '" class="file">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>').appendTo(dialog.find('.plup-filelist'));
                    });
                    up.start();
                },
                UploadProgress: function (up, file) {
                    var squiggle;
                    if ((squiggle = dialog.find('.squiggle')).length == 0)
                        squiggle = $('<small class="squiggle">&nbsp;</small>').appendTo(dialog.find('.plup-filelist'));
                    squiggle.stop().animate({width: up.total.percent + '%'}, 500, 'swing');
                    var subsquiggle;
                    if ((subsquiggle = dialog.find('#' + file.id).find('b').html('<span>' + file.percent + '%</span>').find('.squiggle')).length == 0) {
                        subsquiggle = $('<small class="squiggle">&nbsp;</small>').appendTo(dialog.find('#' + file.id));
                    }
                    subsquiggle.stop().animate({width: file.percent + '%'}, 500, 'swing');
                },
                FileUploaded: function (up, file, response) {
                    var data = JSON.parse(response.response);
                    dialog.find('input[type="hidden"]').val(data.fid);
                    dialog.find('.plup-filelist .squiggle').stop().remove();
                    dialog.find('#' + file.id).find('.squiggle').stop().remove();
                    dialog.find('.plupload img').attr('src', data.src).removeClass('default').load(function () {
                        centerize.apply($(this));
                    });
                },
                Error: function (up, err) {
                }
            }
        });

        setTimeout(function () {
            upload.init();
        }, 200);

    });
    var defaultImage;

    // hide any visible modals when panel changes
    body.on('hide', '.panel-pane', function () {
        body.find('.modal:visible').modal('hide');
        body.find('.ui-datepicker:not(.ui-datepicker-inline)').hide();
    });

    $(document.body).bind("dragover", function () {
        $(this).addClass('dragging');
    });
    $(document.body).bind("dragleave", function () {
        $(this).removeClass('dragging');
    });
    $(document.body).bind("drop", function () {
        $(this).removeClass('dragging').addClass('dropped');
    });

});