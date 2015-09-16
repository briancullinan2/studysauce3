$(document).ready(function () {

    var pan = null;

    setTimeout(function () {
        var title = $('.landing-home .video h1');
        title.animate({left: 0, opacity: 1}, 1200);
        setTimeout(function () {
            // TODO: fade in video
            var video = $('.landing-home .player-wrapper');
            video.animate({opacity: 1}, 1200);
            setTimeout(function () {
                // fade in cta
                var cta = $('.landing-home .video .highlighted-link');
                cta.animate({top: 0, opacity: 1}, 1200);
            }, 500)
        }, 500)
    }, 200);

    var testimonies = false;

    $(window).on('DOMContentLoaded load resize scroll', checkForVisibility);

    function checkForVisibility()
    {
        var shouldLoad = false;
        var quotes = $('.landing-home .testimony-inner');
        quotes.each(function () {
            if(isElementInViewport($(this))) {
                shouldLoad = true;
            }
        });
        if(shouldLoad) {

            // make all testimonies visible
            if(!testimonies) {
                testimonies = true;

                quotes.each(function (i, el) {
                    setTimeout(function () {
                        // fade in cta
                        $(el).animate({left: 0, top: 0, opacity: 1}, 1200);
                    }, i * 500)
                });
            }
        }
    }

    checkForVisibility();
    onYouTubeIframeAPIReady.apply($('.landing-home'));

});